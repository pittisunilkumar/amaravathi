<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php
            if ($this->rbac->hasPrivilege('fees_type', 'can_add')) {
                ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('add_account_type'); ?></h3>
                        </div><!-- /.box-header -->
                        <form id="form1" action="<?php echo base_url() ?>admin/addaccount"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php 
                                        echo $this->session->flashdata('msg'); 
                                        $this->session->unset_userdata('msg'); ?>
                                <?php } ?>
                                <?php
                                if (isset($error_message)) {
                                    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                }
                                ?>
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('accountname'); ?></label> <small class="req">*</small>
                                    <input autofocus="" id="name" name="name" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>
                                
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('accountcode'); ?></label> <small class="req">*</small>
                                    <input id="code" name="code" type="text" class="form-control"  value="<?php echo set_value('code'); ?>" />
                                    <span class="text-danger"><?php echo form_error('code'); ?></span>
                                </div>



                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('accountcategory'); ?></label> <small class="req">*</small>
                                    <select autofocus="" id="accountcategory_id" name="accountcategory_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($feegroupList as $feegroup) {
                                            ?>
                                            <option value="<?php echo $feegroup['id'] ?>"<?php
                                            if (set_value('accountcategory_id') == $feegroup['id']) {
                                                echo "selected =selected";
                                            }
                                            ?>><?php echo $feegroup['name'] ?></option>

                                            <?php
                                            $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('accountcategory_id'); ?></span>
                                </div>
                                
                                
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('accounttype'); ?></label><small class="req">*</small>
                                    <select  id="section_id" name="section_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                </div>



                                <div class="form-group">
                                    <label for="exampleInputFile"> <?php echo $this->lang->line('accountrole'); ?></label><small class="req"> *</small>
                                    <select class="form-control" name="gender">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                            foreach ($genderList as $key => $value) {
                                                ?>
                                            <option value="<?php echo $key; ?>" <?php
                                                if (set_value('gender') == $key) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php echo $value; ?></option>
                                                    <?php
                                            }
                                            ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('gender'); ?></span>
                                </div>




                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('paymentmodes'); ?></label>
                                    <small class="req"> *</small>

                                    <div class="checkbox"><label><input type="checkbox" name="sections[]" value="cash" <?php echo set_checkbox('sections[]', "cash"); ?>>Cash<label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="sections[]" value="cheque" <?php echo set_checkbox('sections[]', "cheque"); ?>>Cheque<label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="sections[]" value="dd" <?php echo set_checkbox('sections[]', "dd"); ?>>DD<label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="sections[]" value="bank_transfer" <?php echo set_checkbox('sections[]', "bank_transfer"); ?>>Bank Transfer<label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="sections[]" value="upi" <?php echo set_checkbox('sections[]', "upi"); ?>>UPI<label></div>
                                    <div class="checkbox"><label><input type="checkbox" name="sections[]" value="card" <?php echo set_checkbox('sections[]', "card"); ?>>Card<label></div>

                                    <span class="text-danger"><?php echo form_error('sections[]'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                                    <span class="text-danger"></span>
                                </div>
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('fees_type', 'can_add')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('account_type_list'); ?></h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('account_type_list'); ?></div>
                        <div class="mailbox-messages table-responsive overflow-visible">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('accountname'); ?></th>
                                        <th><?php echo $this->lang->line('code'); ?></th>
                                        <th><?php echo $this->lang->line('accountcategory'); ?></th>
                                        <th><?php echo $this->lang->line('accounttype'); ?></th>
                                        <th><?php echo $this->lang->line('accountrole'); ?></th>
                                        <th><?php echo $this->lang->line('paymentmodes'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($feetypeList as $feetype) {
                                        ?>
                                        <tr>
                                            <td class="mailbox-name">
                                                <?php echo $feetype['name']; ?>
                                            </td>
                                            <td class="mailbox-name">
                                                <?php echo $feetype['code']; ?>
                                            </td>
                                            <td class="mailbox-name">
                                                <?php echo $feetype['accountcategoryname']; ?>
                                            </td>
                                            <td class="mailbox-name">
                                                <?php echo $feetype['type']; ?>
                                            </td>
                                            <td class="mailbox-name">
                                                <?php echo $feetype['account_role']; ?>
                                            </td>
                                            <td class="mailbox-name">
                                                <?php 
                                                    $s = '';
                                                    if($feetype['cash']==1){
                                                        $s  = $s . '(cash)';
                                                    }
                                                    if($feetype['cheque']==1){
                                                        $s  = $s . '(cheque)';
                                                    }
                                                    if($feetype['dd']==1){
                                                        $s  = $s . '(dd)';
                                                    }
                                                    
                                                    if($feetype['bank_transfer']==1){
                                                        $s  = $s . '(bank_transfer)';
                                                    }
                                                    if($feetype['upi']==1){
                                                        $s  = $s . '(upi)';
                                                    }
                                                    if($feetype['card']==1){
                                                        $s  = $s . '(card)';
                                                    }
                                                    
                                                ?>
                                                <?php echo $s; ?>
                                            </td>
                                            <td class="mailbox-name">
                                                <a href="#" data-toggle="popover" class="detail_popover"><?php echo $feetype['type'] ?></a>
                                                <div class="fee_detail_popover" style="display: none">
                                                    <?php
                                                    if ($feetype['description'] == "") {
                                                        ?>
                                                        <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <p class="text text-info"><?php echo $feetype['description']; ?></p>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                            
                                            <td class="mailbox-date pull-right">
                                                <?php
                                                if ($this->rbac->hasPrivilege('fees_type', 'can_edit')) {
                                                    ?>
                                                    <a href="<?php echo base_url(); ?>admin/addaccount/edit/<?php echo $feetype['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php
                                                if ($this->rbac->hasPrivilege('fees_type', 'can_delete')) {
                                                    ?>
                                                    <a href="<?php echo base_url(); ?>admin/addaccount/delete/<?php echo $feetype['id'] ?>"class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
        <div class="row">
            <!-- left column -->
            <!-- right column -->
            <div class="col-md-12">
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });

    function getSectionByClass(accountcategory_id, section_id) {
        if (accountcategory_id != "" && section_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "admin/addaccount/getaccounttype",
                data: {'accountcategory_id': accountcategory_id},
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (section_id == obj.accounttypeid) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.accounttypeid + " " + sel + ">" + obj.accounttypename + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        }
    }


    


    $(document).ready(function () {
        var accountcategory_id = $('#accountcategory_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(accountcategory_id, section_id);
        $(document).on('change', '#accountcategory_id', function (e) {
            $('#section_id').html("");
            var accountcategory_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "admin/addaccount/getaccounttype",
                data: {'accountcategory_id': accountcategory_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        div_data += "<option value=" + obj.accounttypeid + ">" + obj.accounttypename + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });
    });



</script>