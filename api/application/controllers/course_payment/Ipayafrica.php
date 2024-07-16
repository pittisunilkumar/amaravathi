<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ipayafrica extends Admin_Controller {

    var $setting;
    var $payment_method;
    var $pay_method;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
        $this->load->model('course_model');
    }

    /*
    This is used to show payment detail page
    */
    public function index() {
        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $this->load->view('course_payment/ipayafrica/index', $data);
    }

    /*
    This is for payment gateway functionality
    */
    public function pay() {
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');

        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;

        if ($this->form_validation->run() == false) {
            $this->load->view('course_payment/ipayafrica/index', $data);
        } else {
            $instadetails = $this->paymentsetting_model->getActiveMethod();
            $insta_apikey = $instadetails->api_secret_key;
            $insta_authtoken = $instadetails->api_publishable_key;

            $fields = array("live"=> "1",
                "oid"=> uniqid(),
                "inv"=> time(),
                "ttl"=> convertBaseAmountCurrencyFormat($params['total_amount']),
                "tel"=> $_POST['phone'],
                "eml"=> $_POST['email'],
                "vid"=> ($this->pay_method->api_publishable_key),
                "curr"=> $params['currency_name'],
                "p1"=> "airtel",
                "p2"=> "",
                "p3"=> "",
                "p4"=> convertBaseAmountCurrencyFormat($params['total_amount']),
                "cbk"=> base_url().'course_payment/ipayafrica/success',
                "cst"=> "1",
                "crl"=> "2"
                );
             
            $datastring =  $fields['live'].$fields['oid'].$fields['inv'].$fields['ttl'].$fields['tel'].$fields['eml'].$fields['vid'].$fields['curr'].$fields['p1'].$fields['p2'].$fields['p3'].$fields['p4'].$fields['cbk'].$fields['cst'].$fields['crl'];

            $hashkey =($this->pay_method->api_secret_key);
            $generated_hash = hash_hmac('sha1',$datastring , $hashkey);
            $data['fields']=$fields;
            $data['generated_hash']=$generated_hash;
            $this->load->view('course_payment/ipayafrica/pay', $data);
        }
    }
    /*
    This is used to show success page status
    */
    public function success(){
        if(!empty($_GET['status'])){
            $params = $this->session->userdata('course_amount');
            $payment_id = $_GET['txncd'];;
            $payment_data = array(
                    'date' => date('Y-m-d'),
                    'student_id' => $params['student_id'],
                    'online_courses_id' => $params['courseid'],
                    'course_name' => $params['course_name'],
                    'actual_price' => $params['actual_amount'],
                    'paid_amount' => $params['total_amount'],
                    'payment_type' => 'Online',
                    'transaction_id' => $payment_id,
                    'note' => "Online course fees deposit through iPayAfrica Txn ID: " . $payment_id,
                    'payment_mode' => 'iPayAfrica',
                );
                $this->course_model->add($payment_data);
                $this->load->view('course_payment/paymentsuccess');
        }else{
            redirect(base_url("course_payment/course_payment/paymentfailed"));
        }
    }
}