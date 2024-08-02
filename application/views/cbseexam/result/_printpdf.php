<?php 
$student_allover_rank=[];
$subject_rank = [];
foreach ($result as $student_key => $student_value) {
  $total_max_marks=0;
$total_gain_marks=0;

foreach ($student_value['term']['exams'] as $student_exam_key => $student_exam_value) {
  foreach ($student_exam_value['subjects'] as $subject_key => $subject_value) {
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

foreach ($subject_rank as $subject_term_key => $subject_term_value) {

    $rank_overall_subject = array_column($subject_rank[$subject_term_key], 'rank_percentage');

    array_multisort($rank_overall_subject, SORT_DESC,$subject_rank[$subject_term_key]);

    $subject_rank_allover_list=unique_array($subject_rank[$subject_term_key], "rank_percentage");

foreach ($subject_rank[$subject_term_key] as $subject_rank_key => $subject_rank_value) {

    $subject_rank[$subject_term_key][$subject_rank_key]['rank']=array_search($subject_rank_value['rank_percentage'],$subject_rank_allover_list);
    
}

}
             ?>

<?php 

$count_result=count($result);
$student_increment=0;

foreach ($result as $student_key => $student_value) {
    $student_increment++;
  $total_max_marks=0;
$total_gain_marks=0;
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
                <td valign="top">: <?php echo $this->customlib->dateformat(date('Y-m-d')); ?></td>                
                         
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
        </table>
      </td>
    </tr>
    <tr><td valign="top" style="height:10px"></td></tr>
    <tr>
      <td valign="top">
     <table cellpadding="0" cellspacing="0" width="100%" class="denifittable">
          <thead>         
          <tr>
            <td valign="middle" ><?php echo $this->lang->line('subject'); ?></td>
            <?php 

foreach ($student_value['term']['exams'] as $exam_key => $exam_value) {
  
reset($exam_value['subjects']);
$subject_first_key = key($exam_value['subjects']);
  foreach ($exam_value['subjects'][$subject_first_key]['exam_assessments'] as $subject_assesment_key => $subject_assesment_value) {
   
    ?> <td valign="middle" class="text-center">
      <?php 
      echo $subject_assesment_value['cbse_exam_assessment_type_name']."-"." (".$subject_assesment_value['cbse_exam_assessment_type_code'].")";
      echo "<br/>";
       echo $subject_assesment_value['maximum_marks'] ;
       ?></td><?php
  }
}
             ?>          
     
            <td valign="middle" class="text-center"><?php echo $this->lang->line('total'); ?></td>          
            <td valign="middle" class="text-center"><?php echo $this->lang->line('grade'); ?></td>
            <td valign="middle" class="text-center"><?php echo $this->lang->line('rank'); ?></td>
          </tr>  
        </thead>
        <tbody>

               <?php 

foreach ($student_value['term']['exams'] as $student_exam_key => $student_exam_value) {
  foreach ($student_exam_value['subjects'] as $exam_key => $exam_value) {    
    ?>
<tr>
            <td valign="top"><?php echo $exam_value['subject_name']." (".$exam_value['subject_code'].")"; ?>
            </td>
            <?php 
            $subject_total=0;
            $subject_max_total=0;
foreach ($exam_value['exam_assessments'] as $assessment_key => $assessment_value) {
    $subject_total+=$assessment_value['marks'];
    $subject_max_total+=$assessment_value['maximum_marks'];
    $total_gain_marks+=$assessment_value['marks'];
    $total_max_marks+=$assessment_value['maximum_marks']; 
  ?>
    <td valign="top" class="text-center">
<?php 
 if(is_null($assessment_value['marks'])){
      echo "N/A";
    }else{
      echo ($assessment_value['is_absent']) ? $this->lang->line('abs') :$assessment_value['marks'];      
    }
 ?>
    </td>
  <?php
}
             ?>           
            <td valign="top" class="text-center"><?php echo $subject_total; ?></td>
            <td valign="top" class="text-center">
              <?php 
                $subject_percentage=getPercent($subject_max_total,$subject_total);
                echo  getGrade($exam,$subject_percentage);
              ?>              
            </td>
            <td valign="top" class="text-center">         
            
            <?php echo searchSubjectRank($student_value['subject_rank'],$exam_value['subject_id']); ?>

           </td>        
           
          </tr>
    <?php  
  }

}
$exam_percentage=getPercent($total_max_marks,$total_gain_marks);

             ?>
          
        </tbody>
    </table>
      </td>
    </tr>
     <tr><td valign="top" style="height:0px"></td></tr>
    <tr>
      <td>
      <table  cellpadding="0" cellspacing="0" width="100%" class="denifittable">
        <tbody>
          <tr>
            <td><?php echo $this->lang->line('overall_marks'); ?> : <?php echo two_digit_float($total_gain_marks, 2)."/".$total_max_marks ?></td>
            <td><?php echo $this->lang->line('percentage'); ?> : <?php echo two_digit_float($exam_percentage, 2); ?></td>
            <td><?php echo $this->lang->line('grade'); ?> : <?php echo getGrade($exam,$exam_percentage) ?></td>
            <td><?php echo $this->lang->line('rank'); ?> : <?php echo $student_value['rank']; ?></td>            
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
    <td valign="middle" class="text-center"><?php echo $student_value['total_working_days']; ?></td>
    <td valign="middle" class="text-center"><?php echo $student_value['total_present_days']; ?></td>
    <td valign="middle" class="text-center"><?php echo getPercent($student_value['total_working_days'],$student_value['total_present_days']);?></td>
  </tr>
</tbody>
</table>
    </td>
    </tr>
    <tr>
    <tr>
      <td style="padding-bottom: 6px;display: block;">
<b><?php echo $this->lang->line('class_teacher_remark'); ?> :</b> <?php echo $student_value['remark']; ?>
    </td>
    </tr>
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
  <td valign="top" class="text-left" colspan="<?php echo count($exam_assessments)+4 ?>">
     <?php echo $this->lang->line('grading_scale'); ?> : <?php 

            echo implode(', ', array_map(
            function($k)  {
                return $k->name." (".$k->maximum_percentage . "% - " . $k->minimum_percentage."%)";
            },
            ($exam->grades)
            
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

  if(!empty($grade_array->grades)){
    foreach ($grade_array->grades as $grade_key => $grade_value) {

      if($grade_value->minimum_percentage <= $Percentage){
 return $grade_value->name;
         break;
}elseif( ($grade_value->minimum_percentage >= $Percentage && $grade_value->maximum_percentage <= $Percentage)){

         return $grade_value->name;
         break;
      }

    }

  }
return "-";

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