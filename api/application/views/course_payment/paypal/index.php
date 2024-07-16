<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Paypal Pay Custom Integration Demo</title>
        <link href="style.css" type="text/css" rel="stylesheet" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-api.css">
          
    </head>
    <body> 
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="paybox">   
                        <div class="paybox_bg"> 
                            <h3 class="bt_title"><img src="<?php echo base_url(); ?>/backend/images/paypal.png" ><span>Paypal Payment Gateway</span></h3>                         
                            <div class="paybody">
                                <form class="paddtlrb" action="<?php echo site_url('course_payment/paypal/pay') ?>" method="POST" id="paypalForm">
                                    <?php if(!empty($session_params['course_thumbnail'])){?>
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
                                                <tr class="paybtngray">
                                                  <td><button type="submit"  name="search"  value="" class="btn btn-info buttondarkgray">Pay With Paypal <span class="bolds"><?php
                                                     
                                                      if(!empty($session_params['total_amount'])){ echo amountFormat($session_params['total_amount']);}else{echo '0.00';} ?></span></button></td>
                                              </tr>   
                                            </table>

                                </form>
                            </div><!--./paybody-->                       
                        </div><!--./paybox_bg-->                        
                    </div><!--./paybox-->
                </div><!--./col-md-12-->
            </div><!--./row-->
        </div><!--./container-->                           
    </body>
</html> 
