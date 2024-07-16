<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Stripe Pay Custom Integration Demo</title>
        <link href="style.css" type="text/css" rel="stylesheet" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <style type="text/css">
            .paybox{width: 460px;margin: 7% auto;}
            .paybox_bg{background: #fff;box-shadow: 0px 1px 15px rgba(0, 0, 0, 0.18);border-radius: 10px;}
            .bt_title{background: #fff; color: #000; padding: 20px 20px; border-bottom:1px solid #ccc;}
            .paybody{padding: 20px;}
            .paybox label{font-size: 13px; padding-top: 7px;}
            .submit_button{border-radius: 4px; padding: 10px 20px; border:0; background: #204d74; color: #fff; display: block;width: 100%; font-size: 16px; text-transform: uppercase; margin-top: 20px;}
            .submit_button:hover{background: #367fa9;}
            .payspan{position: absolute; right:0; top:5px; font-size: 18px;}
            @media(max-width:767px){
                .paybox{width: 100%;margin: 1% auto;}
                .bt_title img{width: 200px; height: 80px;}
            }
            .text-red {
    color: #dd4b39 !important;
}
        </style>

    </head>
    <body>

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="paybox">
                        <div class="paybox_bg">
                            <h3 class="bt_title"><img src="<?php echo base_url();?>/backend/images/stripe.png" style="margin-bottom: 10px;"><br />Stripe Payment Gateway</h3>
                            <div class="paybody">
                                <div class="dropin-page">
                                    <div class="form-group row"><label class="col-sm-4">Name</label>
                                        <div class="col-sm-8">
                                            <div class="form-control"><?php echo $session_params['name']; ?></div>	
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4">Email</label>
                                        <div class="col-sm-8">
                                            <div class="form-group"><input type="text" class="form-control" name="" value="<?php echo $session_params['email']; ?>"></div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-4">Guardian Phone</label>
                                        <div class="col-sm-8">
                                            <div class="form-control"><?php echo $session_params['guardian_phone']; ?></div>
                                        </div>
                                    </div>
                                     <div class="form-group row">
                                        <label class="col-sm-4">Fine</label>
                                        <div class="col-sm-8">
                                            <div class="form-control"><?php echo amountFormat($session_params['payment_detail']->fine_amount); ?></div>

                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-4">Fee</label>
                                        <div class="col-sm-8">
                                            <div class="form-control"><?php echo amountFormat($session_params['payment_detail']->fine_amount+$session_params['total']); ?></div>

                                        </div>
                                    </div>

                                    <form action="" method="POST" id="payment-form">
                                        <span class="payment-errors text-red"></span>

                                        <div class="form-group row">
                                            <label class="col-sm-4">Card Number</label>
                                            <div class="col-sm-8"><input type="text" class="form-control" size="20" data-stripe="number"></div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4">Expiration (MM/YY)</label>
                                            <div class="col-sm-8">
                                                <div class="row">	
                                                    <div class="col-sm-6 col-xs-6"><input type="text" class="form-control"  size="2" data-stripe="exp_month"><span class="payspan"> / </span></div>


                                                    <div class="col-sm-6 col-xs-6"><input type="text"  class="form-control" size="2" data-stripe="exp_year"></div>
                                                </div>
                                            </div>  
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-4">CVC</label>
                                            <div class="col-sm-8"><input type="text" class="form-control" size="4" data-stripe="cvc"></div>
                                        </div>
                                        <input type="submit" class="submit submit_button" value="Submit Payment">
                                    </form>
                                </div>
                            </div>		
                        </div><!--./paybox-->
                    </div><!--./paybox_bg-->
                </div><!--./col-md-12-->
            </div><!--./row-->
        </div><!--./container-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <!-- TO DO : Place below JS code in js file and include that JS file -->
        <script type="text/javascript">
            Stripe.setPublishableKey('<?php echo $params['public_test_key']; ?>');

            $(function () {
                var $form = $('#payment-form');
                $form.submit(function (event) {
                    // Disable the submit button to prevent repeated clicks:
                    $form.find('.submit').prop('disabled', true);

                    // Request a token from Stripe:
                    Stripe.card.createToken($form, stripeResponseHandler);

                    // Prevent the form from being submitted:
                    return false;
                });
            });

            function stripeResponseHandler(status, response) {
                // Grab the form:
                var $form = $('#payment-form');

                if (response.error) { // Problem!

                    // Show the errors on the form:
                    $form.find('.payment-errors').text(response.error.message);
                    $form.find('.submit').prop('disabled', false); // Re-enable submission

                } else { // Token was created!

                    // Get the token ID:
                    var token = response.id;

                    // Insert the token ID into the form so it gets submitted to the server:
                    $form.append($('<input type="hidden" name="stripeToken">').val(token));

                    // Submit the form:
                    $form.get(0).submit();
                }
            }
            ;
        </script>
    </body>
</html>