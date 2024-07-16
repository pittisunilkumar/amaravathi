<?php

$student_allover_exam_rank = [];
foreach ($result as $result_key => $result_value) {
	$subject_total_term_percentage = 0;

	$terms_subject_percentage = [];
	foreach ($terms as $term_key => $term_value) {

		foreach ($term_value->exams as $exam_key => $exam_value) {
			$res = getTermMarks($term_value->cbse_term_id, $result_value['terms'], $exam_value->cbse_exam_id, $exam_value->weightage);

			$terms_subject_percentage = getFinalTerms($res['subject_weight'], $subject_array, $terms_subject_percentage);

			if ($res['maximum_marks'] > 0) {
				$total_term_ = (($res['get_marks'] * 100) / $res['maximum_marks']);
				$subject_total_term_percentage += ($total_term_ * ($exam_value->weightage / 100));
			}
		}
	}
	$subject_term = 0;

	if (!empty($terms_subject_percentage)) {
		foreach ($terms_subject_percentage as $subject_key => $subject_value) {
			$subject_term += $subject_value['terms_subject_percentage'];
		}
	}

	$student_allover_exam_rank[$result_value['student_session_id']] = [
		'student_session_id' => $result_value['student_session_id'],
		'firstname' => $result_value['firstname'],
		'rank_percentage' => two_digit_float($subject_total_term_percentage),
		'rank' => 0,
	];
}

$rank_overall_term_percentage_keys = array_column($student_allover_exam_rank, 'rank_percentage');
$rank_overall_term_student_name_keys = array_column($student_allover_exam_rank, 'firstname');
array_multisort($rank_overall_term_percentage_keys, SORT_DESC, $rank_overall_term_student_name_keys, SORT_ASC, $student_allover_exam_rank);

$term_rank_allover_list = unique_array($student_allover_exam_rank, "rank_percentage");

foreach ($student_allover_exam_rank as $term_rank_key => $term_rank_value) {

	$student_allover_exam_rank[$term_rank_key]['rank'] = array_search($term_rank_value['rank_percentage'], $term_rank_allover_list);
}
?>

<div class="btn-group  pb10" role="group" aria-label="First group">
	<button type="button" class="btn btn-default btn-xs" title="<?php echo $this->lang->line('print'); ?>" onclick="printDiv('div_print')"><i class="fa fa-print"></i></button>
	<button type="button" class="btn btn-default btn-xs" title="<?php echo $this->lang->line('download_excel'); ?>" onclick="exportToExcel('div_print')"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
</div>

<div class="table-responsive" id="div_print">
	<h4 id="print_title"><?php echo $this->lang->line('template_wise_report'); ?></h4>
	<table class="table table-bordered table-b vertical-middle">
		<thead>
			<tr>
				<th><?php echo $this->lang->line('student'); ?></th>
				<th><?php echo $this->lang->line('admission_no'); ?></th>
				<th><?php echo $this->lang->line('class'); ?></th>
				<th><?php echo $this->lang->line('date_of_birth'); ?></th>
				<?php
				foreach ($terms as $term_key => $term_value) {


				?>
					<th><?php echo ($term_value->name . " (" . $term_value->term_code . ")"); ?></th>
				<?php
				}
				?>
				<th class="text-center"><?php echo $this->lang->line('overall_marks'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('percentage'); ?> (%)</th>
				<th class="text-center"><?php echo $this->lang->line('grade'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('rank'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($student_allover_exam_rank as $student_exam_rank_wise_key => $student_exam_rank_wise_value) {
				$result_value = $result[$student_exam_rank_wise_value['student_session_id']];
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
						<td>

							<?php


							foreach ($term_value->exams as $exam_key => $exam_value) {
								$res = getTermMarks($term_value->cbse_term_id, $result_value['terms'], $exam_value->cbse_exam_id, $exam_value->weightage);
								$terms_subject_percentage = getFinalTerms($res['subject_weight'], $subject_array, $terms_subject_percentage);
								echo  "<span class='text text-primary bolds'>" . $exam_value->name . "</span>:  " . $res['get_marks'] . "/" . $res['maximum_marks'];
								echo "<br>";
								if ($res['maximum_marks'] > 0) {
									$total_term_ = (($res['get_marks'] * 100) / $res['maximum_marks']);
									$subject_total_term_percentage += ($total_term_ * ($exam_value->weightage / 100));
								}
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
													echo two_digit_float($subject_term) . "/" . (count($subject_array) * 100) ?></td>
					<td class="text-center bolds"><?php echo two_digit_float($subject_total_term_percentage); ?></td>
					<td class="text-center bolds"><?php echo getGrade($exam_grades, $subject_total_term_percentage); ?></td>
					<td class="text-center bolds">
						<?php echo $result_value['rank']; ?></td>
				</tr>
			<?php
			}

			?>
		</tbody>
	</table>
</div>

<?php

// function searcharray($value, $key, $array)
// {
// 	foreach ($array as $k => $val) {

// 		if ($val[$key] == $value) {
// 			return $val['rank'];
// 		}
// 	}
// 	return null;
// }

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

function getTermMarks($term_id, $terms, $cbse_exam_id, $term_weight)
{
	$return_array = [
		'maximum_marks' => "",
		'get_marks' => "",
		'subject_weight' => [],

	];

	$get_marks = 0;
	$maximum_marks = 0;
	$subject_weight = [];
	if (!empty($terms)) {

		if (array_key_exists($term_id, $terms)) {

			if (array_key_exists('exams', $terms[$term_id])) {

				if (!empty($terms[$term_id]['exams'][$cbse_exam_id]['subjects'])) {

					foreach ($terms[$term_id]['exams'][$cbse_exam_id]['subjects'] as $subject_key => $subject_value) {

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
			}
		}

		$return_array = [
			'get_marks' => $get_marks,
			'maximum_marks' => $maximum_marks,
			'subject_weight' => $subject_weight,
		];
	}
	return $return_array;
}

// function unique_array($my_array, $key)
// {
// 	$result = array();
// 	$i = 1;
// 	$key_array = array();

// 	foreach ($my_array as $val) {
// 		if (!in_array($val[$key], $key_array)) {
// 			$key_array[$i] = $val[$key];
// 			$result[$i] = $val['rank_percentage'];
// 			$i++;
// 		}
// 	}
// 	return $result;
// }

?>