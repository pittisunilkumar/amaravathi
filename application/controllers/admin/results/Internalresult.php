<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Internalresult extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->library('media_storage');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->model('examtype_model');
        $this->load->model('publicresultsubjectgroup_model');
        $this->load->model('addresult_model');
    }


    public function index()
    {
        if (!$this->rbac->hasPrivilege('internal_results', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generateidcard');


        $sessionss               = $this->examtype_model->sessions();
        $data['sessionss']       = $sessionss;

        $category                   = $this->examtype_model->get();
        $data['categorylist']       = $category;
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/results/internalresult', $data);
        $this->load->view('layout/footer', $data);
    }


    
    public function search()
    {
        if (!$this->rbac->hasPrivilege('internal_results', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generateidcard');


        $category                   = $this->examtype_model->get();
        $data['categorylist']       = $category;

        $sessionss               = $this->examtype_model->sessions();
        $data['sessionss']       = $sessionss;


        $this->form_validation->set_rules('admission_no', $this->lang->line('admi_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('academic_id', $this->lang->line('academic_year'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_id', $this->lang->line('exam'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {

        } else {

            $admino=$this->input->post('admission_no');
            $academicid=$this->input->post('academic_id');
            $resulttype=$this->input->post('exam_id');

            $data['academicid']=$academicid;

            $stidd=$this->publicresultsubjectgroup_model->getstudentid($admino);

            $data['resultname']=$this->publicresultsubjectgroup_model->getresultname($resulttype);

            $data['studentdata']=$this->publicresultsubjectgroup_model->gtstudentdata($stidd);
            
            $data['resultstatus']=$this->publicresultsubjectgroup_model->getresultstatus($stidd,$resulttype,$academicid);

            $resultdata = $this->publicresultsubjectgroup_model->getstudentresults($admino,$resulttype,$academicid);

            $data['resultdata']=$resultdata;

            $data['admissionno']=$admino;

            // $data['searchby']     = "filter";
            // $data['class_id']     = $this->input->post('class_id');
            // $data['section_id']   = $this->input->post('section_id');

            // $resultlist           = $this->student_model->hallticketnostatusgetDatatableByClassSection($class, $section,$vall);
            // $data['resultlist']   = $resultlist;
                
        }
            

        $this->load->view('layout/header', $data);
        $this->load->view('admin/results/internalresult', $data);
        $this->load->view('layout/footer', $data);
        
    }



}


?>