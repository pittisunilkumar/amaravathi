<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Flutterwave extends Admin_Controller {

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
        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $this->load->view('course_payment/flutterwave/index', $data);
    }

    /*
    This is for payment gateway functionality
    */
    public function pay() {
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');

        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;

        if ($this->form_validation->run() == false) {
            $this->load->view('course_payment/flutterwave/index', $data);
        } else { 
            $details = $this->paymentsetting_model->getActiveMethod();
            $api_secret_key = $details->api_secret_key;
            $api_publishable_key = $details->api_publishable_key;
            $amount = convertBaseAmountCurrencyFormat($params['total_amount']);
            $curl = curl_init();
            $customer_email = $_POST['email'];
            $currency = $params['currency_name'];
            
            $txref = "rave" . uniqid(); // ensure you generate unique references per transaction.
            // get your public key from the dashboard.
            $PBFPubKey = $api_publishable_key; 
            $redirect_url = base_url() . 'course_payment/flutterwave/success'; // Set your own redirect URL

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/hosted/pay",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => json_encode([
                'amount'=>$amount,
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
              // there was an error contacting the rave API
              die('Curl returned error: ' . $err);
            }
            $transaction = json_decode($response);
            if(!$transaction->data && !$transaction->data->link){
              // there was an error from the API
              print_r('API returned error: ' . $transaction->message);
            }
            // redirect to page so User can pay
            header('Location: ' . $transaction->data->link);
        }
    }

    /*
    This is used to show success page status
    */
    public function success() {
        $details = $this->paymentsetting_model->getActiveMethod();
     $api_secret_key = $details->api_secret_key;
        $params = $this->session->userdata('course_amount');
       
       if(isset($_GET['cancelled']) && $_GET['cancelled']!=true){
            if (isset($_GET['txref'])) {
        $ref = $_GET['txref'];
        $query = array(
            "SECKEY" => $api_secret_key,
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
              // transaction was successful...
              // please check other things like whether you already gave value for this ref
              // if the email matches the customer who owns the product etc
              //Give Value and return to Success page
                $payment_id = $txid;
                $payment_data = array(
                    'date' => date('Y-m-d'),
                    'student_id' => $params['student_id'],
                    'online_courses_id' => $params['courseid'],
                    'course_name' => $params['course_name'],
                    'actual_price' => $params['actual_amount'],
                    'paid_amount' => $params['total_amount'],
                    'payment_type' => 'Online',
                    'transaction_id' => $payment_id,
                    'note' => "Online course fees deposit through Flutterwave Txn ID: " . $payment_id,
                    'payment_mode' => 'Flutterwave',
                );
                $this->course_model->add($payment_data);
                $this->load->view('course_payment/paymentsuccess');
        } else {
           redirect(base_url("course_payment/course_payment/paymentfailed"));
        }
    }
       }else{
        redirect(base_url("course_payment/course_payment/paymentfailed"));
       }
    }
}