<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sslcommerz extends Admin_Controller {

    var $setting;
    var $payment_method;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
    }

    public function index() {
         $data = array();
        $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
        $this->load->view('payment/sslcommerz/index', $data);
    }

    public function pay() {

        $this->form_validation->set_rules('phone', ('Phone'), 'trim|required');
        $this->form_validation->set_rules('email', ('Email'), 'trim|required');

        if ($this->form_validation->run() == false) {
             $data = array();
        $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
            $this->load->view('payment/sslcommerz/index', $data);
        } else {

            $instamojo = $this->paymentsetting_model->getActiveMethod();

            $pay_method = $this->paymentsetting_model->getActiveMethod();
            if ($this->session->has_userdata('params')) {
                $session_params = $this->session->userdata('params');
                $data['session_params'] = $session_params;
            }
 
        $amount = convertBaseAmountCurrencyFormat($data['session_params']['payment_detail']->fine_amount+$data['session_params']['total']);
        
        $requestData        = array();
        $CURLOPT_POSTFIELDS = array(
            'store_id'         => $pay_method->api_publishable_key,
            'store_passwd'     => $pay_method->api_password,
            'total_amount'     => $amount,
            'currency'         => $data['session_params']['invoice']->currency_name,
            'tran_id'          => abs(crc32(uniqid())),
            'success_url'      => base_url() . 'gateway/sslcommerz/success',
            'fail_url'         => base_url() . 'gateway/sslcommerz/fail',
            'cancel_url'       => base_url() . 'gateway/sslcommerz/cancel',
            'cus_name'         => $data['session_params']['name'],
            'cus_email'        => !empty($_POST['email']) ? $_POST['email'] : "example@email.com",
            'cus_add1'         =>  "Dhaka",
            'cus_phone'        => !empty($_POST['phone']) ? $_POST['phone'] : "01711111111",
            'cus_city'         => '',
            'cus_country'      => '',
            'multi_card_name'  => 'mastercard,visacard,amexcard,internetbank,mobilebank,othercard ',
            'shipping_method'  => 'NO',
            'product_name'     => 'test',
            'product_category' => 'Electronic',
            'product_profile'  => 'general',
        );
        $string = "";
        foreach ($CURLOPT_POSTFIELDS as $key => $value) {
            $string .= $key . '=' . $value . "&";
            if ($key == 'product_profile') {
                $string .= $key . '=' . $value;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php');//for sand box https://sandbox.sslcommerz.com/gwprocess/v4/api.php
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$string");

        $headers   = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $response = json_decode($result);
       
        if($response->status=='FAILED'){
        $data = array();
        $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
        $data['api_error']  = $response->failedreason;
        
        $this->load->view('payment/sslcommerz/index', $data);
        }else{
            header("Location: $response->GatewayPageURL");
        }
        }
    }
 
    public function success() {
        if ($_POST['status'] == 'VALID') {
            $payment_id = $_POST['val_id'];
            if ($payment_id) {
                $params = $this->session->userdata('params');
               
                $json_array = array(
                    'amount' => $params['total'],
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0,
                    'amount_fine' => $params['payment_detail']->fine_amount,
                    'received_by' => '',
                    'description' => "Online fees deposit through Sslcommerz TXN ID: " . $payment_id,
                    'payment_mode' => 'Sslcommerz',
                );

              if(($params['fee_category']=='transport') && !empty($params['student_transport_fee_id']) ){
                    $data = array(
                    'student_transport_fee_id' => $params['student_transport_fee_id'],
                    'amount_detail' => $json_array,
                );
                }else{
                    $data = array(
                    'student_fees_master_id' => $params['student_fees_master_id'],
                    'fee_groups_feetype_id' => $params['fee_groups_feetype_id'],
                    'amount_detail' => $json_array,
                );
                }

                $send_to = $params['guardian_phone'];
                $inserted_id = $this->studentfeemaster_model->fee_deposit($data, $send_to, "");
                $invoice_detail = json_decode($inserted_id);
                redirect("payment/successinvoice/" . $invoice_detail->invoice_id . "/" . $invoice_detail->sub_invoice_id, "refresh");
            }
        } else {
            redirect(base_url("payment/paymentfailed"));
        }
    }

}
