<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Payu Custom Integration Demo</title>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.js"></script>
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
                            <h3 class="bt_title"><img src="<?php echo base_url(); ?>/backend/images/paym.png" class="mb10">
                              <span>PayU Payment Gateway</span></h3>
                            <div class="paybody">

                            <div class="">
                            <div class="paymentbg">
                              <div class="invtext">Course Purchase Details</div>
                                  <form class="" action="<?php echo $action; ?>/_payment" method="post" id="payuForm" name="payuForm">  
                                    <?php if(!empty($session_data['course_thumbnail'])){
                                    ?>
                                      <div class="img-container">
                                          <img src="<?php echo $this->customlib->getCourseThumbnailPath($session_data['course_thumbnail']); ?>" class="img-responsive center-block">
                                      </div>
                                      <?php
                                    }?>
                                     <table class="table mb0 paytable">
                                          <tr>
                                            <td class="bmedium text-center pb20">
                                              <?php echo $session_data['course_name']; ?>
                                            </td>
                                          </tr>
                                            
                                        
                                        <input type="hidden" name="key" value="<?php echo $mkey ?>" />
                                          <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
                                          <input type="hidden" name="txnid" value="<?php echo $tid ?>" />

                                          <input class="form-control" type="hidden" name="amount" value="<?php if(!empty($session_data['total_amount'])){ echo $amount;} ?>"  readonly/>

                                          <input class="form-control" type="hidden" name="firstname" id="firstname" value="<?php echo $session_data['name']; ?>" readonly/>

                                          <input class="form-control" type="hidden" name="email" id="email" value="<?php echo $session_data['email']; ?>" readonly/>

                                          <input class="form-control"  type="hidden" name="phone" value="<?php echo $session_data['contact_no']; ?>" readonly />

                                          <textarea class="form-control displaynone" name="productinfo" readonly><?php echo $productinfo ?></textarea>

                                          <input name="surl" value="<?php echo $sucess ?>" size="64" type="hidden" />
                                          <input name="furl" value="<?php echo $failure ?>" size="64" type="hidden" />                             
                                          <input type="hidden" name="service_provider" value="payu_paisa" size="64" />
                                          <input name="curl" value="<?php echo $cancel ?>" type="hidden" />

                                         
                                           <tr class="paybtngray">
                                            <td><button type="submit" name="search" value="" class="btn btn-info buttondarkgray">Pay With PayU <span class="bolds"><?php
                                                
                                                if(!empty($session_data['total_amount'])){ echo amountFormat($session_data['total_amount']);}else{echo '0.00';} ?></span></button></td>
                                        </tr>              
                                      </table>
                                      <script src="<?php echo base_url(); ?>backend/custom/jquery.min.js"></script>
                                          
                                  </form>
                                </div>
                              </div>
                                
                            </div><!--./paybody-->
                        </div><!--./paybox-->
                    </div><!--./paybox_bg-->
                </div><!--./col-md-12-->
            </div><!--./row-->
        </div><!--./container-->
    </body>
</html>
<script type="text/javascript">
(function ($) {
  "use strict";

      $(".submit_button").click(function (e) {
          var url = "<?php echo site_url('course_payment/payu/checkout') ?>";
          $.ajax({
              type: "POST",
              url: url,
              data: $("#payuForm").serialize(),
              dataType: "Json",
              success: function (response)
              {
                  if (response.status == "success") {
                      $('form#payuForm').submit();
                  } else if (response.status == "fail") {
                      $.each(response.error, function (index, value) {
                          var errorDiv = '.' + index + '_error';
                          $(errorDiv).empty().append(value);
                      });
                  }
              }
          });
          e.preventDefault();
      });
})(jQuery);
</script>