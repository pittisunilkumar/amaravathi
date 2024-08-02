<form role="form" id="form1" method="post" enctype="multipart/form-data" action="">
                <div class="modal-body">
                    <div id="delete_ides"></div>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('name'); ?></label><small class="req"> *</small>
                        <input type="text" id="name" name="name" class="form-control">
                        <input type="hidden" name="grade_id" id="grade_id" class="form-control">
                    </div>                    
                    <div class="form-group">
                        <label><?php echo $this->lang->line('description'); ?></label>
                        <input type="text" id="description" name="description" class="form-control">
                    </div>
                    <div class="row">
                    <div class="form-group">
                        <lebel class="btn btn-xs btn-info pull-right" onclick="add_newgrade()"><?php echo $this->lang->line('add_more')?></lebel>
                    </div>                    
                    </div>              
                    
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group" >
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('range_name'); ?></label> <small class="req"> *</small>
                            </div>
                        </div>                      
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('maximum_percentage'); ?></label> <small class="req"> *</small>
                            </div>
                        </div>  
                          <div class="col-md-3">
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('minimum_percentage'); ?></label><small class="req"> *</small>
                            </div>
                        </div>        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('range_description'); ?></label> 
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group"> 
                            </div>
                        </div>
                    </div>                    
                    <div id="grade_result" class="relative"></div>
                </div>
                 <div class="modal-footer clearboth mx-nt-lr-15 pb0">
                    
                        <button type="submit" class="btn btn-primary pull-right" data-loading-text="<?php echo $this->lang->line('submitting') ?>" value=""><?php echo $this->lang->line('save'); ?></button>
                </div> 
            </form>