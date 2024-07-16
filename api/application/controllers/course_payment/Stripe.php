<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'third_party/stripe/init.php';

class Stripe extends Admin_Controller {

    var $setting;
    var $payment_method;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
        $this->load->model('course_model');
      
         $this->load->library('notification_lib');
    }
 
    /*
    This is used to show payment detail page with payment gateway functionality and success reposnse status
    */
    public function index() {
        $error= array();
        $data = array();
        $pay_method = $this->paymentsetting_model->getActiveMethod();

        if ($pay_method->payment_type == "stripe") {
            $data = array();

            if ($this->session->has_userdata('course_amount')) {
                if ($pay_method->api_secret_key != "" && $pay_method->api_publishable_key != "") {
                    $session_params = $this->session->userdata('course_amount');
                    $total= convertBaseAmountCurrencyFormat($session_params['total_amount']);
                    $data['setting'] = $this->setting;
                    $data['session_params'] = $session_params;
                     
                    $params = array(
                        "testmode" => "on",
                        "private_live_key" => "sk_live_xxxxxxxxxxxxxxxxxxxxx",
                        "public_live_key" => "pk_live_xxxxxxxxxxxxxxxxxxxxx",
                        "private_test_key" => "sk_test_YLQh86Az2IdcuqfQQOx47yam",
                        "public_test_key" => "pk_test_nYHEZ1mJ8FpaoXV4KVxQs7qR",
                    );

                    if ($params['testmode'] == "on") {
                        \Stripe\Stripe::setApiKey($params['private_test_key']);
                        $pubkey = $params['public_test_key'];
                    } else {
                        \Stripe\Stripe::setApiKey($params['private_live_key']);
                        $pubkey = $params['public_live_key'];
                    }

                    if ($this->input->server('REQUEST_METHOD') == 'POST') {

                        if (isset($_POST['stripeToken'])) {

                            $invoiceid = abs(crc32(uniqid())); // Invoice ID
                            $description = "Fees Payment";
                           
                            try {

                                $response = \Stripe\Charge::create(array(
                                            'amount' => ($total) * 100,
                                            "currency" => $data['session_params']['currency_name'],
                                            "source" => $_POST['stripeToken'],
                                            "description" => $description,
                                                )
                                );
                                
                                // Payment has succeeded, no exceptions were thrown or otherwise caught

                                $result = "success";
                                if ($response->status == 'succeeded') {
                                    $transactionid = $response->balance_transaction;
                                    $params = $this->session->userdata('course_amount');
                                    $payment_data['transactionid'] = $transactionid;
                                    $payment_data = array(
                                        'date' => date('Y-m-d'),
                                        'student_id' => $params['student_id'],
                                        'online_courses_id' => $params['courseid'],
                                        'course_name' => $params['course_name'],
                                        'actual_price' => $params['actual_amount'],
                                        'paid_amount' => $params['total_amount'],
                                        'payment_type' => 'Online',
                                        'transaction_id' => $transactionid,
                                        'note' => "Online course fees deposit through Stripe Txn ID: " . $transactionid,
                                        'payment_mode' => 'Stripe',
                                    );

                                    $this->course_model->add($payment_data);
                                    
                                   
                                }
                            } catch (\Stripe\Error\Card $e) {
                                // Since it's a decline, \Stripe\Error\Card will be caught
                                $body = $e->getJsonBody();
                                $err = $body['error'];
                                $error[] = $err['message'];
                            } catch (\Stripe\Error\RateLimit $e) {
                                // Too many requests made to the API too quickly
                                $error[] = $e->getMessage();
                            } catch (\Stripe\Error\InvalidRequest $e) {
                                // Invalid parameters were supplied to Stripe's API
                                $error[] = $e->getMessage();
                            } catch (\Stripe\Error\Authentication $e) {
                                // Authentication with Stripe's API failed
                                // (maybe you changed API keys recently)
                                $error[] = $e->getMessage();
                            } catch (\Stripe\Error\ApiConnection $e) {
                                // Network communication with Stripe failed
                                $error[] = $e->getMessage();
                            } catch (\Stripe\Error\Base $e) {
                                // Display a very generic error to the user, and maybe send
                                // yourself an email
                                $error[] = $e->getMessage();
                            } catch (Exception $e) {
                                // Something else happened, completely unrelated to Stripe
                                $error[] = $e->getMessage();
                            }
                            $this->session->set_flashdata('error', $error);
                          
                            if(empty($error)){
                                 redirect(base_url("course_payment/stripe/success"));
                            }else{
                                
                            }
                            
                        }
                    }
                    $data['params'] = $params;
                    if(empty($error)){
                     $data['error']=array();
                 }else{
                    $data['error']=$error;

                 } 
                  $this->load->view('course_payment/stripe/pay', $data);  
                }
            }
        } else {
            $this->session->set_flashdata('error', 'Oops! Something went wrong');
            $this->load->view('course_payment/course_payment/paymentfailed');
        }
    }

    function success(){
         $this->load->view('course_payment/paymentsuccess');
    }
}