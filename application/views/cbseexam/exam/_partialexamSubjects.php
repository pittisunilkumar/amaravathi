    <div class="row pb10">
        <div class="col-lg-2 col-md-3 col-sm-12">   
            <p class="examinfo"><span><?php echo $this->lang->line('exam'); ?></span><?php echo $examDetail['name']; ?></p>
        </div>
        <div class="col-lg-10 col-md-9 col-sm-12">   
            <p class="examinfo"><span><?php echo $this->lang->line('class_section');?></span><?php echo $examDetail['class_sections']; ?></p>
        </div> 
    </div>    
    <div class="divider2"></div>
   
<div class="row">
    <div class="col-md-12 pt5">
            <button type="button" name="add" class="btn btn-primary btn-sm add pull-right" autocomplete="off"><span class="fa fa-plus"></span> <?php echo $this->lang->line('add_exam_subject');?></button>
    </div>
</div>
<form action="<?php echo site_url('cbseexam/exam/addexamsubject') ?>" method="POST" class="ssaddSubject ptt10 autoscroll">
    <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
    <div class="">
        <table class="table table-bordered" id="item_table">
            <thead>
                <tr>
                    <th class=""><?php echo $this->lang->line('subject'); ?><small class="req"> *</small></th>
                    <th class=""><?php echo $this->lang->line('date'); ?><small class="req"> *</small></th>
                    <th class=""><?php echo $this->lang->line('start_time');?><small class="req"> *</small></th>
                    <th class=""><?php echo $this->lang->line('duration_minute')?><small class="req"> *</small></th>                
                    <th class=""><?php echo $this->lang->line('room_no')?><small class="req"> *</small></th>                      
                    <th class="text-center"><?php echo $this->lang->line('action'); ?></th>              
                </tr>
            </thead>
            <?php
            if (!empty($exam_subjects)) {
   
            $count = 1;
            foreach ($exam_subjects as $exam_subject_key => $exam_subject_value) {
             ?> 
                <tr>
                    <td >
                        <select class="form-control item_unit tddm200" name="subject_<?php echo $count; ?>">
                            <option value=""><?php echo $this->lang->line('select')?></option>

                            <?php
                            if (!empty($batch_subjects)) {
                                foreach ($batch_subjects as $subject_key => $subject_value) {
                                    
                                    ?>
                                    <option value="<?php echo $subject_value['id'] ?>" <?php echo set_select('subject_' . $count, $subject_value['id'], ($exam_subject_value->subject_id == $subject_value['id']) ? true : false); ?>>
                                        <?php 
                                        $sub_code=($subject_value['code'] != "") ? " (".$subject_value['code'].")":"";
                                        echo $subject_value['name'].$sub_code; ?>
                                            
                                        </option>

                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td>                       
                        <div class="input-group datepicker_init">
                            <input class="form-control tddm200" name="date_from_<?php echo $count; ?>" type="text" value="<?php echo $this->customlib->dateformat($exam_subject_value->date); ?>">
                            <span class="input-group-addon" id="basic-addon2">
                                <i class="fa fa-calendar">
                                </i>
                            </span>
                            </input>
                        </div>
                    </td>
                    <td > 
                        <div class="input-group datepicker_init_time">
                            <input type="text" name="time_from<?php echo $count; ?>" class="form-control tddm200" value="<?php echo $exam_subject_value->time_from; ?>">
                            <span class="input-group-addon" id="basic-addon2">
                                <i class="fa fa-clock-o"></i>
                            </span>
                        </div>
                    </td>
                    <td>
                        <input type="text" name="duration<?php echo $count; ?>" class="form-control duration tddm200" value="<?php echo $exam_subject_value->duration; ?>" autocomplete="off">
                    </td>                   
                    <td >
                        <input class="form-control room_no" name="room_no_<?php echo $count; ?>" type="text" value="<?php echo $exam_subject_value->room_no ?>"/>
                         <input name="rows[]" type="hidden" value="<?php echo $count; ?>">
                        <input name="prev_row[<?php echo $count; ?>]" type="hidden" value="<?php echo $exam_subject_value->id; ?>">
                    </td>                     
                    <td  class="text-center">
                        <span class="text text-danger remove fa fa-times"></span>
                    </td>                   
                </tr>
                <?php
                $count++;
            }
        }
        ?>
    </table>
  </div>  
  <div class="modal-footer clearboth mx-nt-lr-15 pb0">
    <?php 
    if($this->rbac->hasPrivilege('cbse_exam_subjects','can_edit')){
        ?>
        <button type="submit" class="btn btn-primary pull-right" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Saving..."><?php echo $this->lang->line('save')?></button>
        <?php
    }
    ?>  
  
</div>
</form>