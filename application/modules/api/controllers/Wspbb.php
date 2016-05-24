<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Wspbb extends REST_Controller {
    //$this->load->model('Invoice_Model');
    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        //$this->methods['user_get']['limit'] = 500; // 500 requests per hour per user/key
        //$this->methods['user_post']['limit'] = 100; // 100 requests per hour per user/key
        //$this->methods['user_delete']['limit'] = 50; // 50 requests per hour per user/key
        $group = $this->get('group');
        $this->is_pemda = ($group =='all'?True:False);
        $this->is_kecamatan = ($group =='kec'?True:False);
        $this->is_kelurahan = ($group =='kel'?True:False);
        
        $this->is_buku = ($this->get('buku')==1?True:False);
        
        if(!$this->get('awal')||!$this->get('akhir')||!$group)
           $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid Parameter'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code

        $this->awal = preg_replace("/[^0-9]/","",$this->get('awal'));
        $this->akhir = preg_replace("/[^0-9]/","",$this->get('akhir'));
        
        $this->query = Null;
        
        $this->aku = $this->get('aku'); //realisasi all 0 pokok 1 piutang 2
        
        $kode = preg_replace("/[^0-9]/","",$this->get('kode'));
        $this->sql_filter = "";
        if (isset($kode) and $kode) {
          if (strlen($kode)>2){
              $kd_kec=substr($kode,0,3);
              if($kd_kec!='000')
                $this->sql_filter = " AND a.kd_kecamatan='$kd_kec' ";
          }
          
          if (strlen($kode)==6){
              $kd_kel=substr($kode,3,3);
              if ($kd_kel!='000')
                  $this->sql_filter .= " AND a.kd_kelurahan='$kd_kel' ";
          }
        }
        $this->buku = array(
                     array('kode'=>'001','uraian'=>'Buku I' , 'min'=>1,'max'=>100000),
                     array('kode'=>'003','uraian'=>'Buku III','min'=>500001, 'max'=>2000000),
                     array('kode'=>'002','uraian'=>'Buku II' ,'min'=>100001, 'max'=>500000),
                     array('kode'=>'004','uraian'=>'Buku IV', 'min'=>2000001,'max'=>5000000),
                     array('kode'=>'005','uraian'=>'Buku V',  'min'=>5000001,'max'=>999999999999)
               );      
    }
    
    private function sql_buku_k($buku_id){ 
         $buku_id -= 1;
         $buku = $this->buku;
         $kode   = $buku[$buku_id]['kode'];
         $uraian = $buku[$buku_id]['uraian'];
         $min    = $buku[$buku_id]['min'];
         $max    = $buku[$buku_id]['max'];
         
         return "SELECT '$kode' kode, '$uraian' uraian, COALESCE(SUM(COALESCE(a.pbb_yg_harus_dibayar_sppt,0)),0)  as pokok, count(*) as jumlah
                FROM sppt a
                WHERE a.thn_pajak_sppt BETWEEN '$this->awal' AND '$this->akhir'
                      AND a.pbb_yg_harus_dibayar_sppt BETWEEN $min AND $max ";
    }

    private function sql_buku_k_kec($buku_id){ 
         $buku_id -= 1;
         $buku = $this->buku;
         $kode   = $buku[$buku_id]['kode'];
         $uraian = $buku[$buku_id]['uraian'];
         $min    = $buku[$buku_id]['min'];
         $max    = $buku[$buku_id]['max'];
         $sql = "SELECT k1.kd_kecamatan, k1.nm_kecamatan, '$kode' kode, '$uraian' uraian, COALESCE(SUM(COALESCE(a.pbb_yg_harus_dibayar_sppt,0)),0)  as pokok, count(*) as jumlah
                FROM sppt a
                    INNER JOIN ref_kecamatan k1 on
                                a.kd_propinsi=k1.kd_propinsi AND
                                a.kd_dati2=k1.kd_dati2 AND
                                a.kd_kecamatan = k1.kd_kecamatan
                WHERE a.thn_pajak_sppt BETWEEN '$this->awal' AND '$this->akhir'
                      AND a.pbb_yg_harus_dibayar_sppt BETWEEN $min AND $max 
                      $this->sql_filter
                GROUP BY 1, 2";  
        return $sql;
    }

    private function sql_buku_k_kel($buku_id){
         $buku_id -= 1;
         $buku = $this->buku;
         $kode   = $buku[$buku_id]['kode'];
         $uraian = $buku[$buku_id]['uraian'];
         $min    = $buku[$buku_id]['min'];
         $max    = $buku[$buku_id]['max'];
        
        $sql = "SELECT k1.kd_kecamatan, k1.nm_kecamatan, k2.kd_kelurahan, k2.nm_kelurahan,
                       '$kode' kode, '$uraian' uraian, COALESCE(SUM(COALESCE(a.pbb_yg_harus_dibayar_sppt,0)),0)  as pokok, count(*) as jumlah
                FROM sppt a
                    INNER JOIN ref_kecamatan k1 on
                                a.kd_propinsi=k1.kd_propinsi AND
                                a.kd_dati2=k1.kd_dati2 AND
                                a.kd_kecamatan = k1.kd_kecamatan
                      INNER JOIN ref_kelurahan k2 on
                            a.kd_propinsi=k2.kd_propinsi AND
                            a.kd_dati2=k2.kd_dati2 AND
                            a.kd_kecamatan = k2.kd_kecamatan AND
                            a.kd_kelurahan = k2.kd_kelurahan
                WHERE a.thn_pajak_sppt BETWEEN '$this->awal' AND '$this->akhir'
                      AND a.pbb_yg_harus_dibayar_sppt BETWEEN $min AND $max 
                      $this->sql_filter
                GROUP BY 1, 2, 3, 4";  
        return $sql;
    }
    
    private function sql_buku_r($buku_id){
         $buku_id -= 1;
         $buku = $this->buku;
         $kode   = $buku[$buku_id]['kode'];
         $uraian = $buku[$buku_id]['uraian'];
         $min    = $buku[$buku_id]['min'];
         $max    = $buku[$buku_id]['max'];
        return "SELECT '$kode' kode, '$uraian' uraian,
                        COALESCE(SUM(COALESCE(a.jml_sppt_yg_dibayar,0)-COALESCE(a.denda_sppt,0)),0)  as pokok, count(*) as jumlah
                FROM pembayaran_sppt a
                WHERE a.tgl_pembayaran_sppt BETWEEN TO_DATE('$this->awal','YYYYMMDD') AND TO_DATE('$this->akhir','YYYYMMDD')
                      AND (COALESCE(a.jml_sppt_yg_dibayar,0)-COALESCE(a.denda_sppt,0)) BETWEEN $min AND $max 
                      $this->sql_filter ";
    }
    
    private function sql_buku_r_kec($buku_id, $sql_filter){
         $buku_id -= 1;
         $buku = $this->buku;
         $kode   = $buku[$buku_id]['kode'];
         $uraian = $buku[$buku_id]['uraian'];
         $min    = $buku[$buku_id]['min'];
         $max    = $buku[$buku_id]['max'];
        $sql = "SELECT k1.kd_kecamatan, k1.nm_kecamatan, '$kode' kode, '$uraian' uraian, 
                        COALESCE(SUM(COALESCE(a.jml_sppt_yg_dibayar,0)-COALESCE(a.denda_sppt,0)),0)  as pokok, count(*) as jumlah
                FROM pembayaran_sppt a
                    INNER JOIN ref_kecamatan k1 on
                                a.kd_propinsi=k1.kd_propinsi AND
                                a.kd_dati2=k1.kd_dati2 AND
                                a.kd_kecamatan = k1.kd_kecamatan
                WHERE a.tgl_pembayaran_sppt BETWEEN TO_DATE('$this->awal','YYYYMMDD') AND TO_DATE('$this->akhir','YYYYMMDD')
                      AND (COALESCE(a.jml_sppt_yg_dibayar,0)-COALESCE(a.denda_sppt,0)) BETWEEN $min AND $max 
                      $this->sql_filter
                      $sql_filter
                GROUP BY 1, 2";  
        return $sql;
    }

    private function sql_buku_r_kel($buku_id, $sql_filter){
         $buku_id -= 1;
         $buku = $this->buku;
         $kode   = $buku[$buku_id]['kode'];
         $uraian = $buku[$buku_id]['uraian'];
         $min    = $buku[$buku_id]['min'];
         $max    = $buku[$buku_id]['max'];
        $sql = "SELECT k1.kd_kecamatan, k1.nm_kecamatan, k2.kd_kelurahan, k2.nm_kelurahan, '$kode' kode, '$uraian' uraian, 
                        COALESCE(SUM(COALESCE(a.jml_sppt_yg_dibayar,0)-COALESCE(a.denda_sppt,0)),0)  as pokok, count(*) as jumlah
                FROM pembayaran_sppt a
                    INNER JOIN ref_kecamatan k1 on
                                a.kd_propinsi=k1.kd_propinsi AND
                                a.kd_dati2=k1.kd_dati2 AND
                                a.kd_kecamatan = k1.kd_kecamatan
                    INNER JOIN ref_kelurahan k2 on
                            a.kd_propinsi=k2.kd_propinsi AND
                            a.kd_dati2=k2.kd_dati2 AND
                            a.kd_kecamatan = k2.kd_kecamatan AND
                            a.kd_kelurahan = k2.kd_kelurahan
                WHERE a.tgl_pembayaran_sppt BETWEEN TO_DATE('$this->awal','YYYYMMDD') AND TO_DATE('$this->akhir','YYYYMMDD')
                      AND (COALESCE(a.jml_sppt_yg_dibayar,0)-COALESCE(a.denda_sppt,0)) BETWEEN $min AND $max 
                      $this->sql_filter
                      $sql_filter
                GROUP BY 1, 2, 3, 4";  
        return $sql;
    }
    
    public function dop_get(){
    }
    
    public function ketetapan_get(){
      $sql = $query = "";
      
      if($this->is_pemda){
        if (!$this->is_buku){
          $sql = "SELECT sum(COALESCE(a.pbb_yg_harus_dibayar_sppt,0)) as total, count(*) as jumlah
                  FROM sppt a
                  WHERE a.thn_pajak_sppt BETWEEN '$this->awal' AND '$this->akhir'
                      AND a.pbb_yg_harus_dibayar_sppt>0
                ";          
        }
        else{
          $sql =  $this->sql_buku_k(1)
                  ." UNION ".
                  $this->sql_buku_k(2)
                  ." UNION ".
                  $this->sql_buku_k(3)
                  ." UNION ".
                  $this->sql_buku_k(4)
                  ." UNION ".
                  $this->sql_buku_k(5)
                  ." ORDER BY 1 ";          
        }
      }
      elseif($this->is_kecamatan){
        if(!$this->is_buku){
          $sql = "SELECT k1.kd_kecamatan, k1.nm_kecamatan,
                         sum(COALESCE(a.pbb_yg_harus_dibayar_sppt,0)) as total, count(*) as jumlah
                FROM sppt a
                    INNER JOIN ref_kecamatan k1 on
                                a.kd_propinsi=k1.kd_propinsi AND
                                a.kd_dati2=k1.kd_dati2 AND
                                a.kd_kecamatan = k1.kd_kecamatan
                WHERE a.thn_pajak_sppt BETWEEN '$this->awal' AND '$this->akhir'
                      AND a.pbb_yg_harus_dibayar_sppt>0
                      $this->sql_filter
                GROUP BY 1,2
                ORDER BY 1 ";      
        }else{
          $sql =  $this->sql_buku_k_kec(1)
                  ." UNION ".
                  $this->sql_buku_k_kec(2)
                  ." UNION ".
                  $this->sql_buku_k_kec(3)
                  ." UNION ".
                  $this->sql_buku_k_kec(4)
                  ." UNION ".
                  $this->sql_buku_k_kec(5)
                  ." ORDER BY 1,3 ";             
        }
      }
      elseif ($this->is_kelurahan){
        if(!$this->is_buku){
        $sql = "SELECT k1.kd_kecamatan, k1.nm_kecamatan, k2.kd_kelurahan, k2.nm_kelurahan,
                       sum(COALESCE(a.pbb_yg_harus_dibayar_sppt,0)) as total, count(*) as jumlah
                FROM sppt a
                      INNER JOIN ref_kecamatan k1 on
                            a.kd_propinsi=k1.kd_propinsi AND
                            a.kd_dati2=k1.kd_dati2 AND
                            a.kd_kecamatan = k1.kd_kecamatan
                      INNER JOIN ref_kelurahan k2 on
                            a.kd_propinsi=k2.kd_propinsi AND
                            a.kd_dati2=k2.kd_dati2 AND
                            a.kd_kecamatan = k2.kd_kecamatan AND
                            a.kd_kelurahan = k2.kd_kelurahan
                WHERE a.thn_pajak_sppt BETWEEN '$this->awal' AND '$this->akhir'
                      AND a.pbb_yg_harus_dibayar_sppt>0 
                      $this->sql_filter
                GROUP BY 1, 2, 3, 4
                ORDER BY 1, 3 ";
        }else{
          $sql =  $this->sql_buku_k_kel(1)
                  ." UNION ".
                  $this->sql_buku_k_kel(2)
                  ." UNION ".
                  $this->sql_buku_k_kel(3)
                  ." UNION ".
                  $this->sql_buku_k_kel(4)
                  ." UNION ".
                  $this->sql_buku_k_kel(5)
                  ." ORDER BY 1,3,5 ";    
        }
      }
      
      if($sql)
          $query = $this->db->query($sql)->result_array();
      
      if ($query){
          $ret = $query; // array_merge($query, $wil);
          $this->response(['status'=>True,'message' => 'Sukses','data'=>$ret], 200); // 200 being the HTTP response code
      }
      else{ 
        $this->response([
                'status' => FALSE,
                'message' => 'Data Not Found'
              ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
      }
    }
    
    public function realisasi_get(){
      $thn = substr($this->awal,0,4);
      if ($this->aku==1)
         $sql_filter = " AND a.thn_pajak_sppt='$thn'";
      elseif ($this->aku==2)
         $sql_filter = " AND a.thn_pajak_sppt<'$thn'";
      else $sql_filter ="";
     
      if($this->is_pemda){
        if(!$this->is_buku){
          $sql = "SELECT sum(COALESCE(a.jml_sppt_yg_dibayar,0)-COALESCE(a.denda_sppt,0)) pokok,
                       sum(a.denda_sppt) as denda, sum(a.jml_sppt_yg_dibayar) as total, count(*) as jumlah
                  FROM pembayaran_sppt a
                  WHERE a.tgl_pembayaran_sppt BETWEEN TO_DATE('$this->awal','YYYYMMDD') AND TO_DATE('$this->akhir','YYYYMMDD')
                      AND a.jml_sppt_yg_dibayar>0
                      $sql_filter ";
        }
        else{
            $sql = $this->sql_buku_r(1)
                  ." $sql_filter 
                     UNION ".
                  $this->sql_buku_r(2)
                  ." $sql_filter 
                     UNION ".
                  $this->sql_buku_r(3)
                  ." $sql_filter 
                     UNION ".
                  $this->sql_buku_r(4)
                  ." $sql_filter 
                     UNION ".
                  $this->sql_buku_r(5)
                  ." $sql_filter 
                     ORDER BY 1 ";  
        }
      }
      elseif($this->is_kecamatan){
        if(!$this->is_buku){
           $sql =  "SELECT k1.kd_kecamatan, k1.nm_kecamatan,
                       sum(COALESCE(a.jml_sppt_yg_dibayar,0)-COALESCE(a.denda_sppt,0)) pokok,
                       sum(a.denda_sppt) as denda, sum(a.jml_sppt_yg_dibayar) as total, count(*) as jumlah
                FROM pembayaran_sppt a
                      INNER JOIN ref_kecamatan k1 on
                            a.kd_propinsi=k1.kd_propinsi AND
                            a.kd_dati2=k1.kd_dati2 AND
                            a.kd_kecamatan = k1.kd_kecamatan
                WHERE a.tgl_pembayaran_sppt BETWEEN TO_DATE('$this->awal','YYYYMMDD') AND TO_DATE('$this->akhir','YYYYMMDD')
                      AND a.jml_sppt_yg_dibayar>0
                      $sql_filter 
                      $this->sql_filter
                GROUP BY 1,2
                ORDER BY 1 ";
        }
        else{
            $sql = $this->sql_buku_r_kec(1,$sql_filter)
                  ." UNION ".
                  $this->sql_buku_r_kec(2,$sql_filter)
                  ." UNION ".
                  $this->sql_buku_r_kec(3,$sql_filter)
                  ." UNION ".
                  $this->sql_buku_r_kec(4,$sql_filter)
                  ." UNION ".
                  $this->sql_buku_r_kec(5,$sql_filter)
                  ." ORDER BY 1,3 ";           
        }
      }
      elseif ($this->is_kelurahan){
        if(!$this->is_buku){
          $sql =  "SELECT k1.kd_kecamatan, k1.nm_kecamatan, k2.kd_kelurahan, k2.nm_kelurahan,
                       sum(COALESCE(a.jml_sppt_yg_dibayar,0)-COALESCE(a.denda_sppt,0)) pokok,
                       sum(a.denda_sppt) as denda, sum(a.jml_sppt_yg_dibayar) as total, count(*) as jumlah
                FROM pembayaran_sppt a
                      INNER JOIN ref_kecamatan k1 on
                            a.kd_propinsi=k1.kd_propinsi AND
                            a.kd_dati2=k1.kd_dati2 AND
                            a.kd_kecamatan = k1.kd_kecamatan
                    INNER JOIN ref_kelurahan k2 on
                            a.kd_propinsi=k2.kd_propinsi AND
                            a.kd_dati2=k2.kd_dati2 AND
                            a.kd_kecamatan = k2.kd_kecamatan AND
                            a.kd_kelurahan = k2.kd_kelurahan
                WHERE a.tgl_pembayaran_sppt BETWEEN TO_DATE('$this->awal','YYYYMMDD') AND TO_DATE('$this->akhir','YYYYMMDD')
                      AND a.jml_sppt_yg_dibayar>0
                      $this->sql_filter
                      $sql_filter
                GROUP BY 1, 2, 3, 4
                ORDER BY 1, 3 ";
        }
        else{
            $sql = $this->sql_buku_r_kel(1,$sql_filter)
                  ." UNION ".
                  $this->sql_buku_r_kel(2,$sql_filter)
                  ." UNION ".
                  $this->sql_buku_r_kel(3,$sql_filter)
                  ." UNION ".
                  $this->sql_buku_r_kel(4,$sql_filter)
                  ." UNION ".
                  $this->sql_buku_r_kel(5,$sql_filter)
                  ." ORDER BY 1,3,5 ";    
        }
      }
  
      if($sql)
          $query = $this->db->query($sql)->result_array();
      
      if ($query){
          $ret = $query; // array_merge($query, $wil);
          $this->response(['status'=>True,'message' => 'Sukses','data'=>$ret], 200); // 200 being the HTTP response code
      }
      else{ 
        $this->response([
                'status' => FALSE,
                'message' => 'Data Not Found'
              ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
      }
    }
    
    public function monthly_get(){
        if ($this->is_pemda){
            $sql = "SELECT to_char(tgl_pembayaran_sppt,'YYYYMM') ym, to_char(tgl_pembayaran_sppt,'MON') bulan, 
                       sum(COALESCE(a.jml_sppt_yg_dibayar,0)-COALESCE(a.denda_sppt,0)) pokok,
                       sum(a.denda_sppt) as denda, sum(a.jml_sppt_yg_dibayar) as total, count(*) as jumlah
                FROM pembayaran_sppt a
                WHERE a.tgl_pembayaran_sppt BETWEEN TO_DATE('$this->awal','YYYYMMDD') AND TO_DATE('$this->akhir','YYYYMMDD')
                      AND a.jml_sppt_yg_dibayar>0
                group by 1,2
                order by 1
                ";
        }
        elseif ($this->is_kecamatan){
                $sql = "SELECT k1.kd_kecamatan, k1.nm_kecamatan, to_char(tgl_pembayaran_sppt,'YYYYMM') ym, 
                               to_char(tgl_pembayaran_sppt,'MON') bulan, 
                               sum(COALESCE(a.jml_sppt_yg_dibayar,0)-COALESCE(a.denda_sppt,0)) pokok,
                               sum(a.denda_sppt) as denda, sum(a.jml_sppt_yg_dibayar) as total, count(*) as jumlah
                        FROM pembayaran_sppt a
                              INNER JOIN ref_kecamatan k1 on
                                    a.kd_propinsi=k1.kd_propinsi AND
                                    a.kd_dati2=k1.kd_dati2 AND
                                    a.kd_kecamatan = k1.kd_kecamatan
                        WHERE a.tgl_pembayaran_sppt BETWEEN TO_DATE('$this->awal','YYYYMMDD') AND TO_DATE('$this->akhir','YYYYMMDD')
                              AND a.jml_sppt_yg_dibayar>0
                              $this->sql_filter
                        GROUP BY 1,2, 3, 4
                        ORDER BY 1,3 ";
         }
         elseif ($this->is_kelurahan){
                $sql = "SELECT k1.kd_kecamatan, k1.nm_kecamatan, k2.kd_kelurahan, k2.nm_kelurahan,
                               to_char(tgl_pembayaran_sppt,'YYYYMM') ym,  to_char(tgl_pembayaran_sppt,'MON') bulan, 
                               sum(COALESCE(a.jml_sppt_yg_dibayar,0)-COALESCE(a.denda_sppt,0)) pokok,
                               sum(a.denda_sppt) as denda, sum(a.jml_sppt_yg_dibayar) as total, count(*) as jumlah
                        FROM pembayaran_sppt a
                              INNER JOIN ref_kecamatan k1 on
                                    a.kd_propinsi=k1.kd_propinsi AND
                                    a.kd_dati2=k1.kd_dati2 AND
                                    a.kd_kecamatan = k1.kd_kecamatan
                              INNER JOIN ref_kelurahan k2 on
                                    a.kd_propinsi=k2.kd_propinsi AND
                                    a.kd_dati2=k2.kd_dati2 AND
                                    a.kd_kecamatan = k2.kd_kecamatan AND
                                    a.kd_kelurahan = k2.kd_kelurahan
                        WHERE a.tgl_pembayaran_sppt BETWEEN TO_DATE('$this->awal','YYYYMMDD') AND TO_DATE('$this->akhir','YYYYMMDD')
                              AND a.jml_sppt_yg_dibayar>0
                              $this->sql_filter
                        GROUP BY 1,2,3,4,5,6
                        ORDER BY 1,3,5";
        }

      if($sql)
          $query = $this->db->query($sql)->result_array();
      
      if ($query){
          $ret = $query; // array_merge($query, $wil);
          $this->response(['status'=>True,'message' => 'Sukses','data'=>$ret], 200); // 200 being the HTTP response code
      }
      else{ 
        $this->response([
                'status' => FALSE,
                'message' => 'Data Not Found'
              ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
      }        
    }

}
?>