<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
ob_start();
class Accountreport extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->model('addaccount_model');

    }

    public function index()
    {
        // if (!$this->rbac->hasPrivilege('generate_certificate', 'can_view')) {
        //     access_denied();
        // }
        // $this->session->set_userdata('top_menu', 'Certificate');
        // $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

        $certificateList         = $this->feediscount_model->get();
        $data['certificateList'] = $certificateList;
        $progresslist            = $this->customlib->getProgress();
        $data['progresslist']    = $progresslist;

        $class                   = $this->addaccount_model->getaddedaccounts();
        $data['classlist']       = $class;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/accountreport/accountreport', $data);
        $this->load->view('layout/footer', $data);
        
    }


    // public function search()
    // {
    //     // $this->session->set_userdata('top_menu', 'Certificate');
    //     // $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

    //     $class                   = $this->addaccount_model->getaddedaccounts();
    //     $data['classlist']       = $class;

    //     // $certificateList         = $this->feediscount_model->get();
    //     // $progresslist            = $this->customlib->getProgress();
    //     // $data['progresslist']    = $progresslist;
    //     // $data['certificateList'] = $certificateList;
        
    //     if ($this->input->server('REQUEST_METHOD') == "GET") {
    //         $this->load->view('layout/header', $data);
    //         $this->load->view('admin/accountreport/accountreport', $data);
    //         $this->load->view('layout/footer', $data);
    //     } else {
            
    //         // $section     = $this->input->post('section_id');
    //         // $disstatus   = $this->input->post('progress_id');
    //         // $search      = $this->input->post('search');
    //         // $certificate = $this->input->post('certificate_id');

    //         $this->form_validation->set_rules('accountname_id', $this->lang->line('accountname'), 'trim|required|xss_clean');
    //         // $this->form_validation->set_rules('certificate_id', $this->lang->line('certificate'), 'trim|required|xss_clean');
    //         $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
    //         $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');

    //         if ($this->form_validation->run() == false) {

    //         } else {

    //             $accountnameid       = $this->input->post('accountname_id');
    //             $startdate  = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_from')));
    //             $enddate    = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_to')));

    //             // $data['searchby']          = "filter";
    //             // $data['class_id']          = $this->input->post('class_id');
    //             // $data['section_id']        = $this->input->post('section_id');
    //             // $certificate               = $this->input->post('certificate_id');
    //             // $certificateResult         = $this->feediscount_model->get($certificate);
    //             // $data['certificateResult'] = $certificateResult;
    //             $resultlist                = $this->addaccount_model->gettranscations($accountnameid,$startdate,$enddate);
    //             $data['resultlist']        = $resultlist;
    //             // $data['discountstat']      = $disstatus;
    //             // $title                     = $this->classsection_model->getDetailbyClassSection($data['class_id'], $data['section_id']);
    //             // $data['title']             = $this->lang->line('std_dtl_for') . ' ' . $title['class'] . "(" . $title['section'] . ")";
    //         }
            
    //         // $data['sch_setting'] = $this->sch_setting_detail;
    //         $this->load->view('layout/header', $data);
    //         $this->load->view('admin/accountreport/accountreport', $data);
    //         $this->load->view('layout/footer', $data);
    //     }
    // }



    // public function search()
    // {
    //     // $this->session->set_userdata('top_menu', 'Certificate');
    //     // $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

    //     $class                   = $this->addaccount_model->getaddedaccounts();
    //     $data['classlist']       = $class;

    //     // $certificateList         = $this->feediscount_model->get();
    //     // $progresslist            = $this->customlib->getProgress();
    //     // $data['progresslist']    = $progresslist;
    //     // $data['certificateList'] = $certificateList;
        
    //     if ($this->input->server('REQUEST_METHOD') == "GET") {
    //         $this->load->view('layout/header', $data);
    //         $this->load->view('admin/accountreport/accountreport', $data);
    //         $this->load->view('layout/footer', $data);
    //     } else {
            
    //         // $section     = $this->input->post('section_id');
    //         // $disstatus   = $this->input->post('progress_id');
    //         // $search      = $this->input->post('search');
    //         // $certificate = $this->input->post('certificate_id');

    //         $this->form_validation->set_rules('accountname_id', $this->lang->line('accountname'), 'trim|required|xss_clean');
    //         // $this->form_validation->set_rules('certificate_id', $this->lang->line('certificate'), 'trim|required|xss_clean');
    //         $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
    //         $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');

    //         if ($this->form_validation->run() == false) {

    //         } else {

    //             $accountnameid       = $this->input->post('accountname_id');
    //             $startdate  = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_from')));
    //             $enddate    = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_to')));

    //             $previousDate = date('Y-m-d', strtotime('-1 day', $this->customlib->datetostrtotime($this->input->post('date_from'))));
                
    //             $financialyear  = $this->addaccount_model->getactivefinancialyear();
                
    //             // $opendebitamount  = $this->addaccount_model->gettransactionssum($accountnameid,"debit",$financialyear->start_date,$startdate);
    //             // $opencreditamount =$this->addaccount_model->gettransactionssum($accountnameid,"credit",$financialyear->start_date,$startdate);
                
    //             $opendebitamount  = $this->addaccount_model->gettransactionssum($accountnameid,"debit",$financialyear->start_date,$previousDate);
    //             $opencreditamount =$this->addaccount_model->gettransactionssum($accountnameid,"credit",$financialyear->start_date,$previousDate);
                

    //             $closedebitamount =$this->addaccount_model->gettransactionssum($accountnameid,"debit",$financialyear->start_date,$enddate);
    //             $closecreditamount =$this->addaccount_model->gettransactionssum($accountnameid,"credit",$financialyear->start_date,$enddate);
    //             $resultlist                = $this->addaccount_model->gettranscations($accountnameid,$startdate,$enddate);
    //             $data['resultlist']        = $resultlist;

    //             // print_r($opendebitamount);
                
    //             if($opendebitamount==null){
    //                 $opendebitamount = 0;
    //             }
    //             if($opencreditamount==null){
    //                 $opencreditamount = 0;
    //             }
    //             if($closedebitamount==null){
    //                 $closedebitamount = 0;
    //             }
    //             if($closecreditamount==null){
    //                 $closecreditamount = 0;
    //             }
    //             // $data['opendebitamount'] = $opencreditamount;
    //             $data['openaccountbalance'] = $opencreditamount - $opendebitamount;
    //             $data['closeaccountblance'] =$closecreditamount - $closedebitamount;
    //             // $data['discountstat']      = $disstatus;
    //             // $title                     = $this->classsection_model->getDetailbyClassSection($data['class_id'], $data['section_id']);
    //             // $data['title']             = $this->lang->line('std_dtl_for') . ' ' . $title['class'] . "(" . $title['section'] . ")";
    //         }
            
    //         $data['sch_setting'] = $this->sch_setting_detail;
    //         $this->load->view('layout/header', $data);
    //         $this->load->view('admin/accountreport/accountreport', $data);
    //         $this->load->view('layout/footer', $data);
    //     }
    // }






    public function search()
    {
        $class = $this->addaccount_model->getaddedaccounts();
        $data['classlist'] = $class;

        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/accountreport/accountreport', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $this->form_validation->set_rules('accountname_id', $this->lang->line('accountname'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');

            if ($this->form_validation->run() == false) {
                // Handle validation errors
            } else {
                $accountnameid = $this->input->post('accountname_id');
                $startdate = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_from')));
                $enddate = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_to')));
                
                $financialyear = $this->addaccount_model->getactivefinancialyear();


                $previousDate = date('Y-m-d', strtotime('-1 day', $this->customlib->datetostrtotime($this->input->post('date_from'))));

                $opendebitamount  = $this->addaccount_model->gettransactionssum($accountnameid,"debit",$financialyear->start_date,$previousDate);
                $opencreditamount =$this->addaccount_model->gettransactionssum($accountnameid,"credit",$financialyear->start_date,$previousDate);
                

                $closedebitamount =$this->addaccount_model->gettransactionssum($accountnameid,"debit",$financialyear->start_date,$enddate);
                $closecreditamount =$this->addaccount_model->gettransactionssum($accountnameid,"credit",$financialyear->start_date,$enddate);
                

                $data['openaccountbalance'] = ($opencreditamount ?: 0) - ($opendebitamount ?: 0);
                $data['closeaccountblance'] =($closecreditamount ?: 0) - ($closedebitamount ?: 0);
                $data['startdate'] = date('d/m/y',strtotime( $startdate));
                $data['enddate']=date('d/m/y', strtotime($enddate));


                
                $dailyData = [];
                $currentDate = $startdate;
                
                while (strtotime($currentDate) <= strtotime($enddate)) {
                    $previousDate = date('Y-m-d', strtotime('-1 day', strtotime($currentDate)));
                    
                    $openingDebit = $this->addaccount_model->gettransactionssum($accountnameid, "debit", $financialyear->start_date, $previousDate);
                    $openingCredit = $this->addaccount_model->gettransactionssum($accountnameid, "credit", $financialyear->start_date, $previousDate);
                    
                    $dailyTransactions = $this->addaccount_model->gettranscations($accountnameid, $currentDate, $currentDate);
                    
                    $closingDebit = $this->addaccount_model->gettransactionssum($accountnameid, "debit", $financialyear->start_date, $currentDate);
                    $closingCredit = $this->addaccount_model->gettransactionssum($accountnameid, "credit", $financialyear->start_date, $currentDate);
                    
                    $dailyData[$currentDate] = [
                        'date' => $currentDate,
                        'opening_balance' => ($openingCredit ?: 0) - ($openingDebit ?: 0),
                        'transactions' => $dailyTransactions,
                        'closing_balance' => ($closingCredit ?: 0) - ($closingDebit ?: 0)
                    ];
                    
                    $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
                }
                
                $data['daily_data'] = $dailyData;
            }
            
            $data['sch_setting'] = $this->sch_setting_detail;
            $this->load->view('layout/header', $data);
            $this->load->view('admin/accountreport/accountreport', $data);
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


    public function dismissapprovalsingle()
    {

        $studentid           = $this->input->post('data');
        $certificate_id      = $this->input->post('certificate_id');

        $update_result = $this->feediscount_model->updateapprovalstatus($certificate_id, $studentid, 2);

        if ($update_result) {
            $response = array('status' => 'success');
        } else {
            $response = array('status' => 'fail');
        }

        // Send the response
        echo json_encode($response);
        
    }

    // public function approvalsingle()
    // {

    //     $studentid           = $this->input->post('data');
    //     $certificate_id      = $this->input->post('certificate_id');
    //     $class               = $this->input->post('class_id');
    //     $item['student_session_id']=$studentid;
    //     $item['fees_discount_id']=$certificate_id;
    //     // $temp=$this->feediscount_model->allotdiscount($item);
    //     $this->feediscount_model->updateapprovalstatus($certificate_id,$studentid,1);
        
        
    // }



    // public function approvalsingle()
    // {
    //     $studentid = $this->input->post('data');
    //     $certificate_id = $this->input->post('certificate_id');

    //     // Update the approval status in the database using your model
    //     // Assuming you have a method like updateApprovalStatus in your model
    //     $this->feediscount_model->updateapprovalstatus($studentid, $certificate_id, 1);

    //     // Send a response to indicate success
    //     $response = array('status' => 'success');
    //     echo json_encode($response);
    // }




    // public function approvalsingle()
    // {

    //     $studentid = $this->input->post('data');
    //     $certificate_id = $this->input->post('certificate_id');

    //     // Update the approval status in the database using your model
    //     $update_result = $this->feediscount_model->updateapprovalstatus($certificate_id, $studentid, 1);

    //     if ($update_result) {
    //         $response = array('status' => 'success');
    //     } else {
    //         $response = array('status' => 'fail');
    //     }

    //     // Send the response
    //     echo json_encode($response);
    // }

    public function approvalsingle()
    {
        $studentid = $this->input->post('dataa');
        $certificate_id = $this->input->post('certificate_id');

        // Update the approval status in the database using your model
        $update_result = $this->feediscount_model->updateapprovalstatus($certificate_id, $studentid, 1);

        if ($update_result) {
            $response = array('status' => 'success');
        } else {
            $response = array('status' => 'fail');
        }

        // Send the response
        echo json_encode($response);
    }




    





    // public function addfee($id)
    // {

    //     if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
    //         access_denied();
    //     }

    //     $data['sch_setting']   = $this->sch_setting_detail;
    //     $data['title']         = 'Student Detail';
    //     $student               = $this->student_model->getByStudentSession($id);
    //     $route_pickup_point_id = $student['route_pickup_point_id'];
    //     $student_session_id    = $student['student_session_id'];
    //     $transport_fees=[];

    //     $module=$this->module_model->getPermissionByModulename('transport');
    //     if($module['is_active']){

    //     $transport_fees        = $this->studentfeemaster_model->getStudentTransportFees($student_session_id, $route_pickup_point_id);
    //     }
  


    //     $data['student']       = $student;
    //     $student_due_fee       = $this->studentfeemaster_model->getStudentFees($id);
    //     $student_discount_fee  = $this->feediscount_model->getStudentFeesDiscount($id);

    //     $data['transport_fees']         = $transport_fees;
    //     $data['student_discount_fee']   = $student_discount_fee;
    //     $data['student_due_fee']        = $student_due_fee;
    //     $category                       = $this->category_model->get();
    //     $data['categorylist']           = $category;
    //     $class_section                  = $this->student_model->getClassSection($student["class_id"]);
    //     $data["class_section"]          = $class_section;
    //     $session                        = $this->setting_model->getCurrentSession();
    //     $studentlistbysection           = $this->student_model->getStudentClassSection($student["class_id"], $session);
    //     $data["studentlistbysection"]   = $studentlistbysection;
    //     $student_processing_fee         = $this->studentfeemaster_model->getStudentProcessingFees($id);
    //     $data['student_processing_fee'] = false;

    //     foreach ($student_processing_fee as $key => $processing_value) {
    //         if (!empty($processing_value->fees)) {
    //             $data['student_processing_fee'] = true;
    //         }
    //     }

    //     $this->load->view('layout/header', $data);
    //     $this->load->view('admin/feediscount/studentaddfeediscount', $data);
    //     $this->load->view('layout/footer', $data);
    // }


}

