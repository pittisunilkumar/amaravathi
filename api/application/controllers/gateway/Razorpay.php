<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Razorpay extends Admin_Controller {

    var $setting;
    var $payment_method;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
    }

    public function index() {

        $razorpay = $this->paymentsetting_model->getActiveMethod();
        $pay_method = $this->paymentsetting_model->getActiveMethod();
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        if ($this->session->has_userdata('params')) {
            $session_params = $this->session->userdata('params');
        } 
        $total=number_format((float)($session_params['payment_detail']->fine_amount+$session_params['total']), 2, '.', '');
        $data['params'] = $session_params;
        $amount = $session_params['total'];
        $data['name'] = $session_params['name'];
        $data['merchant_order_id'] = time() . "01";
        $data['txnid'] = time() . "02";
        $data['title'] = 'Student Fee';
        $data['total'] = $total * 100;
        $data['amount'] = $total;
        $data['key_id'] = $pay_method->api_publishable_key;
        $data['currency_code'] = $session_params['invoice']->currency_name;
        $this->load->view('payment/razorpay/razorpay', $data);
    }

    public function callback() {


$params = $this->session->userdata('params');
     
        if (isset($_POST['razorpay_payment_id']) && $_POST['razorpay_payment_id'] != '') {
            
          
            $payment_id = $_POST['razorpay_payment_id'];
            $json_array = array(
                'amount' => $params['total'],
                'date' => date('Y-m-d'),
                'amount_discount' => 0,
                'amount_fine' => $params['payment_detail']->fine_amount,
                'description' => "Online fees deposit through Razorpay TXN ID: " . $payment_id,
                'received_by' => '',
                'payment_mode' => 'Razorpay',
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
        } else {
            redirect(base_url("payment/paymentfailed"));
        }
    }

}
