<div id="<?php echo $delete_string;?>">
    <hr>
    <div class="row">
         <div class="col-md-6">
            <div class="form-group" >
                <label for="exampleInputEmail1"><?php echo $this->lang->line('exam_name'); ?></label> <small class="req"> *</small>
               <input class="form-control" value="<?php echo $result['name']?>" name="exam_name" />   
            </div>
         </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label> 
                <textarea type="text" class="form-control" name="exam_description" cols="115" rows="3"><?php echo $result['description']?></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
            <div class="checkbox-inline">
                <label>
                    <input type="checkbox" value="1" name="is_active"><?php echo $this->lang->line('publish'); ?>
                </label>
            </div>
        </div>
             <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
            <div class="checkbox-inline">
                <label>
                    <input type="checkbox" value="1" name="is_publish" autocomplete="off"> <?php echo $this->lang->line('publish_result'); ?>
                </label>
            </div>
        </div>
    </div>
</div>