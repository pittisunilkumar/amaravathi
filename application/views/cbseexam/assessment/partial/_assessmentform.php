<form role="form" id="add_form" method="post" enctype="multipart/form-data" action="<?php echo site_url('cbseexam/assessment/add') ?>">
         <input type="hidden" name="action" value="<?php echo $action; ?>">

<?php 
if($action == "add"){
?>
                  <div class="form-group">
                        <label><?php echo $this->lang->line('assessment'); ?></label><small class="req"> *</small>
                        <input type="text" id="name" name="name" class="form-control">
                        <input type="hidden" name="assessment_id" id="assessment_id" class="form-control">
                  </div>                    
                  <div class="form-group">
                        <label><?php echo $this->lang->line('assessment_description'); ?></label>
                        <textarea type="text" id="description" name="description" class="form-control" cols="115" rows="3"></textarea>
                  </div>                    
                  <div class="form-group row">
                        <div class="col-md-12">
                           <lebel class="btn btn-xs btn-info pull-right add_row"><?php echo $this->lang->line('add_more')?></lebel>
                        </div>
                  </div>                    
                 <div class="row">                    
                        <div class="col-md-12">
                           <div class="col-md-2">
                                    <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('assessment_type'); ?></label> <small class="req"> *</small>                                   
                                    </div>
                                    </div>
                           <div class="col-md-2">
                                    <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('code'); ?></label>
                                    
                                    </div>
                           </div>
                           <div class="col-md-2">
                                    <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('maximum_marks'); ?></label> <small class="req"> *</small>                                   
                                    </div>
                           </div>
                           <div class="col-md-2">
                                    <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('pass_percentage'); ?></label> <small class="req"> *</small>                                   
                                    </div>
                           </div>
                           <div class="col-md-3">
                                    <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                    </div>
                           </div>
                           <div class="col-md-1">                                    
                           </div>
                        </div>                    
                    </div>                     
                    <div id="grade_result">
      
     <div class="row">
        <input type="hidden" name="row[]" value="1">
         <input type="hidden" name="update_id[]" value="0">
         <div class="col-md-2">
                                    <div class="form-group">
                                    <input class="form-control" name="type_name_1" />
                                    </div>
                           </div>
                           <div class="col-md-2">
                                    <div class="form-group">                                    
                                    <input class="form-control"  name="code_1" />
                                    </div>
                           </div>
                           <div class="col-md-2">
                                    <div class="form-group">
                                    <input class="form-control" type="number" name="maximum_marks_1" />
                                    </div>
                           </div>
                           <div class="col-md-2">
                                    <div class="form-group">
                                    <input class="form-control" type="number" name="pass_percentage_1" />
                                    </div>
                           </div>
                           <div class="col-md-3">
                                    <div class="form-group">
                                    <textarea type="text" name="type_description_1" class="form-control"  rows="2"></textarea>
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
                        <label><?php echo $this->lang->line('assessment'); ?></label><small class="req"> *</small>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo $get_old_data['name']; ?>">
                        <input type="hidden" name="cbse_exam_assessment_id" id="cbse_exam_assessment_id" class="form-control">
                    </div>                    
                    <div class="form-group">
                        <label><?php echo $this->lang->line('assessment_description'); ?></label>
                        <textarea id="description" name="description" class="form-control"><?php echo $get_old_data['description']; ?></textarea>
                    </div>                   
                    <div class="form-group row">
                        <div class="col-md-12">
                            <lebel class="btn btn-xs btn-info pull-right add_row" ><?php echo $this->lang->line('add_more')?></lebel>
                        </div>  
                    </div> 
                      <div class="row">                    
                        <div class="col-md-12">
                           <div class="col-md-2">
                                    <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('assessment_type'); ?></label> <small class="req"> *</small>                                   
                                    </div>
                           </div>
                           <div class="col-md-2">
                                    <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('code'); ?></label>
                                    </div>
                           </div>
                           <div class="col-md-2">
                                    <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('maximum_marks'); ?></label> <small class="req"> *</small>
                                    </div>
                           </div>
                           <div class="col-md-2">
                                    <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('pass_percentage'); ?></label> <small class="req"> *</small>
                                    </div>
                           </div>
                           <div class="col-md-3">
                                    <div>
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                                    </div>
                           </div>
                           <div class="col-md-1">
                                    
                           </div>
                        </div>                    
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
         <div class="col-md-2">
             <div class="form-group" >                  
                  <input class="form-control" value="<?php echo $list_value['name'] ?>" name="type_name_<?php echo $i;?>" />
             </div>
         </div>
            <div class="col-md-2">
             <div class="form-group">
                  <input type="text" value="<?php echo $list_value['code'] ?>" class="form-control" name="code_<?php echo $i;?>" />                                   
             </div>
         </div> 
          <div class="col-md-2">
             <div class="form-group">
                  <input type="number" value="<?php echo $list_value['maximum_marks'] ?>" class="form-control" name="maximum_marks_<?php echo $i;?>" />
                                   
             </div>
         </div> 
         <div class="col-md-2">
              <div class="form-group">
                  <input type="number" class="form-control" value="<?php echo $list_value['pass_percentage'] ?>" name="pass_percentage_<?php echo $i;?>" />
                                   
             </div>
         </div> 
                
         <div class="col-md-3">
             <div class="form-group">
                  <textarea type="text" class="form-control" name="type_description_<?php echo $i;?>"><?php echo $list_value['description'] ?></textarea>
                                   
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

            <div class="mx-nt-lr-15">
                <div class="modal-footer clearboth pb0">
                        <button type="submit" class="btn btn-primary pull-right" data-loading-text="<?php echo $this->lang->line('submitting') ?>" value=""><?php echo $this->lang->line('save'); ?></button>
                </div> 
            </div>    
</form>