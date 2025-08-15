<?php
namespace App\Models;
use CodeIgniter\Model;

class Park_BF_CURE_Stock_Model extends Model
{
    protected $DBGroup = "default";
    protected $table = "parking_bf_curr_stock";
    protected $primaryKey = "id";
    protected $allowedFields = ["MM_CODE", 
								"WM_NAME_WM_SURNAME", 
								"MAT_IP_CODE", 
								"MAT_DESC", 
								"id_paint", 
								"Park", 
								"tag_stock", 
								"adj_stock", 
								"dateCURE", 
								"cured_stts", 
								"dateAdj"];

    public function getPark($id = false)
    {
        if ($id == false) {
            return $this->orderBy("id", "DESC")->findAll();
        }
        return $this->where(["id" => $id])->first();
    }
}
