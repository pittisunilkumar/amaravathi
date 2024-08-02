<?php $this->load->view('layout/cbseexam_css.php'); ?>
<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
        <div class="row">           
            <div class="col-md-12">             
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('exam_schedule_list'); ?></h3>  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-sm btn-primary" onclick="add()" data-original-title="" title="" autocomplete="off"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add')?></button> 
                                          
                                </div>               
                    </div>
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('exam_schedule_list'); ?></div>
                        <div class="table-responsive mailbox-messages">
                            <?php if ($this->session->flashdata('msgdelete')) { ?>
                                <?php echo $this->session->flashdata('msgdelete') ?>
                            <?php } ?>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('exam'); ?></th>
                                        <th><?php echo $this->lang->line('subject '); ?></th>
                                        <th><?php echo $this->lang->line('assessment_type'); ?></th>
                                        <th><?php echo $this->lang->line('code'); ?></th>
                                        <th><?php echo $this->lang->line('maximum_marks'); ?></th>
                                        <th><?php echo $this->lang->line('pass_percentage'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>                                        
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php 
                                   foreach ($result as $key => $value) {
                                    ?>
                                    <tr>
                                        <td><?php echo $value['name']?></td>
                                        <td><?php echo $value['description']?></td>
                                        <td class="mailbox-name"> <?php $sn=1;foreach ($value['data'] as $datakey => $datavalue) {
                                                    ?>
                                                    <ul class="liststyle1"><li><?php echo $datavalue['name'];?></li></ul>
                                                    
                                                    <?php 
                                                    } ?>
                                        </td>
                                        <td class="mailbox-name"> <?php $sn=1;foreach ($value['data'] as $datakey => $datavalue) {
                                                    ?>
                                                    <ul class="liststyle1"><li><?php echo $datavalue['code'];?></li></ul>
                                                    
                                                    <?php 
                                                    } ?>
                                        </td>
                                        <td class="mailbox-name"> <?php $sn=1;foreach ($value['data'] as $datakey => $datavalue) {
                                                    ?>
                                                    <ul class="liststyle1"><li><?php echo $datavalue['maximum_marks'];?></li></ul>
                                                    
                                                    <?php 
                                                    } ?>
                                        </td>
                                        <td class="mailbox-name"> <?php $sn=1;foreach ($value['data'] as $datakey => $datavalue) {
                                                    ?>
                                                    <ul class="liststyle1"><li><?php echo $datavalue['pass_percentage'];?></li></ul>
                                                    
                                                    <?php 
                                                    } ?>
                                        </td>
                                        <td class="mailbox-name"> <?php $sn=1;foreach ($value['data'] as $datakey => $datavalue) {
                                                    ?>
                                                    <ul class="liststyle1"><li><?php echo $datavalue['description'];?></li></ul>
                                                    
                                                    <?php 
                                                    } ?>
                                        </td>
                                        <td> 
                                            <a data-placement="left" onclick="edit('<?php echo $value['id']; ?>')" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a> 

                                        <a data-placement="left" href="<?php echo base_url(); ?>cbseexam/assessment/remove/<?php echo $value['id'] ?>"class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                                <i class="fa fa-remove"></i>
                                                            </a></td>
                                    </tr>
                                    <?php
                                       # code...
                                   }
                                   ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </section>
</div>

<div id="myModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" >&times;</button>
                <h4 class="modal-title" id="modal-title" ></h4>
            </div>
            <div class="scroll-area">
            <form role="form" id="form1" method="post" enctype="multipart/form-data" action="">
                <div class="modal-body">
                    <div id="delete_ides"></div>
                    <div class="row">
                        <div class="col-md-4">
                             <div class="form-group">
                            <label><?php echo $this->lang->line('exam'); ?></label><small class="req"> *</small>
                         <select class="form-control" name="exam" >
                             <option value="" > <?php echo $this->lang->line('select')?> </option>
                             <?php 
                             foreach ($exam_result as $key => $value) {
                                ?>
                                <option value="<?php echo $value['id'];?>"> <?php echo $value['name'];?></option>
                                <?php
                             }
                             ?>
                         </select>
                    </div>
                        </div>
                         <div class="col-md-4">
                             <div class="form-group">
                            <label><?php echo $this->lang->line('exam_grade'); ?></label><small class="req"> *</small>
                         <select class="form-control" name="exam" >
                             <option value="" > <?php echo $this->lang->line('select')?> </option>
                             <?php 
                             foreach ($grade_result as $key => $value) {
                                ?>
                                <option value="<?php echo $value['id'];?>"> <?php echo $value['name'];?></option>
                                <?php
                             }
                             ?>
                         </select>
                    </div>
                        </div>
                         <div class="col-md-4">
                             <div class="form-group">
                            <label><?php echo $this->lang->line('exam_assessments'); ?></label><small class="req"> *</small>
                         <select class="form-control" name="exam" >
                             <option value="" > <?php echo $this->lang->line('select')?> </option>
                             <?php 
                             foreach ($assessment_result as $key => $value) {
                                ?>
                                <option value="<?php echo $value['id'];?>"> <?php echo $value['name'];?></option>
                                <?php
                             }
                             ?>
                         </select>
                    </div>
                        </div> 
                    </div> 
                    <div class="row">
                      <div class="col-md-4 col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                                <select autofocus="" id="searchclassid" name="class_id" onchange="getSectionByClass(this.value, 0, 'secid')"  class="form-control" >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($classlist as $class) {
                                        ?>
                                        <option <?php
                                       
                                        ?> value="<?php echo $class['id'] ?>"><?php echo $class['class'] ?></option>
                                            <?php
                                        }
                                        ?>
                                </select>
                                <span class="text-danger" id="error_class_id"></span>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('section'); ?></label>
                                <select  id="secid" name="section_id" class="form-control" >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                </select>
                                <span class="section_id_error text-danger rtl-float-right cursor-pointer"></span>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('subject') . " " . $this->lang->line('group') ?></label>
                                <select  id="subject_group_id" name="subject_group_id" class="form-control" >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                </select>
                                <span class="section_id_error text-danger rtl-float-right cursor-pointer"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row" >
                        <div id="subject_list">
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                             <div class="form-group">
                            <label><?php echo $this->lang->line('overall_pass_percentage'); ?></label><small class="req"> *</small>
                         <input type="text" name="overall_pass_percentage" class="form-control">
                    </div> 
                        </div>
                         <div class="col-md-4">
                             <div class="form-group">
                            <div class="checkbox-inline">
                                <label>
                                    <input type="checkbox" value="1" name="is_publish" autocomplete="off"> <?php echo $this->lang->line('publish_result'); ?> </label>
                            </div>                        
                    </div>
                        </div>                         
                    </div>          
                    
               </div>
                <div class="modal-footer">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <button type="submit" class="btn btn-primary pull-right" data-loading-text="<?php echo $this->lang->line('submitting') ?>" value=""><?php echo $this->lang->line('save'); ?></button></div>
                </div> 
            </form>
        </div>
        </div>
    </div>
</div>

<script type="text/javascript">
(function ($) {
    "use strict"; 
    
    $(document).ready(function () {
        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });
    });
    
    $("#form1").on('submit', (function (e) {
        e.preventDefault();
        var $this = $(this).find("button[type=submit]:focus");      

        $.ajax({
            url: base_url+"cbseexam/assessment/add",
            type: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $this.button('loading');
            },
            success: function (res)
            {

                if (res.status == "fail") {
                    var message = "";
                    $.each(res.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);

                } else {

                    successMsg(res.message);
                    window.location.reload(true);

                }
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.button('reset');
            },
            complete: function () {
                $this.button('reset');
            }

        });
    }));
    
    $(document).on('change', '#secid', function () {
        var class_id = $('#searchclassid').val();
        var section_id = $(this).val();
        getSubjectGroup(class_id, section_id, 0, 'subject_group_id');
    });
    
    $(document).on('change', '#subject_group_id', function () {
        var class_id = $('#searchclassid').val();
        var section_id = $('#secid').val();
        var subject_group_id = $(this).val();
        getsubjectBySubjectGroup(class_id, section_id, subject_group_id, 0, 'subid');

    });

    function getsubjectBySubjectGroup(class_id, section_id, subject_group_id, subject_group_subject_id, subject_target) {
        if (class_id != "" && section_id != "" && subject_group_id != "") {

            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';

            $.ajax({
                type: 'POST',
                url: base_url + 'admin/subjectgroup/getGroupsubjects',
                data: {'subject_group_id': subject_group_id},
                dataType: 'JSON',
                beforeSend: function () {
                    // setting a timeout
                    $('#' + subject_target).html("").addClass('dropdownloading');
                },
                success: function (data) {
                   var subject_list="";
                    $.each(data, function (i, obj)
                    {
                        console.log(obj); 
                        subject_list+="<div class='row'><div class='col-md-4'><div class='form-group'><label>"+obj.name+"("+obj.code+")"+"</label></div></div><div class='col-md-4'><div class='form-group'><label><?php echo $this->lang->line('date'); ?></label><small class='req'> *</small><input type='text' name='overall_pass_percentage' class='form-control datetime'></div></div></div>";
                    });

                    $('#subject_list').html(subject_list);
                    
                },
                error: function (xhr) { // if error occured
                    alert("Error occured.please try again");

                },
                complete: function () {
                    $('#' + subject_target).removeClass('dropdownloading');
                }
            });
        }
    }
    
})(JQuery);

    function add(){
        $('#assessment_type').html();
        $('#myModal').modal('show');
        $('#assessment_id').val('');
        $('#modal-title').html('<?php echo $this->lang->line('add')?>');
        add_newassessmenttype();
    }
    
    function add_newassessmenttype(){
         $.ajax({
                url: '<?php echo base_url(); ?>cbseexam/assessment/add_type',
                type: "POST",
                data:{delete_string:makeid(8)},
                dataType: 'json', 
                 beforeSend: function() {
                    
                },
                success: function(res) {   
                   $('#assessment_type').append(res);
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                   
            },
            complete: function() {
                  
            }
            });
    }

    function makeid(length) {
        var result = '';
        var characters = '0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    function remove(string){
        var result = confirm("<?php echo $this->lang->line('delete_confirm') ?>");
        if (result) {
            $('#'+string).html('');
        }
    }

    function remove_edit(id){
        var result = confirm("<?php echo $this->lang->line('delete_confirm') ?>");
        if (result) {
            $('#'+id).html('');
            $('#delete_ides').append('<input type="hidden" name="delete_ides[]" value="'+id+'"/>');
        }
    } 

    function edit(assessment_id){
        $('#assessment_type').html('');
        $('#assessment_id').val(assessment_id);
        $('#delete_ides').html('');
        $('#modal-title').html('<?php echo $this->lang->line('edit')?>');
        $.ajax({
                url: '<?php echo base_url(); ?>cbseexam/assessment/get_editdetails',
                type: "POST",
                data:{id:assessment_id},
                dataType: 'json',
                 beforeSend: function() {
                    
                },
                success: function(res) {   
                  $('#name').val(res.name);
                  $('#description').val(res.description);
                    $.each(res.list, function (index, value) {

                         $.ajax({
                url: '<?php echo base_url(); ?>cbseexam/assessment/add_type',
                type: "POST",
                data:{id:value.id,delete_string:value.id},
                dataType: 'json',
                 beforeSend: function() {
                    
                },
                success: function(res) {   
                   $('#assessment_type').append(res);
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                   
            },
            complete: function() {
                  
            }
            });
            });

                     $('#myModal').modal('show');
                    $('#modal-title').html('<?php echo $this->lang->line('edit')?>');
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                   
            },
            complete: function() {
                  
            }
            });
    }    

    function getSectionByClass(class_id, section_id, select_control) {
        if (class_id != "") {
            $('#' + select_control).html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                beforeSend: function () {
                    $('#' + select_control).addClass('dropdownloading');
                },
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#' + select_control).append(div_data);
                },
                complete: function () {
                    $('#' + select_control).removeClass('dropdownloading');
                }
            });
        }
    }

    function getSubjectGroup(class_id, section_id, subjectgroup_id, subject_group_target) {
        if (class_id != "" && section_id != "") {

            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';

            $.ajax({
                type: 'POST',
                url: base_url + 'admin/subjectgroup/getGroupByClassandSection',
                data: {'class_id': class_id, 'section_id': section_id},
                dataType: 'JSON',
                beforeSend: function () {
                    // setting a timeout
                    $('#' + subject_group_target).html("").addClass('dropdownloading');
                },
                success: function (data) {

                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (subjectgroup_id == obj.subject_group_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.subject_group_id + " " + sel + ">" + obj.name + "</option>";
                    });
                    $('#' + subject_group_target).append(div_data);
                },
                error: function (xhr) { // if error occured
                    alert("Error occured.please try again");

                },
                complete: function () {
                    $('#' + subject_group_target).removeClass('dropdownloading');
                }
            });
        }
    }    
</script>