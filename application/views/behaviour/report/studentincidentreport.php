<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/behaviour_addon.css">
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-flask"></i> 
        </h1>
    </section>
       <section class="content">
        <?php $this->load->view('behaviour/report/_behaviour_report'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <input type="hidden" id="student_id">
                    <div class="box-body">
                        <form  action="<?php echo site_url('behaviour/report/search') ?>" method="post" class="class_search_form">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('class'); ?></label>
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
                                            <span class="text-danger" id="error_class_id"></span>
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
                                            <label><?php echo $this->lang->line('session'); ?></label>
                                            <select name="session_id" class="form-control">
                                                <option value="current_session"><?php echo $this->lang->line('current_session_points'); ?></option>
                                                <option value="all_Session"><?php echo $this->lang->line('all_session_points'); ?></option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('session_id'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-sm pull-right" name="class_search" data-loading-text="<?php echo $this->lang->line('please_wait'); ?>" value=""><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>                           
                        </div>
                    </form>
                    </div>
                    <div class="">
                        <div class="box-header ptbnull"></div>
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('student_incident_list'); ?></h3>
                            <div class="box-tools pull-right"></div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">                                
                            <table class="table table-striped table-bordered table-hover student-assign-list" data-export-title="<?php echo $this->lang->line('student_incident_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('admission_no'); ?></th>
                                        <th><?php echo $this->lang->line('student_name'); ?></th>
                                        <th><?php echo $this->lang->line('class'); ?> (<?php echo $this->lang->line('section'); ?>)</th>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <th><?php echo $this->lang->line('total_incidents'); ?></th>
                                        <th><?php echo $this->lang->line('total_points'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            </div>
                        </div><!--./box-body-->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="assignstudentmodel" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-dialogfullwidth" role="document">
        <div class="modal-content modal-media-content mt35">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('assigned_incident'); ?></h4>
            </div>
            <div class="modal-body pt0 pb0 relative">
                 <div class="box-body table-responsive">
                    <table class="table table-striped table-bordered table-hover student-point-list" data-export-title="<?php echo $this->lang->line('assigned_incident'); ?>">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('title') ?></th>
                                <th><?php echo $this->lang->line('point') ?></th>
                                <th><?php echo $this->lang->line('session') ?></th>
                                <th><?php echo $this->lang->line('date') ?></th>
                                <th><?php echo $this->lang->line('description') ?></th>
                                <th><?php echo $this->lang->line('assign_by') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function ($) {
  "use strict";
    $(document).ready(function() {
        emptyDatatable('student-assign-list','data');
    });
})(jQuery);
</script>

<script>
(function ($) {
  "use strict";
    $(document).ready(function () {
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id',0) ?>';
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
})(jQuery);

function getSectionByClass(class_id, section_id) {
    if (class_id != "") {
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
                        sel = "selected=selected";
                    }
                    div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                });
                $('#section_id').append(div_data);
            }
        });
    }
}

(function ($) {
  "use strict";
    $(document).ready(function(){ 
        $(document).on('submit','.class_search_form',function(e){
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var $this = $(this).find("button[type=submit]:focus");  
            var form = $(this);
            var url = form.attr('action');
            var form_data = form.serializeArray();
            form_data.push({name: 'search_type', value: $this.attr('name')});
            $.ajax({
               url: url,
               type: "POST",
               dataType:'JSON',
               data: form_data, // serializes the form's elements.
                  beforeSend: function () {
                    $('[id^=error]').html("");
                    $this.button('loading');
                   },
                  success: function(response) { // your success handler
                    if(!response.status){
                        $.each(response.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                    }else{
                        initDatatable('student-assign-list','behaviour/report/dtstudentlist',response.params,[],100
                            );
                    }
                  },
                 error: function() { // your error handler
                    $this.button('reset');
                 },
                 complete: function() {
                    $this.button('reset');
                 }
            });
        });
    });

    $(document).on("click",".assignstudent",function() {
        $('#assignstudentmodel').modal({backdrop: "static"});
        var student_id = $(this).attr('data-student-id');
        var session_value = $('select[name="session_id"]').val();
        $('#student_id').val(student_id);
        initDatatable('student-point-list','behaviour/report/assignstudent/'+student_id+'/'+session_value,[],[],100);
    });
})(jQuery);
</script>