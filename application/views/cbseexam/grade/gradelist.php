<?php $this->load->view('layout/cbseexam_css.php'); ?>
<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
        <div class="row">           
            <div class="col-md-12">             
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('exam_grade_list'); ?></h3>  
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('cbse_exam_grade', 'can_add')) { ?>
                            <button type="button" class="btn btn-sm btn-primary" data-record_id="0" data-action="add" data-toggle="modal" data-originaltitle="<?php echo $this->lang->line('add_grade')?>" data-target="#myModal"   autocomplete="off"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add')?></button> 
                            <?php } ?>              
                        </div>               
                    </div>
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('exam_grade_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <?php if ($this->session->flashdata('msgdelete')) { ?>
                                <?php echo $this->session->flashdata('msgdelete') ?>
                            <?php } ?>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th width="10%"><?php echo $this->lang->line('grade_title'); ?></th>
                                        <th width="20%"><?php echo $this->lang->line('description'); ?></th>
                                        <th width="65%"><?php echo $this->lang->line('grade'); ?></th>                                    
                                        <th width="5%" class="text text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php 
                                   foreach ($result as $key => $value) {
                                    ?>
                                    <tr>
                                        <td><?php echo $value['name']?></td>
                                        <td><?php echo $value['description']?></td>
                                        <td class="mailbox-name">
  <table class="table table-bordered table-hover table-last-right">
    <thead>
<tr>
    <th><?php echo $this->lang->line('grade'); ?></th>
    <th><?php echo $this->lang->line('maximum_percentage'); ?></th>
    <th><?php echo $this->lang->line('minimum_percentage'); ?></th>
    <th><?php echo $this->lang->line('remark'); ?></th> 
</tr>
    </thead>
    <tbody>
     <?php
                                          $sn=1;
                                         foreach ($value['data'] as $datakey => $datavalue) {
                                                    ?>
                                                   
      <tr>
     
   <td><?php echo $datavalue['name'];?></td>
   <td><?php echo $datavalue['maximum_percentage'];?></td>
   <td><?php echo $datavalue['minimum_percentage'];?></td>
   <td><?php echo $datavalue['description'];?></td>
  </tr>
                                                    <?php 
                                                    }
                                                     ?>
</tbody>
                                    </table>
                                                 </td>
                                   
                                        <td class="white-space-nowrap"> 
                                        
                                            <?php if ($this->rbac->hasPrivilege('cbse_exam_grade', 'can_edit')) { ?>
                                            <span data-toggle="tooltip" >
                                            <button type="button" data-record_id="<?php echo $value['id']; ?>" class="btn btn-default btn-xs" data-action="update" data-originaltitle="<?php echo $this->lang->line('edit_grade'); ?>" data-toggle="modal" data-target="#myModal" data-original-title="<?php echo $this->lang->line('edit_grade'); ?>" title="<?php echo $this->lang->line('edit_grade'); ?>" autocomplete="off"><i class="fa fa-pencil"></i></button></span> 
                                            <?php } if ($this->rbac->hasPrivilege('cbse_exam_grade', 'can_delete')) { ?>                                            
                                            <a data-id="<?php echo $value['id'] ?>" class="btn btn-default btn-xs deletegrade"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-remove"></i></a>
                                            
                                            <?php } ?>
                                        </td>
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
    <div class="modal-dialog modal-dialog2 modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" >&times;</button>
                <h4 class="modal-title" id="modal-title"><?php echo $this->lang->line('add'); ?></h4>
            </div>
           <div class="modal-body minheight260"> 

                <div class="modal_loader_div" style="display: none;"></div>

                <div class="modal-body-inner">
                    
                </div>

            </div>

        </div>
    </div>
</div> 

<script type="text/javascript">

var row=2;
    // Fill modal with content from link href
$("#myModal").on("show.bs.modal", function(e) {
    var link = $(e.relatedTarget);
    let action=link.data('action');
    let title=link.data('originaltitle');
    let record_id=link.data('record_id');
    console.log(link.data());
 
    $('#myModal .modal-title').html(title);
    
     $.ajax({
                    url: baseurl+'cbseexam/grade/gradeform',
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

$(document).on('click','.add_row',function(){
        $('<div>', {   
        class: 'row'
    }).append($('<input>', {   
        class: 'form-control',
        value: row,
        type: 'hidden',
        name: 'row[]'
    })).append($('<input>', {   
        class: 'form-control',
        value: 0,
        type: 'hidden',
        name: 'update_id[]'
    })).append($('<div>', {   
        class: 'col-md-2'
    }).append($('<div>', {   
        class: 'form-group'
    }).append($('<input>', {   
        class: 'form-control',
        value: '',
        name: 'range_name_'+row
    })))).append($('<div>', {   
        class: 'col-md-3'
    }).append($('<div>', {   
        class: 'form-group'
    }).append($('<input>', {   
        class: 'form-control',
        value: '',
        type: 'number',
        step:'any',
        name: 'maximum_percentage_'+row
    })))).append($('<div>', {   
        class: 'col-md-3'
    }).append($('<div>', {   
        class: 'form-group'
    }).append($('<input>', {   
        class: 'form-control',
        value: '',
        type: 'number',
        step:'any',
        name: 'minimum_percentage_'+row
    })))).append($('<div>', {   
        class: 'col-md-3'
    }).append($('<div>', {   
        class: 'form-group'
    }).append($('<textarea>', {   
        class: 'form-control',
        value: '',
        name: 'type_description_'+row
    })))).append($('<div>', {   
        class: 'col-md-1'
    }).append($('<div>', {   
        class: 'form-group'
    }).append($('<span>', {   
        class: 'section_id_error text-danger rtl-float-right cursor-pointer',
       
    }).append($('<i>', {   
        class: 'fa fa-remove remove_row',
       
    }))))).appendTo('#grade_result');
    row++;

});

$(document).on('click','.remove_row',function(){
$(this).parent().closest('div.row').remove();

});

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

    function edit(grade_id)
    {
        $('#grade_result').html('');
        $('#grade_id').val(grade_id);
        $('#delete_ides').html('');
        $('#modal-title').html('<?php echo $this->lang->line('edit_exam_grade')?>');
        $.ajax({
                url: '<?php echo base_url(); ?>cbseexam/grade/get_editdetails',
                type: "POST",
                data:{id:grade_id},
                dataType: 'json',
                 beforeSend: function() {
                    
                },
                success: function(res) {   
                  $('#name').val(res.name);
                  $('#description').val(res.description);
                    $.each(res.list, function (index, value) {

                         $.ajax({
                url: '<?php echo base_url(); ?>cbseexam/grade/add_graderange',
                type: "POST",
                data:{id:value.id,delete_string:value.id},
                dataType: 'json',
                 beforeSend: function() {
                    
                },
                success: function(res) {   
                   $('#grade_result').append(res);
                },
                   error: function(xhr) { // if error occured
                   alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                   
            },
            complete: function() {
                  
            }
            });
            });

                    $('#myModal').modal('show');
                    $('#modal-title').html('<?php echo $this->lang->line('edit_exam_grade') ?>');
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
    
$(document).on('submit','#add_form',function(e){
    console.log("sdfsdf");
      e.preventDefault();
        var $this = $(this).find("button[type=submit]:focus");       

        $.ajax({
            url: base_url+"cbseexam/grade/add",
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
});

    $('.deletegrade').click(function(){
        var id = $(this).attr('data-id');
        if(confirm('<?php echo $this->lang->line('delete_confirm'); ?>')){
            $.ajax({
                url: '<?php echo base_url(); ?>cbseexam/grade/remove',
                type: "POST",
                data: {id:id},
                dataType: 'json',
                success: function (res)
                {
                    successMsg(res.message);
                    window.location.reload(true);
                }
            });
        }
    })

        $(document).ready(function () {
          modal_click_disabled('myModal');
       
    });
    
})(jQuery);
</script>