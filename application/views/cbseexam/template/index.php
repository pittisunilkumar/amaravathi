<script src="<?php echo base_url(); ?>backend/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>backend/js/ckeditor_config.js"></script>
<?php $this->load->view('layout/cbseexam_css.php'); ?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('template_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('cbse_exam_template', 'can_add')) { ?>
                                <button type="button" class="btn btn-sm btn-primary" onclick="add()" data-original-title="" title="" autocomplete="off"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></button>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="sss">
                        </div>
                        <div class="download_label"><?php echo $this->lang->line('template_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <?php if ($this->session->flashdata('msgdelete')) { ?>
                                <?php echo $this->session->flashdata('msgdelete') ?>
                            <?php } ?>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('template'); ?></th>
                                        <th><?php echo $this->lang->line('class_sections'); ?></th>
                                        <th width="60%"><?php echo $this->lang->line('template_description'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($result as $key => $value) {
                                    ?>
                                        <tr>
                                            <td><?php echo $value['name'] ?></td>
                                            <td><?php echo $value['class_sections'] ?></td>
                                            <td><?php echo $value['description'] ?></td>
                                            <td>                                    


                                                <?php if ($this->rbac->hasPrivilege('cbse_exam_template', 'can_view')) { ?>
                                                    <button type="button" class="btn btn-default btn-xs view_template" id="load" data-toggle="tooltip" data-recordid="<?php echo $value['id']; ?>" data-temp_name="<?php echo $value['name']; ?>" title="<?php echo $this->lang->line('view'); ?>" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-reorder"></i>
                                                    </button>

                                                <?php }
                                                if ($this->rbac->hasPrivilege('cbse_exam_link_exam', 'can_view')) { ?>

                                                    <button type="button" class="btn btn-default btn-xs linkexam" id="load" data-toggle="tooltip" data-recordid="<?php echo $value['id']; ?>" data-is_weightage="<?php echo $value['is_weightage']; ?>" data-marksheet_type="<?php echo $value['marksheet_type']; ?>" title="<?php echo $this->lang->line('link_exam'); ?>" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-newspaper-o"></i></button>

                                                <?php }
                                                if ($this->rbac->hasPrivilege('cbse_exam_template', 'can_edit')) { ?>

                                                    <a onclick="edit('<?php echo $value['id']; ?>')" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>"><i class="fa fa-pencil"></i></a>

                                                <?php }  ?>
                                                
                                                <?php if ($this->rbac->hasPrivilege('cbse_exam_generate_rank', 'can_view')) { ?>
                                                    <a href="<?php echo site_url('cbseexam/template/templatewiserank/'.$value['id']);?>" class="btn btn-default btn-xs"  data-toggle="tooltip"  title="<?php echo $this->lang->line('generate_rank'); ?>" data-loading-text="<i class='fa fa-spinner fa-spin'></i>"><i class="fa fa-list-alt"></i></a>                                                
                                                <?php } ?>
                                                <?php if ($this->rbac->hasPrivilege('cbse_exam_template', 'can_delete')) { ?>

                                                    <a data-id="<?php echo $value['id'] ?>" class="btn btn-default btn-xs deletetemplate" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-remove"></i></a>

                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php
                                     
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


<div id="viewTemplateModal" class="modal fade modalmark" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" autocomplete="off">×</button>
                <h4 class="modal-title"><?php echo $this->lang->line('template'); ?></h4>
            </div>
            <div class="modal-body minheight260">
                <div class="modal_loader_div" style="display: none;"></div>
                <div class="modal-body-inner">
                </div>
            </div>
        </div>
    </div>
</div>


<div id="myModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal-title"></h4>
            </div>
            <form role="form" id="form1" method="post" enctype="multipart/form-data" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label><?php echo $this->lang->line('template'); ?></label><small class="req"> *</small>
                        <input type="text" id="name" name="name" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                                <select autofocus="" id="searchclassid" name="class_id" onchange="getSectionByClass(this.value, 0, 'sections')" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($classlist as $class) {
                                    ?>
                                        <option value="<?php echo $class['id'] ?>"><?php echo $class['class'] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <span class="text-danger" id="error_class_id"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group relative z-index-6">
                                <label><?php echo $this->lang->line('section'); ?></label>
                                <small class="req"> *</small>
                                <div id="checkbox-dropdown-container">
                                    <div class="">
                                        <div class="custom-select" id="custom-select"><?php echo $this->lang->line('select'); ?></div>

                                        <div class="custom-select-option-box" id="custom-select-option-box">
                                            <div class="custom-select-option checkbox">
                                                <label class="vertical-middle line-h-18">
                                                    <input class="custom-select-option-checkbox select_all" type="checkbox" name="select_all" id="select_all"> <?php echo $this->lang->line('select_all'); ?>
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <span class="text-danger" id="error_class_id"></span>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="input-type"><?php echo $this->lang->line('marksheet_type'); ?></label>
                                    <div id="input-type" class="row">
                                        <div class="col-sm-4">
                                            <label class="radio-inline">
                                                <input name="orientation" class="orientation" id="input-type-student" value="L" type="radio"><?php echo $this->lang->line('landscape'); ?> </label>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="radio-inline">
                                                <input name="orientation" class="orientation" id="input-type-student" value="P" type="radio" checked="checked"><?php echo $this->lang->line('portrait'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('school_name') ?></label>
                                <input autofocus="" id="line" value="<?php echo set_value('school_name'); ?>" name="school_name" placeholder="" type="text" class="form-control" />
                                <span class="text-danger"><?php echo form_error('line'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('exam_center'); ?></label>
                                <input autofocus="" id="exam_center" value="<?php echo set_value('exam_center'); ?>" name="exam_center" placeholder="" type="text" class="form-control" />
                                <span class="text-danger"><?php echo form_error('exam_center'); ?></span>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('printing_date'); ?></label>
                                <input autofocus="" id="date" name="date" value="<?php echo set_value('date'); ?>" placeholder="" type="text" class="form-control date" />
                                <span class="text-danger"><?php echo form_error('date'); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo $this->lang->line('header_image'); ?> (965px X 150px)</label>
                        <input autofocus="" id="header_image" value="<?php echo set_value('header_image'); ?>" name="header_image" placeholder="" type="file" class="filestyle form-control" data-height="40" />
                        <span class="text-danger"><?php echo form_error('header_image'); ?></span>
                    </div>

                    <div class="form-group">
                        <label><?php echo $this->lang->line('footer_text'); ?></label>
                        <textarea class="form-control" id="question_textbox" name="content_footer"></textarea>
                        <span class="text-danger"><?php echo form_error('content_footer'); ?></span>
                    </div>

                    <div class="form-group">
                        <label><?php echo $this->lang->line('left_sign'); ?> (100px X 50px)</label>
                        <input id="documents" name="left_sign" placeholder="" type="file" class="filestyle form-control" data-height="40" name="left_sign">
                        <span class="text-danger"><?php echo form_error('left_sign'); ?></span>
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('middle_sign') ?> (100px X 50px)</label>
                        <input id="documents" name="middle_sign" placeholder="" type="file" class="filestyle form-control" data-height="40" name="middle_sign">
                        <span class="text-danger"><?php echo form_error('middle_sign'); ?></span>
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('right_sign'); ?> (100px X 50px)</label>
                        <input id="documents" name="right_sign" placeholder="" type="file" class="filestyle form-control" data-height="40" name="right_sign">
                        <span class="text-danger"><?php echo form_error('right_sign'); ?></span>
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('background_image') ?></label>
                        <input id="documents" name="background_img" placeholder="" type="file" class="filestyle form-control" data-height="40" name="background_image">
                        <span class="text-danger"><?php echo form_error('background_img'); ?></span>
                    </div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('template_description'); ?></label>
                        <textarea type="text" name="description" cols="115" rows="3" class="form-control"></textarea>
                    </div>
                    <div class="form-group switch-inline">
                        <label><?php echo $this->lang->line('student_name') ?></label>
                        <div class="material-switch switchcheck">
                            <input id="is_name" name="is_name" type="checkbox" class="chk" value="1">
                            <label for="is_name" class="label-success"></label>
                        </div>
                    </div>
                    <div class="form-group switch-inline">
                        <label><?php echo $this->lang->line('father_name') ?></label>
                        <div class="material-switch switchcheck">
                            <input id="is_father_name" name="is_father_name" type="checkbox" class="chk" value="1">
                            <label for="is_father_name" class="label-success"></label>
                        </div>
                    </div>
                    <div class="form-group switch-inline">
                        <label><?php echo $this->lang->line('mother_name') ?></label>
                        <div class="material-switch switchcheck">
                            <input id="is_mother_name" name="is_mother_name" type="checkbox" class="chk" value="1">
                            <label for="is_mother_name" class="label-success"></label>
                        </div>
                    </div>
                    <div class="form-group switch-inline">
                        <label><?php echo $this->lang->line('template_academic_session') ?></label>
                        <div class="material-switch switchcheck">
                            <input id="exam_session" name="exam_session" type="checkbox" class="chk" value="1">
                            <label for="exam_session" class="label-success"></label>
                        </div>
                    </div>
                    <div class="form-group switch-inline">
                        <label><?php echo $this->lang->line('admission_no') ?></label>
                        <div class="material-switch switchcheck">
                            <input id="is_admission_no" name="is_admission_no" type="checkbox" class="chk" value="1">
                            <label for="is_admission_no" class="label-success"></label>
                        </div>
                    </div>

                    <div class="form-group switch-inline">
                        <label><?php echo $this->lang->line('roll_no'); ?></label>
                        <div class="material-switch switchcheck">
                            <input id="is_roll_no" name="is_roll_no" type="checkbox" class="chk" value="1">
                            <label for="is_roll_no" class="label-success"></label>
                        </div>
                    </div>
                    <div class="form-group switch-inline">
                        <label><?php echo $this->lang->line('photo') ?></label>
                        <div class="material-switch switchcheck">
                            <input id="is_photo" name="is_photo" type="checkbox" class="chk" value="1">
                            <label for="is_photo" class="label-success"></label>
                        </div>
                    </div>
                    <div class="form-group switch-inline">
                        <label><?php echo $this->lang->line('class') ?></label>
                        <div class="material-switch switchcheck">
                            <input id="is_class" name="is_class" type="checkbox" class="chk" value="1">
                            <label for="is_class" class="label-success"></label>
                        </div>
                    </div>
                    <div class="form-group switch-inline">
                        <label><?php echo $this->lang->line('section') ?></label>
                        <div class="material-switch switchcheck">
                            <input id="is_section" name="is_section" type="checkbox" class="chk" value="1">
                            <label for="is_section" class="label-success"></label>
                        </div>
                    </div>
                    <div class="form-group switch-inline">
                        <label><?php echo $this->lang->line('date_of_birth') ?></label>
                        <div class="material-switch switchcheck">
                            <input id="is_dob" name="is_dob" type="checkbox" class="chk" value="1">
                            <label for="is_dob" class="label-success"></label>
                        </div>
                    </div>
                    <div class="form-group switch-inline">
                        <label><?php echo $this->lang->line('teacher_remark'); ?></label>
                        <div class="material-switch switchcheck">
                            <input id="remark" name="remark" type="checkbox" class="chk" value="1">
                            <label for="remark" class="label-success"></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer clearboth">
                    <button type="submit" class="btn btn-primary pull-right" data-loading-text="<?php echo $this->lang->line('submitting') ?>" value=""><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <?php echo $this->lang->line('edit_template'); ?> </h4>
            </div>
            <form role="form" id="edit_form" method="post" enctype="multipart/form-data" action="">
                <div class="modal-body">
                    <div id="templatedata"></div>
                </div>
                <div class="modal-footer clearboth">
                    <button type="submit" class="btn btn-primary pull-right" data-loading-text="<?php echo $this->lang->line('submitting') ?>" value=""><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="linkexamModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('link_exam'); ?></h4>
            </div>
            <form role="form" id="formlink" method="post" enctype="multipart/form-data" action="">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="hidden" name="template_id" id="template_id">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('marksheet_type') ?></label><small class="req"> *</small>
                                <select id="marksheet" name="marksheet" class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($marksheet as $marksheet) {
                                    ?>
                                        <option value="<?php echo $marksheet['short_code'] ?>" <?php
                                                                                                if (set_value('marksheet') == $marksheet['short_code']) {
                                                                                                    echo "selected=selected";
                                                                                                }
                                                                                                ?>><?php echo $this->lang->line($marksheet['short_code']); ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('marksheet'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt15">
                        <div class="col-md-12" id="examdata"></div>
                    </div>
                </div>
                <div class="modal-footer clearboth">
                    <?php if ($this->rbac->hasPrivilege('cbse_exam_link_exam', 'can_view')) { ?>
                        <button type="submit" class="btn btn-primary pull-right" data-loading-text="<?php echo $this->lang->line('submitting') ?>" value=""><?php echo $this->lang->line('save'); ?></button>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).on('click', '.view_template', function() {

        let recordid = $(this).data('recordid');
        let temp_name = $(this).data('temp_name');
        $.ajax({
            url: baseurl + 'cbseexam/template/viewtemplate',
            type: "POST",
            data: {
                "template_id": recordid
            },
            dataType: 'json',
            beforeSend: function() {
                $('#viewTemplateModal .modal-title').html(temp_name);
                $('#viewTemplateModal .modal-body .modal-body-inner').html("");
                $('#viewTemplateModal .modal-body .modal_loader_div').css("display", "block");
                $('#viewTemplateModal').modal('show');

            },
            success: function(data) {
                $('#viewTemplateModal .modal-body .modal-body-inner').html(data.page);
                $('#viewTemplateModal .modal-body .modal_loader_div').fadeOut(400);
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

            },
            complete: function() {

            }
        });
    });


    CKEDITOR.replace('question_textbox', {
        toolbar: 'Ques',
        allowedContent: true,
        enterMode: CKEDITOR.ENTER_BR,
        shiftEnterMode: CKEDITOR.ENTER_P,
        customConfig: baseurl + '/backend/js/ckeditor_config.js'
    });    
    


    function add() {
        $('#myModal').modal('show');
        $('#form1').trigger('reset');
        $('#modal-title').html('<?php echo $this->lang->line('add_template'); ?>');
        $('#sections').html('');
    }

    function getSectionByClass(class_id, section_id, select_control) {

        if (class_id != "") {
            var base_url = '<?php echo base_url() ?>';
            var div_data = '';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {
                    'class_id': class_id
                },
                dataType: "json",
                beforeSend: function() {
                    $('.custom-select-option-box').closest('div').find("input[name='select_all']").attr('checked', false);
                    $('.custom-select-option-box').children().not(':first').remove();
                },
                success: function(data) {

                    $.each(data, function(i, obj) {
                        var s = $('<div>', {
                            class: 'custom-select-option checkbox'
                        }).append($('<label>', {
                            class: 'vertical-middle line-h-18',

                        }).append($('<input />', {
                            class: 'custom-select-option-checkbox',
                            type: 'checkbox',
                            name: "section[]",
                            val: obj.id
                        })).append(obj.section));

                        $('.custom-select-option-box', $('#myModal .modal-body')).append(s);

                    });
                },
                complete: function() {

                }
            });
        }
    }

    function edit(template_id) {
        $('#editModal').modal('show');
        $.ajax({
            url: '<?php echo base_url(); ?>cbseexam/template/getdata',
            type: "POST",
            data: {
                template_id: template_id
            },
            dataType: 'json',
            beforeSend: function() {

            },
            success: function(res) {
                console.log(res.page);
                $('#templatedata').html(res.page);
                var elem = $('#editModal .modal-content').find('#ckeditor');
                $(elem).each(function(_, ckeditor) {

                    CKEDITOR.replace(ckeditor, {
                        toolbar: 'Ques',
                        allowedContent: true,
                        enterMode: CKEDITOR.ENTER_BR,
                        shiftEnterMode: CKEDITOR.ENTER_P,
                        customConfig: baseurl + '/backend/js/ckeditor_config.js'
                    });
                });

            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function() {

            }
        });
    }
</script>

<script type="text/javascript">
    (function($) {
        "use strict";

        $(document).on('click', '.linkexam', function() {
            var $this = $(this);
            $('#linkexamModal').modal('show');
            var recordid = $this.data('recordid');
            var is_weightage = $this.data('is_weightage');
            var marksheet_type = $this.data('marksheet_type');

            $('#template_id').val(recordid);
            $('#is_weightage').val(is_weightage).change();
            $('#marksheet').val(marksheet_type).change();
            if (marksheet_type == '') {

            } else {

            }
        });

        $("#form1").on('submit', (function(e) {
            e.preventDefault();

            for (var instanceName in CKEDITOR.instances) {
                CKEDITOR.instances[instanceName].updateElement();
            }

            var $this = $(this).find("button[type=submit]:focus");
            $.ajax({
                url: baseurl + "cbseexam/template/add",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $this.button('loading');
                },
                success: function(res) {
                    if (res.status == "fail") {
                        var message = "";
                        $.each(res.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(res.message);
                        window.location.reload(true);
                    }
                },
                error: function(xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    $this.button('reset');
                },
                complete: function() {
                    $this.button('reset');
                }
            });
        }));

        $("#edit_form").on('submit', (function(e) {
            e.preventDefault();

            for (var instanceName in CKEDITOR.instances) {
                CKEDITOR.instances[instanceName].updateElement();
            }
            
            var $this = $(this).find("button[type=submit]:focus");

            $.ajax({
                url: baseurl + "cbseexam/template/edit",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $this.button('loading');
                },
                success: function(res) {
                    if (res.status == "fail") {
                        var message = "";
                        $.each(res.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(res.message);
                        window.location.reload(true);
                    }
                },
                error: function(xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    $this.button('reset');
                },
                complete: function() {
                    $this.button('reset');
                }
            });
        }));

        $('.deletetemplate').click(function() {
            var templateid = $(this).attr('data-id');
            if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
                $.ajax({
                    url: '<?php echo base_url(); ?>cbseexam/template/remove',
                    type: "POST",
                    data: {
                        templateid: templateid
                    },
                    dataType: 'json',
                    success: function(res) {
                        successMsg(res.message);
                        window.location.reload(true);
                    }
                });
            }
        });

        $('#marksheet,#is_weightage').change(function() {
            var marksheet_type = $('#marksheet').val();
            var template_id = $('#template_id').val();
            var weightage = $('#is_weightage').val();
            $('#examdata').html('');
            $.ajax({
                type: 'POST',
                url: baseurl + 'cbseexam/template/get_examdata',
                data: {
                    marksheet_type: marksheet_type,
                    template_id: template_id,
                    weightage: weightage
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(data) {
                    $('#examdata').html(data.examdata);
                },
                error: function() {},
                complete: function() {}
            });
        });

        $("#formlink").on('submit', (function(e) {
            e.preventDefault();
            var $this = $(this).find("button[type=submit]:focus");
            var inps = document.getElementsByName('lessons[]');
            $.ajax({
                url: baseurl + "cbseexam/template/linkexams",
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $this.button('loading');
                },
                success: function(res) {
                    if (res.status == "fail") {
                        var message = "";
                        $.each(res.error, function(index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(res.message);
                        window.location.reload(true);
                    }
                },
                error: function(xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                    $this.button('reset');
                },
                complete: function() {
                    $this.button('reset');
                }
            });
        }));

        $(document).ready(function() {
            modal_click_disabled('myModal', 'linkexamModal', 'viewTemplateModal');
        });

    })(jQuery);
</script>

<script>
    $(document).on('click', ".custom-select", function() {
        $(".custom-select-option-box").toggle();
    });

    $(".custom-select-option").on("click", function(e) {
        var checkboxObj = $(this).children("input");
        if ($(e.target).attr("class") != "custom-select-option-checkbox") {
            if ($(checkboxObj).prop('checked') == true) {
                $(checkboxObj).prop('checked', false)
            } else {
                $(checkboxObj).prop("checked", true);
            }
        }
    });

    $(document).on('click', function(event) {
        if (event.target.className != "custom-select" && !$(event.target).closest('div').hasClass("custom-select-option")) {
            $(".custom-select-option-box").hide();
        }
    });

    $(document).on('change', '.select_all', function() {

        $(this).closest('#checkbox-dropdown-container').find('input:checkbox').not(this).prop('checked', this.checked);
    });
</script>