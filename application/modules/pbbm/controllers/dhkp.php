<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class dhkp extends CI_Controller
{
    private $module = 'dhkp';
    
    function __construct()
    {
        parent::__construct();
        
        if (active_module() != 'pbbm') {
            show_404();
            exit;
        }
        
        $this->load->model('login_model');
        if ($grp = $this->login_model->check_user_app()) {
            $this->session->set_userdata('groupid', $grp->group_id);
            $this->session->set_userdata('groupkd', $grp->group_kode);
            $this->session->set_userdata('groupname', $grp->group_nama);
        }
        
        $this->load->model(array(
            'apps_model',
            'login_model',
            'pbbm_model'
        ));
        $this->pbbm_model->set_userarea();
    }
    
    function load_auth()
    {
        $this->load->library('module_auth', array(
            'module' => $this->module
        ));
    }
    
    public function index()
    {
        $this->load_auth();
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url());
        }
        
        if (ENVIRONMENT == 'development') {
            $tglawal     = '01-07-2013';
            $tglakhir    = '30-07-2013';
            $tahun_sppt2 = '2013';
            $tahun_sppt1 = '2013';
        } else {
            $tglawal     = date('d-m-Y');
            $tglakhir    = date('d-m-Y');
            $tahun_sppt1 = date('Y'); //'1999'; //minta tahun berjalan, kasih dahh
            $tahun_sppt2 = date('Y');
        }
        
        // kecamatan
        $this->load->model('kecModel', 'kec');
        $data['user_kec_kd'] = get_user_kec_kd();
        $data['kecamatan']   = $this->kec->getRecord(get_user_kec_kd());
        $kec_kd              = (isset($_GET['kec_kd']) ? $_GET['kec_kd'] : $data['user_kec_kd']);
        $data['kec_kd']      = $kec_kd;
        
        // kelurahan
        $this->load->model('kelModel', 'kel');
        $data['user_kel_kd'] = get_user_kel_kd();
        $data['kelurahan'] = $this->kel->getRecord($kec_kd, get_user_kel_kd());
        $kel_kd = (isset($_GET['kel_kd']) ? $_GET['kel_kd'] : $data['user_kel_kd']);
        $data['kel_kd']    = $kel_kd;
        
        // buku
        $buku         = (isset($_GET['buku']) ? $_GET['buku'] : '11');
        $data['buku'] = $buku;
        
        // tahun
        $tahun_sppt1         = (isset($_GET['tahun_sppt1']) ? $_GET['tahun_sppt1'] : $tahun_sppt1);
        $tahun_sppt2         = (isset($_GET['tahun_sppt2']) ? $_GET['tahun_sppt2'] : $tahun_sppt2);
        $data['tahun_sppt1'] = $tahun_sppt1;
        $data['tahun_sppt2'] = $tahun_sppt2;
        
        // tanggal
        if (isset($_GET['tglawal']) && $_GET['tglawal'])
            $tglawal = $_GET['tglawal'];
        
        if (isset($_GET['tglakhir']) && $_GET['tglakhir'])
            $tglakhir = $_GET['tglakhir'];
        
        $data['tglawal']  = $tglawal;
        $data['tglakhir'] = $tglakhir;
        
        // blok
        if (isset($_GET['blok1']) && $_GET['blok1'])
            $blok1 = $_GET['blok1'];
        else $blok1 = '001';
        if (isset($_GET['blok2']) && $_GET['blok2'])
            $blok2 = $_GET['blok2'];
        else $blok2 = '001';
        $data['blok1']  = $blok1;
        $data['blok2']  = $blok2;

        // tp
        $tp_kd = (isset($_GET['tp_kd'])) ? $_GET['tp_kd'] : '';
        $data['tp_kd'] = $tp_kd;
        $data['tp']    = $this->load->model('tp_model')->get_select();
        
        // load
        $data['current'] = 'dhkp';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['title']   = 'DHKP';
        
        $data['data_source'] = active_module_url() . "dhkp/grid?tahun_sppt1={$tahun_sppt1}&kec_kd={$kec_kd}&kel_kd={$kel_kd}&blok1={$blok1}&blok2={$blok2}&buku={$buku}&tp_kd={$tp_kd}";
        $this->load->view('vdhkp', $data);
    }
    
    function grid()
    {
        ob_start("ob_gzhandler");
        $aColumns     = array(
            'nop',
            'wp',
            'alamat',
            'terhutang',
            'tgl_pembayaran_sppt'
        );
        $sIndexColumn = "nop";
        
        // Get params
        $tglm = (isset($_GET['tglawal'])) ? $_GET['tglawal'] : date('d-m-Y');
        $tgls = (isset($_GET['tglakhir'])) ? $_GET['tglakhir'] : date('d-m-Y');
        
        $tglm = substr($tglm, 6, 4) . '-' . substr($tglm, 3, 2) . '-' . substr($tglm, 0, 2);
        $tgls = substr($tgls, 6, 4) . '-' . substr($tgls, 3, 2) . '-' . substr($tgls, 0, 2);
        
        $tahun_sppt1 = (isset($_GET['tahun_sppt1'])) ? $_GET['tahun_sppt1'] : date('Y');
        $tahun_sppt2 = (isset($_GET['tahun_sppt2'])) ? $_GET['tahun_sppt2'] : date('Y');
        
        $buku    = (isset($_GET['buku'])) ? $_GET['buku'] : '11';
        $bukumin = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];
        
        $kec_kd = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();
            
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kec_kd = get_user_kel_kd();
                
        // blok
        if (isset($_GET['blok1']) && $_GET['blok1'])
            $blok1 = $_GET['blok1'];
        else $blok1 = '001';
        if (isset($_GET['blok2']) && $_GET['blok2'])
            $blok2 = $_GET['blok2'];
        else $blok2 = '001';

        // tp
        $tp_kd = (isset($_GET['tp_kd'])) ? $_GET['tp_kd'] : '';
        $data['tp_kd'] = $tp_kd;
        $data['tp']    = $this->load->model('tp_model')->get_select();
        
        // Limit
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . $_GET['iDisplayLength'] . " OFFSET " . $_GET['iDisplayStart'];
        }
        
        // Ordering
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    if ($aColumns[intval($_GET['iSortCol_' . $i])] == "bphtbno" || $aColumns[intval($_GET['iSortCol_' . $i])] == "tanggal") {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . $_GET['sSortDir_' . $i] . ", ";
                    } else {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . ' ' . $_GET['sSortDir_' . $i] . ", ";
                    }
                }
            }
            
            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY ") {
                $sOrder = "";
            }
        }
        
        // Filtering
        $where = "WHERE 1=1
            AND a.kd_propinsi='" . KD_PROPINSI . "'
            AND a.kd_dati2='" . KD_DATI2 . "'
            AND a.thn_pajak_sppt = '{$tahun_sppt1}'
            AND a.pbb_yg_harus_dibayar_sppt between {$bukumin} AND {$bukumax} 
            AND a.kd_blok BETWEEN '{$blok1}' AND '{$blok2}' ";
        
        if ($kec_kd != "000")
            $where .= " AND a.kd_kecamatan='{$kec_kd}'";
        if ($kel_kd != "000")
           $where .= " AND a.kd_kelurahan='{$kel_kd}'";
        
        $search = '';
        $sSearch   = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";        
        if ($sSearch) {
            $search .= " AND a.nm_wp_sppt ilike '%{$sSearch}%'";
            $search .= " AND a.jln_wp_sppt ilike '%{$sSearch}%'";
        }
                
        /// -- DARI SINI ..
        $fields     = explode(',', POS_FIELD);
        $pos_fld    = '';
        $pos_join   = '';
        $pos_uraian = '';
        $fs         = '';
        foreach ($fields as $f) {
            $fs = $f;
            if ($f == 'kd_kanwil_bank')
                $fs = 'kd_kanwil';
            else if ($f == 'kd_kppbb_bank')
                $fs = 'kd_kppbb';
            
            $pos_fld .= "a.{$f}, ";
            $pos_join .= "a.{$f}=tp.{$fs} and ";
            $pos_uraian .= "tp.{$fs}||";
        }
        $pos_fld    = substr($pos_fld, 0, -2);
        $pos_join   = substr($pos_join, 0, -4);
        $pos_uraian = substr($pos_uraian, 0, -2);
        
        $tp_kd = (isset($_GET['tp_kd'])) ? $_GET['tp_kd'] : '';
        if ($tp_kd != "")
            $where .= " AND {$pos_uraian} = '{$tp_kd}'";
        
        // Output
        $pg_pokok = 0;
        $pg_denda = 0;
        $pg_total = 0;
        
        $sql_query_r = "SELECT coalesce(count(*),0) as jml_baris, sum(a.pbb_yg_harus_dibayar_sppt) total_pbb
            FROM sppt a
            LEFT JOIN tempat_pembayaran tp ON {$pos_join}
            LEFT JOIN pembayaran_sppt b
              ON  a.kd_propinsi = b.kd_propinsi 
              AND a.kd_dati2 = b.kd_dati2 
              AND a.kd_kecamatan = b.kd_kecamatan 
              AND a.kd_kelurahan = b.kd_kelurahan
              AND a.kd_blok = b.kd_blok 
              AND a.no_urut = b.no_urut 
              AND a.kd_jns_op = b.kd_jns_op 
              AND a.thn_pajak_sppt = b.thn_pajak_sppt 

            $where $search ";
            
        $qry       = $this->db->query($sql_query_r);
        $pg_total  = $qry->row()->total_pbb;
        $iTotal    = $qry->row()->jml_baris;
        $iFiltered = $iTotal;
        
        $sql_query_r = "SELECT a.kd_propinsi||'.'||a.kd_dati2||'-'||a.kd_kecamatan||'.'||a.kd_kelurahan ||'-'|| a.kd_blok ||'.'||a.no_urut||'.'|| a.kd_jns_op nop, 
            a.nm_wp_sppt wp, a.jln_wp_sppt alamat, a.pbb_yg_harus_dibayar_sppt terhutang, b.tgl_pembayaran_sppt
            FROM sppt a
            LEFT JOIN tempat_pembayaran tp ON {$pos_join}
            LEFT JOIN pembayaran_sppt b
              ON  a.kd_propinsi = b.kd_propinsi 
              AND a.kd_dati2 = b.kd_dati2 
              AND a.kd_kecamatan = b.kd_kecamatan 
              AND a.kd_kelurahan = b.kd_kelurahan
              AND a.kd_blok = b.kd_blok 
              AND a.no_urut = b.no_urut 
              AND a.kd_jns_op = b.kd_jns_op 
              AND a.thn_pajak_sppt = b.thn_pajak_sppt 

            $where $search $sLimit ";
        
        // print_r($sql_query_r); exit;
        $qry = $this->db->query($sql_query_r);
        
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => intval($iTotal),
            "iTotalDisplayRecords" => intval($iFiltered),
            // "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i == 3)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                elseif ($i == 4) {
                    if(!empty($aRow->$aColumns[$i]))
                        $row[] = date('d-m-Y', strtotime($aRow->$aColumns[$i]));
                    else
                        $row[] = '';
                } else
                    $row[] = $aRow->$aColumns[$i];
            }
            
            // $pg_pokok += $aRow->$aColumns[3];
            // $pg_denda += $aRow->$aColumns[4];
            // $pg_total += $aRow->$aColumns[5];
            
            $output['aaData'][] = $row;
        }
        
        $output['pokok'] = number_format($pg_pokok, 0, ',', '.');
        $output['denda'] = number_format($pg_denda, 0, ',', '.');
        $output['total'] = number_format($pg_total, 0, ',', '.');
        
        echo json_encode($output);
    }
    
	function show_rpt() {
		$cls_mtd_html = $this->router->fetch_class()."/cetak/html/";
		$cls_mtd_pdf  = $this->router->fetch_class()."/cetak/pdf/";
		$data['rpt_html'] = active_module_url($cls_mtd_html. $_SERVER['QUERY_STRING']);;
		$data['rpt_pdf']  = active_module_url($cls_mtd_pdf . $_SERVER['QUERY_STRING']);;
        $this->load->view('vjasper_viewer', $data);
	}
	
	function cetak() {
        $type = $this->uri->segment(4);
		$rptx = $this->input->get('rpt');

        $tglm = (isset($_GET['tglawal'])) ? $_GET['tglawal'] : date('d-m-Y');
        $tgls = (isset($_GET['tglakhir'])) ? $_GET['tglakhir'] : date('d-m-Y');
        
        $tglm = substr($tglm, 6, 4) . '-' . substr($tglm, 3, 2) . '-' . substr($tglm, 0, 2);
        $tgls = substr($tgls, 6, 4) . '-' . substr($tgls, 3, 2) . '-' . substr($tgls, 0, 2);
        
        $tahun_sppt1 = (isset($_GET['tahun_sppt1'])) ? $_GET['tahun_sppt1'] : date('Y');
        $tahun_sppt2 = (isset($_GET['tahun_sppt2'])) ? $_GET['tahun_sppt2'] : date('Y');
        
        $buku    = (isset($_GET['buku'])) ? $_GET['buku'] : '11';
        $bukumin = $this->pbbm_model->rangebuku[substr($buku, 0, 1)][0];
        $bukumax = $this->pbbm_model->rangebuku[substr($buku, 1, 1)][1];
        
        $kec_kd = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        if (get_user_kec_kd() != '000' && get_user_kec_kd() != $kec_kd)
            $kec_kd = get_user_kec_kd();
            
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        if (get_user_kel_kd() != '000' && get_user_kel_kd() != $kel_kd)
            $kec_kd = get_user_kel_kd();
                
        // blok
        if (isset($_GET['blok1']) && $_GET['blok1'])
            $blok1 = $_GET['blok1'];
        else $blok1 = '001';
        if (isset($_GET['blok2']) && $_GET['blok2'])
            $blok2 = $_GET['blok2'];
        else $blok2 = '001';

        // 
        $kec = (isset($_GET['kec'])) ? strtoupper($_GET['kec']) : '';
        $kel = (isset($_GET['kel'])) ? strtoupper($_GET['kel']) : '';
        $tp = (isset($_GET['tp'])) ? strtoupper($_GET['tp']) : '';
        
        
        
        // posfield
        $fields     = explode(',', POS_FIELD);
        $pos_fld    = '';
        $pos_join   = '';
        $pos_uraian = '';
        $fs         = '';
        foreach ($fields as $f) {
            $fs = $f;
            if ($f == 'kd_kanwil_bank')
                $fs = 'kd_kanwil';
            else if ($f == 'kd_kppbb_bank')
                $fs = 'kd_kppbb';
            
            $pos_fld .= "a.{$f}, ";
            $pos_join .= "a.{$f}=tp.{$fs} and ";
            $pos_uraian .= "tp.{$fs}||";
        }
        $pos_fld    = substr($pos_fld, 0, -2);
        $pos_join   = substr($pos_join, 0, -4);
        $pos_uraian = substr($pos_uraian, 0, -2);
        
        // kondisi
        $kondisi = " AND a.kd_propinsi='" . KD_PROPINSI . "'
            AND a.kd_dati2='" . KD_DATI2 . "'
            AND a.thn_pajak_sppt = '{$tahun_sppt1}'
            AND a.pbb_yg_harus_dibayar_sppt between {$bukumin} AND {$bukumax} 
            AND a.kd_blok BETWEEN '{$blok1}' AND '{$blok2}' ";
        
        if ($kec_kd != "000")
            $kondisi .= " AND a.kd_kecamatan='{$kec_kd}'";
        if ($kel_kd != "000")
           $kondisi .= " AND a.kd_kelurahan='{$kel_kd}'";
        
        $tp_kd = (isset($_GET['tp_kd'])) ? $_GET['tp_kd'] : '';
        if ($tp_kd != "")
            $kondisi .= " AND {$pos_uraian} = '{$tp_kd}'";
        
        // parse
        $bukumin = (int)substr($buku, 0, 1);
        $bukumax = (int)substr($buku, 1, 1);
        $buku = "";
        for($i=$bukumin;$i<=$bukumax;$i++) 
            $buku .= $i.","; 
        $buku = substr($buku, 0, strlen($buku)-1);
        
        $prop  = $this->db->query("select nm_propinsi from ref_propinsi where kd_propinsi='".KD_PROPINSI."';")->row()->nm_propinsi;
        $dati2 = $this->db->query("select nm_dati2 from ref_dati2 where kd_propinsi='".KD_PROPINSI."' AND kd_dati2='".KD_DATI2."';")->row()->nm_dati2;
        
        // report
		$jasper = $this->load->library('Jasper');
		$params = array(
			"daerah" => LICENSE_TO,
			"dinas" => LICENSE_TO_SUB,
			"logo" => base_url("assets/img/logorpt__.jpg"),
            
			"tahun" => $tahun_sppt1, 
			"buku" => $buku, 
			"prop" => $prop, 
			"dati2" => $dati2, 
			"kec" => $kec, 
			"kel" => $kel, 
			"tp" => $tp, 
			"blok" => $blok1." S.D ".$blok2, 
            
			"pos_join" => $pos_join, 
			"kondisi" => $kondisi, 
		);
        // $rptx = 'dhkp_end';
        // echo "<pre>";print_r($params); exit;
		echo $jasper->cetak($rptx, $params, $type, false);
	}
    
}
