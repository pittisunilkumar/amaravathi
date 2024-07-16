<div id="<?php echo $delete_string;?>">      
     <div class="row">
         <div class="col-md-2">
             <div class="form-group" >
                  <input type="hidden" name="grade_range_id[]" value="<?php echo $result['id']?>">
                  <input class="form-control" value="<?php echo $result['name']?>" name="range_name[]" />
             </div>
         </div>
         <div class="col-md-3">
             <div class="form-group">
                  <input value="<?php echo $result['maximum_percentage']?>" class="form-control" name="maximum_percentage[]" />
                                   
             </div>
         </div> 
         <div class="col-md-3">
              <div class="form-group">
                  <input class="form-control" value="<?php echo $result['minimum_percentage']?>" name="minimum_percentage[]" />
                                   
             </div>
         </div>                 
         <div class="col-md-3">
             <div class="form-group">
                  <textarea type="text" class="form-control" name="type_description[]"><?php echo $result['description']?></textarea>
                                   
             </div>
         </div>
         <div class="col-md-1">
             <div class="form-group">
                  <span <?php if(empty($result)){ ?> onclick="remove('<?php echo $delete_string;?>')" <?php }else{ ?> onclick="remove_edit('<?php echo $delete_string;?>')" <?php }?>  class="section_id_error text-danger rtl-float-right cursor-pointer">&nbsp;<i class="fa fa-remove"></i></span>
                                   
             </div>
         </div>
     </div>
</div>