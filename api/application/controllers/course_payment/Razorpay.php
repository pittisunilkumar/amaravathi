<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Razorpay extends Admin_Controller {

    var $setting;
    var $payment_method;
    var $api_config;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->api_config = $this->paymentsetting_model->getActiveMethod();
        $this->payment_method = $this->paymentsetting_model->get();
        $this->load->model('course_model');
    }

    /*
    This is used to show payment detail page
    */
    public function index() {
        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $data['merchant_order_id'] = time() . "01";
        $data['txnid'] = time() . "02";
        $data['return_url'] = site_url() . 'course_payment/razorpay/callback';
        $data['total_amount']=$params['total_amount'];
        $data['total'] = $params['total_amount'] * 100;
        $data['key_id'] = $this->api_config->api_publishable_key;
        
        $this->load->view('course_payment/razorpay/razorpay', $data);
    }

    /*
    This is used to show payment detail page
    */
    public function callback() {
        $params = $this->session->userdata('course_amount');
        if(!empty($params)){
          $payment_id = $_POST['razorpay_payment_id'];
        $payment_data = array(
            'date' => date('Y-m-d'),
            'student_id' => $params['student_id'],
            'online_courses_id' => $params['courseid'],
            'course_name' => $params['course_name'],
            'actual_price' => $params['actual_amount'],
            'paid_amount' => $params['total_amount'],
            'payment_type' => 'Online',
            'transaction_id' => $payment_id,
            'note' => "Online course fees deposit through Razorpay Txn ID: " . $payment_id,
            'payment_mode' => 'Razorpay',
        );
        $this->course_model->add($payment_data);  
        }
        
         
        redirect('course_payment/razorpay/success');
    }

    /*
    This is used to show success page status
    */
    public function success() {
        $this->load->view('course_payment/paymentsuccess');
    }
}