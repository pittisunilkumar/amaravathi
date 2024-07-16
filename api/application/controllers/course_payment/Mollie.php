<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Mollie extends Admin_Controller {

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
        $this->load->view('course_payment/mollie/index', $data);
    }

    public function pay() {
        $this->form_validation->set_rules('phone', ('Phone'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('email', ('Email'), 'required|trim|xss_clean');
        $params = $this->session->userdata('course_amount');
        if ($this->form_validation->run() == false) {
            $data = array();
            $data['params'] = $this->session->userdata('course_amount');
            $data['setting'] = $this->setting;
            $data['api_error'] = array();
            $data['student_data'] = $this->student_model->get($data['params']['student_id']);
            $this->load->view('course_payment/mollie/index', $data);
        } else {

            $apidetails = $this->paymentsetting_model->getActiveMethod();
            $data = array();
            $data['name'] = $params['name'];
            $amount =convertBaseAmountCurrencyFormat($params['total_amount']);
            $api=' '.$apidetails->api_publishable_key;
            $order=time();
            $currency=$params['currency_name'];
            $redirectUrl=base_url()."course_payment/mollie/success";

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.mollie.com/v2/payments');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "amount[currency]=".$currency."&amount[value]=".$amount."&description=#".$order."&redirectUrl=".$redirectUrl);

            $headers = array();
            $headers[] = 'Authorization: Bearer'.$api;
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            $json = json_decode($result, true);
            
            if ($json['status']=='open') {
                $url = $json['_links']['checkout']['href'];
                
                $params['mollie_payment_id']=$json['id'];
                $this->session->set_userdata("params", $params);
               
                header("Location: $url");
            } else { 
                 
                $data = array();

                $data['params'] = $this->session->userdata('course_amount');
                $data['student_data'] = $this->student_model->get($data['params']['student_id']);
                $data['setting'] = $this->setting;
    
                $json = json_decode($result, true);
                $data['api_error'] = $json['detail'];
                $this->load->view('course_payment/mollie/index', $data);
            }
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