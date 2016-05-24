<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class pmb extends CI_Controller
{
    private $module = 'pbbmr';
    
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
        
        ob_start("ob_gzhandler");
        $data['iDisplayLength'] = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $data['iDisplayStart']  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $data['iSortCol_0']     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $data['iSortingCols']   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $data['sEcho']          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $data['sSearch']        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $data['sSearch_0']      = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $data['sSearch_1']      = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $data['sSearch_2']      = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $data['sSearch_3']      = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $data['sSearch_4']      = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        $data['sSortDir_0']     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        
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
        
        // sektor
        $sektor = (isset($_GET['sektor']) ? $_GET['sektor'] : '000');
        $data['sektor'] = $sektor;
        $data['sektors'] = array(
            (object) array(
                'kode'=>'10',
                'uraian'=>'PERDESAAN',
            ),
            (object) array(
                'kode'=>'20',
                'uraian'=>'PERKOTAAN',
            ),
        );
        
        // kecamatan
        $this->load->model('kecModel', 'kec');
        $data['user_kec_kd'] = get_user_kec_kd();
        $data['kecamatan']   = $this->kec->getRecord(get_user_kec_kd());
        $kec_kd              = (isset($_GET['kec_kd']) ? $_GET['kec_kd'] : $data['user_kec_kd']);
        $data['kec_kd']      = $kec_kd;
        
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
        
        // load
        $data['current'] = 'realisasi';
        $data['apps']    = $this->apps_model->get_active_only();
        $data['title']   = 'Penerimaan Pembayaran';
        
        $data['data_source'] = active_module_url() . "pmb/grid?tahun_sppt1={$tahun_sppt1}&tahun_sppt2={$tahun_sppt2}&tglawal={$tglawal}&tglakhir={$tglakhir}&kec_kd={$kec_kd}&buku={$buku}&sektor={$sektor}";
        
        $this->load->view('vpmb', $data);
    }
    
    function grid()
    {
        ob_start("ob_gzhandler");
        $aColumns     = array(
            'kode',
            'uraian',
            'thn_pajak_sppt',
            'pokok',
            'denda',
            'bayar'
        );
        $sIndexColumn = "kode";
        
        $iDisplayLength = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $iDisplayStart  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $iSortCol_0     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sSortDir_0     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        $sEcho          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        
        $sSearch   = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        $sSearch_0 = (isset($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : "";
        $sSearch_1 = (isset($_GET['sSearch_1'])) ? $_GET['sSearch_1'] : "";
        $sSearch_2 = (isset($_GET['sSearch_2'])) ? $_GET['sSearch_2'] : "";
        $sSearch_3 = (isset($_GET['sSearch_3'])) ? $_GET['sSearch_3'] : "";
        $sSearch_4 = (isset($_GET['sSearch_4'])) ? $_GET['sSearch_4'] : "";
        
        
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
        
        $sektor = (isset($_GET['sektor']) && is_numeric($_GET['sektor'])) ? $_GET['sektor'] : '000';
        
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
        $where = "WHERE p.tgl_pembayaran_sppt BETWEEN '$tglm' AND '$tgls'
            AND p.kd_propinsi='" . KD_PROPINSI . "'
            AND p.kd_dati2='" . KD_DATI2 . "'
            AND p.thn_pajak_sppt BETWEEN '$tahun_sppt1' AND '$tahun_sppt2' 
            AND k.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax ";
        
        if ($kec_kd != "000")
            $where .= " AND p.kd_kecamatan='$kec_kd'";
        // if ($kel_kd != "000")
        //    $where .= " AND p.kd_kelurahan='$kel_kd'";
        
        if ($sektor != "000")
            $where .= " AND kel.kd_sektor='{$sektor}'";
            
        $search = '';
        if ($sSearch) {
            $search .= " AND kel.nm_kelurahan ilike '%$sSearch%'";
            $search .= " AND kec.nm_kecamatan ilike '%$sSearch%'";
        }
        
        $iTotal    = 0;
        $iFiltered = 0;
        
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
            
            $pos_fld .= "p.{$f}, ";
            $pos_join .= "p.{$f}=tp.{$fs} and ";
            $pos_uraian .= "tp.{$fs}||";
        }
        $pos_fld    = substr($pos_fld, 0, -2);
        $pos_join   = substr($pos_join, 0, -4);
        $pos_uraian = substr($pos_uraian, 0, -2);
        
        $tp_kd = (isset($_GET['tp_kd'])) ? $_GET['tp_kd'] : '';
        if ($tp_kd != "")
            $where .= " AND {$pos_uraian} = '{$tp_kd}'";
        
        // Output
        // $sql_query_r = "SELECT  tgl_pembayaran_sppt kode,{$pos_uraian}||':'||tp.nm_tp uraian, p.thn_pajak_sppt,
        $sql_query_r = "SELECT  tgl_pembayaran_sppt kode,kel.nm_kelurahan||' - '||kec.nm_kecamatan uraian, p.thn_pajak_sppt,
            sum(p.jml_sppt_yg_dibayar - p.denda_sppt)  pokok, sum(p.denda_sppt) denda,
            sum(p.jml_sppt_yg_dibayar) bayar
            FROM sppt k
            INNER JOIN pembayaran_sppt p
            ON k.kd_propinsi = p.kd_propinsi
            AND k.kd_dati2 = p.kd_dati2
            AND k.kd_kecamatan = p.kd_kecamatan
            AND k.kd_kelurahan = p.kd_kelurahan
            AND k.kd_blok = p.kd_blok
            AND k.no_urut = p.no_urut
            AND k.kd_jns_op = p.kd_jns_op
            AND k.thn_pajak_sppt = p.thn_pajak_sppt
            -- LEFT JOIN tempat_pembayaran tp ON {$pos_join}
            INNER JOIN ref_kecamatan kec ON kec.kd_propinsi=p.kd_propinsi AND kec.kd_dati2=p.kd_dati2 AND kec.kd_kecamatan=p.kd_kecamatan
            INNER JOIN ref_kelurahan kel ON kel.kd_propinsi=p.kd_propinsi AND kel.kd_dati2=p.kd_dati2 AND kel.kd_kecamatan=p.kd_kecamatan AND kel.kd_kelurahan=p.kd_kelurahan
            $where $search
            GROUP BY 1,2,3
            ORDER BY 1,2,3
            $sLimit ";
        
        $output = array(
            "sEcho" => $sEcho,
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFiltered,
            "iDisplayStart" => $iDisplayStart,
            "iDisplayLength" => $iDisplayLength,
            // "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        
        $pg_pokok = 0;
        $pg_denda = 0;
        $pg_total = 0;
        
        $qry = $this->db->query($sql_query_r);
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($i > 2)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                elseif ($i == 0)
                    $row[] = date('d-m-Y', strtotime($aRow->$aColumns[$i]));
                else
                    $row[] = $aRow->$aColumns[$i];
            }
            
            $pg_pokok += $aRow->$aColumns[3];
            $pg_denda += $aRow->$aColumns[4];
            $pg_total += $aRow->$aColumns[5];
            
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
            
        $sektor = (isset($_GET['sektor']) && is_numeric($_GET['sektor'])) ? $_GET['sektor'] : '000';
        
        $kondisi = "AND p.tgl_pembayaran_sppt BETWEEN '$tglm' AND '$tgls'
            AND p.kd_propinsi='" . KD_PROPINSI . "'
            AND p.kd_dati2='" . KD_DATI2 . "'
            AND p.thn_pajak_sppt BETWEEN '$tahun_sppt1' AND '$tahun_sppt2' 
            AND k.pbb_yg_harus_dibayar_sppt between $bukumin AND $bukumax ";
        
        if ($kec_kd != "000")
            $kondisi .= " AND p.kd_kecamatan='$kec_kd'";
            
        if ($sektor != "000")
            $kondisi .= " AND kel.kd_sektor='$sektor'";
            
            
        $bukumin = (int)substr($buku, 0, 1);
        $bukumax = (int)substr($buku, 1, 1);
        $periode = "BUKU ";
        for($i=$bukumin;$i<=$bukumax;$i++) 
            $periode .= $i.","; 
        $periode = substr($periode, 0, strlen($periode)-1);
        $periode.= "\nPERIODE TANGGAL ".$_GET['tglawal']." S/D ".$_GET['tglakhir'];
            
        
		$jasper = $this->load->library('Jasper');
		$params = array(
			"daerah" => LICENSE_TO,
			"dinas" => LICENSE_TO_SUB,
			"logo" => base_url("assets/img/logorpt__.jpg"),
            
			"kondisi" => $kondisi, 
			"periode" => $periode, 
		);
        // echo"<pre>";print_r($params);exit;
		echo $jasper->cetak($rptx, $params, $type, false);
	}
    
}
