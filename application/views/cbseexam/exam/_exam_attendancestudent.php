<?php
    if (isset($resultlist) && !empty($resultlist)) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <form id="formadd" method="post" class="ptt10" enctype="multipart/form-data">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('admission_no'); ?></th>
                                <th><?php echo $this->lang->line('roll_no'); ?></th>
                                <th><?php echo $this->lang->line('student_name'); ?></th> 
                                <th><?php echo $this->lang->line('class'); ?></th>
                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <th><?php echo $this->lang->line('total_present_days'); ?></th> 
                            </tr>
                        </thead>
                        <tbody>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label"><?php echo $this->lang->line('total_attendance_days'); ?> <small class="req"> *</small></label>
                                    <div class="col-sm-2">
                                      <input type="text" name="total_working_days" class="form-control" value="<?php echo $resultlist[0]['total_working_days']; ?>">
                                    </div>
                                </div>
                            </div>     
                            <input type="hidden" class="form-control" name="exam_id" value="<?php echo $exam_id; ?>">

                            <?php
                            if (empty($resultlist)) {
                                ?>
                                <tr>
                                    <td colspan="7" class="text-danger text-center"><?php echo $this->lang->line('no_record_found'); ?></td>
                                </tr>
                                <?php
                            } else {
                                
                                foreach ($resultlist as $student) {
                                    ?>

                                <input type="hidden" class="form-control" name="exam_student_id[]" value="<?php echo $student['exam_student_id']; ?>">

                                <tr class="cbse_exam_student_id_<?php echo $student['exam_student_id']; ?>">                               
                                <td><?php echo $student['admission_no']; ?></td>
                                <td><?php 
                          
                                if($exam['use_exam_roll_no'] != 0){
                                    echo $student['exam_roll_no'];
                                }else{
                                    
                                    echo ($student['roll_no'] != 0) ? $student['roll_no'] : '-';
                                }

                                 ?></td>
                                <td><?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname);?></td>
                                 <td><?php echo $student['class_name']."(".$student['section_name'].")"; ?></td>
                                <td><?php echo $student['father_name']; ?></td>                                
                                <td><?php echo $this->lang->line(strtolower($student['gender'])); ?></td>                             
                                <td> 
                                    <input type="text" class="form-control" name="total_present_days[<?php echo $student['exam_student_id']; ?>]" value="<?php echo $student['total_present_days']; ?>">                                     
                                    </td>
                                </tr> 
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($this->rbac->hasPrivilege('cbse_exam_attendance', 'can_edit')) { ?>
                  <div class="modal-footer clearboth mx-nt-lr-15 pb0">
                    <button type="submit" class="btn btn-info pull-right" id="submit" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait'); ?>"><?php echo $this->lang->line('save') ?></button>
                </div>
                <?php } ?>
            </form>             

            </div>
        </div>
        <?php
    } else {
        ?>

        <div class="alert alert-info">
            <?php echo $this->lang->line('no_record_found'); ?>
        </div>
        <?php
    }
    ?>

<script>
(function ($){
    "use strict";
    
$("#formadd").on('submit', (function (e) {
    e.preventDefault();

    var $this = $(this).find("button[type=submit]:focus");

    $.ajax({
        url: "<?php echo site_url("cbseexam/exam/addattendance") ?>",
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
            if (res.status == "0") {

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

})(jQuery);
</script>