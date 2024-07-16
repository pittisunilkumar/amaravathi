<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper">  
    <section class="content-header">
        <h1><i class="fa fa-newspaper-o"></i> <?php //echo $this->lang->line('certificate'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php if ($this->session->flashdata('msg')) { ?>
            <?php 
                echo $this->session->flashdata('msg');
                $this->session->unset_userdata('msg');
            ?>
        <?php } ?>  
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">


                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('hall_no_check'); ?></h3>
                    </div>


                    <div class="box-body">
                        <div class="row">
                            <form role="form" action="<?php echo site_url('admin/hallticket/hallticket/search') ?>" method="post" class="">
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="col-sm-4">
                                    <div class="form-group"> 
                                        <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                                        <select autofocus="" id="class_id" name="class_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($classlist as $class) {
                                                ?>
                                                <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) echo "selected=selected" ?>><?php echo $class['class'] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                    </div>  
                                </div>


                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('section'); ?></label>
                                        <select  id="section_id" name="section_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                    </div>   
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('hall_stattus'); ?></label><small class="req">*</small>
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
                        <div  class="" id="duefee">
                            <div class="box-header ptbnull"></div>   
                            <div class="box-header ptbnull">
                                <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('student_list'); ?></h3>
                            </div>

                            <div class="box-body table-responsive overflow-visible">
                                <div class="download_label"><?php echo $this->lang->line('student_list'); ?></div>

                                <div class="tab-pane active table-responsive no-padding" id="tab_1">
                                    <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                        <thead>
                                            <tr> 
                                                
                                                <?php if (!$adm_auto_insert) { ?>
                                                    <th><?php echo $this->lang->line('admission_no'); ?></th>
                                                <?php } ?>
                                                <th><?php echo $this->lang->line('student_name'); ?></th>
                                                <th><?php echo $this->lang->line('class'); ?></th>
                                                <?php if ($sch_setting->father_name) { ?>
                                                    <th><?php echo $this->lang->line('father_name'); ?></th>
                                                <?php } ?>
                                                <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                                <th><?php echo $this->lang->line('gender'); ?></th>
                                                <?php if ($sch_setting->category) { ?>
                                                    <th><?php echo $this->lang->line('category'); ?></th>
                                                <?php } if ($sch_setting->mobile_no) { ?>
                                                    <th><?php echo $this->lang->line('mobile_number'); ?></th>
                                                <?php } ?>
                                                <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
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
                                                    ?>
                                                    <tr>
                                                        <!-- <td class="text-center"><input type="hidden" class="checkbox center-block" data-student_id="<?php echo $student['id'] ?>"  name="check" id="check" value="<?php echo $student['id'] ?>">
                                                            <input type="hidden" name="class_id" id="class_id" value="<?php echo $student['class_id'] ?>">
                                                            <input type="hidden" name="id_card_id" id="id_card_id" value="<?php echo $idcardResult[0]->id ?>">
                                                        </td> -->
                                                        
                                                        <?php if (!$adm_auto_insert) { ?>
                                                            <td><?php echo $student['admission_no']; ?></td>
                                                        <?php } ?>
                                                        <td>
                                                            <input type="hidden" name="class_id" id="class_id" value="<?php echo $student['class_id'] ?>">
                                                            <!-- <?php echo $student['id'] ?> -->
                                                            <a href="<?php echo base_url(); ?>student/view/<?php echo $student['id']; ?>"><?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo $student['class'] . "(" . $student['section'] . ")" ?></td>
                                                        <?php if ($sch_setting->father_name) { ?>
                                                            <td><?php echo $student['father_name']; ?></td>
                                                        <?php } ?>
                                                        <td>
                                                            <?php if(!empty($student['dob'])){ echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob'])); } ?>
                                                        </td>
                                                        <td><?php echo $this->lang->line(strtolower($student['gender'])); ?></td>
                                                        <?php if ($sch_setting->category) { ?>
                                                            <td><?php echo $student['category']; ?></td>
                                                        <?php } if ($sch_setting->mobile_no) { ?>
                                                            <td><?php echo $student['mobileno']; ?></td>
                                                        <?php }?>

                                                        <?php if(!$student['hallticket_status']) {?>
                                                            <td>
                                                            <!-- <a href=" . site_url('studentfee/addfee/' . $student->student_session_id) . "  class='btn btn-info btn-xs'>" .  . "</a> -->

                                                                <a class='btn btn-info btn-xs admi_no_btn' data-toggle="modal" data-target="#myAdmiEditModal" data-student_id="<?php echo $student['id']; ?>" data-student_name="<?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>"><?php echo $this->lang->line('add_hall_no');?></i></a>

                                                            </td>
                                                        <?php }else {?>
                                                            <td>
                                                            <!-- $row[] = "<a href=" . site_url('student/view/' . $student->id) . " class='btn btn-default btn-xs'> <i class='fa fa-reorder'></i></a>" . "<a href=" . site_url('student/edit/' . $student->id) . " class='btn btn-default btn-xs'> <i class='fa fa-pencil'></i></a>" ; -->

                                                                <!-- <a href="<?php echo base_url(); ?>student/view/<?php echo $student['id']; ?>"  class='btn btn-info btn-xs'><i class='fa fa-reorder'></i></a> -->
                                                               
                                                               
                                                                <a class='btn btn-info btn-xs admi_no_view' data-toggle="modal" data-target="#myAdmiviewModal" data-student_id="<?php echo $student['id']; ?>" data-student_name="<?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>" ><i class='fa fa-reorder'></i></a>

                                                                <a class='btn btn-info btn-xs admi_no_edit' data-toggle="modal" data-target="#myAdmiEditModal" data-student_id="<?php echo $student['id']; ?>" data-student_name="<?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>"><i class='fa fa-pencil'></i></a>
        
                                                            </td>
                                                        <?php }?>
                                                    </tr>
                                                    <?php
                                                    $count++;
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>                                                                           
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
<div class="response"> 
</div>



<div class="modal fade" id="myAdmiEditModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title title text-center"></h4>
                
            </div>
            <div class="modal-body pb0">
                <div class="form-horizontal balanceformpopup">
                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo $this->lang->line('student_name'); ?><small class="req"> *</small></label>
                            <div class="col-sm-9">
                                <h4 class="text-center fees_title" ></h4>
                                <input  type="hidden" class="form-control stid" id="stid" name="stid" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo $this->lang->line('hall_no'); ?><small class="req"> *</small></label>
                            <div class="col-sm-9">
                                <input  id="admi_no" name="admi_no" placeholder="" type="text" class="form-control admi_no"  value=""/>
                                <span class="text-danger admi_no_error" id="admi_no_error"></span>
                            </div>
                        </div>

                      
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="button" class="btn cfees edit_save_button" id="load" data-action="collect" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>"> <?php echo $currency_symbol; ?> <?php echo $this->lang->line('save'); ?> </button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="myAdmiviewModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title title text-center"></h4>
                
            </div>
            <div class="modal-body pb0">
                <div class="form-horizontal balanceformpopup">
                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo $this->lang->line('student_name'); ?><small class="req"> *</small></label>
                            <div class="col-sm-9">
                                <h4 class="text-center fees_title" ></h4>
                                <input  type="hidden" class="form-control stid" id="stid" name="stid" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo $this->lang->line('hall_no'); ?><small class="req"> *</small></label>
                            <div class="col-sm-9">
                                <h4 class="text-center admission_number" ></h4>
                            </div>
                        </div>

                      
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" id="load" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?> </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="myAdmiEdittModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title title text-center"></h4>
                
            </div>
            <div class="modal-body pb0">
                <div class="form-horizontal balanceformpopup">
                    <div class="box-body">

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo $this->lang->line('student_name'); ?><small class="req"> *</small></label>
                            <div class="col-sm-9">
                                <h4 class="text-center fees_title" ></h4>
                                <input  type="hidden" class="form-control stid" id="stid" name="stid" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo $this->lang->line('hall_no'); ?><small class="req"> *</small></label>
                            <div class="col-sm-9">
                                <input  id="admi_no" name="admi_no" placeholder="" type="text" class="form-control admi_no"  value=""/>
                                <span class="text-danger admi_no_error" id="admi_no_error"></span>
                            </div>
                        </div>

                      
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>
                <button type="button" class="btn cfees editt_save_button" id="load" data-action="collect" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>"> <?php echo $currency_symbol; ?> <?php echo $this->lang->line('save'); ?> </button>
            </div>
        </div>
    </div>
</div>







<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>

    $(document).ready(function () {
        $('.admi_no_btn').click(function () {
            
            var studentId = $(this).data('student_id');
            var studentname=$(this).data('student_name');
            $('#myAdmiEditModal .fees_title').text(studentname);
            $('#myAdmiEditModal .stid').val(studentId);
            $('#myAdmiEditModal .admi_no').val('');
            $('#admi_no_error').html("");
            // $('#myFeesModal .stid').attr('value', studentId);

        });
    });


    $(document).ready(function () {
    // Event listener for the admi_no buttons
        $('.admi_no_view').click(function () {
            // Get the student ID from the data-student_id attribute
            var studentId = $(this).data('student_id');
            var studentName = $(this).data('student_name');
            $('#myAdmiviewModal .fees_title').text(studentName);
            $('#myAdmiviewModal .stid').val(studentId);

            $.ajax({
                url: '<?php echo site_url("admin/hallticket/hallticket/getadmino") ?>',
                type: 'post',
                data: {
                    studentid: studentId, // Use the correct variable name
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === "success") {
                        // Set the admission number in the <h4> tag
                        $('#myAdmiviewModal h4.admission_number').text(response.admi_no);
                    } else if (response.status === "fail") {
                        alert("Error: " + response.error_message);
                    }
                },
                error: function () {
                    alert("AJAX error occurred");
                }
            });
        });
    });



    $(document).ready(function () {
        // Event listener for the admi_no buttons
        $('.admi_no_edit').click(function () {
            // Get the student ID from the data-student_id attribute
            var studentId = $(this).data('student_id');
            var studentname=$(this).data('student_name');
            $('#myAdmiEditModal .fees_title').text(studentname);
            $('#myAdmiEditModal .stid').val(studentId);

            $.ajax({
                url: '<?php echo site_url("admin/hallticket/hallticket/getadmino") ?>',
                type: 'post',
                data: {
                    studentid: studentId, // Use the correct variable name
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === "success") {
                        // Set the admission number in the <h4> tag
                        $('.admi_no').val(response.admi_no);
                    } else if (response.status === "fail") {
                        alert("Error: " + response.error_message);
                    }
                },
                error: function () {
                    alert("AJAX error occurred");
                }
            });


        });
    });

</script>








<script type="text/javascript">
    $(document).on('click', '.edit_save_button', function (e) {
        var $this = $(this);
        $this.button('loading');


        var studentid = $('#stid').val();
        var admi_no  = $('#admi_no').val();


        $.ajax({
            url: '<?php echo site_url("admin/hallticket/hallticket/addadmino") ?>',
            type: 'post',
            data: {
                studentid: studentid,
                admi_no: admi_no,
            },
            dataType: 'json',
            success: function (response) {
                $this.button('reset');
                if (response.status === "success") {
                    location.reload();
                }else if (response.status === "fail") {
                    if (response.error.admi_no) {
                        $('#admi_no_error').html(response.error.admi_no);
                    }else {
                        $('#admi_no_error').html(""); // Clear any previous error messages
                    }
                    // alert("Error: " + response.error_message);
                }
            },
            error: function () {
                alert("AJAX error occurred");
            }
        });

    });
</script>








<script type="text/javascript">

    $(document).ready(function () {
        $('#myAdmiviewModal,#myAdmiEditModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
    });


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




