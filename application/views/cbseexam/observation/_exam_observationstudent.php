<form method="post" role="form" action="<?php echo site_url('cbseexam/observation/add_observation_marks') ?>" id="addTeacherRemark">  
    <?php
    if (isset($resultlist) && !empty($resultlist)) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('admission_no'); ?></th>
                                <th><?php echo $this->lang->line('roll_no'); ?></th>
                                <th><?php echo $this->lang->line('student_name'); ?></th> 
                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <?php 
                             foreach($sub_parameter as $key=>$value){?>
                                <th><?php echo $value['name']; ?></th> 
                            <?php } ?>
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
                                    <tr>
                                
                                <td><?php echo $student['admission_no']; ?></td>
                                <td><?php 
  									$roll_no=0; echo ($roll_no != 0) ? $roll_no : '-'; ?>	
  								</td>
                                <td><?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname);?></td>
                                <td><?php echo $student['father_name']; ?></td>
                                <td><?php echo $student['gender']; ?></td>
                             <?php 
                             foreach($sub_parameter as $key=>$value){?>
                             	<td> 
                                    <input type="text" class="form-control" name="sub_parameter[<?php echo $student['student_session_id']; ?>][<?php echo $value['cbse_observation_parameter_id']?>][<?php echo $value['id']; ?>]" value="<?php  ?>" step="any">
                                </td>
                            <?php }
                             ?>                                
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>                
                <?php if ($this->rbac->hasPrivilege('cbse_exam_assign_observation', 'can_edit')) { ?>
                    <button type="submit" class="allot-fees btn btn-primary btn-sm pull-right" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please Wait.."><?php echo $this->lang->line('save'); ?>
                    </button>
                <?php } ?>
                <br/>
                <br/>
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