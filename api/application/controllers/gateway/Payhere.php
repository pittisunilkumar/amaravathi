<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payhere extends Admin_Controller {

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

        $this->load->view('payment/payhere/index', $data);
    }

       public function pay() {
       $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
        $this->form_validation->set_rules('phone', ('Phone'), 'trim|required');
        $this->form_validation->set_rules('email', ('Email'), 'trim|required');
       
        if ($this->form_validation->run() == false) {
           
            
            $data['setting'] = $this->setting;
            $data['api_error'] = array();
            $this->load->view('payment/payhere/index', $data);
        } else {

           

            $pay_method = $this->paymentsetting_model->getActiveMethod();
            if ($this->session->has_userdata('params')) {
                $session_params = $this->session->userdata('params');
                $data['session_params'] = $session_params;
            }
            $amount = number_format((float)(convertBaseAmountCurrencyFormat($data['session_params']['payment_detail']->fine_amount+$data['session_params']['total'])), 2, '.', '');
             $htmlform=array(
                'merchant_id'=>$pay_method->api_publishable_key,
                'return_url'=>base_url().'gateway/payhere/success',
                'cancel_url'=>base_url().'gateway/payhere/cancel',
                'notify_url'=>trim(base_url(),'/api').'/gateway_ins/payhere',
                'order_id'=>time().rand(99,999),
                'items'=>'Student Fees',
                'currency'=>$session_params['invoice']->currency_name,
                'amount'=>$amount,
                'first_name'=>$session_params['name'],
                'last_name'=>'',
                'email'=>$_POST['email'],
                'phone'=>$_POST['phone'],
                'address'=>'',
                'city'=>'',
                'country'=>''
            );

            $data['htmlform']=$htmlform;
            $session_params['transaction_id']=$htmlform['order_id'];
            $this->session->set_userdata("params", $session_params);

            $ins_data=array(
            'unique_id'=>$htmlform['order_id'],
            'parameter_details'=>json_encode($htmlform),
            'gateway_name'=>'payhere',
            'module_type'=>'fees',
            'payment_status'=>'processing',
            );
            $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
            $bulk_fees=array();
         
           $json_array = array(
                    'amount' => $session_params['total'],
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0,
                    'amount_fine' => $session_params['payment_detail']->fine_amount,
                    'received_by' => '',
                    'description' => "Online fees deposit through Payhere TXN ID: " . $htmlform['order_id'],
                    'payment_mode' => 'Payhere',
                );

               
                 if(($session_params['fee_category']=='transport') && !empty($session_params['student_transport_fee_id']) ){
                   
                     $insert_fee_data = array(
                    'gateway_ins_id'=>$gateway_ins_id,
                    'student_transport_fee_id' => $params['student_transport_fee_id'],
                    'amount_detail' => $json_array,
                    'fee_category'=>'transport',
                );  
                }else{
                     $insert_fee_data = array(
                    'gateway_ins_id'=>$gateway_ins_id,
                    'student_fees_master_id' => $session_params['student_fees_master_id'],
                    'fee_groups_feetype_id' => $session_params['fee_groups_feetype_id'],
                    'amount_detail' => $json_array,
                    'fee_category'=>'fees',
                );  
                }                
           $bulk_fees[]=$insert_fee_data;

            $fee_processing=$this->gateway_ins_model->fee_processing($bulk_fees);

              
                 $this->load->view('payment/payhere/pay', $data);
                
                }                
             
          

            
        }
    
 public function success(){
        
         $params = $this->session->userdata('params');
            $parameter_data=$this->gateway_ins_model->get_gateway_ins($params['transaction_id'],'payhere');
            if($parameter_data['payment_status']=='2'){
                  $this->load->view('payment/invoice');
            }elseif($parameter_data['payment_status']=='-2'){
                $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
               $this->load->view('payment/paymentfailed');
            }elseif($parameter_data['payment_status']=='0'){
               $this->load->view('payment/processing');
            }else{
                $this->load->view('payment/paymentfailed');
            }
    }

    public function cancel(){
         $this->load->view('payment/paymentfailed');
    }

}
