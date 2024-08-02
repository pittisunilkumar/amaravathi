<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Toyyibpay extends Admin_Controller {

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
        $this->load->view('payment/toyyibpay/index', $data);
    }

    public function pay() {
$data = array();
            $data['params'] = $this->session->userdata('params');
            $data['setting'] = $this->setting;
            $data['api_error'] = array();
            $result="";
        $this->form_validation->set_rules('phone', ('Phone'), 'trim|required');
        $this->form_validation->set_rules('email', ('Email'), 'trim|required');
$data['student_data'] = $this->student_model->get($data['params']['student_id']);
        if ($this->form_validation->run() == false) {
            
            $this->load->view('payment/toyyibpay/index', $data);
        } else {

           

            $pay_method = $this->paymentsetting_model->getActiveMethod();
            if ($this->session->has_userdata('params')) {
                $session_params = $this->session->userdata('params');
                $data['session_params'] = $session_params;
            }
 
           // echo "<pre>"; print_r($session_params);die;
             $payment_data = array(
                'userSecretKey'=>$pay_method->api_secret_key,
                'categoryCode'=>$pay_method->api_signature,
                'billName'=>'Fees',
                'billDescription'=>'Student Fees',
                'billPriceSetting'=>1,
                'billPayorInfo'=>1,
                'billAmount'=>convertBaseAmountCurrencyFormat($session_params['payment_detail']->fine_amount+$session_params['total']),
                'billReturnUrl'=>base_url().'gateway/toyyibpay/success',
                'billCallbackUrl'=>trim(base_url(),'/api').'/gateway_ins/toyyibpay',
                'billExternalReferenceNo' => time().rand(99,999),
                'billTo'=>$session_params['name'],
                'billEmail'=>$_POST['email'],
                'billPhone'=>$_POST['phone'],
                'billSplitPayment'=>0,
                'billSplitPaymentArgs'=>'',
                'billPaymentChannel'=>'0',
                'billContentEmail'=>'Thank you for fees submission!',
                'billChargeToCustomer'=>1
              );  
           // echo "<pre>"; print_r($payment_data);die;
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
            $session_params['transaction_id']=$payment_data['billExternalReferenceNo'];
            $this->session->set_userdata("params", $session_params);
            $ins_data=array(
            'unique_id'=>$payment_data['billExternalReferenceNo'],
            'parameter_details'=>json_encode($payment_data),
            'gateway_name'=>'toyyibpay',
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
                    'description' => "Online fees deposit through Toyyibpay TXN ID: " . $payment_data['billExternalReferenceNo'],
                    'payment_mode' => 'Toyyibpay',
                );

                $insert_fee_data = array(
                    'fee_category'=>'fees',
                    'gateway_ins_id'=>$gateway_ins_id,
                    'student_fees_master_id' => $session_params['student_fees_master_id'],
                    'fee_groups_feetype_id' => $session_params['fee_groups_feetype_id'],
                    'amount_detail' => $json_array,
                );               
           $bulk_fees[]=$insert_fee_data;

            $fee_processing=$this->gateway_ins_model->fee_processing($bulk_fees);

                if((isset($obj->status) && $obj->status=='error')){
                    $result=$obj->msg;  
                    
                }else{
                  $url = "https://dev.toyyibpay.com/".$obj[0]->BillCode;
                    header("Location: $url");
                }
                 
                }
                }                
             
            $data['api_error'] = $result;

            
        }
    

    public function success() {
        $params = $this->session->userdata('params');
            $parameter_data=$this->gateway_ins_model->get_gateway_ins($params['transaction_id'],'toyyibpay');
            if($parameter_data['payment_status']=='1'){
                $this->load->view('payment/invoice');
            }elseif($parameter_data['payment_status']=='3'){
                $this->gateway_ins_model->deleteBygateway_ins_id($parameter_data['id']); 
                $this->load->view('payment/paymentfailed');
            }else{
                 $this->load->view('payment/processing');
            }
    }

}
