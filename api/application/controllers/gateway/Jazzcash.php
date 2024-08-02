<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Jazzcash extends Admin_Controller {

    var $setting;
    var $payment_method;

    public function __construct() {
        parent::__construct();

        $this->setting = $this->setting_model->get();
        $this->payment_method = $this->paymentsetting_model->get();
        $this->jazzcash_api=$this->paymentsetting_model->getActiveMethod();
        $this->session_details = $this->session->userdata('params');
    }

    public function index() {
        $data = array();
        $data['params'] = $this->session_details;
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
        $this->load->view('payment/jazzcash/index', $data);
    }

    public function pay() {

    	date_default_timezone_set("Asia/Karachi");
        $data['ExpiryTime'] = date('YmdHis', strtotime("+3 hours"));
		$data['TxnDateTime'] = date('YmdHis', strtotime("+0 hours"));
		$data['TxnRefNumber'] = "T". date('YmdHis');
        $input_para["pp_Version"]="2.0";
        $input_para["pp_IsRegisteredCustomer"]="Yes";
        $input_para["pp_TxnType"]="MPAY";
        $input_para["pp_TokenizedCardNumber"]="";
        $input_para["pp_CustomerID"]=time();
        $input_para["pp_CustomerEmail"]="";
        $input_para["pp_CustomerMobile"]="";
        $input_para["pp_MerchantID"]=$this->jazzcash_api->api_secret_key;
        $input_para["pp_Language"]="EN";
        $input_para["pp_SubMerchantID"]="";
        $input_para["pp_Password"]=$this->jazzcash_api->api_password;
        $input_para["pp_TxnRefNo"]="T". date('YmdHis');
        $input_para["pp_Amount"]=(number_format((float)(convertBaseAmountCurrencyFormat($this->session_details['payment_detail']->fine_amount+$this->session_details['total'])), 2, '.', ''))*100;
        $input_para["pp_DiscountedAmount"]="";
        $input_para["pp_DiscountBank"]="";
        $input_para["pp_TxnCurrency"]="PKR";
        $input_para["pp_TxnDateTime"]=date('YmdHis', strtotime("+0 hours"));
        $input_para["pp_TxnExpiryDateTime"]=date('YmdHis', strtotime("+3 hours"));
        $input_para["pp_BillReference"]=time();
        $input_para["pp_Description"]='Student Fee';
        $input_para["pp_ReturnURL"]=base_url().'gateway/jazzcash/success';
        $input_para["pp_SecureHash"]="0123456789";
        $input_para["ppmpf_1"]="1";
        $input_para["ppmpf_2"]="2";
        $input_para["ppmpf_3"]="3";
        $input_para["ppmpf_4"]="4";
        $input_para["ppmpf_5"]="5";
        $data['payment_data']=$input_para;
        $this->load->view('payment/jazzcash/pay', $data);

    }

    public function success() {

      date_default_timezone_set("Asia/Calcutta");

        if($_POST['pp_ResponseCode']=='000'){
        	$ref_id = $_POST['pp_TxnRefNo'];
       $params = $this->session->userdata('params');
                
                $json_array = array(
                    'amount' => $this->session_details['total'],
                    'date' => date('Y-m-d'),
                    'amount_discount' => 0, 
                    'amount_fine' => $this->session_details['payment_detail']->fine_amount,
                    'received_by' => '',
                    'description' => "Online fees deposit through Jazzcash TXN ID: " . $ref_id,
                    'payment_mode' => 'Jazzcash',
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
        }elseif($_POST['pp_ResponseCode']=='112'){
        		redirect(base_url("payment/paymentfailed")); 
        }else{
            $this->session->set_flashdata('msg',$_POST['pp_ResponseMessage']);
              $data = array();
        $data['params'] = $this->session_details;
        $data['setting'] = $this->setting;
        $data['api_error'] = array();
        $data['student_data'] = $this->student_model->get($data['params']['student_id']);
                redirect(site_url('gateway/jazzcash'));
        }

    	
    }

}
