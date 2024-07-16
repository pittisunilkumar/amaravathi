<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Paystack extends Admin_Controller {

    var $setting;
    var $payment_method;
    var $api_config;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
        $this->api_config = $this->paymentsetting_model->getActiveMethod();
        $this->load->model('course_model');
    }

    /*
    This is used to show payment detail page
    */
    public function index() {
        $data['params'] = $this->session->userdata('course_amount');
        $data['setting'] = $this->setting;
        $this->load->view('course_payment/paystack/index', $data);
    }

    /*
    This is for payment gateway functionality
    */
    public function paystack_pay() {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');

        $params = $this->session->userdata('course_amount');
        $data['params'] = $params;
        $data['setting'] = $this->setting;
        
        if ($this->form_validation->run() == false) {
            $this->load->view('course_payment/paystack/index', $data);
        } else {
            $data['total'] = convertBaseAmountCurrencyFormat($params['total_amount']) * 100;
            if (isset($data)) {
                $amount = $data['total'];
                $ref = time() . "02";
                $callback_url = base_url() . 'course_payment/paystack/verify_payment/' . $ref;
                $postdata = array('email' => $_POST['email'], 'amount' => $amount, "reference" => $ref, "callback_url" => $callback_url);
                $url = "https://api.paystack.co/transaction/initialize";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));//Post Fields
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $headers = [
                    'Authorization: Bearer ' . $this->api_config->api_secret_key,
                    'Content-Type: application/json',
                ];
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $request = curl_exec($ch);
                curl_close($ch);
                $result = json_decode($request, true);

                if ($result['status']) {
                    $redir = $result['data']['authorization_url'];
                    header("Location: " . $redir);
                } else {
                    $this->load->view('course_payment/paystack/index', $data);
                }
            }
        }
    }

    /*
    This is used to show success page status
    */
    public function verify_payment($ref) {
        $result = array();
        $url = 'https://api.paystack.co/transaction/verify/' . $ref;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $this->api_config->api_secret_key]
        );
        $request = curl_exec($ch);
        curl_close($ch);

        if ($request) {
            $result = json_decode($request, true);
            if ($result) {
                if ($result['data']) {
                    //something came in
                    if ($result['data']['status'] == 'success') {
                        $params = $this->session->userdata('course_amount');
                        $payment_id = $ref;
                        $payment_data = array(
                            'date' => date('Y-m-d'),
                            'student_id' => $params['student_id'],
                            'online_courses_id' => $params['courseid'],
                            'course_name' => $params['course_name'],
                            'actual_price' => $params['actual_amount'],
                            'paid_amount' => $params['total_amount'],
                            'payment_type' => 'Online',
                            'transaction_id' => $payment_id,
                            'note' => "Online course fees deposit through Paystack Ref ID: " . $payment_id,
                            'payment_mode' => 'Paystack',
                        );
                        $this->course_model->add($payment_data);
                        $this->load->view('course_payment/paymentsuccess');
                    } else {
                        // the transaction was not successful, do not deliver value'
                        //uncomment this line to inspect the result, to check why it failed.
                        redirect(base_url("course_payment/course_payment/paymentfailed"));
                    }
                } else {
                    redirect(base_url("course_payment/course_payment/paymentfailed"));
                }
            } else {
                //die("Something went wrong while trying to convert the request variable to json. Uncomment the print_r command to see what is in the result variable.");
                redirect(base_url("course_payment/course_payment/paymentfailed"));
            }
        } else {
            //die("Something went wrong while executing curl. Uncomment the var_dump line above this line to see what the issue is. Please check your CURL command to make sure everything is ok");
            redirect(base_url("course_payment/course_payment/paymentfailed"));
        }
    }
}