<?php
 
/*
 * ***************************************************************
 * Script : 
 * Version : 
 * Date :
 * Author : Pudyasto Adi W.
 * Email : mr.pudyasto@gmail.com
 * Description : 
 * ***************************************************************
 */
 
/**
 * Description of Test
 *
 * @author adi
 */
class Pbb extends MX_Controller{
    public function __construct() {
        parent::__construct();
        //'api_key'         => 'Setec_Astronomy'
        //'api_name'        => 'X-API-KEY'
        //'ssl_verify_peer' => TRUE,
        //'ssl_cainfo'      => '/certs/cert.pem'
        $this->load->library('rest_client');
        $this->rest_client->initialize(array('server'=>PBB_RPC_SERVER,
                                            'http_user'=>PBB_RPC_USER,
                                            'http_pass'=>PBB_RPC_PASS,
                                            'http_auth'=>PBB_RPC_AUTH
                                            ));
        
        $this->akhir = $this->input->get_post('akhir');

        if (!$this->akhir) $dt_today = time();
        else $dt_today = strtotime ($this->akhir);//,'Ymd');
        
        $this->akhir  = ($this->akhir?$this->akhir:date('Ymd',$dt_today));
        $this->awal  = ($this->input->get_post('awal')?$this->input->get_post('awal'):date('Y',$dt_today).'0101');
        
        $this->month = date('Ym',$dt_today).'01';
        $this->dow   = date('w',$dt_today);
        $this->week  = date('Ymd', strtotime("-$this->dow days"));
        $this->group = ($this->input->get_post('group')?$this->input->get_post('group'):'all');
        $this->buku  = ($this->input->get_post('buku')?$this->input->get_post('buku'):0);
        $this->kode  = ($this->input->get_post('kode')?$this->input->get_post('kode'):'');
        $this->aku   = ($this->input->get_post('aku')?$this->input->get_post('aku'):0);
      }
    
    private function get_dashboard( $awal, $akhir, $method='realisasi', $group='', $kode='', $buku=0, $aku=0){
      if ($group=='') $group=$this->group;
      if ($kode=='') $kode=$this->kode;
      
      $amt = $this->rest_client->get($method,
                                      array('group'=>$group,
                                            'awal'=>$awal,
                                            'akhir'=>$akhir,
                                            'kode'=>$kode,
                                            'buku'=>$buku,
                                            'aku'=>$aku
                                            ));
      // print "$awal, $akhir, $group, $kode";
      // print_r($amt);
      return $amt;
    }    
    
    public function index()
    {
        $val = $this->get_dashboard($this->awal,$this->akhir)->data;
        $max_val=(double)$val[0]->pokok;
        $data['devider']=1;
        $data['num_ext'] ='';
        if ($max_val>1000000000){
            $data['devider']=1000000;
            $data['num_ext'] ='Dalam Juta';
        } 
        $data['tahun']  = substr($this->awal,0,4);
        $data['awal']  = $this->awal;
        $data['akhir'] = $this->akhir;
        $data['kode']  = $this->kode;
        $data['buku']  = $this->buku;
        $data['group']  = $this->group;
        $data['aku']  = $this->aku;
        $this->load->view('pbb', $data);
    }
    private function get_realisasi($awal, $akhir){
      $ret = $this->get_dashboard($awal,$akhir);
      if ($ret->status)
            $val=$ret->data;
        else {
          $val[0]=(object)array('pokok'=>0,
                         'jumlah'=>0);
        }
       return $val;
    }
    
    public function realisasi(){
        $data=array();
        //print "$this->awal, $this->akhir";
        $val = $this->get_realisasi($this->akhir,$this->akhir);
        $data['sum_today'] = number_format((double)$val[0]->pokok, 0, ',', '.');
        $data['cnt_today'] = number_format((double)$val[0]->jumlah, 0, ',', '.');
        
        $val = $this->get_realisasi($this->week,$this->akhir);
        $data['sum_week']= number_format((double)$val[0]->pokok, 0, ',', '.');
        $data['cnt_week']= number_format((double)$val[0]->jumlah, 0, ',', '.');
        
        
        $val = $this->get_realisasi($this->month,$this->akhir);
        $data['sum_month']= number_format((double)$val[0]->pokok, 0, ',', '.');
        $data['cnt_month']= number_format((double)$val[0]->jumlah, 0, ',', '.');
        
        $val = $this->get_realisasi($this->awal,$this->akhir);
        $data['sum_year']= number_format((double)$val[0]->pokok, 0, ',', '.');
        $data['cnt_year']= number_format((double)$val[0]->jumlah, 0, ',', '.');
                            
        $data['max_val']=(double)$val[0]->pokok;
        //print_r($data);
        echo json_encode($data);
    }
    public function rbook()
    {
      $pieBook = $this->get_dashboard( $this->awal, $this->akhir, 'realisasi', $this->group, $this->kode, 1);
      $pieBook = $pieBook->data;
      echo json_encode($pieBook);
    }

    public function rwil()
    {
      $data = $this->get_dashboard( $this->awal, $this->akhir, 'realisasi', $this->group, $this->kode, 0)->data;
      echo json_encode($data);
    }
    
    public function rmonth()
    {
      $data = $this->get_dashboard( $this->awal, $this->akhir, 'monthly', $this->group, $this->kode, 0)->data;
      echo json_encode($data);
    }
    public function grid()
    {
      $awal  = $this->input->get('awal',FALSE);
      $akhir = $this->input->get('akhir',FALSE);
      $group = $this->input->get('group','kec');
      $kode = $this->input->get('kode','');
      if(!$awal||!$akhir||$awal>$akhir)
        return;
      $amts =  $this->get_dashboard($awal,$akhir,'realisasi',$group,$kode)->data;
      $output = array();
      $total = array('999','TOTAL',0,0,0,0);
      foreach($amts as $k => $values) {
          $arr_values = array_values((array)$values);
          $len_val = count($arr_values);
          if($len_val>6){
              for ($i=$len_val;$i>6;$i--){
                  array_shift($arr_values);
              }
          }
                        
          for($i=2; $i < 6 ;$i++){
              $total[$i] += $arr_values[$i];
              $arr_values[$i] = number_format($arr_values[$i],0,".",".");
          }
          $output[] = (array)$values;
      }
      for($i=2; $i <= 5 ;$i++){
         $total[$i] = number_format($total[$i],0,".",".");
      }    
      //$output[] = $total;
      echo(json_encode(array('data'=>$output)));
    }
}