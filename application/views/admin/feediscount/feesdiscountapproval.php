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
                            <form role="form" action="<?php echo site_url('admin/feesdiscountapproval/search') ?>" method="post" class="">
                                
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
                                        <select name="certificate_id" class="form-control" >
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
                                            <select class="form-control" name="progress_id" id="progress_id">
                                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                    <?php
                                                        foreach ($progresslist as $key => $value) {
                                                            ?>
                                                        <option value="<?php echo $key; ?>" 
                                                            <?php
                                                                if (set_value('progress_id') == $key) {echo "selected";}
                                                            ?>>
                                                            <?php echo $value; ?>
                                                        </option>
                                                    <?php 
                                                }
                                                ?>
                                            </select>
                                        <span class="text-danger"><?php echo form_error('discountstatus'); ?></span>
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
                                                    <th><input type="checkbox" id="select_all" /></th>
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
                                                                        



                                                                        <?php 
                                                                            
                                                                            $discountmaount=$this->feediscount_model->getfeeamount($certificateResult['id']);
                                                                        ?>
                                                        
                                                                        <tr>
                                                                            <td class="text-center">
                                                                                
                                                                                <input type="<?php echo $hidde; ?>" class="checkbox center-block"  name="check" data-student_id="<?php echo $student['id'] ?>" value="<?php echo $student['id'] ?>">
                                                                                <input type="hidden" name="class_id" value="<?php echo $student['class_id'] ?>">
                                                                                <input type="hidden" name="std_id" value="<?php echo $student['id'] ?>">
                                                                                <input type="hidden" name="certificate_id" value="<?php echo $certificateResult['id'];?>" id="certificate_id">
                                                                            </td>
                                                                            <td><?php echo $student['admission_no']; ?></td>
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

                                                                            <td><?php echo $discountmaount['amount']; ?></td>


                                                                            

                                                                            <?php 
                                                                                if($student['approval_status']==0){
                                                                            ?>
                                                                                <td>
                                                                                    <!-- <button type="button" class="btn btn-xs btn-default myCollectFeeBtn" data-toggle="modal" data-target="#myFeesModal" data-fee-category="transport"><i class="fa fa-plus"></i></button> -->

                                                                                    <span class="label label-warning"><?php echo $this->lang->line('pending'); ?></span>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <td>
                                                                                    
                                                                                    <span style="margin-right:5px; cursor:pointer;" class="label label-success myCollectFeeBtn" onclick="approveStudent('<?php echo $student['student_session_id']; ?>', '<?php echo $discountmaount['amount']; ?>', '<?php echo $certificateResult['id']; ?>')">Approve</span>
                                                                                    </td>
                                                                                    <td>
                                                                                    <!-- <span class="label label-danger disapprove-btn">Disapprove</span> -->
                                                                                    <span class="label label-danger disapprove-btn" data-studentid="<?php echo $student['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Disapprove</span>

                                                                                    </td>
                                                                                </td>
                                                                            <?php }?>



                                                                            <?php 
                                                                                if($student['approval_status']==1){
                                                                            ?>
                                                                                <td><span class="label label-success" ><?php echo $this->lang->line('approved');?></span></td>
                                                                                <td class="text-center">----</span><td>

                                                                            <?php }?>



                                                                            <?php 
                                                                                if($student['approval_status']==2){
                                                                            ?>
                                                                                <td><span class="label label-danger"><?php echo $this->lang->line('rejected');?></span></td>
                                                                                <td class="text-center">----<td>

                                                                            <?php }?>
                                                                            

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


<!-- <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirm Delete</h4>
            </div>
            <div class="modal-body">
                Are you sure you want to disapprove student with ID: <span class="student-id"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Confirm Disapprove</button>
            </div>
        </div>
    </div>
</div> -->



<script>

// $(document).ready(function () {
//     $('#confirm-delete').on('show.bs.modal', function (e) {
//         $('.invoice_no', this).text("");
//         $('#main_invoice', this).val("");
//         $('#sub_invoice', this).val("");
//         $('.invoice_no', this).text($(e.relatedTarget).data('invoiceno'));
//         $('#main_invoice', this).val($(e.relatedTarget).data('main_invoice'));
//         $('#sub_invoice', this).val($(e.relatedTarget).data('sub_invoice'));

//         var studentID = $(this).closest('tr').find('input[name="std_id"]').val();
//         var certificateId = $("#certificate_id").val();
//         console.log("Student ID:", studentID);
//         console.log("Certificate ID:", certificateId);
//     });
// });


$(document).ready(function () {

    $('.disapprove-btn').on('click', function () {
        var studentID = $(this).data('studentid');
        var certificateId = $("#certificate_id").val();
        
        $('#confirm-delete').on('show.bs.modal', function (e) {
            $('#main_invoice',this).val(studentID);
            $('#sub_invoice',this).val(certificateId);
        });
    });


    
});



        // $('#confirm-delete').on('show.bs.modal', function (e) {
        //     $('.invoice_no', this).text("");
        //     $('#main_invoice', this).val("");
        //     $('#sub_invoice', this).val("");
        //     $('.invoice_no', this).text($(e.relatedTarget).data('invoiceno'));
        //     $('#main_invoice', this).val($(e.relatedTarget).data('main_invoice'));
        //     $('#sub_invoice', this).val($(e.relatedTarget).data('sub_invoice'));

        //     var studentID = $(this).closest('tr').find('input[name="std_id"]').val();
        //     var certificateId = $("#certificate_id").val();
        //     console.log(studentID);
        //     console.log(certificateId);

        // });


        $('#confirm-delete').on('click', '.btn-ok', function (e) {
            var $modalDiv = $(e.delegateTarget);
            var studentID = $('#main_invoice').val();
            var certificateId = $('#sub_invoice').val();
            $modalDiv.addClass('modalloading');

            $.ajax({
                url: '<?php echo site_url("admin/feesdiscountapproval/dismissapprovalsingle") ?>',
                type: 'post',
                dataType: "html",
                data: {'data': studentID,'certificate_id': certificateId},
                success: function (response) {
                    location.reload();
                }
            });
        });


</script>



























<script>
    // Function to handle the "Approve" button click
    function approveStudent(studentId,discountamt,discountid) {
        // Navigate to the new page with the student ID as a parameter
        window.location.href = '<?php echo site_url("studentfee/discountaddfee"); ?>' + '/' + studentId+'/'+discountamt+'/'+discountid;
    }
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
        $('#select_all').on('click', function () {
            if (this.checked) {
                $('.checkbox').each(function () {
                    this.checked = true;
                });
            } else {
                $('.checkbox').each(function () {
                    this.checked = false;
                });
            }
        });

        $('.checkbox').on('click', function () {
            if ($('.checkbox:checked').length == $('.checkbox').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });
    });
</script>


<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.printSelected', function () {
            var array_to_print = [];
            var classId = $("#class_id").val();
            var certificateId = $("#certificate_id").val();
            $.each($("input[name='check']:checked"), function () {
                var studentId = $(this).data('student_id');
                item = {}
                item ["student_id"] = studentId;
                array_to_print.push(item);
            });
            
            if (array_to_print.length == 0) {
                alert("<?php echo $this->lang->line('no_record_selected'); ?>");
            } else {
                $.ajax({
                    url: '<?php echo site_url("admin/feesdiscountapproval/generatemultiple") ?>',
                    type: 'post',
                    dataType: "html",
                    data: {'data': JSON.stringify(array_to_print), 'class_id': classId,'certificate_id': certificateId},
                    success: function (response) {
                        location.reload();
                    }
                });
            }
        });
    });
</script>




<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.disapprovalprintSelected', function () {
            var array_to_print = [];
            var classId = $("#class_id").val();
            var certificateId = $("#certificate_id").val();
            $.each($("input[name='check']:checked"), function () {
                var studentId = $(this).data('student_id');
                item = {}
                item ["student_id"] = studentId;
                array_to_print.push(item);
            });
            
            if (array_to_print.length == 0) {
                alert("<?php echo $this->lang->line('no_record_selected'); ?>");
            } else {
                $.ajax({
                    url: '<?php echo site_url("admin/feesdiscountapproval/dismissapprovalgeneratemultiple") ?>',
                    type: 'post',
                    dataType: "html",
                    data: {'data': JSON.stringify(array_to_print), 'class_id': classId,'certificate_id': certificateId},
                    success: function (response) {
                        location.reload();
                    }
                });
            }
        });
    });
</script>



<script type="text/javascript">

    $(document).ready(function() {
        $(document).on('click', '.approve-btn', function() {
            var studentID = $(this).closest('tr').find('input[name="std_id"]').val();
            var certificateId = $("#certificate_id").val();

            $.ajax({
                url: '<?php echo site_url("admin/feesdiscountapproval/approvalsingle") ?>',
                type: 'post',
                dataType: "html",
                data: {'data': studentID,'certificate_id': certificateId},
                success: function (response) {
                    location.reload();
                }
            });

        });
    });


</script>



<script type="text/javascript">

    // $(document).ready(function() {
    //     $(document).on('click', '.disapprove-btn', function() {
    //         var studentID = $(this).closest('tr').find('input[name="std_id"]').val();
    //         var classId = $("#class_id").val();
    //         var certificateId = $("#certificate_id").val();

    //         $.ajax({
    //             url: '<?php echo site_url("admin/feesdiscountapproval/dismissapprovalsingle") ?>',
    //             type: 'post',
    //             dataType: "html",
    //             data: {'data': studentID, 'class_id': classId,'certificate_id': certificateId},
    //             success: function (response) {
    //                 location.reload();
    //             }
    //         });

    //     });
    // });


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



<script type="text/javascript">
    

    $(document).ready(function() {
        $(document).on('click', '.save_button', function() {

        var $this = $(this);
        var action = $this.data('action');

        $this.button('loading');
        // var form = $(this).attr('frm');
        // var feetype = $('#feetype_').val();

        var date = $('#date').val();
        var student_session_id = $('#std_id').val();     //ok
        var amount = 0;
        var amount_discount = $('#amount_discount').val();  //ok
        var amount_fine = 0;
        var description = $('#description').val();

        var guardian_phone = $('#guardian_phone').val();   //ok
        var guardian_email = $('#guardian_email').val();   //ok

    

        // var student_fees_master_id = $('#student_fees_master_id').val(); //ok
        
        var fee_groups_feetype_id = $('#student_fees_master_id').val();

        // var transport_fees_id = $('#transport_fees_id').val();
        // var fee_category = $('#fee_category').val();

        var payment_mode = $('input[name="payment_mode_fee"]:checked').val(); //ok

        // var student_fees_discount_id = $('#discount_group').val();

        var studentID = student_session_id;
        var classId = $("#class_id").val();
        var certificateId = $("#certificate_id").val();



        $.ajax({
            url: '<?php echo site_url("admin/feesdiscountapproval/addstudentfee") ?>',
            type: 'post',
            dataType: 'json',
            data: {
                'student_session_id': student_session_id,
                'fee_groups_feetype_id': fee_groups_feetype_id,
                'amount':amount,
                'amount_discount':amount_discount,
                'amount_fine':amount_fine,
                'date':date,
                'description':description,
                'guardian_phone':guardian_phone,
                'payment_mode':payment_mode,
            },
            success: function(response) {
                $this.button('reset');
                if (response.status === 'success') {
                    $.ajax({
                        url: '<?php echo site_url("admin/feesdiscountapproval/approvalsingle") ?>',
                        type: 'post',
                        dataType: "html",
                        data: {'data': studentID, 'class_id': classId,'certificate_id': certificateId},
                        success: function (response) {
                            location.reload();
                        }
                    });
                    // alert('Status: ' + response.status + '\nMessage: ' + response.message);
                } else {
                    alert('Failed to add fee');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $this.button('reset');
                alert('An error occurred: ' + textStatus + ', ' + errorThrown);
            }
        });







        

        // $.ajax({
        //     url: '<?php echo site_url("feesdiscountapproval/addstudentfee") ?>',
        //     type: 'post',
        //     data: {action: action, student_session_id: student_session_id, date: date, type: feetype, amount_discount: amount_discount, amount_fine: amount_fine, description: description, student_fees_master_id: student_fees_master_id, fee_groups_feetype_id: fee_groups_feetype_id,fee_category:fee_category, transport_fees_id:transport_fees_id, payment_mode: payment_mode, guardian_phone: guardian_phone, guardian_email: guardian_email, student_fees_discount_id: student_fees_discount_id, parent_app_key: parent_app_key},
        //     dataType: 'json',
        //     success: function (response) {
        //         $this.button('reset');

        //         location.reload();


        //         // if (response.status === "success") {
        //         //     if (action === "collect") {
        //         //         location.reload(true);
        //         //     } else if (action === "print") {
        //         //         Popup(response.print, true);
        //         //     }
        //         // } else if (response.status === "fail") {
        //         //     $.each(response.error, function (index, value) {
        //         //         var errorDiv = '#' + index + '_error';
        //         //         $(errorDiv).empty().append(value);
        //         //     });
        //         // }



        //     }
        // });



        });

    });


</script>


