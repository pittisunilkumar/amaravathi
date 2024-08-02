<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.js"></script>

<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
        <div class="row">           
            <div class="col-md-12">             
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('exam_list'); ?></h3>  
                        <div class="box-tools pull-right">
                            <?php
                              if ($this->rbac->hasPrivilege('cbse_exam', 'can_add')) { ?>
                            <button type="button" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#addExamModal" autocomplete="off"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add')?></button> 
                            <?php 
                             } 
                           ?>                                       
                        </div>               
                    </div>
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('exam_list'); ?></div>
                        <div class="table-responsive mailbox-messages">
                            <?php if ($this->session->flashdata('msgdelete')) { ?>
                                <?php echo $this->session->flashdata('msgdelete') ?>
                            <?php } ?>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>  
                                        <th><?php echo $this->lang->line('exam_name'); ?></th>
                                        <th><?php echo $this->lang->line('class'); ?> (<?php echo $this->lang->line('sections'); ?>)</th>
                                        <th><?php echo $this->lang->line('term'); ?></th>                                        
                                        <th><?php echo $this->lang->line('subjects_included'); ?></th>
                                        <th><?php echo $this->lang->line('exam_published'); ?></th>
                                        <th><?php echo $this->lang->line('published_result'); ?></th>
                                        <th width="30%"><?php echo $this->lang->line('description') ?></th>
                                        <th><?php echo $this->lang->line('created_at') ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result as $key => $value) { ?>
                                        <tr>
                                            <td><?php echo $value['name']; ?></td>
                                            <td><?php echo $value['class_sections']; ?></td>
                                            <td><?php echo $value['term_name']; ?></td>                                            
                                            <td><?php echo $value['subjectsincluded']; ?></td>                               
                                            <td><?php                                             
                                            
                                            if ($value['is_active'] == 1) {
                                                $is_active = "<i class='fa fa-check-square-o'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                                            } else {
                                                $is_active = "<i class='fa fa-exclamation-circle'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                                            }
                                            
                                            echo $is_active; 
                                            
                                            ?> </td>                                            
                                            
                                            <td><?php                                             
                                            
                                            if ($value['is_publish'] == 1) {
                                                $is_publish = "<i class='fa fa-check-square-o'></i><span style='display:none'>" . $this->lang->line('yes') . "</span>";
                                            } else {
                                                $is_publish = "<i class='fa fa-exclamation-circle'></i><span style='display:none'>" . $this->lang->line('no') . "</span>";
                                            }
                                            
                                            echo $is_publish; 
                                            
                                            ?> </td> 
                                            
                                            <td>                                                
                                                <?php echo $value['description']; ?>
                                            </td>
                                            <td><?php echo $this->customlib->dateformat($value['created_at']); ?></td> 
                                            <td class="text-right white-space-nowrap">                                           

                                                
                                                <?php  if ($this->rbac->hasPrivilege('cbse_exam_assign_view_student', 'can_view')) { ?>
                                                
                                                <button  data-toggle="tooltip" title="" class="btn btn-default btn-xs assignStudent" id="load" data-examid="<?php echo $value['id']; ?>" data-original-title="<?php echo $this->lang->line('assign_view_student'); ?>"><i class="fa fa-tag"></i></button>
                                                
                                                <?php } if ($this->rbac->hasPrivilege('cbse_exam_subjects', 'can_view')) { ?>
                                                
                                                <button class="btn btn-default btn-xs" id="subjectModalButton" data-toggle="tooltip" data-exam_id="<?php echo $value['id']; ?>" title="<?php echo $this->lang->line('exam_subjects'); ?>"><i class="fa fa-book" aria-hidden="true"></i></button>
                                                
                                                <?php } if ($this->rbac->hasPrivilege('cbse_exam_marks', 'can_view')) { ?>
                                                
                                                <button  class="btn btn-default btn-xs examMarksSubject" data-toggle="tooltip" data-recordid="<?php echo $value['id']; ?>" title="<?php echo $this->lang->line('exam_marks'); ?>" ><i class="fa fa-newspaper-o"></i></button>
                                                
                                                <?php } if ($this->rbac->hasPrivilege('cbse_exam_attendance', 'can_view')) { ?>
                                                
                                                <button class="btn btn-default btn-xs examattendance" data-toggle="tooltip" data-recordid="<?php echo $value['id']; ?>" title="<?php echo $this->lang->line('exam_attendance'); ?>"><i class="fa fa-calendar-check-o ftlayer"></i></button>
                                                
                                                <?php } if ($this->rbac->hasPrivilege('cbse_exam_teacher_remark', 'can_view')) { ?>
                                                
                                                <button  class="btn btn-default btn-xs examTeacherReamark" data-toggle="tooltip" data-recordid="<?php echo $value['id']; ?>" title="<?php echo $this->lang->line('teacher_remark'); ?>" ><i class="fa fa-comment"></i></button>
                                                
                                                <?php } if ($this->rbac->hasPrivilege('cbse_exam', 'can_edit')) { ?>
                                                
                                                <button class="btn btn-default btn-xs editexamModalButton" data-toggle="tooltip" data-exam_id="<?php echo $value['id']; ?>" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                                                
                                                <?php } ?>
                                                
                                                <?php if ($this->rbac->hasPrivilege('cbse_exam_generate_rank', 'can_view')) { ?>
                                                <a href="<?php echo site_url('cbseexam/exam/examwiserank/'.$value['id']);?>" data-toggle="tooltip" title="" class="btn btn-default btn-xs" id="load" data-examid="<?php echo $value['id']; ?>" data-original-title="<?php echo $this->lang->line('generate_rank'); ?>"><i class="fa fa-list-alt"></i></a>
                                                <?php } ?>
                                                
                                                <?php if ($this->rbac->hasPrivilege('cbse_exam', 'can_delete')) { ?>
                                                
                                                <span data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>">
                                                    <button class="btn btn-default btn-xs deleteexam" data-id="<?php echo $value['id']; ?>" data-exam="yk" id="deleteItem" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-remove"></i></button>
                                                </span>
                                                
                                                <?php } ?>
                                                
                                            </td>
                                        </tr>
                                   <?php }
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
 
<div id="addExamModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" >&times;</button>
                <h4 class="modal-title" id="modal-title"><?php echo $this->lang->line('add_exam'); ?></h4>
            </div>
            <div class="scroll-area">
            <form role="form" id="add_exam_form" method="post" enctype="multipart/form-data" action="">
                <div class="modal-body">
                    <div id="delete_ides"></div>
                    <div class="row">
         <div class="col-md-12">
            <div class="form-group" >
                <label for="exampleInputEmail1"><?php echo $this->lang->line('exam_name'); ?></label> <small class="req"> *</small>
               <input class="form-control"  name="exam_name" />   
            </div>
         </div>      
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
            <div class="checkbox-inline">
                <label>
                    <input type="checkbox" value="1" name="is_active"><?php echo $this->lang->line('publish'); ?>
                </label>
            </div>
        </div>
        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 hidden">
            <div class="checkbox-inline">
                <label>
                    <input type="checkbox" value="1" name="is_publish" autocomplete="off"> <?php echo $this->lang->line('publish_result'); ?>
                </label>
            </div>
        </div> 
    </div>
<div class="row">
    <div class="col-md-12">
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label> 
                <textarea type="text" class="form-control" name="exam_description" cols="115" rows="3"></textarea>
            </div>
        </div>
</div>
<hr/>
                    <div class="row">
                        <div class="col-md-4 col-lg-4 col-sm-6">
                    <div class="form-group">
                       
                        <label><?php echo $this->lang->line('term'); ?></label><small class="req"> *</small>
                        <select name="exam_term_id" id="exam_term_id"  class="form-control" >
                             <option value=""><?php echo $this->lang->line('select')?></option>
                             <?php 
                             foreach ($term_list as $term_key => $term_value) {
                                ?>
                                <option value="<?php echo $term_value->id;?>"> <?php echo $term_value->name." (".$term_value->term_code.")";?></option>
                                <?php
                             }
                             ?>
                         </select> 
                         <input type="hidden" name="action" id="action"  class="form-control">
                    </div>
                </div>
                    <div class="col-md-4 col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                            <select autofocus="" id="searchclassid" name="class_id" onchange="getSectionByClass(this.value, 0, 'sections')"  class="form-control" >
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
                       
      <div class="form-group relative">
        <label><?php echo $this->lang->line('section'); ?></label>
        <small class="req"> *</small>
         <div id="checkbox-dropdown-container" class="checkbox-dropdown-container">
            <div class="">
               <div class="custom-select" id="custom-select"><?php echo $this->lang->line('select'); ?></div>               
                <div id="custom-select-option-box" class="custom-select-option-box">
                    <div class="custom-select-option checkbox">
                        <label class="vertical-middle line-h-18">
                            <input  class="custom-select-option-checkbox" type="checkbox"  name="select_all" id="select_all"> <?php echo $this->lang->line('select_all'); ?> 
                        </label> 
                    </div>                  
                </div>
            </div>
          </div>
      <span class="text-danger" id="error_class_id"></span>
    </div>
                    </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('assessment'); ?></label><small class="req"> *</small>
                            <select autofocus="" id="searchclassid" name="assessment_id"   class="form-control" >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($assessment_result as $assessment) {
                                        ?>
                                        <option <?php
                                       
                                        ?> value="<?php echo $assessment['id'] ?>"><?php echo $assessment['name'] ?></option>
                                            <?php
                                        }
                                        ?>
                            </select>
                            <span class="text-danger" id="error_class_id"></span>
                        </div>                        
                    </div>
                     <div class="col-md-4 col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('grade'); ?></label><small class="req"> *</small>
                            <select autofocus="" id="searchclassid" name="grade_id"   class="form-control" >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($grade_result as $grade) {
                                        ?>
                                        <option <?php
                                       
                                        ?> value="<?php echo $grade['id'] ?>"><?php echo $grade['name'] ?></option>
                                            <?php
                                        }
                                        ?>
                            </select>
                            <span class="text-danger" id="error_class_id"></span>
                        </div>                        
                    </div>
                    </div>                    
                                    
                </div>
                <div class="modal-footer">                    
                    <button type="submit" class="btn btn-primary pull-right" data-loading-text="<?php echo $this->lang->line('submitting') ?>" value=""><?php echo $this->lang->line('save'); ?></button>
                </div> 
            </form>
        </div>
        </div>
    </div>
</div>

<div id="allotStudentModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <?php echo $this->lang->line('assign_view_student') ?></h4>
            </div>
            <div class="modal-body">                
                <div id="studentAllotForm">

                </div>
            </div>
        </div>
    </div>
</div> 

<div id="addSubject" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_exam_subject') ?></h4>
            </div>
            <div class="modal-body minheight260">

                <div class="modal_loader_div" style="display: none;"></div>

                <div class="modal-body-inner">
                     
                </div>

            </div>
        </div>
    </div>
</div>

<div id="subjectmarkModal" class="modal fade modalmark" role="dialog">
    <div class="modal-dialog modal-xl">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <?php echo $this->lang->line('exam_subjects'); ?></h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<div id="observationModal" class="modal fade modalmark" role="dialog">
    <div class="modal-dialog modal-xl">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <?php echo $this->lang->line('exam_attendance'); ?></h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<div id="subjectModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title subjectmodal_header"></h4>
            </div>
            <div class="modal-body">               
                <div class="examheight100 relative">
                    <div id="examfade"></div>
                    <div id="exammodal">
                        <img id="loader" src="<?php echo base_url() ?>/backend/images/loading_blue.gif" />
                    </div>
                    <div class="marksEntryForm">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  -->
<div id="observationParameterModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">        
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title subjectmodal_header"></h4>
            </div>
            <div class="modal-body">               
                <div class="examheight100 relative">
                    <div id="examfade"></div>
                    <div id="exammodal">
                        <img id="loader" src="<?php echo base_url() ?>/backend/images/loading_blue.gif" />
                    </div>
                    <div class="marksObservationEntryForm">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="teacherRemarkModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('teacher_remark') ; ?></h4>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>

<div id="editexamModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" >&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_exam') ; ?></h4>
            </div>
            <div class="scroll-area">
            <form role="form" id="editform" method="post" enctype="multipart/form-data" action="">
     <div class="modal-body minheight260"> 

                <div class="modal_loader_div" style="display: none;"></div>

                <div class="modal-body-inner">
                     
                </div>

            </div>

            </form>
        </div>
        </div>
    </div>
</div>

<script type="text/javascript">      

    var x = 1;

(function ($){
    "use strict";
       var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy','M'=>'MM']) ?>';

    var date_format_time = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'DD', 'm' => 'MM', 'Y' => 'YYYY','M'=>'MMM']) ?>'; 
    
    $(document).ready(function () {
modal_click_disabled('subjectModal','addExamModal','observationParameterModal','teacherRemarkModal','allotStudentModal','addSubject','editexamModal','observationModal','subjectmarkModal');
   
    });
    
    $('#observationParameterModal').on('shown.bs.modal', function (e) {
        var exam_student_id = $(e.relatedTarget).data('exam_student_id');
        var exam_id=$(e.relatedTarget).data('exam_id');
        var student_name=$(e.relatedTarget).data('student_name');
        $('.subjectmodal_header').html("").html(student_name);
        $('.marksEntryForm').html(""); 
        
        $.ajax({ 
            type: 'POST',
            url: baseurl + "cbseexam/exam/get_observation_parameter",
            data: {exam_id:exam_id,exam_student_id:exam_student_id},
            dataType: 'JSON',
            beforeSend: function () {
               
            },
            success: function (data) {                
                $('.marksEntryForm').html(data.page);
            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");              
            },
            complete: function () {
                
            }
        });
    }) 
    
    $('#subjectModal').on('shown.bs.modal', function (e) {
        var subject_id = $(e.relatedTarget).data('subject_id');
        var subject_name = $(e.relatedTarget).data('subject_name');
        var timetable_id = $(e.relatedTarget).data('timetable_id');
        var exam_id=$(e.relatedTarget).data('exam_id');
        
        $('.subjectmodal_header').html("").html('Enter ' + subject_name + ' Marks');
        $('.marksEntryForm').html(""); 
        $('.subject_id').val("").val(subject_id);

        $(e.currentTarget).find('input[name="subject_name"]').val(subject_name);
        var current_session = $('#current_session').val();
        $('#session_id option[value="'+current_session+'"]').prop("selected", true);
        $.ajax({ 
            type: 'POST',
            url: baseurl + "cbseexam/exam/subjectstudent",
            data: {exam_id:exam_id,subject_id:subject_id,timetable_id:timetable_id},
            dataType: 'JSON',
            beforeSend: function () {
               
            },
            success: function (data) {
                
                $('.marksEntryForm').html(data.page);
            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");
              
            },
            complete: function () {
                
            }
        });
    })

    $('#subjectModal').on('hidden.bs.modal', function () {
        $('.subjectmodal_header').html("");
        $('.marksEntryForm').html("");
        $('.subject_id').val("");
        $("#searchStudentForm").find('input:text,select,textarea').val('');
        $('#section_id').find('option').not(':first').remove();
        $('#session_id > option[selected="selected"]').removeAttr('selected'); 
    });

    $('#addExamModal,#editexamModal').on('hidden.bs.modal', function () {
           reset_form('#add_exam_form');
           $('.custom-select-option-box').closest('div').find("input[name='select_all']").attr('checked', false);
           $('.custom-select-option-box').children().not(':first').remove();           
           $("input[name=use_exam_roll_no][value='1']").prop("checked",true);
    });
    
    $(document).on('click', '.select_all', function (e) {
        if (this.checked) {
            $(this).closest('div.table-responsive').find('[type=checkbox]').prop('checked', true);
        } else {
            $(this).closest('div.table-responsive').find('[type=checkbox]').prop('checked', false);
        }
    });    
 
    $(document).on('submit', 'form#allot_exam_student', function (e) {
        e.preventDefault(); // avoid to execute the actual submit of the form.
        var form = $(this);
        var $this = form.find("button[type=submit]:focus");
        var url = form.attr('action');
        $.ajax({
            url: url,
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
                if (res.status == 1) {
                    successMsg(res.message);
                    $('#allotStudentModal').modal('hide');

                } else {
                    errorMsg(res.message);
                }

                $this.button('reset');
            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");
                $this.button('reset');
            },
            complete: function () {
                $this.button('reset');
            }

        });
    }
    );

    $(document).on('click', '.examMarksSubject', function () {
        var $this = $(this);
        var recordid = $this.data('recordid');
        $('input[name=recordid]').val(recordid);
        
        $.ajax({
            type: 'POST',
            url: baseurl + "cbseexam/exam/getSubjectByExam",
            data: {'recordid': recordid},
            dataType: 'JSON',
            beforeSend: function () {
                $this.button('loading');
            },
            success: function (data) {
                $('#subjectmarkModal .modal-body').html(data.subject_page);
                $('#subjectmarkModal').modal('show');
                $this.button('reset');
            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");
                $this.button('reset');
            },
            complete: function () {
                $this.button('reset');
            }
        });
    });

    $(document).on('click', '.examattendance', function () {
        var $this = $(this);
        var recordid = $this.data('recordid');
        $('input[name=recordid]').val(recordid);
        
        $.ajax({
            type: 'POST',
            url: baseurl + "cbseexam/exam/exam_attendance",
            data: {'exam_id': recordid},
            dataType: 'JSON',
            beforeSend: function () {
                $this.button('loading');
            },
            success: function (data) {
                $('#observationModal .modal-body').html(data.page);
                $('#observationModal').modal('show');
                $this.button('reset');
            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");
                $this.button('reset');
            },
            complete: function () {
                $this.button('reset');
            }
        });
    });    
        
    $(document).on('click', '.examTeacherReamark', function () {
        var $this = $(this);
        $('#teacherRemarkModal').modal('show');
        var recordid = $this.data('recordid');
        $.ajax({ 
            type: 'POST',
            url: baseurl + "cbseexam/exam/teacherRemark",
            data: {exam_id:recordid},
            dataType: 'JSON',
            beforeSend: function () {
               
            },
            success: function (data) {
                
                $('#teacherRemarkModal .modal-body').html(data.page);
            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");
              
            },
            complete: function () {
                
            }
        });
    });    
 
    $(document).on('click', '.assignStudent', function () {
        var $this = $(this);
        var examid = $(this).data('examid');
        
        $('#allotStudentModal').modal('show');
        $.ajax({ 
            type: 'POST',
            url: baseurl + "cbseexam/exam/examstudent",
            data: {examid:examid},
            dataType: 'JSON',
            beforeSend: function () {
                $this.button('loading');
            },
            success: function (data) {                
                $('#studentAllotForm').html(data.page);
                $('#allotStudentModal').modal('show');
                $this.button('reset');
            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");
                $this.button('reset');
            },
            complete: function () {
                $this.button('reset');
            }
        });
    });

    $("#add_exam_form").on('submit', (function (e) {
        e.preventDefault();
        var $this = $(this).find("button[type=submit]:focus");      

        $.ajax({
            url: base_url+"cbseexam/exam/add",
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

    $(document).ready(function(){
        $("#exam_term_id").on('change',function(){            
        var term_id = $(this).val();
        var base_url = '<?php echo base_url() ?>';
            if(term_id!=''){
            $.ajax({
                type: "GET",
                url: base_url + "cbseexam/term/get_ClassSectionByTermId/"+term_id,
                dataType: "json",
                beforeSend: function () {
                     
                },
                success: function (data) {
                   
                   $('#searchclassid').val(data.class_id);
                   getSectionByClass(data.class_id, '', 'sections');
                },
                complete: function () {
                  
                }
            });
            }else{
                $('#searchclassid').val('');
                $('#sections').html('');
            }
        });
    });    

    var batch_subjects = "";
    $(document).on('click', '#subjectModalButton', function (e) {
            
             x = 1;
            var class_batch_id = $(this).data('class_batch_id');
            var exam_id = $(this).data('exam_id');
            var exam_group_id = $('#examgroup_id').val();
          
            $.ajax({
                type: "POST",
                url: baseurl + "cbseexam/exam/getexamSubjects",
                data: {'exam_group_id': exam_group_id, 'class_batch_id': class_batch_id, 'exam_id': exam_id},
                dataType: "json",
                beforeSend: function () {

                        $('#addSubject .modal-body .modal-body-inner').html(""); 
                        $('#addSubject .modal-body .modal_loader_div').css("display", "block"); 
                        $('#addSubject').modal('show');

                },
                success: function (data) {

                    var s = data.subject_page;

                          $('#addSubject .modal-body .modal-body-inner').html(data.subject_page); 
                          $('#addSubject .modal-body .modal_loader_div').fadeOut(400);

                    var tmp_row = $('#item_table');

                    $('.datepicker_init', tmp_row).datetimepicker({
                        format: date_format_time,
                        showTodayButton: true,
                        ignoreReadonly: true
                    });

                    $('.datepicker_init_time', tmp_row).datetimepicker({
                        format: 'HH:mm:ss',
                        showTodayButton: true,
                        ignoreReadonly: true
                    });

                    batch_subjects = data.batch_subject_dropdown;
                    if (data.exam_subjects_count > 0) {
                        x = data.exam_subjects_count + 1;
                    }

                },
                complete: function () {
                       
                 $('#addSubject .modal-body .modal_loader_div').fadeOut(400);
                }
            });
    });
        
    $(document).on('click', '.add', function () {

        var html = '';           
        
        html += '<tr>';
        html += '<td ><select name="subject_' + x + '" class="form-control item_unit tddm200">' + batch_subjects + '</select></td>';
        html += '<td><div class="input-group datepicker_init"><input type="text" name="date_from_' + x + '" class="form-control"/><span class="input-group-addon" id="basic-addon2"><i class="fa fa-calendar"></i></span></div></td>';
        html += '<td><div class="input-group datepicker_init_time"><input type="text" name="time_from' + x + '" class="form-control"/><span class="input-group-addon" id="basic-addon2"><i class="fa fa-clock-o"></i></span></div></td>';
        html += '<td><input type="text" name="duration' + x + '" class="form-control duration" value="0"/></td>';       
        html += '<td class="" ><input type="text" name="room_no_' + x + '" class="form-control room_no" /><input type="hidden" name="rows[]" value="' + x + '"> <input name="prev_row[' + x + ']" type="hidden" value="0"></td>';      
        html += '<td class="text-center" ><span class="text text-danger remove fa fa-times mt5"></span></td></tr>';
        var tmp_row = $('#item_table').append(html);

        $('.datepicker_init', tmp_row).datetimepicker({
            format: date_format_time,
            showTodayButton: true,
            ignoreReadonly: true
        });

        $('.datepicker_init_time', tmp_row).datetimepicker({
            format: 'HH:mm:ss',
            showTodayButton: true,
            ignoreReadonly: true
        });
        x++;
    });

    $(document).on('click', '.remove', function () {
        $(this).closest('tr').remove();
    });

    $('#insert_form').on('submit', function (event) {
        event.preventDefault();
        var error = '';
        $('.item_name').each(function () {
            var count = 1;
            if ($(this).val() == '')
            {
                error += "<p>Enter Item Name at " + count + " Row</p>";
                return false;
            }
            count = count + 1;
        });

        $('.item_quantity').each(function () {
            var count = 1;
            if ($(this).val() == '')
            {
                error += "<p>Enter Item Quantity at " + count + " Row</p>";
                return false;
            }
            count = count + 1;
        });

        $('.item_unit').each(function () {
            var count = 1;
            if ($(this).val() == '')
            {
                error += "<p>Select Unit at " + count + " Row</p>";
                return false;
            }
            count = count + 1;
        });
        var form_data = $(this).serialize();
        if (error == '')
        {
            $.ajax({
                url: "insert.php",
                method: "POST",
                data: form_data,
                success: function (data)
                {
                    if (data == 'ok')
                    {
                        $('#item_table').find("tr:gt(0)").remove();
                        $('#error').html('<div class="alert alert-success">Item Details Saved</div>');
                    }
                }
            });
        } else
        {
            $('#error').html('<div class="alert alert-danger">' + error + '</div>');
        }
    }); 
    
    $(document).on('submit', '.ssaddSubject', function (e) {

        e.preventDefault();
        var form = $(this);
        var subsubmit_button = $(this).find(':submit');
        var formdata = form.serializeArray();

        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: formdata, // serializes the form's elements.
            dataType: "JSON", // serializes the form's elements.
            beforeSend: function () {
                subsubmit_button.button('loading');
            },
            success: function (response)
            {
                if (response.status == 0) {
                    var message = "";
                    $.each(response.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
 
                } else {                  
                    successMsg(response.message);
                    $('#addSubject').modal('hide');
                }
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occured').", ".$this->lang->line('please_try_again')?>");
                subsubmit_button.button('reset');
            },
            complete: function () {
                subsubmit_button.button('reset');
            }
        });
    });

    $(document).ready(function () {

    $.validator.addMethod("uniqueUserName", function (value, element, options)
    {
       var max_mark = element.getAttribute('data-marks');   
        //we need the validation error to appear on the correct element
        return parseFloat(value) <= parseFloat(max_mark);
    },
            "Invalid Marks"
            );

        $(document).on('submit', 'form#assign_form1111', function (event) {
            event.preventDefault();

               $('form#assign_form1111').validate({
    debug: true,
    errorClass: 'error text text-danger',
    validClass: 'success',
    errorElement: 'span',
    highlight: function(element, errorClass, validClass) {
       $(element).addClass(errorClass);
    },
    unhighlight: function(element, errorClass, validClass) {
      $(element).removeClass(errorClass);
    }
});
            $('.mark').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                            uniqueUserName: true,
                            messages: {
                                required: "<?php echo $this->lang->line('required'); ?>",
                            }
                        });
            });


// test if form is valid
            if ($('form#assign_form1111').validate().form()) {
    var form = $(this);
        var subsubmit_button = $(this).find(':submit');
        var $this = $(this).find("button[type=submit]:focus");     

        $.ajax({
            
            url: form.attr('action'),
            type: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                subsubmit_button.button('loading');
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
                    $('#subjectModal').modal('hide');

                }
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                subsubmit_button.button('reset');
            },
            complete: function () {
                subsubmit_button.button('reset');
            }
        });       

                  } else {
                console.log("does not validate");
            }
    
        })         

    });

    $('.editexamModalButton').click(function(){
        var exam_id = $(this).attr('data-exam_id');
        $.ajax({
            url: baseurl+'cbseexam/exam/get_exam',
            type: "POST",
            data: {exam_id:exam_id,delete_string:makeid(8)},
            dataType: 'json',
             beforeSend: function () {
                        $('#editexamModal .modal-body .modal-body-inner').html(""); 
                        $('#editexamModal .modal-body .modal_loader_div').css("display", "block"); 
                        $('#editexamModal').modal('show');
                    },
                    success: function (data)
                    {
                          $('#editexamModal .modal-body .modal-body-inner').html(data.page); 
                          $('#editexamModal .modal-body .modal_loader_div').fadeOut(400);
                           
                    },
                    error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                 
                    },

         complete: function () {
            
                    }

        });
    })

    $("#editform").on('submit', (function (e) {
        e.preventDefault();
        var $this = $(this).find("button[type=submit]:focus");
        $.ajax({
            url: base_url+"cbseexam/exam/edit",
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

    $('.deleteexam').click(function(){
        var exam_id = $(this).attr('data-id');
        if(confirm('<?php echo $this->lang->line('delete_confirm'); ?>')){
            $.ajax({
                url: base_url+'cbseexam/exam/deleteexam',
                type: "POST",
                data: {exam_id:exam_id},
                dataType: 'json',
                success: function (res)
                {
                    successMsg(res.message);
                    window.location.reload(true);
                }
            });
        }
    })
    
})(jQuery);
</script>
<script>
    
    function add_exam(){
        $.ajax({
                url: base_url+'cbseexam/exam/add_exam',
                type: "POST",
                data:{delete_string:makeid(8)},
                dataType: 'json', 
                beforeSend: function() {
                    
                },
                success: function(res) {   
                   $('#exam_result').html(res);
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

    function remove(string)
    {
        var result = confirm("<?php echo $this->lang->line('delete_confirm') ?>");
        if (result) {
            $('#'+string).html('');
        }
    }

    function remove_edit(id)
    {
        var result = confirm("<?php echo $this->lang->line('delete_confirm') ?>");
        if (result) {
            $('#'+id).html('');
            $('#delete_ides').append('<input type="hidden" name="delete_ides[]" value="'+id+'"/>');
        }
    } 

    function edit(exam_term_id){
        $('#action').val('edit');
        $('#exam_result').html('');
        $('#exam_term_id').val(exam_term_id);
        $('#delete_ides').html('');
        $('#modal-title').html('<?php echo $this->lang->line('edit')?>');
        
        $.ajax({
                url: base_url+'cbseexam/exam/get_editdetails',
                type: "POST",
                data:{id:exam_term_id},
                dataType: 'json',
                 beforeSend: function() {
                    
                },
                success: function(res) {   
                 
                    $.each(res.list, function (index, value) {

                         $.ajax({
                url: base_url+'cbseexam/exam/add_exam',
                type: "POST",
                data:{id:value.id,delete_string:value.id},
                dataType: 'json',
                beforeSend: function() {
                    
                },
                success: function(res) {   
                   $('#exam_result').append(res);
                },
                error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");                   
                },
                complete: function() {
                      
                }
            });
            });

                    $('#addExamModal').modal('show');
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
            var div_data = '';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                beforeSend: function () {
                    $('.custom-select-option-box').closest('div').find("input[name='select_all']").attr('checked', false);
                    $('.custom-select-option-box').children().not(':first').remove();
                },
                success: function (data) {                 

                    $.each(data, function (i, obj)
                    {
                        var s=  $('<div>', {   
                            class: 'custom-select-option checkbox',
                        }).append($('<label>', {   
                            class: 'vertical-middle line-h-18',
                        }).append($('<input />', {   
                            class: 'custom-select-option-checkbox',
                            type: 'checkbox',
                            name:"section[]",
                            val:obj.id
                        })).append(obj.section));

                   $('.custom-select-option-box').append(s);
                     
                    });
                    
                },
                complete: function () {
                   
                }
            });
        }else{
            $('#sections').html('');
        }
    }  

</script>
<script>
    $(document).on('click',".custom-select",function(){
         $(".custom-select-option-box").toggle();
    });    

    $(".custom-select-option").on("click", function(e) {
        var checkboxObj = $(this).children("input");
        if($(e.target).attr("class") != "custom-select-option-checkbox") {
                if($(checkboxObj).prop('checked') == true) {
                    $(checkboxObj).prop('checked',false)
                } else {
                    $(checkboxObj).prop("checked",true);
                }
        }
    });

$(document).on('click', function(event) {
  if (event.target.className != "custom-select" && !$(event.target).closest('div').hasClass("custom-select-option")  ) {
          $(".custom-select-option-box").hide();
     }
});

$(document).on('change','#select_all',function(){   
        $('input:checkbox',$('.checkbox-dropdown-container')).not(this).prop('checked', this.checked);
});

$(document).on('change','.check_absent',function(){
  if(this.checked) {
    $(this).closest('td').find("input.mark").val(0).attr('readonly', true);
  }else{
    $(this).closest('td').find("input.mark").val(0).attr('readonly', false);
  }
});
</script>