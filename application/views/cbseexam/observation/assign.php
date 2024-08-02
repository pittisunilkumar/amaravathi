<?php $this->load->view('layout/cbseexam_css.php'); ?>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.js"></script>
<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
        <div class="row">           
            <div class="col-md-12">             
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line("assign_observation_list"); ?></h3> 
                        
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('cbse_exam_assign_observation', 'can_add')) { ?>
                            <button type="button" class="btn btn-sm btn-primary"  data-record_id="0" data-original-title="<?php echo $this->lang->line('add_observation_term')?>"  data-action="add" data-toggle="modal" data-target="#myModal" autocomplete="off"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add')?></button>
                            <?php } ?>
                        </div>               
                    </div>
                    <div class="box-body">                    
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <?php if ($this->session->flashdata('msgdelete')) { ?>
                                <?php echo $this->session->flashdata('msgdelete') ?>
                            <?php } ?>
                                 <table class="table table-striped table-bordered table-hover observation_list" data-export-title="<?php echo $this->lang->line('assign_observation_list') ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('observation'); ?></th>
                                        <th><?php echo $this->lang->line('term'); ?></th>
                                        <th><?php echo $this->lang->line('code'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>                                     
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
    </section>
</div>

<div id="myModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-dialog2 modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" >&times;</button>
                <h4 class="modal-title" id="modal-title"><?php echo $this->lang->line('add'); ?></h4>
            </div>
           <div class="modal-body minheight149"> 

                <div class="modal_loader_div" style="display: none;"></div>

                <div class="modal-body-inner">
                    
                </div>

            </div>
        </div>
    </div>
</div>

<div id="assignModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-dialog2 modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" >&times;</button>
                <h4 class="modal-title" id="modal-title"><?php echo $this->lang->line('assign_marks'); ?></h4>
            </div>
             <div class="modal-body">
                <form role="form" id="allotStudentMarks" action="<?php echo site_url('cbseexam/observation/termstudent') ?>" method="post" >
                    <input type="hidden" name="cbse_observation_term_id" value="0" id="cbse_observation_term_id">
                    <input type="hidden" name="cbse_term_id" value="0" id="cbse_term_id">
                    <div class="row">
                        <div class="col-sm-6">
                                <div class="form-group">
                            <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                            <select autofocus="" id="class_id" name="class_id" class="form-control" >
                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                <?php
foreach ($classlist as $class) {
    ?>
                                    <option value="<?php echo $class['id'] ?>" <?php
if (set_value('class_id') == $class['id']) {
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
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label>
                                <select  id="section_id" name="section_id" class="form-control" >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                </select>
                                <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer clearboth mx-nt-lr-15 pb0">
                        
                        <button type="submit" name="search" value="search_filter" class="btn btn-primary pull-right btn-sm checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                        
                    </div>
                </form>

                <div class="studentAllotForm">

                </div>
            </div>
        </div>
    </div>
</div> 

<script type="text/javascript">

    $(document).ready(function () {

    $.validator.addMethod("uniqueUserName", function (value, element, options)
    {
       var max_mark = element.getAttribute('data-marks');   
        //we need the validation error to appear on the correct element
        return parseFloat(value) <= parseFloat(max_mark);
    },
            "<?php echo $this->lang->line('invalid_marks') ?>"
            );

        $(document).on('submit', 'form#allot_exam_student', function (event) {
            event.preventDefault();

                $('form#allot_exam_student').validate({
                    debug: true,
                    errorClass: 'error text text-danger',
                    validClass: 'success',
                    errorElement: 'span',
                    highlight: function(element, errorClass, validClass) {
                       $(element).parent().addClass(errorClass);
                    },
                    unhighlight: function(element, errorClass, validClass) {
                      $(element).parent().removeClass(errorClass);
                    }
                });

            $('.marksssss').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                              uniqueUserName: true,
                            messages: {
                                required: "<?php echo $this->lang->line('required') ?>",
                            }
                        });
            });

// test if form is valid
            if ($('form#allot_exam_student').validate().form()) {
    //     e.preventDefault(); // avoid to execute the actual submit of the form.
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
                    $('#assignModal').modal('hide');

                } else {
                    var message = "";
            $.each(res.error, function (index, value) {

                message += value;

            });
         errorMsg(message);           

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

                  } else {
                console.log("does not validate");
            }    
        })        

    });

    $("#assignModal").on("show.bs.modal", function(e) {
    var link = $(e.relatedTarget);  
    let record_id=link.data('record_id');
    let cbse_term_id=link.data('cbse_term_id');
    $('#cbse_observation_term_id').val(record_id);
    $('#cbse_term_id').val(cbse_term_id);

});

    $("#assignModal").on("hidden.bs.modal", function(e) {
    $('.studentAllotForm').html(""); 
    reset_form('#allotStudentMarks'); 
    $('#section_id').find('option').not(':first').remove();
    
});

  $("form#allotStudentMarks").on('submit', (function (e) {
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
                    $('.studentAllotForm').html("");  
            },
            success: function (res)
            {                

            if (res.status == 1) {
            $('.studentAllotForm').html(res.page); 

            } else {
             var message = "";
            $.each(res.error, function (index, value) {

            message += value;

            });
             errorMsg(message);          

            }

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
    ));

    $(document).on('change', '#class_id', function (e) {
        $('#section_id').html("");
        var class_id = $(this).val();
        var selector = $(this).closest("div.assignModal").find('#section_id');
        getSectionByClass(class_id, section_id, selector);
    });

        function getSectionByClass(class_id, section_id) {
            if (class_id != 0 && class_id !== "") {
                $('#section_id').html("");
                var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
                $.ajax({
                    type: "GET",
                   url: base_url + "sections/getByClass",
                    data: {'class_id': class_id},
                    dataType: "json",
                    beforeSend: function () {
                        $('#section_id').addClass('dropdownloading');
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
                        $('#section_id').append(div_data);
                    },
                    complete: function () {
                        $('#section_id').removeClass('dropdownloading');
                    }
                });
            }
        }


    $("#myModal").on("show.bs.modal", function(e) {
    var link = $(e.relatedTarget);
    let action=link.data('action');
    let _title=link.data('originalTitle');
    let record_id=link.data('record_id');
    console.log(link.data());

    $('#myModal .modal-title').html(_title);
     $.ajax({
                    url: baseurl+'cbseexam/observation/observationtermform',
                    type: "POST",
                    data: {"action" : action,'record_id':record_id},
                    dataType: 'json',                   
                    beforeSend: function () {
                        $('#myModal .modal-body .modal-body-inner').html(""); 
                        $('#myModal .modal-body .modal_loader_div').css("display", "block"); 
                   
                    },
                    success: function (data)
                    {
                          row=data.total_rows;
                          $('#myModal .modal-body .modal-body-inner').html(data.page); 
                          $('#myModal .modal-body .modal_loader_div').fadeOut(400);
                    },
                    error: function (xhr) { // if error occured
                    alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                 
                    },
                    complete: function () {
            
                    }
            });
});

$(document).on('submit','#add_form',function(e){
    console.log("sdfsdf");
      e.preventDefault();
        var $this = $(this).find("button[type=submit]:focus");      

        $.ajax({
            url: base_url+"cbseexam/observation/assignObservationTerm",
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
                if (res.status == 0) {
                    var message = "";
                    $.each(res.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(res.message);
                    $('#myModal').modal('hide');
                   table.ajax.reload( null, false );
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
});

    ( function ( $ ) {
    'use strict';
    $(document).ready(function () {

        initDatatable('observation_list','cbseexam/observation/getlist',[],[],100);
       
    });
} ( jQuery ) )
</script>

<script type="text/javascript">

    $(document).ready(function () {

        modal_click_disabled('myModal','assignModal');

    }); 
    
    function add()
    {        
        $('#observation_parameter').val('');
        $('#searchclassid').val('');
        $('#sections').html('');
        $('#myModal').modal('show');  
        $('#modal-title').html('<?php echo $this->lang->line('add')?>');            
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

    function edit(cbse_observation_parameter_id,class_id)
    {  
        getSectionByClass(class_id, '', 'sections');
        $('#observation_parameter').val(cbse_observation_parameter_id);
        $('#searchclassid').val(class_id);
        $('#sections').html("");       
        $('#edit_val').val(1);
        $.ajax({
            url: '<?php echo base_url(); ?>cbseexam/observation/get_assignclasssections',
            type: "POST",
            data:{cbse_observation_parameter_id:cbse_observation_parameter_id,class_id:class_id},
            dataType: 'json',
            beforeSend: function() {
                    
            },
            success: function(res) {  
                  $('#modal-title').html('<?php echo $this->lang->line('edit')?>');
                  $('#sections').html(res.view);  
                   $.each(res.acs, function (i, obj)
                {  
                    
            $("div.custom-select-option").find("input[type=checkbox][value="+obj.class_section_id+"]").prop('checked',true);

                });
                     $('#myModal').modal('show');
            },
            error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");                   
            },
            complete: function() {
                  
            }
        });
    }

(function ($){
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
            url: base_url+"cbseexam/observation/assignClassSection",
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

    $(document).on('click', '.examobservation', function () {
        var $this = $(this);
        var recordid = $this.data('recordid');
        $('input[name=recordid]').val(recordid);
        
        $.ajax({
            type: 'POST',
            url: baseurl + "cbseexam/observation/exam_observationstudent",
            data: {'observation_id': recordid},
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

    $('.deleteobservation').click(function(){
        var observation_parameter_id = $(this).attr('data-observation_parameter_id');
        var class_id = $(this).attr('data-class_id');
        if(confirm('<?php echo $this->lang->line('delete_confirm'); ?>')){
            $.ajax({
                url: '<?php echo base_url(); ?>cbseexam/observation/removeassignclass_sections',
                type: "POST",
                data: {observation_parameter_id:observation_parameter_id,class_id:class_id},
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
    $("#custom-select").on("click",function(){
        $("#custom-select-option-box").toggle();
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
  if (event.target.id != "custom-select" && !$(event.target).closest('div').hasClass("custom-select-option")  ) {
          $("#custom-select-option-box").hide();
     }
});

$(document).on('change','#select_all',function(){   
        $('input:checkbox').not(this).prop('checked', this.checked);
});
</script>