<form role="form" id="add_form" method="post" enctype="multipart/form-data" action="<?php echo site_url('cbseexam/grade/add') ?>">
    <input type="hidden" name="action" value="<?php echo $action; ?>">
<?php 
if($action == "add"){
?>
              <div class="form-group">
                        <label><?php echo $this->lang->line('grade_title'); ?></label><small class="req"> *</small>
                        <input type="text" id="name" name="name" class="form-control">
                        <input type="hidden" name="grade_id" id="grade_id" class="form-control">
                    </div>                    
                    <div class="form-group">
                        <label><?php echo $this->lang->line('description'); ?></label>                       
                        <textarea id="description" name="description" class="form-control"></textarea>
                    </div>                    
                    <div class="form-group row">
                        <div class="col-md-12">
                            <lebel class="btn btn-xs btn-info pull-right add_row" ><?php echo $this->lang->line('add_more')?></lebel>
                        </div>               
                    </div>                  
                    
                    <div class="row">
                        <div class="col-md-2">
                            <div>
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('grade'); ?></label> <small class="req"> *</small>
                            </div>
                        </div>
                      
                        <div class="col-md-3">
                            <div>
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('maximum_percentage'); ?></label> <small class="req"> *</small>
                            </div>
                        </div>  
                          <div class="col-md-3">
                            <div>
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('minimum_percentage'); ?></label><small class="req"> *</small>
                            </div>
                        </div>        
                        <div class="col-md-3">
                            <div>
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('remark'); ?></label> 
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div> 
                            </div>
                        </div>
                    </div>                    
                    <div id="grade_result">      
     <div class="row relative">
        <input type="hidden" name="row[]" value="1">
         <input type="hidden" name="update_id[]" value="0">
         <div class="col-md-2">
             <div class="form-group" >                  
                  <input class="form-control" value="" name="range_name_1" />
             </div>
         </div>
          <div class="col-md-3">
             <div class="form-group">
                  <input type="text" value="" class="form-control" name="maximum_percentage_1" />                                   
             </div>
         </div> 
         <div class="col-md-3">
              <div class="form-group">
                  <input type="text" class="form-control" value="" name="minimum_percentage_1" />                                   
             </div>
         </div>                
         <div class="col-md-3">
             <div class="form-group">
                  <textarea type="text" class="form-control" name="type_description_1"></textarea>                                   
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
                        <label><?php echo $this->lang->line('grade_title'); ?></label><small class="req"> *</small>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo $get_old_data['name']; ?>">
                        <input type="hidden" name="grade_id" id="grade_id" class="form-control">
                    </div>                    
                    <div class="form-group">
                        <label><?php echo $this->lang->line('description'); ?></label>
                        <textarea id="description" name="description" class="form-control"><?php echo $get_old_data['description']; ?></textarea>
                    </div>                    
                    <div class="form-group row">
                        <div class="col-md-12">
                            <lebel class="btn btn-xs btn-info pull-right add_row" ><?php echo $this->lang->line('add_more')?></lebel>                  
                        </div>
                    </div>                       
                    
                    <div class="row">
                        <div class="col-md-2">
                            <div >
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('grade'); ?></label> <small class="req"> *</small>
                            </div>
                        </div>                      
                        <div class="col-md-3">
                            <div>
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('maximum_percentage'); ?></label> <small class="req"> *</small>
                            </div>
                        </div>  
                          <div class="col-md-3">
                            <div>
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('minimum_percentage'); ?></label><small class="req"> *</small>
                            </div>
                        </div>        
                        <div class="col-md-3">
                            <div>
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('remark'); ?></label> 
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div> 
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
             <div class="form-group " >                  
                  <input class="form-control" value="<?php echo $list_value['name'] ?>" name="range_name_<?php echo $i;?>" />
             </div>
         </div>
          <div class="col-md-3">
             <div class="form-group">
                  <input type="text" value="<?php echo $list_value['maximum_percentage'] ?>" class="form-control" name="maximum_percentage_<?php echo $i;?>" />                                   
             </div>
         </div> 
         <div class="col-md-3">
              <div class="form-group">
                  <input type="text" class="form-control" value="<?php echo $list_value['minimum_percentage'] ?>" name="minimum_percentage_<?php echo $i;?>" />                                   
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
                 <div class="modal-footer clearboth mx-nt-lr-15 pb0">                    
                        <button type="submit" class="btn btn-primary pull-right" data-loading-text="<?php echo $this->lang->line('submitting') ?>" value=""><?php echo $this->lang->line('save'); ?></button>
                </div> 
            </form>