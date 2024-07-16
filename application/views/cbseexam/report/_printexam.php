<?php 
    if(isset($subjects)){

if(!empty($subjects) && !empty($students)){

    //===============
// Rank

    $student_allover_rank=[];
$subject_rank = [];
foreach ($students as $student_key => $student_value) {
  $total_max_marks=0;
  $total_gain_marks=0;

  foreach ($student_value['subjects'] as $subject_key => $subject_value) {
            $subject_total=0;
            $subject_max_total=0;

                foreach ($subject_value['exam_assessments'] as $assessment_key => $assessment_value) {
                    $subject_total+=$assessment_value['marks'];
                    $subject_max_total+=$assessment_value['maximum_marks'];
                    $total_gain_marks+=$assessment_value['marks'];
                    $total_max_marks+=$assessment_value['maximum_marks'];
                }

                if (!array_key_exists($subject_key, $subject_rank)) {
                    $subject_rank[$subject_key] = [];
                }

                $subject_rank[$subject_key][] = [
                    'student_session_id' => $student_value['student_session_id'],
                    'rank_percentage'    => $subject_total,
                    'rank'=>0

                ];    
  
  }
  

 $exam_percentage=getPercent($total_max_marks,$total_gain_marks);

  $student_allover_rank[$student_value['student_session_id']]=[
      'student_session_id'=>$student_value['student_session_id'],
      'firstname'=>$student_value['firstname'],
      'rank_percentage'=>$exam_percentage,
      'rank'=>0,
  ];
 
}

//-=====================start term calculation Rank=============

$rank_overall_percentage_keys = array_column($student_allover_rank, 'rank_percentage');

 array_multisort($rank_overall_percentage_keys, SORT_DESC, $student_allover_rank);

$term_rank_allover_list=unique_array($student_allover_rank, "rank_percentage");

foreach ($student_allover_rank as $term_rank_key => $term_rank_value) {
 
   $student_allover_rank[$term_rank_key]['rank']=array_search($term_rank_value['rank_percentage'],$term_rank_allover_list);
}

//-=====================end term calculation Rank=============

    //===============
    ?>
       <div class="btn-group pb10" role="group" aria-label="First group"> 
          <button type="button" class="btn btn-default btn-xs" onclick="printDiv('div_print')"><i class="fa fa-print"></i></button>
          <button type="button" class="btn btn-default btn-xs" onclick="exportToExcel('div_print')"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
     </div>

    <div class="table-responsive" id="div_print">
    <h4 id="print_title"><?php echo $this->lang->line('template_wise_report'); ?></h4>
    <table class="table table-bordered table-b vertical-middle">
    <thead>
        <tr>
            <th rowspan="2" class="white-space-nowrap"><?php echo $this->lang->line('student'); ?></th>
            <th rowspan="2" class="white-space-nowrap"><?php echo $this->lang->line('admission_no'); ?></th>
            <th rowspan="2" class="white-space-nowrap"><?php echo $this->lang->line('class'); ?></th>
           
           <?php 
            foreach ($subjects as $subject_key => $subject_value) {
                       ?>
                    <th colspan="<?php echo count($exam_assessments);?>" class="text-center">
                        <?php echo $subject_value->subject_name."(".$subject_value->subject_code.")"; ?>    
                    </th>
                       <?php
            }
            ?>
                 <th rowspan="2" class="text-center"><?php echo $this->lang->line('total_marks'); ?></th>
                 <th rowspan="2" class="text-center"><?php echo $this->lang->line('percentage'); ?> (%)</th>
                 <th rowspan="2" class="text-center"><?php echo $this->lang->line('grade'); ?></th>
                 <th rowspan="2" class="text-center"><?php echo $this->lang->line('rank'); ?></th>
        </tr>
        <tr>
           
            <?php 
                foreach ($subjects as $subject_key => $subject_value) {
                 foreach ($exam_assessments as $exam_assessment_key => $exam_assessment_value) {
                    ?>
                <th class="text-center">
                    
                    <?php echo $exam_assessment_value->name." (".$exam_assessment_value->code.")"; ?>
                    <br/>
                    ( <?php echo $this->lang->line('max'); ?> - <?php echo $exam_assessment_value->maximum_marks; ?>)
                </th>
                    <?php
                 }
                }
                            ?>
        </tr>
    </thead>
    <tbody>
        <?php 
if(!empty($students)){
  
    foreach ($student_allover_rank as $student_rank_key => $student_rank_value) {
  
 $student_value =$students[$student_rank_value['student_session_id']];

        $total_marks=0;
        $total_max_marks=0;
        ?>
<tr>
    <td><?php echo $student_value['firstname']." ". $student_value['middlename']." ".$student_value['lastname']; ?></td>
    <td><?php echo $student_value['admission_no']; ?></td>
    <td><?php echo $student_value['class']." (".$student_value['section'].")"; ?></td>

          <?php 
foreach ($subjects as $subject_key => $subject_value) {
 foreach ($exam_assessments as $exam_assessment_key => $exam_assessment_value) {
    ?>
<td class="text-center">
    <?php 

        $assessment_array= findAssessmentValue($subject_value->subject_id,$exam_assessment_value->id,$student_value);
        echo ($assessment_array['is_absent']) ? $this->lang->line('abs') : $assessment_array['marks'];
        if($assessment_array['marks'] == "N/A"){
            $assessment_array['marks']=0;
        }
        $total_max_marks+=$assessment_array['maximum_marks'];
        $total_marks+=$assessment_array['marks'];
    ?>
</td>
    <?php
 }
}

$exam_percentage=getPercent($total_max_marks, $total_marks);
            ?>
                <td class="text-center"><?php echo $total_marks."/".$total_max_marks; ?></td>
                <td class="text-center"><?php echo $exam_percentage; ?></td>
                <td class="text-center"><?php echo getGrade($exam->grades,$exam_percentage); ?></td>
                <td class="text-center"><?php echo $student_value['rank']; ?></td>

</tr>
        <?php
    }
}
         ?>
    </tbody>
    
</table>
    </div>
    <?php
}else{
    ?>
<div class="alert alert-info">
    <?php echo $this->lang->line('no_record_found'); ?>
</div>
    <?php
}
}

?>
<?php 

function findAssessmentValue($find_subject_id,$find_cbse_exam_assessment_type_id,$student_value){

      $return_array=[
                    'maximum_marks'=>"",                   
                    'marks'=>"",
                    'note'=>"",
                    'is_absent'=>"",
                    ];

if (array_key_exists('subjects', $student_value)){

 if (array_key_exists($find_subject_id, $student_value['subjects'])){
     $result_array=($student_value['subjects'][$find_subject_id]['exam_assessments'][$find_cbse_exam_assessment_type_id]); 
       $return_array=[
                    'maximum_marks'=>$result_array['maximum_marks'],              
                    'marks'=>is_null($result_array['marks']) ? "N/A" : $result_array['marks'],
                    'note'=>$result_array['note'],
                    'is_absent'=>$result_array['is_absent'],
                    ];
 }
  }

return $return_array;
}

 function getGrade($grade_array,$Percentage){

    if(!empty($grade_array)){
    foreach ($grade_array as $grade_key => $grade_value) {

              if($grade_value->minimum_percentage <= $Percentage){
                 return $grade_value->name;
                 break;
    }elseif(($grade_value->minimum_percentage >= $Percentage && $grade_value->maximum_percentage <= $Percentage)){

                 return $grade_value->name;
                 break;
              }
     }
          }
return "-";

 }

// function unique_array($my_array, $key) { 
    // $result = array(); 
    // $i = 1; 
    // $key_array = array(); 
    
    // foreach($my_array as $val) { 
        // if (!in_array($val[$key], $key_array)) { 
            // $key_array[$i] = $val[$key]; 
            // $result[$i] = $val['rank_percentage']; 
        // $i++; 
        // } 
    // } 
    // return $result; 
// } 

 // function searcharray($value, $key, $array) {
   // foreach ($array as $k => $val) {
  
       // if ($val[$key] == $value) {
           // return $val['rank'];
       // }
   // }
   // return null;
// }

?>