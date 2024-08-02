<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Skrill extends Admin_Controller {

    var $setting;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
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
        $this->load->view('course_payment/skrill/index', $data);
    }

    public function pay() {

        $this->form_validation->set_rules('phone', ('Phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', ('Email'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array();
            $data['params'] = $this->session->userdata('course_amount');
            $data['setting'] = $this->setting;
            $data['api_error'] = $data['api_error'] = array();
              $this->load->view('course_payment/skrill/index', $data);
        } else {

            $params = $this->session->userdata('course_amount');
            $data['params']=$params;
            
            $student_id = $params['student_id'];
            $data['total'] =convertBaseAmountCurrencyFormat($data['params']['total_amount']);;
          //  $data['symbol'] = $params['invoice']->symbol;
            $data['currency_name'] = $params['currency_name'];
            $data['name'] = $params['name'];
           // $data['guardian_phone'] = $params['guardian_phone'];
           
            $payment_data['pay_to_email'] =$this->pay_method->api_email;
            $payment_data['transaction_id'] ='A'.time();
            $payment_data['return_url'] =base_url().'course_payment/skrill/success';
            $payment_data['cancel_url'] =base_url().'course_payment/skrill/cancel';
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
            $payment_data['amount'] =$data['total'];
            $payment_data['currency'] =$data['currency_name'];
            $payment_data['firstname'] =$params['name'];
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
            $data['params']['transaction_id']=$payment_data['transaction_id'];
            $this->session->set_userdata("params", $data['params']);
            $ins_data=array(
                'unique_id'=>$payment_data['transaction_id'],
                'parameter_details'=>json_encode($payment_data),
                'gateway_name'=>'skrill',
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
                'transaction_id' => $payment_data['transaction_id'],
                'note' => "Online course fees deposit through Skrill Txn ID: " . $payment_data['transaction_id'],
                'payment_mode' => 'Skrill',
            );
            $this->course_model->addprocessingpayment($payment_data);
           
            $this->load->view('course_payment/skrill/pay', $data);
            
        }
    }


    public function success() {

        $params = $this->session->userdata('course_amount');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($params['transaction_id'],'skrill');
        $payment_id = $params['transaction_id'];
        
            if($parameter_data['payment_status']=='success'){
            
            if($params['courseid'] !='') {

                $payment_data = array(
                    'date' => date('Y-m-d'),
                    'student_id' => $params['student_id'],
                    'guest_id' => $params['guest_id'],
                    'online_courses_id' => $params['courseid'],
                    'course_name' => $params['course_name'],
                    'actual_price' => $params['actual_amount'],
                    'paid_amount' => $params['total_amount'],
                    'payment_type' => 'Online',
                    'transaction_id' => $payment_id,
                    'note' => "Online course fees deposit through Skrill Txn ID: " . $payment_id,
                    'payment_mode' => 'Skrill',
                );
                $this->course_model->add($payment_data);
            
                $this->load->view('course_payment/paymentsuccess');
        
            }elseif($parameter_data['payment_status']=='CANCELLED'){
                $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
                redirect(base_url("course_payment/course_payment/paymentfailed"));
            }else{
                //redirect(base_url("user/gateway/payment/paymentprocessing"));
            }
        
        }
    }

    public function cancel() {
        $params = $this->session->userdata('course_amount');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($params['transaction_id'],'skrill');
        
        $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
        redirect(base_url("course_payment/course_payment/paymentfailed"));  
    }
}