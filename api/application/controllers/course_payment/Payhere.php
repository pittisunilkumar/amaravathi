<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payhere extends Admin_Controller {

    var $setting;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->api_config = $this->paymentsetting_model->getActiveMethod();
        $this->load->model('course_model');
        $this->load->model('gateway_ins_model');
      
        $this->load->library('notification_lib');
    }
 
    /*
    This is used to show payment detail page
    */
    public function index() {
        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $this->load->view('course_payment/payhere/index', $data);
    }

    public function pay() {

        $data['params'] = $this->session->userdata('course_amount');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('course_payment/payhere/index', $data);
        } else {

            $data['name'] = $data['params']['name'];
            $amount =convertBaseAmountCurrencyFormat($data['params']['total_amount']); 
            $htmlform=array(
                'merchant_id'=>$this->api_config->api_publishable_key,
                'return_url'=>base_url().'course_payment/payhere/success',
                'cancel_url'=>base_url().'course_payment/payhere/cancel',
                'notify_url'=>trim(base_url(),'/api').'/gateway_ins/payhere',
                'order_id'=>time().rand(99,999),
                'items'=>'online course Fees',
                'currency'=>$data['params']['currency_name'],
                'amount'=>$amount,
                'first_name'=>$data['name'],
                'last_name'=>'',
                'email'=>$_POST['email'],
                'phone'=>$_POST['phone'],
                'address'=>'',
                'city'=>'',
                'country'=>''
            );

            $data['htmlform']=$htmlform;
            $data['params']['transaction_id']=$htmlform['order_id'];
            $this->session->set_userdata("params", $data['params']);
            $ins_data=array(
                'unique_id'=>$htmlform['order_id'],
                'parameter_details'=>json_encode($htmlform),
                'gateway_name'=>'payhere',
                'module_type'=>'online_course',
                'payment_status'=>'processing',
            );
            $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
           
            $payment_data = array(
                'gateway_ins_id'=>$gateway_ins_id,
                'date' => date('Y-m-d'),
                'student_id' => $data['params']['student_id'],
                'online_courses_id' => $data['params']['courseid'],
                'course_name' => $data['params']['course_name'],
                'actual_price' => $data['params']['actual_amount'],
                'paid_amount' => $data['params']['total_amount'],
                'payment_type' => 'Online',
                'transaction_id' => $htmlform['order_id'],
                'note' => "Online course fees deposit through payhere Txn ID: " . $htmlform['order_id'],
                'payment_mode' => 'payhere',
            );
            $this->course_model->addprocessingpayment($payment_data);

            $this->load->view('course_payment/payhere/pay', $data);
            }  
    }

    public function success(){
        
        $params = $this->session->userdata('params');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($params['transaction_id'],'payhere');
        if($parameter_data['payment_status']=='2'){
             redirect(base_url("user/gateway/payment/successinvoice"));
        }elseif($parameter_data['payment_status']=='-2'){
            $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
            redirect(base_url("user/gateway/payment/paymentfailed"));
        }elseif($parameter_data['payment_status']=='0'){
            redirect(base_url("user/gateway/payment/paymentprocessing"));
        }else{
             redirect(base_url("user/gateway/payment/paymentfailed"));  
        }
    }

    public function cancel(){
        redirect(base_url("user/gateway/payment/paymentfailed"));  
    }
}