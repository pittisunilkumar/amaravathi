<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Walkingm extends Admin_Controller {

    var $setting;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->api_config = $this->paymentsetting_model->getActiveMethod();
        $this->load->model('course_model');
        $this->load->model('gateway_ins_model');
        $this->load->library('walkingm_lib');
    }
 
    /*
    This is used to show payment detail page
    */
    public function index() {
        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $this->load->view('course_payment/walkingm/index', $data);
    }

    public function pay() {

        $params = $this->session->userdata('course_amount');

        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $this->form_validation->set_rules('email', $this->lang->line('walkingm_email'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('walkingm_password'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
         $data['api_error'] = array();
        $this->load->view('course_payment/walkingm/index', $data);
        } else {

          $amount = convertBaseAmountCurrencyFormat($params['total_amount']);
          $payment_array['payer']="Walkingm"; 
          $payment_array['amount']=$amount;
          $payment_array['currency']=$params['currency_name'];
          $payment_array['successUrl']=base_url()."course_payment/walkingm/success";
          $payment_array['cancelUrl']=base_url()."course_payment/walkingm/cancel";
          $responce= $this->walkingm_lib->walkingm_login($_POST['email'],$_POST['password'],$payment_array);

          if($responce!=""){
            $data['api_error'] = $responce;
            $this->load->view('course_payment/walkingm/index', $data);
          }

            }
        }

    public function success() {
        $responce= base64_decode($_SERVER["QUERY_STRING"]);
        $payment_responce=json_decode($responce);
        $params = $this->session->userdata('course_amount');
        if ($responce != '' && $payment_responce->status=200) {
              $payment_id = $payment_responce->transaction_id; 
              $payment_data = array(
                'date' => date('Y-m-d'),
                'student_id' => $params['student_id'],
                'online_courses_id' => $params['courseid'],
                'course_name' => $params['course_name'],
                'actual_price' => $params['actual_amount'],
                'paid_amount' => $params['total_amount'],
                'payment_type' => 'Online',
                'transaction_id' => $payment_id,
                'note' => "Online course fees deposit through Walkingm Txn ID: " . $payment_id,
                'payment_mode' => 'Walkingm',
            );
            $this->course_model->add($payment_data);

            $this->load->view('course_payment/paymentsuccess');
              
            } else {
                redirect(base_url("course_payment/course_payment/paymentfailed"));
            }
    }

    public function cancel($responce){
      redirect(base_url("course_payment/course_payment/paymentfailed"));
    }
}