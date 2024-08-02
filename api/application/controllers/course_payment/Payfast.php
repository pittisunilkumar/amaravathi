<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payfast extends Admin_Controller {

    var $setting;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->api_config = $this->paymentsetting_model->getActiveMethod();
        $this->load->model('course_model');
        $this->load->model('gateway_ins_model');
    }
 
    /*
    This is used to show payment detail page
    */
    public function index() {
        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $this->load->view('course_payment/payfast/index', $data);
    }

    public function pay() {
        
        $this->form_validation->set_rules('phone', ('Phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', ('Email'), 'trim|required|xss_clean');

        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;

        if ($this->form_validation->run() == false) {
            $this->load->view('course_payment/payfast/index', $data);
        } else { 
            
            $params = $this->session->userdata('course_amount');
            $amount = $params['total_amount'];
            $cartTotal = convertBaseAmountCurrencyFormat($params['total_amount']);// This amount needs to be sourced from your application
            $data = array(
            'merchant_id' => $this->api_config->api_publishable_key,
            'merchant_key' => $this->api_config->api_secret_key,
            'return_url' => base_url().'course_payment/payfast/success',
            'cancel_url' => base_url().'course_payment/payfast/cancel',
            'notify_url' => trim(base_url(),'/api').'/gateway_ins/payfast',
            'name_first' => $params['name'],
            'name_last'  => 'name_last',
            'email_address'=> $_POST['email'],
            'm_payment_id' => time().rand(99,999), //Unique payment ID to pass through to notify_url
            'amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),
            'item_name' => 'fees#'.rand(99,999),
            );
           
            $signature = $this->generateSignature($data,$this->api_config->salt);
            $data['signature'] = $signature;
           
            $params['transaction_id']=$data['m_payment_id'];
            
             $ins_data=array(
                'unique_id'=>$data['m_payment_id'],
                'parameter_details'=>json_encode($data),
                'gateway_name'=>'payfast',
                'module_type'=>'online_course',
                'payment_status'=>'processing',
            );

            $gateway_ins_id=$this->gateway_ins_model->add_gateway_ins($ins_data);
           
            $payment_data = array(
                'gateway_ins_id'=>$gateway_ins_id,
                'date' => date('Y-m-d'),
                'student_id' => $params['student_id'],
                'online_courses_id' => $params['courseid'],
                'course_name' => $params['course_name'],
                'actual_price' => $params['actual_amount'],
                'paid_amount' => $params['total_amount'],
                'payment_type' => 'Online',
                'transaction_id' => $params['transaction_id'],
                'note' => "Online course fees deposit through Payfast Txn ID: " . $params['transaction_id'],
                'payment_mode' => 'Payfast',
            );
             
            $this->course_model->addprocessingpayment($payment_data);
            $this->session->set_userdata("course_amount", $params);
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
            $this->load->view('course_payment/payfast/pay', $data);
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
    public function success() {

        $params = $this->session->userdata('course_amount');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($params['transaction_id'],'payfast');

        $payment_id = $params['transaction_id'];

        if($parameter_data['payment_status']=='success'){
            
            if($params['courseid'] !='') {

               $payment_data = array(
                    'date' => date('Y-m-d'),
                    'student_id' => $params['student_id'],
                    'online_courses_id' => $params['courseid'],
                    'course_name' => $params['course_name'],
                    'actual_price' => $params['actual_amount'],
                    'paid_amount' => $params['total_amount'],
                    'payment_type' => 'Online',
                    'transaction_id' => $payment_id,
                    'note' => "Online course fees deposit through Payfast Txn ID: " . $payment_id,
                    'payment_mode' => 'Payfast',
                );
                $this->course_model->add($payment_data);

               $this->load->view('course_payment/paymentsuccess');

            }elseif($parameter_data['payment_status']=='CANCELLED'){
                $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
                redirect(base_url("course_payment/course_payment/paymentfailed"));
            }else{
               redirect(base_url("course_payment/course_payment/paymentprocessing")); 
            }
        }elseif($parameter_data['payment_status']=='processing '){
           redirect(base_url("course_payment/course_payment/paymentprocessing")); 
        }else{
          redirect(base_url("course_payment/course_payment/paymentfailed"));  
        }
    }

    function cancel(){
         redirect(base_url("course_payment/course_payment/paymentfailed"));
    }
}