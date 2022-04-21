<?php

namespace App\Controllers;

class Chart extends BaseController
{

	// Deklarasikan tabel dan service yg akan dipakai
	/*model*/
	protected $M_Chart;
	//inconstruct
	protected $request; // pengganti this input untuk get parameter yg dipost dan diget
	protected $db; // untuk database deklarasi tabel
	

	// deklarasikan semua
	public function __construct()
	{
		$this->mdl = new \App\Models\M_Chart(); //load model terlebih dahulu
		$this->db = \Config\Database::connect(); // load services koneksi database
		$request = \Config\Services::request(); // load services request
		$this->request = $request;
	}

	

	public function index()

	{

        $data["pieparking"]=$this->db->query("SELECT b.MAT_IP_CODE ,SUM(b.Amount) as Total
        FROM parking a LEFT JOIN painting b ON a.id_paint = b.id 
        where a.id_paint >0 GROUP BY b.MAT_IP_CODE order BY Total Desc")->getResult();

		$data["tbl_statistik"]=$this->db->table('v_tbl_statistik')->get()->getResult();
		
		$total_semua =$this->db->query("SELECT SUM(Total) as Total FROM v_tbl_statistik")->getRow();
		$data['total_semua'] = $total_semua->Total?? '0';

		
  return view('v_chart',$data) ;
	}



}