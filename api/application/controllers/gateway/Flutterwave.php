<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Flutterwave extends Admin_Controller {

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
        $this->load->view('payment/flutterwave/index', $data);
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
            $this->load->view('payment/flutterwave/index', $data);
        } else {

           
       

            $pay_method = $this->paymentsetting_model->getActiveMethod();
            if ($this->session->has_userdata('params')) {
                $session_params = $this->session->userdata('params');
                $data['session_params'] = $session_params;
            }

            $amount = $data['session_params']['total'];
            $secret_key = $pay_method->api_secret_key;
            $public_key = $pay_method->api_publishable_key;
            $data['currency_code'] = $session_params['invoice']->currency_name;
           
            $curl = curl_init();
            $customer_email = $_POST['email'];
            $currency = $data['currency_code'];
            $txref = "rave" . uniqid();
            $PBFPubKey = $public_key; 
            $redirect_url = base_url() . 'gateway/flutterwave/success'; // Set your own redirect URL
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => json_encode([
                'amount'=>number_format((float)(convertBaseAmountCurrencyFormat($session_params['payment_detail']->fine_amount+$session_params['total'])), 2, '.', ''),
                'customer_email'=>$customer_email,
                'currency'=>$currency,
                'txref'=>$txref,
                'PBFPubKey'=>$PBFPubKey,
                'redirect_url'=>$redirect_url,
              ]),
              CURLOPT_HTTPHEADER => [
                "content-type: application/json",
                "cache-control: no-cache"
              ],
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if($err){
             
              die('Curl returned error: ' . $err);
            }

            $transaction = json_decode($response);

            if(!$transaction->data && !$transaction->data->link){
             
              print_r('API returned error: ' . $transaction->message);
            }

           

            header('Location: ' . $transaction->data->link);
            

           
        }
    }

    
    public function success() {

       $pay_method = $this->paymentsetting_model->getActiveMethod();
       $secret_key = $pay_method->api_secret_key;
       if ($this->session->has_userdata('params')) {
                $session_params = $this->session->userdata('params');
                $data['session_params'] = $session_params;
            }

            $amount = $data['session_params']['total'];
       if (isset($_GET['txref'])) {
        $ref = $_GET['txref'];
        $amount = $amount; //Get the correct amount of your product
        $currency = $session_params['invoice']->currency_name;; //Correct Currency from Server

        $query = array(
            "SECKEY" => $secret_key,
            "txref" => $ref
        );

        $data_string = json_encode($query);
                
        $ch = curl_init('https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                              
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($ch); 

        $resp = json_decode($response, true);

        $paymentStatus = $resp['data']['status'];
        $chargeResponsecode = $resp['data']['chargecode'];
        $chargeAmount = $resp['data']['amount'];
        $chargeCurrency = $resp['data']['currency'];
        $txid= $resp['data']['txref'];
        if (($chargeResponsecode == "00" || $chargeResponsecode == "0") && ($chargeAmount == $amount)  && ($chargeCurrency == $currency)) {
         if ($txid) {
                $params = $this->session->userdata('params');
                $ref_id = $txid;
                $json_array = array(
                    'amount' => $params['total'],
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0,
                    'amount_fine' => $params['payment_detail']->fine_amount,
                    'received_by' => '',
                    'description' => "Online fees deposit through Flutterwave TXN ID: " . $ref_id,
                    'payment_mode' => 'Flutterwave',
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
        else {
      redirect(base_url("payment/paymentfailed"));
    }
    }

}
