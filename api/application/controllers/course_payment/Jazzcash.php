<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Jazzcash extends Admin_Controller {

    var $setting;
    var $payment_method;
    var $pay_method;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
        $this->api_config = $this->paymentsetting_model->getActiveMethod();
        $this->load->model('course_model');
        date_default_timezone_set("Asia/Karachi");
    }

    /*
    This is used to show payment detail page
    */
    public function index() {
        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $this->load->view('course_payment/jazzcash/index', $data);
    }

    /*
    This is for payment gateway functionality
    */
    public function pay(){
        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $amount = convertBaseAmountCurrencyFormat($params['total_amount']);
        $data['title'] = 'Online Course Fee';
        $data['return_url'] = base_url() . 'course_payment/jazzcash/success';
        $data['pp_MerchantID'] = $this->api_config->api_secret_key;
        $data['pp_Password'] = $this->api_config->api_password;
        $data['currency_code'] = $params['currency_name'];
        $data['ExpiryTime'] = date('YmdHis', strtotime("+3 hours"));
        $data['TxnDateTime'] = date('YmdHis', strtotime("+0 hours"));
        $data['TxnRefNumber'] = "T". date('YmdHis');
        $input_para["pp_Version"]="2.0";
        $input_para["pp_IsRegisteredCustomer"]="Yes";
        $input_para["pp_TxnType"]="MPAY";
        $input_para["pp_TokenizedCardNumber"]="";
        $input_para["pp_CustomerID"]=time();
        $input_para["pp_CustomerEmail"]="";
        $input_para["pp_CustomerMobile"]="";
        $input_para["pp_MerchantID"]=$data['pp_MerchantID'];
        $input_para["pp_Language"]="EN";
        $input_para["pp_SubMerchantID"]="";
        $input_para["pp_Password"]=$data['pp_Password'];
        $input_para["pp_TxnRefNo"]=$data['TxnRefNumber'];
        $input_para["pp_Amount"]=$amount*100;
        $input_para["pp_DiscountedAmount"]="";
        $input_para["pp_DiscountBank"]="";
        $input_para["pp_TxnCurrency"]="PKR";
        $input_para["pp_TxnDateTime"]=$data['TxnDateTime'];
        $input_para["pp_TxnExpiryDateTime"]=$data['ExpiryTime'];
        $input_para["pp_BillReference"]=time();
        $input_para["pp_Description"]=$data['title'];
        $input_para["pp_ReturnURL"]=$data['return_url'];
        $input_para["pp_SecureHash"]="0123456789";
        $input_para["ppmpf_1"]="1";
        $input_para["ppmpf_2"]="2";
        $input_para["ppmpf_3"]="3";
        $input_para["ppmpf_4"]="4";
        $input_para["ppmpf_5"]="5";
        $data['payment_data']=$input_para;
        $this->load->view('course_payment/jazzcash/pay', $data);
    }

    /*
    This is used to show success page status
    */
    public function success() {

        $params = $this->session->userdata('course_amount');
        $data = array();
        if($_POST['pp_ResponseCode']=='000'){
            $payment_id = $_POST['pp_TxnRefNo'];
            $payment_data = array(
                'date' => date('Y-m-d'),
                'student_id' => $params['student_id'],
                'online_courses_id' => $params['courseid'],
                'course_name' => $params['course_name'],
                'actual_price' => $params['actual_amount'],
                'paid_amount' => $params['total_amount'],
                'payment_type' => 'Online',
                'transaction_id' => $payment_id,
                'note' => "Online course fees deposit through JazzCash Txn ID: " . $payment_id,
                'payment_mode' => 'JazzCash',
            );
            $this->course_model->add($payment_data);
            $this->load->view('course_payment/paymentsuccess');

        }elseif($_POST['pp_ResponseCode']=='112'){
            redirect(base_url("course_payment/course_payment/paymentfailed"));
        }else{
            $this->session->set_flashdata('msg',$_POST['pp_ResponseMessage']);
            redirect(site_url('course_payment/jazzcash'));
        }
    }
}