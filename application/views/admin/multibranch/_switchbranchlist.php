<?php 
if(!empty($branches)){
  ?>
    <div class="row">
        <div class="col-md-12">
            <div class="well well-sm">
                <div class="row">
                     <div class="col-xs-12 col-md-12 section-box">
                         <div class="row rating-desc">
                            <div class="col-md-12">
                                <label class="radio-inline">
                                    <input type="radio" value="0" name="branch"  <?php echo ($active_branch == 0) ? "checked" : ""?>> <?php echo $this->lang->line("home_branch") ?>
                                </label>
                            </div>
                        </div>
                    </div>                   
                </div>
            </div>
        </div>        
    </div>
    <?php
foreach ($branches as $branch_key => $branch_value) {
     ?>
    <div class="row">
        <div class="col-md-12">
            <div class="well well-sm">
                <div class="row">                    
                    <div class="col-xs-12 col-md-12 section-box">                      
                        <div class="row rating-desc">
                            <div class="col-md-12">
                            	<label class="radio-inline">
                                    <input type="radio" value="<?php echo $branch_value->id; ?>" class="branch_chg" name="branch" <?php echo ($branch_value->id == $active_branch) ? "checked" : ""?>> <?php echo $branch_value->branch_name; ?>
                                </label>                          
                            </div>
                        </div>
                    </div>                   
                </div>
            </div>
        </div>        
    </div>
	<?php
}
?>
<div class="row">
      <button type="submit" class="btn btn-primary pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please wait"><?php echo $this->lang->line('update');?></button>
      </div>
<?php
}else{
?>

<div class="alert alert-info">
	<?php echo $this->lang->line('no_more_branch_found_if_you_want_to_add_new_branch_please');?> <a href="<?php echo site_url('admin/multibranch/branch') ?>"> <?php echo $this->lang->line('click_here');?></a>
</div>
    <div class="row">
        <div class="col-md-12">
            <div class="well well-sm">
                <div class="row">                    
                    <div class="col-xs-12 col-md-12 section-box">                      
                        <div class="row rating-desc">
                            <div class="col-md-12">
                                <label class="radio-inline">
                                    <input type="radio" value="0" name="branch" checked> <?php echo $this->lang->line("home_branch") ?>
                                </label>                          
                            </div>
                        </div>
                    </div>                   
                </div>
            </div>
        </div>        
    </div>
<?php 	
}

 ?>