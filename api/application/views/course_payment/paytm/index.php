<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Paytm Custom Integration </title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-api.css">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="paybox">
                        <div class="paybox_bg">
                            <h3 class="bt_title"><img src="<?php echo base_url(); ?>/backend/images/paytm.jpg" ><span>Paytm Payment Gateway</span></h3>
                            <div class="paybody">

                                <div class="">
                                    <div class="paymentbg">
                                      <div class="invtext">Course Purchase Details</div>
                                      
<div class=" paddtzero">
    <form method='post' action='<?php echo $transactionURL; ?>' name='f1'>
      <?php if(!empty($params['course_thumbnail'])){
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
                        <td><button type="submit"  name="search"   value="" class="btn btn-info buttondarkgray">Pay With Paytm <span class="bolds"><?php
                            
                            if(!empty($params['total_amount'])){ echo amountFormat($params['total_amount']);}else{echo '0.00';} ?></span></button></td>
                    </tr>  

                  </table>
                  <?php
                                                    foreach ($paytmParams as $name => $value) {
                                                        echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
                                                    }
                                                    ?>
 
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
</html>