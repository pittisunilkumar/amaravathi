<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ccavenue extends Admin_Controller {

    var $setting;
    var $payment_method;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
        $this->load->library('Ccavenue_crypto');
    }

    public function index() {
        $data = array();
        $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
        $this->load->view('payment/ccavenue/index', $data);
    }

    public function pay() {
           $data = array();
        $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
        $this->form_validation->set_rules('phone', ('Phone'), 'trim|required');
        $this->form_validation->set_rules('email', ('Email'), 'trim|required');

        if ($this->form_validation->run() == false) {
          
            $this->load->view('payment/ccavenue/index', $data);
        } else {

            $pay_method = $this->paymentsetting_model->getActiveMethod();
            if ($this->session->has_userdata('params')) {
                $session_params = $this->session->userdata('params');
                $data['session_params'] = $session_params;
            }
            $details['tid']=abs(crc32(uniqid()));
            $details['merchant_id']=$pay_method->api_secret_key;
            $details['order_id']=abs(crc32(uniqid()));
            $details['amount']=convertBaseAmountCurrencyFormat($session_params['payment_detail']->fine_amount+$session_params['total']);
            $details['currency']='INR';
            $details['redirect_url']=base_url('gateway/ccavenue/success'); 
            $details['cancel_url']=base_url('gateway/ccavenue/cancel');
            $details['language'] = "EN";
            $details['billing_name']     = $session_params['name'];
            $details['billing_email']= $_POST['email'];
            $details['billing_tel']= $_POST['phone'];
            $merchant_data="";
            foreach ($details as $key => $value){
            $merchant_data.=$key.'='.$value.'&';
            } 
           $data['encRequest'] = $this->ccavenue_crypto->encrypt($merchant_data,$pay_method->salt);
           $data['access_code'] = $pay_method->api_publishable_key;

            $this->load->view('payment/ccavenue/pay', $data);

         
        }
    }

    public function success() {
        $pay_method = $this->paymentsetting_model->getActiveMethod();
        $encResponse=$_POST["encResp"];  
        $rcvdString=$this->ccavenue_crypto->decrypt($encResponse,$pay_method->salt);  
        $params = $this->session->userdata('params');
        $order_status=array();
        $decryptValues=explode('&', $rcvdString);
        $dataSize=sizeof($decryptValues);
        for($i = 0; $i < $dataSize; $i++) 
        {
            $information=explode('=',$decryptValues[$i]);
            $status[$information[0]]=$information[1];
        }
        if($status['order_status']=="Success")
        { 
            $tracking_id = $status['tracking_id'];
            $bank_ref_no = $status['bank_ref_no'];
            $json_array = array(
                'amount' => $params['total'],
                'date' => date('Y-m-d'),
                'amount_discount' => 0,
                'amount_fine' => $params['payment_detail']->fine_amount,
                'received_by' => '',
                'description' => "Online fees deposit through CCAvenue. TXN ID: " . $tracking_id . " Bank Ref. No.: " . $bank_ref_no,
                'payment_mode' => 'Ccavenue',
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
        }else if($status['order_status']==="Aborted"){
            echo "<br>We will keep you posted regarding the status of your order through e-mail";
        }else if($status['order_status']==="Failure"){
            redirect(base_url("payment/paymentfailed"));
        }else{
            echo "<br>Security Error. Illegal access detected";
        }
    }
}
