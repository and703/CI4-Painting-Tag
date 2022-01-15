<?php 
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
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
        $session = session();
        // jika user belum login
        if(! $session->get('logged_in_wm')){
            // maka redirct ke halaman login
            return redirect()->to('worker');
        // jika user sudah login
		}else{
			$data['title'] = "Input Machine Code";
			echo view('C_U/mch', $data);
		}
    }
    
    public function Park()
    {
        $session = session();
        // jika user belum login
        if(! $session->get('logged_in_mm')){
            // maka redirct ke halaman login
            return redirect()->to('parking');
        // jika user sudah login
		}else{
			$data['title'] = "Painting Park";
			echo view('C_U/Park', $data);
		}
    }
    
    public function get_nik_mm()
    {
        $session = session();
        $model = new Worker_model();
        $id = $this->request->getPost('WM_CODE');
        $data['worker'] = $model->getWorker($id)->getRow();
        if($data['worker']){
			$ses_data = [
				'WM_CODE'       => $data['worker']['WM_CODE'],
				'WM_NAME'     	=> $data['worker']['WM_NAME'],
				'WM_SURNAME'    => $data['worker']['WM_SURNAME'],
				'logged_in_mm'  => TRUE
			];
			$session->set($ses_data);
			$data['title'] = "Painting Park";
			echo view('C_U/Park', $data);
        }else{
            $session->setFlashdata('msg', 'Email not Found');
            return redirect()->to('parking');
        }
    }
 
    public function logout_MM()
    {
        $session = session();
        $session->destroy();
		$data['title'] = "Painting Park";
		return redirect()->to('parking');
    }

    public function get_nik()
    {
        $session = session();
        $model = new Worker_model();
        $id = $this->request->getPost('WM_CODE');
        $data['worker'] = $model->getWorker($id)->getRow();
        if($data['worker']){
			$ses_data = [
				'WM_CODE'       => $data['worker']['WM_CODE'],
				'WM_NAME'     	=> $data['worker']['WM_NAME'],
				'WM_SURNAME'    => $data['worker']['WM_SURNAME'],
				'logged_in_wm'  => TRUE
			];
			$session->set($ses_data);
			$data['title'] = "Input Machine Code";
			echo view('C_U/mch', $data);
        }else{
            $session->setFlashdata('msg', 'Email not Found');
            return redirect()->to('worker');
        }
    }
 
    public function logout_WM()
    {
        $session = session();
        $session->destroy();
		$data['title'] = "Painting Park";
		return redirect()->to('worker');
    }

    public function get_mch()
    {
        $session = session();
        $model = new Worker_model();
        $id = $this->request->getPost('WM_CODE');
        $data['mch'] = $this->request->getPost('mch');
        $data['worker'] = $model->getWorker($id)->getRow();
        $data['title'] = "Input GT IPCode";
        echo view('C_U/GT_IP_view', $data);
    }

    public function get_ip()
    {
        $session = session();
        $model = new Worker_model();
        $id = $this->request->getPost('MAT_IP_CODE');
        $data['mch'] = $this->request->getPost('mch');
        $data['gt_ip'] = $model->getGtip($id)->getRow();
        $data['title'] = "Input Amount GT";
        echo view('C_U/input_amount', $data);
    }

    public function save()
    {
        $session = session();
        $md2 = new ParkModel();
        $dt['park']     = $md2->where('id_paint', '0')->first();
        $slot = $dt['park']['slot'];
        $startTime = date('d/m/Y H.i');
        $cenvertedTime = date("d/m/Y H.i", strtotime('+3 hours'));
		$WM_NAME_WM_SURNAME = $session->get('WM_NAME').' '.$session->get('WM_SURNAME');
        $model = new Worker_model();
        $data = array(
            'WM_CODE'               => $session->get('WM_CODE'),
            'MCH'                   => $this->request->getPost('mch'),
            'WM_NAME_WM_SURNAME'    => $WM_NAME_WM_SURNAME,
            'MAT_DESC'              => $this->request->getPost('MAT_DESC'),
            'MAT_IP_CODE'           => $this->request->getPost('MAT_IP_CODE'),
            'Amount'                => $this->request->getPost('Amount'),
            'On_Insert'             => $startTime,
            'CURE_TIME'             => $cenvertedTime,
            'Count_Printed'         => $this->request->getPost('Count_Printed'),
            'Park'                  => $slot,
        );
        $model->savePrint($data);
        return redirect()->to('worker/print');
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
	
    public function get_tag()
    {
        $md1 = new KomikModel();
        $list = $this->request->getPost('listTag');
		$id = explode(",", $list);
		$data['painting'] = $md1->where('id', $id[4])->first();
		$data['title'] = "Tag Confirm";
		echo view('C_U/ParkConf', $data);
    }
	
    public function tagconf()
    {
        $md2 = new ParkModel();
        $Park = $this->request->getPost('Park');
		$array = ['slot' => $Park, 'id_paint !=' => '0'];
		$data['park'] = $md2->where($array)->first();
        // tampilkan 404 error jika data tidak ditemukan
		if (!$data['park']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Park ' . $Park . ' Tidak di temukan / Sudah Kosong');
		}else{
			$dtid = $data['park']['id'];
			$dt = [
				'id_paint'  => '0',
			];
			$md2->update($dtid, $dt);
			
			return redirect()->to('parking');
		}
    }
}
