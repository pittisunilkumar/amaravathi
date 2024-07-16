<div class="content-wrapper" style="min-height: 348px;">
    <section class="content-header">
        <h1><i class="fa fa-ioxhost"></i> <?php echo $this->lang->line('setting'); ?></h1>
    </section>
    <section class="content">
        <div class="row">

            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->

                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><i class="fa fa-gear"></i> <?php echo $this->lang->line('setting'); ?></h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="">
                        <form role="form" id="student_guardian_form" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="sch_id" value="<?php echo $setting['id']; ?>">
                            <div class="box-body">
                                <div class="row">
                                    <div class="row">
                                    <div class="col-md-12">

  
                                        <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-sm-3"> <?php echo $this->lang->line('comment_option'); ?></label>
                                            <div class="col-sm-8">

                                                <?php $comment_option    = json_decode($setting['comment_option']); ?>

                                                <label class="checkbox-inline">
                                                    <input id="comment_option" type="checkbox" name="comment_option[]" value="student" <?php
                                                    if (!empty($comment_option) && in_array("student", $comment_option)){
                                                        echo "checked";
                                                    }
                                                    ?> ><?php echo  $this->lang->line('student_comment'); ?>
                                                </label>
                                                <label class="checkbox-inline">
                                                    <input id="comment_option" type="checkbox" name="comment_option[]" value="parent" <?php
                                                    if (!empty($comment_option) && in_array("parent", $comment_option)){
                                                        echo "checked";
                                                    }
                                                    ?> ><?php echo  $this->lang->line('parent_comment'); ?>
                                                </label>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div><!--./row-->
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <div class="col-md-6 col-sm-6 col-xs-6 col-md-offset-3">
                                    <?php
                                if ($this->rbac->hasPrivilege('behaviour_records_setting', 'can_edit')) {
                                    ?>
                                    <button type="button" class="btn btn-info pull-left edit_student_guardian" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>"> <?php echo $this->lang->line('save'); ?></button>
                                    <?php
                                }
                                ?>

                                </div>
                               <div class="pull-right"><?php echo $this->lang->line('version') . " " . $version; ?></div>
                            </div>
                        </form>
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<!-- new END -->
</div><!-- /.content-wrapper -->

<script type="text/javascript">
(function ($) {
  "use strict";
    var base_url = '<?php echo base_url(); ?>';

    $(".edit_student_guardian").on('click', function (e) {
        var $this = $(this);
        $this.button('loading');
        $.ajax({
            url: '<?php echo site_url("behaviour/setting/updatesetting") ?>',
            type: 'POST',
            data: $('#student_guardian_form').serialize(),
            dataType: 'json',

            success: function (data) {

                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function (index, value) {

                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    window.location.reload(true);
                }

                $this.button('reset');
            }
        });
    });
})(jQuery);
</script>
