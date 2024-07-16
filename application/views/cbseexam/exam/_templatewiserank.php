<?php

                   if (isset($studentList)) {
                 if(!empty($studentList)){
           ?>
                        <form method="post" action="<?php echo base_url('cbseexam/exam/rankgenerate') ?>" id="rankgenerate">
                            <input type="hidden" name="cbse_template_id" value="<?php echo set_value('cbse_template_id',$cbse_template_id); ?>">

                            <div class="box-header ptbnull"></div>  
                       
                            <div class="box-body">
                                    <div class="tab-pane active table-responsive no-padding" id="tab_1">
                                    <table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                           
                                                <th><?php echo $this->lang->line('admission_no'); ?></th>
                                                <th><?php echo $this->lang->line('student_name'); ?></th>
                                                <th><?php echo $this->lang->line('class'); ?></th>                        
                                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                                <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                                <th><?php echo $this->lang->line('gender'); ?></th>                                             
                                                <th class=""><?php echo $this->lang->line('mobile_no'); ?></th>
                                                <th class="text-center"><?php echo $this->lang->line('rank'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (empty($studentList)) {
                                                ?>

                                                <?php
                                            } else {
                                                $count = 1;
                                                foreach ($studentList as $student_key => $student_value) {
                                                
                                                    ?>
                                                    <tr>
                                                       
                                                        <td>  <input type="hidden" name="student_session_id[]" value="<?php echo $student_value->student_session_id?>"/>
                                                  
                                                            <?php echo $student_value->admission_no; ?></td>
                                                        <td>
            <a href="<?php echo base_url(); ?>student/view/<?php echo $student_value->id; ?>"><?php echo $this->customlib->getFullName($student_value->firstname,$student_value->middlename,$student_value->lastname,$sch_setting->middlename,$sch_setting->lastname); ?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo $student_value->class."(".$student_value->section.")"; ?></td>
                                                        <td><?php echo $student_value->father_name; ?></td>
                                                        <td><?php 
                                                            if (!empty($student_value->dob) && $student_value->dob != '0000-00-00') {
                                                            echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student_value->dob)); }?></td>
                                                        <td><?php echo $this->lang->line(strtolower($student_value->gender)); ?></td>                  
                                                        <td><?php echo $student_value->mobileno; ?></td>
                                                        <td class="text-center"><?php echo $student_value->rank;?></td>
                                                       
                                                    </tr>
                                                    <?php
                                                    $count++;
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>                                                                      <div class="col-sm-12">
                                <div class="form-group">
                                <button type="submit" name="search"  class="btn btn-primary pull-right btn-sm checkbox-toggle" autocomplete="off"><?php echo $this->lang->line('generate_rank');?></button>
                                </div>
                            </div>     
                            </div> 
                        </form>
                    </div>
                    <?php

}else{
                    ?>
                    <div class="box-body row">
                        <div class="col-md-12">                            
<div class="alert alert-danger">
    <?php echo $this->lang->line('no_record_found');?>
</div>
                        </div>
                    </div>
                    <?php
                }
                }
                ?>