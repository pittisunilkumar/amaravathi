<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
ob_start();
class Additionalfeeassigns extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('feesessiongroupadding_model');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {
        // if (!$this->rbac->hasPrivilege('generate_certificate', 'can_view')) {
        //     access_denied();
        // }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

        $certificateList         = $this->feesessiongroupadding_model->getgroups();
        $data['certificateList'] = $certificateList;

        
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/additionalfeeassigns/additionalfeeassigns', $data);
        $this->load->view('layout/footer', $data);
        
    }



    public function getByClass()
    {
        $class_id = $this->input->get('class_id');
        $data     = $this->feesessiongroupadding_model->getClassBySection($class_id);
        echo json_encode($data);
    }



    public function search()
    {
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;

        $certificateList         = $this->feesessiongroupadding_model->getgroups();
        $data['certificateList'] = $certificateList;

        $button                  = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/additionalfeeassigns/additionalfeeassigns', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class       = $this->input->post('class_id');
            $section     = $this->input->post('section_id');
            $disstatus   = $this->input->post('progress_id');
            $search      = $this->input->post('search');
            $certificate = $this->input->post('certificate_id');
            if (isset($search)) {
                $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('progress_id', $this->lang->line('class'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('certificate_id', $this->lang->line('certificate'), 'trim|required|xss_clean');
                
                if ($this->form_validation->run() == false) {

                } else {

                    $data['searchby']          = "filter";
                    $data['class_id']          = $this->input->post('class_id');
                    $data['section_id']        = $this->input->post('section_id');
                    $certificate               = $this->input->post('certificate_id');
                    // $certificateResult         = $this->feediscount_model->get($certificate);
                    // $data['certificateResult'] = $certificateResult;
                    $resultlist                = $this->feesessiongroupadding_model->searchByClassSectionAnddiscountStatus($class,$certificate, $section,$disstatus);
                    $data['resultlist']        = $resultlist;
                    $data['discountstat']      = $disstatus;
                    $title                     = $this->classsection_model->getDetailbyClassSection($data['class_id'], $data['section_id']);
                    $data['title']             = $this->lang->line('std_dtl_for') . ' ' . $title['class'] . "(" . $title['section'] . ")";
                }
            }
            $data['sch_setting'] = $this->sch_setting_detail;
            $this->load->view('layout/header', $data);
            $this->load->view('admin/additionalfeeassigns/additionalfeeassigns', $data);
            $this->load->view('layout/footer', $data);
        }
    }


    public function generate($student, $class, $certificate)
    {
        $certificateResult         = $this->Generatecertificate_model->getcertificatebyid($certificate);
        $data['certificateResult'] = $certificateResult;
        $resultlist                = $this->student_model->searchByClassStudent($class, $student);
        $data['resultlist']        = $resultlist;

        $this->load->view('admin/certificate/transfercertificate', $data);
    }

    public function generatemultiple()
    {

        $studentid           = $this->input->post('data');
        $student_array       = json_decode($studentid);
        $certificate_id      = $this->input->post('certificate_id');
        $class               = $this->input->post('class_id');
        foreach ($student_array as $key => $value) {
            $item['student_session_id']=$value->student_id;
            $item['fees_discount_id']=$certificate_id;
            $temp=$this->feediscount_model->allotdiscount($item);
            $this->feediscount_model->updateapprovalstatus($certificate_id,$value->student_id,1);
        }
        
        redirect('admin/feesdiscountapproval/index');

    }


    public function dismissapprovalgeneratemultiple()
    {

        $studentid           = $this->input->post('data');
        $student_array       = json_decode($studentid);
        $certificate_id      = $this->input->post('certificate_id');
        $class               = $this->input->post('class_id');
        foreach ($student_array as $key => $value) {
            $this->feediscount_model->updateapprovalstatus($certificate_id,$value->student_id,2);
        }
        
        redirect('admin/feesdiscountapproval/index');
        
    }




    

    public function updateadditionalfee()
    {

        
        $this->form_validation->set_rules('feegroupid_feegroupid', $this->lang->line('fee_master'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount_amount', $this->lang->line('amount'), 'required|trim|xss_clean');

        
        if ($this->form_validation->run() == false) {
            $data = array(
                'feegroupid_feegroupid'                 => form_error('feegroupid_feegroupid'),
                'amount_amount' => form_error('amount_amount'),
                
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {

            $data = array(
                'id' => $this->input->post('feegroupid_feegroupid'),
                'amount'  => $this->input->post('amount_amount'),
            );

            $this->feesessiongroupadding_model->updateadditionalfee($data);


            $array = array('status' => 'success', 'error' => '');
            echo json_encode($array);
        }
    }


}



