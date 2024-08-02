<form method="post" action="<?php echo site_url('cbseexam/observation/add_observation_term_marks') ?>" id="allot_exam_student">
    <input type="hidden" name="cbse_observation_term_id" value="<?php echo $cbse_observation_term_id; ?>">
    <?php
if (isset($observationParamsList) && (!empty($observationParamsList) && !empty($studentlist))) {
    ?>
        <div class="row">
            <div class="col-md-12">
                <div class=" table-responsive ptt10">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('admission_no'); ?></th>
                                <th><?php echo $this->lang->line('student_name'); ?></th>
                                <th><?php echo $this->lang->line('class'); ?></th>
                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                <th><?php echo $this->lang->line('gender'); ?></th>
                                <?php

    foreach ($observationParamsList as $param_key => $param_value) {

        ?>
        <input type="hidden" name="cbse_observation_parameters[]" value="<?php echo  $param_value->cbse_observation_subparameter_id;?>">
 <th><?php echo $param_value->cbse_observation_parameter_name . " (" . $this->lang->line('max_marks') . $param_value->maximum_marks . ")" ?></th>
                                 	<?php
}
    ?>


                            </tr>
                        </thead>
                        <tbody>
                        <?php

    if (!empty($studentlist)) {
$row=1;
        foreach ($studentlist as $student_key => $student_value) {        	

            ?>
	<tr>
		<input type="hidden" name="row[]" value="<?php echo $row; ?>">
		<input type="hidden" name="student_session_<?php echo $row;?>" value="<?php echo $student_value->student_session_id; ?>">
  <td>
  	<?php echo $student_value->admission_no; ?></td>
  <td><?php echo $this->customlib->getFullName($student_value->firstname, $student_value->middlename, $student_value->lastname, $sch_setting->middlename, $sch_setting->lastname); ?></td>
                                        <td><?php echo $student_value->class." (".$student_value->section.")"; ?></td>
                                        <td><?php echo $student_value->father_name; ?></td>

                                        <td><?php echo $this->lang->line(strtolower($student_value->gender)); ?></td>
                                                       <?php

    foreach ($observationParamsList as $param_key => $param_value) {
            ?>
 <td>
 	<input type="hidden" name="old_cbse_observation_term_student_subparameter_id_<?php echo $student_value->student_session_id."_".$param_value->cbse_observation_subparameter_id ?>" value="<?php echo $student_value->params[$param_value->cbse_observation_parameter_id]['cbse_observation_term_student_subparameter_id']; ?>">
 	
 <input type="number" data-marks="<?php echo $param_value->maximum_marks;?>" class="form-control marksssss" name="param_value_<?php echo $student_value->student_session_id."_".$param_value->cbse_observation_subparameter_id ?>" value="<?php echo $student_value->params[$param_value->cbse_observation_parameter_id]['obtain_marks']; ?>">
 </td>
                                 	<?php
}
    ?>

</tr>
	<?php
	$row++;
}
    }
    ?>
                        </tbody>
                    </table>
                </div>
              
                    <button type="submit" class="btn btn-primary btn-sm pull-right" id="load" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please Wait.."><?php echo $this->lang->line('save'); ?>
                    </button>            
            </div>
        </div>
        <?php
} else {
    ?>
        <div class="alert alert-danger mt10 mb0"><?php echo $this->lang->line('no_student_assigned_in_examination_on_this_term'); ?> </div>
        <?php
}
?>
</form>