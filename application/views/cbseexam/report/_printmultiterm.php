

<div class="btn-group  pb10" role="group" aria-label="First group">
	<button type="button" class="btn btn-default btn-xs" title="<?php echo $this->lang->line('print'); ?>" onclick="printDiv('div_print')"><i class="fa fa-print"></i></button>
	<button type="button" class="btn btn-default btn-xs" title="<?php echo $this->lang->line('download_excel'); ?>" onclick="exportToExcel('div_print')"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
</div>
<div class="table-responsive" id="div_print">
	<h4 id="print_title"><?php echo $this->lang->line('template_wise_report'); ?></h4>
	<table class="table table-bordered table-b vertical-middle">
		<thead>
			<tr>
				<th rowspan="2" class=""><?php echo $this->lang->line('student'); ?></th>
				<th rowspan="2" class=""><?php echo $this->lang->line('admission_no'); ?></th>
				<th rowspan="2" class=""><?php echo $this->lang->line('class'); ?></th>
				<th rowspan="2" class=""><?php echo $this->lang->line('date_of_birth'); ?></th>
				<?php
				foreach ($terms as $term_key => $term_value) {
				?>
					<th rowspan="2" class="text-center"><?php print_r($term_value->name . " (" . $term_value->term_code . ")"); ?></th>
				<?php
				}
				?>
				<?php
				foreach ($terms as $assess_key => $assess_value) {
					$terms_weight_array[] = $assess_value->name . " (" . $assess_value->weightage . ")";
				}

				?>
				<th valign="middle" class="text-center" colspan="4">
					<?php
					//term merge array              
					echo implode(" + ", $terms_weight_array);
					?>
				</th>
				<?php
				?>
			</tr>
			<tr>
				<td valign="middle" class="text-center bolds"><?php echo $this->lang->line('grand_total'); ?> <br /> <?php echo $this->lang->line('out_of'); ?> (100)</td>
				<td valign="middle" class="text-center bolds"><?php echo $this->lang->line('percentage'); ?> (%)</td>
				<td valign="middle" class="text-center bolds"><?php echo $this->lang->line('grade'); ?></td>
				<td valign="middle" class="text-center bolds"><?php echo $this->lang->line('rank'); ?></td>
			</tr>
		</thead>
		<tbody>
			<?php

			if (!empty($result)) {

				foreach ($result as $student_rank_wise_key => $result_value) {
					 
					$grand_total_term_percentage = 0;
					$subject_total_term_percentage = 0;
			?>
					<tr>
						<td><?php echo $result_value['firstname'] . " " . $result_value['middlename'] . " " . $result_value['lastname']; ?></td>
						<td><?php echo $result_value['admission_no']; ?></td>
						<td><?php echo $result_value['class'] . " (" . $result_value['section'] . ")"; ?></td>
						<td><?php echo $this->customlib->dateformat($result_value['dob']); ?></td>
						<?php
						$terms_subject_percentage = [];
						foreach ($terms as $term_key => $term_value) {
						?>
							<td class="text-center">
								<?php
				
								$res = getTermMarks($term_value->cbse_term_id, $result_value['terms'], $term_value->weightage,$exam_term_exam_assessment,$subject_array,$terms);
						    

								$terms_subject_percentage = getFinalTerms($res['subject_weight'], $subject_array, $terms_subject_percentage);
						
								
								if(!$res['term_status']){								
									echo "<span class='text text-danger'>". $this->lang->line('term_not_assigned')."</span>";

								}else{
									echo $res['get_marks'] . "/" . $res['maximum_marks'];
								}
								if ($res['maximum_marks'] > 0) {
									$total_term_ = (($res['get_marks'] * 100) / $res['maximum_marks']);
									$subject_total_term_percentage += ($total_term_ * ($term_value->weightage / 100));
								}

								?>
							</td>
						<?php
						}
						?>
						<td class="text-center bolds"><?php
						$subject_term = 0;
						if (!empty($terms_subject_percentage)) {
							foreach ($terms_subject_percentage as $subject_key => $subject_value) {
								$subject_term += $subject_value['terms_subject_percentage'];
							}
						}
						echo two_digit_float($subject_term) . "/" . (count($subject_array) * 100) ?>
						</td>
						<td class="text-center bolds"><?php echo two_digit_float($subject_total_term_percentage); ?></td>
						<td class="text-center bolds"><?php echo getGrade($exam_grades, $subject_total_term_percentage); ?></td>
						<td class="text-center bolds"><?php echo $result_value['rank']; ?></td>

					</tr>
			<?php

				}
			}

			?>
		</tbody>
	</table>
</div>

<?php

function getFinalTerms($subject_weight, $subjects, $terms_subject_percentage)
{
	if (!empty($subjects)) {
		foreach ($subjects as $subject_key => $subject_value) {

			if (array_key_exists($subject_key, $subject_weight)) {

				$total_term_ = (($subject_weight[$subject_key]['subject_get_marks'] * 100) / $subject_weight[$subject_key]['subject_max_marks']);

				$subject_total_term_percentage = ($total_term_ * ($subject_weight[$subject_key]['term_weight'] / 100));

				if (array_key_exists($subject_key, $terms_subject_percentage)) {
					$terms_subject_percentage[$subject_key]['terms_subject_percentage'] += $subject_total_term_percentage;
				} else {
					$terms_subject_percentage[$subject_key]['terms_subject_percentage'] = $subject_total_term_percentage;
				}
			}
		}
	}
	return $terms_subject_percentage;
}

function getGrade($grade_array, $Percentage)
{

	if (!empty($grade_array)) {
		foreach ($grade_array as $grade_key => $grade_value) {
			if ($grade_value->minimum_percentage <= $Percentage) {
				return $grade_value->name;
				break;
			} elseif ($grade_value->maximum_percentage <= $Percentage && $grade_value->minimum_percentage >= $Percentage) {

				return $grade_value->name;
				break;
			}
		}
	}
	return "-";
}

function getTermMarks($term_id, $terms, $term_weight,$exam_term_exam_assessment,$subject_array,$all_terms)
{

	$return_array = [
		'maximum_marks' => 0,
		'get_marks' => 0,
		'subject_weight' => [],
		'term_status' => true

	];


	$get_marks = 0;
	$maximum_marks = 0;
	$term_status =true;
	$subject_weight = [];
	if (!empty($terms)) {

		if (array_key_exists($term_id, $terms)) {
			foreach ($terms[$term_id]['exams'] as $term_key => $term_value) {

				if (!empty($term_value['subjects'])) {

					foreach ($term_value['subjects'] as $subject_key => $subject_value) {

						$subject_max_marks = 0;
						$subject_get_marks = 0;

						foreach ($subject_value['exam_assessments'] as $assessment_key => $assessment_value) {

							$maximum_marks += $assessment_value['maximum_marks'];
							$get_marks += is_null($assessment_value['marks']) ? 0 : $assessment_value['marks'];
							$subject_get_marks += is_null($assessment_value['marks']) ? 0 : $assessment_value['marks'];
							$subject_max_marks += $assessment_value['maximum_marks'];
						}

						if (array_key_exists($subject_value['subject_id'], $subject_weight)) {
							$subject_weight[$subject_value['subject_id']]['subject_get_marks'] += $subject_get_marks;
							$subject_weight[$subject_value['subject_id']]['subject_max_marks'] += $subject_max_marks;
							$subject_weight[$subject_value['subject_id']]['term_weight'] = $term_weight;
						} else {
							$subject_weight[$subject_value['subject_id']] = [
								'subject_get_marks' => $subject_get_marks,
								'subject_max_marks' => $subject_max_marks,
								'term_weight' => $term_weight,
							];
						}
					}
				}
				$term_status =true;
			}
		}else{
			
			if (array_key_exists($term_id, $exam_term_exam_assessment)) {
				        $subject_max_marks = 0;
						$subject_get_marks = 0;

						// print_r($exam_term_exam_assessment[$term_id]['exams']);
						foreach ($exam_term_exam_assessment[$term_id]['exams'] as $ex_key => $ex_value) {
							foreach ($subject_array as $sub_key => $sub_value) {

									foreach ($exam_term_exam_assessment[$term_id]['term_total_assessments'] as $exam_assement_key => $exam_assement_value) {
				         	$maximum_marks += ($exam_assement_value['assesment_type_maximum_marks']);
							$get_marks += ($exam_assement_value['assesment_type_maximum_marks']);

							$subject_get_marks += is_null($exam_assement_value['assesment_type_maximum_marks']) ? 0 : $exam_assement_value['assesment_type_maximum_marks'];
							$subject_max_marks += $exam_assement_value['assesment_type_maximum_marks'];
				            }
							
							$subject_weight[$sub_key]=[
		                    'subject_get_marks'=> $subject_get_marks,
							'subject_max_marks'=> $subject_max_marks,
							'term_weight'=> getTermWeightage($all_terms,$term_id)
							];		
							}
						
						}
					
			
				$term_status =false;
			}          
		}

		$return_array = [
			'get_marks' => $get_marks,
			'maximum_marks' => $maximum_marks,
			'subject_weight' => $subject_weight,
			'term_status' => $term_status
		];
	}

	return $return_array;
}


function getTermWeightage($terms,$term_id){
	foreach ($terms as $t_key => $t_value) {
		if($t_value->cbse_term_id == $term_id){
			return $t_value->weightage;
		}
	}
	return 100;

}



?>