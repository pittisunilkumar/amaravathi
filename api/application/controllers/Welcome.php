<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('student_model', 'customfield_model', 'setting_model'));
    }

    public function index()
    {
    	 echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br />';
        exit();
        $this->load->view('welcome_message');
    }

}
