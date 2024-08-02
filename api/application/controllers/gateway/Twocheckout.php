<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Twocheckout extends Admin_Controller {

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
        $this->load->view('payment/twocheckout/index', $data);
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
            $this->load->view('payment/twocheckout/index', $data);
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

                $data = array(
                    'student_fees_master_id' => $params['student_fees_master_id'],
                    'fee_groups_feetype_id' => $params['fee_groups_feetype_id'],
                    'amount_detail' => $json_array,
                );

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
