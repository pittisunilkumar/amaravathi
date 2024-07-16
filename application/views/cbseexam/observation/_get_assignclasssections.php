<div class="checkbox">
   <?php 
   $checked1="";
   foreach ($section_list as $key => $value) { ?>   
 <label><input type="checkbox"   id="class_section_id_<?php echo $value['id']?>" name="section[]" value="<?php echo $value['id']?>"  ><?php echo $value['section']?></label> 
    <?php 
   }
   ?>    
</div>