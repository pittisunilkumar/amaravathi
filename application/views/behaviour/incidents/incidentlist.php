<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-flask"></i> 
        </h1>
    </section>
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('incident_list'); ?></h3>
                        <div class="box-tools pull-right">
                            <?php
                                if ($this->rbac->hasPrivilege('behaviour_records_incident', 'can_add')) {
                            ?>
                            <button type="button" class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-backdrop="static" data-target="#incidentmodel"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add'); ?></button>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="box-body table-responsive">
                        <table class="table table-striped table-bordered table-hover incident-list" data-export-title="<?php echo $this->lang->line('incident_list'); ?>">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('title') ?></th>
                                    <th><?php echo $this->lang->line('point') ?></th>
                                    <th><?php echo $this->lang->line('description') ?></th>
                                    <th class="text-right noExport"><?php echo $this->lang->line('action') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="incidentmodel" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close close_btn" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('add_incident'); ?></h4>
            </div>

            <div class="modal-body pt0 pb0">
                <form id="formadd" method="post" enctype="multipart/form-data" class="ptt10">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('title'); ?></label><small class="req"> *</small>
                                        <input name="title" type="text" class="form-control" value="<?php echo set_value('title'); ?>" />
                                        <span class="text-danger"><?php echo form_error('title'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('point'); ?></label><small class="req"> *</small>
                                        <input name="point" type="number" class="form-control"  value="<?php echo set_value('point'); ?>" />
                                        <span class="text-danger"><?php echo form_error('point'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('is_this_negative_incident'); ?></label><br>
                                        <input name="negative_incident" type="checkbox" value="1"  value="<?php echo set_value('negative_incident'); ?>" />
                                        <span class="text-danger"><?php echo form_error('negative_incident'); ?></span>
                                    </div>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('description'); ?></label><small class="req"> *</small>
                                        <textarea name="description" rows="5" class="form-control"></textarea>
                                        <span class="text-danger"><?php echo form_error('description'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="box-footer col-md-12">
                                    <button type="submit" class="btn btn-info pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait'); ?>"><?php echo $this->lang->line('save') ?></button>
                                </div>
                            </div>
                        </div>
                    </div> 
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editincidentmodel" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="box-title"><?php echo $this->lang->line('edit_incident'); ?></h4>
            </div>

            <div class="modal-body pt0 pb0">
                <form id="editincidentform" method="post" class="ptt10" enctype="multipart/form-data">
                    <div id="editincidentdata"></div>
                    <div class="row">
                        <div class="box-footer col-md-12">
                            <button type="submit" class="btn btn-info pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait'); ?>"><?php echo $this->lang->line('save') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function ($) {
  "use strict";
    $(document).ready(function() {
        initDatatable('incident-list','behaviour/incidents/dtincident',[],[],100);
    });

    $("#formadd").on('submit', (function (e) {
        e.preventDefault();

        var $this = $(this).find("button[type=submit]:focus");

        $.ajax({
            url: "<?php echo base_url(); ?>behaviour/incidents/create",
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

    $(document).on("click",".editincidentmodel",function() {
        $('#editincidentmodel').modal({backdrop: "static"});
        var incidentid = $(this).attr('data-record_id');
       
        $.ajax({
            url: "<?php echo base_url(); ?>behaviour/incidents/get",
            type: "POST",
            data: {incidentid:incidentid},
            dataType: 'json',
            beforeSend: function () {
              $('#editincidentdata').html('<center><?php echo $this->lang->line('loading'); ?>  <i class="fa fa-spinner fa-spin"></i></center>');
            },
            success: function (res)
            {
                $('#editincidentdata').html(res.page);
            }
        });
    });

    $("#editincidentform").on('submit', (function (e) {
        e.preventDefault();

        var $this = $(this).find("button[type=submit]:focus");

        $.ajax({
            url: "<?php echo base_url(); ?>behaviour/incidents/edit",
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

    $(document).on("click",".deletebtn",function() {
        var incidentid = $(this).attr('data-record_id');

        if(confirm('<?php echo $this->lang->line('delete_confirm'); ?>')){
            $.ajax({
                url: "<?php echo base_url(); ?>behaviour/incidents/delete",
                type: "POST",
                data: {incidentid:incidentid},
                dataType: 'json',
                success: function (res)
                {
                    if(res.status == 1){
                        successMsg(res.message);
                        window.location.reload(true);
                    }
                }
            });
        }
    });

    $('.close_btn').click(function(){
        $('#formadd')[0].reset();
    })
})(jQuery);
</script>