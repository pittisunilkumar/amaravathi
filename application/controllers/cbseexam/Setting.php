<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Setting extends MY_Addon_CBSEController {

    function __construct() {
        parent::__construct();
    }

    public function index() 
    {
        $data['version'] = $this->config->item('version');
        $this->load->view('layout/header');
        $this->load->view('cbseexam/setting',$data);
        $this->load->view('layout/footer');
    }   

    
}