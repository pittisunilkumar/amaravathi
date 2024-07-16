<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
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
        <form id="form1" action="<?php echo base_url() ?>admin/accounttranscation/transaction" id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
            <div class="row">

                <!-- <?php
                if ($this->rbac->hasPrivilege('fees_type', 'can_add')) {
                    ?> -->

                    <div class="col-md-6">
                        <!-- Horizontal Form -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $this->lang->line('debitaccount'); ?></h3>
                            </div><!-- /.box-header -->

                            <div class="box-body">




                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('debitaccount'); ?></label>
                                    <small class="req">*</small>
                                    <select autofocus="" id="debitaccount" name="debitaccount"
                                        class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($feetypeList as $feegroup) {
                                            ?>
                                            <option value="<?php echo $feegroup['id'] ?>" <?php
                                               if (set_value('debitaccount') == $feegroup['id']) {
                                                   echo "selected =selected";
                                               }
                                               ?>><?php echo $feegroup['name'] ?></option>

                                            <?php
                                            $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('debitaccount'); ?></span>
                                </div>




                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('date'); ?></label>
                                    <small class="req"> *</small>
                                    <input id="date" name="date" placeholder="" type="text"
                                        class="form-control date_fee"
                                        value="<?php echo date($this->customlib->getSchoolDateFormat()); ?>"
                                        readonly="readonly" />
                                    <span class="text-danger" id="date_error"></span>
                                    
                                </div>




                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('debitedamount'); ?></label>
                                    <small class="req">*</small>
                                    <input autofocus="" id="debitedamount" name="debitedamount" type="number" class="form-control" min="0"
                                        value="<?php echo set_value('debitedamount'); ?>" />
                                    <span class="text-danger"><?php echo form_error('debitedamount'); ?></span>
                                </div>




                            </div><!-- /.box-body -->

                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Horizontal Form -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $this->lang->line('creditaccount'); ?></h3>
                            </div><!-- /.box-header -->

                            <div class="box-body">
                                


                                <div class="form-group">
                                    <label
                                        for="exampleInputEmail1"><?php echo $this->lang->line('creditaccount'); ?></label>
                                    <small class="req">*</small>
                                    <select autofocus="" id="creditaccount" name="creditaccount"
                                        class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($feegroupList as $feegroup) {
                                            ?>
                                            <option value="<?php echo $feegroup['id'] ?>" <?php
                                               if (set_value('creditaccount') == $feegroup['id']) {
                                                   echo "selected =selected";
                                               }
                                               ?>><?php echo $feegroup['name'] ?></option>

                                            <?php
                                            $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('creditaccount'); ?></span>
                                </div>
                                




                            </div><!-- /.box-body -->


                        </div>
                    </div>

                <!-- <?php } ?> -->


            </div>
            <div class="row">

                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-body">

                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                <textarea class="form-control" id="description" name="description"
                                    rows="3"><?php echo set_value('description'); ?></textarea>
                                <!-- <span class="text-danger"><?php echo form_error('description'); ?></span> -->
                            </div>
                        </div><!-- /.box-body -->
                        <div class="box-footer">
                            <button type="submit"
                                class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                        </div>
                    </div>
                </div>
            </div> <!-- /.row -->

        </form>

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
                data: { 'accountcategory_id': accountcategory_id },
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    $.each(data, function (i, obj) {
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
                data: { 'accountcategory_id': accountcategory_id },
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj) {
                        div_data += "<option value=" + obj.accounttypeid + ">" + obj.accounttypename + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });
    });

</script>