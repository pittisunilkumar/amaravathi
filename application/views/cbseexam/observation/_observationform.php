<form role="form" id="form_add" method="post" enctype="multipart/form-data" action="<?php echo site_url('cbseexam/observation/add') ?>">
    <input type="hidden" name="action" value="<?php echo $action; ?>">                   
<?php 
if($action == "add"){
?>
                  <div class="form-group">
                            <label><?php echo $this->lang->line('observation'); ?></label><small class="req"> *</small>
                         <input type="text" id="name" name="observation" class="form-control">
                        
                    </div>                    
                    <div class="form-group">
                            <label><?php echo $this->lang->line('observation_description'); ?></label>
                         <textarea type="text" id="description" name="description" class="form-control" cols="115" rows="3"></textarea>
                    </div>                    
                        <div class="form-group row">
                            <div class="col-md-12">
                            <lebel class="btn btn-xs btn-info pull-right addrow"><?php echo $this->lang->line('add_more')?></lebel>
                        </div></div>                    
                        <div class="row">
                            <div class="col-md-6">
                                <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('parameter')?></label> <small class="req"> *</small>    
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('max_marks')?></label><small class="req"> *</small>   
                                </div>
                            </div>                            
                            <div class="col-md-1"></div>                   
                        </div> 
                               <div id="grade_result">      
     <div class="row">
        <input type="hidden" name="row[]" value="1">
         <input type="hidden" name="update_id[]" value="0">
         <div class="col-md-6">
                                    <div class="form-group">                                   
                                   <select class="form-control" name="parameter_1">
                           <option value=""><?php echo $this->lang->line('select')?></option>
                            <?php 
                            foreach ($parameters as $parameter_key => $parameter_value) {
                               ?>
  <option value="<?php echo $parameter_value->id; ?>"><?php echo $parameter_value->name; ?></option>
                               <?php
                            } ?>
                        </select>
                                    </div>
                           </div>
                           <div class="col-md-5">
                                    <div class="form-group">                                    
                                    <input type="number" name="max_marks_1" class="form-control">
                                    </div>
                           </div>                     
         <div class="col-md-1">
             <div class="form-group">
                  <span class="section_id_error text-danger rtl-float-right cursor-pointer"><i class="fa fa-remove remove_row"></i></span>
                                   
             </div>
         </div>
     </div>                    
      </div>
<?php
}elseif ($action == "update") {
    ?>
        <input type="hidden" name="record_id" value="<?php echo $get_old_data['id'];?>">
           <div class="form-group">
                            <label><?php echo $this->lang->line('observation'); ?></label><small class="req"> *</small>
                         <input type="text" id="name" name="observation" class="form-control" value="<?php echo $get_old_data['name']; ?>">                        
                    </div>                    
                    <div class="form-group">
                            <label><?php echo $this->lang->line('observation_description'); ?></label>
                         <textarea type="text" id="description" name="description" class="form-control" cols="115" rows="3"><?php echo $get_old_data['description']; ?></textarea>
                    </div>                    
                        <div class="form-group row">
                            <div class="col-md-12">
                            <lebel class="btn btn-xs btn-info pull-right addrow" ><?php echo $this->lang->line('add_more')?></lebel>
                        </div></div>                
                         <div class="row">                    
                            <div class="col-md-6">
                                <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('parameter')?></label> <small class="req"> *</small>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('max_marks')?></label> <small class="req"> *</small>  
                                </div>
                            </div>                        
                            <div class="col-md-1"></div>              
                        </div> 

                           <div id="grade_result">
      <?php 
$i=0;
      foreach ($get_old_data['list'] as $list_key => $list_value) {     

$i++;
         ?>
         <input type="hidden" name="prev_ids[]" value="<?php echo $list_value['id']; ?>">
  <div class="row">
        <input type="hidden" name="row[]" value="<?php echo $i;?>">
        <input type="hidden" name="update_id[]" value="<?php echo $list_value['id']; ?>">
              <div class="col-md-6">
                                    <div class="form-group">                                   
                                   <select class="form-control" name="parameter_<?php echo $i;?>">
                           <option value=""><?php echo $this->lang->line('select')?></option>
                            <?php 
                            foreach ($parameters as $parameter_key => $parameter_value) {
                               ?>
           <option value="<?php echo $parameter_value->id; ?>" <?php echo set_select('parameter_' . $i, $parameter_value->id, ($list_value['cbse_observation_parameter_id'] == $parameter_value->id) ? true : false); ?>><?php echo $parameter_value->name; ?></option>
                               <?php
                            } ?>
                        </select>
                                    </div>
                           </div>
                           <div class="col-md-5">
                                    <div class="form-group">
                                    
                                    <input type="number" name="max_marks_<?php echo $i;?>"  value="<?php echo $list_value['maximum_marks'] ?>" class="form-control">
                                    </div>
                           </div>                     
         <div class="col-md-1">
             <div class="form-group">
                  <span class="section_id_error text-danger rtl-float-right cursor-pointer"><i class="fa fa-remove remove_row"></i></span>                                   
             </div>
         </div>
     </div>
         <?php
       } 
       ?>   
</div>
    <?php
}
?>
             <div class="modal-footer clearboth mx-nt-lr-15 pb0">
                        <button type="submit" class="btn btn-primary pull-right" data-loading-text="<?php echo $this->lang->line('submitting') ?>" value=""><?php echo $this->lang->line('save'); ?></button>
                </div> 
       </form>