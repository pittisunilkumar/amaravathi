<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Paypal Pay Custom Integration Demo</title>
        <link href="style.css" type="text/css" rel="stylesheet" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <style type="text/css">
            .paybox{width: 460px;margin: 7% auto;}
            .paybox_bg{background: #fff;box-shadow: 0px 1px 15px rgba(0, 0, 0, 0.18);border-radius: 10px;}
            .bt_title{background: #fff; color: #000; padding: 20px 20px; border-bottom:1px solid #ccc;}
            .paybody{padding: 20px;}
            .paybox label{font-size: 13px; padding-top: 7px;}
            .submit_button{border-radius: 4px; padding: 10px 20px; border:0; background: #204d74; color: #fff; display: block;width: 100%; font-size: 16px; text-transform: uppercase; margin-top: 20px;}
            .submit_button:hover{background: #367fa9;}
            @media(max-width:767px){
                .paybox{width: 100%;margin: 1% auto;}
                .bt_title img{width: 100%; height: auto;}
            }
        </style>   
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="paybox">   
                        <div class="paybox_bg"> 
                            <h3 class="bt_title"><img src="<?php echo base_url();?>/backend/images/paypal.png" style="margin-bottom:10px"><br />Paypal Payment Gateway</h3>                         
                            <div class="paybody">
                                <form class="paddtlrb" action="<?php echo site_url('gateway/paypal/pay') ?>" method="POST" id="paypalForm">
                                    <div class="form-group row">
                                        <label class="col-sm-4">Name</label>
                                        <div class="col-sm-8">
                                            <div class="form-control"><?php echo $session_params['name']; ?></div></div>
                                    </div><!--./form-group-->
                                    <div class="form-group row">
                                        <label class="col-sm-4">Email</label>
                                        <div class="col-sm-8">
                                            <div class="form-control"><?php echo $session_params['email']; ?></div></div>
                                    </div><!--./form-group-->
                                    <div class="form-group row">
                                        <label class="col-sm-4">Guardian Phone</label>
                                        <div class="col-sm-8"><div class="form-control"><?php echo $session_params['guardian_phone']; ?></div></div>
                                    </div><!--./form-group-->
                                    <div class="form-group row">
                                        <label class="col-sm-4">Amount</label>
                                        <div class="col-sm-8"><div class="form-control"><?php echo amountFormat($session_params['total']); ?></div></div>
                                    </div><!--./form-group-->
                                    <div class="form-group row">
                                        <label class="col-sm-4">Fine</label>
                                        <div class="col-sm-8"><div class="form-control"><?php echo amountFormat($session_params['payment_detail']->fine_amount); ?></div></div>
                                    </div><!--./form-group-->
                                     <div class="form-group row">
                                        <label class="col-sm-4">Total</label>
                                        <div class="col-sm-8"><div class="form-control"><?php echo amountFormat($session_params['payment_detail']->fine_amount+$session_params['total']); ?></div></div>
                                    </div><!--./form-group-->

                                    <div class="form-group">
                                        <button type="submit" class="submit_button"><i class="fa fa-money"></i> Pay Now </button> 
                                    </div>	

                                </form>
                            </div><!--./paybody-->                       
                        </div><!--./paybox_bg-->                        
                    </div><!--./paybox-->
                </div><!--./col-md-12-->
            </div><!--./row-->
        </div><!--./container-->                           
    </body>
</html> 
