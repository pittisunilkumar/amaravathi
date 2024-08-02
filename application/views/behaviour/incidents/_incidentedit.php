<div class="row">
    <div class="col-md-12">
        <input type="hidden" name="incident_id" value="<?php echo $incidentlist['id']; ?>">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><?php echo $this->lang->line('title'); ?></label><small class="req"> *</small>
                    <input name="title" type="text" class="form-control" value="<?php echo $incidentlist['title']; ?>" />
                    <span class="text-danger"><?php echo form_error('title'); ?></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label><?php echo $this->lang->line('point'); ?></label><small class="req"> *</small>
                    <input name="point" type="number" class="form-control"  value="<?php echo $incidentlist['point']; ?>" />
                    <span class="text-danger"><?php echo form_error('point'); ?></span>
                </div>
            </div>
            <div class="col-md-6">  
                <div class="form-group">
                    <label><?php echo $this->lang->line('is_this_negative_incident'); ?></label><br>
                    <input name="negative_incident" type="checkbox" value="1" <?php if(isset($incidentlist['negative_incident'])){if($incidentlist['negative_incident'] == 1){echo "checked";}} ?> />
                    <span class="text-danger"><?php echo form_error('negative_incident'); ?></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label><?php echo $this->lang->line('description'); ?></label><small class="req"> *</small>
                    <textarea name="description" rows="5" class="form-control"><?php echo $incidentlist['description']; ?></textarea>
                    <span class="text-danger"><?php echo form_error('description'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>