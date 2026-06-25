<?php

namespace App\Controllers;

class Report_paint_re extends BaseController
{
    protected $M_Report_paint_re;
    protected $request;
    protected $db;
    protected $Dompdf;

    public function __construct()
    {
        $this->mdl = new \App\Models\M_Report_paint_re();
        $this->db = \Config\Database::connect();
        $request = \Config\Services::request();
        $this->request = $request;
    }

    public function index()
    {
        echo view('v_report_paint_re');
    }

    function data_tables()
    {
        $list = $this->mdl->get_data();
        $data = array();
        $no = $this->request->getPost("start");
        $no = $no + 1;
        foreach ($list as $v) {
            $wm_code = isset($v->WM_CODE) ? ($v->WM_CODE) : '';
            $mch = isset($v->MCH) ? ($v->MCH) : '';
            $ipcode = isset($v->MAT_IP_CODE) ? ($v->MAT_IP_CODE) : '';
            $matdesc = isset($v->MAT_DESC) ? ($v->MAT_DESC) : '';
            $jumlah = isset($v->Amount) ? ($v->Amount) : '';
            $park = isset($v->Park) ? ($v->Park) : '';
            $on_insert = isset($v->On_Insert) ? ($v->On_Insert) : '';
            $cure_time = isset($v->CURE_TIME) ? ($v->CURE_TIME) : '';
            $count_printed = isset($v->Count_Printed) ? ($v->Count_Printed) : '';
            $operator = isset($v->WM_NAME_WM_SURNAME) ? ($v->WM_NAME_WM_SURNAME) : '';
            $group = isset($v->WM_GROUP) ? ($v->WM_GROUP) : '';
            $shift = isset($v->WM_SHIFT) ? ($v->WM_SHIFT) : '';
            $re = isset($v->Re) ? ($v->Re) : '';

            $row = array();
            $row[] = $no++;
            $row[] = $wm_code;
            $row[] = $mch;
            $row[] = $ipcode;
            $row[] = $matdesc;
            $row[] = $jumlah;
            $row[] = $park;
            $row[] = $on_insert;
            $row[] = $cure_time;
            $row[] = $count_printed;
            $row[] = $operator;
            $row[] = $group;
            $row[] = $shift;
            $row[] = $re;

            $data[] = $row;
        }
        $output = array(
            "draw" => (int) ($this->request->getPost("draw") ?? 0),
            "recordsTotal" => $this->mdl->count_all(),
            "recordsFiltered" => $this->mdl->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
}
