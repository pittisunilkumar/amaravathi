
<?php 
if(!empty($result)){
?>

<div class="table-responsive">     
    <table class="table table-strippedn table-hover mb10">
        <thead>
            <tr class="active">
                <th colspan="2"><?php echo $this->lang->line('term'); ?></th>  
                <th colspan="2"><?php echo $this->lang->line('exam_name'); ?></th>
               
                <th><?php echo $this->lang->line('weightage'); ?></th>
                <th class="text-center"><?php echo $this->lang->line('grading'); ?></th>
                <th class="text-center"><?php echo $this->lang->line('teacher_remark'); ?></th>                
            </tr>
        </thead>
        <tbody>
        <?php  foreach($result as $key=>$value){
           
if($templatedata['marksheet_type'] == "term_wise"){
    ?> 
            <tr>
                <td><input type="radio" class="checkbox checkBoxExam termcheckbox" data-id="<?php echo $key; ?>" name="terms[]" value="<?php echo $key; ?>" <?php if(isset($templatedata['term_exam']) && array_key_exists($key,$templatedata['term_exam'])){ echo "checked"; }?> > </td>
                <td><?php echo $value['name']; ?></td>
                <td colspan="5"></td>
            </tr>
            <?php 
            foreach ($value['exam'] as $examkey => $examvalue) { 
                ?>
            <tr>
                <td colspan="2"></td>
                <td >
                    <input type="checkbox" class="checkbox checkBoxExam examcheckbox_<?php echo $key;?>" name="exam[<?php echo $key;?>][<?php echo $examvalue['id'];?>]" value="<?php echo $examvalue['id'];?>" <?php if(isset($templatedata['term_exam'][$key]) && array_key_exists($examvalue['id'],$templatedata['term_exam'][$key])){ echo "checked"; }?>> 
                </td>
                <td ><?php echo $examvalue['name'];?></td>
              
                <td>
                    <input type="number" min="0" max="100"  class="form-control"  name="weightage[<?php echo $key;?>][<?php echo $examvalue['id'];?>]" value="<?php if(isset($templatedata['term_exam'][$key][$examvalue['id']])){ echo $templatedata['term_exam'][$key][$examvalue['id']]['weightage']; }?>">
                </td>
              
                <td >
                    <div class="d-flex justify-content-center">
                        <input type="radio" class="checkbox checkBoxExam" name="grading" <?php if($examvalue['id']==$templatedata['gradeexam_id']){ echo "checked";} ?> value="<?php echo $examvalue['id'];?>"> 
                    </div>    
                </td>
                <td >
                    <div class="d-flex justify-content-center">
                        <input type="radio" <?php if($examvalue['id']==$templatedata['remarkexam_id']){ echo "checked";} ?> class="checkbox checkBoxExam" id="remark_<?php echo $key;?>" name="teacher_remark" value="<?php echo $examvalue['id'];?>"> 
                    </div>    
                </td>
            </tr>
            <?php 
        }

}else{
    ?> 
            <tr>
                <td><input type="radio" class="checkbox checkBoxExam termcheckbox" data-id="<?php echo $key; ?>" name="terms[]" value="<?php echo $key; ?>"> </td>
                <td><?php echo $value['name']; ?></td>
                <td colspan="5"></td>
            </tr>
            <?php 
            foreach ($value['exam'] as $examkey => $examvalue) { 
                ?>
            <tr>
                <td colspan="2"></td>
                <td >
                    <input type="checkbox" class="checkbox checkBoxExam examcheckbox_<?php echo $key;?>" name="exam[<?php echo $key;?>][<?php echo $examvalue['id'];?>]" value="<?php echo $examvalue['id'];?>"> 
                </td>
                <td ><?php echo $examvalue['name'];?></td>
              
                <td class="width150">
                    <input type="number" min="0" max="100"  class="form-control"  name="weightage[<?php echo $key;?>][<?php echo $examvalue['id'];?>]" value="">
                </td>
              
                <td >
                    <div class="d-flex justify-content-center">
                        <input type="radio" class="checkbox checkBoxExam" name="grading"  value="<?php echo $examvalue['id'];?>"> 
                    </div>    
                </td>
                <td >
                    <div class="d-flex justify-content-center">
                        <input type="radio" class="checkbox checkBoxExam" id="remark_<?php echo $key;?>" name="teacher_remark" value="<?php echo $examvalue['id'];?>"> 
                    </div>    
                </td>
            </tr>
            <?php 
        }

}


         
         } 
         ?>
          </tbody> 
    </table> 
</div>
<?php
}else{
    ?>
    <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
    <?php
}
?>

<script>
    $('.termcheckbox').change(function(){        
        $('input:checkbox').removeAttr('checked');          
        var termcheckbox = $(this).attr('data-id');         
        if(this.checked){
            $(".examcheckbox_" + termcheckbox).prop('checked', true);                
        }        
    });   
</script>