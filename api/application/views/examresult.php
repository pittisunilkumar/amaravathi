<?php

if (!empty($exam_result->exam_result)) {
    $exam = new stdClass;
    $exam->exam_group_class_batch_exam_id = $exam_result->exam_group_class_batch_exam_id;
    $exam->exam_group_id = $exam_result->exam_group_id;
    $exam->exam = $exam_result->exam;
    $exam->exam_group = $exam_result->name;
    $exam->description = $exam_result->description;
    $exam->exam_type = $exam_result->exam_type;
    $exam->subject_result = array();
    $exam->total_max_marks = 0;
    $exam->total_get_marks = 0;
    $exam->total_exam_points = 0;
    $exam->exam_quality_points = 0;
    $exam->exam_credit_hour = 0;
    $exam->exam_credit_hour = 0;
    $exam->exam_result_status = "pass";
    if ($exam_result->exam_result['exam_connection'] == 0) {
        $exam->is_consolidate = 0;
        foreach ($exam_result->exam_result['result'] as $exam_result_key => $exam_result_value) {

            $subject_array = array();
            if ($exam_result_value->attendence != "present") {
                $exam->exam_result_status = "fail";
            } elseif ($exam_result_value->get_marks < $exam_result_value->min_marks) {

                $exam->exam_result_status = "fail";
            }
            $exam->total_max_marks = $exam->total_max_marks + $exam_result_value->max_marks;
            $exam->total_get_marks = $exam->total_get_marks + $exam_result_value->get_marks;
            // $subject_array['']=$exam_result_value->id;
            $percentage = ($exam_result_value->get_marks * 100) / $exam_result_value->max_marks;
            $subject_array['name'] = $exam_result_value->name;
            $subject_array['code'] = $exam_result_value->code;
            $subject_array['exam_group_class_batch_exams_id'] = $exam_result_value->exam_group_class_batch_exams_id;
            $subject_array['room_no'] = $exam_result_value->room_no;
            $subject_array['max_marks'] = $exam_result_value->max_marks;
            $subject_array['min_marks'] = $exam_result_value->min_marks;
            $subject_array['subject_id'] = $exam_result_value->subject_id;
            $subject_array['attendence'] = $exam_result_value->attendence;
            $subject_array['get_marks'] = $exam_result_value->get_marks;
            $subject_array['class_batch_subject_id'] = $exam_result_value->class_batch_subject_id;
            $subject_array['exam_group_exam_results_id'] = $exam_result_value->exam_group_exam_results_id;
            $subject_array['note'] = $exam_result_value->note;
            $subject_array['duration'] = $exam_result_value->duration;
            $subject_array['credit_hours'] = $exam_result_value->credit_hours;
            $subject_array['exam_grade'] = findExamGrade($exam_grade, $exam_result->exam_type, $percentage);

            if ($exam_result->exam_type == "gpa") {

                $point = findGradePoints($exam_grade, $exam_result->exam_type, $percentage);
                $exam->exam_quality_points = $exam->exam_quality_points + ($exam_result_value->credit_hours * $point);
                $exam->exam_credit_hour = $exam->exam_credit_hour + $exam_result_value->credit_hours;
                $exam->total_exam_points = $exam->total_exam_points + $point;
                $subject_array['exam_grade_point'] = number_format($point, 2, '.', '');
            }
            $exam->subject_result[] = $subject_array;
        }
        $exam->percentage = ($exam->total_get_marks * 100) / $exam->total_max_marks;
        print_r($exam);
    } else {
        $exam->is_consolidate = 1;
        $exam_connected_exam = ($exam_result->exam_result['exam_result']['exam_result_' . $exam_result->exam_group_class_batch_exam_id]);

        if (!empty($exam_connected_exam)) {
            foreach ($exam_connected_exam as $exam_result_key => $exam_result_value) {

                $subject_array = array();
                if ($exam_result_value->attendence != "present") {
                    $exam->exam_result_status = "fail";
                } elseif ($exam_result_value->get_marks < $exam_result_value->min_marks) {

                    $exam->exam_result_status = "fail";
                }
                $exam->total_max_marks = $exam->total_max_marks + $exam_result_value->max_marks;
                $exam->total_get_marks = $exam->total_get_marks + $exam_result_value->get_marks;
                
                $percentage = ($exam_result_value->get_marks * 100) / $exam_result_value->max_marks;
                $subject_array['name'] = $exam_result_value->name;
                $subject_array['code'] = $exam_result_value->code;
                $subject_array['exam_group_class_batch_exams_id'] = $exam_result_value->exam_group_class_batch_exams_id;
                $subject_array['room_no'] = $exam_result_value->room_no;
                $subject_array['max_marks'] = $exam_result_value->max_marks;
                $subject_array['min_marks'] = $exam_result_value->min_marks;
                $subject_array['subject_id'] = $exam_result_value->subject_id;
                $subject_array['attendence'] = $exam_result_value->attendence;
                $subject_array['get_marks'] = $exam_result_value->get_marks;
                $subject_array['class_batch_subject_id'] = $exam_result_value->class_batch_subject_id;
                $subject_array['exam_group_exam_results_id'] = $exam_result_value->exam_group_exam_results_id;
                $subject_array['note'] = $exam_result_value->note;
                $subject_array['duration'] = $exam_result_value->duration;
                $subject_array['credit_hours'] = $exam_result_value->credit_hours;
                $subject_array['exam_grade'] = findExamGrade($exam_grade, $exam_result->exam_type, $percentage);

                if ($exam_result->exam_type == "gpa") {

                    $point = findGradePoints($exam_grade, $exam_result->exam_type, $percentage);
                    $exam->exam_quality_points = $exam->exam_quality_points + ($exam_result_value->credit_hours * $point);
                    $exam->exam_credit_hour = $exam->exam_credit_hour + $exam_result_value->credit_hours;
                    $exam->total_exam_points = $exam->total_exam_points + $point;
                    $subject_array['exam_grade_point'] = number_format($point, 2, '.', '');
                }
                $exam->subject_result[] = $subject_array;
            }
            $exam->percentage = ($exam->total_get_marks * 100) / $exam->total_max_marks;
        }
        $consolidate_result = new stdClass;
        $consolidate_get_total = 0;
        $consolidate_total_points = 0;
        $consolidate_max_total = 0;
        $consolidate_subjects_total = 0;
        $consolidate_result->exam_array = array();
        $consolidate_result->consolidate_result = array();
        $consolidate_result_status = "pass";
        if (!empty($exam_result->exam_result['exams'])) {
            $consolidate_exam_result = "pass";
            foreach ($exam_result->exam_result['exams'] as $each_exam_key => $each_exam_value) {
                if ($exam_result->exam_type != "gpa") {
                    $consolidate_each = getCalculatedExam($exam_result->exam_result['exam_result'], $each_exam_value->id);

                    if ($consolidate_each->exam_status == "fail") {
                        $consolidate_result_status = "fail";
                    }

                    $consolidate_get_percentage_mark = getConsolidateRatio($exam_result->exam_result['exam_connection_list'], $each_exam_value->id, $consolidate_each->get_marks);

                    $each_exam_value->percentage = $consolidate_get_percentage_mark;
                    $consolidate_get_total = $consolidate_get_total + ($consolidate_get_percentage_mark);
                    $consolidate_max_total = $consolidate_max_total + ($consolidate_each->max_marks);
                }

                if ($exam_result->exam_type == "gpa") {
                    $consolidate_each = getCalculatedExamGradePoints($exam_result->exam_result['exam_result'], $each_exam_value->id, $exam_grade, $exam_result->exam_type);

                    $each_exam_value->total_points = $consolidate_each->total_points;
                    $each_exam_value->total_exams = $consolidate_each->total_exams;

                    $consolidate_exam_result = ($consolidate_each->total_points / $consolidate_each->total_exams);
                    $consolidate_get_percentage_mark = getConsolidateRatio($exam_result->exam_result['exam_connection_list'], $each_exam_value->id, $consolidate_exam_result);
                    $each_exam_value->percentage = $consolidate_get_percentage_mark;
                    $consolidate_get_total = $consolidate_get_total + ($consolidate_get_percentage_mark);

                    $consolidate_subjects_total = $consolidate_subjects_total + $consolidate_each->total_exams;

                    $each_exam_value->exam_result = number_format($consolidate_exam_result, 2, '.', '');
                }

                $consolidate_result->exam_array[] = $each_exam_value;
            }
            $consolidate_result->consolidate_result['marks_obtain'] = $consolidate_get_total;
            $consolidate_result->consolidate_result['marks_total'] = $consolidate_max_total;
            if ($exam_result->exam_type != "gpa") {

                $consolidate_percentage_grade = ($consolidate_get_total * 100) / $consolidate_max_total;
                $consolidate_result->consolidate_result['result'] = $consolidate_get_total . "/" . $consolidate_max_total;
                $consolidate_result->consolidate_result['grade'] = findExamGrade($exam_grade, $exam_result->exam_type, $consolidate_percentage_grade);
                $consolidate_result->consolidate_result['result_status'] = $consolidate_result_status;
            } elseif ($exam_result->exam_type == "gpa") {
                $consolidate_percentage_grade = ($consolidate_get_total * 100) / $consolidate_subjects_total;

                $consolidate_result->consolidate_result['result'] = $consolidate_get_total . "/" . $consolidate_subjects_total;

                $consolidate_result->consolidate_result['grade'] = findExamGrade($exam_grade, $exam_result->exam_type, $consolidate_percentage_grade);
            }

            $consolidate_exam_result_percentage = $consolidate_percentage_grade;
        }
        $exam->consolidated_exam_result = $consolidate_result;
        print_r($exam);
    }
}

function findExamGrade($exam_grade, $exam_type, $percentage) {

    foreach ($exam_grade as $exam_grade_key => $exam_grade_value) {
        if ($exam_grade_value['exam_key'] == $exam_type) {

            if (!empty($exam_grade_value['exam_grade_values'])) {
                foreach ($exam_grade_value['exam_grade_values'] as $grade_key => $grade_value) {
                    if ($grade_value->mark_from >= $percentage && $grade_value->mark_upto <= $percentage) {
                        return $grade_value->name;
                    }
                }
            }
        }
    }
    return "-";
}

function getCalculatedExamGradePoints($array, $exam_id, $exam_grade, $exam_type) {
    $object = new stdClass();
    $return_total_points = 0;
    $return_total_exams = 0;
    if (!empty($array)) {
        
        if (!empty($array['exam_result_' . $exam_id])) {
            foreach ($array['exam_result_' . $exam_id] as $exam_key => $exam_value) {
                $return_total_exams++;
                $percentage_grade = ($exam_value->get_marks * 100) / $exam_value->max_marks;
                $point = findGradePoints($exam_grade, $exam_type, $percentage_grade);
                $return_total_points = $return_total_points + $point;
            }
        }
    }
    $object->total_points = $return_total_points;
    $object->total_exams = $return_total_exams;
    return $object;
}

function findGradePoints($exam_grade, $exam_type, $percentage) {
    foreach ($exam_grade as $exam_grade_key => $exam_grade_value) {
        if ($exam_grade_value['exam_key'] == $exam_type) {

            if (!empty($exam_grade_value['exam_grade_values'])) {
                foreach ($exam_grade_value['exam_grade_values'] as $grade_key => $grade_value) {
                    if ($grade_value->mark_from >= $percentage && $grade_value->mark_upto <= $percentage) {
                        return $grade_value->point;
                    }
                }
            }
        }
    }
    return 0;
}

function getCalculatedExam($array, $exam_id) {

    $object = new stdClass();
    $return_max_marks = 0;
    $return_get_marks = 0;
    $return_credit_hours = 0;
    $return_exam_status = false;
    if (!empty($array)) {
        $return_exam_status = 'pass';
        if (!empty($array['exam_result_' . $exam_id])) {
            foreach ($array['exam_result_' . $exam_id] as $exam_key => $exam_value) {
                if ($exam_value->get_marks < $exam_value->min_marks || $exam_value->attendence != "present") {
                    $return_exam_status = "fail";
                }
                $return_max_marks = $return_max_marks + ($exam_value->max_marks);
                $return_get_marks = $return_get_marks + ($exam_value->get_marks);
                $return_credit_hours = $return_credit_hours + ($exam_value->credit_hours);
            }
        }
    }
    $object->credit_hours = $return_credit_hours;
    $object->get_marks = $return_get_marks;
    $object->max_marks = $return_max_marks;
    $object->exam_status = $return_exam_status;
    return $object;
}

function getConsolidateRatio($exam_connection_list, $examid, $get_marks) {
    if (!empty($exam_connection_list)) {
        foreach ($exam_connection_list as $exam_connection_key => $exam_connection_value) {
            if ($exam_connection_value->exam_group_class_batch_exams_id == $examid) {
                return ($get_marks * $exam_connection_value->exam_weightage) / 100;
            }
        }
    }
    return 0;
}
