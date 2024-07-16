<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payfast extends Admin_Controller {

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
        $this->load->view('payment/payfast/index', $data);
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
            $this->load->view('payment/payfast/index', $data);
        } else {

            

            $pay_method = $this->paymentsetting_model->getActiveMethod();
            if ($this->session->has_userdata('params')) {
                $session_params = $this->session->userdata('params');
                $data['session_params'] = $session_params;
            }
           
            $amount = ($data['session_params']['payment_detail']->fine_amount+$data['session_params']['total']);
            
           
            if ($pay_method->payment_type == "payfast") { 
                 $data = array(
            'merchant_id' => $pay_method->api_publishable_key,
            'merchant_key' => $pay_method->api_secret_key,
            'return_url' => base_url().'gateway/payfast/success',
            'cancel_url' => base_url().'gateway/payfast/cancel',
            'notify_url' => trim(base_url(),'/api').'/gateway_ins/payfast',
            'name_first' => $session_params['name'],
            'name_last'  => 'name_last',
            'email_address'=> $_POST['email'],
            'm_payment_id' => time().rand(99,999), //Unique payment ID to pass through to notify_url
            'amount' => number_format( sprintf( '%.2f', convertBaseAmountCurrencyFormat($amount)), 2, '.', '' ),
            'item_name' => 'fees#'.rand(99,999),
            );
           // echo "<pre>"; print_r($data);die;
            $signature = $this->generateSignature($data,$pay_method->salt);
            $data['signature'] = $signature;
           
            $session_params['transaction_id']=$data['m_payment_id'];
            
             $ins_data=array(
            'unique_id'=>$data['m_payment_id'],
            'parameter_details'=>json_encode($data),
            'gateway_name'=>'payfast',
            'module_type'=>'fees',
            'payment_status'=>'processing',
            );
            $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
            $bulk_fees=array();
         
            //foreach ($session_params['student_fees_master_array'] as $fee_key => $fee_value) {
           
            $json_array = array(
                    'amount' => $session_params['total'],
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0,
                    'amount_fine' => $session_params['payment_detail']->fine_amount,
                    'received_by' => '',
                    'description' => "Online fees deposit through Payfast TXN ID: " . $data['m_payment_id'],
                    'payment_mode' => 'Payfast',
                );

               
                if(($session_params['fee_category']=='transport') && !empty($session_params['student_transport_fee_id']) ){
                   
                     $insert_fee_data = array(
                    'gateway_ins_id'=>$gateway_ins_id,
                    'student_transport_fee_id' => $params['student_transport_fee_id'],
                    'amount_detail' => $json_array,
                );  
                }else{
                     $insert_fee_data = array(
                    'gateway_ins_id'=>$gateway_ins_id,
                    'student_fees_master_id' => $session_params['student_fees_master_id'],
                    'fee_groups_feetype_id' => $session_params['fee_groups_feetype_id'],
                    'amount_detail' => $json_array,
                );  
                }           
           $bulk_fees[]=$insert_fee_data;
            //========
           // } 

            $fee_processing=$this->gateway_ins_model->fee_processing($bulk_fees);
            $this->session->set_userdata("params", $session_params);
            // If in testing mode make use of either sandbox.payfast.co.za or www.payfast.co.za
            $testingMode = true;
            $pfHost = $testingMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
            $htmlForm = '<form action="https://'.$pfHost.'/eng/process" method="post" name="pay_now">';
            foreach($data as $name=> $value)
            {
            $htmlForm .= '<input name="'.$name.'" type="hidden" value=\''.$value.'\' />';
            }
            $htmlForm .= '</form>';
            $data['htmlForm']= $htmlForm;
             $this->load->view('payment/payfast/pay', $data);
            } else { 
                $this->session->set_flashdata('error', 'Oops! Something went wrong');
                $this->load->view('payment/error');
            }
        }
    }

 
    public  function generateSignature($data, $passPhrase = null) {
    // Create parameter string
    $pfOutput = '';
    foreach( $data as $key => $val ) {
        if($val !== '') {
            $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
        }
    } 
    // Remove last ampersand
    $getString = substr( $pfOutput, 0, -1 );
    if( $passPhrase !== null ) {
        $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
    }
    return md5( $getString );
} 
    public function success()
    {
              
            $params = $this->session->userdata('params');
            $parameter_data=$this->gateway_ins_model->get_gateway_ins($params['transaction_id'],'payfast');
            if($parameter_data['payment_status']=='success'){
                $this->load->view('payment/invoice');
            }elseif($parameter_data['payment_status']=='CANCELLED'){
                $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
                 $this->load->view('payment/paymentfailed');
            }else{ 
                 $this->load->view('payment/processing');
            } 
           

      
    }

    public function cancel(){
        $params = $this->session->userdata('params');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($params['payfast_payment_id'],'payfast');
        $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
        redirect(base_url("payment/paymentfailed"));
    }

}
