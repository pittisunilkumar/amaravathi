<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>


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
                        
                        <a href="<?php echo base_url(); ?>admin/accounttranscationreport/addfinaceyear" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-plus"></i> <?php echo $this->lang->line('addfinaceyear'); ?></a>
                    
                    
                    </div>
                    
                    <div class="box-body">
                        <div class="row">
                            <form role="form" action="<?php echo site_url('admin/accounttranscationreport/search') ?>" method="post" class="">
                                
                                <?php echo $this->customlib->getCSRF(); ?>

                                <!-- <div class="col-sm-3">
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
                                </div> -->

                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="inputEmail3">
                                            <?php echo $this->lang->line('date_from'); ?><small class="req"> *</small>
                                        </label>
                                        
                                        <!-- <input id="date_from" name="date_from" placeholder="" type="text"
                                            class="form-control date_fee"
                                            value="<?php echo date($this->customlib->getSchoolDateFormat()); ?>"
                                            readonly="readonly" /> -->
                                        <input id="date_from" name="date_from" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_from') ?>" autocomplete="off">

                                        <!-- <span class="text-danger" id="date_from_error"></span> -->
                                        <span class="text-danger"><?php echo form_error('date_from'); ?></span>
                                        
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="inputEmail3">
                                            <?php echo $this->lang->line('date_to'); ?><small class="req"> *</small>
                                        </label>
                                        
                                        <!-- <input id="date_to" name="date_to" placeholder="" type="text"
                                            class="form-control date_fee"
                                            value="<?php echo date($this->customlib->getSchoolDateFormat()); ?>"
                                            readonly="readonly" /> -->
                                        <input id="date_to" name="date_to" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date_to') ?>" autocomplete="off">

                                        <!-- <span class="text-danger" id="date_to_error"></span> -->
                                        <span class="text-danger"><?php echo form_error('date_to'); ?></span>
                                        
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
                    if (isset($resultlist)) {
                        ?>
                        
                            
                                
                        <div class="box-body table-responsive overflow-visible">
                            <div class="download_label"><?php echo $this->lang->line('accountreport'); ?></div>
                            <div class="tab-pane active table-responsive no-padding" id="tab_1">
                                <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            
                                            <th><?php echo $this->lang->line('receipt_no'); ?></th>
                                            <th><?php echo $this->lang->line('fromaccount'); ?></th>
                                            <th><?php echo $this->lang->line('toaccount'); ?></th>
                                            <th><?php echo $this->lang->line('amount'); ?></th>
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th class="text text-center"><?php echo $this->lang->line('description');?></th>
                                            <th><?php echo $this->lang->line('action'); ?></th>
                                            
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (empty($resultlist)) {
                                            $totalamount = 0;
                                                ?>
                                            <?php
                                                } else {
                                                    foreach ($resultlist as $student) {
                                                        // if($student['status'] == "debit"){
                                                        //     $totalamount = $totalamount - $student['amount'];  
                                                        // }  else{
                                                            $totalamount = $totalamount + $student['amount'];
                                                        // }                                  
                                                ?>
                                                <tr>
                                                    <td><?php echo $student['id']; ?></td>
                                                    <td>
                                                        <?php 
                                                            $fromaccount = $this->addaccount_model->getaddedaccount($student['fromaccountid']);
                                                            echo $fromaccount['name']; 
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            $toaccount = $this->addaccount_model->getaddedaccount($student['toaccountid']);
                                                            echo $toaccount['name'];
                                                        ?>
                                                    </td>
                                                    <td><?php echo $student['amount']; ?></td>
                                                    <td><?php echo $student['date']; ?></td>
                                                    <td><?php echo $student['note']; ?></td>
                                                    <td>
                                                        <a href="<?php echo base_url(); ?>admin/accounttranscationreport/delete/<?php echo $student['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
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
                                            <td class="text text-right"></td>
                                            <td class="text text-right">Total : <?php echo $currency_symbol; ?> <?php echo $totalamount; ?></td>
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



