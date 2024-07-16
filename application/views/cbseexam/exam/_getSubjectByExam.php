<div class="row pb10">
    <div class="col-lg-2 col-md-3 col-sm-12">   
        <p class="examinfo"><span><?php echo $this->lang->line('exam')?></span><?php echo $examDetail['name']; ?></p>
    </div> 
    <div class="col-lg-10 col-md-9 col-sm-12">   
        <p class="examinfo"><span><?php echo $this->lang->line('class_section'); ?></span><?php echo $examDetail['class_sections']; ?></p>
    </div> 
</div><!--./row-->
<div class="divider2"></div>
<div class="table-responsive row">
    <table class="table table-bordered" id="subjects_table">
        <thead>
            <tr>
                <th class="col-sm-3"><?php echo $this->lang->line('subject')?></th>
                <th class=""><?php echo $this->lang->line('date');?></th>
                <th class=""><?php echo $this->lang->line('start_time'); ?></th>             
                <th class=""><?php echo $this->lang->line('room_no')?></th>                     
                <th class="text-right"><?php echo $this->lang->line('enter_marks'); ?></th>
            </tr>
        </thead>
        <tbody>
    <?php
        if (!empty($exam_subjects)) {
            foreach ($exam_subjects as $exam_subject_key => $exam_subject_value) {
            $exam_subject_value=(array)$exam_subject_value;  
    ?>
            <tr>
                <td><?php echo $exam_subject_value['subject_name']." (".$exam_subject_value['subject_code'].")"; ?></td>
                <td><?php echo $this->customlib->dateformat($exam_subject_value['date']); ?></td>
                <td><?php echo $exam_subject_value['time_from']; ?></td>
                <td><?php echo $exam_subject_value['room_no']; ?></td>
                <td class="col-sm-1 text-right">                                             
                    <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#subjectModal" data-subject_name="<?php echo $exam_subject_value['subject_name']; ?>" data-exam_id="<?php echo $examDetail['id']; ?>" data-subject_id="<?php echo $exam_subject_value['subject_id']; ?>" data-timetable_id="<?php echo $exam_subject_value['id']; ?>" ><i class="fa fa-newspaper-o"  aria-hidden="true"></i></button>
                </td>
            </tr>
    <?php
            }
        }
    ?>
        </tbody>
    </table>
</div>    