<?php
defined('BASEPATH') or exit('No direct script access allowed');



function find_subject_assessment_exists($subject_assessments, $cbse_exam_timetable_id, $cbse_exam_assessment_type_id)
{

    if (!empty($subject_assessments)) {
        foreach ($subject_assessments as $key => $value) {
            if ($value->id == $cbse_exam_timetable_id) {
                if (!empty($value->subject_assessments)) {
                    foreach ($value->subject_assessments as $askey => $asvalue) {
                        if ($asvalue->cbse_exam_timetable_id == $cbse_exam_timetable_id  && $asvalue->cbse_exam_assessment_type_id == $cbse_exam_assessment_type_id) {
                            return true;
                            break;
                        }
                    }
                }
            }
        }
    }
    return false;
}



function findAssessmentValue($find_subject_id, $find_cbse_exam_assessment_type_id, $student_value)
{


    $return_array = [
        'maximum_marks' => 0,
        'marks' =>0,
        'note' => "",
        'is_absent' => "",
    ];


    if (property_exists($student_value, 'subjects')) {

        
         
            $get_subject_id= searchForId($find_subject_id, $student_value->exam_data['subjects']);

            if (!is_null($get_subject_id)) {

          
            $result_array = ($student_value->exam_data['subjects'][$get_subject_id]['exam_assessments'][$find_cbse_exam_assessment_type_id]);
            $return_array = [
                'maximum_marks' => $result_array['maximum_marks'],
                'marks' => is_null($result_array['marks']) ? "N/A" : $result_array['marks'],
                'note' => $result_array['note'],
                'is_absent' => $result_array['is_absent'],
            ];

        }
    }


    return $return_array;
}


function searchForId($find_subject_id, $subjects) {
    foreach ($subjects as $key => $val) {
        if ($val['subject_id'] == $find_subject_id) {
            return $key;
        }
    }
    return NULL;
 }


function getGrade($grade_array, $Percentage)
{

    if (!empty($grade_array)) {
        foreach ($grade_array as $grade_key => $grade_value) {

            if ($grade_value->minimum_percentage <= $Percentage) {
                return $grade_value->name;
                break;
            } elseif (($grade_value->minimum_percentage >= $Percentage && $grade_value->maximum_percentage <= $Percentage)) {

                return $grade_value->name;
                break;
            }

        }

    }
    return "-";

}

function getPercent($total, $obtain)
{

    $percent = 0;
    if ($total > 0) {
        $percent = ($obtain * 100) / $total;

    }
    return number_format((float) $percent, 2, '.', '');
}


?>