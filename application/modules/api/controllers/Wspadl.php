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
class Wspadl extends REST_Controller {
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
    }
    public function wp_get(){
        $query = Null;
        
        $group = $this->get('group')?$this->get('group') : 0;
        $group = (int)$group;
        
        if ($group==0){
                $sql = "SELECT kc.kecamatankd kd_kecamatan, kc.kecamatannm as nm_kecamatan, 
                     kl.kelurahankd kd_kelurahan, kl.kelurahannm as nm_kelurahan, count(*) jml
                FROM pad.pad_customer cu
                     INNER JOIN pad.tblkecamatan kc on cu.kecamatan_id=kc.id
                     INNER JOIN pad.tblkelurahan kl on cu.kelurahan_id=kl.id
                WHERE cu.enabled=1 
                GROUP BY 1,2,3,4
                ORDER BY 1,2,3,4;
               ";  
        }
        elseif ($group==1){
                $sql = "SELECT kc.kecamatankd kd_kecamatan, kc.kecamatannm as nm_kecamatan, 
                     count(*) jml
                FROM pad.pad_customer cu
                     INNER JOIN pad.tblkecamatan kc on cu.kecamatan_id=kc.id
                     INNER JOIN pad.tblkelurahan kl on cu.kelurahan_id=kl.id
                WHERE cu.enabled=1 
                GROUP BY 1,2
                ORDER BY 1,2;
               ";          
        }
        $query = $this->db->query($sql)->result_array();
        
        
        if($query) {
            //$wil = array('wilayah' => LICENSE_TO);
            $ret = $query; // array_merge($query, $wil);
            $this->response($ret, 200); // 200 being the HTTP response code
        } else {
           $this->response([
                    'status' => FALSE,
                    'message' => 'Data Not Found'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }
    
    public function op_get(){
        $query = Null;
        
        $group = $this->get('group')?$this->get('group') : 0;
        $group = (int)$group;
        
        if ($group==0){
            $sql = "SELECT cu.usaha_id, us.usahanm nm_usaha, kc.kecamatankd as kd_kecamatan, kc.kecamatannm as nm_kecamatan, 
                             kl.kelurahankd as kd_kelurahan, kl.kelurahannm as nm_kelurahan, count(*) jml
                      FROM pad.pad_customer_usaha cu
                            INNER JOIN pad.tblkecamatan kc on cu.kecamatan_id=kc.id
                            INNER JOIN pad.tblkelurahan kl on cu.kelurahan_id=kl.id
                            INNER JOIN pad.pad_usaha us on cu.usaha_id = us.id
                      WHERE cu.enabled=1 
                      GROUP BY 1,2,3,4,5,6";  
        }
        elseif ($group==1){
              $sql = "SELECT cu.usaha_id, us.usahanm nm_usaha, kc.kecamatankd as kd_kecamatan, kc.kecamatannm as nm_kecamatan, 
                             count(*) jumlah
                      FROM pad.pad_customer_usaha cu
                            INNER JOIN pad.tblkecamatan kc on cu.kecamatan_id=kc.id
                            INNER JOIN pad.pad_usaha us on cu.usaha_id = us.id
                      WHERE cu.enabled=1 
                      GROUP BY 1,2,3,4";  
        }

        $query = $this->db->query($sql)->result_array();
        
        if($query) {
            $ret = $query; 
            $this->response($ret, 200); // 200 being the HTTP response code
        } else {
           $this->response([
                    'status' => FALSE,
                    'message' => 'Data Not Found'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function ketetapan_get(){
        if(!$this->get('awal')||!$this->get('akhir'))
           $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid Parameter'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
                
        $awal = preg_replace("/[^0-9]/","",$this->get('awal'));
        $akhir = preg_replace("/[^0-9]/","",$this->get('akhir'));
        $query = Null;
        $sql = " SELECT cu.usaha_id kode, u.usahanm uraian, sum(inv.pajak_terhutang-denda-bunga-kenaikan-lain2) pokok, 
                        sum(inv.denda) as denda, sum(inv.bunga) as bunga, sum(inv.pajak_terhutang) as total 
                  FROM pad.pad_spt inv 
                       INNER JOIN pad.pad_customer_usaha cu
                              on inv.customer_usaha_id = cu.id
                       INNER JOIN pad.pad_usaha u
                              on cu.usaha_id=u.id
                       
                  WHERE TO_CHAR(inv.terimatgl,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                  GROUP BY 1, 2 ORDER BY 1,2 ";
                  
        $group = $this->get('group');
        if ($group) {
            $group = (int)$group;
            if ($group==1){
                $sql = "SELECT kode, uraian, kd_kecamatan, nm_kecamatan, sum(pokok) pokok, sum(denda) denda, sum(bunga) bunga,
                               sum(total) total 
                        FROM( SELECT 1 source, inv.usaha_id kode, inv.jenis_usaha uraian, kec.kode as kd_kecamatan, 
                                     kec.nama as nm_kecamatan, sum(inv.pokok) pokok, sum(inv.denda) as denda, 
                                     sum(inv.bunga) as bunga, sum(inv.total) as total
                              FROM pad.pad_spt inv
                                   INNER JOIN pad.pad_spt spt ON inv.source_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.tblkecamatan kec ON cu.kecamatan_id = kec.id
                              WHERE inv.source_nama='pad_spt' AND TO_CHAR(inv.created,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5
                              UNION 
                              SELECT 2 source, inv.usaha_id kode, inv.jenis_usaha uraian, kec.kode as kd_kecamatan, kec.nama as nm_kecamatan,
                                      sum(inv.pokok) pokok, sum(inv.denda) as denda, sum(inv.bunga) as bunga, sum(inv.total) as total
                              FROM pad.pad_spt inv
                                   INNER JOIN pad.pad_stpd stp ON inv.source_id=stp.id
                                   INNER JOIN pad.pad_spt spt ON stp.spt_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.tblkecamatan kec ON cu.kecamatan_id = kec.id
                              WHERE inv.source_nama='pad_stpd' AND TO_CHAR(inv.created,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5) AS drv
                        GROUP BY 1,2,3,4
                        ORDER BY 1,2,3,4 ";  
            }  
            
            elseif ($group==2){
                $sql = "SELECT kode, uraian, kd_kecamatan, nm_kecamatan, kd_kelurahan, nm_kelurahan,
                               sum(pokok) pokok, sum(denda) denda, sum(bunga) bunga, sum(total) total 
                        FROM( SELECT 1 source, inv.usaha_id kode, inv.jenis_usaha uraian, kec.kode as kd_kecamatan, 
                                     kec.nama as nm_kecamatan, kel.kode as kd_kelurahan, 
                                     kel.nama as nm_kelurahan, sum(inv.pokok) pokok, sum(inv.denda) as denda, 
                                     sum(inv.bunga) as bunga, sum(inv.total) as total
                              FROM pad.pad_spt inv
                                   INNER JOIN pad.pad_spt spt ON inv.source_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.tblkecamatan kec ON cu.kecamatan_id = kec.id
                                   INNER JOIN pad.tblkelurahan kel ON cu.kelurahan_id = kel.id
                              WHERE inv.source_nama='pad_spt' AND TO_CHAR(inv.created,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5,6,7
                              UNION 
                              SELECT 2 source, inv.usaha_id kode, inv.jenis_usaha uraian, kec.kode as kd_kecamatan, 
                                       kec.nama as nm_kecamatan, kel.kode as kd_kelurahan, kel.nama as nm_kelurahan, 
                                      sum(inv.pokok) pokok, sum(inv.denda) as denda, sum(inv.bunga) as bunga, sum(inv.total) as total
                              FROM pad.pad_spt inv
                                   INNER JOIN pad.pad_stpd stp ON inv.source_id=stp.id
                                   INNER JOIN pad.pad_spt spt ON stp.spt_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.tblkecamatan kec ON cu.kecamatan_id = kec.id
                                   INNER JOIN pad.tblkelurahan kel ON cu.kelurahan_id = kel.id
                              WHERE inv.source_nama='pad_stpd' AND TO_CHAR(inv.created,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5,6,7) AS drv
                        GROUP BY 1,2,3,4,5,6
                        ORDER BY 1,2,3,4,5,6 ";                
            } 
        }
        $query = $this->db->query($sql)->result_array();
        
        
        if($query) {
          $this->response([
                    'status' => TRUE,
                    'data' => $ret
                    ], 200); // 200 being the HTTP response code
        } else {
           $this->response([
                    'status' => FALSE,
                    'message' => 'Data Not Found'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }
    
    public function realisasi_get(){
        if(!$this->get('awal')||!$this->get('akhir'))
           $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid Parameter'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
                
        $awal = preg_replace("/[^0-9]/","",$this->get('awal'));
        $akhir = preg_replace("/[^0-9]/","",$this->get('akhir'));
        $query = Null;
        $sql = " SELECT cu.usaha_id kode, u.usahanm uraian, sum(a.jml_bayar-a.bunga) pokok, 
                    sum(a.bunga) as bunga, sum(a.jml_bayar) as total, count(*) as jumlah 
                 FROM pad.pad_sspd a 
                    INNER JOIN pad.pad_spt b on a.spt_id=b.id 
                    INNER JOIN pad.pad_customer_usaha cu on b.customer_usaha_id = cu.id 
                    INNER JOIN pad.pad_usaha u on cu.usaha_id=u.id 
                 WHERE TO_CHAR(a.sspdtgl,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                 GROUP BY 1,2
                 ORDER BY 1,2 
                ";  
        $group = $this->get('group');
        if ($group) {
            $group = (int)$group;
            if ($group==1){
                $sql = "SELECT kode, uraian, kd_kecamatan, nm_kecamatan, 
                               sum(pokok) pokok, sum(denda) denda, sum(bunga) bunga, sum(total) total, sum(jumlah) as jumlah  
                        FROM( SELECT 1 source, inv.usaha_id kode, inv.jenis_usaha uraian, 
                                     kec.kode as kd_kecamatan, kec.nama as nm_kecamatan,
                                     sum(a.jml_bayar-a.denda-a.bunga) pokok, sum(a.denda) as denda, sum(a.bunga) as bunga, 
                                     sum(a.jml_bayar) as total, count(*) as jumlah
                              FROM pad.pad_sspd a 
                                   INNER JOIN pad.pad_spt inv
                                              on a.invoice_id=inv.id
                                   INNER JOIN pad.pad_spt spt ON inv.source_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.tblkecamatan kec ON cu.kecamatan_id = kec.id
                              WHERE inv.source_nama='pad_spt' AND TO_CHAR(a.sspdtgl,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5
                              UNION 
                              SELECT 1 source, inv.usaha_id kode, inv.jenis_usaha uraian,
                                     kec.kode as kd_kecamatan, kec.nama as nm_kecamatan,                              
                                     sum(a.jml_bayar-a.denda-a.bunga) pokok, sum(a.denda) as denda, sum(a.bunga) as bunga, 
                                     sum(a.jml_bayar) as total, count(*) as jumlah
                              FROM pad.pad_sspd a 
                                   INNER JOIN pad.pad_spt inv
                                              on a.invoice_id=inv.id
                                   INNER JOIN pad.pad_stpd stp ON inv.source_id=stp.id
                                   INNER JOIN pad.pad_spt spt ON stp.spt_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.tblkecamatan kec ON cu.kecamatan_id = kec.id
                              WHERE inv.source_nama='pad_stpd' AND TO_CHAR(a.sspdtgl,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5) AS drv
                        GROUP BY 1,2,3,4
                        ORDER BY 1,2,3,4 ";  
            }  
            elseif ($group==2){
                $sql = "SELECT kode, uraian, kd_kecamatan, nm_kecamatan, kd_kelurahan, nm_kelurahan, 
                               sum(pokok) pokok, sum(denda) denda, sum(bunga) bunga, sum(total) total, sum(jumlah) as jumlah 
                        FROM( SELECT 1 source, inv.usaha_id kode, inv.jenis_usaha uraian, 
                                     kec.kode as kd_kecamatan, kec.nama as nm_kecamatan,
                                     kel.kode as kd_kelurahan, kel.nama as nm_kelurahan, 
                                     sum(a.jml_bayar-a.denda-a.bunga) pokok, sum(a.denda) as denda, sum(a.bunga) as bunga, 
                                     sum(a.jml_bayar) as total, count(*) as jumlah
                              FROM pad.pad_sspd a 
                                   INNER JOIN pad.pad_spt inv
                                              on a.invoice_id=inv.id
                                   INNER JOIN pad.pad_spt spt ON inv.source_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.tblkecamatan kec ON cu.kecamatan_id = kec.id
                                   INNER JOIN pad.tblkelurahan kel ON cu.kelurahan_id = kel.id
                              WHERE inv.source_nama='pad_spt' AND TO_CHAR(a.sspdtgl,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5,6,7
                              UNION 
                              SELECT 1 source, inv.usaha_id kode, inv.jenis_usaha uraian,
                                     kec.kode as kd_kecamatan, kec.nama as nm_kecamatan,                              
                                     kel.kode as kd_kelurahan, kel.nama as nm_kelurahan, 
                                     sum(a.jml_bayar-a.denda-a.bunga) pokok, sum(a.denda) as denda, sum(a.bunga) as bunga, 
                                     sum(a.jml_bayar) as total, count(*) as jumlah
                              FROM pad.pad_sspd a 
                                   INNER JOIN pad.pad_spt inv
                                              on a.invoice_id=inv.id
                                   INNER JOIN pad.pad_stpd stp ON inv.source_id=stp.id
                                   INNER JOIN pad.pad_spt spt ON stp.spt_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.tblkecamatan kec ON cu.kecamatan_id = kec.id
                                   INNER JOIN pad.tblkelurahan kel ON cu.kelurahan_id = kel.id
                              WHERE inv.source_nama='pad_stpd' AND TO_CHAR(a.sspdtgl,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5,6,7) AS drv
                        GROUP BY 1,2,3,4,5,6
                        ORDER BY 1,2,3,4,5,6 ";  
            }
            elseif ($group==3){
                $sql = "SELECT 
                       sum(a.jml_bayar-a.denda-a.bunga) pokok, sum(a.denda) as denda, sum(a.bunga) as bunga, 
                       sum(a.jml_bayar) as total, count(*) as jumlah
                FROM pad.pad_sspd a 
                  INNER JOIN pad.pad_spt b
                    on a.invoice_id=b.id
                WHERE TO_CHAR(a.sspdtgl,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'                 
                ";  
            }
        }
                                      
        $query = $this->db->query($sql)->result_array();
        
        if($query) {
            $ret = $query; 
            $this->response([
                    'status' => TRUE,
                    'data' => $ret
                    ], 200); // 200 being the HTTP response code
        } else {
           $this->response([
                    'status' => FALSE,
                    'message' => 'Data Not Found'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

}
