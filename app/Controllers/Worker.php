<?php 
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\Worker_model;
use App\Models\KomikModel;
use App\Models\ParkModel;
use App\Models\MMModel;
use App\Models\CQModel;
use App\Models\QModel;

class Worker extends Controller
{
    public function index()
    {
        $data['title'] = "Input Nik";
        echo view('worker_view', $data);
    }

    public function mch_log()
    {
        $data['title'] = "Input Machine Code";
        echo view('C_U/mch', $data);
    }

    public function MM_log()
    {
        $data['title'] = "Input Machine Code";
        echo view('C_U/MM_log', $data);
    }

    public function park_view()
    {
        $data['title'] = "Parking View";
        echo view('C_U/Park', $data);
    }
    
    public function worker()
    {
        $data['title'] = "Painting Login";
        echo view('worker_view', $data);
    }
    
    public function get_nik_mm()
    {
        $session = session();
        $model = new Worker_model();
        $id = $this->request->getPost('WM_CODE');
        $data['park'] = $model->getWorker($id)->getRow();
        $dt = json_decode(json_encode($data['park']), true);
        if($dt){
			$ses_data = [
				'MM_CODE'       => $dt['WM_CODE'],
				'MM_NAME'     	=> $dt['WM_NAME'],
				'MM_SURNAME'    => $dt['WM_SURNAME'],
				'logged_in_mm'  => '1'
			];
			$session->set($ses_data);
            return redirect()->to('park_view');
        }else{
            session()->setFlashdata('pesan', 'Login Gagal Nik : '.$id.' Tidak terdaftar');
            return redirect()->to('park_log');
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
        $data['painting'] = $model->getWorker($id)->getRow();
        $dt = json_decode(json_encode($data['painting']), true);
        if($dt){
			$ses_data = [
				'WM_CODE'       => $dt['WM_CODE'],
				'WM_NAME'     	=> $dt['WM_NAME'],
				'WM_SURNAME'    => $dt['WM_SURNAME'],
				'logged_in_wm'  => '1'
			];
			$session->set($ses_data);
			$data['title'] = "Input Machine Code";
			echo view('C_U/mch', $data);
        }else{
            session()->setFlashdata('pesan', 'Login Gagal Nik : '.$id.' Tidak terdaftar');
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
		$data['message']  = '';
		echo view('C_U/ParkConf', $data);
    }
	
    public function tagconf()
    {
        $md1 			= new KomikModel();
        $md2 			= new ParkModel();
        $md3 			= new MMModel();
        $dateTime 		= date('d/m/Y H.i');
        $Park 			= $this->request->getPost('Park');
        $MM_CODE 		= $this->request->getPost('MM_CODE');
        $id 			= $this->request->getPost('id');
        $CURE_TIME 		= $this->request->getPost('CURE_TIME');
		$array 			= ['slot' => $Park, 'id_paint !=' => '0'];
		$data['park'] 	= $md2->where($array)->first();
        // tampilkan 404 error jika data tidak ditemukan
		if (!$data['park']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Park ' . $Park . ' Tidak di temukan / Sudah Kosong');
		}else{
			$dtid = $data['park']['id'];
			$dt1 = [
				'id_paint'  => '0',
			];
            $dt2 = [
                'MM_CODE'     => $MM_CODE,
                'Park_id'     => $dtid,
                'Paint_id'    => $id,
                'CURE_TIME'   => $CURE_TIME,
                'dateTIME'    => $dateTime,
            ];
            
			$md2->update($dtid, $dt1);
            $md3->insert($dt2);

            return redirect()->to('parking');
		}
    }
	
    public function tagconf_qty()
    {
        $md1 			= new KomikModel();
        $md2 			= new ParkModel();
        $md3 			= new MMModel();
        $md4 			= new CQModel();
        $md5 			= new QModel();
        $dateTime 		= date('d/m/Y H.i');
        $Qty_NIK 		= $this->request->getPost('Qty_NIK');
        $pass_QC 		= $this->request->getPost('pass_QC');
        $Park 			= $this->request->getPost('Park');
        $MM_CODE 		= $this->request->getPost('MM_CODE');
        $id 			= $this->request->getPost('id');
        $CURE_TIME 		= $this->request->getPost('CURE_TIME');
		$arr_park 		= ['slot' => $Park, 'id_paint !=' => '0'];
		$arr_Qty 		= ['Qty_NIK' => $Qty_NIK];
		$data['park'] 	= $md2->where($arr_park)->first();
        $data['QC']		= $md4->getQC($Qty_NIK)->getRow();
        $dt = json_decode(json_encode($data['QC']), true);
        // tampilkan 404 error jika data tidak ditemukan
		if (! $data['park']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Park ' . $Park . ' Tidak di temukan / Sudah Kosong');
		}else{
			if (! $data['QC']){
				throw new \CodeIgniter\Exceptions\PageNotFoundException('User ' . $Qty_NIK . '  QC Tidak Ditemukan');
				
			}else{
				if ($dt['pass'] !== $pass_QC){
					$data['painting'] = $md1->where('id', $id)->first();
					$data['title'] 	  = "Tag Confirm";
					$data['message']  = '							    
										<div class="alert alert-danger" role="alert">
											<strong>Password Salah</strong>
										</div>
										';
					echo view('C_U/ParkConf', $data);
				}else{
					$dtid = $data['park']['id'];
					$dt1 = [
						'id_paint'  => '0',
					];
					$dt2 = [
						'MM_CODE'     => $MM_CODE,
						'Park_id'     => $dtid,
						'Paint_id'    => $id,
						'CURE_TIME'   => $CURE_TIME,
						'dateTIME'    => $dateTime,
					];
					$dt3 = [
						'Qty_NIK'     => $Qty_NIK,
						'MM_CODE'     => $MM_CODE,
						'Park_id'     => $dtid,
						'Paint_id'    => $id,
						'CURE_TIME'   => $CURE_TIME,
						'dateTIME'    => $dateTime,
					];
					
					$md2->update($dtid, $dt1);
					$md3->insert($dt2);
					$md5->insert($dt3);

					return redirect()->to('parking');
				}
			}
		}
    }
}
