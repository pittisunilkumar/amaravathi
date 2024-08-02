<?php $this->load->view('layout/cbseexam_css.php'); ?>
<div class="content-wrapper">
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
                       
                            <div class="box-body">
                                <div class="row">
                                    <div class="row">
                                        <div class="col-md-12">

                                           
          
                                           
                                        
                                        </div>
                                    </div>
                                </div><!--./row-->
                            </div><!-- /.box-body -->
                            
                            <div class="box-footer">
                             
                                <div class="pull-right"><?php echo $this->lang->line('version') . " " . $version; ?></div>
                            </div>
                        
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
    (function($) {
        "use strict";
        var base_url = '<?php echo base_url(); ?>';

        $(".edit_student_guardian").on('click', function(e) {
            var $this = $(this);
            $this.button('loading');
            $.ajax({
                url: '<?php echo site_url("behaviour/setting/updatesetting") ?>',
                type: 'POST',
                data: $('#student_guardian_form').serialize(),
                dataType: 'json',

                success: function(data) {

                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function(index, value) {

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