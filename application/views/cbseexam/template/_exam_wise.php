<?php 
if(!empty($result)){
?>
<table class="table table-strippedn table-hover mb10">
    <thead>
        <tr class="active">
            <th width="20%"><?php echo $this->lang->line('term'); ?></th> 
            <th colspan="2"><?php echo $this->lang->line('exam_name'); ?></th>              
        </tr>
    </thead>
    <tbody>
	<?php  
		foreach($result as $key=>$value){
	?> 
		<tr>			
			<th><?php echo $value['name']; ?></th>
			<td colspan="2"></td>
		</tr>
        <?php 
        foreach ($value['exam'] as $examkey => $examvalue) { 
            if($templatedata['marksheet_type'] == "exam_wise"){
                     ?>
        <tr>
            <td></td>
            <td width="4%">
                <input <?php if(isset($templatedata['exam_without_term']) && array_key_exists($examvalue['id'],$templatedata['exam_without_term'])){ echo "checked"; }?> type="radio" class="checkbox checkBoxExam" name="exam[<?php //echo $examvalue['id'];?>]" value="<?php echo $examvalue['id'];?>">
            </td>
            <td ><?php echo $examvalue['name'];?></td>
        </tr>
        <?php
            }else{
                     ?>
        <tr>
            <td></td>
            <td width="4%"><input type="radio" class="checkbox checkBoxExam" name="exam[<?php //echo $examvalue['id'];?>]" value="<?php echo $examvalue['id'];?>"></td>
            <td ><?php echo $examvalue['name'];?></td>
        </tr>
        <?php
            }       
         }    
	}
?>
    </tbody>    
</table>

<?php
}else{
    ?>
    <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
    <?php
}
?>
