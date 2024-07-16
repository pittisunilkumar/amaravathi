<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Setting extends MY_Addon_BRController {

    function __construct() {
        parent::__construct();
    }

    /*
    This function is used to load behaviour records setting
    */
    public function index() 
    {
        if (!$this->rbac->hasPrivilege('behaviour_records_setting', 'can_view')) {
            access_denied();
        }
        $data['version'] = $this->config->item('version');
        $this->session->set_userdata('top_menu', 'behaviour');
        $this->session->set_userdata('sub_menu', 'behaviour/setting');
        $data['setting'] = $this->studentbehaviour_model->getsettings();
        $this->load->view('layout/header');
        $this->load->view('behaviour/setting',$data);
        $this->load->view('layout/footer');
    } 
    
    /*
    This function is used to update behaviour records setting
    */
    public function updatesetting()
    {
        if (!$this->rbac->hasPrivilege('behaviour_records_setting', 'can_edit')) {
            access_denied();
        } 

        $data = array(
            'id'                 => $this->input->post('sch_id'),                
            'comment_option'     => json_encode($this->input->post('comment_option')), 
        );

        $this->studentbehaviour_model->updatesetting($data);
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        echo json_encode($array);
       
    }
    
}