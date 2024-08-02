<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>     <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1><i class="fa fa-newspaper-o"></i> <?php //echo $this->lang->line('certificate'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php
            if ($this->rbac->hasPrivilege('student_id_card', 'can_add')) {
                ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('add_student_id_card'); ?></h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form id="form1" enctype="multipart/form-data" action="<?php echo site_url('admin/tcgeneration/create') ?>"  id="certificateform" name="certificateform" method="post" accept-charset="utf-8">
                            <div class="box-body">

                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php 
                                        echo $this->session->flashdata('msg');
                                        $this->session->unset_userdata('msg');
                                    ?>
                                <?php } ?>

                                <?php
                                if (isset($error_message)) {
                                    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                }
                                ?>

                                <div class="form-group">
                                    <!-- <label><?php echo $this->lang->line('tc_name'); ?></label><small class="req"> *</small> -->
                                    <label><?php echo $this->lang->line('tc_name'); ?></label><small class="req"> *</small>

                                    <input autofocus="" id="tc_name" name="tc_name" placeholder="" type="text" class="form-control" value="<?php echo set_value('tc_name'); ?>" />
                                    <span class="text-danger"><?php echo form_error('tc_name'); ?></span>
                                </div>

                                
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('logo'); ?></label>
                                    <input id="logo_img" placeholder="" type="file" class="filestyle form-control" data-height="40"  name="logo_img">
                                    <span class="text-danger"><?php echo form_error('logo_img'); ?></span>
                                </div>

                                
                                <!-- Head tittle -->
                                <div class="form-group">
                                    <!-- <label><?php echo $this->lang->line('tc_head_tittle'); ?></label><small class="req"> *</small> -->
                                    <label><?php echo $this->lang->line('tc_head_tittle'); ?></label><small class="req"> *</small>

                                    <input autofocus="" id="tc_head_tittle" name="tc_head_tittle" placeholder="" type="text" class="form-control" value="<?php echo set_value('tc_head_tittle'); ?>" />
                                    <span class="text-danger"><?php echo form_error('tc_head_tittle'); ?></span>
                                </div>

                                <!-- school name -->
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('school_name'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="school_name" name="school_name" placeholder="" type="text" class="form-control" value="<?php echo set_value('school_name'); ?>" />
                                    <span class="text-danger"><?php echo form_error('school_name'); ?></span>
                                </div>

                                <!-- header discription -->
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('tc_description'); ?></label><small class="req"> *</small>
                                    <textarea class="form-control" id="tc_description" name="tc_description" placeholder="" rows="2" placeholder=""><?php echo set_value('tc_description'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('tc_description'); ?></span>
                                </div>

                                <!-- header address -->
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('tc_address'); ?></label><small class="req"> *</small>
                                    <!-- <input id="tc_address" name="tc_address" placeholder="" type="text" class="form-control" value="<?php echo set_value('tc_address'); ?>" /> -->
                                    <textarea class="form-control" id="tc_address" name="tc_address" placeholder="" rows="2" placeholder=""><?php echo set_value('tc_address'); ?></textarea>
                                    <span class="text-danger"><?php echo form_error('tc_address'); ?></span>
                                </div>


                                <div class="form-group">
                                    <label><?php echo $this->lang->line('tc_body'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="tc_body" name="tc_body" placeholder="" type="text" class="form-control" value="<?php echo set_value('tc_body'); ?>" />
                                    <span class="text-danger"><?php echo form_error('tc_body'); ?></span>
                                </div>


                                <div class="form-group">
                                    <label><?php echo $this->lang->line('student_tc_firstlangg'); ?></label><small class="req"> *</small>
                                    <select autofocus="" id="firstlang_id" name="firstlang_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                            foreach ($classlist as $class) {
                                                ?>
                                            <option value="<?php echo $class['id'] ?>" <?php if (set_value('firstlang_id') == $class['id']) {
                                                echo "selected=selected";
                                            }
                                            ?>><?php echo $class['name'] ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('firstlang_id'); ?></span>
                                </div>


                                <div class="form-group">
                                    <label><?php echo $this->lang->line('student_tc_secondlangg'); ?></label><small class="req"> *</small>
                                    <select autofocus="" id="secondlang_id" name="secondlang_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                            foreach ($classlist as $class) {
                                                ?>
                                            <option value="<?php echo $class['id'] ?>" <?php if (set_value('secondlang_id') == $class['id']) {
                                                echo "selected=selected";
                                            }
                                            ?>><?php echo $class['name'] ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('secondlang_id'); ?></span>
                                </div>


                                <div class="form-group">
                                    <label><?php echo $this->lang->line('mother_tongue'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="tc_mother_tongue" name="tc_mother_tongue" placeholder="" type="text" class="form-control" value="<?php echo set_value('tc_mother_tongue'); ?>" />
                                    <span class="text-danger"><?php echo form_error('tc_mother_tongue'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label><?php echo $this->lang->line('student_tc_conduct'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="tc_conduct" name="tc_conduct" placeholder="" type="text" class="form-control" value="<?php echo set_value('tc_conduct'); ?>" />
                                    <span class="text-danger"><?php echo form_error('tc_conduct'); ?></span>
                                </div>

                                
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('tc_footer'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="tc_footer" name="tc_footer" placeholder="" type="text" class="form-control" value="<?php echo set_value('tc_footer'); ?>" />
                                    <span class="text-danger"><?php echo form_error('tc_footer'); ?></span>
                                </div>


                                <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('student_name'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_student_name" name="is_active_student_name" type="checkbox" class="chk" value="1" <?php echo set_checkbox('is_active_student_name', '1', (set_value('is_active_student_name') == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_student_name" class="label-success"></label>
                                    </div>
                                </div>
                                
                                <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('parents_name'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_parents_name" name="is_active_parents_name" type="checkbox" class="chk" value="1"  <?php echo set_checkbox('is_active_parents_name', '1', (set_value('is_active_parents_name') == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_parents_name" class="label-success"></label>
                                    </div>
                                </div>

                                <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('date_of_birth'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_dob" name="is_active_dob" type="checkbox" class="chk" value="1" <?php echo set_checkbox('is_active_dob', '1', (set_value('is_active_dob') == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_dob" class="label-success"></label>
                                    </div>
                                </div>

                                <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('admission_date'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_admission_date" name="is_active_admission_date" type="checkbox" class="chk" value="1" <?php echo set_checkbox('is_active_admission_date', '1', (set_value('is_active_admission_date') == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_admission_date" class="label-success"></label>
                                    </div>
                                </div>

                                <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('mother_tongue'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_mother_tongue" name="is_mother_tongue" type="checkbox" class="chk" value="1" <?php echo set_checkbox('is_active_mother_tongue', '1', (set_value('is_active_mother_tongue') == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_mother_tongue" class="label-success"></label>
                                    </div>
                                </div>

                                <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('date_tc'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_date_tc" name="is_active_date_tc" type="checkbox" class="chk" value="1" <?php echo set_checkbox('is_active_date_tc', '1', (set_value('is_active_date_tc') == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_date_tc" class="label-success"></label>
                                    </div>
                                </div>

                                <div class="form-group switch-inline">
                                    <label><?php echo $this->lang->line('caste'); ?></label>
                                    <div class="material-switch switchcheck">
                                        <input id="enable_caste" name="is_active_caste" type="checkbox" class="chk" value="1" <?php echo set_checkbox('is_active_caste', '1', (set_value('is_active_caste') == 1) ? TRUE : FALSE); ?>>
                                        <label for="enable_caste" class="label-success"></label>
                                    </div>
                                </div>

                                
                            </div>
                            


                            <div class="box-footer">
                                <button type="submit" id="submitbtn" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>



                        </form>
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('student_id_card', 'can_add')) {
                echo "8";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="box box-primary" id="hroom">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('student_id_card_list'); ?></h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="mailbox-messages table-responsive overflow-visible">
                            <div class="download_label"><?php echo $this->lang->line('student_id_card_list'); ?></div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('id_card_title'); ?></th>
                                        <th><?php echo $this->lang->line('background_image'); ?></th>
                                        <th class="text text-center"> <?php echo $this->lang->line('design_type'); ?> </th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($idcardlist)) {
                                        ?>

                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($idcardlist as $idcard) {
                                            ?>
                                            <tr>
                                                <td class="mailbox-name">                                               
                                                    
                                                    <a data-id="<?php echo $idcard->id ?>" class="btn btn-default btn-xs view_data"  data-toggle="tooltip" ><?php echo $idcard->title; ?></a>                             
                                                </td>
                                                <td class="mailbox-name">
                                                    <?php if ($idcard->background != '' && !is_null($idcard->background)) { ?>
                                                        <img src="<?php echo $this->media_storage->getImageURL('uploads/student_id_card/background/'.$idcard->background) ?>" width="40">
                                                    <?php } else { ?>
                                                        <i class="fa fa-picture-o fa-3x" aria-hidden="true"></i>
                                                    <?php } ?>
                                                </td>
                                                   <td class="mailbox-name text text-center">
                                                    <?php echo ($idcard->enable_vertical_card) ? 
                                                    $this->lang->line('vertical') :$this->lang->line('horizontal')  ?>
                                                </td>
                                                <td class="mailbox-date pull-right no-print">
                                                    <a data-id="<?php echo $idcard->id ?>" class="btn btn-default btn-xs view_data"  data-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>">
                                                        <i class="fa fa-reorder"></i>
                                                    </a>
                                                    <?php
                                                    if ($this->rbac->hasPrivilege('student_id_card', 'can_edit')) {
                                                        ?>
                                                        <a href="<?php echo base_url(); ?>admin/tcgeneration/edit/<?php echo $idcard->id ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <?php
                                                    }
                                                    if ($this->rbac->hasPrivilege('student_id_card', 'can_delete')) {
                                                        ?>
                                                        <a href="<?php echo base_url(); ?>admin/tcgeneration/delete/<?php echo $idcard->id ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
        <div class="row">
            <div class="col-md-12">
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<!-- Modal -->
<!-- <div class="modal fade" id="certificateModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('view_id_card'); ?></h4>
            </div>
            <div class="modal-body" id="certificate_detail">
            <div class="modal-inner-loader"></div>
            <div class="modal-inner-content">
          
            </div> 
            </div>
        </div>
    </div>
</div> -->

<div class="modal fade" id="certificateModal" role="dialog" style="width: 100%;" >
    <div class="modal-dialog modal-lg" style="width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('view_certificate'); ?></h4>
            </div>
            <div class="modal-body" id="certificate_detail">
                <div class="modal-inner-loader"></div>
                <div class="modal-inner-content"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#header_color").colorpicker();
        $(document).on('click','.view_data',function(){
    
            $('#certificateModal').modal("show");
            var certificateid = $(this).data('id');
            $.ajax({
                url: "<?php echo base_url('admin/tcgeneration/view') ?>",
                method: "post",
                data: {certificateid: certificateid},
                 beforeSend: function() {
      
                  },
                success: function (data) {
                 $('#certificateModal .modal-inner-content').html(data);
                 $('#certificateModal .modal-inner-loader').addClass('displaynone');

                 },
                error: function(xhr) { // if error occured
                 alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                },
                complete: function() {
                 
                }
            });
        });       
    });

    $('#certificateModal').on('hidden.bs.modal', function (e) {
        $('#certificateModal .modal-inner-content').html("");
        $('#certificateModal .modal-inner-loader').removeClass('displaynone');
     });
</script>
<script type="text/javascript">
    function valueChanged()
    {
        if ($('#enable_student_img').is(":checked"))
            $("#enableImageDiv").show();       
        else
            $("#enableImageDiv").hide();       
    }
</script>
<script>
    $(function(){
        $('#form1'). submit( function() {
            $("#submitbtn").button('loading');
        });
    })
</script>