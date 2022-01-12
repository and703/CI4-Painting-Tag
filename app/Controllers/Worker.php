<?php 
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Worker_model;
use App\Models\KomikModel;
use App\Models\ParkModel;

class Worker extends Controller
{
    public function index()
    {
        $data['title'] = "Input Nik";
        echo view('worker_view', $data);
    }
    
    public function worker()
    {
        $data['title'] = "Input Nik";
        echo view('worker_view', $data);
    }

    public function get_nik()
    {
        $model = new Worker_model();
        $id = $this->request->getPost('WM_CODE');
        $data['worker'] = $model->getWorker($id)->getRow();
        $data['title'] = "Input Machine Code";
        echo view('C_U/mch', $data);
    }

    public function get_mch()
    {
        $model = new Worker_model();
        $id = $this->request->getPost('WM_CODE');
        $data['mch'] = $this->request->getPost('mch');
        $data['worker'] = $model->getWorker($id)->getRow();
        $data['title'] = "Input GT IPCode";
        echo view('C_U/GT_IP_view', $data);
    }

    public function get_ip()
    {
        $model = new Worker_model();
        $id1 = $this->request->getPost('WM_CODE');
        $id2 = $this->request->getPost('MAT_IP_CODE');
        $data['mch'] = $this->request->getPost('mch');
        $data['worker'] = $model->getWorker($id1)->getRow();
        $data['gt_ip'] = $model->getGtip($id2)->getRow();
        $data['title'] = "Input Amount GT";
        echo view('C_U/input_amount', $data);
    }

    public function save()
    {
        
        $md2 = new ParkModel();
        $dt['park']     = $md2->where('id_paint', '0')->first();
        $slot = $dt['park']['slot'];
        $startTime = date('d/m/Y H.i');
        $cenvertedTime = date("d/m/Y H.i", strtotime('+3 hours'));

        $model = new Worker_model();
        $data = array(
            'WM_CODE'               => $this->request->getPost('WM_CODE'),
            'MCH'                   => $this->request->getPost('mch'),
            'WM_NAME_WM_SURNAME'    => $this->request->getPost('WM_NAME_WM_SURNAME'),
            'MAT_DESC'              => $this->request->getPost('MAT_DESC'),
            'MAT_IP_CODE'           => $this->request->getPost('MAT_IP_CODE'),
            'Amount'                => $this->request->getPost('Amount'),
            'On_Insert'             => $startTime,
            'CURE_TIME'             => $cenvertedTime,
            'Count_Printed'         => $this->request->getPost('Count_Printed'),
            'Park'                  => $slot,
        );
        $model->savePrint($data);
        return redirect()->to('/worker/print');
    }
    
    public function print()
    {
        $md1 = new KomikModel();
        $md2 = new ParkModel();
		$data['painting'] = $md1->orderBy('id', 'DESC')->first();
        $id = $data['painting']['id'];
        $slot = $data['painting']['Park'];
		$data['park'] = $md2->where('slot', $slot)->first();
        $dtid = $data['park']['id'];
        $dt = [
            'id_paint'  => $id,
        ];
        $md2->update($dtid, $dt);
		$data['title'] = "Print Tag";
        //print_r($dtid);
		echo view('C_U/print', $data);
    }
	
}
