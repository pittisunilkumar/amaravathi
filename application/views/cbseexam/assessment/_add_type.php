<div id="<?php echo $delete_string;?>">    
         <div class="row">
                  <div class="col-md-12">
                           <div class="col-md-2">
                                    <div class="form-group">                                   
                                    <input type="hidden" name="assessment_type_id[]" value="<?php echo $result['id']?>">
                                    <input class="form-control" value="<?php echo $result['name']?>" name="type_name[]" />
                                    </div>
                           </div>
                           <div class="col-md-2">
                                    <div class="form-group">                                    
                                    <input class="form-control" value="<?php echo $result['code']?>" name="code[]" />
                                    </div>
                           </div>
                           <div class="col-md-2">
                                    <div class="form-group">                                   
                                    <input value="<?php echo $result['maximum_marks']?>" class="form-control" name="maximum_marks[]" />
                                    </div>
                           </div>
                           <div class="col-md-2">
                                    <div class="form-group">                                   
                                    <input value="<?php echo $result['pass_percentage']?>" class="form-control" name="pass_percentage[]" />
                                    </div>
                           </div>
                           <div class="col-md-3">
                                    <div class="form-group">                                    
                                    <textarea type="text" name="type_description[]" class="form-control"  rows="2"><?php echo $result['description']?></textarea>
                                    </div>
                            </div>
                            <div class="col-md-1">
                                    <div class="form-group">
                                    <span <?php if(empty($result)){ ?> onclick="remove('<?php echo $delete_string;?>')" <?php }else{ ?> onclick="remove_edit('<?php echo $delete_string;?>')" <?php }?>  class="section_id_error text-danger rtl-float-right cursor-pointer"><i class="fa fa-remove"></i></span>
                                    </div>
                            </div>
                  </div>                    
         </div>
</div>