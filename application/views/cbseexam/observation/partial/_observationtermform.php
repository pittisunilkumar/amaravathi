<form role="form" id="add_form" method="post" enctype="multipart/form-data" action="<?php echo site_url('cbseexam/grade/add') ?>">
    <input type="hidden" name="action" value="<?php echo $action; ?>">

<?php 
if($action == "add"){
?>
        <div class="row">
            <div class="col-md-4 col-lg-4 col-sm-6">
            <div class="form-group">
                <label><?php echo $this->lang->line('observation'); ?></label><small class="req"> *</small>
                <select autofocus="" id="cbse_exam_observation_id" name="cbse_exam_observation_id"   class="form-control">
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                    <?php
                        foreach ($observation_parameter as $observation) {
                    ?>
                    <option <?php ?> value="<?php echo $observation['id'] ?>"><?php echo $observation['name'] ?></option>
                    <?php
                        }
                    ?> 
                </select>
                <span class="text-danger" id="error_class_id"></span>
            </div>
            </div>
            <div class="col-md-4 col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('term'); ?></label><small class="req"> *</small>
                            <select  id="cbse_term_id" name="cbse_term_id"  class="form-control" >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($terms as $term_key => $term_value) {
                                        ?>
                                        <option <?php
                                       
                                        ?> value="<?php echo $term_value->id; ?>"><?php echo $term_value->name; ?> (<?php echo $term_value->term_code; ?>)</option>
                                            <?php
                                        }
                                        ?> 
                            </select>
                            <input type="hidden" name="edit" id="edit_val" value="0">
                            <span class="text-danger" id="error_class_id"></span>
                        </div>                        
                    </div>                 
                    </div>
                    <div class="row">
                         <div class="col-md-12 col-lg-12 col-sm-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('description'); ?></label><small class="req"> *</small>
                          <textarea class="form-control" id="description" name="description"></textarea>                           
                        </div>                        
                    </div>                 
                    </div>
<?php
}elseif ($action == "update") {
 
    ?>
    <input type="hidden" name="record_id" value="<?php echo $get_old_data->id;?>">
               <div class="row">
                         <div class="col-md-4 col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('observation'); ?></label><small class="req"> *</small>
                            <select autofocus="" id="cbse_exam_observation_id" name="cbse_exam_observation_id"   class="form-control">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($observation_parameter as $observation) {
                                        ?>
                                        <option value="<?php echo $observation['id'] ?>" <?php if($observation['id'] ==$get_old_data->cbse_exam_observation_id ) echo "selected"; ?>><?php echo $observation['name'] ?></option>
                                            <?php
                                        }
                                        ?> 
                            </select>
                            <span class="text-danger" id="error_class_id"></span>
                        </div>                        
                    </div>
                          <div class="col-md-4 col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('term'); ?></label><small class="req"> *</small>
                            <select  id="cbse_term_id" name="cbse_term_id"  class="form-control" >
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php
                                    foreach ($terms as $term_key => $term_value) {
                                        ?>
                                        <option  value="<?php echo $term_value->id; ?>" <?php if($term_value->id ==$get_old_data->cbse_term_id ) echo "selected"; ?>><?php echo $term_value->name; ?> (<?php echo $term_value->term_code; ?>)</option>
                                            <?php
                                        }
                                        ?> 
                            </select>
                            <input type="hidden" name="edit" id="edit_val" value="0">
                            <span class="text-danger" id="error_class_id"></span>
                        </div>                        
                    </div>                 
                    </div>
                    <div class="row">
                         <div class="col-md-12 col-lg-12 col-sm-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('description'); ?></label><small class="req"> *</small>
                          <textarea class="form-control" id="description" name="description"><?php echo $get_old_data->description; ?></textarea>
                           
                        </div>                        
                    </div>                 
                    </div>  
       
    <?php
}

 ?>            
                 <div class="modal-footer clearboth mx-nt-lr-15 pb0">                    
                        <button type="submit" class="btn btn-primary pull-right" data-loading-text="<?php echo $this->lang->line('submitting') ?>" value=""><?php echo $this->lang->line('save'); ?></button>
                </div> 
            </form>