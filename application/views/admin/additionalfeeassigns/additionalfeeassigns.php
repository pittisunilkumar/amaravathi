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
                        <h3 class="box-title"><i class="fa fa-search"></i> Select Discount</h3>
                    </div>
                    
                    <div class="box-body">
                        <div class="row">
                            <form role="form" action="<?php echo site_url('admin/additionalfeeassigns/search') ?>" method="post" class="">
                                
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                                        <select autofocus="" id="class_id" name="class_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                                foreach ($classlist as $class) {
                                                    ?>
                                                <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) {
                                                    echo "selected=selected";
                                                }
                                                ?>><?php echo $class['class'] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('section'); ?></label>
                                        <select  id="section_id" name="section_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Discount Type</label><small class="req"> *</small>
                                        <select name="certificate_id" id="certificate_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            if (isset($certificateList)) {
                                                foreach ($certificateList as $list) {
                                                    ?>
                                                    <option value="<?php echo $list['id'] ?>" <?php if (set_value('certificate_id') == $list['id']) {
                                                    echo "selected=selected";
                                                }
                                            ?>><?php echo $list['name'] ?></option>
                                                    <?php
                                            }
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('certificate_id'); ?></span>
                                    </div>
                                </div>



                                

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('discount_status'); ?></label>
                                        <select  id="progress_id" name="progress_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('progress_id'); ?></span>
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
                        <form method="post" action="">
                            <div  class="" id="duefee">
                                <div class="box-header ptbnull">
                                </div>
                            
                                <div class="box-header ptbnull">
                                    <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('student_list'); ?></h3>
                                    <!-- <button style="margin-left:10px;" class="btn btn-info btn-sm disapprovalprintSelected pull-right" type="button" name="generate" title="generate multiple certificate">Disapprove</button>
                                    <button class="btn btn-info btn-sm printSelected pull-right" type="button" name="generate" title="generate multiple certificate">Approve</button> -->


                                </div>

                                <div class="box-body table-responsive overflow-visible">
                                    <div class="download_label"><?php echo $this->lang->line('student_list'); ?></div>
                                    <div class="tab-pane active table-responsive no-padding" id="tab_1">
                                        <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <!-- <th><input type="checkbox" id="select_all" /></th> -->
                                                    <th><?php echo $this->lang->line('admission_no'); ?></th>
                                                    <th><?php echo $this->lang->line('student_name'); ?></th>
                                                    <th><?php echo $this->lang->line('class'); ?></th>

                                                    <th><?php echo $this->lang->line('father_name'); ?></th>
                                                    <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                                    <th><?php echo $this->lang->line('gender'); ?></th>

                                                    <th><?php echo $this->lang->line('category'); ?></th>

                                                    <th class=""><?php echo $this->lang->line('mobile_number'); ?></th>

                                                    <th><?php echo $this->lang->line('discount_amountt'); ?></th>
                                                    <th><?php echo $this->lang->line('discount_status'); ?></th>
                                                    <th><?php echo $this->lang->line('discount_status'); ?></th>
                                                    <th class="text-center" style="text-align:center"><?php echo $this->lang->line('action'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (empty($resultlist)) {
                                                    
                                                        ?>

                                                    <?php
                                                    } else {

                                                        

                                                            $count = 1;
                                                            foreach ($resultlist as $student) {


                                                                        $hidde = 'hidden';
                                                                        if ($student['approval_status']==0) {
                                                                            $hidde = 'checkbox';
                                                                            // Change the color if the condition is true
                                                                        } 

                                                                    
                                                                        ?>
                                                                        



                                                                        
                                                        
                                                                        <tr>
                                                                            
                                                                            <td>
                                                                                <input type="hidden" name="class_id" value="<?php echo $student['fee_groups_feetypeadding_id'] ?>">
                                                                                <input type="hidden" name="std_id" value="<?php echo $student['student_session_id'] ?>">
                                                                                <input type="hidden" name="grpname" value="<?php echo $student['grpname'] ?>">
                                                                                <input type="hidden" name="type" value="<?php echo $student['type'] ?>">
                                                                                <?php echo $student['admission_no']; ?>
                                                                            </td>
                                                                            <td>
                                                                                <a href="<?php echo base_url(); ?>student/view/<?php echo $student['id']; ?>"><?php echo $this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?>
                                                                                </a>
                                                                            </td>
                                                                            <td><?php echo $student['class'] . "(" . $student['section'] . ")" ?></td>
                                                                            <td><?php echo $student['father_name']; ?></td>
                                                                            <td><?php if ($student['dob'] != '' && $student['dob'] != '0000-00-00') {echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob']));}?></td>
                                                                            <td><?php echo $this->lang->line(strtolower($student['gender'])); ?></td>
                                                                            <td><?php echo $student['category']; ?></td>
                                                                            <td><?php echo $student['mobileno']; ?></td>

                                                                            <td><?php echo $student['grpname']; ?></td>

                                                                            <td><?php echo $student['type']; ?></td>

                                                                            <td><?php echo $student['amt']; ?></td>
                                                                            

                                                                            
                                                                            
                                                                            <td class="text-center">

                                                                                <!-- <a data-toggle="modal" data-target="#myFeesModal" href="<?php echo base_url(); ?>student/edit/<?php echo $student['id']; ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                                    <i class="fa fa-pencil"></i>
                                                                                </a> -->
                                                                                <a data-toggle="modal" data-target="#myFeesModal" data-amount="<?php echo $student['amt']; ?>" data-fee_groups_feetypeadding_id="<?php echo $student['amountid']; ?>" data-studentsessionid="<?php echo $student['student_session_id'] ?>" class="btn btn-default btn-xs" title="<?php echo $this->lang->line('edit'); ?>">
                                                                                    <i class="fa fa-pencil"></i>
                                                                                </a>

                                                                                <!-- <a data-toggle="modal" data-target="#addmyFeesModal" class="btn btn-default btn-xs" title="" data-original-title="<?php echo $this->lang->line('add_fees'); ?>">
                                                                                    <?php echo $currency_symbol; ?>
                                                                                </a> -->
                                                                                
                                                                            </td>
                                                                            
                                                                            

                                                                        </tr>


                                                                        <?php
                                                                            }
                                                                    }

                                                                    
                                                                    $count++;
                                                                
                                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <?php
                        }
                        ?>
                </div>
            </div>
        </div>
    </section>
</div>























<div class="delmodal modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $this->lang->line('confirmation'); ?></h4>
            </div>
            <div class="modal-body">

                <p><?php echo $this->lang->line('are_you_sure_to_reject_discount') ?></p>

                <input type="hidden" name="main_invoice"  id="main_invoice" value="">
                <input type="hidden" name="sub_invoice" id="sub_invoice"  value="">


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <a class="btn btn-danger btn-ok"><?php echo $this->lang->line('yes'); ?></a>
            </div>
        </div>
    </div>
</div>






<div class="modal fade" id="myFeesModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title title text-center fees_title"></h4>
            </div>
            <div class="modal-body pb0">
                <div class="form-horizontal balanceformpopup">
                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('amount'); ?> (
                                <?php echo $currency_symbol; ?>)<small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <input type="hidden" autofocus="" class="form-control modal_amount" id="feegroupid_feegroupid" value="">
                                <input type="text" autofocus="" class="form-control modal_amount" id="amount_amount" value="0">
                                <span class="text-danger" id="amount_amount_error"></span>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                    <?php echo $this->lang->line('cancel'); ?>
                </button>
                <button type="button" class="btn cfees save_button" id="load" data-action="collect"
                    data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>">
                    <?php echo $currency_symbol; ?>
                    <?php echo $this->lang->line('add_fees'); ?>
                </button>
                
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">
    

    $(document).ready(function() {
        $(document).on('click', '.save_button', function() {

        var $this = $(this);
        var form = $(this).attr('frm');

        $this.button('loading');

        var feegroupid_feegroupid = $("#feegroupid_feegroupid").val();
        var amount_amount = $("#amount_amount").val();

        // alert(feegroupid_feegroupid);
        $.ajax({
            url: '<?php echo site_url("admin/additionalfeeassigns/updateadditionalfee") ?>',
            type: 'post',
            data: {
                feegroupid_feegroupid: feegroupid_feegroupid,
                amount_amount: amount_amount,
                
            },
            dataType: 'json',
            success: function (response) {
                console.log("AJAX Success Response:", response);
                $this.button('reset');
                if (response.status === "success") {
                    
                    location.reload(true);
                    
                } else if (response.status === "fail") {
                    console.log("Validation Errors:", response.error);
                    $.each(response.error, function (index, value) {
                        var errorDiv = '#' + index + '_error';
                        $(errorDiv).empty().append(value);
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log("AJAX Error:", status, error);
                alert("AJAX Error: " + status + " - " + error);
            }
        });

        });

    });


</script>



<!-- 

<div class="modal fade" id="addmyFeesModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title title text-center fees_title"></h4>
            </div>
            <div class="modal-body pb0">
                <div class="form-horizontal balanceformpopup">
                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">
                                <?php echo $this->lang->line('amount'); ?> (
                                <?php echo $currency_symbol; ?>)<small class="req"> *</small>
                            </label>
                            <div class="col-sm-9">
                                <input type="text" autofocus="" class="form-control modal_amount" id="amount" value="0">
                                <span class="text-danger" id="amount_error"></span>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                    <?php echo $this->lang->line('cancel'); ?>
                </button>
                <button type="button" class="btn cfees save_button" id="load" data-action="collect"
                    data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>">
                    <?php echo $currency_symbol; ?>
                    <?php echo $this->lang->line('add_fees'); ?>
                </button>
                
            </div>
        </div>
    </div>
</div>

 -->



















<script type="text/javascript">
    function gettypes(class_idd, section_idd) {
        if (class_idd != "" && section_idd != "") {
            $('#progress_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "admin/additionalfeeassigns/getByClass",
                data: {'class_id': class_idd},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (section_idd == obj.id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.id + " " + sel + ">" + obj.type + "</option>";
                    });
                    $('#progress_id').append(div_data);
                }
            });
        }
    }
    $(document).ready(function () {
        var class_idd = $('#certificate_id').val();
        var section_idd = '<?php echo set_value('progress_id') ?>';
        gettypes(class_idd, section_idd);
        $(document).on('change', '#certificate_id', function (e) {
            $('#progress_id').html("");
            var class_idd = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "admin/additionalfeeassigns/getByClass",
                data: {'class_id': class_idd},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        div_data += "<option value=" + obj.id + ">" + obj.type + "</option>";
                    });
                    $('#progress_id').append(div_data);
                }
            });
        });
    });
</script>









<script type="text/javascript">
    function getSectionByClass(class_id, section_id) {
        if (class_id != "" && section_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        }
    }
    $(document).ready(function () {
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);
        $(document).on('change', '#class_id', function (e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });
    });
</script>














<script type="text/javascript">
    
    
                                                                

    $(document).ready(function () {

        $('#myFeesModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });

    });
     
    
</script>







<script>
$(document).ready(function(){

    $('#myFeesModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var studentid = button.data('studentsessionid');
        var fee_groups_feetypeadding_id =button.data('fee_groups_feetypeadding_id');

        $('#amount_amount').val(button.data('amount'));
        $('#feegroupid_feegroupid').val(fee_groups_feetypeadding_id);

        // $('#amount_amount').val(fee_groups_feetypeadding_id);
        // $('#feegroupid_feegroupid').val(fee_groups_feetypeadding_id);

    });
});
</script>




