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
class Wspbbref extends REST_Controller {
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
    
    
    public function kecamatan_get(){
      $sql = "SELECT kd_kecamatan, nm_kecamatan 
              FROM ref_kecamatan
              ORDER BY kd_kecamatan
          ";
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
    public function kelurahan_get(){
        $kd_kecamatan = $this->get('kecamatan');
        $sql = "SELECT kd_kecamatan||kd_kelurahan kd_kelurahan, nm_kelurahan 
                FROM ref_kelurahan
                WHERE kd_kecamatan='$kd_kecamatan'
                ORDER BY kd_kecamatan
            ";
      $this->query($sql);
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