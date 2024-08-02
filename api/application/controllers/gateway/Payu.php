<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payu extends Admin_Controller {

    var $setting;
    var $payment_method;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
    }

    public function index() {

        $pay_method = $this->paymentsetting_model->getActiveMethod();

        if ($pay_method->payment_type == "payu") {
            $data = array();
            if ($this->session->has_userdata('params')) {
                if ($pay_method->api_secret_key != "" && $pay_method->salt != "") {

                    $data = array();
                    $session_params = $this->session->userdata('params');
                    $total=number_format((float)(convertBaseAmountCurrencyFormat($session_params['payment_detail']->fine_amount+$session_params['total'])), 2, '.', '');
                    $data['product_info'] = $session_params['payment_detail']->fee_group_name . " - " . $session_params['payment_detail']->code;
                    $data['session_params'] = $session_params;
                    // Merchant key here as provided by Payu
                    $data['MERCHANT_KEY'] = $session_params['key'];
                    // Merchant Salt as provided by Payu
                    $SALT = $session_params['salt'];
                    // End point - change to https://secure.payu.in for LIVE mode
                    $PAYU_BASE_URL = "https://secure.payu.in";
                    $data['action'] = '';
                    $data['surl'] = site_url('gateway/payu/success');
                    $data['furl'] = site_url('gateway/payu/success');

                    $posted = array();
                    if (!empty($_POST)) {

                        foreach ($_POST as $key => $value) {

                            $posted[$key] = $value;
                        }
                    }

                    $data['posted'] = $posted;
                    $data['formError'] = 0;
                    if (empty($posted['txnid'])) {
                        // Generate random transaction id
                        $data['txnid'] = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
                    } else {
                        $data['txnid'] = $posted['txnid'];
                    }
                    $session_params['txn_id'] = $data['txnid'];
                    $this->session->set_userdata("params", $session_params);
                    $data['hash'] = '';
// Hash Sequence
                    $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
                    if (empty($posted['hash']) && sizeof($posted) > 0) {

                        if (
                                empty($posted['key']) || empty($posted['txnid']) || empty($posted['amount']) || empty($posted['firstname']) || empty($posted['email']) || empty($posted['phone']) || empty($posted['productinfo']) || empty($posted['surl']) || empty($posted['furl']) || empty($posted['service_provider'])
                        ) {
                            $formError = 1;
                        } else {

                            $hashVarsSeq = explode('|', $hashSequence);
                            $hash_string = '';
                            foreach ($hashVarsSeq as $hash_var) {
                                $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
                                $hash_string .= '|';
                            }

                            $hash_string .= $SALT;

                            $data['hash'] = strtolower(hash('sha512', $hash_string));
                            $data['action'] = $PAYU_BASE_URL . '/_payment';
                        }
                    } elseif (!empty($posted['hash'])) {
                        $data['hash'] = $posted['hash'];
                        $data['action'] = $PAYU_BASE_URL . '/_payment';
                    }

                    $this->load->view('payment/payu/index', $data);
                }
            }
        } else {
            $this->session->set_flashdata('error', 'Oops! Something went wrong');
            $this->load->view('payment/error');
        }
    }

    public function success() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $session_data = $this->session->userdata('params');

            if ($this->input->post('status') == "success") {
                $mihpayid = $this->input->post('mihpayid');
                $transactionid = $this->input->post('txnid');
                $txn_id = $session_data['txn_id'];

                if ($txn_id == $transactionid) {
                    $params = $this->session->userdata('params');
                    $json_array = array(
                        'amount' => $params['total'],
                        'date' => date('Y-m-d'),
                        'amount_discount' => 0,
                        'amount_fine' => $params['payment_detail']->fine_amount,
                        'received_by' => '',
                        'description' => "Online fees deposit through PayU TXN ID: " . $txn_id . " PayU Ref ID: " . $mihpayid,
                        'payment_mode' => 'PayU',
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

                    if ($inserted_id) {
                        $invoice_detail = json_decode($inserted_id);
                        redirect("payment/successinvoice/" . $invoice_detail->invoice_id . "/" . $invoice_detail->sub_invoice_id, "refresh");
                    } else {
                        
                    }
                } else {
                    redirect('payment/paymentfailed', 'refresh');
                }
            } else {

                redirect('payment/paymentfailed', 'refresh');
            }
        }
    }

}
