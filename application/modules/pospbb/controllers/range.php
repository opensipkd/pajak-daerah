<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class range extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('login')) {
            $this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
            redirect('login');
            exit;
        }

        if (!is_super_admin() && !isset($this->session->userdata['tpnm'])) {
            show_404();
            exit;
        }

        $module = 'POSC';
        $this->load->library('module_auth', array(
            'module' => $module
        ));
    $this->load->helper('sipkd_helper');

        $this->load->model(array(
            'apps_model'
        ));
        $this->load->model(array(
            'sppt_model',
            'payment_model'
        ));
    }

    public function index()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }

        $filter         = $this->session->userdata('pos_filter');
        $filter         = isset($filter) ? $filter : '';
        $data['prefix'] = KD_PROPINSI . "." . KD_DATI2;
        $data['tpnm']   = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : '';

        $data['filter'] = $filter;

        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('range/simpan');
        $data['current'] = 'stts';

        $this->load->view('rangev', $data);
    }

    public function cari()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }

        $range1 = $this->uri->segment(4);
        $range2 = $this->uri->segment(5);
        $thn    = $this->uri->segment(6);

        if ($range1 && $range2 && $thn && $result = $this->sppt_model->get_by_range_thn($range1, $range2, $thn)) {
            $output = array(
                'found' => 1,
                //'sEcho' => intval($sEcho),
                'iTotalRecords' => $result['tot_rows'] + 1,
                'iTotalDisplayRecords' => $result['num_rows'] + 1,
                //'sql' => $result['sql'],
                'aaData' => array()
            );

            $sisatot  = 0;
            $dendatot = 0;
            $utangtot = 0;

            foreach ($result['query'] as $data) {
                $sisa  = (float) $data['pbb_yg_harus_dibayar_sppt'] - ($data['jml_sppt_yg_dibayar'] - (float) $data['denda_sppt']);
                $denda = 0;
                if (date($data['tgl_jatuh_tempo_sppt']) < date('Y-m-d'))
                    $denda = hitdenda($sisa, $data['tgl_jatuh_tempo_sppt']);

                //Untuk tahun <= 2014 denda di 0 kan. Sesuai request dari majalengka cc. EKO
                /*
                if(KD_PROPINSI=='32' && KD_DATI2=='12')
                    if((int)$thn <= 2014)
                        $denda = 0;
                */
                
                //Pangandara minta denda=0 cc. AA
                if(KD_PROPINSI=='32' && KD_DATI2=='19')
                    $denda = 0;
                        
                $utang = $sisa + $denda;

                $row = array();

                $row[]              = $data['kode'];
                $row[]              = $data['thn_pajak_sppt'];
                $row[]              = number_format($sisa, 0, ',', '.');
                $row[]              = number_format($denda, 0, ',', '.');
                $row[]              = number_format($utang, 0, ',', '.');
                $row[]              = $data['nm_wp_sppt'];
                $row[]              = $data['jln_wp_sppt'];
                $output['aaData'][] = $row;

                $sisatot += $sisa;
                $dendatot += $denda;
                $utangtot += $utang;

            }
            $row   = array();
            $row[] = 'TOTAL';
            $row[] = '';
            $row[] = number_format($sisatot, 0, ',', '.');
            $row[] = number_format($dendatot, 0, ',', '.');
            $row[] = number_format($utangtot, 0, ',', '.');
            $row[] = '';
            $row[] = '';


            $output['aaData'][] = $row;
            /*$output['iSisa']= number_format($sisatot,0,',','.');
            $output['iDenda']= number_format($dendatot,0,',','.');
            $output['iUtang']= number_format($utangtot,0,',','.');
            */
            echo json_encode($output);
            //	'terbilang'=>$terbilang
            //$terbilang=terbilang($utang);


        } else {
            $output = array(
                'found' => 0,
                //'sEcho' => intval($sEcho),
                'iTotalRecords' => 0,
                'iTotalDisplayRecords' => 0,
                //'sql' => $result['sql'],
                'aaData' => array()
            );
            echo json_encode($output);
        }
    }

    private function fvalidation()
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('range1', 'NOP Awal', 'required|numeric');
        $this->form_validation->set_rules('range1', 'NOP Akhir', 'required|numeric');
        $this->form_validation->set_rules('tahun', 'Tahun', 'required|numeric');
    }

    function simpan()
    {
        if (!$this->module_auth->create) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_insert);
            redirect('info');
        }

        $data['faction'] = active_module_url('range/simpan');
        $data['current'] = 'stts';

        $nop = trim($this->input->post('prefix')) . trim($this->input->post('blok'));
        $nop = urldecode($nop);
        $nop = str_replace('.', '', $nop);
        $nop = str_replace(' ', '', $nop);
        $nop = str_replace('-', '', $nop);
        $nop = preg_replace( '/[^0-9]/', '', $nop);

        $nop2 = trim($this->input->post('blok2'));
        $nop2 = urldecode($nop2);
        $nop2 = str_replace('.', '', $nop2);
        $nop2 = str_replace(' ', '', $nop2);
        $nop2 = str_replace('-', '', $nop2);
        $nop2 = preg_replace( '/[^0-9]/', '', $nop2);

        $thn = $this->input->post('tahun');

        $kd_propinsi  = substr($nop, 0, 2);
        $kd_dati2     = substr($nop, 2, 2);
        $kd_kecamatan = substr($nop, 4, 3);
        $kd_kelurahan = substr($nop, 7, 3);
        $kd_blok      = substr($nop, 10, 3);
        $no_urut      = substr($nop, 13, 4);
        $kd_jns       = substr($nop, 17, 1);

        $no_urut_2 = substr($nop2, 0, 4);
        $kd_jns_2  = substr($nop2, 4, 1);

        $thn_pajak_sppt = $thn;


        if ($nop && $thn && $qry = $this->sppt_model->get_by_range_thn($nop, $nop2, $thn)) {
            $saved = array();
            $cetak = array();

            foreach ($qry['query'] as $row) {
                $sisa = (float) $row['pbb_yg_harus_dibayar_sppt'] - ($row['jml_sppt_yg_dibayar'] - (float) $row['denda_sppt']);
                if ($sisa > 0) {

                    $denda = 0;
                    if (date($row['tgl_jatuh_tempo_sppt']) < date('Y-m-d'))
                        $denda = hitdenda($sisa, $row['tgl_jatuh_tempo_sppt']);

                    //Untuk tahun <= 2014 denda di 0 kan. Sesuai request dari majalengka cc. EKO
                    /*
                    if(KD_PROPINSI=='32' && KD_DATI2=='12')
                        if((int)$thn <= 2014)
                            $denda = 0;
                    */
                            
                    //Pangandara minta denda=0 cc. AA
                    if(KD_PROPINSI=='32' && KD_DATI2=='19')
                        $denda = 0;

                    $utang = $sisa + $denda;

                    $denda_sppt          = $denda;
                    $jml_sppt_yg_dibayar = $utang;
                    $tgl_pembayaran_sppt = date('Y-m-d');
                    $tgl_rekam_byr_sppt  = date('Y-m-d');
                    $nip_rekam_byr_sppt  = $this->session->userdata('nip');
                    $nopb                = $row['kode'];
                    $no_urut             = $row['no_urut'];
                    $kd_jns_op           = $row['kd_jns_op'];
                    ;
                    $pembayaran_sppt_ke = $this->payment_model->get_pembayaran_ke($nopb, $thn);
                    $data               = array(
                        'kd_propinsi' => $kd_propinsi,
                        'kd_dati2' => $kd_dati2,
                        'kd_kecamatan' => $kd_kecamatan,
                        'kd_kelurahan' => $kd_kelurahan,
                        'kd_blok' => $kd_blok,
                        'no_urut' => $no_urut,
                        'kd_jns_op' => $kd_jns_op,
                        'thn_pajak_sppt' => $thn_pajak_sppt,
                        'pembayaran_sppt_ke' => $pembayaran_sppt_ke,
                        'denda_sppt' => $denda_sppt,
                        'jml_sppt_yg_dibayar' => $jml_sppt_yg_dibayar,
                        'tgl_pembayaran_sppt' => $tgl_pembayaran_sppt,
                        'tgl_rekam_byr_sppt' => $tgl_rekam_byr_sppt,
                        'nip_rekam_byr_sppt' => $nip_rekam_byr_sppt,
                        'user_id' => $this->session->userdata('userid')
                    );

                    $fields = explode(',', POS_FIELD); //seuai parameter yang ada di master konfig
                    foreach ($fields as $f) {
                        $f    = trim($f);
                        $data = array_merge($data, array(
                            trim($f) => $this->session->userdata($f)
                        ));
                    }

                    $this->payment_model->update_pmb($data);
                    $prints  = array(
                        'nop' => $nopb,
                        'thn' => $thn_pajak_sppt,
                        'ke' => $pembayaran_sppt_ke
                    );
                    $saved[] = $prints;
                        
                    //buat cetak
                    //32.10.030.011.000-5327.7 
                    $nopp = $kd_propinsi.".".$kd_dati2.".".$kd_kecamatan.".".$kd_kelurahan.".";
                    $nopp.= $kd_blok."-".$no_urut.".".$kd_jns_op;
                    
                    if($qctk = $this->payment_model->get_by_nop_thn_ke($nopb, $thn_pajak_sppt,$pembayaran_sppt_ke)) {
                        $dctk = array();
                        $dctk[8] =  $qctk->nm_tp;
                        $dctk[9] =  $qctk->thn_pajak_sppt;
                        $dctk[10] = $qctk->nm_wp_sppt;
                        $dctk[11] = $qctk->nm_kecamatan;
                        $dctk[12] = $qctk->nm_kelurahan;
                        $dctk[13] = $nopp; //kode;//x
                        $dctk[14] = $qctk->jml_sppt_yg_dibayar;
                        $dctk[15] = $qctk->denda_sppt;
                        $dctk[16] = $qctk->tgl_jatuh_tempo_sppt;
                        $dctk[17] = $qctk->tgl_pembayaran_sppt;
                        $dctk[18] = $qctk->jml_sppt_yg_dibayar;
                        $dctk[19] = $qctk->luas_bumi_sppt;
                        $dctk[20] = $qctk->luas_bng_sppt;
                        
                        $dctk[40] = $qctk->jln_wp_sppt;
                        $dctk[41] = $qctk->blok_kav_no_wp_sppt;
                        $dctk[42] = $qctk->nm_propinsi;
                        $dctk[43] = $qctk->nm_dati2;
                    }
                    $cetak[] = $dctk;
                }
            }
            $ret = array();
            $ret['simpan'] = 'sukses';
            $ret['saved']  = $saved;
            $ret['cetak']  = $cetak;
            echo json_encode($ret);
            
            // $this->cetak($saved);
        } else
            echo json_encode(array('simpan'=>'gagal'));
    }
    
  function cetak() {
    $cetak = $this->input->post('dtCetak');
    $tambahan_data2 = array();

    if(isset($cetak)) {
      $i = 1;
      $j = json_decode($cetak, true);
      if(count($j['dtCetak']) > 0)
        $this->load->view(STTS2, $j);
    }
  }

  function cetak_draft() {
    $cetak = $this->input->post('dtCetak');
    $tambahan_data2 = array();

    if(isset($cetak)) {
      $i = 1;
      $j = json_decode($cetak, true);
      if(count($j['dtCetak']) > 0)
        $this->load->view(STTS4, $j);
    }
  }
    public function cetak_pdf() {
        $data = $_POST['data'];
        $data = json_decode($data, true);
        $join = '';

		//tambahan parameter join untuk relasi tabel pembayaran sppt dgn tempat pembayaran 
		if (DEF_POS_TYPE==1) {
			$join =" ps.kd_kanwil=tp.kd_kanwil AND ps.kd_kantor=tp.kd_kantor AND ps.kd_tp=tp.kd_tp ";
		} elseif (DEF_POS_TYPE==2) {
			$join =" ps.kd_kanwil=tp.kd_kanwil AND ps.kd_kantor=tp.kd_kantor AND ps.kd_bank_tunggal=tp.kd_bank_tunggal AND ps.kd_bank_persepsi=tp.kd_bank_persepsi AND  ps.kd_tp=tp.kd_tp ";
		} 
		
        $rpt   = "stts_nop";
        $sttsno = $_POST['sttsno'];
        $rpt  .= $sttsno;
        
		if (count($data)>0){    
            $param = '';
            foreach ($data as $d) {
                $param_n = "{$d['nop']}{$d['thn']}{$d['ke']}";
                $param_x = preg_replace("/[^0-9]/","",$param_n);
                $param_x = " ('".substr($param_x,0,2)."','".substr($param_x,2,2)."','".
                               substr($param_x,4,3)."','".substr($param_x,7,3)."','".
                               substr($param_x,10,3)."','".substr($param_x,13,4)."','".
                               substr($param_x,17,1)."','".substr($param_x,18,4)."',".
                               substr($param_x,22,1).")";                    
                $param  .= "{$param_x},";
            }
            $param = substr($param, 0, -1);
            
            $params = array(
                "daerah" => LICENSE_TO,
                "dinas" => LICENSE_TO_SUB,
                "logo" => base_url("assets/img/logorpt__.jpg"),

                "param" => $param,
                "join" => $join, 
            );

            $jasper = $this->load->library('Jasper');
            echo $jasper->cetak(POS_WIL."/{$rpt}", $params, "pdf", false);
            
        } else {
            echo "No Data";
        }
    }
    
    public function cetak_bank() {
        $data = $_POST['data'];
        $data = json_decode($data, true);
        $join ='';
		
		//tambahan parameter join untuk relasi tabel pembayaran sppt dgn tempat pembayaran 
		if (DEF_POS_TYPE==1) {
			$join =" ps.kd_kanwil=tp.kd_kanwil AND ps.kd_kantor=tp.kd_kantor AND ps.kd_tp=tp.kd_tp ";
		} elseif (DEF_POS_TYPE==2) {
			$join =" ps.kd_kanwil=tp.kd_kanwil AND ps.kd_kantor=tp.kd_kantor AND ps.kd_bank_tunggal=tp.kd_bank_tunggal AND ps.kd_bank_persepsi=tp.kd_bank_persepsi AND  ps.kd_tp=tp.kd_tp ";
		} 

		if (count($data)>0){    
            $param = '';
            foreach ($data as $d) {
                $param_n = "{$d['nop']}{$d['thn']}{$d['ke']}";
                $param_x = preg_replace("/[^0-9]/","",$param_n);
                $param_x = " ('".substr($param_x,0,2)."','".substr($param_x,2,2)."','".
                               substr($param_x,4,3)."','".substr($param_x,7,3)."','".
                               substr($param_x,10,3)."','".substr($param_x,13,4)."','".
                               substr($param_x,17,1)."','".substr($param_x,18,4)."',".
                               substr($param_x,22,1).")";                    
                $param  .= "{$param_x},";
            }
            $param = substr($param, 0, -1);
            
            $params = array(
                "daerah" => LICENSE_TO,
                "dinas" => LICENSE_TO_SUB,
                "logo" => base_url("assets/img/logorpt__.jpg"),

                "param" => $param,
                "join" => $join, 
            );

            $jasper = $this->load->library('Jasper');
            echo $jasper->cetak(POS_WIL."/stts_nop_bank", $params, "pdf", false);
            
        } else {
            echo "No Data";
        }
    }
}
