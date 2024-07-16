<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary" id="route">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix pt5"> <?php echo $this->lang->line('setting') ?></h3>     
                    </div>
                    <div class="box-body">
                        
                        
                        <button type="button"  data-toggle="modal" data-target="#addModal" class="btn btn-primary btn-sm mt10 mb10 pull-right"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add_new') ?> </button>
                        <table class="table table-striped table-bordered table-hover branch-list" data-export-title="<?php echo $this->lang->line('branch_list') ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('branch') ?></th>
                                    <th><?php echo $this->lang->line('url') ?></th>
                                    <th class="noExport"><?php echo $this->lang->line('action') ?></th>                                       
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table><!-- /.table -->
                            

                    </div>                
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal -->
<div id="addModal" class="modal fade" role="dialog" data-keyboard="false" data-backdrop="static"> 
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('add_new_branch'); ?></h4>
            </div>
        <div class="modal-body">
            <form method="POST" class="form_db" action="<?php echo site_url('admin/multibranch/branch/add') ?>">
            <input type="hidden" class="form-control id" name="id" value="0">
                
                <span class="clearfix">
                    <h4 class="box-title">
                        <?php echo $this->lang->line('branch_database_detail'); ?>
                    </h4>
                   
                    <div class="row">                    
               
                        <div class="col-md-6 form-group displaynone branch_div">               
                            <label><?php echo $this->lang->line('branch_name'); ?></label><small class="req"> *</small>
                            <input type="text" class="form-control branch_name" name="branch_name">
                        </div>
                        <div class="col-md-6 form-group">               
                            <label><?php echo $this->lang->line('hostname'); ?></label><small class="req"> *</small>
                            <input type="text" class="form-control host_name" name="host_name">
                        </div>                 
                        <div class="col-md-6 form-group">                     
                            <label><?php echo $this->lang->line('database_name'); ?></label><small class="req"> *</small>
                            <input type="text" class="form-control database" name="database">
                        </div>
                        <div class="col-md-6 form-group">
                            <label><?php echo $this->lang->line('username'); ?></label><small class="req"> *</small>
                            <input type="text" class="form-control username" name="username">
                        </div>                    
                        <div class="col-md-6 form-group">
                            <label><?php echo $this->lang->line('password'); ?></label><small class="req"> *</small>
                            <input type="password" class="form-control password" name="password">
                        </div>                     
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <button type="submit" class="btn btn-primary pull-right" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Please wait"><?php echo $this->lang->line('verify_save'); ?> </button>
                        </div>
                        
                    </div>              
               </form>
            </div>     
        </div>
    </div>
</div>

<script type="text/javascript">

    ( function ( $ ) {
    'use strict';
    
        $(document).ready(function () {
            // initDatatable('branch-list','admin/multibranch/branch/getlist',[],[],100);
        });
        
    } ( jQuery ) )

    var panel_count= $('.panel-group').length;
  
     $('#addModal').on('hidden.bs.modal', function(e) { 

       $("#addModal .modal-title").text("<?php echo $this->lang->line('add_new_branch'); ?>");


       $(".form_db").find('input:text, input:password, input:file, select, textarea').val('');
       $(".form_db").find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
       $('.branch_div',$('.modal-body')).css("display", "none");
    });  
  

$(document).on("click", ".delete_branch", function(e) {

         e.preventDefault(); // avoid to execute the actual submit of the form.
         var $this=$(this);
         let id = $(this).data('recordid');
  var result = confirm("<?php echo $this->lang->line('delete_confirm'); ?>");
    if(result){

    $.ajax({
           type: "POST",
           url: baseurl+"admin/multibranch/branch/delete",
           data: {'id' :id}, // serializes the form's elements.
           dataType:"JSON",
             beforeSend: function() {
                $this.button('loading');
             },
           success: function(data)
           {
             if (data.status == 0) {
                 var message = "";
               
                 errorMsg(data.message);
             } else {
                 successMsg(data.message);                        
              table.ajax.reload( null, false ); 
             }
              $this.button('reset');
           },
             error: function(xhr) { // if error occured
        alert("Error occured.please try again");
       $this.button('reset');
    },
    complete: function() {
     $this.button('reset');
    }
         });
    }

    });


           $(document).on('click', '.edit_branch', function () {
            var $this=$(this);
            var recordid = $(this).data('recordid');
            $('input[name=recordid]').val(recordid);
            $.ajax({
                type: 'POST',
                url: baseurl + "admin/multibranch/branch/edit",
                data: {'recordid': recordid},
                dataType: 'JSON',
                beforeSend: function () {
                    $this.button('loading');
                           $("#addModal .modal-title").text("<?php echo $this->lang->line('edit_branch'); ?>");
                    $('.branch_div',$('.modal-body')).css("display", "block");
                },
                success: function (data) {
                $('.id').val(data.result.id);
                $('.branch_name').val(data.result.branch_name);
                $('.host_name').val(data.result.hostname);
                $('.database').val(data.result.database_name);
                $('.username').val(data.result.username);
                $('.password').val(data.result.password);
                $('#default_chk_0').prop('checked', (data.result.is_default == 1) ? true : false);
                $('#addModal').modal('show');           

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


    // this is the id of the form
    $(document).on('submit','.form_db',function(e){

    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var url = form.attr('action');
       var smt_btn = $(this).find("button[type=submit]:focus" );
    $.ajax({
           type: "POST",
           url: url,
           dataType:'JSON',
           data: form.serialize(), // serializes the form's elements.
                beforeSend: function() {
                     smt_btn.button('loading');
                    },
                    success: function (data) {
                    if (data.status == 0) {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);                        
                     table.ajax.reload( null, false ); 
                     $('#addModal').modal('hide');
                    }
                     smt_btn.button('reset');
                },
                      error: function(xhr) { // if error occured
        alert("Error occured.please try again");
       smt_btn.button('reset');
    },
    complete: function() {
     smt_btn.button('reset');
    }
         });


    });
$('input.form-check-input').on('change', function() {
    $('input.form-check-input').not(this).prop('checked', false);  
});

$(document).on("click", ".check_branch", function(e) {

          e.preventDefault(); // avoid to execute the actual submit of the form.
          let $this = $(this);
          var host_name=$(this).closest('div.panel-body').find(".host_name").val();
          var database=$(this).closest('div.panel-body').find(".database").val();
          var username=$(this).closest('div.panel-body').find(".username").val();
           var password=$(this).closest('div.panel-body').find(".password").val();
   let id=$(this).data('id');
    $.ajax({
           type: "POST",
           url: baseurl+"admin/multibranch/branch/verify",
           data: {
            'host_name' :host_name,
            'database' :database,
            'username' :username,
            'password' :password
           }, // serializes the form's elements.
           dataType:"JSON",
             beforeSend: function() {
                $this.button('loading');
           },
           success: function(data)
           {
            console.log(data);
             if (data.status == 0) {
                 var message = "";
               
                 errorMsg(data.message);
             } else {
                 successMsg(data.message);                        
             
             }
 $this.button('reset');
           },
             error: function(xhr) { // if error occured
        alert("Error occured.please try again");
       $this.button('reset');
    },
    complete: function() {
     $this.button('reset');
    }
         });

    });


</script>