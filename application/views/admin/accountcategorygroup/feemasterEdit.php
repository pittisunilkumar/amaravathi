<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php echo $this->lang->line('fees_collection'); ?></h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- <?php if ($this->rbac->hasPrivilege('fees_master', 'can_add') || $this->rbac->hasPrivilege('fees_master', 'can_edit')) { ?> -->
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('editaccountcategorygroup') . " : " . $this->setting_model->getCurrentSessionName(); ?></h3>
                        </div><!-- /.box-header -->
                        <form id="form1" action="<?php echo site_url("admin/accountcategorygroup/edit/" . $feegroup_type->id) ?>"  id="feemasterform" name="feemasterform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php 
                                        echo $this->session->flashdata('msg');
                                        $this->session->unset_userdata('msg'); 
                                    ?>
                                <?php } ?>

                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" name="id" value="<?php echo $feegroup_type->id; ?>">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('accountcategory'); ?></label><small class="req"> *</small>
                                            <select autofocus="" id="fee_groups_id" name="fee_groups_id" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php
                                                foreach ($feegroupList as $feegroup) {
                                                    ?>
                                                    <option value="<?php echo $feegroup['id'] ?>"<?php
                                                    if (set_value('fee_groups_id', $feegroup_type->accountcategory_id) == $feegroup['id']) {
                                                        echo "selected =selected";
                                                    }
                                                    ?>><?php echo $feegroup['name'] ?></option>

                                                    <?php
                                                    $count++;
                                                }
                                                ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('fee_groups_id'); ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1"><?php echo $this->lang->line('accounttype'); ?></label><small class="req"> *</small>

                                            <select  id="feetype_id" name="feetype_id" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php
                                                foreach ($feetypeList as $feetype) {
                                                    ?>
                                                    <option value="<?php echo $feetype['id'] ?>"<?php
                                                    if (set_value('feetype_id', $feegroup_type->accounttype_id) == $feetype['id']) {
                                                        echo "selected =selected";
                                                    }
                                                    ?>><?php echo $feetype['type'] ?></option>
                                                    <?php
                                                    $count++;
                                                }
                                                ?>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('feetype_id'); ?></span>
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                    </div><!-- /.box-body -->
                    </form>
                </div>
                <!-- <div class="col-md-<?php
                if ($this->rbac->hasPrivilege('fees_master', 'can_add') || $this->rbac->hasPrivilege('fees_master', 'can_edit')) {
                    echo "8";
                } else {
                    echo "12";
                }
                ?>"> -->

                <div class="col-md-8">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><?php echo $this->lang->line('editaccountcategorygroup') . " : " . $this->setting_model->getCurrentSessionName(); ?></h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="download_label"><?php echo $this->lang->line('accountcategorygroup') . " : " . $this->setting_model->getCurrentSessionName(); ?></div>
                            <div class="mailbox-messages">
                                <div class="table-responsive">  
                                    <table class="table table-striped table-bordered table-hover example">
                                        <thead>
                                            <tr>
                                                <th><?php echo $this->lang->line('accountcategory'); ?></th>
                                                <th>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <?php echo $this->lang->line('accounttype'); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <!-- <?php echo $this->lang->line('amount'); ?> -->
                                                        </div>
                                                    </div>
                                                </th>
                                                <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($feemasterList as $feegroup) {
                                                if($feegroup->feetypes){
                                                ?>
                                                <tr>
                                                    <td class="mailbox-name">
                                                        <a href="#" data-toggle="popover" class="detail_popover"><?php echo $feegroup->group_name; ?></a>
                                                    </td>
                                                    <td class="mailbox-name">
                                                        <ul class="liststyle1">
                                                            <?php
                                                            foreach ($feegroup->feetypes as $feetype_key => $feetype_value) {
                                                                ?>
                                                               <li> 
                                                                <div class="row">
                                                                    <div class="col-md-6"> 
                                                                        <i class="fa fa-money"></i>
                                                                      <?php 


                                                                echo $feetype_value->type."(".$feetype_value->code.")"; ?></div>
                                                                    <div class="col-md-3"> 
                                                                     <!-- <?php 
                                                                echo $currency_symbol.amountFormat($feetype_value->amount); ?> -->
                                                                </div>
                                                                    <div class="col-md-3"> 
                                                                        <!-- <?php if ($this->rbac->hasPrivilege('fees_master', 'can_edit')) {
                                                                 ?> -->
                                                                    <a href="<?php echo base_url(); ?>admin/accountcategorygroup/edit/<?php echo $feetype_value->id ?>"   data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                        <i class="fa fa-pencil"></i>
                                                                    </a>&nbsp;
                                                                    <!-- <?php
                                                                }
                                                                if ($this->rbac->hasPrivilege('fees_master', 'can_delete')) {
                                                                    ?> -->
                                                                    <a href="<?php echo base_url(); ?>admin/accountcategorygroup/delete/<?php echo $feetype_value->id ?>" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                                        <i class="fa fa-remove"></i>
                                                                    </a>
                                                                <!-- <?php } ?> -->
                                                            </div>
                                                                    
                                                                </div>
                                                             
                                                            </li>

                                                                <?php
                                                            }
                                                            ?>
                                                        </ul>
                                                    </td>
                                                    <td class="mailbox-date pull-right">
                                                        <!-- <?php if ($this->rbac->hasPrivilege('fees_group_assign', 'can_view')) { ?>
                                                            <a href="<?php echo base_url(); ?>admin/feemaster/assign/<?php echo $feegroup->id ?>" 
                                                               class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('assign_view_student'); ?>">
                                                                <i class="fa fa-tag"></i>
                                                            </a>
                                                        <?php } ?> -->
                                                        <!-- <?php if ($this->rbac->hasPrivilege('fees_master', 'can_delete')) { ?> -->
                                                            <a href="<?php echo base_url(); ?>admin/feemaster/deletegrp/<?php echo $feegroup->id ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                        <!-- <?php } ?> -->

                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                            ?>
                                        </tbody>
                                    </table><!-- /.table -->
                                </div>  
                            </div><!-- /.mail-box-messages -->
                        </div><!-- /.box-body -->
                        </form>
                    </div>
                </div><!--/.col (right) -->
            </div><!--/.col (right) -->
            <!-- left column -->
        <!-- <?php } ?> -->
        <!-- left column -->
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var account_type = "<?php echo set_value('account_type', $feegroup_type->fine_type); ?>";
        load_disable(account_type);
    });

    $(document).on('change', '.finetype', function () {
        calculatefine();
    });

    $(document).on('keyup', '#amount,#fine_percentage', function () {
        calculatefine();
    });

    function load_disable(account_type) {
        if (account_type === "percentage") {
            $('#fine_amount').prop('readonly', true);
            $('#fine_percentage').prop('readonly', false);
        } else if (account_type === "fix") {
            $('#fine_amount').prop('readonly', false);
            $('#fine_percentage').prop('readonly', true);
        } else {
            $('#fine_amount').prop('readonly', true);
            $('#fine_percentage').prop('readonly', true);
        }
    }

    function calculatefine() {
        var amount = $('#amount').val();
        var fine_percentage = $('#fine_percentage').val();
        var finetype = $('input[name=account_type]:checked', '#form1').val();
        if (finetype === "percentage") {
            fine_amount = ((amount * fine_percentage) / 100).toFixed(2);
            $('#fine_amount').val(fine_amount).prop('readonly', true);
            $('#fine_percentage').prop('readonly', false);
        } else if (finetype === "fix") {
            $('#fine_amount').val("").prop('readonly', false);
            $('#fine_percentage').val("").prop('readonly', true);
        } else {
            $('#fine_amount').val("");
        }
    }
</script>