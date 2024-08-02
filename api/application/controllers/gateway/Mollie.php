<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Mollie extends Admin_Controller {

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
        $this->load->view('payment/mollie/index', $data);
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
            $this->load->view('payment/mollie/index', $data);
        } else {

            $pay_method = $this->paymentsetting_model->getActiveMethod();
            if ($this->session->has_userdata('params')) {
                $session_params = $this->session->userdata('params');
                $data['session_params'] = $session_params;
            }

            $amount =convertBaseAmountCurrencyFormat($data['session_params']['payment_detail']->fine_amount+$data['session_params']['total']);
            $api=' '.$pay_method->api_publishable_key;
            $order=time();

            $currency=$data['session_params']['invoice']->currency_name;

            $redirectUrl=base_url()."gateway/mollie/success";
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
                $this->session->set_userdata("mollie_payment_id", $json['id']);
                
                header("Location: $url");
            }else {

            $data = array();
            $json = json_decode($result, true); 
            $error = array();
           
            $data['params'] = $this->session->userdata('params');
            $data['setting'] = $this->setting; 
				
                if(isset($json['message'])){
                    $data['api_error'] = $json['message'];
                }elseif($json['detail']){
                    $data['api_error'] = $json['detail'];
                }else{
                    $data['api_error'] = ""; 
                }
           
            $data['student_data'] = $this->student_model->get($data['params']['student_id']);
            $this->load->view('payment/mollie/index', $data);
        }
           
        }
    }
 
    public function success() {

        $apidetails = $this->paymentsetting_model->getActiveMethod();
        $data['params'] = $this->session->userdata('params');
        $mollie_payment_id=$this->session->userdata('mollie_payment_id');
        $api=' '.$apidetails->api_publishable_key;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.mollie.com/v2/payments/'.$mollie_payment_id);
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
                    
                  
                   $params = $this->session->userdata('params');
                  
                $ref_id = $payment_id;
                $json_array = array(
                    'amount' => $params['total'],
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0,
                    'amount_fine' => $params['payment_detail']->fine_amount,
                    'received_by' => '',
                    'description' => "Online fees deposit through Mollie TXN ID: " . $ref_id,
                    'payment_mode' => 'Mollie',
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
                    $send_to     = $params['guardian_phone'];
                    $inserted_id = $this->studentfeemaster_model->fee_deposit_bulk($bulk_fees, $send_to);
                   

                } else {
                    redirect(base_url("payment/paymentfailed"));
                }


      
    }

}
