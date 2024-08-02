<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Midtrans extends Admin_Controller {

    var $setting;
    var $payment_method;
    var $api_config;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
        $this->api_config = $this->paymentsetting_model->getActiveMethod();
        $this->load->library('Midtrans_lib');
        $this->load->model('course_model');
    }

    /*
    This is used to show payment detail page
    */
    public function index() {
        $data['params'] = $this->session->userdata('course_amount');
        $data['setting'] = $this->setting;
        $enable_payments = array('credit_card');
        $transaction = array(
            'enabled_payments' => $enable_payments,
            'transaction_details' => array(
            'order_id' => time(),
            'gross_amount' => round(convertBaseAmountCurrencyFormat($data['params']['total_amount'])), // no decimal allowed
            ),
        );
        $snapToken = $this->midtrans_lib->getSnapToken($transaction, $this->api_config->api_secret_key);
        $data['snap_Token'] = $snapToken;
        $this->load->view('course_payment/midtrans/index', $data);
    }

    /*
    This is for payment gateway functionality
    */
    public function midtrans_pay() {
        $response = json_decode($_POST['result_data']);
        $payment_id = $response->transaction_id;
        $params = $this->session->userdata('course_amount');
        $payment_data = array(
                'date' => date('Y-m-d'),
                'student_id' => $params['student_id'],
                'online_courses_id' => $params['courseid'],
                'course_name' => $params['course_name'],
                'actual_price' => $params['actual_amount'],
                'paid_amount' => $params['total_amount'],
                'payment_type' => 'Online',
                'transaction_id' => $payment_id,
                'note' => "Online course fees deposit through midtrans Txn ID: " . $payment_id,
                'payment_mode' => 'midtrans',
            );
            $this->course_model->add($payment_data);
            echo 1;
    }
    
    /*
    This is used to show success page status
    */
    public function success(){
       $this->load->view('course_payment/paymentsuccess');
    }
}