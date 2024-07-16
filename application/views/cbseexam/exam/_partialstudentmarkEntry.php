<form method="post" action="<?php echo site_url('cbseexam/exam/entrymarks') ?>" id="assign_form1111">

    <input type="hidden" name="cbse_exam_timetable_id" value="<?php echo $timetable_id; ?>">                                
    <?php
    if (isset($resultlist) && !empty($resultlist)) {
        ?>
        <div class="row">
            <div class="col-md-12">               
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr class="white-space-nowrap">
                                <th><?php echo $this->lang->line('admission_no'); ?></th>
                                <th><?php echo $this->lang->line('roll_no'); ?></th>
                                <th><?php echo $this->lang->line('student_name'); ?></th> 
                                <th><?php echo $this->lang->line('class'); ?></th> 
                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <?php 
                                foreach ($exam_assessment_types as $key => $value) {
                                    $value=(array)$value;
                                    ?>
                                     <th><?php echo $value['name']; ?></th>
                                    <?php
                                }
                                ?>
                                <th><?php echo $this->lang->line('note') ?></th>
                            </tr>
                        </thead>
                        <tbody>
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
                                    <tr class="std_adm_<?php echo $student['admission_no']; ?>">
                              <input type="hidden" name="exam_student_id[]" value="<?php echo $student['exam_student_id']; ?>">
                                
                                <td><?php echo $student['admission_no']; ?></td>
                                <td><?php 
                          
                                if($exam['use_exam_roll_no'] != 0){
                                    echo $student['exam_roll_no'];
                                }else{                                    
                                    echo ($student['roll_no'] != 0) ? $student['roll_no'] : '-';
                                }

                                 ?></td>
                                <td><?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname);?></td>
                                <td><?php echo ($student['class_name']."(".$student['section_name'].")"); ?></td>
                                <td><?php echo $student['father_name']; ?></td>                                
                                <td><?php echo $this->lang->line(strtolower($student['gender'])); ?> </td>
                                <?php 
                                foreach ($exam_assessment_types as $key => $value) {
                                    $value=(array)$value;
                                    ?>
                                <td class="white-space-nowrap">
                                    <div class="form-group mb0">     
    
     <?php 
     $absent_status=0;
     if(!empty($student['marks'][$value['id']])){
      $absent_status= ($student['marks'][$value['id']]['is_absent']) ? 1 : 0;
    } 
    ?>

        <label class="d-flex align-items-center gap-0-5">
          <input type="checkbox" name="absent[<?php echo $student['exam_student_id'];?>][<?php echo $value['id']?>]" value="1" <?php echo ($absent_status) ? "checked='checked'" :""; ?> class="check_absent mt-0"> <?php echo $this->lang->line('mark_as_absent'); ?>
        </label>     

  </div>
                                    <input type="number" data-marks="<?php echo $value['maximum_marks']; ?>" class="mark  w-150 form-control" name="mark[<?php echo $student['exam_student_id'];?>][<?php echo $value['id']?>]" value="<?php if(!empty($student['marks'][$value['id']]['marks'])){ echo $student['marks'][$value['id']]['marks']; } ?>" step="any" placeholder="<?php echo $this->lang->line('max_mark'); ?>: <?php echo $value['maximum_marks'];?>" <?php echo ($absent_status) ? "readonly='readonly'" :""; ?>></td>
                                    <?php
                                }
                                ?>
                                <td> 

                                    <input type="text" class="form-control note noteinput" name="exam_student_note[<?php echo $student['exam_student_id'];?>]" value="<?php if(!empty($student['marks'][$value['id']]['note'])){ echo $student['marks'][$value['id']]['note']; } ?>"></td>

                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($this->rbac->hasPrivilege('cbse_exam_marks', 'can_edit')) { ?>
                    <div class="modal-footer clearboth mx-nt-lr-15 pb0">
                        <button type="submit" class="allot-fees btn btn-primary pull-right" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please Wait.."><?php echo $this->lang->line('save'); ?>
                        </button>
                    </div>
                <?php } ?>

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
</form>