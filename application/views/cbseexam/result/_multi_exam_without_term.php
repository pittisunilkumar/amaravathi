<?php $this->load->view('layout/cbseexam_css.php'); ?>
<?php 

$student_allover_exam_rank=[];
$subject_wise_rank = [];
foreach ($result as $student_key => $student_value) {
    $grand_total_term_percentage = 0;
    $grand_total_exam_weight_percentage=0;

foreach ($subject_array as $subject_array_key => $subject_array_value) {
$subject_grand_total=0;

$subject_total_weight_percentage=0;
 
foreach ($exam_term_exam_assessment as $exam_key => $exam_value) {

$exam_subject_total=0;
$exam_subject_maximum_total=0;
      foreach ($exam_value['exam_total_assessments'] as $exam_assessment_key => $exam_assessment_value) {
          
$subject_marks_array= getSubjectData($student_value,$exam_value['exam_id'],$subject_array_key,$exam_assessment_value['assesment_type_id']);

if(!$subject_marks_array['marks'] <= 0 ||  $subject_marks_array['marks'] == "N/A"){

$exam_subject_total+=($subject_marks_array['marks'] == "N/A") ? 0 : $subject_marks_array['marks'] ;
$exam_subject_maximum_total+=$subject_marks_array['maximum_marks'];

}else{

$exam_subject_total+=0;
$exam_subject_maximum_total+=0;

}
      }
      
      $subject_percentage=getPercent($exam_subject_maximum_total,$exam_subject_total);
       $subject_total_weight_percentage+=($subject_percentage*($exam_value['weightage']/100));

}
     if (!array_key_exists($subject_array_key, $subject_wise_rank)) {
            $subject_wise_rank[$subject_array_key] = [];
        }

        $subject_wise_rank[$subject_array_key][] = [
            'student_session_id' => $student_value['student_session_id'],
            'rank_percentage'    => $subject_total_weight_percentage,
            'rank'=>0

        ];

$grand_total_exam_weight_percentage+=$subject_total_weight_percentage;
}

   $overall_percentage = getPercent((count($subject_array) * 100), $grand_total_exam_weight_percentage);
  
    $student_allover_exam_rank[$student_value['student_session_id']]=[
      'student_session_id'=>$student_value['student_session_id'],
      'firstname'=>$student_value['firstname'],
      'rank_percentage'=>$overall_percentage,
      'rank'=>0,
  ];

}

// //-=====================start term calculation Rank=============

$rank_overall_term_percentage_keys = array_column($student_allover_exam_rank, 'rank_percentage');
$rank_overall_term_student_name_keys = array_column($student_allover_exam_rank, 'firstname');
 array_multisort($rank_overall_term_percentage_keys, SORT_DESC, $rank_overall_term_student_name_keys , SORT_ASC,$student_allover_exam_rank);

$term_rank_allover_list=unique_array($student_allover_exam_rank, "rank_percentage");

foreach ($student_allover_exam_rank as $term_rank_key => $term_rank_value) { 
   $student_allover_exam_rank[$term_rank_key]['rank']=array_search($term_rank_value['rank_percentage'],$term_rank_allover_list);
}

//-=====================end term calculation Rank=============

//=====================start subject term calculation Rank=============

foreach ($subject_wise_rank as $subject_term_key => $subject_term_value) {

$rank_overall_subject = array_column($subject_wise_rank[$subject_term_key], 'rank_percentage');

 array_multisort($rank_overall_subject, SORT_DESC,$subject_wise_rank[$subject_term_key]);

$subject_rank_allover_list=unique_array($subject_wise_rank[$subject_term_key], "rank_percentage");

foreach ($subject_wise_rank[$subject_term_key] as $subject_rank_key => $subject_rank_value) {

    $subject_wise_rank[$subject_term_key][$subject_rank_key]['rank']=array_search($subject_rank_value['rank_percentage'],$subject_rank_allover_list);
    
}

}

 ?>

<?php 

$count_result=count($result);
$student_increment=0;

foreach ($result as $student_key => $student_value) {
  $student_increment++;
  $grand_total_marks=0;
  $grand_total_exam_weight_percentage=0;
  $grand_total_gain_marks=0;
  $terms_weight_array=[];
    $total_present_day=0;
    $total_total_working_day=0;

  foreach ($student_value['exams'] as $each_exam_key => $each_exam_value) {

  $total_present_day+=$each_exam_value['total_present_days'];
   $total_total_working_day+=$each_exam_value['total_working_days'] ;
  }    

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
      <td valign="top" width="100%">
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
                  <td valign="top" style="font-weight: bold; padding-bottom: 2px;"><?php echo $this->lang->line('roll_no'); ?></td>
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

    <tr><td valign="top" style="height:10px"></td></tr>
    <tr>
      <td valign="top">
 <table cellpadding="0" cellspacing="0" width="100%" class="denifittable">
          <thead>
            <tr>
              <td valign="middle" class="text-center"><?php echo $this->lang->line('scholastic_areas'); ?></td>

<?php 
foreach ($exam_term_exam_assessment as $assess_key => $assess_value) {

 $term_colspan=count($assess_value['exam_total_assessments']);

 $terms_weight_array[]=($template['is_weightage'] == "no") ? ($assess_value['exam_name']) :$assess_value['exam_name']." (".$assess_value['weightage'].")";
  ?>
 <td valign="middle" class="text-center" colspan="<?php echo $term_colspan+2; ?>"><?php echo $assess_value['exam_name']; ?> 
 </td> 

  <?php
}
 ?>             
              <td valign="top" class="text-center" colspan="3">   
               <?php 
               //term merge array              
                      echo implode(" + ", $terms_weight_array);
               ?>
             </td>          
          </tr> 
 <tr>
            <td valign="middle" class="text-center"><?php echo $this->lang->line('subject'); ?></td>
                     <?php 
foreach ($exam_term_exam_assessment as $exam_name => $exam_value) { 

    foreach ($exam_value['exam_total_assessments'] as $exam_assement_key => $exam_assement_value) {
      ?> <td valign="middle" class="text-center"><?php 
    echo $exam_assement_value['assesment_type_name']. " (".$exam_assement_value['assesment_type_code'].")";
     echo "<br/>";
       echo $exam_assement_value['assesment_type_maximum_marks'];  

    ?>
    </td>
    <?php  
      }
  
?>
<td  valign="middle" class="text-center"><?php echo $this->lang->line('total'); ?></td>
<td  valign="middle" class="text-center"><?php echo $this->lang->line('grade'); ?></td>
<?php
}
 ?>  
           <td valign="middle" class="text-center"><?php echo $this->lang->line('grand_total'); ?><br/> <?php echo $this->lang->line('out_of'); ?> (100)</td>
           <td valign="middle" class="text-center"><?php echo $this->lang->line('grade'); ?></td>
           <td valign="middle" class="text-center"><?php echo $this->lang->line('rank'); ?></td>
       
          </tr>            
             <?php 

foreach ($subject_array as $subject_array_key => $subject_array_value) {
  
$subject_grand_total=0;

$subject_total_weight_percentage=0;
?>
<tr>  
<td valign="top"><?php echo $subject_array_value;?></td>
     <?php 
foreach ($exam_term_exam_assessment as $exam_key => $exam_value) {

$exam_subject_total=0;
$exam_subject_maximum_total=0;
      foreach ($exam_value['exam_total_assessments'] as $exam_assessment_key => $exam_assessment_value) {
      ?>
   <td valign="top" class="text-center">
    <?php 

$subject_marks_array= getSubjectData($student_value,$exam_value['exam_id'],$subject_array_key,$exam_assessment_value['assesment_type_id']);

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
       $subject_total_weight_percentage+=($subject_percentage*($exam_value['weightage']/100));

      ?>
<td valign="top">
<?php echo $exam_subject_total; ?></td>
<td valign="top">  <?php echo getGrade($exam_grades,$subject_percentage); ?></td>
      <?php 
}
 ?>
   <td valign="top"> <?php echo two_digit_float($subject_total_weight_percentage); ?></td>
<td valign="top"> <?php echo getGrade($exam_grades,$subject_total_weight_percentage); ?></td>        
<td valign="top"> 
<?php 
      echo searchSubjectRank($student_value['subject_rank'],$subject_array_key); ?>  
</td>        
</tr>
<?php
$grand_total_exam_weight_percentage+=$subject_total_weight_percentage;
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

$overall_percentage=getPercent((count($subject_array)*100),$grand_total_exam_weight_percentage);
             ?>
            <td><?php echo $this->lang->line('overall_marks'); ?> : <?php echo two_digit_float($grand_total_exam_weight_percentage, 2)."/".count($subject_array)*100; ?></td>
            <td><?php echo $this->lang->line('percentage'); ?> : <?php echo two_digit_float($overall_percentage, 2); ?></td>
            <td><?php echo $this->lang->line('grade'); ?> : <?php echo getGrade($exam_grades,$overall_percentage); ?></td>
            <td><?php echo $this->lang->line('rank'); ?> :  <?php echo $student_value['rank']; ?></td>            
          </tr>          
        </tbody>
      </table>
    </td>
    </tr>
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

<b><?php echo $this->lang->line('class_teacher_remark'); ?> :</b>  <?php echo $student_value['remark']; ?>
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
$total_colspan=4;
foreach ($exam_term_exam_assessment as $assess_key => $assess_value) { 

 $term_colspan=count($assess_value['exam_total_assessments']);
$total_colspan+=$term_colspan+2;

}
 ?>              
              <td valign="top" colspan="<?php echo $total_colspan ?>">   
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


 function getStudentObservation($student_observations,$student_session_id,$cbse_term_id,$parameter_id){
   if(!empty($student_observations)){
    if(array_key_exists($student_session_id, $student_observations)){

      if (array_key_exists($cbse_term_id, $student_observations[$student_session_id]['terms'])) {      

      if (array_key_exists( $parameter_id, $student_observations[$student_session_id]['terms'][$cbse_term_id]['paramters'])) {
      
           return $student_observations[$student_session_id]['terms'][$cbse_term_id]['paramters'][$parameter_id];  
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