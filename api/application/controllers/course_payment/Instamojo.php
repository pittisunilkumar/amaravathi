<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Instamojo extends Admin_Controller {

    var $setting;
    var $payment_method;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
        $this->load->model('course_model');
    }

    /*
    This is used to show payment detail page
    */
    public function index() {
        $data['params'] = $this->session->userdata('course_amount');
        $data['setting'] = $this->setting;
        $data['api_error'] =array();
        $this->load->view('course_payment/instamojo/index', $data);
    }

    /*
    This is for payment gateway functionality
    */
    public function insta_pay() {
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');

        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;

        if ($this->form_validation->run() == false) {
            $data['api_error'] =array();
            $this->load->view('course_payment/instamojo/index', $data);
        } else {
            $instadetails = $this->paymentsetting_model->getActiveMethod();
            $insta_apikey = $instadetails->api_secret_key;
            $insta_authtoken = $instadetails->api_publishable_key;
            $params = $this->session->userdata('course_amount');
            $amount = convertBaseAmountCurrencyFormat($params['total_amount']);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://test.instamojo.com/api/1.1/payment-requests/'); // for live https://www.instamojo.com/api/1.1/payment-requests/
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Api-Key:$insta_apikey",
                "X-Auth-Token:$insta_authtoken"));
            $payload = Array(
                'purpose' => 'Online Course Fess',
                'amount' => $amount,
                'phone' => $_POST['phone'],
                'buyer_name' => $params['name'],
                'redirect_url' => base_url() . 'course_payment/instamojo/success',
                'send_email' => false,
                'webhook' => base_url() . 'webhooks/insta_webhook',
                'send_sms' => false,
                'email' => $_POST['email'],
                'allow_repeated_payments' => false
            );
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            $response = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($response, true);
            if ($json['success']) {
                $url = $json['payment_request']['longurl'];
                header("Location: $url");
            } else {  
                $json = json_decode($response, true);
                $data['api_error'] = $json['message'];
                
                $this->load->view('course_payment/instamojo/index', $data);
            }
        }
    }

    /*
    This is used to show success page status
    */
    public function success() {
        if ($_GET['payment_status'] == 'Credit') {
            $params = $this->session->userdata('course_amount');
            $payment_id = $_GET['payment_id'];
            $payment_data = array(
                'date' => date('Y-m-d'),
                'student_id' => $params['student_id'],
                'online_courses_id' => $params['courseid'],
                'course_name' => $params['course_name'],
                'actual_price' => $params['actual_amount'],
                'paid_amount' => $params['total_amount'],
                'payment_type' => 'Online',
                'transaction_id' => $payment_id,
                'note' => "Online course fees deposit through Instamojo Txn ID: " . $payment_id,
                'payment_mode' => 'Instamojo',
            );
            $this->course_model->add($payment_data);
            $this->load->view('course_payment/paymentsuccess');
        } else {
            redirect(base_url("course_payment/course_payment/paymentfailed"));
        }
    }
}