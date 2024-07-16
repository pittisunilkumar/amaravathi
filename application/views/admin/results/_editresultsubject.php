<?php 
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div id="<?php echo $delete_string;?>">
          <div class="row"> 

    


        <div class="col-md-4">
            <div class="form-group" >
                <label for="exampleInputEmail1"><?php echo $this->lang->line('subject_name'); ?></label> <small class="req"> *</small>
                <input type="hidden" name="pickup_point_id[]" value="<?php echo $subjectsData['subject_id']?>">
                <h5><?php echo $subjectsData['examtype']; ?></h5>
            </div>
        </div>


        <div class="col-md-3">
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo $this->lang->line('marks_min'); ?></label> <small class="req"> *</small>
                <div class="input-group">
                  <input type="text" disabled  value="<?php echo $subjectsData['minmarks'];?>" name="minmarks[]"  class="form-control"/>
                </div>
            </div>
        </div>


        <div class="col-md-2">
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo $this->lang->line('marks_max'); ?></label> <small class="req"> *</small>
                <div class="input-group">
                    <input disabled value="<?php echo $subjectsData['maxmarks'];?>" class="form-control" name="maxmarks[]" />
                </div>
            </div>
        </div>


        <div class="col-md-2">
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo $this->lang->line('total_scored_marks'); ?> </label> <small class="req"> *</small>
                <input value="<?php echo $marks ?>" class="form-control full-width" name="actualmarks[]" />
            </div>
        </div>

     </div>
</div>