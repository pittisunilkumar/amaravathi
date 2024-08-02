<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Billplz extends Admin_Controller {

    var $setting;
 
    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->api_config = $this->paymentsetting_model->getActiveMethod();
        $this->load->library('billplz_lib');
        $this->load->model('course_model');
      //  $this->load->library('course_mail_sms');

    }

    /*
    This is used to show payment detail page
    */
    public function index() {
        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $this->load->view('course_payment/billplz/index', $data);
    }

    /*
    This is for payment gateway functionality
    */
    public function pay(){
        $params = $this->session->userdata('course_amount');
        $data['name'] = $params['name'];
        $data['title'] = 'Online Course Fee';
        $data['return_url'] = base_url() . 'course_payment/billplz/success';
        $parameter = array(
            'title' => $data['name'],
            'description' => $data['title'],
            'amount' => convertBaseAmountCurrencyFormat($params['total_amount'])*100
        );
        $optional = array(
            'fixed_amount' => 'true',
            'fixed_quantity' => 'true',
            'payment_button' => 'pay',
            'redirect_uri'=>$data['return_url'],
            'photo' => '',
            'split_header' => false,
            'split_payments' => array(
            ['split_payments[][email]' => $this->api_config->api_email],
            ['split_payments[][fixed_cut]' => '0'],
            ['split_payments[][variable_cut]' => ''],
            ['split_payments[][stack_order]' => '0'],
        )
        );
        $api_key=$this->api_config->api_secret_key;
        $this->billplz_lib->payment($parameter,$optional,$api_key);
    }
 
    /*
    This is used to show success page status
    */
    public function success() {
        $params = $this->session->userdata('course_amount');
        if($_GET['billplz']['paid']=='true'){
            $payment_id =$_GET['billplz']['id'];
            $payment_data = array(
                'date' => date('Y-m-d'),
                'student_id' => $params['student_id'],
                'online_courses_id' => $params['courseid'],
                'course_name' => $params['course_name'],
                'actual_price' => $params['actual_amount'],
                'paid_amount' => $params['total_amount'],
                'payment_type' => 'Online',
                'transaction_id' => $payment_id,
                'note' => "Online course fees deposit through Billplz Txn ID: " . $payment_id,
                'payment_mode' => 'Billplz',
            );
            $this->course_model->add($payment_data);
            
            $this->load->view('course_payment/paymentsuccess');
        }else{
            redirect(base_url("course_payment/course_payment/paymentfailed"));
        }
    }
}
