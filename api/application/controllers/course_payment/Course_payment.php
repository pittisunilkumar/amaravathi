<?php 
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

use Omnipay\Omnipay;
require_once(APPPATH . 'third_party/omnipay/vendor/autoload.php');
class Course_payment extends Admin_Controller {

    public $pay_method;

    function __construct() {
        parent::__construct();
        $this->setting = $this->setting_model->get();
        $this->load->model('course_model');
        $this->pay_method = $this->paymentsetting_model->getActiveMethod();
    }

    /*
    This is used to call all payment gateway and also store payment data in session
    */
    public function payment($courseid, $studentid)
    { 
        
        if (!empty($this->pay_method)) {
        $this->session->unset_userdata("course_amount");
        $studentdata = $this->course_model->get($studentid);

        $contact_no = $studentdata["mobileno"];
        $email = $studentdata["email"];
        $name = $studentdata["firstname"].' '.$studentdata["lastname"];
        
        $courseslist = $this->course_model->coursedetail($courseid);

        $sectionarray   =   $this->course_model->sectionbycourse($courseid);
         
        $discount = '';
        $price = '';
        if ($courseslist['discount'] !='0.00') {
            $discount = $courseslist['price'] - (($courseslist['price'] * $courseslist['discount']) / 100);
        }
        if (($courseslist["free_course"] == 1) && ($courseslist["price"] == '0.00')) {
            $price      = 'Free';           
        } elseif (($courseslist["free_course"] == 1) && ($courseslist["price"] != '0.00')) {
            if($courseslist['price'] > '0.00'){
                $courseprice = $courseslist['price'];
            }else{
                $courseprice = '';
            }
            $price      = $courseprice;           
        } elseif (($courseslist["price"] != '0.00') && ($courseslist["discount"] != '0.00')) {
            $discount   = number_format((float) $discount, 2, '.', '');
            if($courseslist['price'] > '0.00'){
                $courseprice = $courseslist['price'];
            }else{
                $courseprice = '';
            }
            $price      = $discount ;
        } else {
            $price      = $courseslist['price']  ; 
        }
        $paymentdata = array(
            'actual_amount' => $courseslist['price'],
            'discount' => $courseslist['discount'],
            'total_amount' => $price,
            'courseid' => $courseid,
            'course_name' => $courseslist['title'],
            'description' => $courseslist['description'],
            'course_thumbnail' => $courseslist['course_thumbnail'],
            'student_id' => $studentid,
            'contact_no' => $contact_no,
            'email' => $email,
            'name' => $name, 
            'sectionarray' => $sectionarray,
            'paid_free' => $courseslist['free_course'],
            'address' => '',
        );
        if(($studentdata['currency_id']!=0)){
               
                $paymentdata['currency_name']=$studentdata['currency_name'];
                $paymentdata['currency_base_price']=$studentdata['base_price'];
                $paymentdata['currency_symbol']=$studentdata['symbol'];
            }else{
               $paymentdata['currency_name']=$studentdata['currency_name'];
                $paymentdata['currency_base_price']=$this->setting[0]['base_price'];
                $paymentdata['currency_symbol']=$this->setting[0]['currency_symbol'];
            }

        $this->session->set_userdata('course_amount', $paymentdata);
       $this->session->set_userdata('student_currency', array('currency_name'=>$paymentdata['currency_name'],'currency_base_price'=>$paymentdata['currency_base_price'],'currency_symbol'=>$paymentdata['currency_symbol']));
        $data = array();
   
            
            if ($this->pay_method->payment_type == "payu") {
                redirect(base_url("course_payment/payu"));
            } elseif ($this->pay_method->payment_type == "stripe") {
                redirect(base_url("course_payment/stripe"));
            } elseif ($this->pay_method->payment_type == "ccavenue") {
                redirect(base_url("course_payment/ccavenue"));
            } elseif ($this->pay_method->payment_type == "paypal") {
                redirect(base_url("course_payment/paypal"));
            } elseif ($this->pay_method->payment_type == "instamojo") {
                redirect(base_url("course_payment/instamojo"));
            } elseif ($this->pay_method->payment_type == "paytm") {
                redirect(base_url("course_payment/paytm"));
            } elseif ($this->pay_method->payment_type == "razorpay") {
                redirect(base_url("course_payment/razorpay"));
            } elseif ($this->pay_method->payment_type == "paystack") {
                redirect(base_url("course_payment/paystack"));
            } elseif ($this->pay_method->payment_type == "midtrans") {
                redirect(base_url("course_payment/midtrans"));
            }elseif ($this->pay_method->payment_type == "ipayafrica") {
                redirect(base_url("course_payment/ipayafrica"));
            }elseif ($this->pay_method->payment_type == "jazzcash") {
                redirect(base_url("course_payment/jazzcash"));
            }elseif ($this->pay_method->payment_type == "pesapal") {
                redirect(base_url("course_payment/pesapal"));
            }elseif ($this->pay_method->payment_type == "flutterwave") {
                redirect(base_url("course_payment/flutterwave"));
            }elseif ($this->pay_method->payment_type == "billplz") {
                redirect(base_url("course_payment/billplz"));
            }elseif ($this->pay_method->payment_type == "sslcommerz") {
                redirect(base_url("course_payment/sslcommerz"));
            }elseif ($this->pay_method->payment_type == "cashfree") {
                redirect(base_url("course_payment/cashfree"));
            }elseif ($this->pay_method->payment_type == "walkingm") {
                redirect(base_url("course_payment/walkingm"));
            }elseif ($this->pay_method->payment_type == "mollie") {
                redirect(base_url("course_payment/mollie"));
            }elseif ($this->pay_method->payment_type == "payfast") {
                redirect(base_url("course_payment/payfast"));
            }elseif ($this->pay_method->payment_type == "toyyibpay") {
                redirect(base_url("course_payment/toyyibpay"));
            }elseif ($this->pay_method->payment_type == "twocheckout") {
                redirect(base_url("course_payment/twocheckout"));
            }elseif ($this->pay_method->payment_type == "skrill") {
                redirect(base_url("course_payment/skrill"));
            }elseif ($this->pay_method->payment_type == "payhere") {
                redirect(base_url("course_payment/payhere"));
            }elseif ($this->pay_method->payment_type == "onepay") {
                redirect(base_url("course_payment/onepay"));
            }
        }else{
            echo "Oops! payment method not available, Please contact to administrator";
        }
    }

	/*
    This is used to show failed payment status
    */
    public function paymentfailed()
    {
        $data = array();
        $this->load->view('payment/paymentfailed', $data);
    }
     public function paymentprocessing()
    {
        $data = array();
        $this->load->view('payment/processing', $data);
    }
}