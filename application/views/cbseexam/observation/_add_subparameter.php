<div id="<?php echo $delete_string;?>">      
     <div class="row">
         <div class="col-md-3">
             <div class="form-group" >                 
                  <input type="hidden" name="observation_subparameter_id[]" value="<?php echo $result['id']?>">
                  <input class="form-control" value="<?php echo $result['name']?>" name="subparameter_name[]" />               
             </div>
         </div>        
         <div class="col-md-3">
             <div class="form-group">                  
                  <input type="number" value="<?php echo $result['maximum_marks']?>" class="form-control" name="maximum_marks[]" />
             </div>
         </div>        
         <div class="col-md-5">
             <div class="form-group">                  
                  <input value="<?php echo $result['description']?>" class="form-control" name="subparameter_description[]" />
                                   
             </div>
         </div>
         <div class="col-md-1">
             <div class="form-group">
                  <span <?php if(empty($result)){ ?> onclick="remove('<?php echo $delete_string;?>')" <?php }else{ ?> onclick="remove_edit('<?php echo $delete_string;?>')" <?php } ?> class="section_id_error text-danger rtl-float-right cursor-pointer"><i class="fa fa-remove"></i></span>
                                   
             </div>
         </div>
     </div>
</div>