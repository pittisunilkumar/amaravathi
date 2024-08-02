<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Midtrans Custom Integration </title>
        <link href="style.css" type="text/css" rel="stylesheet" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <script type="text/javascript"
                src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-2uDtZD3V5ZA_pNYW"></script> 
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <style type="text/css">
            .paybox{width: 460px;margin: 7% auto;}
            .paybox_bg{background: #fff;box-shadow: 0px 1px 15px rgba(0, 0, 0, 0.18);border-radius: 10px;}
            .bt_title{background: #fff; color: #000; padding: 20px 20px; border-bottom:1px solid #ccc;}
            .paybody{padding: 0px 20px 20px;}
            .paybox label{font-size: 13px; padding-top: 7px;}
            .submit_button{border-radius: 4px; padding: 10px 20px; border:0; background: #204d74; color: #fff; display: block;width: 100%; font-size: 16px; text-transform: uppercase; margin-top: 20px;}
            .submit_button:hover{background: #367fa9;}
            .payspan{position: absolute; right:0; top:5px; font-size: 18px;}
            .table2 td{padding-top: 8px;}
            @media(max-width:767px){
                .paybox{width: 100%;margin: 1% auto;}
                .bt_title img{width: 100%; height: auto;}
            }
            .bt_title img{width: auto; height: auto;}
        </style>

    </head>
    <body>

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="paybox">
                        <div class="paybox_bg">
                            <h3 class="bt_title"><img src="<?php echo base_url();?>/backend/images/midtrans.jpg" style="margin-bottom: 10px;"><br />Midtrans Payment Gateway</h3>
                            <div class="paybody">

                                <div class="">
                                    <div class="paymentbg">

                                        <?php if ($api_error) {
                                            ?>
                                            <div class="alert alert-danger"><?php print_r($api_error); ?> </div>
                                            <?php
                                        }
                                        if (validation_errors()) {
                                            ?>
                                            <div class="alert alert-danger"> <?php echo validation_errors(); ?></div>
                                            <?php
                                        }
                                        ?>


                                        <div class="padd2 paddtzero">
                                            <form id="payment-form" method="post" action="<?= site_url() ?>/admin/admin/response">
                                                <input type="hidden" name="result_type" id="result-type" value="">
                                                <input type="hidden" name="result_data" id="result-data" value="">
                                            </form>
                                        </div>

                                        <div class="row">
                                            <div class="">
                                                <div class="padd2 paddtzero">
                                                    <table class="table2" width="100%">
                                                    <tr>
                                                        <th><?php echo ('Description'); ?></th>
                                                        <th class="text-right"><?php echo ('Amount') ?></th>
                                                    </tr>
                                                    <tr>
                                                        <td><?php
                                                            echo $params['payment_detail']->fee_group_name . "<br/><span>" . $params['payment_detail']->code;
                                                            ?></td>
                                                        <td class="text-right"><?php echo amountFormat($params['total']); ?></td>
                                                    </tr>

                                                    <tr class="bordertoplightgray">
                                                        <td bgcolor="#fff"> <?php echo ('Fine'); ?>:</td>
                                                        <td bgcolor="#fff" class="text-right"> <?php echo amountFormat($params['payment_detail']->fine_amount); ?></td>
                                                    </tr>
                                                    <tr class="bordertoplightgray">
                                                        <td bgcolor="#fff"> <?php echo ('Total'); ?>:</td>
                                                        <td bgcolor="#fff" class="text-right"> <?php echo amountFormat(($params['payment_detail']->fine_amount+$params['total'])); ?></td>
                                                    </tr>
                                                    
                                                    <tr><td colspan="2"><hr /></td></tr>
                                                    <tr class="bordertoplightgray">
                                                        <td bgcolor="#fff">  </td>
                                                        <td bgcolor="#fff" class="text-right"> <button type="submit"  name="search" id="pay-button" value="" class="btn btn-info"><i class="fa fa fa-chevron-right"></i> <?php echo ('Pay With Midtrans'); ?></button>  </td>
                                                    </tr>
                                                </table>




                                                </div>
                                            </div>
                                        </div> 
                                        </form>
                                    </div>    
                                </div><!--./paybox-->
                            </div><!--./paybox_bg-->
                        </div><!--./col-md-12-->
                    </div><!--./row-->
                </div><!--./container-->

                </body>
                <script type="text/javascript">
                    var resultType = document.getElementById('result-type');
                    var resultData = document.getElementById('result-data');

                    function changeResult(type, data) {
                        $("#result-type").val(type);
                        $("#result-data").val(JSON.stringify(data));
                        //resultType.innerHTML = type;
                        //resultData.innerHTML = JSON.stringify(data);
                    }
                    var payButton = document.getElementById('pay-button');
                    payButton.addEventListener('click', function () {
                        snap.pay('<?php echo $snap_Token; ?>', {// store your snap token here
                            onSuccess: function (result) {
                                changeResult('success', result);
                                $.ajax({
                                    url: '<?php echo base_url(); ?>gateway/midtrans/success',
                                    type: 'POST',
                                    data: $('#payment-form').serialize(),
                                    dataType: "json",
                                    success: function (msg) {

                                        window.location.href = "<?php echo base_url(); ?>payment/successinvoice/" + msg.invoice_id + "/" + msg.sub_invoice_id;

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
                </script>
                </html>