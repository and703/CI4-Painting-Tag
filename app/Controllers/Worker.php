<?php 
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Worker_model;

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

    public function get_troly()
    {
        $model = new Worker_model();
        $id1 = $this->request->getPost('WM_CODE');
        $id2 = $this->request->getPost('MAT_IP_CODE');
        $data['mch'] = $this->request->getPost('mch');
        $data['Amount'] = $this->request->getPost('Amount');
        $data['Troly'] = $this->request->getPost('Troly');
        $data['worker'] = $model->getWorker($id1)->getRow();
        $data['gt_ip'] = $model->getGtip($id2)->getRow();
        $data['title'] = "Input Amount GT";
        echo view('C_U/troly', $data);
    }

    public function save()
    {
        $model = new Worker_model();
        $startTime = date('d/m/Y H.i');
        $cenvertedTime = date("d/m/Y H.i", strtotime('+3 hours'));

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
            'Troly'                 => $this->request->getPost('Troly'),
        );
        $model->savePrint($data);
        return redirect()->to('/worker/print');
    }
    
    public function print()
    {
        $model = new Worker_model();
		$data['painting'] = $model->viewPrint()->getRow();
		$data['title'] = "Print Tag";
		echo view('C_U/print', $data);
    }
	
}
