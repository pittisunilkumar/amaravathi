<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Publicresult extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->library('media_storage');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->model('publicexamtype_model');
    }


    public function index()
    {
        if (!$this->rbac->hasPrivilege('internal_results', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generateidcard');


        $sessionss               = $this->publicexamtype_model->sessions();
        $data['sessionss']       = $sessionss;

        $category                   = $this->publicexamtype_model->get();
        $data['categorylist']       = $category;
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/results/publicresult', $data);
        $this->load->view('layout/footer', $data);
    }

    


    
    // public function search()
    // {
    //     if (!$this->rbac->hasPrivilege('generate_id_card', 'can_view')) {
    //         access_denied();
    //     }
    //     $this->session->set_userdata('top_menu', 'Certificate');
    //     $this->session->set_userdata('sub_menu', 'admin/generateidcard');

       
    //     $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
    //     $data['sch_setting']     = $this->sch_setting_detail;
    //     $category                   = $this->publicexamtype_model->get();
    //     $data['categorylist']       = $category;
    //     $progresslist            = $this->customlib->checkadminstatus();
    //     $data['progresslist']    = $progresslist;

    //     $button                  = $this->input->post('search');
    //     if ($this->input->server('REQUEST_METHOD') == "GET") {
    //         $this->load->view('layout/header', $data);
    //         $this->load->view('admin/results/publicresult', $data);
    //         $this->load->view('layout/footer', $data);
    //     } else {
    //         $class   = $this->input->post('class_id');
    //         $section = $this->input->post('section_id');
    //         $search  = $this->input->post('search');
    //         $admistatus   = $this->input->post('progress_id');
    //         if($admistatus=="withadmissionno"){
    //             $vall=1;
    //         }
    //         if($admistatus=="noadmissionno"){
    //             $vall=0;
    //         }
    //         if (isset($search)) {
    //             $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
    //             if ($this->form_validation->run() == false) {

    //             } else {
    //                 $data['searchby']     = "filter";
    //                 $data['class_id']     = $this->input->post('class_id');
    //                 $data['section_id']   = $this->input->post('section_id');

    //                 $resultlist           = $this->student_model->hallticketnostatusgetDatatableByClassSection($class, $section,$vall);
    //                 $data['resultlist']   = $resultlist;
                     
    //             }
    //         }

    //         $this->load->view('layout/header', $data);
    //         $this->load->view('admin/results/publicresult', $data);
    //         $this->load->view('layout/footer', $data);
    //     }
    // }


    public function search()
    {
        if (!$this->rbac->hasPrivilege('internal_results', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generateidcard');


        $category                   = $this->publicexamtype_model->get();
        $data['categorylist']       = $category;

        $sessionss               = $this->publicexamtype_model->sessions();
        $data['sessionss']       = $sessionss;


        $this->form_validation->set_rules('hallticket_no', $this->lang->line('hall_no'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('academic_id', $this->lang->line('academic_year'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_id', $this->lang->line('exam'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {

        } else {

            $hallno=$this->input->post('hallticket_no');
            $academicid=$this->input->post('academic_id');
            $resulttype=$this->input->post('exam_id');

            $data['academicid']=$academicid;

            $stidd=$this->publicexamtype_model->getstudentid($hallno);

            $data['resultname']=$this->publicexamtype_model->getresultname($resulttype);

            $data['studentdata']=$this->publicexamtype_model->gtstudentdata($stidd);
            
            $data['resultstatus']=$this->publicexamtype_model->getresultstatus($stidd,$resulttype,$academicid);

            $resultdata = $this->publicexamtype_model->getstudentresults($hallno,$resulttype,$academicid);

            $data['resultdata']=$resultdata;

            $data['admissionno']=$hallno;

            // $data['searchby']     = "filter";
            // $data['class_id']     = $this->input->post('class_id');
            // $data['section_id']   = $this->input->post('section_id');

            // $resultlist           = $this->student_model->hallticketnostatusgetDatatableByClassSection($class, $section,$vall);
            // $data['resultlist']   = $resultlist;
                
        }
            

        $this->load->view('layout/header', $data);
        $this->load->view('admin/results/publicresult', $data);
        $this->load->view('layout/footer', $data);
        
    }



}


?>