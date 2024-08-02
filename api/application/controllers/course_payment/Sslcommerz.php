<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sslcommerz extends Admin_Controller {

    public $api_config = "";

    public function __construct() {
        parent::__construct();
        $this->load->model('course_model');
        $this->setting = $this->setting_model->get();
    }

    /*
    This is used to show payment detail page
    */
    public function index() {

        $data['params'] = $this->session->userdata('course_amount');
        $data['setting'] = $this->setting;
        $data['error'] =array();
        $this->load->view('course_payment/sslcommerz/index', $data);
    }

    /*
    This is for payment gateway functionality
    */
    public function pay()
    {
        $params       = $this->session->userdata('course_amount');
        $sslcommerzdetails = $this->paymentsetting_model->getActiveMethod();

        $requestData        = array();
        $CURLOPT_POSTFIELDS = array(
            'store_id'         => $sslcommerzdetails->api_publishable_key,
            'store_passwd'     => $sslcommerzdetails->api_password,
            'total_amount'     => convertBaseAmountCurrencyFormat($params['total_amount']),
            'currency'         => $params['currency_name'],
            'tran_id'          => abs(crc32(uniqid())),
            'success_url'      => base_url() . 'course_payment/sslcommerz/success',
            'fail_url'         => base_url() . 'course_payment/sslcommerz/fail',
            'cancel_url'       => base_url() . 'course_payment/sslcommerz/cancel',
            'cus_name'         => $params['name'],
            'cus_email'        => !empty($params['email']) ? $params['email'] : "example@email.com",
            'cus_add1'         => !empty($params['address']) ? $params['address'] : "Dhaka",
            'cus_phone'        => !empty($params['contact_no']) ? $params['contact_no'] : "01711111111",
            'cus_city'         => '',
            'cus_country'      => '',
            'multi_card_name'  => 'mastercard,visacard,amexcard,internetbank,mobilebank,othercard ',
            'shipping_method'  => 'NO',
            'product_name'     => 'test',
            'product_category' => 'Electronic',
            'product_profile'  => 'general',
        );
        $string = "";
        foreach ($CURLOPT_POSTFIELDS as $key => $value) {
            $string .= $key . '=' . $value . "&";
            if ($key == 'product_profile') {
                $string .= $key . '=' . $value;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://securepay.sslcommerz.com/gwprocess/v4/api.php');
//https://securepay.sslcommerz.com/gwprocess/v4/api.php
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$string");

        $headers   = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        $response = json_decode($result);
        //print_r($response);die;
        if($response->status=='FAILED'){
            $data['params'] = $this->session->userdata('course_amount');
        $data['setting'] = $this->setting;
        $data['error'] =array('0'=>$response->failedreason);
        $this->load->view('course_payment/sslcommerz/index', $data);
 //redirect(base_url("course_payment/course_payment/paymentfailed"));
        }else{
header("Location: $response->GatewayPageURL");
        }

        

    }

    /*
    This is used to show success page status
    */
    public function success()
    {
        $params = $this->session->userdata('course_amount');

        if ($_POST['status'] == 'VALID') {
        
            $payment_id = $_POST['val_id'];

            $payment_data = array(
                'date' => date('Y-m-d'),
                'student_id' => $params['student_id'],
                'online_courses_id' => $params['courseid'],
                'course_name' => $params['course_name'],
                'actual_price' => $params['actual_amount'],
                'paid_amount' => $params['total_amount'],
                'payment_type' => 'Online',
                'transaction_id' => $payment_id,
                'note' => "Online course fees deposit through Sslcommerz Txn ID: " . $payment_id,
                'payment_mode' => 'Sslcommerz',
            );
            $this->course_model->add($payment_data);
            $this->load->view('course_payment/paymentsuccess');
        } else {
          redirect(base_url("course_payment/course_payment/paymentfailed"));
        }

    }

    /*
    This is used to show cancel page status
    */
    public function fail()
    {
      redirect(base_url("course_payment/course_payment/paymentfailed"));
    }

    /*
    This is used to show cancel page status
    */
    public function cancel()
    {
      redirect(base_url("course_payment/course_payment/paymentfailed"));
    }
}