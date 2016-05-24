<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class lapbatal extends CI_Controller
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

        $module = 'POSL';
        $this->load->library('module_auth', array(
            'module' => $module
        ));

        $this->load->model(array(
            'apps_model'
        ));
        $this->load->model(array(
            'pbb/refkelurahan_model',
            'pbb/tp_model',
            'rpt_model',
            'pos_user_model'
        ));
    }

    public function index()
    {
       if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }

        $data['apps']    = $this->apps_model->get_active_only();
        $data['faction'] = active_module_url('lapbatal/batal');
        $data['current'] = 'laporan';
        // $data['keldata'] = $r = $this->refkelurahan_model->get_array();
        $data['tpnm']    = isset($this->session->userdata['tpnm']) ? $this->session->userdata['tpnm'] : '';
        $data['users']   =  $this->pos_user_model->get_tp_user();
        //print_r($data['users']);
        $this->fvalidation();
        $this->load->view('lapbatalv', $data);
    }

    private function fvalidation()
    {
        $this->form_validation->set_error_delimiters('<span>', '</span>');
        $this->form_validation->set_rules('tgl', 'Tanggal', 'required');
        $this->form_validation->set_rules('tgl2', 'Tanggal', 'required');
    }

    public function batal()
    {
        if (!$this->module_auth->read) {
            $this->session->set_flashdata('msg_warning', $this->module_auth->msg_read);
            redirect('info');
        }
        $this->fvalidation();
        $tgl = '';
        if (isset($_POST['tgl'])) {
            if ($_POST['tgl'] != '')
                $tgl = date('Y-m-d', strtotime($_POST['tgl']));
        }
        if ($tgl == '')
            $tgl = date('Y-m-d');
        
        if (isset($_POST['tgl2'])) {
            if ($_POST['tgl2'] != '')
                $tgl2 = date('Y-m-d', strtotime($_POST['tgl2']));
        }
        if ($tgl2 == '')
            $tgl2 = date('Y-m-d');

        
        
        $data['user_id'] = $_POST['user'];
        $data['tgl']     = date('d-m-Y', strtotime($tgl));
        $data['tgl2']    = date('d-m-Y', strtotime($tgl2));
        
        $r            = $this->rpt_model->get_lap_pembatalan($tgl,$tgl2);
        $data['rows'] = $r;
        $this->load->view('lapbatalrpt', $data);
    }

    public function csv_download() {
        $tgl    = date('Y-m-d', strtotime($_POST['tgl']));
        $tgl2   = date('Y-m-d', strtotime($_POST['tgl2']));

        header("Content-type: text/plain");
        header("Cache-Control: no-store, no-cache");
        header('Content-Disposition: attachment; filename="Laporan Pembatalan '.$tgl.' s.d.'.$tgl2.'.csv"');

        if($rows = $this->rpt_model->get_lap_pembatalan2($tgl,$tgl2)){
            $title = array('TANGGAL','NOP','NILAI');
            $this->csv_encode( $rows, $title );
        } else {
            echo "Tidak ada data";
        }
        exit;
    }

    function csv_encode($aaData, $aHeaders = NULL) {
        // output headers
        if ($aHeaders) echo implode('|', $aHeaders ) . "\r\n";

        foreach ($aaData as $aRow) {
            echo implode('|', $aRow) . "\r\n";
        }
    }
}
