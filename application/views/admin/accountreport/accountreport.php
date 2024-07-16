<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<style>
.balance-wrapper {
    display: flex;
    justify-content: space-between; 
    margin-bottom: 20px;
}

</style>


<div class="content-wrapper">
    <section class="content-header">
        <!-- <h1><i class="fa fa-newspaper-o"></i> <?php //echo $this->lang->line('certificate'); ?></h1> -->
    </section>
    <!-- Main content -->
    <section class="content">
        <?php if ($this->session->flashdata('msg')) {?>
            <?php 
                echo $this->session->flashdata('msg');
                $this->session->unset_userdata('msg');
            ?>
        <?php }?>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> Select Accountreport</h3>
                    </div>
                    
                    <div class="box-body">
                        <div class="row">
                            <form role="form" action="<?php echo site_url('admin/accountreport/search') ?>" method="post" class="">
                                
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('accountname'); ?></label><small class="req"> *</small>
                                        <select autofocus="" id="accountname_id" name="accountname_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                                foreach ($classlist as $class) {
                                                    ?>
                                                <option value="<?php echo $class['id'] ?>" <?php if (set_value('accountname_id') == $class['id']) {
                                                    echo "selected=selected";
                                                }
                                                ?>><?php echo $class['name'] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('accountname_id'); ?></span>
                                    </div>
                                </div>

                                

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="inputEmail3">
                                            <?php echo $this->lang->line('date_from'); ?><small class="req"> *</small>
                                        </label>
                                        
                                        <input id="date_from" name="date_from" placeholder="" type="text"
                                            class="form-control date_fee"
                                            value=""
                                            readonly="readonly" />
                                        <span class="text-danger" id="date_from_error"><?php echo form_error('date_from'); ?></span>
                                        
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="inputEmail3">
                                            <?php echo $this->lang->line('date_to'); ?><small class="req"> *</small>
                                        </label>
                                        
                                        <input id="date_to" name="date_to" placeholder="" type="text"
                                            class="form-control date_fee"
                                            value=""
                                            readonly="readonly" />
                                        <span class="text-danger" id="date_to_error"><?php echo form_error('date_to'); ?></span>
                                        
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                    
                                </div>

                                

                            </form>
                        </div>
                    </div>
                













                    <?php
                    if (isset($daily_data)) {
                        $totalamount = 0;
                        ?>
                        
                            
                                
                        <div class="box-body table-responsive overflow-visible">
                            <div class="download_label"><?php echo $this->lang->line('accountreport'); ?></div>
                            <div class="tab-pane active table-responsive no-padding" id="tab_1">
                                <!-- <div>
                                    <h5>Opening Balance : <?php echo $currency_symbol . amountFormat(($openaccountbalance)); ?> </h5>
                                    <h5>Closing Balance : <?php echo $currency_symbol . amountFormat(($closeaccountblance)); ?></h5>
                                </div> -->

                                <div class="balance-wrapper">
                                    <div>
                                        <h4><b>Opening Balance : </b>
                                            <?php 
                                                if($openaccountbalance<0){
                                                    echo '-'.$currency_symbol . amountFormat(($openaccountbalance));
                                                }else{
                                                echo $currency_symbol . amountFormat(($openaccountbalance));
                                                }
                                            ?>
                                            <?php echo "(". $startdate . "  to  " . $enddate . ")";?>
                                        </h4>
                                    </div>
                                    <div>
                                        <h4><b>Closing Balance : </b>
                                            <?php 
                                            // echo $currency_symbol . amountFormat(($closeaccountblance)); 
                                            
                                                if($closeaccountblance<0){
                                                    echo '-'.$currency_symbol . amountFormat(($closeaccountblance));
                                                }else{
                                                echo $currency_symbol . amountFormat(($closeaccountblance));
                                                }
                                            ?>
                                        </h4>
                                    </div>
                                </div>
                                    


                                <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            
                                            <th><?php echo $this->lang->line('receipt_no'); ?></th>
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th><?php echo $this->lang->line('type'); ?></th>
                                            <th><?php echo $this->lang->line('status'); ?></th>
                                            <th class="text text-center"><?php echo $this->lang->line('description');?></th>
                                            <th><?php echo $this->lang->line('amount'); ?></th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (empty($daily_data)) {
                                            
                                                ?>
                                            <?php
                                                } else {
                                                    
                                                    foreach ($daily_data as $student) {
                                                        // if($student['status'] == "debit"){
                                                        //     $totalamount = $totalamount - $student['amount'];  
                                                        // }  else{
                                                        //     $totalamount = $totalamount + $student['amount'];
                                                        // } 
                                                        
                                                        $daily_total = 0;
                                                ?>

                                                <tr class="box box-solid total-bg">
                                                    <td class="text text-left"><h5>Opening Balance : <?php echo $student['opening_balance']; ?> ( <?php echo $student['date'];?> )</h5></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text text-right"><h5>Closing Balance : <?php echo $student['closing_balance']; ?></h5></td>
                                                </tr>

                                                <?php foreach($student['transactions'] as $tran) {
                                                    if($tran['status'] == "debit"){
                                                            $daily_total = $daily_total - $tran['amount'];
                                                            $totalamount = $totalamount - $tran['amount'];
                                                        }  else{
                                                            $daily_total = $daily_total + $tran['amount'];
                                                            $totalamount = $totalamount + $tran['amount'];
                                                        } 
                                                    
                                                ?>

                                                <tr>
                                                    <td><?php echo $tran['receiptid']; ?></td>
                                                    <td><?php echo $tran['date']; ?></td>
                                                    <td><?php echo $tran['type']; ?></td>
                                                    <td><?php echo $tran['status']; ?></td>
                                                    <td class="text text-center"><?php echo $tran['description']; ?></td>
                                                    <td><?php if($tran['status']=='debit'){echo "- ";}?><?php echo $currency_symbol; ?> <?php echo amountFormat(($tran['amount'])); ?></td>

                                                </tr>
                                                <?php }?>
                                                <tr class="box box-solid total-bg">
                                                    <td class="text text-right"></td>
                                                    <td class="text text-right"></td>
                                                    <td class="text text-right"></td>
                                                    <td class="text text-right"></td>
                                                    <td class="text text-right"></td>
                                                    <td class="text text-right">Daily Total : <?php echo $currency_symbol; ?> <?php 
                                                        echo $daily_total; 
                                                        // if($closeaccountblance<0){
                                                        //     echo '-'.$currency_symbol . amountFormat(($closeaccountblance));
                                                        // }else{
                                                        // echo $currency_symbol . amountFormat(($closeaccountblance));
                                                        // }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php
                                            }

                                            
                                        }                                                        
                                        ?>

                                        <tr class="box box-solid total-bg">
                                            <td class="text text-right"></td>
                                            <td class="text text-right"></td>
                                            <td class="text text-right"></td>
                                            <td class="text text-right"></td>
                                            <td class="text text-right"></td>
                                            <td class="text text-right">Total Transaction : <?php echo $currency_symbol; ?> <?php 
                                                echo $totalamount; 
                                                // if($closeaccountblance<0){
                                                //     echo '-'.$currency_symbol . amountFormat(($closeaccountblance));
                                                // }else{
                                                // echo $currency_symbol . amountFormat(($closeaccountblance));
                                                // }
                                                ?>
                                            </td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                        
                        
                    <?php
                    }
                    ?>











                </div>





            </div>
        </div>
    </section>
</div>



