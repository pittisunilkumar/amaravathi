<?php

defined('BASEPATH') or exit('No direct script access allowed');


function isJSON($string)
{
    return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}

function convertBaseAmountCurrencyFormat($amount)
{
    $CI              = &get_instance();
    $currency_price  = $CI->customlib->getSchoolCurrencyPrice();
    $amount=($amount*$currency_price);
    return two_digit_float($amount);
}
function two_digit_float($number)
{
    
   if($number != ""){
    $number = number_format($number, 2, ".", "");
   }
    return $number;
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
    $return_quality_points = 0;
    $return_total_points = 0;
    $return_total_exams = 0;
    $return_total_credit_hours = 0;
    if (!empty($array)) {
         
        if (!empty($array['exam_result_' . $exam_id])) {
            foreach ($array['exam_result_' . $exam_id] as $exam_key => $exam_value) {
                $return_total_exams++;
              
                $return_total_credit_hours+=$exam_value->credit_hours;

                $percentage_grade = ($exam_value->get_marks * 100) / $exam_value->max_marks;
                 $point = findGradePoints($exam_grade, $exam_type, $percentage_grade);

                $return_quality_points+=($point*$exam_value->credit_hours);
                $return_total_points = $return_total_points + $point;
               
            }
        }
    }
    $object->total_points = $return_total_points;
    $object->total_exams = $return_total_exams;
    $object->return_quality_points = $return_quality_points;
    $object->return_total_credit_hours = $return_total_credit_hours;
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

function getConsolidateRatio($exam_connection_list, $examid, $get_marks,$max_marks) {
    if (!empty($exam_connection_list)) {
        foreach ($exam_connection_list as $exam_connection_key => $exam_connection_value) {
            if ($exam_connection_value->exam_group_class_batch_exams_id == $examid) {
                       $percentage=($get_marks*100)/$max_marks;
                     return ['marks_weight'=>(($get_marks * $exam_connection_value->exam_weightage) / 100),
                        'percentage_weight'=>(($percentage * $exam_connection_value->exam_weightage) / 100),
                        'exam_weightage'=>$exam_connection_value->exam_weightage
];

                // return ($get_marks * $exam_connection_value->exam_weightage) / 100;
            }
        }
    }
    return 0;
}

function checkDuplicateTeacher($array, $staff_id) {
    if (!empty($array)) {
        return array_count_values(array_column($array, 'staff_id'))[$staff_id];
    }
}

function getExamDivision($percentage) {

    $division = "";
    if ($percentage >= 60) {
        $division = "first";
    } elseif ($percentage >= 50 && $percentage < 60) {
        $division = "second";
    } elseif ($percentage >= 0 && $percentage < 50) {

        $division = "third";
    } else {
        
    }
    return $division;
}

function check_student_field_status($sch_setting_detail, $field_value) {

    if (array_key_exists($field_value->name, $sch_setting_detail) && $sch_setting_detail->{$field_value->name} && $field_value->status) {
        return 1;
    } elseif ($field_value->status) {
        return 1;
    } else {

        return 0;
    }
}
function getSecondsFromHMS($time) {
    $timeArr = array_reverse(explode(":", $time));    
    $seconds = 0;
    foreach ($timeArr as $key => $value) {
        if ($key > 2)
            break;
        $seconds += pow(60, $key) * $value;
    }
    return $seconds;
}

function getHMSFromSeconds($seconds) {
  $t = round($seconds);
  return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
}

    function getFullName($firstname, $middlename, $lastname, $is_middlename,$is_lastname)
    {
        $name="";
        if ($is_middlename) {
            $name= ($middlename == "") ? $firstname : $firstname . " " . $middlename;
        } else {
            $name= $firstname;
        }

       if ($is_lastname) {
            $name= ($lastname == "") ? $name : $name . " " . $lastname;
        } 

        return $name;
    }
    
    function amountFormat($amount)
{ 
    $CI              = &get_instance();
    $currency_format = $CI->customlib->getCurrencyFormat();
    $currency_symbol = $CI->customlib->getCurrencySymbol();
    $currency_price = $CI->customlib->getSchoolCurrencyPrice();
    $amount=($amount*$currency_price);

 
    if ($currency_format == "#,###.##") {
        $return_amt = number_format($amount, 2, '.', ',');
    } elseif ($currency_format == "#.###,##") {
        $return_amt = number_format($amount, 2, ',', '.');
    } elseif ($currency_format == "# ###.##") {
        $return_amt = number_format($amount, 2, '.', ' ');
    } elseif ($currency_format == "#.###.##") {
        $return_amt = number_format($amount, 2, '.', '.');
    } elseif ($currency_format == "#,###.###") {
        $return_amt = number_format($amount, 3, '.', ',');
    } elseif ($currency_format == "####.##") {
        $return_amt = number_format($amount, 2, '.', '');
    } elseif ($currency_format == "#,##,###.##") {
        $return_amt = indian_money_format($amount);
    }
    return $currency_symbol.$return_amt;
}

function indian_money_format($num)
{
    $explrestunits = "";
    $num           = preg_replace('/,+/', '', $num);
    $words         = explode(".", $num);
    $des           = "00";
    if (count($words) <= 2) {
        $num = $words[0];
        if (count($words) >= 2) {$des = $words[1];}
        if (strlen($des) < 2) {$des = "$des";} else { $des = substr($des, 0, 2);}
    }
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3, strlen($num));
        $restunits = substr($num, 0, strlen($num) - 3); // extracts the last three digits
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit   = str_split($restunits, 2);
        for ($i = 0; $i < sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if ($i == 0) {
                $explrestunits .= (int) $expunit[$i] . ","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i] . ",";
            }
        }
        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $num;
    }
    return "$thecash.$des"; // writes the final format where $currency is the currency symbol.

}

    ?>