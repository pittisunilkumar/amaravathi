<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Twocheckout extends Admin_Controller {

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
        $this->load->view('course_payment/twocheckout/index', $data);
    }

    public function pay() {
        $this->form_validation->set_rules('phone', ('Phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', ('Email'), 'trim|required|xss_clean');

        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $data['api_error'] = $data['api_error'] = array();
        $result=array();
        if ($this->form_validation->run() == false) {
            $params = $this->session->userdata('course_amount');
            $data['params'] = $params;
            $this->load->view('course_payment/twocheckout/index', $data);
        } else { 
            redirect(base_url("course_payment/twocheckout/pay"));
            $params = $this->session->userdata('course_amount');

            $data['amount'] = $params['total_amount'];
            $data['currency']=$params['currency_name'];
            $data['api_config']=$this->api_config;
           
            $this->load->view('course_payment/twocheckout/pay', $data);
        }
    }

    public function success() {

        $params = $this->session->userdata('course_amount');
        $parameter_data=$this->gateway_ins_model->get_gateway_ins($params['transaction_id'],'toyyibpay');

        $payment_id = $params['transaction_id'];
        
            if($parameter_data['payment_status']=='success'){
            
                if(!empty($params['courseid'])) {

               $payment_data = array(
                    'date' => date('Y-m-d'),
                    'student_id' => $params['student_id'],
                    'online_courses_id' => $params['courseid'],
                    'course_name' => $params['course_name'],
                    'actual_price' => $params['actual_amount'],
                    'paid_amount' => $params['total_amount'],
                    'payment_type' => 'Online',
                    'transaction_id' => $payment_id,
                    'note' => "Online course fees deposit through Twocheckout Txn ID: " . $payment_id,
                    'payment_mode' => 'Twocheckout',
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
}