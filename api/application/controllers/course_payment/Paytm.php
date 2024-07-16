<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Paytm extends Admin_Controller {

    var $setting;
    var $payment_method;
    var $api_config;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
        $this->api_config = $this->paymentsetting_model->getActiveMethod();
        $this->load->library('Paytm_lib');
        $this->load->model('course_model');
    }

    /*
    This is used to show payment detail page and payment gateway functionality
    */
    public function index() {
        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        
        $paytmParams = array();
        $ORDER_ID = time();
        $CUST_ID = time();
        $paytmParams = array(
            "MID" => $this->api_config->api_publishable_key,
            "WEBSITE" => $this->api_config->paytm_website,
            "INDUSTRY_TYPE_ID" => $this->api_config->paytm_industrytype,
            "CHANNEL_ID" => "WEB",
            "ORDER_ID" => $ORDER_ID,
            "CUST_ID" => $params['student_id'],
            "TXN_AMOUNT" => convertBaseAmountCurrencyFormat($params['total_amount']),
            "CALLBACK_URL" => base_url() . "course_payment/Paytm/paytm_response",
        ); 
      // echo "<pre>"; print_r($paytmParams);die;
        $paytmChecksum = $this->paytm_lib->getChecksumFromArray($paytmParams, $this->api_config->api_secret_key);

        $paytmParams["CHECKSUMHASH"] = $paytmChecksum;
       $transactionURL = 'https://securegw.paytm.in/order/process';
        //$transactionURL              = 'https://securegw-stage.paytm.in/order/process';//for sand-box
         
        $data['paytmParams'] = $paytmParams;
        $data['transactionURL'] = $transactionURL;
        $this->load->view('course_payment/paytm/index', $data);
    }

    /*
    This is used to show success page status
    */
    public function paytm_response() {

        $paytmChecksum = "";
        $paramList = array();
        $isValidChecksum = "FALSE";
        $paramList = $_POST;
        $paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : "";

        $isValidChecksum = $this->paytm_lib->verifychecksum_e($paramList, $this->api_config->api_secret_key, $paytmChecksum);

        if ($isValidChecksum == "TRUE") {

            if ($_POST["STATUS"] == "TXN_SUCCESS") {

                $params = $this->session->userdata('course_amount');
                $payment_id = $_POST['TXNID'];
                $payment_data = array(
                    'date' => date('Y-m-d'),
                    'student_id' => $params['student_id'],
                    'online_courses_id' => $params['courseid'],
                    'course_name' => $params['course_name'],
                    'actual_price' => $params['actual_amount'],
                    'paid_amount' => $params['total_amount'],
                    'payment_type' => 'Online',
                    'transaction_id' => $payment_id,
                    'note' => "Online course fees deposit through Paytm Txn ID: " . $payment_id,
                    'payment_mode' => 'Paytm'
                );
                $this->course_model->add($payment_data);
                $this->load->view('course_payment/paymentsuccess');
            } else {
               redirect(base_url("course_payment/course_payment/paymentfailed"));
            }
        } else {
            redirect(base_url("course_payment/course_payment/paymentfailed"));
        }
    }
}