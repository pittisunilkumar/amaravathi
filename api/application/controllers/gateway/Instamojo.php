<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Instamojo extends Admin_Controller {

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

        $this->load->view('payment/instamojo/index', $data);
    }

    public function insta_pay() {
$data = array();
            $data['params'] = $this->session->userdata('params');
            $data['setting'] = $this->setting;
            $data['api_error'] = array();
        $this->form_validation->set_rules('phone', ('Phone'), 'trim|required');
        $this->form_validation->set_rules('email', ('Email'), 'trim|required');
$data['student_data'] = $this->student_model->get($data['params']['student_id']);
        if ($this->form_validation->run() == false) {
            
            $this->load->view('payment/instamojo/index', $data);
        } else {

            $instamojo = $this->paymentsetting_model->getActiveMethod();

            $pay_method = $this->paymentsetting_model->getActiveMethod();
            if ($this->session->has_userdata('params')) {
                $session_params = $this->session->userdata('params');
                $data['session_params'] = $session_params;
            }

            $amount = $data['session_params']['total'];
            $insta_apikey = $instamojo->api_secret_key;
            $insta_authtoken = $instamojo->api_publishable_key;

            if ($pay_method->payment_type == "instamojo") { 
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://test.instamojo.com/api/1.1/payment-requests/'); // for live https://www.instamojo.com
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Api-Key:$insta_apikey",
                    "X-Auth-Token:$insta_authtoken"));
                $payload = Array(
                    'purpose' => 'Student Fess',
                    'amount' => number_format((float)(convertBaseAmountCurrencyFormat($session_params['payment_detail']->fine_amount+$session_params['total'])), 2, '.', ''),
                    'phone' => $_POST['phone'],
                    'buyer_name' => $data['session_params']['name'],
                    'redirect_url' => base_url() . 'gateway/instamojo/success',
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
                    $data = array();
                    $data['params'] = $this->session->userdata('params');
                    $data['setting'] = $this->setting;
                    $json = json_decode($response, true);
                   
                    $data['api_error'] = $json['message'];
                    $data['student_data'] = $this->student_model->get($data['params']['student_id']);
                    $this->load->view('payment/instamojo/index', $data);
                }
            } else {
                $this->session->set_flashdata('error', 'Oops! Something went wrong');
                $this->load->view('payment/error');
            }
        }
    }

    public function success() {
        if ($_GET['payment_status'] == 'Credit') {
            $purchaseId = $_GET['payment_id'];
            if ($purchaseId) {
                $params = $this->session->userdata('params');
                $ref_id = $purchaseId;
                $json_array = array(
                    'amount' => $params['total'],
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0,
                    'amount_fine' => $params['payment_detail']->fine_amount,
                    'received_by' => '',
                    'description' => "Online fees deposit through Instamojo TXN ID: " . $ref_id,
                    'payment_mode' => 'Instamojo',
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
