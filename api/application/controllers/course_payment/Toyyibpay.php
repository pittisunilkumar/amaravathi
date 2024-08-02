<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Toyyibpay extends Admin_Controller {

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
        $data['api_error']="";
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        $this->load->view('course_payment/toyyibpay/index', $data);
    }

    public function pay(){

        $data['params'] = $this->session->userdata('course_amount');

        $data['setting'] = $this->setting;
        $data['api_error'] = "";
        $result="";
        $this->form_validation->set_rules('phone', ('Phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('email', ('Email'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            
           
        } else {


            $data['name'] = $data['params']['name'];
            
            $amount =convertBaseAmountCurrencyFormat($data['params']['total_amount']); 
            $payment_data = array(
                'userSecretKey'=>$this->api_config->api_secret_key,
                'categoryCode'=>$this->api_config->api_signature,
                'billName'=>'Fees',
                'billDescription'=>'Online Course Fees',
                'billPriceSetting'=>1,
                'billPayorInfo'=>1,
                'billAmount'=>$amount,
                'billReturnUrl'=>base_url().'course_payment/toyyibpay/success',
                'billCallbackUrl'=>trim(base_url(),'/api').'/gateway_ins/toyyibpay',
                'billExternalReferenceNo' => time().rand(99,999),
                'billTo'=>$data['name'],
                'billEmail'=>$_POST['email'],
                'billPhone'=>$_POST['phone'],
                'billSplitPayment'=>0,
                'billSplitPaymentArgs'=>'',
                'billPaymentChannel'=>'0',
                'billContentEmail'=>'Thank you for fees submission!',
                'billChargeToCustomer'=>1
              );  

              $curl = curl_init();
              curl_setopt($curl, CURLOPT_POST, 1);
              curl_setopt($curl, CURLOPT_URL, 'https://dev.toyyibpay.com/index.php/api/createBill');  
              curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($curl, CURLOPT_POSTFIELDS, $payment_data);

              $result = curl_exec($curl);
              $info = curl_getinfo($curl);  
              curl_close($curl);
              $obj = json_decode($result);

            if (!empty($obj)) {
                $data['params']['transaction_id']=$payment_data['billExternalReferenceNo'];
                $this->session->set_userdata("params", $data['params']);
                $ins_data=array(
                    'unique_id'=>$payment_data['billExternalReferenceNo'],
                    'parameter_details'=>json_encode($payment_data),
                    'gateway_name'=>'toyyibpay',
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
                    'transaction_id' => $payment_data['billExternalReferenceNo'],
                    'note' => "Online course fees deposit through Toyyibpay Txn ID: " . $payment_data['billExternalReferenceNo'],
                    'payment_mode' => 'Toyyibpay',
                );
                $this->course_model->addprocessingpayment($payment_data);

                if((isset($obj->status) && $obj->status=='error')){
                    $result=$obj->msg;  
                    
                }else{
                  $url = "https://dev.toyyibpay.com/".$obj[0]->BillCode;
                    header("Location: $url");
                }
             
            }
        }                
             
            $data['api_error'] = $result;
        
        $this->load->view('course_payment/toyyibpay/index', $data);
    }

    public function success() {

        $params = $this->session->userdata('course_amount');
        $payment_id = $this->session->userdata('params')['transaction_id'];

        $parameter_data=$this->gateway_ins_model->get_gateway_ins($payment_id,'toyyibpay');
      

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
                    'note' => "Online course fees deposit through Toyyibpay Txn ID: " . $payment_id,
                    'payment_mode' => 'Toyyibpay',
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
}