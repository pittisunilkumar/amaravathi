<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Skrill extends Admin_Controller {

    var $setting;
    var $payment_method;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
          $this->load->model('gateway_ins_model');
    }

    public function index() {
        $data = array();
        $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']); 
        $this->load->view('payment/skrill/index', $data);
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
            $this->load->view('payment/skrill/index', $data);
        } else {

           

            $pay_method = $this->paymentsetting_model->getActiveMethod();
            if ($this->session->has_userdata('params')) {
                $session_params = $this->session->userdata('params');
                $data['session_params'] = $session_params;
            }
            //echo "<pre>"; print_r($session_params);die;
           $payment_data['pay_to_email'] =$pay_method->api_email;
            $payment_data['transaction_id'] ='A'.time();
            $payment_data['return_url'] =base_url().'gateway/skrill/success';
            $payment_data['cancel_url'] =base_url().'gateway/skrill/cancel';
            $payment_data['status_url'] =trim(base_url(),'/api').'/gateway_ins/skrill';
            $payment_data['language'] ='EN';
            $payment_data['merchant_fields'] ='customer_number,session_id';
            $payment_data['customer_number'] ='C'.time();
            $payment_data['session_ID'] ='A3D'.time();;
            $payment_data['pay_from_email'] =$_POST['email'];
            $payment_data['amount2_description'] ='';
            $payment_data['amount2'] ='';
            $payment_data['amount3_description'] ='';
            $payment_data['amount3'] ='';
            $payment_data['amount4_description'] ='';
            $payment_data['amount4'] ='';
            $payment_data['amount'] =convertBaseAmountCurrencyFormat($session_params['payment_detail']->fine_amount+$session_params['total']);
            $payment_data['currency'] =$session_params['invoice']->currency_name;
            $payment_data['firstname'] =$session_params['name'];
            $payment_data['lastname'] ='';
            $payment_data['address'] ='';
            $payment_data['postal_code'] ='';
            $payment_data['city'] ='';
            $payment_data['country'] ='';
            $payment_data['detail1_description'] ='';
            $payment_data['detail1_text'] ='';
            $payment_data['detail2_description'] ='';
            $payment_data['detail2_text'] ='';
            $payment_data['detail3_description'] ='';
            $payment_data['detail3_text'] ='';
            
            $data['form_fields']=$payment_data;
             $session_params['transaction_id']=$payment_data['transaction_id'];

            $ins_data=array(
            'unique_id'=>$payment_data['transaction_id'],
            'parameter_details'=>json_encode($payment_data),
            'gateway_name'=>'skrill',
            'module_type'=>'fees',
            'payment_status'=>'processing',
            );
            $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
            $bulk_fees=array();
         
           $json_array = array(
                    'amount' => convertBaseAmountCurrencyFormat($session_params['payment_detail']->fine_amount+$session_params['total']),
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0,
                    'amount_fine' => $session_params['payment_detail']->fine_amount,
                    'received_by' => '',
                    'description' => "Online fees deposit through Skrill TXN ID: " . $payment_data['transaction_id'],
                    'payment_mode' => 'Skrill',
                );
           if(($session_params['fee_category']=='transport') && !empty($session_params['student_transport_fee_id']) ){
              $insert_fee_data = array(
                    'fee_category'=>'transport',
                    'gateway_ins_id'=>$gateway_ins_id,
                    'student_transport_fee_id' => $session_params['student_transport_fee_id'],
                    'student_fees_master_id' => null,
                    'fee_groups_feetype_id' => null,
                    'amount_detail' => $json_array,
                );               
           $bulk_fees[]=$insert_fee_data;
           }else{ 
  $insert_fee_data = array(
                    'fee_category'=>'fees',
                    'student_transport_fee_id' => null,
                    'gateway_ins_id'=>$gateway_ins_id,
                    'student_fees_master_id' => $session_params['student_fees_master_id'],
                    'fee_groups_feetype_id' => $session_params['fee_groups_feetype_id'],
                    'amount_detail' => $json_array,
                );               
           $bulk_fees[]=$insert_fee_data;
           }
              

            $fee_processing=$this->gateway_ins_model->fee_processing($bulk_fees);

           
                 $this->load->view('payment/skrill/pay', $data);
                
                }                
             
           

            
        }
    

    public function success() {
      
             $params = $this->session->userdata('params');
            $parameter_data=$this->gateway_ins_model->get_gateway_ins($_GET['transaction_id'],'skrill');
            if($parameter_data['payment_status']=='2'){
                  $this->load->view('payment/invoice');
            }elseif(($parameter_data['payment_status']=='-1') || ($parameter_data['payment_status']=='-2')){
                $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
                $this->load->view('payment/paymentfailed');
            }else{
                 $this->load->view('payment/processing');
            }
    }

    public function cancel(){
        $this->load->view('payment/paymentfailed');
    }

}
