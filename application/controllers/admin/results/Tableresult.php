<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Tableresult extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->library('media_storage');
        $this->load->model('addtableresult_model');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }


    public function index()
    {
        if (!$this->rbac->hasPrivilege('add_admisno', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generateidcard');

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;

        $sessionss                    = $this->addtableresult_model->getsession();
        $data['sessionss']=$sessionss;
       

        $progresslist            = $this->customlib->checkadminstatus();
        $data['progresslist']    = $progresslist;

        $button                  = $this->input->post('search');

        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/internaltableresult', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class   = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $search  = $this->input->post('search');

            
           
            if (isset($search)) {

                $this->form_validation->set_rules('academic_id', $this->lang->line('academic_year'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('exam_id', $this->lang->line('exam'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');

                if ($this->form_validation->run() == false) {

                } else {
                    $data['searchby']     = "filter";
                    $data['class_id']     = $this->input->post('class_id');
                    $data['section_id']   = $this->input->post('section_id');
                   
                    $resultlist           = $this->student_model->admissionnostatusgetDatatableByClassSection($class, $section,$vall);
                    $data['resultlist']   = $resultlist;
                     
                }
            }

            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/internaltableresult', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('add_admisno', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generateidcard');

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;

        $sessionss                    = $this->addtableresult_model->getsession();
        $data['sessionss']=$sessionss;
       
        $button                  = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/internaltableresult', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class   = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $examtype = $this->input->post('exam_id');
            $academic_id = $this->input->post('academic_id');
            $search  = $this->input->post('search');

            if (isset($search)) {
                $this->form_validation->set_rules('academic_id', $this->lang->line('academic_year'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('exam_id', $this->lang->line('exam'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');

                if ($this->form_validation->run() == false) {

                } else {
                    $data['searchby']     = "filter";
                    $data['class_id']     = $this->input->post('class_id');
                    $data['section_id']   = $this->input->post('section_id');
                    $data['exam_id']       =$this->input->post('exam_id');
                 
                    $resultlist           = $this->student_model->admissionnostatusgetDatatableByClassSection($class, $section,$vall);
                    $data['resultlist']   = $resultlist;
                     
                }
            }

            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/internaltableresult', $data);
            $this->load->view('layout/footer', $data);
        }
    }


    public function examtype(){
        $session_id = $this->input->get('session_id');
        $data     = $this->addtableresult_model->getexamstype($session_id);
        echo json_encode($data);
    }





}
