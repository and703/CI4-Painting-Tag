<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Report_paint_re extends Model
{
    protected $painting_re = "painting_re";

    protected $request;
    protected $db;
    protected $db_rpot;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $request = \Config\Services::request();
        $this->request = $request;
        $this->db_rpot = $this->db->table($this->painting_re);
    }

    public function get_data()
    {
        $this->_get_datatables();
        if ($this->request->getPost("length") != -1)
            $this->db_rpot->limit($this->request->getPost("length"), $this->request->getPost("start"));
        $query = $this->db_rpot->get();
        return $query->getResult();
    }
    function _get_datatables()
    {
        $f1 = $this->request->getPost("f1");

        if ($f1) {
            $tgl1 = $this->rangeindo($f1, 0);
            $tgl2 = $this->rangeindo($f1, 1);
            $this->db_rpot->where("CAST(On_Insert AS DATE)>=", $tgl1);
            $this->db_rpot->where("CAST(On_Insert AS DATE)<=", $tgl2);
        }

        $f2 = $this->request->getPost("f2");

        if ($f2) {
            $this->db_rpot->where("WM_SHIFT", $f2);
        }

        $column_order = array('', 'WM_CODE', 'MCH', 'MAT_IP_CODE', 'MAT_DESC', 'Amount', 'Park', 'On_Insert', 'CURE_TIME', 'Count_Printed', 'WM_NAME_WM_SURNAME', 'WM_GROUP', 'WM_SHIFT', 'Re');
        $column_search = array('MAT_IP_CODE', 'Park', 'WM_CODE');

        $i = 0;
        $search = $this->request->getPost('search');
        foreach ($column_search as $item) {
            if (isset($search['value']) && $search['value']) {
                if ($i === 0) {
                    $this->db_rpot->groupStart();
                    $this->db_rpot->like($item, $this->request->getPost('search')['value']);
                } else {
                    $this->db_rpot->orLike($item, $this->request->getPost('search')['value']);
                }
                if (count($column_search) - 1 == $i)
                    $this->db_rpot->groupEnd();
            }
            $i++;
        }
        $post_order = $this->request->getPost('order');
        if (isset($post_order)) {
            $this->db_rpot->orderBy($column_order[$this->request->getPost('order')['0']['column']], $this->request->getPost('order')['0']['dir']);
        } else if (isset($order)) {
            $order = $order;
            $this->db_rpot->orderBy(key($order), $order[key($order)]);
        }
    }
    function count_filtered()
    {
        $this->_get_datatables();
        return $this->db_rpot->countAllResults();
    }
    function count_all()
    {
        $this->_get_datatables();
        return $this->db_rpot->countAllResults();
    }

    function rangeindo($tgl, $ambil)
    {
        $tglORI = explode(" - ", $tgl);
        $tglAwal = explode("/", $tglORI[$ambil]);
        $tgl1 = $tglAwal[2] . "-" . $tglAwal[1] . "-" . $tglAwal[0];
        return $tgl1;
    }
}
