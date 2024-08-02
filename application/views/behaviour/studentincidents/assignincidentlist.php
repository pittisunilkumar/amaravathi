<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/behaviour_addon.css">
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-flask"></i> 
        </h1>
    </section>
       <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">
                        <form action="<?php echo site_url('behaviour/studentincidents/search') ?>" method="post" class="class_search_form">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('class'); ?></label>
                                            <select id="class_id" name="class_id" class="form-control" >
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
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('section'); ?></label>
                                            <select id="section_id" name="section_id" class="form-control" >
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('section_id'); ?></span>
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
                            <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('assign_incident_list'); ?></h3>
                            <div class="box-tools pull-right"></div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover assign-incident-list" data-export-title="<?php echo $this->lang->line('assign_incident_list'); ?>">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('student_name'); ?></th>
                                            <th><?php echo $this->lang->line('admission_no'); ?></th>
                                            <th><?php echo $this->lang->line('class'); ?></th>
                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <th><?php echo $this->lang->line('phone'); ?></th>
                                            <th class="text-right"><?php echo $this->lang->line('total_points'); ?></th>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content mt35">
            <div class="">
                <button type="button" class="close close_btn" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body pt0 pb0 relative">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h4 class="box-title bmedium"><?php echo $this->lang->line('assign_incident'); ?></h4>
                        <div class="divider mt0 mb0"></div>                       
                        <form id="formadd" method="post" enctype="multipart/form-data" class="ptt10">
                        <?php if(!empty($incidentlist)){ ?>
                            <input type="hidden" name="student_id" id="student_id">
                            <div class="page-container-fixed">
                                <div class="content-wrap-fixed">
                                    <div class="scroll-area-inside">  
                                        <?php foreach($incidentlist as $value){   ?> 
                                            <div class="behaviour-item">
                                                <div class="d-flex justify-content-between">
                                                 <h5 class="timeline-header bolds mt0 mb0"><?php echo $value['title']; ?> </h5>
                                                  <div class="point-md"><span class="bmedium"><?php echo $this->lang->line('point'); ?>: <?php echo $value['point']; ?></span><input type="checkBox" name="incident_id[]" value="<?php echo $value['id']; ?>"></div>
                                                </div>
                                                <div class="mt5"><?php echo $value['description']; ?></div>
                                            </div>                                        
                                        <?php } ?>
                                    </div>    
                                </div>
                            <div class="footer-fixed">
                                <button type="submit" class="btn btn-info pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait'); ?>"><?php echo $this->lang->line('save') ?></button>
                            </div>
                          </div>
                            <?php }else{ ?>
                        <div class="alert alert-danger"><?php echo $this->lang->line('no_record_found'); ?></div>
                        <?php } ?>
                        </form>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="viewassignedincidents" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" >&times;</button>
                <h4 class="modal-title" id="modal-title"><?php echo $this->lang->line('view_assigned_incidents'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="scroll-area-inside">
                    <div id="assigneddata"></div>
                </div>                 
            </div>
        </div>
    </div>
</div>

<script>
(function ($) {
  "use strict"; 
    $(document).ready(function() {
        emptyDatatable('assign-incident-list','data');
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
                        initDatatable('assign-incident-list','behaviour/studentincidents/dtassignincidentlist',response.params,[],100,
                            [{ "bSortable": false, "aTargets": [ -2 ] ,'sClass': 'dt-body-right'}]
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
        $('#student_id').val(student_id);
    });

    $(document).on("click",".viewassignedincidents",function() {
        $('#viewassignedincidents').modal({backdrop: "static"});
        var student_id = $(this).attr('data-student-id');
        $('#student_id').val(student_id);
        viewassignedincidentslist(student_id);
    });

    $("#formadd").on('submit', (function (e) {
        e.preventDefault();
        var student_id = $('student_id').val();
        var $this = $(this).find("button[type=submit]:focus");
        $.ajax({
            url: "<?php echo base_url(); ?>behaviour/studentincidents/create",
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
                    $('#assignstudentmodel').modal('hide');
                    var class_id = $('select[name="class_id"]').val();
                    var section_id = $('select[name="section_id"]').val();

                    initDatatable('assign-incident-list','behaviour/studentincidents/dtassignincidentlist/'+class_id+'/'+section_id,[],[],100,
                    [{ "bSortable": false, "aTargets": [ -2 ] ,'sClass': 'dt-body-right'}]
                    );

                    $('#formadd')[0].reset();
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

    $('.close').click(function(){
        var class_id = $('select[name="class_id"]').val();
        var section_id = $('select[name="section_id"]').val();

        initDatatable('assign-incident-list','behaviour/studentincidents/dtassignincidentlist/'+class_id+'/'+section_id,[],[],100,
            [{ "bSortable": false, "aTargets": [ -2 ] ,'sClass': 'dt-body-right'}]
            );
    })
})(jQuery);

function viewassignedincidentslist(student_id){
    $.ajax({
        url: "<?php echo base_url(); ?>behaviour/studentincidents/viewassignedincidentslist",
        type: "POST",
        data: {student_id:student_id},
        dataType: 'json',
        beforeSend: function () {
          $('#assigneddata').html('<center><?php echo $this->lang->line('loading'); ?>  <i class="fa fa-spinner fa-spin"></i></center>');
        },
        success: function (res)
        {
           $('#assigneddata').html(res.page);
        }
    });
}

(function ($) {
  "use strict";
    $(document).on("click",".delete_assign",function() {
        var assigned_id = $(this).attr('data-id');
        var student_id = $(this).attr('data-student-id');
        if(confirm('<?php echo $this->lang->line('delete_confirm'); ?>')){
            $.ajax({
                url: "<?php echo base_url(); ?>behaviour/studentincidents/delete",
                type: "POST",
                data: {assigned_id:assigned_id,student_id:student_id},
                dataType: 'json',
                success: function (res)
                {
                   successMsg(res.message);
                   viewassignedincidentslist(res.student_id);
                }
            });
        }
    });

    $('.close_btn').click(function(){
        $('#formadd')[0].reset();
    });
})(jQuery);
</script>