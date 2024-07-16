<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Paytm extends Admin_Controller {

    var $setting;
    var $payment_method;
    var $api_config;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
        $this->api_config = $this->paymentsetting_model->getActiveMethod();
        $this->load->library('Paytm_lib');
    }

    public function index() {

        $data = array();
        $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $params = $this->session->userdata('params');
        $total=number_format((float)($params['payment_detail']->fine_amount+$params['total']), 2, '.', '');
        $student_fees_master_id = $params['student_fees_master_id'];
        $fee_groups_feetype_id = $params['fee_groups_feetype_id'];
        $student_id = $params['student_id'];
        $data['student_fees_master_id'] = $student_fees_master_id;
        $data['fee_groups_feetype_id'] = $fee_groups_feetype_id;
        $data['student_id'] = $student_id;
        $data['total'] = $total;
        $data['symbol'] = $params['invoice']->symbol;
        $data['currency_name'] = $params['invoice']->currency_name;
        $data['name'] = $params['name'];
        $data['guardian_phone'] = $params['guardian_phone'];
        $posted = $_POST;
        $paytmParams = array();
        $ORDER_ID = time();
        $CUST_ID = time();

        $paytmParams = array(
            "MID" => $this->api_config->api_publishable_key,
            "WEBSITE" => $this->api_config->paytm_website,
            "INDUSTRY_TYPE_ID" => $this->api_config->paytm_industrytype,
            "CHANNEL_ID" => "WEB",
            "ORDER_ID" => $ORDER_ID,
            "CUST_ID" => $data['student_id'],
            "TXN_AMOUNT" => convertBaseAmountCurrencyFormat($data['total']),
            "CALLBACK_URL" => base_url() . "gateway/Paytm/paytm_response",
        );

        $paytmChecksum = $this->paytm_lib->getChecksumFromArray($paytmParams, $this->api_config->api_secret_key);
        $paytmParams["CHECKSUMHASH"] = $paytmChecksum;

         //$transactionURL              = 'https://securegw-stage.paytm.in/order/process';
         $transactionURL              = 'https://securegw.paytm.in/order/process';



        $data['paytmParams'] = $paytmParams;
        $data['transactionURL'] = $transactionURL;

        $this->load->view('payment/paytm/index', $data);
    }

 

    public function paytm_response() {

        $paytmChecksum = "";
        $paramList = array();
        $isValidChecksum = "FALSE";

        $paramList = $_POST;
       
        $paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : "";



        $isValidChecksum = $this->paytm_lib->verifychecksum_e($paramList, $this->api_config->api_secret_key, $paytmChecksum);


        if ($isValidChecksum == "TRUE") {




            if ($_POST["STATUS"] == "TXN_SUCCESS") {

                $params = $this->session->userdata('params');
                $total=number_format((float)($params['payment_detail']->fine_amount+$params['total']), 2, '.', '');
                $ref_id = $_POST['TXNID'];
                $json_array = array(
                    'amount' =>$params['total'],
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0,
                    'amount_fine' => $params['payment_detail']->fine_amount,
                    'description' => "Online fees deposit through Paytm Txn ID: " . $ref_id,
                    'received_by' => '',
                    'payment_mode' => 'Paytm',
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
                $inserted_id = $this->studentfeemaster_model->fee_deposit($data, $send_to, '');
                $invoice_detail = json_decode($inserted_id);
                redirect(base_url("payment/successinvoice/" . $invoice_detail->invoice_id . "/" . $invoice_detail->sub_invoice_id));
            } else {
                redirect(base_url("payment/paymentfailed"));
            }
        } else {
            redirect(base_url("payment/paymentfailed"));
        }
    }

}
