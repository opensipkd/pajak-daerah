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
class Home extends CI_Controller{
    //put your code here
    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'app_model'
        ));
   }
    
    public function index() {
        $data['apps'] = $this->app_model->get_active_only();
        $this->load->view('main', $data);
        //$this->load->view('_foot');

    }

    
}