<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Onepay extends Admin_Controller {

    var $setting;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->api_config = $this->paymentsetting_model->getActiveMethod();
        $this->load->model('course_model');
    }
 
    /*
    This is used to show payment detail page
    */
    public function index() {
        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $this->load->view('course_payment/onepay/index', $data);
    }


    public function pay() {
        $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');
        $params = $this->session->userdata('course_amount');
        if ($this->form_validation->run() == false) {
            $data = array();
             $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
              $this->load->view('course_payment/onepay/index', $data);
        } else {
            
            $data = array();
            $data['name'] = $params['name'];
            $amount =convertBaseAmountCurrencyFormat($params['total_amount']);
            
        $student_data = $this->student_model->get($params['student_id']);
        $appendAmp = 0;
        $SECURE_SECRET =$this->api_config->api_signature;
        $payment_data=array(
        'AVS_City' => '',
        'AVS_Country' =>'',
        'AVS_PostCode' => '',
        'AVS_StateProv' => '',
        'AVS_Street01' => '',
        'AgainLink' => urlencode($_SERVER['HTTP_REFERER']),
        'Title' => '',
        'display' => '',
        'vpc_AccessCode' => $this->api_config->salt,
        'vpc_Amount' => $amount*100,
        'vpc_Command' => 'pay',
        'vpc_Customer_Email' => '',
        'vpc_Customer_Id' => '',
        'vpc_Customer_Phone' => '',
        'vpc_Locale' => 'en',
        'vpc_MerchTxnRef' => date('YmdHis') . rand(),
        'vpc_Merchant' => $this->api_config->api_publishable_key,
        'vpc_OrderInfo' => 'JSECURETEST01',
        'vpc_ReturnURL' => base_url() . 'course_payment/onepay/success',
        'vpc_SHIP_City' => '',
        'vpc_SHIP_Country' => '',
        'vpc_SHIP_Provice' => '',
        'vpc_SHIP_Street01' => '',
        'vpc_TicketNo' => $_SERVER ['REMOTE_ADDR'],
        'vpc_Version' => '2');
        
        $vpcURL="https://mtf.onepay.vn/paygate/vpcpay.op?";
        foreach($payment_data as $key => $value) {
            if (strlen($value) > 0) {
                if ($appendAmp == 0) {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $appendAmp = 1;
                } else {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                }

                if ((strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
                    $md5HashData .= $key . "=" . $value . "&";
                }
            }
        }

        $md5HashData = rtrim($md5HashData, "&");

        if (strlen($SECURE_SECRET) > 0) {

            $vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $md5HashData, pack('H*',$SECURE_SECRET)));
        }


        header("Location: ".$vpcURL);
        }
    }

    public function success() {
        $apidetails = $this->paymentsetting_model->getActiveMethod();
        $data['params'] = $this->session->userdata('course_amount');
         $api=' '.$apidetails->api_publishable_key;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.mollie.com/v2/payments/'.$data['params']['mollie_payment_id']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Authorization: Bearer'.$api;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $json=json_decode($result);

        if ($json->status=='paid') {
            $payment_id = $json->id; 
            $bulk_fees=array();
            $params     = $this->session->userdata('course_amount');

            $payment_data = array(
                'date' => date('Y-m-d'),
                'student_id' => $params['student_id'],
                'online_courses_id' => $params['courseid'],
                'course_name' => $params['course_name'],
                'actual_price' => $params['actual_amount'],
                'paid_amount' => $params['total_amount'],
                'payment_type' => 'Online',
                'transaction_id' => $payment_id,
                'note' => "Online course fees deposit through Mollie Txn ID: " . $payment_id,
                'payment_mode' => 'Mollie',
            );
            $inserted_id = $this->course_model->add($payment_data);
                 
            if ($inserted_id) {
                $this->load->view('course_payment/paymentsuccess');                     
            } else {
              redirect(base_url("course_payment/course_payment/paymentfailed"));
            }

        } else {
            redirect(base_url("course_payment/course_payment/paymentfailed"));
        }
    }
}