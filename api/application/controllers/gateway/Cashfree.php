<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cashfree extends Admin_Controller {

    var $setting;
    var $payment_method;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
    }

    public function index() {
        $data = array();
        $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
		
        $this->load->view('payment/cashfree/index', $data);
    }

    public function pay() {

        $this->form_validation->set_rules('phone', ('Phone'), 'trim|required');
        $this->form_validation->set_rules('email', ('Email'), 'trim|required');
        $data = array();
       
        if ($this->form_validation->run() == false) {
             $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
            $this->load->view('payment/cashfree/index', $data);
        } else {

            

            $pay_method = $this->paymentsetting_model->getActiveMethod();
            
            $params=$this->session->userdata('params');
            $data['name'] = $params['name'];
            $amount =convertBaseAmountCurrencyFormat(($params['payment_detail']->fine_amount+$params['total']));
            $api=' '.$pay_method->api_publishable_key;
            $customer_id="Student_id_".$params['student_id'];
            $order_id="order_".time().mt_rand(100,999);
            $currency=$params['invoice']->currency_name;;
            $redirectUrl=base_url()."gateway/cashfree/success?order_id={order_id}&order_token={order_token}";

            $my_array=array(
            "order_id"=> $order_id,
            "order_amount"=> $amount,
            "order_currency"=> $currency,
            "customer_details"=> array(
            "customer_id"=> $customer_id,
            "customer_name"=> $data['name'],
            "customer_email"=> $_POST['email'],
            "customer_phone"=> $_POST['phone'],
            ),
            "order_meta"=> array(
            "return_url"=> $redirectUrl,
            "notify_url"=> base_url() .'webhooks/cashfree',
            "payment_methods"=> ""
            )
        );
        $new_arrya=(object)$my_array;
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://sandbox.cashfree.com/pg/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($new_arrya));

            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'X-Api-Version: 2021-05-21';
            $headers[] = 'X-Client-Id: '.$pay_method->api_publishable_key;
            $headers[] = 'X-Client-Secret: '.$pay_method->api_secret_key;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            $json=json_decode($result);

            if (isset($json->order_status) && $json->order_status="ACTIVE") {
                $url = $json->payment_link;
               
                header("Location: $url");
            } else {
                
              
                 $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
       
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
                $data['api_error'] = $json->message;
               
            $this->load->view('payment/cashfree/index', $data);
            }
        }
    }

    public function success() {
        $pay_method = $this->paymentsetting_model->getActiveMethod();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://sandbox.cashfree.com/pg/orders/'.$_GET['order_id']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-Api-Version: 2021-05-21';
        $headers[] = 'X-Client-Id: '.$pay_method->api_publishable_key;
        $headers[] = 'X-Client-Secret: '.$pay_method->api_secret_key;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $payment_data=json_decode($result);
       if (isset($payment_data->order_status) && $payment_data->order_status=="PAID") {
                    $payment_id = $_GET['order_id']; 
                    $params = $this->session->userdata('params');
                $ref_id = $payment_id;
                $json_array = array(
                    'amount' => $params['total'],
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0,
                    'amount_fine' => $params['payment_detail']->fine_amount,
                    'received_by' => '',
                    'description' => "Online fees deposit through Cashfree TXN ID: " . $ref_id,
                    'payment_mode' => 'Cashfree',
                );

               if(($params['fee_category']=='transport') && !empty($params['student_transport_fee_id']) ){
                    $data = array(
                    'student_transport_fee_id' => $params['student_transport_fee_id'],
                    'amount_detail' => $json_array,
                );
                }else{
                    $data = array(
                    'student_fees_master_id' => $params['student_fees_master_id'],
                    'fee_groups_feetype_id' => $params['fee_groups_feetype_id'],
                    'amount_detail' => $json_array,
                );
                }

                $send_to = $params['guardian_phone'];
                $inserted_id = $this->studentfeemaster_model->fee_deposit($data, $send_to, "");
                $invoice_detail = json_decode($inserted_id);
                redirect("payment/successinvoice/" . $invoice_detail->invoice_id . "/" . $invoice_detail->sub_invoice_id, "refresh");

                } else {
                    redirect(base_url('payment/paymentfailed'));
                }
        
    }

}
