<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Onepay extends Admin_Controller {

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
        $this->load->view('payment/onepay/index', $data);
    }

    public function pay() {

        $this->form_validation->set_rules('phone', ('Phone'), 'trim|required');
        $this->form_validation->set_rules('email', ('Email'), 'trim|required');
  $data = array();
        $data['params'] = $this->session->userdata('params');
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
        if ($this->form_validation->run() == false) {

            $this->load->view('payment/onepay/index', $data);
        } else {

            $api = $this->paymentsetting_model->getActiveMethod();

            $pay_method = $this->paymentsetting_model->getActiveMethod();
            if ($this->session->has_userdata('params')) {
                $session_params = $this->session->userdata('params');
                $data['session_params'] = $session_params;
            }

            $amount = convertBaseAmountCurrencyFormat($data['session_params']['payment_detail']->fine_amount+$data['session_params']['total']);
            

           $SECURE_SECRET =$api->api_signature;
        $payment_data=array(
        'AVS_City' => '',
        'AVS_Country' =>'',
        'AVS_PostCode' => '',
        'AVS_StateProv' => '',
        'AVS_Street01' => '',
        'AgainLink' => urlencode($_SERVER['HTTP_REFERER']),
        'Title' => '',
        'display' => '',
        'vpc_AccessCode' => $api->salt,
        'vpc_Amount' => $amount*100,
        'vpc_Command' => 'pay',
        'vpc_Customer_Email' => '',
        'vpc_Customer_Id' => '',
        'vpc_Customer_Phone' => '',
        'vpc_Locale' => 'en',
        'vpc_MerchTxnRef' => date('YmdHis') . rand(),
        'vpc_Merchant' => $api->api_publishable_key,
        'vpc_OrderInfo' => 'JSECURETEST01',
        'vpc_ReturnURL' => base_url() . 'gateway/onepay/complete',
        'vpc_SHIP_City' => '',
        'vpc_SHIP_Country' => '',
        'vpc_SHIP_Provice' => '',
        'vpc_SHIP_Street01' => '',
        'vpc_TicketNo' => $_SERVER ['REMOTE_ADDR'],
        'vpc_Version' => '2');
        $vpcURL="https://mtf.onepay.vn/paygate/vpcpay.op?";
        foreach($payment_data as $key => $value) {
            if (strlen($value) > 0) {
                if ($appendAmp == 0) {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $appendAmp = 1;
                } else {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                }

                if ((strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
                    $md5HashData .= $key . "=" . $value . "&";
                }
            }
        }

        $md5HashData = rtrim($md5HashData, "&");

        if (strlen($SECURE_SECRET) > 0) {

            $vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $md5HashData, pack('H*',$SECURE_SECRET)));
        }


        header("Location: ".$vpcURL);
        }
    }

 
	
	public function complete()
    {
        $api = $this->paymentsetting_model->getActiveMethod();
		$SECURE_SECRET = $api->api_signature;
$vpc_Txn_Secure_Hash = $_GET["vpc_SecureHash"];
$vpc_MerchTxnRef = $_GET["vpc_MerchTxnRef"];
//$vpc_AcqResponseCode = $_GET["vpc_AcqResponseCode"];
unset($_GET["vpc_SecureHash"]);
$errorExists = false;
if (strlen($SECURE_SECRET) > 0 && $_GET["vpc_TxnResponseCode"] != "7" && $_GET["vpc_TxnResponseCode"] != "No Value Returned") {
    ksort($_GET);
    $md5HashData = "";
    foreach ($_GET as $key => $value) {
        if ($key != "vpc_SecureHash" && (strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
            $md5HashData .= $key . "=" . $value . "&";
        }
    }

    $md5HashData = rtrim($md5HashData, "&");
    if (strtoupper ( $vpc_Txn_Secure_Hash ) == strtoupper(hash_hmac('SHA256', $md5HashData, pack('H*',$SECURE_SECRET)))) {
        $hashValidated = "CORRECT";
    } else {
        $hashValidated = "INVALID HASH";
    }
} else {

    $hashValidated = "INVALID HASH";
}

$txnResponseCode = $this->null2unknown($_GET["vpc_TxnResponseCode"]);

$verType = array_key_exists("vpc_VerType", $_GET) ? $_GET["vpc_VerType"] : "No Value Returned";
$verStatus = array_key_exists("vpc_VerStatus", $_GET) ? $_GET["vpc_VerStatus"] : "No Value Returned";
$token = array_key_exists("vpc_VerToken", $_GET) ? $_GET["vpc_VerToken"] : "No Value Returned";
$verSecurLevel = array_key_exists("vpc_VerSecurityLevel", $_GET) ? $_GET["vpc_VerSecurityLevel"] : "No Value Returned";
$enrolled = array_key_exists("vpc_3DSenrolled", $_GET) ? $_GET["vpc_3DSenrolled"] : "No Value Returned";
$xid = array_key_exists("vpc_3DSXID", $_GET) ? $_GET["vpc_3DSXID"] : "No Value Returned";
$acqECI = array_key_exists("vpc_3DSECI", $_GET) ? $_GET["vpc_3DSECI"] : "No Value Returned";
$authStatus = array_key_exists("vpc_3DSstatus", $_GET) ? $_GET["vpc_3DSstatus"] : "No Value Returned";

$errorTxt = "";

if ($txnResponseCode == "7" || $txnResponseCode == "No Value Returned" || $errorExists) {
    $errorTxt = "Error ";
}

$transStatus = "";
if($hashValidated=="CORRECT" && $txnResponseCode=="0"){
    $transStatus = "success";
    

            $payment_id = $_GET["vpc_MerchTxnRef"];
             if ($payment_id) {
                $params = $this->session->userdata('params');
                $ref_id = $payment_id;
                $json_array = array(
                    'amount' => $params['total'],
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0,
                    'amount_fine' => $params['payment_detail']->fine_amount,
                    'received_by' => '',
                    'description' => "Online fees deposit through Onepay TXN ID: " . $ref_id,
                    'payment_mode' => 'Onepay',
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
            }
			
			


    }else{
		redirect("gateway/onepay/fail", 'refresh');
	}
	}
	
	public function fail(){
		$this->load->view('payment/paymentfailed');
	}
	   
       
  


// If input is null, returns string "No Value Returned", else returns input
public function null2unknown($data)
{
    if ($data == "") {
        return "No Value Returned";
    } else {
        return $data;
    }
}

}
