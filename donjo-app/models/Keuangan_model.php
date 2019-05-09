<?php
class Keuangan_model extends CI_model {

  private $zip_dir = '';
  private $id_master_keuangan;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('upload');
    $this->load->helper('donjolib');
    $this->load->helper('pict_helper');
    $this->uploadConfig = array(
      'upload_path' => LOKASI_KEUANGAN_ZIP,
      'allowed_types' => 'zip',
      'max_size' => max_upload()*1024,
    );
  }

  // $file = full path ke file yg akan diproses
  private function extract_file($file)
  {
    // print_r($file);die();
    $csv = fopen($file, "r");
    $header = fgetcsv($csv); // baris berisi nama kolom
    $data = array(); // array untuk diisi semua baris
    while (($row = fgetcsv($csv)) !== FALSE)
    {
        $baris = array();
        $baris['id_keuangan_master'] = $this->id_master_keuangan;
        foreach($row as $key => $value)
        {
          $baris[$header[$key]] = $value; // $baris['nama-kolom'] = isi-kolom
        }
        $data[] = $baris;
    }
    fclose($csv);
    return $data;
  }

  private function get_master_keuangan($id_master_keuangan)
  {
      $this->zip_dir = LOKASI_KEUANGAN_ZIP.pathinfo($_FILES['keuangan']['name'], PATHINFO_FILENAME);
    if (!empty($id_master_keuangan))
    {
      $this->id_master_keuangan = $id_master_keuangan;
    }
    else
    {
      $csv_versi = fopen($this->zip_dir.'/'.'Ref_Version.csv', "r");
      fgetcsv($csv_versi); // abaikan baris header
      $data_versi = fgetcsv($csv_versi);
      $csv_anggaran= fopen($this->zip_dir.'/'.'Ta_Anggaran.csv', "r");
      fgetcsv($csv_anggaran); // abaikan baris header
      $data_anggaran = fgetcsv($csv_anggaran); // baris pertama
      $data_master = array(
        'versi_database' => $data_versi[0],
        'tahun_anggaran' => $data_anggaran[1],
        'aktif' => 1
      );
      $this->db->insert('keuangan_master', $data_master);
      $this->id_master_keuangan = $this->db->insert_id();
    }

  }

  public function extract()
  {
    $this->id_master_keuangan = '';
    $this->get_master_keuangan();
    $data_siskeudes = array(
      'keuangan_ref_bank_desa' => 'Ref_Bank_Desa.csv',
      'keuangan_ref_bel_operasional' => 'Ref_Bel_Operasional.csv',
      'keuangan_ref_bidang' => 'Ref_Bidang.csv',
      'keuangan_ref_bunga' => 'Ref_Bunga.csv',
      'keuangan_ref_desa' => 'Ref_Desa.csv',
      'keuangan_ref_kecamatan' => 'Ref_Kecamatan.csv',
      'keuangan_ref_kegiatan' => 'Ref_Kegiatan.csv',
      'keuangan_ref_korolari' => 'Ref_Korolari.csv',
      'keuangan_ref_neraca_close' => 'Ref_NeracaClose.csv',
      'keuangan_ref_perangkat' => 'Ref_Perangkat.csv',
      'keuangan_ref_potongan' => 'Ref_Potongan.csv',
      'keuangan_ref_rek1' => 'Ref_Rek1.csv',
      'keuangan_ref_rek2' => 'Ref_Rek2.csv',
      'keuangan_ref_rek3' => 'Ref_Rek3.csv',
      'keuangan_ref_rek4' => 'Ref_Rek4.csv',
      'keuangan_ref_sbu' => 'Ref_SBU.csv',
      'keuangan_ref_sumber' => 'Ref_Sumber.csv',
      'keuangan_ta_anggaran' => 'Ta_Anggaran.csv',
      'keuangan_ta_anggaran_log' => 'Ta_AnggaranLog.csv',
      'keuangan_ta_anggaran_rinci' => 'Ta_AnggaranRinci.csv',
      'keuangan_ta_bidang' => 'Ta_Bidang.csv',
      'keuangan_ta_jurnal_umum' => 'Ta_JurnalUmum.csv',
      'keuangan_ta_jurnal_umum_rinci' => 'Ta_JurnalUmumRinci.csv',
      'keuangan_ta_kegiatan' => 'Ta_Kegiatan.csv',
      'keuangan_ta_mutasi' => 'Ta_Mutasi.csv',
      'keuangan_ta_pajak' => 'Ta_Pajak.csv',
      'keuangan_ta_pajak_rinci' => 'Ta_PajakRinci.csv',
      'keuangan_ta_pemda' => 'Ta_Pemda.csv',
      'keuangan_ta_pencairan' => 'Ta_Pencairan.csv',
      'keuangan_ta_perangkat' => 'Ta_Perangkat.csv',
      'keuangan_ta_rab' => 'Ta_RAB.csv',
      'keuangan_ta_rab_rinci' => 'Ta_RABRinci.csv',
      'keuangan_ta_rab_sub' => 'Ta_RABSub.csv',
      'keuangan_ta_rpjm_bidang' => 'Ta_RPJM_Bidang.csv',
      'keuangan_ta_rpjm_kegiatan' => 'Ta_RPJM_Kegiatan.csv',
      'keuangan_ta_rpjm_misi' => 'Ta_RPJM_Misi.csv',
      'keuangan_ta_rpjm_pagu_indikatif' => 'Ta_RPJM_Pagu_Indikatif.csv',
      'keuangan_ta_rpjm_pagu_tahunan' => 'Ta_RPJM_Pagu_Tahunan.csv',
      'keuangan_ta_rpjm_sasaran' => 'Ta_RPJM_Sasaran.csv',
      'keuangan_ta_rpjm_tujuan' => 'Ta_RPJM_Tujuan.csv',
      'keuangan_ta_rpjm_visi' => 'Ta_RPJM_Visi.csv',
      'keuangan_ta_saldo_awal' => 'Ta_SaldoAwal.csv',
      'keuangan_ta_spj' => 'Ta_SPJ.csv',
      'keuangan_ta_spjpot' => 'Ta_SPJPot.csv',
      'keuangan_ta_spj_bukti' => 'Ta_SPJBukti.csv',
      'keuangan_ta_spj_rinci' => 'Ta_SPJRinci.csv',
      'keuangan_ta_spj_sisa' => 'Ta_SPJSisa.csv',
      'keuangan_ta_spp' => 'Ta_SPP.csv',
      'keuangan_ta_sppbukti' => 'Ta_SPPBukti.csv',
      'keuangan_ta_spppot' => 'Ta_SPPPot.csv',
      'keuangan_ta_spp_rinci' => 'Ta_SPPRinci.csv',
      'keuangan_ta_sts' => 'Ta_STS.csv',
      'keuangan_ta_sts_rinci' => 'Ta_STSRinci.csv',
      'keuangan_ta_tbp' => 'Ta_TBP.csv',
      'keuangan_ta_tbp_rinci' => 'Ta_TBPRinci.csv',
      'keuangan_ta_triwulan' => 'Ta_Triwulan.csv',
      'keuangan_ta_triwulan_rinci' => 'Ta_TriwulanArsip.csv'
    );

    foreach ($data_siskeudes as $tabel_opensid => $file_siskeudes)
    {
      $data_tabel_siskeudes = $this->extract_file($this->zip_dir.'/'.$file_siskeudes);
      if (!empty($data_tabel_siskeudes))
      {
        $this->db->insert_batch($tabel_opensid, $data_tabel_siskeudes);
      }
    }
  }

  public function extract_update()
  {
    $this->id_master_keuangan = $_POST['id_keuangan_master'];
    $this->get_master_keuangan($this->id_master_keuangan);
    $data_siskeudes = array(
      'keuangan_ref_bank_desa' => 'Ref_Bank_Desa.csv',
      'keuangan_ref_bel_operasional' => 'Ref_Bel_Operasional.csv',
      'keuangan_ref_bidang' => 'Ref_Bidang.csv',
      'keuangan_ref_bunga' => 'Ref_Bunga.csv',
      'keuangan_ref_desa' => 'Ref_Desa.csv',
      'keuangan_ref_kecamatan' => 'Ref_Kecamatan.csv',
      'keuangan_ref_kegiatan' => 'Ref_Kegiatan.csv',
      'keuangan_ref_korolari' => 'Ref_Korolari.csv',
      'keuangan_ref_neraca_close' => 'Ref_NeracaClose.csv',
      'keuangan_ref_perangkat' => 'Ref_Perangkat.csv',
      'keuangan_ref_potongan' => 'Ref_Potongan.csv',
      'keuangan_ref_rek1' => 'Ref_Rek1.csv',
      'keuangan_ref_rek2' => 'Ref_Rek2.csv',
      'keuangan_ref_rek3' => 'Ref_Rek3.csv',
      'keuangan_ref_rek4' => 'Ref_Rek4.csv',
      'keuangan_ref_sbu' => 'Ref_SBU.csv',
      'keuangan_ref_sumber' => 'Ref_Sumber.csv',
      'keuangan_ta_anggaran' => 'Ta_Anggaran.csv',
      'keuangan_ta_anggaran_log' => 'Ta_AnggaranLog.csv',
      'keuangan_ta_anggaran_rinci' => 'Ta_AnggaranRinci.csv',
      'keuangan_ta_bidang' => 'Ta_Bidang.csv',
      'keuangan_ta_jurnal_umum' => 'Ta_JurnalUmum.csv',
      'keuangan_ta_jurnal_umum_rinci' => 'Ta_JurnalUmumRinci.csv',
      'keuangan_ta_kegiatan' => 'Ta_Kegiatan.csv',
      'keuangan_ta_mutasi' => 'Ta_Mutasi.csv',
      'keuangan_ta_pajak' => 'Ta_Pajak.csv',
      'keuangan_ta_pajak_rinci' => 'Ta_PajakRinci.csv',
      'keuangan_ta_pemda' => 'Ta_Pemda.csv',
      'keuangan_ta_pencairan' => 'Ta_Pencairan.csv',
      'keuangan_ta_perangkat' => 'Ta_Perangkat.csv',
      'keuangan_ta_rab' => 'Ta_RAB.csv',
      'keuangan_ta_rab_rinci' => 'Ta_RABRinci.csv',
      'keuangan_ta_rab_sub' => 'Ta_RABSub.csv',
      'keuangan_ta_rpjm_bidang' => 'Ta_RPJM_Bidang.csv',
      'keuangan_ta_rpjm_kegiatan' => 'Ta_RPJM_Kegiatan.csv',
      'keuangan_ta_rpjm_misi' => 'Ta_RPJM_Misi.csv',
      'keuangan_ta_rpjm_pagu_indikatif' => 'Ta_RPJM_Pagu_Indikatif.csv',
      'keuangan_ta_rpjm_pagu_tahunan' => 'Ta_RPJM_Pagu_Tahunan.csv',
      'keuangan_ta_rpjm_sasaran' => 'Ta_RPJM_Sasaran.csv',
      'keuangan_ta_rpjm_tujuan' => 'Ta_RPJM_Tujuan.csv',
      'keuangan_ta_rpjm_visi' => 'Ta_RPJM_Visi.csv',
      'keuangan_ta_saldo_awal' => 'Ta_SaldoAwal.csv',
      'keuangan_ta_spj' => 'Ta_SPJ.csv',
      'keuangan_ta_spjpot' => 'Ta_SPJPot.csv',
      'keuangan_ta_spj_bukti' => 'Ta_SPJBukti.csv',
      'keuangan_ta_spj_rinci' => 'Ta_SPJRinci.csv',
      'keuangan_ta_spj_sisa' => 'Ta_SPJSisa.csv',
      'keuangan_ta_spp' => 'Ta_SPP.csv',
      'keuangan_ta_sppbukti' => 'Ta_SPPBukti.csv',
      'keuangan_ta_spppot' => 'Ta_SPPPot.csv',
      'keuangan_ta_spp_rinci' => 'Ta_SPPRinci.csv',
      'keuangan_ta_sts' => 'Ta_STS.csv',
      'keuangan_ta_sts_rinci' => 'Ta_STSRinci.csv',
      'keuangan_ta_tbp' => 'Ta_TBP.csv',
      'keuangan_ta_tbp_rinci' => 'Ta_TBPRinci.csv',
      'keuangan_ta_triwulan' => 'Ta_Triwulan.csv',
      'keuangan_ta_triwulan_rinci' => 'Ta_TriwulanArsip.csv'
    );

    foreach ($data_siskeudes as $tabel_opensid => $file_siskeudes)
    {
      $data_tabel_siskeudes = $this->extract_file($this->zip_dir.'/'.$file_siskeudes);
      if (!empty($data_tabel_siskeudes))
      {
        $this->db->update_batch($tabel_opensid, $data_tabel_siskeudes);
      }
    }
  }

  public function cek_master_keuangan($file)
  {
    $this->upload->initialize($this->uploadConfig);
    $adaLampiran = !empty($_FILES['keuangan']['name']);
    $versi_database = '';
    $tahun_anggaran = '';
    if ($this->upload->do_upload('keuangan'))
    {
      $data = $this->upload->data();
      $zip_file = new ZipArchive;
      $full_path = $data['full_path'];
      $this->zip_dir = LOKASI_KEUANGAN_ZIP.pathinfo($_FILES['keuangan']['name'], PATHINFO_FILENAME);
      if (!file_exists($this->zip_dir))
      {
        mkdir($this->zip_dir, 0755);
      }

      if ($zip_file->open($full_path) === TRUE)
      {
        $zip_file->extractTo($this->zip_dir);
        $csv_versi = fopen($this->zip_dir.'/'.'Ref_Version.csv', "r");
        while (($datacsv_versi = fgetcsv($csv_versi)) !== FALSE)
        {
          $csv_versi_data = $datacsv_versi;
        }
        $csv_versi_anggaran = fopen($this->zip_dir.'/'.'Ta_Anggaran.csv', "r");
        while (($datacsv_versi_anggaran = fgetcsv($csv_versi_anggaran)) !== FALSE)
        {
          $data_versi_anggaran = $datacsv_versi_anggaran;
        }

        $versi_database = $csv_versi_data[0];
        $tahun_anggaran = $data_versi_anggaran[1];
      }
      else
      {
        log_message('error', '== Tidak bisa extract '.$full_path);
      }
      $zip_file->close();
    }
    $this->db->where('versi_database', $versi_database);
    $this->db->where('tahun_anggaran', $tahun_anggaran);
    $result = $this->db->get('keuangan_master')->row();
    return $result;
  }
}
