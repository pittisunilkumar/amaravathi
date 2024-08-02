<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cashfree extends Admin_Controller {

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
        $this->load->view('course_payment/cashfree/index', $data);
    }

    public function pay() {

        $this->form_validation->set_rules('phone', ('Phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', ('Email'), 'trim|required|xss_clean');
        $params = $this->session->userdata('course_amount');
        if ($this->form_validation->run() == false) {
            $data = array();
            $data['params'] = $this->session->userdata('course_amount');
            $data['setting'] = $this->setting;
            $data['api_error'] = array();
            $data['student_data'] = $this->student_model->get($data['params']['student_id']);
            $data['api_error'] = $data['api_error'] = array();
            $this->load->view('course_payment/cashfree/index', $data);
        } else {

            $data = array();
            $data['name'] = $params['name'];
            $amount =convertBaseAmountCurrencyFormat($params['total_amount']);
            //$api=' '.$this->api_config->api_publishable_key;
            $customer_id="Student_id_".$params['student_id'];
            $order_id="order_".time().mt_rand(100,999);
            $currency=$params['currency_name'];
            $redirectUrl=base_url()."course_payment/cashfree/success?order_id={order_id}&order_token={order_token}";

            $my_array=array(
            "order_id"=> $order_id,
            "order_amount"=> $amount,
            "order_currency"=> $currency,
            "customer_details"=> array(
            "customer_id"=> $customer_id,
            "customer_name"=> $data['name'],
            "customer_email"=> $_POST['email'],
            "customer_phone"=> $_POST['phone'],
            ),
            "order_meta"=> array(
            "return_url"=> $redirectUrl,
            "notify_url"=> base_url() .'webhooks/cashfree',
            "payment_methods"=> ""
            )
        );
        $new_arrya=(object)$my_array;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://sandbox.cashfree.com/pg/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($new_arrya));

            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'X-Api-Version: 2021-05-21';
            $headers[] = 'X-Client-Id: '.$this->api_config->api_publishable_key;
            $headers[] = 'X-Client-Secret: '.$this->api_config->api_secret_key;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            $json=json_decode($result);
           
            if (isset($json->order_status) && $json->order_status="ACTIVE") {
                $url = $json->payment_link;
               
                header("Location: $url");
            } else {
                 
                $data = array();
                $data['params'] = $this->session->userdata('course_amount');
                $data['setting'] = $this->setting;
                $data['api_error'] = array();
                $data['student_data'] = $this->student_model->get($data['params']['student_id']);
                $data['api_error'] = $json->message;
                $this->load->view('course_payment/cashfree/index', $data);
            }
        }
    }

    public function success() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://sandbox.cashfree.com/pg/orders/'.$_GET['order_id']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-Api-Version: 2021-05-21';
        $headers[] = 'X-Client-Id: '.$this->api_config->api_publishable_key;
        $headers[] = 'X-Client-Secret: '.$this->api_config->api_secret_key;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $response_data=json_decode($result);
       if (isset($response_data->order_status) && $response_data->order_status=="PAID") {
                $payment_id = $_GET['order_id']; 
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
                    'note' => "Online course fees deposit through Cashfree Txn ID: " . $payment_id,
                    'payment_mode' => 'Cashfree',
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