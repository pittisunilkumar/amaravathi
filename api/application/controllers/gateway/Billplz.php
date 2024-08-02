<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Billplz extends Admin_Controller {

    var $setting;
    var $payment_method;
 
    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
        $this->api_config=$this->paymentsetting_model->getActiveMethod();
        $this->load->library('billplz_lib');
    }

    public function index() {
        $data = array();
        
        $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
        $this->load->view('payment/billplz/index', $data);
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
            $this->load->view('payment/billplz/index', $data);
        } else {

            $pay_method = $this->paymentsetting_model->getActiveMethod();
            if ($this->session->has_userdata('params')) {
                $session_params = $this->session->userdata('params');
                $data['session_params'] = $session_params;
            }

            $amount = number_format((float)(convertBaseAmountCurrencyFormat($data['session_params']['payment_detail']->fine_amount+$data['session_params']['total'])), 2, '.', '');
            $secret_key = $pay_method->api_secret_key;
            $public_key = $pay_method->api_publishable_key;
            $data['currency_code'] = $session_params['invoice']->currency_name;
           
            $parameter = array(
            'title' => $data['session_params']['name'],
            'description' => 'Student Fee',
            'amount' => $amount*100,
             ); 
  
            $optional = array(
                'fixed_amount' => 'true',
                'fixed_quantity' => 'true',
                'payment_button' => 'pay',
                'redirect_uri'=>base_url() . 'gateway/billplz/success',
                'photo' => '',
                'split_header' => false,
                'split_payments' => array(
                ['split_payments[][email]' => $this->api_config->api_email],
                ['split_payments[][fixed_cut]' => '0'],
                ['split_payments[][variable_cut]' => ''],
                ['split_payments[][stack_order]' => '0'],
            )
            );
        $api_key=$this->api_config->api_secret_key;
        $this->billplz_lib->payment($parameter,$optional,$api_key);
            

           
        }
    }
 
     
    public function success() {

      if ($_GET['billplz']['paid']=='true') {
            $purchaseId = $_GET['billplz']['id'];
            if ($purchaseId) {
                $params = $this->session->userdata('params');
                $ref_id = $purchaseId;
                $json_array = array(
                    'amount' => $params['total'],
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0,
                    'amount_fine' => $params['payment_detail']->fine_amount,
                    'received_by' => '',
                    'description' => "Online fees deposit through Billplz TXN ID: " . $ref_id,
                    'payment_mode' => 'Billplz',
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
