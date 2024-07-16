<input type="hidden" name="id" value="<?php echo $result[0]['id'];?>">
<div class="form-group">
        <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
     <input type="text" id="name" value="<?php echo $result[0]['name'];?>" name="name" class="form-control">
     <input type="hidden" name="id" value="<?php echo $result[0]['id'];?>" id="id" class="form-control">
</div>    
<div class="form-group">
    <label><?php echo $this->lang->line('description'); ?></label>
    <textarea type="text" class="form-control" name="description" cols="115" rows="3"><?php echo $result[0]['description'];?></textarea>
</div>