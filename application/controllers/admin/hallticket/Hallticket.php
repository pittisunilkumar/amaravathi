<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Hallticket extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->library('media_storage');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }


    public function index()
    {
        if (!$this->rbac->hasPrivilege('add_hallticno', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generateidcard');

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;
        $progresslist            = $this->customlib->checkadminstatus();
        $data['progresslist']    = $progresslist;

        $button                  = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/hallticket/hallticket', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class   = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $search  = $this->input->post('search');

            $admistatus   = $this->input->post('progress_id');
            if($admistatus=="withadmissionno"){
                $vall=1;
            }
            if($admistatus=="noadmissionno"){
                $vall=0;
            }
            if (isset($search)) {
                $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('progress_id', $this->lang->line('hall_stattus'), 'trim|required|xss_clean');
                if ($this->form_validation->run() == false) {

                } else {
                    $data['searchby']     = "filter";
                    $data['class_id']     = $this->input->post('class_id');
                    $data['section_id']   = $this->input->post('section_id');
                    $resultlist           = $this->student_model->hallticketnostatusgetDatatableByClassSection($class, $section,$vall);
                    $data['resultlist']   = $resultlist;
                     
                }
            }

            $this->load->view('layout/header', $data);
            $this->load->view('admin/hallticket/hallticket', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('add_hallticno', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generateidcard');

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;

        $progresslist            = $this->customlib->checkadminstatus();
        $data['progresslist']    = $progresslist;

        $button                  = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/hallticket/hallticket', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class   = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $search  = $this->input->post('search');
            $admistatus   = $this->input->post('progress_id');
            if($admistatus=="withadmissionno"){
                $vall=1;
            }
            if($admistatus=="noadmissionno"){
                $vall=0;
            }
            if (isset($search)) {
                $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('progress_id', $this->lang->line('hall_stattus'), 'trim|required|xss_clean');
                if ($this->form_validation->run() == false) {

                } else {
                    $data['searchby']     = "filter";
                    $data['class_id']     = $this->input->post('class_id');
                    $data['section_id']   = $this->input->post('section_id');

                    $resultlist           = $this->student_model->hallticketnostatusgetDatatableByClassSection($class, $section,$vall);
                    $data['resultlist']   = $resultlist;
                     
                }
            }

            $this->load->view('layout/header', $data);
            $this->load->view('admin/hallticket/hallticket', $data);
            $this->load->view('layout/footer', $data);
        }
    }


    public function addadmino()
    {

        $studentid = $this->input->post('studentid');
        $this->form_validation->set_rules('admi_no', $this->lang->line('hall_no'), 'required|trim|xss_clean|callback_check_student_admi_no_exists');

        if ($this->form_validation->run() == false) {

            $data = array(
                'admi_no' => form_error('admi_no'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);

        } else {
            $admi_no = $this->input->post('admi_no');
            $admi_no_id=$this->student_model->getadmi_no_id($studentid);
            $data = array(
                'std_hallticket' => $admi_no,
                'hallticket_status'=>1,
                'admi_no_id'=>$admi_no_id,
            );
            
            $check = $this->student_model->gethallticket_noo($admi_no_id);

            if($check){
                $s = $this->student_model->hallticket_no_update($data,$admi_no_id);
            }else{
                $s = $this->student_model->hallticket_no_add($data);
            }


            if($s){
                $array = array('status' => 'success');
                echo json_encode($array);
            }else{
                $array = array('status' => 'fail');
                echo json_encode($array);
            }
           
        }
    }



    public function check_student_admi_no_exists($str)
    {
        $this->load->database();

        $admi_no = $this->security->xss_clean($str);
        $query = $this->db->get_where('student_hallticket', array('std_hallticket' => $admi_no));
        
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('check_student_admi_no_exists', $this->lang->line('record_already_exist'));
            return false;
        }
        
        return true;
    }


    public function getadmino()
    {
        $studentId = $this->input->post('studentid');
        $admi_no_id=$this->student_model->getadmi_no_id($studentId);
        $admissionNumber = $this->student_model->gethallticket_no($admi_no_id);

        if ($admissionNumber !== false) {
            $response = array('status' => 'success', 'admi_no' => $admissionNumber);
            echo json_encode($response);
        } else {
            $response = array('status' => 'fail', 'error_message' => 'Hall Ticket Number not found.');
            echo json_encode($response);
        }
    }







}
