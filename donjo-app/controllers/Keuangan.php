<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Keuangan extends Admin_Controller {

  public function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('keuangan_model');
		$this->load->model('header_model');
		$this->modul_ini = 201;
	}

  public function widget()
  {
    $header = $this->header_model->get_data();
    $nav['act_sub'] = 203;
    $this->load->view('header', $header);
    $this->load->view('nav', $nav);
    $this->load->view('keuangan/widget',$data);
    $this->load->view('footer');
  }

  public function import_data()
  {
    $data['form_action'] = site_url("keuangan/proses_impor");
    $header = $this->header_model->get_data();
		$nav['act_sub'] = 202;
    $this->load->view('header', $header);
    $this->load->view('nav', $nav);
		$this->load->view('keuangan/import_data', $data);
    $this->load->view('footer');
  }

  public function proses_impor()
  {
    $nama = $_FILES['keuangan'];
    if ($_POST['jenis_import'] == 'update')
    {
      if($_FILES['keuangan']['name'] !='')
      {
        $this->keuangan_model->extractUpdate($nama);
      }
    }
    else
    {
      if($_FILES['keuangan']['name'] !='')
      {
        $this->keuangan_model->extract($nama);
      }
    }
    redirect('keuangan/import_data');
  }

  public function cek_versi_database()
  {
    $nama = $_FILES['keuangan'];
    $cek = $this->keuangan_model->cek_master_keuangan($nama);
    if ($cek){
      echo json_encode($cek->id);
    }
    else
    {
      echo json_encode(0);
    }
  }

}
