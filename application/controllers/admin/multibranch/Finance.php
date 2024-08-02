<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Finance extends MY_Addon_MBController
{
    public $sch_setting_detail; 

    public function __construct()
    {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }
    
    /*
    This function is used to load report list
    */
    public function index()
    {    
        $this->load->view('layout/header');
        $this->load->view('admin/multibranch/finance/index');
        $this->load->view('layout/footer');
    }
    
    /*
    This function is used to load daily collection
    */
    public function dailycollectionreport()
    {     
        
        $this->session->set_userdata('subsub_menu', 'finance/dailycollectionreport');
        $this->load->model("multibranch/multi_student_fee_model");
        $data          = array();
        $data['title'] = 'Daily Collection Report';
        $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {

            $date_from          = $this->input->post('date_from');
            $date_to            = $this->input->post('date_to');
            $formated_date_from = strtotime($this->customlib->dateFormatToYYYYMMDD($date_from));
            $formated_date_to   = strtotime($this->customlib->dateFormatToYYYYMMDD($date_to));
            $st_fees            = $this->multi_student_fee_model->getCurrentSessionStudentFees();
            $fees_data          = array();

            for ($i = $formated_date_from; $i <= $formated_date_to; $i += 86400) {
                $fees_data[$i]['amt']                       = 0;
                $fees_data[$i]['count']                     = 0;
                $fees_data[$i]['student_fees_deposite_ids'] = array();

            }

            if (!empty($st_fees)) {
                foreach ($st_fees as $fee_key => $fee_value) {
                    if (isJSON($fee_value->amount_detail)) {
                      

                        $fees_details = (json_decode($fee_value->amount_detail));
                        if (!empty($fees_details)) {
                            foreach ($fees_details as $fees_detail_key => $fees_detail_value) {
                                $date = strtotime($fees_detail_value->date);
                                if ($date >= $formated_date_from && $date <= $formated_date_to) {
                                    if (array_key_exists($date, $fees_data)) {
                                        $fees_data[$date]['amt'] += $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $fees_data[$date]['count'] += 1;
                                        $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id.'-'.$fee_value->db_name;
                                    } else {
                                        $fees_data[$date]['amt']                         = $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $fees_data[$date]['count']                       = 1;
                                        $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $data['fees_data'] = $fees_data;

        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/multibranch/finance/dailycollectionreport', $data);
        $this->load->view('layout/footer', $data);
    }

    /*
    This function is used to load datatabel for income report
    */
    public function incomereport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('subsub_menu', 'finance/incomereport');
        $this->load->model("multibranch/multi_income_model");
        $data               = array();
        $data['title']      = 'Daily Collection Report';
        $data['searchlist'] = $this->customlib->get_searchtype();
        $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {

            $date_from          = $this->input->post('date_from');
            $date_to            = $this->input->post('date_to');
            $formated_date_from = strtotime($this->customlib->dateFormatToYYYYMMDD($date_from));
            $formated_date_to   = strtotime($this->customlib->dateFormatToYYYYMMDD($date_to));
            $st_fees            = $this->multi_student_fee_model->getCurrentSessionStudentFees();
            $fees_data          = array();

            for ($i = $formated_date_from; $i <= $formated_date_to; $i += 86400) {
                $fees_data[$i]['amt']                       = 0;
                $fees_data[$i]['count']                     = 0;
                $fees_data[$i]['student_fees_deposite_ids'] = array();

            }

            if (!empty($st_fees)) {
                foreach ($st_fees as $fee_key => $fee_value) {
                    if (isJSON($fee_value->amount_detail)) {

                        $fees_details = (json_decode($fee_value->amount_detail));
                        if (!empty($fees_details)) {
                            foreach ($fees_details as $fees_detail_key => $fees_detail_value) {
                                $date = strtotime($fees_detail_value->date);
                                if ($date >= $formated_date_from && $date <= $formated_date_to) {
                                    if (array_key_exists($date, $fees_data)) {
                                        $fees_data[$date]['amt'] += $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $fees_data[$date]['count'] += 1;
                                        $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                                    } else {
                                        $fees_data[$date]['amt']                         = $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $fees_data[$date]['count']                       = 1;
                                        $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $data['fees_data'] = $fees_data;

        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/multibranch/finance/incomereport', $data);
        $this->load->view('layout/footer', $data);
    }

    /*
    This function is used to load fee collection 
    */
    public function feeCollectionStudentDeposit()
    {        
        $this->load->model("multibranch/multi_student_fee_model");
        $data                 = array();
        $date                 = $this->input->post('date');
        $fees_id              = $this->input->post('fees_id');
        $fees_list=[];
        $fee_arr=[];
        if(!empty($fees_id)){
        $fees_id_array        = explode(',', $fees_id);
            foreach ($fees_id_array as $fee_key => $fee_value) {
                   $array_branch_key= explode('-', $fee_value);

                   $fee_arr[$array_branch_key[1]][]=$array_branch_key[0];
            }

        }
        if(!empty($fee_arr)){

        $fees_list            = $this->multi_student_fee_model->getFeesDepositeByIdArray($fee_arr);
        }
      
        $data['student_list'] = $fees_list;
        $data['date']         = $date;
        $data['sch_setting']  = $this->sch_setting_detail;
        $page                 = $this->load->view('admin/multibranch/finance/_feeCollectionStudentDeposit', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

   /*
    This function is used to load income list based on search type 
    */
    public function getincomelistbydate()
    {
        $this->load->model("multibranch/multi_income_model");
        $search_type = $this->input->post('search_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');

        if ($search_type == "") {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        } else {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $incomeList = $this->multi_income_model->search($start_date, $end_date);

        $incomeList      = json_decode($incomeList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;
        if (!empty($incomeList->data)) {
            foreach ($incomeList->data as $key => $value) {
                $grand_total += $value->amount;

                $row   = array();
                $row[] = $value->branch_name;
                $row[] = $value->name;
                $row[] = $value->invoice_no;
                $row[] = $value->income_category;
                $row[] = $this->customlib->dateformat($value->date);
                $row[] = $currency_symbol . amountFormat($value->amount);

                $dt_data[] = $row;
            }
            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('grand_total') . "</b>";
            $footer_row[] = "<b>" . ($currency_symbol . amountFormat($grand_total)) . "</b>";
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($incomeList->draw),
            "recordsTotal"    => intval($incomeList->recordsTotal),
            "recordsFiltered" => intval($incomeList->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);

    }

    
    public function expensereport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('subsub_menu', 'finance/expensereport');
        $this->load->model("multibranch/multi_income_model");
        $data               = array();
        $data['title']      = 'Daily Collection Report';
        $data['searchlist'] = $this->customlib->get_searchtype();
        $this->form_validation->set_rules('date_from', $this->lang->line('date_from'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date_to', $this->lang->line('date_to'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {

            $date_from          = $this->input->post('date_from');
            $date_to            = $this->input->post('date_to');
            $formated_date_from = strtotime($this->customlib->dateFormatToYYYYMMDD($date_from));
            $formated_date_to   = strtotime($this->customlib->dateFormatToYYYYMMDD($date_to));
            $st_fees            = $this->multi_student_fee_model->getCurrentSessionStudentFees();
            $fees_data          = array();

            for ($i = $formated_date_from; $i <= $formated_date_to; $i += 86400) {
                $fees_data[$i]['amt']                       = 0;
                $fees_data[$i]['count']                     = 0;
                $fees_data[$i]['student_fees_deposite_ids'] = array();

            }

            if (!empty($st_fees)) {
                foreach ($st_fees as $fee_key => $fee_value) {
                    if (isJSON($fee_value->amount_detail)) {

                        $fees_details = (json_decode($fee_value->amount_detail));
                        if (!empty($fees_details)) {
                            foreach ($fees_details as $fees_detail_key => $fees_detail_value) {
                                $date = strtotime($fees_detail_value->date);
                                if ($date >= $formated_date_from && $date <= $formated_date_to) {
                                    if (array_key_exists($date, $fees_data)) {
                                        $fees_data[$date]['amt'] += $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $fees_data[$date]['count'] += 1;
                                        $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                                    } else {
                                        $fees_data[$date]['amt']                         = $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $fees_data[$date]['count']                       = 1;
                                        $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $data['fees_data'] = $fees_data;

        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/multibranch/finance/expensereport', $data);
        $this->load->view('layout/footer', $data);
    }

    /*
    This function is used to load datatabel for branch expense 
    */
    public function getexpenselistbydate()
    {        
        $this->load->model("multibranch/multi_expense_model");
        $search_type = $this->input->post('search_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');

        if ($search_type == "") {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        } else {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        
        $expenseList   = $this->multi_expense_model->search($start_date, $end_date);

        $m               = json_decode($expenseList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $grand_total += $value->amount;

                $row       = array();
                $row[]     = $value->branch_name;
                $row[]     = $value->name;
                $row[]     = $value->invoice_no;
                $row[]     = $value->exp_category;
                $row[]     =  $this->customlib->dateformat($value->date);
                $row[]     = $currency_symbol . amountFormat($value->amount);
                $dt_data[] = $row;
            }
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] =  "<b>" . $this->lang->line('grand_total')."</b>";
            $footer_row[] =  "<b>" . ($currency_symbol . amountFormat($grand_total))."</b>";
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);

    }

    /*
    This function is used to load datatabel for staff payroll
    */
    public function payroll()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('subsub_menu', 'finance/payroll');
        $this->load->model("multibranch/multi_payroll_model");

        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['date_type']   = $this->customlib->date_type();
        $data['date_typeid'] = '';

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];

        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';

        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label']        = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $data['payment_mode'] = $this->customlib->payment_mode();

        $result              = $this->multi_payroll_model->getbetweenpayrollReport($start_date, $end_date);
     
        $data['payrollList'] = $result;

        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/multibranch/finance/payroll', $data);
        $this->load->view('layout/footer', $data);
    } 
    
    /*
    This function is used to show all user 
    */
    public function userlogreport()
    {         
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('subsub_menu', 'finance/userlogreport');
        $this->load->model("multibranch/multi_user_log_model");
        $data               = array();
        $data['title']      = 'Daily Collection Report';
        $data['searchlist'] = $this->customlib->get_searchtype();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/multibranch/finance/userlogreport', $data);
        $this->load->view('layout/footer', $data);
    }
    
    /*
    This function is used to load datatabel for userlist
    */
    public function getuserloglistbydate()
    {
        $this->load->model("multibranch/multi_user_log_model");
        $search_type = $this->input->post('search_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');

        if ($search_type == "") {
            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';
        } else {
            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];
        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));
        
        $userlogList   = $this->multi_user_log_model->search($start_date, $end_date);

        $m               = json_decode($userlogList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        $grand_total     = 0;
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {        

                $row       = array();
                $row[]     = $value->branch_name;
                $row[]     = $value->user;
                $row[]     =  ucfirst($value->role);
                $row[]     = ($value->class_name != "") ? $value->class_name . "(" . $value->section_name . ")" : "";
                $row[]     = $value->ipaddress;
                $row[]     = $this->customlib->dateyyyymmddToDateTimeformat($value->login_datetime);
                $row[]     = $value->user_agent;
             
                $dt_data[] = $row;
            }
         
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);

    }


    
}