<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Stripe Pay Custom Integration Demo</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-api.css">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="paybox">
                        <div class="paybox_bg">
                            <h3 class="bt_title"><img src="<?php echo base_url(); ?>/backend/images/stripe.png">
                                <span>Stripe Payment Gateway</span></h3>
                            <div class="paybody">

                                <div class="paymentbg">
                                    <div class="invtext">Course Purchase Details</div>
                                   
                            <div class="dropin-page"> 
                            <form action="" method="POST" id="payment-form">
                                <?php if(!empty($session_params['course_thumbnail'])){
                                    ?>
                                    <div class="img-container">
                                        <img src="<?php echo $this->customlib->getCourseThumbnailPath($session_params['course_thumbnail']); ?>" class="img-responsive center-block">
                                    </div>
                                    <?php
                                  }?>
                                   <table class="table mb0 paytable">
                                        <tr>
                                            <td class="bmedium text-center pb20">
                                                <?php echo $session_params['course_name']; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                        
                                            <td class="bmedium text-center">
                                                <input type="text" placeholder="Card Number" class="form-control" size="20" data-stripe="number">
                                            </td>
                                       </tr>
                                     
                                    <tr>
                                        <td class="bmedium">
                                            <div class="row">
                                                <div class="col-sm-6 col-xs-6">
                                                    <input placeholder="MM" type="text" class="form-control"  size="2" data-stripe="exp_month"><span class="payspan"> / </span>
                                                    
                                                </div>    


                                                <div class="col-sm-6 col-xs-6">
                                                    <input placeholder="YY" type="text" class="form-control" size="2" data-stripe="exp_year">
                                                </div>  
                                            </div>
                                        </td>
                                    </tr>  

                                    <tr>
                                       
                                        <td class="bmedium">
                                            <input placeholder="CVC" type="text" class="form-control" size="4" data-stripe="cvc">
                                        </td>
                                    </tr>  
                                    <tr>
                                       
                                        <td class="bmedium">
                                          <span class="payment-errors text-red"></span>
                                        </td>
                                    </tr>  
                                    <tr><td>  <?php if ($error) {
                                ?>
                                <div class="alert alert-danger error" ><?php
                                   foreach ($error as $key => $value) {
                                       echo $value."<br>";
                                   }
                                    ?> </div>
                            <?php }
                            ?></td>
                                               
                                              </tr> 
                                                <tr class="paybtngray">
                                                  <td><button type="submit" name="search" value="" class="btn btn-info buttondarkgray submit">Pay With Stripe <span class="bolds"><?php
                                                     
                                                      if(!empty($session_params['total_amount'])){ echo amountFormat($session_params['total_amount']);}else{echo '0.00';} ?></span></button></td>
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
        <script src="<?php echo base_url(); ?>backend/jquery.min.js"></script>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <!-- TO DO : Place below JS code in js file and include that JS file -->
        <script type="text/javascript">
            Stripe.setPublishableKey('<?php echo $params['public_test_key']; ?>');

            (function ($) {
              "use strict";
                var $form = $('#payment-form');
                $form.submit(function (event) {
                    // Disable the submit button to prevent repeated clicks:
                    $form.find('.submit').prop('disabled', true);

                    // Request a token from Stripe:
                    Stripe.card.createToken($form, stripeResponseHandler);

                    // Prevent the form from being submitted:
                    return false;
                });
            })(jQuery);

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
        </script>
    </body>
</html>