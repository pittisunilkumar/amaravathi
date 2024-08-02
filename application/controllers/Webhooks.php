<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Webhooks extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function insta_webhook() {
        $insta_webhook = $this->paymentsetting_model->insta_webhooksalt();

        $data = $_POST;
        $mac_provided = $data['mac'];  // Get the MAC from the POST data
        unset($data['mac']);  // Remove the MAC key from the data.
        $ver = explode('.', phpversion());
        $major = (int) $ver[0];
        $minor = (int) $ver[1];
        if ($major >= 5 and $minor >= 4) {
            ksort($data, SORT_STRING | SORT_FLAG_CASE);
        } else {
            uksort($data, 'strcasecmp');
        }
// You can get the 'salt' from Instamojo's developers page(make sure to log in first): https://www.instamojo.com/developers
// Pass the 'salt' without <>
        $mac_calculated = hash_hmac("sha1", implode("|", $data), "$insta_webhook->salt");
        if ($mac_provided == $mac_calculated) {
            if ($data['status'] == "Credit") {
                // Payment was successful, mark it as successful in your database.
                // You can acess payment_request_id, purpose etc here. 
            } else {
                // Payment was unsuccessful, mark it as failed in your database.
                // You can acess payment_request_id, purpose etc here.
            }
        } else {
            echo "MAC mismatch";
        }
    }

    public function cashfree(){
        echo "<pre>"; print_r($_POST);die;
       $orderId = $_POST["orderId"];
 $orderAmount = $_POST["orderAmount"];
 $referenceId = $_POST["referenceId"];
 $txStatus = $_POST["txStatus"];
 $paymentMode = $_POST["paymentMode"];
 $txMsg = $_POST["txMsg"];
 $txTime = $_POST["txTime"];
 $signature = $_POST["signature"];
 $data = $orderId.$orderAmount.$referenceId.$txStatus.$paymentMode.$txMsg.$txTime;
 $hash_hmac = hash_hmac('sha256', $data, $secretkey, true) ;
 $computedSignature = base64_encode($hash_hmac);
 if ($signature == $computedSignature) {
    // Proceed
  } else {
   // Reject this call
 }  
    }


     public function payfast(){
        echo "<pre>"; print_r($_POST);die;
    
    }

}
https://sandbox.payfast.co.za/eng/process?merchant_id=10024779&merchant_key=z6vv3mbu0z1xj&return_url=https://dev.webfeb.com/ss640dev/admin/admin/success&cancel_url=https://dev.webfeb.com/ss640dev/admin/admin/cancel&notify_url=https://dev.webfeb.com/ss640dev/webhooks/payfast&amount=22.00&item_name=sachin&subscription_type=2&signature=81086e1ae5a7bcc93f4c12846f0305de&setup={%22split_payment%22:{%22merchant_id%22:10023922,%22amount%22:%2220%22,%22percentage%22:%225%22}}