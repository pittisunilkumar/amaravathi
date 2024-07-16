<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
ob_start();
class Accounttranscationreport extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->model('addaccount_model');
        $this->load->model('session_model');
        $this->load->model('financialsession_model');

    }


    public function index()
    {
        // if (!$this->rbac->hasPrivilege('generate_certificate', 'can_view')) {
        //     access_denied();
        // }
        // $this->session->set_userdata('top_menu', 'Certificate');
        // $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

        // $certificateList         = $this->feediscount_model->get();
        // $data['certificateList'] = $certificateList;

        // $progresslist            = $this->customlib->getProgress();
        // $data['progresslist']    = $progresslist;

        $class                   = $this->addaccount_model->getaddedaccounts();
        $data['classlist']       = $class;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/accounttranscationreport/accountreport', $data);
        $this->load->view('layout/footer', $data);
        
    }


    public function search()
    {
        // $this->session->set_userdata('top_menu', 'Certificate');
        // $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

        $class                   = $this->addaccount_model->getaddedaccounts();
        $data['classlist']       = $class;

        // $certificateList         = $this->feediscount_model->get();
        // $progresslist            = $this->customlib->getProgress();
        // $data['progresslist']    = $progresslist;
        // $data['certificateList'] = $certificateList;
        
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/accounttranscationreport/accountreport', $data);
            $this->load->view('layout/footer', $data);
        } else {
            
            // $section     = $this->input->post('section_id');
            // $disstatus   = $this->input->post('progress_id');
            // $search      = $this->input->post('search');
            // $certificate = $this->input->post('certificate_id');

            // $this->form_validation->set_rules('accountname_id', $this->lang->line('accountname'), 'trim|required|xss_clean');
            // $this->form_validation->set_rules('certificate_id', $this->lang->line('certificate'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {

            } else {

                $accountnameid       = $this->input->post('accountname_id');
                $startdate  = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_from')));
                $enddate    = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_to')));

                // $data['searchby']          = "filter";
                // $data['class_id']          = $this->input->post('class_id');
                // $data['section_id']        = $this->input->post('section_id');
                // $certificate               = $this->input->post('certificate_id');
                // $certificateResult         = $this->feediscount_model->get($certificate);
                // $data['certificateResult'] = $certificateResult;
                $resultlist                = $this->addaccount_model->gettranscationsreport($startdate,$enddate);
                $data['resultlist']        = $resultlist;
                // $data['discountstat']      = $disstatus;
                // $title                     = $this->classsection_model->getDetailbyClassSection($data['class_id'], $data['section_id']);
                // $data['title']             = $this->lang->line('std_dtl_for') . ' ' . $title['class'] . "(" . $title['section'] . ")";
            }
            
            // $data['sch_setting'] = $this->sch_setting_detail;
            $this->load->view('layout/header', $data);
            $this->load->view('admin/accounttranscationreport/accountreport', $data);
            $this->load->view('layout/footer', $data);
        }
    }


    public function delete($id)
    {
        $this->addaccount_model->deletetrans($id);
        $this->addaccount_model->transcationremove($id);
        redirect('admin/accounttranscationreport/search');
    }


    public function addfinaceyear($id=null){

        
        // $this->session->set_userdata('top_menu', 'System Settings');
        // $this->session->set_userdata('sub_menu', 'sessions/index');
        $data['title']       = 'Session List';
        $session_result      = $this->financialsession_model->getAllSession();
        $data['sessionlist'] = $session_result;

        $this->form_validation->set_rules('date_from', $this->lang->line('start_date'), 'required');
        $this->form_validation->set_rules('date_to', $this->lang->line('end_date'), 'required');
        
        if ($this->form_validation->run() == false) {

        } else {
            if($id != null){
                $dataup = array(
                    'year_id'          => $id,
                    'start_date'        => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_from'))),
                    'end_date'        => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_to'))),
                );
            }else{
                $dataup = array(
                    'start_date'        => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_from'))),
                    'end_date'        => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_to'))),
                );
            }

            $insert     = $this->financialsession_model->add($dataup);

            redirect('admin/accounttranscationreport/addfinaceyear');
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/financialsession/sessionList',$data);
        $this->load->view('layout/footer', $data);

    }

    public function financialedit($id){
        // $this->session->set_userdata('top_menu', 'System Settings');
        // $this->session->set_userdata('sub_menu', 'sessions/index');
        $data['title']       = 'Session List';
        $session    = $this->financialsession_model->get($id);
        $data['se'] = $session;
        $data['id'] = $id;
        $session_result      = $this->financialsession_model->getAllSession();
        $data['sessionlist'] = $session_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/financialsession/sessionEdit',$data);
        $this->load->view('layout/footer', $data);
    }

    public function financialdelete($id){
        $this->financialsession_model->remove($id);
        redirect('admin/accounttranscationreport/addfinaceyear');
    }

    public function updatefinancialsession($id){


        // echo json_encode(array('error' => 'Session not found'));
        // $this->session->set_userdata('top_menu', 'System Settings');
        // $this->session->set_userdata('sub_menu', 'sessions/index');
        // $data['title']       = 'Session List';
        // $session_result      = $this->financialsession_model->getAllSession();
        // $data['sessionlist'] = $session_result;

        $yearid = $this->financialsession_model->get_active_id();
        if($yearid['year_id'] != $id){
            
            $this->financialsession_model->unassignfinancialyear($yearid['year_id']);
            $this->financialsession_model->assignfinancialyear($id);
        }

        $array = array('status' => 'success', 'error' => '');
        echo json_encode($array);

    }



}

