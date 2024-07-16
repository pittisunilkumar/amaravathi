<?php $this->load->view('layout/cbseexam_css.php'); ?>
<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
        <div class="row">           
            <div class="col-md-12">             
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('observation_list'); ?></h3>  
                        <div class="box-tools pull-right">                    
                            <?php if ($this->rbac->hasPrivilege('cbse_exam_observation', 'can_add')) { ?> 
                            
                                <button type="button" class="btn btn-sm btn-primary" data-record_id="0" data-original-title="<?php echo $this->lang->line('add_observation')?>"  data-action="add" data-toggle="modal" data-target="#myModal" autocomplete="off"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add')?></button> 
                                
                            <?php } ?>
                        </div>               
                    </div>
                    <div class="box-body">
                       
                        <div class="download_label"><?php echo $this->lang->line('observation_list'); ?></div>
                        <div class="table-responsive mailbox-messages overflow-visible-lg">
                            <?php if ($this->session->flashdata('msgdelete')) { ?>
                                <?php echo $this->session->flashdata('msgdelete') ?>
                            <?php } ?>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('observation'); ?></th>
                                        <th><?php echo $this->lang->line('observation_description'); ?></th>
                                        <th><?php echo $this->lang->line('parameter'); ?></th>                         
                                        <th><?php echo $this->lang->line('maximum_marks'); ?></th>                                   
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
                                                    } ?></td>
                                       
                                        <td class="mailbox-name"> <?php $sn=1;foreach ($value['data'] as $datakey => $datavalue) {
                                                    ?>
                                                    <ul class="liststyle1"><li><?php echo $datavalue['maximum_marks'];?></li></ul>
                                                    
                                                    <?php 
                                                    } ?></td>
                                                   
                                        <td class="white-space-nowrap">                                        
                                            <?php if ($this->rbac->hasPrivilege('cbse_exam_observation', 'can_edit')) { ?>
                                                
                                            <button type="button" data-record_id="<?php echo $value['id']; ?>" class="btn btn-default btn-xs" data-action="update" data-toggle="modal" data-target="#myModal" data-original-title="<?php echo $this->lang->line('edit_observation')?>" title="<?php echo $this->lang->line('edit_observation')?>" autocomplete="off"><i class="fa fa-pencil"></i></button>  
                                            
                                            <?php } if ($this->rbac->hasPrivilege('cbse_exam_observation', 'can_delete')) { ?>
                                            
                                            <a href="<?php echo site_url('cbseexam/observation/delete/'.$value['id']); ?>" data-id="<?php echo $value['id'] ?>" onclick="return confirm('<?php echo $this->lang->line('are_you_sure_want_to_delete'); ?>');" class="btn btn-default btn-xs deleteobservation"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-remove"></i></a>
                                            
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
    let _title=link.data('originalTitle');
    let record_id=link.data('record_id');
    $('#myModal .modal-title').html(_title);
     $.ajax({
                      url: base_url + "cbseexam/observation/observationform",
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


  $(document).on('click', '.addrow', function () {

        var html = '';
        html += "<div class='row'>";
          html += "<input type='hidden' name='row[]' value=" + row + ">";
           html += "<input type='hidden' name='update_id[]' value='0'>";    
                             html += "<div class='col-md-6'>";
                                      html += "<div class='form-group'>";                                   
                                      html += "<select class='form-control' name='parameter_" + row + "' autocomplete='off'>" + $('#parameter_dropdown').text() + "</select>";
                                      html += "</div>";
                             html += "</div>";
                             html += "<div class='col-md-5'>";
                                      html += "<div class='form-group'>";                                   
                                      html += "<input class='form-control' type='number' name='max_marks_" + row + "'>";
                                      html += "</div>";
                             html += "</div>";                      
           html += "<div class='col-md-1'>";
               html += "<div class='form-group'>";
                    html += "<span class='section_id_error text-danger rtl-float-right cursor-pointer'><i class='fa fa-remove remove_row'></i></span>";                                   
               html += "</div>";
           html += "</div>";
       html += "</div>";

        var tmp_row = $('#grade_result').append(html);

         row++;
    });

</script>
<script type="text/template" id="parameter_dropdown">
    <option value=""><?php echo $this->lang->line('select') ?></option>
    <?php
    foreach ($parameter as $parameter_key => $parameter_value) {
        ?>
        <option value="<?php echo $parameter_value->id; ?>" ><?php echo $parameter_value->name; ?></option>
        <?php
    }
    ?>
</script>
<script>
    $(document).on('submit', '#form_add', function (e) {

        e.preventDefault();
        var form = $(this);
        var subsubmit_button = $(this).find(':submit');
        var formdata = form.serialize();
        
        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: formdata, // serializes the form's elements.
            dataType: "JSON", // serializes the form's elements.
            beforeSend: function () {
                subsubmit_button.button('loading');
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
                 subsubmit_button.button('reset');
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

    $(document).on('click','.remove_row',function(){
       $(this).closest('div.row').remove();

    });
</script>