<?php

if (!empty($template_data)) {
    if ($template_data->marksheet_type == "exam_wise" || $template_data->marksheet_type == "without_term") {
        if (!empty($template_data->exams) && isset($template_data->exams)) {
            foreach ($template_data->exams as $exam_key => $exam_value) {                        	
            	?>
<div class="custom-select-option checkbox"><label class="vertical-middle line-h-18">
	<input class="custom-select-option-checkbox" type="checkbox" name="exams[]" value="<?php echo $exam_value->cbse_exam_id; ?>"><?php echo $exam_value->name." (".$exam_value->exam_code.")"; ?>
</label>
</div>
            	<?php
            }
        }
    } elseif ($template_data->marksheet_type == "all_term" || $template_data->marksheet_type == "term_wise") {
    	  if (!empty($template_data->terms) && isset($template_data->terms)) {
            foreach ($template_data->terms as $term_key => $term_value) {          
            	?>
<div class="custom-select-option checkbox"><label class="vertical-middle line-h-18">
	<input class="custom-select-option-checkbox" type="checkbox" name="term[]" value="<?php echo $term_value->cbse_term_id; ?>"><?php echo $term_value->name." (".$term_value->term_code.")"; ?>
</label>
</div>
            	<?php
            }
        }
    }
}