<?php 

$student_allover_exam_rank=[];
$subject_term_rank = [];
foreach ($result as $student_key => $student_value) {
    
    $grand_total_term_percentage = 0;  

    foreach ($subject_array as $subject_array_key => $subject_array_value) {
       $subject_grand_total=0;
$subject_total_exam_percentage=0;
      
foreach ($exam_term_exam_assessment as $assess_key => $assess_value) {
           $subject_grand_total=0;
$subject_total_exam_percentage=0;

            foreach ($assess_value['exams'] as $exam_key => $exam_value) {
$exam_subject_total=0;
    $exam_subject_maximum_total=0;
                foreach ($exam_value['exam_assessments'] as $exam_assement_key => $exam_assement_value) {

                    $subject_marks_array = getSubjectData_Temp($student_value['terms'], $assess_value['cbse_term_id'], $exam_key, $subject_array_key, $exam_assement_value['cbse_exam_assessment_type_id']);
                  $subject_marks_array= getSubjectData_Temp($student_value['terms'],$assess_value['cbse_term_id'],$exam_key,$subject_array_key,$exam_assement_value['cbse_exam_assessment_type_id']);

if(!$subject_marks_array['marks'] <= 0 ||  $subject_marks_array['marks'] == "N/A"){

$exam_subject_total+=($subject_marks_array['marks'] == "N/A") ? 0 : $subject_marks_array['marks'] ;
$exam_subject_maximum_total+=$subject_marks_array['maximum_marks'];

}else{

$exam_subject_total+=0;
$exam_subject_maximum_total+=0;

}
                }
      $subject_percentage=getPercent($exam_subject_maximum_total,$exam_subject_total);
      $subject_total_exam_percentage+=($subject_percentage*($exam_value['exam_weightage']/100));
      $grand_total_term_percentage+=($subject_percentage*($exam_value['exam_weightage']/100));

            }        

        }

//===============
        if (!array_key_exists($subject_array_key, $subject_term_rank)) {
            $subject_term_rank[$subject_array_key] = [];
        }

        $subject_term_rank[$subject_array_key][] = [
            'student_session_id' => $student_value['student_session_id'],
            'rank_percentage'    => $subject_total_exam_percentage,
            'rank'=>0

        ];

//==============

    }

   $overall_percentage = getPercent((count($subject_array) * 100), $grand_total_term_percentage);
  
    $student_allover_exam_rank[$student_value['student_session_id']]=[
      'student_session_id'=>$student_value['student_session_id'],
      'firstname'=>$student_value['firstname'],
      'rank_percentage'=>$overall_percentage,
      'rank'=>0,
  ];

}

//-=====================start term calculation Rank=============

$rank_overall_term_percentage_keys = array_column($student_allover_exam_rank, 'rank_percentage');
$rank_overall_term_student_name_keys = array_column($student_allover_exam_rank, 'firstname');
 array_multisort($rank_overall_term_percentage_keys, SORT_DESC, $rank_overall_term_student_name_keys , SORT_ASC,$student_allover_exam_rank);

$term_rank_allover_list=unique_array($student_allover_exam_rank, "rank_percentage");

foreach ($student_allover_exam_rank as $term_rank_key => $term_rank_value) { 
   $student_allover_exam_rank[$term_rank_key]['rank']=array_search($term_rank_value['rank_percentage'],$term_rank_allover_list);
}


//-=====================end term calculation Rank=============

//-=====================start subject term calculation Rank=============

foreach ($subject_term_rank as $subject_term_key => $subject_term_value) {


$rank_overall_subject = array_column($subject_term_rank[$subject_term_key], 'rank_percentage');

 array_multisort($rank_overall_subject, SORT_DESC,$subject_term_rank[$subject_term_key]);

$subject_rank_allover_list=unique_array($subject_term_rank[$subject_term_key], "rank_percentage");

foreach ($subject_term_rank[$subject_term_key] as $subject_rank_key => $subject_rank_value) {

    $subject_term_rank[$subject_term_key][$subject_rank_key]['rank']=array_search($subject_rank_value['rank_percentage'],$subject_rank_allover_list);
}

}

 ?>

<?php 

$count_result=count($result);
$student_increment=0;

foreach ($result as $student_key => $student_value) {
    $student_increment++;
  $grand_total_marks=0;
  $grand_total_term_percentage=0;
  $grand_total_gain_marks=0;
  $exam_weight_array=[];
  $total_total_working_day=0;
  $total_present_day=0;

  ?>

<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">  
 
</head>
<body>
<div style="width: 100%; margin: 0 auto;">
  <?php
          
if($template['header_image'] != ""){
  ?>
   
          <img width= "100%" max-width= "100%" src="<?php echo base_url("/uploads/cbseexam/template/header_image/". $template['header_image']) ?>" />
             
  <?php
}
           ?>
  <table cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td valign="top">
        <table cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td valign="top" style="padding-bottom: 0px; padding-top: 5px; width: 100%; font-weight: bold; text-align: center; font-size:20px;">
             <?php echo $this->lang->line('report_card'); ?>
            </td>
          </tr>    
          <tr>
            <td valign="top" style="padding-bottom: 20px; padding-top: 2px; width: 100%;font-weight: bold; text-align: center; font-size:15px;">
             <?php echo $this->lang->line('academic_session'); ?> : <?php echo $current_setting['session'];?>
            </td>
          </tr>

        </table>
      </td>
    </tr> 
    <tr>
      <td valign="top">
        <table cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td valign="top" width="80%">
              <table cellpadding="0" cellspacing="0" width="100%">
             <tr>
                             
                  <?php 

                  if($template['is_admission_no']){
                    ?>

                  <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('admission_no'); ?>.</td>
                  <td valign="top">: <?php echo $student_value['admission_no']; ?></td>
                          <?php
                  }
                  ?>
                  
               <?php 

                  if($template['is_roll_no']){
                    ?>
                  <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('roll_no'); ?></td>
                  <td valign="top">: <?php echo $student_value['roll_no']; ?></td>
                    <?php
                  }
                  ?>

                </tr>
                <tr>
                  <?php 

                  if($template['is_name']){
                    ?>
                  <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('students_name'); ?></td>
                  <td valign="top">: <?php echo   $this->customlib->getFullName($student_value['firstname'], $student_value['middlename'], $student_value['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?></td>

                    <?php

}
 ?>
   <?php 

                  if($template['is_dob']){
                    ?>
                  <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('date_of_birth'); ?></td>
                  <td valign="top">: <?php echo $this->customlib->dateformat($student_value['dob']); ?></td>

                          <?php
                  }
                  ?>            
                </tr>
                <tr>
                    <?php 

                  if($template['is_father_name']){
                    ?>
                  <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('fathers_name'); ?></td>
                  <td valign="top">:  <?php echo $student_value['father_name']; ?> </td>

                          <?php
                  }
                  ?>
                                      <?php 

                  if($template['is_mother_name']){
                    ?>
    <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('mothers_name'); ?></td>
                  <td valign="top">: <?php echo $student_value['mother_name']; ?></td>
                          <?php
                  }
                  ?>                  

                </tr>
                <tr>
                  
                    <?php 

                  if($template['is_class'] && $template['is_section']){
                    ?>
 <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('class_section'); ?></td>
                  <td valign="top">: <?php echo $student_value['class']." (".$student_value['section'].")"; ?></td>
                          <?php
                  }else if($template['is_class']){
                    ?>
 <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('class_section'); ?></td>
                  <td valign="top">: <?php echo $student_value['class']; ?></td>
                          <?php
                  }
                  else if($template['is_section']){
                    ?>
 <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('class_section'); ?></td>
                  <td valign="top">: <?php echo $student_value['section']; ?></td>
                          <?php
                  }
                  ?>             
                  <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('school_name'); ?></td>
                  <td valign="top">: <?php echo $template['school_name']?></td>
                </tr>
                <tr><td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('exam_center'); ?></td>
                  <td valign="top">:  <?php echo $template['exam_center']?></td>
                  
                  <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('result_declaration_date'); ?></td>
                  <td valign="top">: <?php echo $this->customlib->dateformat(date('Y-m-d')); ?> </td>                 

                </tr>
              </table>
            </td>
            <?php 

                  if($template['is_photo']){
                    ?>
    
         <td valign="top" align="right" width="20%">
             <?php
if (!empty($student_value["student_image"])) {
                    $student_image=base_url() . $student_value["student_image"];
                } else {

                    if ($student_value['gender'] == 'Female') {
                        $student_image=base_url() . "uploads/student_images/default_female.jpg";
                    } elseif ($student_value['gender'] == 'Male') {
                        $student_image=base_url() . "uploads/student_images/default_male.jpg";
                    }

                }
                ?>
              <img src="<?php echo $student_image; ?>" width="85" height="100" style="border:1px solid #000">
            </td>

                          <?php
                  }
                  ?> 
          </tr>
        </table>
      </td>
    </tr>
    <?php 

     ?>
    <tr><td valign="top" style="height:10px"></td></tr>
    <tr>
      <td valign="top">
 <table cellpadding="0" cellspacing="0" width="100%" class="denifittable">
          <thead>       
            <tr>
              <td valign="middle" class="text-center"><?php echo $this->lang->line('scholastic_areas'); ?></td>

<?php 
foreach ($exam_term_exam_assessment as $assess_key => $assess_value) {
 $term_colspan=count($assess_value['term_total_assessments']);
  ?>
 <td valign="middle" class="text-center" colspan="<?php echo $term_colspan+3; ?>"><?php echo $assess_value['cbse_term_name']." (".$assess_value['cbse_term_code'].")"; ?> 
 </td> 

  <?php

}
 ?>        
          
          </tr>
 <tr>
            <td rowspan="2" valign="middle" class="text-center"><?php echo $this->lang->line('subject'); ?></td>           

<?php 
foreach ($exam_term_exam_assessment as $assess_key => $assess_value) {

  foreach ($assess_value['exams'] as $exam_key => $exam_value) {

  $exam_weight_array[]=$exam_value['exam_name']." (".$exam_value['exam_weightage'].")";

            if(array_key_exists($exam_value['cbse_exam_id'], $student_value['terms'][$assess_value['cbse_term_id']]['exams'])){
  $total_present_day+=($student_value['terms'][$assess_value['cbse_term_id']]['exams'][$exam_value['cbse_exam_id']]['total_present_days']);
  $total_total_working_day+= ($student_value['terms'][$assess_value['cbse_term_id']]['exams'][$exam_value['cbse_exam_id']]['total_working_days']);
              
            }
 
    ?>
<td colspan="<?php echo count($exam_value['exam_assessments']);?>" valign="top" class="text-center"><?php echo $exam_value['exam_name'];?></td>
    <?php
  }
?>

<td colspan="3" valign="middle" class="text-center"> 
    <?php 
               //term merge array              
echo implode(" + ", $exam_weight_array);
               ?></td>

<?php

}
 ?>
          </tr>
          <tr>
         <?php 
foreach ($exam_term_exam_assessment as $assess_key => $assess_value) {

  foreach ($assess_value['exams'] as $exam_key => $exam_value) {

    foreach ($exam_value['exam_assessments'] as $exam_assement_key => $exam_assement_value) {
      ?> <td valign="top" class="text-center"><?php 
         echo $exam_assement_value['name']." (".$exam_assement_value['code'].")"; 
         echo "<br/>";
    
         echo $exam_assement_value['maximum_marks'];

    ?>    

    </td>
    <?php    }
  
  }
}
 ?>
 <td valign="middle" class="text-center"><?php echo $this->lang->line('grand_total'); ?> <br/> <?php echo $this->lang->line('out_of'); ?> (100)</td>
 <td valign="middle" class="text-center"><?php echo $this->lang->line('total'); ?>  </td>
 <td valign="middle" class="text-center"><?php echo $this->lang->line('rank'); ?></td>          
          
          </tr>           
             <?php 

foreach ($subject_array as $subject_array_key => $subject_array_value) {
$subject_grand_total=0;
$subject_total_exam_percentage=0;
?>
<tr>
  
<td valign="top"><?php echo $subject_array_value;?></td>
     <?php 
foreach ($exam_term_exam_assessment as $assess_key => $assess_value) {   

  foreach ($assess_value['exams'] as $exam_key => $exam_value) {
    $exam_subject_total=0;
    $exam_subject_maximum_total=0;

    foreach ($exam_value['exam_assessments'] as $exam_assement_key => $exam_assement_value) {
      ?> 
      <td valign="top" class="text-center"><?php 

$subject_marks_array= getSubjectData_Temp($student_value['terms'],$assess_value['cbse_term_id'],$exam_key,$subject_array_key,$exam_assement_value['cbse_exam_assessment_type_id']);
if(!$subject_marks_array['marks'] <= 0 ||  $subject_marks_array['marks'] == "N/A"){
echo ($subject_marks_array['is_absent']) ? $this->lang->line('abs') :$subject_marks_array['marks'];
$exam_subject_total+=($subject_marks_array['marks'] == "N/A") ? 0 : $subject_marks_array['marks'] ;
$exam_subject_maximum_total+=$subject_marks_array['maximum_marks'];

}else{
  echo "-";
$exam_subject_total+=0;
$exam_subject_maximum_total+=0;

}
    ?>  

    </td>
    <?php   
     }

      $subject_percentage=getPercent($exam_subject_maximum_total,$exam_subject_total);
      $subject_total_exam_percentage+=($subject_percentage*($exam_value['exam_weightage']/100));
      $grand_total_term_percentage+=($subject_percentage*($exam_value['exam_weightage']/100));      
  
  }
 ?> 
<td valign="top" class="text-center"><?php echo two_digit_float($subject_total_exam_percentage); ?></td>
<td valign="top" class="text-center">
    <?php 
     echo getGrade($exam_grades,$subject_total_exam_percentage);
     ?>
  </td>
    <?php 
}
 ?>
    <td valign="top" class="text-center">
    <?php echo searchSubjectRank($student_value['subject_rank'],$subject_array_key); ?>
    </td>     
</tr>
<?php
}
             ?>

</thead>
</table>
      </td>
    </tr>
         <tr><td valign="top" style="height:0px"></td></tr>
    <tr>
      <td>
      <table  cellpadding="0" cellspacing="0" width="100%" class="denifittable">
        <tbody>
          <tr>
            <?php            

$overall_percentage=getPercent((count($subject_array)*100),$grand_total_term_percentage);
             ?>
            <td><?php echo $this->lang->line('overall_marks'); ?> : <?php echo two_digit_float($grand_total_term_percentage, 2)."/".count($subject_array)*100; ?></td>
            <td><?php echo $this->lang->line('percentage'); ?> : <?php echo two_digit_float($overall_percentage, 2); ?></td>
            <td><?php echo $this->lang->line('grade'); ?> : <?php echo getGrade($exam_grades,$overall_percentage); ?></td>
              <td><?php echo $this->lang->line('rank'); ?> : <?php echo $student_value['rank']; ?></td>
            
          </tr>
        </tbody>
      </table>
    </td>
    </tr>
    
        <tr><td style="height:20px"></td></tr>
    
        <?php 
if(!empty($list_observation))
{
?>
<tr>
      <td valign="top">
        <?php 

$i = 1;
$td = 1;
?>
<table cellpadding="0" cellspacing="0" width="100%" >

<?php

   foreach ($list_observation as $obs_key => $obs_value) {

    if($i == 1)
        echo "<tr>";

?>

    <td valign="top" <?php echo ($td == 2) ? "align='right'" :"align='left'" ?>>
     <table cellpadding="0" cellspacing="0" width="100%" class="denifittable-small">
      <tr>
        <td valign="top" colspan="<?php echo count($obs_value['cbse_terms'])+1; ?>"><?php echo $obs_value['cbse_observation_name']; ?></td>
      </tr>
      <tr>
          <td valign="top" width="50%"><?php echo $this->lang->line('activity'); ?></td>                      
                                <?php 
foreach ($obs_value['cbse_terms'] as $obse_term_key => $obse_term_value) {
  ?> 
   <td valign="top"><?php echo $obse_term_value['term_name']; ?></td>
  <?php
}
               ?>
                      </tr>
                    <?php 
foreach ($obs_value['cbse_observation_parameters'] as $cbse_obs_parma_key => $cbse_obs_parma_value) {  
?>
<tr>
  <td><?php echo $cbse_obs_parma_value->name; ?></td>
                                <?php 
foreach ($obs_value['cbse_terms'] as $obse_term_key => $obse_term_value) {
  ?>

     <td>
<?php 
     $parameter_array=getStudentObservation($student_observations,$student_value['student_session_id'],$obse_term_value['cbse_term_id'],$cbse_obs_parma_value->cbse_observation_parameter_id,$obs_value['cbse_exam_observation_id']);
     if(!empty($parameter_array)){ 

   $parameter_percentage= getPercent($parameter_array['maximum_marks'],$parameter_array['obtain_marks']);
    echo getGrade($exam_grades,$parameter_percentage); 

}else{
  echo "-";
}

 ?>
  </td>
  <?php
}
               ?>
</tr>
<?php
}
                     ?>
      </table>
    </td>

<?php
if($td == 2) {
$td=0;

}
$td++;

    if($i == 2) {
            echo "</tr>";
            $i=0;
        }

    $i++;
}


         ?>
       </table>    
      </td>
    </tr>
<?php
}
 ?>
 
     <tr><td valign="top" style="height:20px"></td></tr>
     <tr>
      <td>
      <table  cellpadding="0" cellspacing="0" width="100%" class="denifittable" style="padding-bottom: 10px;">
<tbody>
<tr>
    <td valign="middle" class="text-center" rowspan="2"><b><?php echo $this->lang->line('attendance_overall'); ?></b></td>
    <td valign="middle" class="text-center"><b><?php echo $this->lang->line('total_working_days'); ?></b></td>
    <td valign="middle" class="text-center"><b><?php echo $this->lang->line('days_present'); ?></b></td>
    <td valign="middle" class="text-center"><b><?php echo $this->lang->line('attendance_percentage'); ?></b></td>
  </tr>
  <tr>
    
    <td valign="middle" class="text-center"><?php echo $total_total_working_day; ?></td>
    <td valign="middle" class="text-center"><?php echo $total_present_day; ?></td>
    <td valign="middle" class="text-center"><?php echo getPercent($total_total_working_day,$total_present_day);?></td>
  </tr>
</tbody>
</table>
    </td>
    </tr>    

 <tr><td valign="top" style="height:15px"></td></tr>
    <tr>
      <td style="padding-bottom: 6px;display: block;">

<b><?php echo $this->lang->line('class_teacher_remark'); ?> :</b> <?php echo $student_value['remark']; ?>
    </td>
    </tr> 
    <tr><td valign="top" style="height:20px"></td></tr>
     <tr>
      <td valign="top" width="100%" align="center">
        <table cellpadding="0" cellspacing="0" width="100%" style="border-bottom:1px solid #999; margin-bottom:10px;">
          <tr>
            <td valign="top" width="32%" class="signature">
              <img src="<?php echo base_url('uploads/cbseexam/template/left_sign/'.$template['left_sign']) ?>" width="100" height="50" style="padding-bottom: 5px;">
              <p class="fw-bold"><?php echo $this->lang->line('signature_of_class_teacher'); ?></p>
            </td>
             <td valign="top" width="32%" class="signature text-center">
              <img src="<?php echo base_url('uploads/cbseexam/template/middle_sign/'.$template['middle_sign']) ?>" width="100" height="50" style="padding-bottom: 5px;">
              <p class="fw-bold"><?php echo $this->lang->line('signature_of_principal'); ?></p>
            </td>
            <td valign="top" width="32%" class="signature text-center">
              <img src="<?php echo base_url('uploads/cbseexam/template/right_sign/'.$template['right_sign']) ?>" width="100" height="50" style="padding-bottom: 5px;">
              <p class="fw-bold"><?php echo $this->lang->line('signature_of_principal'); ?></p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
            <td valign="top" style="padding-bottom: 5px; padding-top: 5px; width: 100%;font-weight: bold; text-align: center; font-size:15px;">
              <?php echo $this->lang->line('instruction'); ?>
            
            </td>
      </tr>
    <tr><td valign="top" style="height:20px"></td></tr>
    <tr>
  <?php 

$total_colspans=4;

foreach ($student_value['terms'] as $student_term_key => $student_term_value) {

  $term_colspan=count($exam_term_exam_assessment[$student_term_value['cbse_term_id']]['term_total_assessments']);

  $total_colspans+=($term_colspan+2);

}
               ?>
        <td valign="top" class="text-left" colspan="<?php echo $total_colspans; ?>">   
           <?php echo $this->lang->line('grading_scale'); ?> : <?php 

            echo implode(', ', array_map(
            function($k)  {
                return $k->name." (".$k->maximum_percentage . "% - " . $k->minimum_percentage."%)";
            },
            ($exam_grades)
            
            )
        );
            ?>
             </td>          
          </tr> 
    <!-- //====================== -->

 <!-- ///////////////// -->
  
    <tr>
     <td valign="top" style="margin-bottom:5px; padding-top: 10px; line-height: normal;">
           <?php echo $template['content_footer']; ?>
      </td>
    </tr>
  </table>
</div>
</body>
</html>

  <?php
  if($student_increment < $count_result){
  echo "<div style='page-break-after:always'></div>";
}
}
 ?>
 <?php 

function getSubjectData_Temp($term_array,$find_term,$find_exam,$find_subject_id,$find_cbse_exam_assessment_type_id){
  $return_array=[
                    'maximum_marks'=>"",                   
                    'marks'=>"",
                    'note'=>"",
                    'is_absent'=>"",
                    ];

if(!empty($term_array)){

if (array_key_exists($find_term, $term_array)){

if (array_key_exists($find_exam, $term_array[$find_term]['exams'])){

 if (array_key_exists($find_subject_id, $term_array[$find_term]['exams'][$find_exam]['subjects'])){
     $result_array=($term_array[$find_term]['exams'][$find_exam]['subjects'][$find_subject_id]['exam_assessments'][$find_cbse_exam_assessment_type_id]);

     $return_array=[
                    'maximum_marks'=>$result_array['maximum_marks'],              
                    'marks'=>is_null($result_array['marks']) ? "N/A" : $result_array['marks'],
                    'note'=>$result_array['note'],
                    'is_absent'=>$result_array['is_absent'],
                    ];
 }
  }
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
}elseif($grade_value->maximum_percentage <= $Percentage && $grade_value->minimum_percentage >= $Percentage){

         return $grade_value->name;
         break;
      }
    }
  }
return "-";

 }

 function getStudentObservation($student_observations,$student_session_id,$cbse_term_id,$parameter_id,$observation_id){
 
   if(!empty($student_observations)){
    if(array_key_exists($student_session_id, $student_observations)){
      if (array_key_exists($cbse_term_id, $student_observations[$student_session_id]['terms'])) {  
        if (array_key_exists( $observation_id, $student_observations[$student_session_id]['terms'][$cbse_term_id]['observations'])) {
          if (array_key_exists( $parameter_id, $student_observations[$student_session_id]['terms'][$cbse_term_id]['observations'][$observation_id])) {

           return $student_observations[$student_session_id]['terms'][$cbse_term_id]['observations'][$observation_id][$parameter_id];

          }
        }
      }
    }
  }
return [];

 }

function searchSubjectRank( $array,$subject_id) {

  foreach ($array as $k => $val) {
 
      if ($k== $subject_id) {
          return $val;
      }
  }
  return null;
}

  ?>