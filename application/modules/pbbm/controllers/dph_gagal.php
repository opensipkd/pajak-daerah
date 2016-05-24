<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dph_gagal extends CI_Controller
{
	private $module = 'dph_entri';
	private $controller = 'dph_gagal';
	
    function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('login')) {
            $this->session->set_flashdata('msg_warning', 'Session telah kadaluarsa, silahkan login ulang.');
            redirect('login');
            exit;
        }
		
		$this->load->library('module_auth',array('module'=> $this->module));

		$this->load->model(array('apps_model'));
		$this->load->model(array('dph_model','dph_payment_model'));
	}
    
    public function index()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect(active_module_url(''));
        }
        
        $data['current']      = 'dph';
        $data['apps']         = $this->apps_model->get_active_only();
		
        //FORM Parameter
		$tahun  = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
        $kec_kd = (isset($_GET['kec_kd']) ? $_GET['kec_kd'] : '000');
        $kel_kd = (isset($_GET['kel_kd']) ? $_GET['kel_kd'] : '000');
		$kec_kd = (get_user_kec_kd()!='000' && get_user_kec_kd()!=$kec_kd) ? get_user_kec_kd() : $kec_kd;
		$kel_kd = (get_user_kel_kd()!='000' && get_user_kel_kd()!=$kel_kd) ? get_user_kel_kd() : $kel_kd;
		   
        $data['tahun']     = $tahun;
        $data['kec_kd']    = $kec_kd;
        $data['kel_kd']    = $kel_kd;
        
        $this->load->model('kecModel', 'kec');
        $this->load->model('kelModel', 'kel');
		$data['kecamatan'] = $this->kec->getRecord('000');
		$data['kelurahan'] = $this->kel->getRecord($kec_kd, '000');
		
        $data['data_source']    = active_module_url() . "dph_gagal/grid?tahun=$tahun&kec_kd=$kec_kd&kel_kd=$kel_kd";
		
        //Grid Standard Parameter
        $data['iDisplayLength'] = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $data['iDisplayStart']  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $data['iSortingCols']   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $data['sEcho']          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $data['sSearch']        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
        
        $this->load->view('vdph_gagal', $data);
    }
    
    function grid()
    {
        ob_start("ob_gzhandler");
		
        $tahun  = (isset($_GET['tahun'])) ? $_GET['tahun'] : date('Y');
        $kec_kd = (isset($_GET['kec_kd']) && is_numeric($_GET['kec_kd'])) ? $_GET['kec_kd'] : '000';
        $kel_kd = (isset($_GET['kel_kd']) && is_numeric($_GET['kel_kd'])) ? $_GET['kel_kd'] : '000';
        
        $aColumns       = array(
            'id', 
            'kode_dph', 
            'nop', 
            'thn_pajak_sppt', 
            'pbb_terhutang_sppt', 
            'denda', 
            'jumlah', 
            'status_bayar', 
            'bayar',
            'pbb_terhutang_sppt', 
            'denda', 
            'jumlah', 
            'bayar'
        );
        $sIndexColumn   = "kode_lengkap";
        $iDisplayLength = (isset($_GET['iDisplayLength']) && is_numeric($_GET['iDisplayLength'])) ? $_GET['iDisplayLength'] : 15;
        $iDisplayLength = (int)$iDisplayLength;
        $iDisplayStart  = (isset($_GET['iDisplayStart']) && is_numeric($_GET['iDisplayStart'])) ? $_GET['iDisplayStart'] : 0;
        $iDisplayStart  = (int)$iDisplayStart;
        $iSortCol_0     = (isset($_GET['iSortCol_0']) && is_numeric($_GET['iSortCol_0'])) ? $_GET['iSortCol_0'] : 0;
        $iSortingCols   = (isset($_GET['iSortingCols']) && is_numeric($_GET['iSortingCols'])) ? $_GET['iSortingCols'] : 1;
        $sSortDir_0     = (isset($_GET['sSortDir_0'])) ? $_GET['sSortDir_0'] : "asc";
        $sEcho          = (isset($_GET['sEcho']) && is_numeric($_GET['sEcho'])) ? $_GET['sEcho'] : 1;
        $sSearch        = (isset($_GET['sSearch'])) ? $_GET['sSearch'] : "";
		
		/* Limit */
        $sLimit = "";
        if (isset($iDisplayLength) && $iDisplayStart != '-1') {
            // $sLimit = "LIMIT {$iDisplayLength} OFFSET {$iDisplayStart}";
        }

		/* Ordering */
        $sOrder = "ORDER BY kode_lengkap";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . ' ' . $_GET['sSortDir_' . $i] . ", ";
                }
            }
            
            $sOrder = substr_replace($sOrder, "", -2);
            
            if ($sOrder == "ORDER BY ") {
                $sOrder = "";
            }
        }
        
		/* Filtering */
        $sWhere = "WHERE thn_pajak_sppt = '{$tahun}' 
             AND kd_propinsi = '" . KD_PROPINSI . "' 
             AND kd_dati2 = '" . KD_DATI2 . "'";
			 
        if ($kec_kd != "000") {
            $sWhere .= " AND kd_kecamatan='{$kec_kd}'";
            if ($kel_kd != "000") $sWhere .= " AND kd_kelurahan='{$kel_kd}'";
        }
        
        $search = '';
        if ($sSearch) $search .= " AND (nama ilike '%$sSearch%' OR nama ilike '%$sSearch%')";
        
        /*
         * Output
         */
        $sql_query_r = "select * from (
            -- 1. di sppt sudah bayar, di pmb 0
            select 1 as kode, d.id, (d.kd_kecamatan||'-'||d.kd_kelurahan||'-'||d.tahun||'-'||d.kode) as kode_dph,
            s.kd_propinsi||'.'||s.kd_dati2||'-'||s.kd_kecamatan||'.'||s.kd_kelurahan ||'-'|| s.kd_blok ||'.'||s.no_urut||'.'||s.kd_jns_op nop, s.thn_pajak_sppt, 
            s.pbb_terhutang_sppt, dd.denda, s.pbb_terhutang_sppt+dd.denda jumlah, 
            case when s.status_pembayaran_sppt='1' then 'SUDAH BAYAR' else 'BELUM BAYAR' end as status_bayar,
            coalesce(p.jml_sppt_yg_dibayar,0) bayar,
            dd.kd_propinsi, dd.kd_dati2, kec.kd_kecamatan, kel.kd_kelurahan
            from dph d 
            inner join dph_payment dd on d.id=dd.dph_id
            inner join ref_kecamatan kec 
              on  kec.kd_propinsi = dd.kd_propinsi 
              and kec.kd_dati2 = dd.kd_dati2 
              and kec.kd_kecamatan = dd.kd_kecamatan 
            inner join ref_kelurahan kel 
              on  kel.kd_propinsi = dd.kd_propinsi 
              and kel.kd_dati2 = dd.kd_dati2 
              and kel.kd_kecamatan = dd.kd_kecamatan 
              and kel.kd_kelurahan = dd.kd_kelurahan
            inner join sppt s 
              on  dd.kd_propinsi = s.kd_propinsi 
              and dd.kd_dati2 = s.kd_dati2 
              and dd.kd_kecamatan = s.kd_kecamatan 
              and dd.kd_kelurahan = s.kd_kelurahan
              and dd.kd_blok = s.kd_blok 
              and dd.no_urut = s.no_urut 
              and dd.kd_jns_op = s.kd_jns_op 
              and dd.thn_pajak_sppt = s.thn_pajak_sppt 
            left join pembayaran_sppt p 
              on  dd.kd_propinsi = p.kd_propinsi 
              and dd.kd_dati2 = p.kd_dati2 
              and dd.kd_kecamatan = p.kd_kecamatan 
              and dd.kd_kelurahan = p.kd_kelurahan
              and dd.kd_blok = p.kd_blok 
              and dd.no_urut = p.no_urut 
              and dd.kd_jns_op = p.kd_jns_op 
              and dd.thn_pajak_sppt = p.thn_pajak_sppt 
            where s.status_pembayaran_sppt='1' and p.jml_sppt_yg_dibayar=0

            -- 2. di sppt belum bayar, di pmb sesuai
            union
            select 2 as kode, d.id, (d.kd_kecamatan||'-'||d.kd_kelurahan||'-'||d.tahun||'-'||d.kode) as kode_dph,
            s.kd_propinsi||'.'||s.kd_dati2||'-'||s.kd_kecamatan||'.'||s.kd_kelurahan ||'-'|| s.kd_blok ||'.'||s.no_urut||'.'||s.kd_jns_op nop, s.thn_pajak_sppt, 
            s.pbb_terhutang_sppt, dd.denda, s.pbb_terhutang_sppt+dd.denda jumlah, 
            case when s.status_pembayaran_sppt='1' then 'SUDAH BAYAR' else 'BELUM BAYAR' end as status_bayar,
            coalesce(p.jml_sppt_yg_dibayar,0) bayar,
            dd.kd_propinsi, dd.kd_dati2, kec.kd_kecamatan, kel.kd_kelurahan
            from dph d 
            inner join dph_payment dd on d.id=dd.dph_id
            inner join ref_kecamatan kec 
              on  kec.kd_propinsi = dd.kd_propinsi 
              and kec.kd_dati2 = dd.kd_dati2 
              and kec.kd_kecamatan = dd.kd_kecamatan 
            inner join ref_kelurahan kel 
              on  kel.kd_propinsi = dd.kd_propinsi 
              and kel.kd_dati2 = dd.kd_dati2 
              and kel.kd_kecamatan = dd.kd_kecamatan 
              and kel.kd_kelurahan = dd.kd_kelurahan
            inner join sppt s 
              on  dd.kd_propinsi = s.kd_propinsi 
              and dd.kd_dati2 = s.kd_dati2 
              and dd.kd_kecamatan = s.kd_kecamatan 
              and dd.kd_kelurahan = s.kd_kelurahan
              and dd.kd_blok = s.kd_blok 
              and dd.no_urut = s.no_urut 
              and dd.kd_jns_op = s.kd_jns_op 
              and dd.thn_pajak_sppt = s.thn_pajak_sppt 
            left join pembayaran_sppt p 
              on  dd.kd_propinsi = p.kd_propinsi 
              and dd.kd_dati2 = p.kd_dati2 
              and dd.kd_kecamatan = p.kd_kecamatan 
              and dd.kd_kelurahan = p.kd_kelurahan
              and dd.kd_blok = p.kd_blok 
              and dd.no_urut = p.no_urut 
              and dd.kd_jns_op = p.kd_jns_op 
              and dd.thn_pajak_sppt = p.thn_pajak_sppt 
            where s.status_pembayaran_sppt<>'1' and round(p.jml_sppt_yg_dibayar)=round(s.pbb_terhutang_sppt+dd.denda)

            -- 3. di sppt belum bayar, di pmb 0
            union
            select 3 as kode, d.id, (d.kd_kecamatan||'-'||d.kd_kelurahan||'-'||d.tahun||'-'||d.kode) as kode_dph,
            s.kd_propinsi||'.'||s.kd_dati2||'-'||s.kd_kecamatan||'.'||s.kd_kelurahan ||'-'|| s.kd_blok ||'.'||s.no_urut||'.'||s.kd_jns_op nop, s.thn_pajak_sppt, 
            s.pbb_terhutang_sppt, dd.denda, s.pbb_terhutang_sppt+dd.denda jumlah, 
            case when s.status_pembayaran_sppt='1' then 'SUDAH BAYAR' else 'BELUM BAYAR' end as status_bayar,
            coalesce(p.jml_sppt_yg_dibayar,0) bayar,
            dd.kd_propinsi, dd.kd_dati2, kec.kd_kecamatan, kel.kd_kelurahan
            from dph d 
            inner join dph_payment dd on d.id=dd.dph_id
            inner join ref_kecamatan kec 
              on  kec.kd_propinsi = dd.kd_propinsi 
              and kec.kd_dati2 = dd.kd_dati2 
              and kec.kd_kecamatan = dd.kd_kecamatan 
            inner join ref_kelurahan kel 
              on  kel.kd_propinsi = dd.kd_propinsi 
              and kel.kd_dati2 = dd.kd_dati2 
              and kel.kd_kecamatan = dd.kd_kecamatan 
              and kel.kd_kelurahan = dd.kd_kelurahan
            inner join sppt s 
              on  dd.kd_propinsi = s.kd_propinsi 
              and dd.kd_dati2 = s.kd_dati2 
              and dd.kd_kecamatan = s.kd_kecamatan 
              and dd.kd_kelurahan = s.kd_kelurahan
              and dd.kd_blok = s.kd_blok 
              and dd.no_urut = s.no_urut 
              and dd.kd_jns_op = s.kd_jns_op 
              and dd.thn_pajak_sppt = s.thn_pajak_sppt 
            left join pembayaran_sppt p 
              on  dd.kd_propinsi = p.kd_propinsi 
              and dd.kd_dati2 = p.kd_dati2 
              and dd.kd_kecamatan = p.kd_kecamatan 
              and dd.kd_kelurahan = p.kd_kelurahan
              and dd.kd_blok = p.kd_blok 
              and dd.no_urut = p.no_urut 
              and dd.kd_jns_op = p.kd_jns_op 
              and dd.thn_pajak_sppt = p.thn_pajak_sppt 
            where s.status_pembayaran_sppt<>'1' and p.jml_sppt_yg_dibayar=0
            ) as data
            -- where kd_kecamatan='030' and kd_kelurahan='005'
            {$sWhere}
            order by id desc";
		
        // if ($sWhere) $sql_query_r .= " $sWhere";
        // if ($search) $sql_query_r .= " $search";
        // $sql_query_r .= $groupBy;
        // if ($sOrder) $sql_query_r .= " $sOrder";
        // if ($sLimit) $sql_query_r .= " $sLimit";

		
        $qry = $this->db->query($sql_query_r);
		
        $output = array(
            "sEcho" => $sEcho,
            "iDisplayLength" => 0, //$iDisplayLength,
            "iTotalRecords" => 0, //$iTotal,
            "iTotalDisplayRecords" => 0, //$iFiltered,
            // "SQL Query" => $sql_query_r,
            "aaData" => array()
        );
        
        foreach ($qry->result() as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) { 
                // if ($i == 3)
                    // $row[] = (strtotime($aRow->tgl_bayar)) ? (string) date('d-m-Y', strtotime($aRow->tgl_bayar)) : '';
				if ($i == 4 || $i == 5 || $i == 6 || $i == 8)
                    $row[] = number_format($aRow->$aColumns[$i], 0, ',', '.');
                else 
                    $row[] = $aRow->$aColumns[$i];
            }
            $output['aaData'][] = $row;
        }
        
        echo json_encode($output);
    }
	
	function get_kelurahan() {
        $this->load->model('kelModel', 'kel');
		$kec_kd = $this->uri->segment(4);
		$kelurahan = $this->kel->getRecord($kec_kd, '000');
		
		$ret = '';
		if (get_user_kel_kd() == '000') $ret = "<option value=\"000\">Semua</option>\n";
		foreach ($kelurahan as $kel) 
		{
			$selected='';
			// if ($kel->kd_kelurahan==$kel_kd) $selected=" selected";
			$ret .= "<option value=\"".$kel->kd_kelurahan."\" $selected>".$kel->nm_kelurahan."</option>\n";
		}
		
		echo $ret;
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
		$thn  = $this->input->get('thn');
		$kec  = $this->input->get('kec');
		$kel  = $this->input->get('kel');
		
        $kondisi = " AND thn_pajak_sppt = '{$thn}' 
             AND kd_propinsi = '" . KD_PROPINSI . "' 
             AND kd_dati2 = '" . KD_DATI2 . "'";
		
        $this->load->model('kecModel', 'kec');
        $this->load->model('kelModel', 'kel');
        if ($kec != "000") {
            $kondisi .= " AND kd_kecamatan='{$kec}'";
        
            if ($kel != "000") {
                $kondisi .= " AND kd_kelurahan='{$kel}'";
                $kel_data = (array) $this->kel->getRecord($kec, $kel);
                $kel = $kel_data[0];
                $kel = $kel->nm_kelurahan;
                
            } else
                $kel = "SEMUA KELURAHAN";
                
            $kec_data = (array) $this->kec->getRecord($kec);
            $kec = $kec_data[0];
            $kec = $kec->nm_kecamatan;
        } else {
            $kec = "SEMUA KECAMATAN";
            $kel = "SEMUA KELURAHAN";
        }
        
		$jasper = $this->load->library('Jasper');
		$params = array(
			"daerah" => LICENSE_TO,
			"dinas" => LICENSE_TO_SUB,
			"logo" => base_url("assets/img/logorpt__.jpg"),
            
			"kondisi" => $kondisi, 
			"thn" => $thn, 
			"kec" => $kec, 
			"kel" => $kel, 
		);
		echo $jasper->cetak($rptx, $params, $type, false);
	}
    
	function csv_encode($aaData, $aHeaders = NULL) {
		// output headers
		if ($aHeaders) echo implode('|', $aHeaders ) . "\r\n";

		foreach ($aaData as $aRow) {
			echo implode('|', $aRow) . "\r\n";
		}
	}
}
