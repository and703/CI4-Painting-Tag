<?php namespace App\Models;
use CodeIgniter\Model;

class Worker_model extends Model
{
    private $db1;

    private $pcs;

    public function __construct()
    {
        $this->db1 = db_connect(); // default database group

        $this->pcs = db_connect("pcs"); // other database group
    }

    public function getWorker($id = false)
    {
        $builder = $this->pcs->table("MD_WORKERS");
        if ($id === false) {
            return $builder->get();
        } else {
            return $builder->getWhere(["WM_CODE" => $id]);
        }
    }

    public function getGtip($id = false)
    {
        $builder = $this->pcs->table("MD_MATERIALS");
        if ($id === false) {
            return $builder->get();
        } else {
            return $builder->getWhere(["MAT_IP_CODE" => $id]);
        }
    }

    public function savePrint($data)
    {
        $query = $this->db1->table("painting")->insert($data);
        return $query;
    }

    public function viewPrint()
    {
        $builder = $this->db1->table("painting");
        $builder->orderBy("id", "DESC");
        $builder->limit(1);
        return $builder->get();
    }
}
