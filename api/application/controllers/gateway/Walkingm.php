<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Walkingm extends Admin_Controller {

    var $setting;
    var $payment_method;

    public function __construct() {
        parent::__construct();
         $this->load->library('Walkingm_lib');
        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
    }

    public function index() {
        $data = array();
        $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = "";
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
        $this->load->view('payment/walkingm/index', $data);
    }

    public function pay() {

        
        $this->form_validation->set_rules('email', ('Email'), 'trim|required');
        $this->form_validation->set_rules('password', ('Password'), 'trim|required');
         $session_params = $this->session->userdata('params');
        if ($this->form_validation->run() == false) {
            $data = array();
        $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
            $this->load->view('payment/walkingm/index', $data);
        } else {
          $amount= number_format((float)(convertBaseAmountCurrencyFormat($session_params['payment_detail']->fine_amount+$session_params['total'])), 2, '.', '');
          $payment_array['payer']="Walkingm";
          $payment_array['amount']=$amount;
          $payment_array['currency']=$this->setting[0]['currency'];
          $payment_array['successUrl']=base_url()."gateway/walkingm/success";
          $payment_array['cancelUrl']=base_url()."gateway/walkingm/cancel";
          $responce= $this->walkingm_lib->walkingm_login($_POST['email'],$_POST['password'],$payment_array);

          if($responce!=""){
           
            $data = array();
            $data['params'] = $this->session->userdata('params');
            $data['setting'] = $this->setting;
            $data['api_error'] = $responce;
            $this->load->view('payment/walkingm/index', $data);
          }
           

            
            } 
        }
    

    public function success() {
      $responce= base64_decode($_SERVER["QUERY_STRING"]);
        $payment_responce=json_decode($responce);
        
        if ($responce != '' && $payment_responce->status=200) {
                    $transaction_id = $payment_responce->transaction_id;
            if ($transaction_id) {
                $params = $this->session->userdata('params');
                $ref_id = $transaction_id;
                $json_array = array(
                    'amount' => $params['total'],
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0,
                    'amount_fine' => $params['payment_detail']->fine_amount,
                    'received_by' => '',
                    'description' => "Online fees deposit through Walkingm TXN ID: " . $ref_id,
                    'payment_mode' => 'Walkingm',
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

      public function cancel() {
        redirect(base_url("payment/paymentfailed"));
      }

}
