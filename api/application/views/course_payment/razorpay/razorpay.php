<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Razorpay Pay Custom Integration</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-api.css">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="paybox">
                        <div class="paybox_bg">
                            <h3 class="bt_title"><img src="<?php echo base_url(); ?>/backend/images/razorpay.jpg" ><span>Razorpay Payment Gateway </span></h3>
                            <div class="paybody">

                            <div class="">
                            <div class="paymentbg">
                              <div class="invtext">Course Purchase Details</div>
                             
                    <form method="post">
                      <?php

                    
                       if(!empty($params['course_thumbnail'])){
                        ?>
                        <div class="img-container">
                            <img src="<?php echo $this->customlib->getCourseThumbnailPath($params['course_thumbnail']); ?>" class="img-responsive center-block">
                        </div>
                        <?php
                      }?>
                      <table class="table mb0 paytable">
                      <tr>
                        <td class="bmedium text-center pb20">
                          <?php echo $params['course_name']; ?>
                        </td>
                      </tr>
                     
                      <tr class="paybtngray">
                        <td><button type="button"  name="search" onclick="pay()"  value="" class="btn btn-info buttondarkgray">Pay With Razorpay <span class="bolds"><?php
                            
                            if(!empty($params['total_amount'])){ echo amountFormat($params['total_amount']);}else{echo '0.00';} ?></span></button></td>
                    </tr>   
                  </table>

                    </form>
                  </div>
                </div>
                            </div>    
                        </div><!--./paybox-->
                    </div><!--./paybox_bg-->
                </div><!--./col-md-12-->
            </div><!--./row-->
        </div><!--./container-->

       <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script> 
        <script>
            function pay(e) { 
                var SITEURL = "<?php echo base_url() ?>";
                var totalAmount = <?php echo $total; ?>;
                var product_id = <?php echo $merchant_order_id; ?>;
                var options = {
                    "key": "<?php echo $key_id; ?>",
                    "amount": "<?php echo convertBaseAmountCurrencyFormat($total); ?>", // 2000 paise = INR 20
                    "currency": "<?php echo $params['currency_name']; ?>",
                    "image": "<?php echo base_url(); ?>/backend/images/razorpay.jpg",
                    "callback_url": SITEURL + 'course_payment/razorpay/callback',
                    "redirect": true,
                    "handler": function (response) {
                    },

                    "theme": {
                        "color": "#528FF0"
                    }
                }; 
                var rzp1 = new Razorpay(options);
                rzp1.open();
            }
        </script>
    </body>
</html>