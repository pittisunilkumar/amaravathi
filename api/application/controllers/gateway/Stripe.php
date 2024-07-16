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
    }

    public function index() {
        $error= array();
        $data = array();
        $pay_method = $this->paymentsetting_model->getActiveMethod();
        if ($pay_method->payment_type == "stripe") {
            $data = array();
            if ($this->session->has_userdata('params')) {
                if ($pay_method->api_secret_key != "" && $pay_method->api_publishable_key != "") {
                    $session_params = $this->session->userdata('params');
                    $total=number_format((float)(convertBaseAmountCurrencyFormat($session_params['payment_detail']->fine_amount+$session_params['total'])), 2, '.', '');
                    $data['setting'] = $this->setting;
                    $data['session_params'] = $session_params;
                    $invoice_array = $session_params['invoice'];
                    $payment_detail_array = $session_params['payment_detail'];

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
                                            "currency" => $invoice_array->currency_name,
                                            "source" => $_POST['stripeToken'],
                                            "description" => $description,
                                                )
                                );

                                // Payment has succeeded, no exceptions were thrown or otherwise caught

                                $result = "success";
                                if ($response->status == 'succeeded') {
                                    $transactionid = $response->balance_transaction;
                                    $params = $this->session->userdata('params');
                                    $payment_data['transactionid'] = $transactionid;
                                    $json_array = array(
                                        'amount' => $session_params['total'],
                                        'date' => date('Y-m-d'),
                                        'amount_discount' => 0,
                                        'amount_fine' => $session_params['payment_detail']->fine_amount,
                                        'received_by' => '',
                                        'description' => "Online fees deposit through Stripe TXN ID: " . $transactionid,
                                        'payment_mode' => 'Stripe',
                                    );

                                    if(($params['fee_category']=='transport') && !empty($params['student_transport_fee_id']) ){
                    $data = array(
                    'student_transport_fee_id' => $params['student_transport_fee_id'],
                    'amount_detail' => $json_array,
                    'fee_category'=>'transport',
                );
                }else{
                    $data = array(
                    'student_fees_master_id' => $params['student_fees_master_id'],
                    'fee_groups_feetype_id' => $params['fee_groups_feetype_id'],
                    'amount_detail' => $json_array,
                    'fee_category'=>'fees',
                );
                }
                    
                                    $send_to = $params['guardian_phone'];
                                    $inserted_id = $this->studentfeemaster_model->fee_deposit($data, $send_to, "");

                                    if ($inserted_id) {
                                        $invoice_detail = json_decode($inserted_id);
                                        redirect("payment/successinvoice/" . $invoice_detail->invoice_id . "/" . $invoice_detail->sub_invoice_id, "refresh");
                                    } else {
                                        
                                    }
                                     
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
                            //redirect(site_url('payment/paymentfailed'));
                        }
                    }
                    $data['params'] = $params;
                   
                    $this->load->view('payment/stripe/pay', $data);
                }
            }
        } else {
            $this->session->set_flashdata('error', 'Oops! Something went wrong');
            $this->load->view('payment/error');
        }
    }

}
