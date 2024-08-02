<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Midtrans Custom Integration </title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-api.css">
        <script type="text/javascript"
                src="https://app.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-2uDtZD3V5ZA_pNYW"></script> 
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        

    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="paybox">
                        <div class="paybox_bg">
                            <h3 class="bt_title"><img src="<?php echo base_url(); ?>/backend/images/midtrans.jpg" class="mb10"><span>Midtrans Payment Gateway</span></h3>
                            <div class="paybody">

                                <div class="">
                                    <div class="paymentbg">
<div class=" paddtzero">
<form id="payment-form" method="post" action="<?= site_url() ?>/admin/admin/response">
    <input type="hidden" name="result_type" id="result-type" value="">
    <input type="hidden" name="result_data" id="result-data" value="">
</form>
</div>

    <div class="invtext">Course Purchase Details</div>
    
    <div class=" paddtzero">
    <form action="#" id="payment-form" method="post">
        <?php if(!empty($params['course_thumbnail'])){?>
            <div class="img-container">
                <img src="<?php echo $this->customlib->getCourseThumbnailPath($params['course_thumbnail']); ?>" class="img-responsive center-block">
            </div>
        <?php } ?>
        <table class="table mb0 paytable">
      <tr>
        <td class="bmedium text-center pb20">
          <?php echo $params['course_name']; ?>
        </td>
      </tr>
     
      <tr class="paybtngray">
        <td><button type="button" id="pay-button" name="search"  value="" class="btn btn-info buttondarkgray">Pay With Midtrans <span class="bolds"><?php
            
            if(!empty($params['total_amount'])){ echo amountFormat($params['total_amount']);}else{echo '0.00';} ?></span></button></td>
    </tr>   
  </table>

</form>
    </div>
</div>    
</div>    
</div>    
                                </div><!--./paybox-->
                            </div><!--./paybox_bg-->
                        </div><!--./col-md-12-->
                    </div><!--./row-->
                </div><!--./container-->
                </body>
                <script type="text/javascript">
                    function changeResult(type, data) {
                        $("#result-type").val(type);
                        $("#result-data").val(JSON.stringify(data));
                    }

                    ( function ( $ ) {
                      'use strict';
                      
                        var resultType = document.getElementById('result-type');
                        var resultData = document.getElementById('result-data');

                        var payButton = document.getElementById('pay-button');
                        payButton.addEventListener('click', function () {
                            snap.pay('<?php echo $snap_Token; ?>', {// store your snap token here
                                onSuccess: function (result) {
                                    changeResult('success', result);
                                    $.ajax({
                                        url: '<?php echo base_url(); ?>course_payment/midtrans/midtrans_pay',
                                        type: 'POST',
                                        data: $('#payment-form').serialize(),
                                        dataType: "json",
                                        success: function (msg) {
                                            window.location.href = "<?php echo base_url(); ?>course_payment/midtrans/success";
                                        }
                                    });
                                },
                                onPending: function (result) {
                                    console.log('pending');
                                    console.log(result);
                                },
                                onError: function (result) {
                                    console.log('error');
                                    console.log(result);
                                },
                                onClose: function () {
                                    console.log('customer closed the popup without finishing the payment');
                                }
                            })

                        });
                    } ( jQuery ) )
                </script>
                </html>