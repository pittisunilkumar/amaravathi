<!DOCTYPE html>
<html lang="en">
<head>
  <title>Report Card</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
 
  <style type="text/css">
    *{padding: 0; margin: 0}
    body{color: #000; font-family: Arial; line-height: 20px; font-size: 12px}
    .denifittable table{border-collapse: collapse;}
    .denifittable th,
    .denifittable td{border: 1px solid #999;border-collapse: collapse;}
    .denifittable th,
    .denifittable td {}
    .denifittable tr th {padding: 3px 10px; font-weight: bold;}
    .denifittable tr td {padding: 3px 10px; font-weight: bold;}
    .denifittable-small table{border-collapse: collapse text-align: center;}
    .denifittable-small th,
    .denifittable-small td{border: 1px solid #999;border-collapse: collapse;}
    .denifittable-small th,
    .denifittable-small td {}
    .denifittable-small tr th {padding: 0px 10px; font-weight: bold; text-align: center}
    .denifittable-small tr td {padding: 0px 10px; font-weight: bold; text-align: center}
    .denifittable-big table{border-collapse: collapse text-align: center;}
    .denifittable-big th,
    .denifittable-big td{border: 1px solid #999;border-collapse: collapse;}
    .denifittable-big th,
    .denifittable-big td {}
    .denifittable-big tr th {padding: 9px 10px; font-weight: bold; text-align: center}
    .denifittable-big tr td {padding: 9px 10px; font-weight: bold; text-align: center}
    .fw-bold{font-weight: bold;}
    .text-decoration-underline{text-decoration: underline;}
    .tcmybg {
      background: top center;
      background-size: 100% 100%;
      position: absolute;
      top: 0;     
      left: 0;
      bottom: 0;
      z-index: 0;
    }
  </style>
</head>
<body>

<div style="width: 97%; margin: 0 auto;">

  <?php 
                  if($template['background_img'] !=""){
?>

   <img src="<?php echo base_url('uploads/cbseexam/template/background_img/'.$template['background_img']) ?>" class="tcmybg" width="100%" height="100%"> 
<?php
                  }
                   ?>

<div style="width: 100%; height:100%; margin: 0 auto; position: relative;" class="table-responsive">
  <?php 
                  if($template['header_image'] !=""){
?>

    <div class="text-center" style="margin-top: 5vh;">
                    <img src="<?php echo base_url('uploads/cbseexam/template/header_image/'.$template['header_image']) ?>" width="100%" height="auto">  
    </div>    
<?php
                  }
                   ?>                
  <table cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td valign="top">
        <table cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td valign="top" style="padding-bottom: 20px;">
               <?php 
                  if($template['exam_session']){
?>

              <h4 style="font-weight: bold; text-align: center;padding-top: 5px"><?php echo $this->lang->line('academic_session'); ?>: <?php
                echo $this->setting_model->getCurrentSessionName();                
              ?></h4>
<?php
                  }
                   ?>
              <h4 style="font-weight: bold; text-align: center;padding-top: 5px"><?php echo $this->lang->line('report_card'); ?></h4>
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
                  if($template['is_roll_no']){
                    ?>
 <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('roll_no'); ?></td>
                  <td valign="top">: 101</td>
                    <?php
                  }
                    ?>
                       <?php 
                  if($template['is_admission_no']){
                    ?>
  <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('admission_no'); ?>.</td>
                  <td valign="top">: 18001</td>
                    <?php
                  }
                    ?>
                </tr>
                <tr>
                    <?php 
                  if($template['is_name']){
                    ?>
                    <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('students_name'); ?></td>
                    <td valign="top">: Edward Thomas</td>
                    <?php
                  }
                   ?>
                <?php 
                  if($template['is_dob']){
                    ?>
  <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('date_of_birth'); ?></td>
                  <td valign="top">: 03-11-2014</td>
                    <?php
                  }
                ?>
                </tr>
                <tr>
                     <?php 
                  if($template['is_father_name']){
                    ?>
   <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('fathers_name'); ?></td>
                  <td valign="top">: Olivier Thomas</td>
                    <?php
                  }
                ?>
                     <?php 
                  if($template['date'] !=""){
                    ?>
  <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('result_declaration_date'); ?></td>
                  <td valign="top">: 26-03-2022 </td>
                    <?php
                  }
                ?>                
                </tr>
                <tr>
                     <?php 
                  if($template['is_mother_name']){
                    ?>
<td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('mothers_name'); ?></td>
                  <td valign="top">: Caroline Thomas</td>
                    <?php
                  }
                ?>                  
                     <?php 
                  if($template['is_class'] && $template['is_section']){
                    ?>
                  <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('class_section'); ?></td>
                  <td valign="top">: Class 3-A</td>
                    <?php
                  }elseif ($template['is_class']) {
                     ?>
                  <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('class'); ?></td>
                  <td valign="top">: Class 3</td>
                    <?php
                  }elseif ($template['is_section']) {
                     ?>
                  <td valign="top" style="font-weight: bold; padding-bottom: 2px"><?php echo $this->lang->line('section'); ?></td>
                  <td valign="top">: A</td>
                    <?php
                  }
                ?>        
                </tr>
              </table>
            </td>
             <?php 
                  if($template['is_photo']){
?>
 <td valign="top" align="right" width="20%" class="rtl-text-left"><img src="<?php echo base_url('uploads/student_images/default_male.jpg?1685767171') ?>" width="102" height="120" style="border:1px solid #000;"></td>
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
          <tr>
            <td valign="top" class="text-center"><?php echo $this->lang->line('scholastic_areas'); ?>:</td>
            <td valign="top" class="text-center" colspan="4">T1</td>
            <td valign="top" class="text-center" colspan="4">T2</td>
            <td valign="top" class="text-center" colspan="2">T1+T2</td>
            <td valign="top" class="text-center" rowspan="2"><?php echo $this->lang->line('rank'); ?></td>
          </tr>
          <tr>
            <td valign="top" class="text-center"><?php echo $this->lang->line('subject'); ?></td>
            <td valign="top" class="text-center">PT-I(10)</td>
            <td valign="top" class="text-center">Multiple Assessment (10)</td>
            <td valign="top" class="text-center">Half Yearly(80)</td>
            <td valign="top" class="text-center"><?php echo $this->lang->line('total'); ?>(100)</td>
            <td valign="top" class="text-center">PT-II(10)</td>
            <td valign="top" class="text-center">Multiple Assessment-2 (10)</td>
            <td valign="top" class="text-center">Annual(80)</td>
            <td valign="top" class="text-center"><?php echo $this->lang->line('total'); ?>(100)</td>
            <td valign="top" class="text-center"><?php echo $this->lang->line('marks_obtained'); ?> (100%)</td>   
            <td valign="top" class="text-center"><?php echo $this->lang->line('grade'); ?></td>
          </tr>
          <tr>
            <td valign="top">ENGLISH (001)</td>
            <td valign="top">3.50</td>
            <td valign="top">8.00</td>
            <td valign="top">48.50</td>
            <td valign="top">60.00</td>
            <td valign="top">3.50</td>
            <td valign="top">8.00</td>
            <td valign="top">28.00</td>
            <td valign="top">39.50</td>
            <td valign="top">9.75</td>
            <td valign="top">c2</td>
            <td valign="top">28</td>
          </tr>
          <tr>
            <td valign="top">HINDI (001)</td>
            <td valign="top">3.50</td>
            <td valign="top">8.00</td>
            <td valign="top">48.50</td>
            <td valign="top">60.00</td>
            <td valign="top">3.50</td>
            <td valign="top">8.00</td>
            <td valign="top">28.00</td>
            <td valign="top">39.50</td>
            <td valign="top">9.75</td>
            <td valign="top">c2</td>
            <td valign="top">28</td>
          </tr>
          <tr>
            <td valign="top">MATHEMATICS (001)</td>
            <td valign="top">3.50</td>
            <td valign="top">8.00</td>
            <td valign="top">48.50</td>
            <td valign="top">60.00</td>
            <td valign="top">3.50</td>
            <td valign="top">8.00</td>
            <td valign="top">28.00</td>
            <td valign="top">39.50</td>
            <td valign="top">9.75</td>
            <td valign="top">c2</td>
            <td valign="top">28</td>
          </tr>
          <tr>
            <td valign="top">EVS (001)</td>
            <td valign="top">3.50</td>
            <td valign="top">8.00</td>
            <td valign="top">48.50</td>
            <td valign="top">60.00</td>
            <td valign="top">3.50</td>
            <td valign="top">8.00</td>
            <td valign="top">28.00</td>
            <td valign="top">39.50</td>
            <td valign="top">9.75</td>
            <td valign="top">c2</td>
            <td valign="top">28</td>
          </tr>
          <tr>
            <td valign="top">COMPUTER (001)</td>
            <td valign="top">3.50</td>
            <td valign="top">8.00</td>
            <td valign="top">48.50</td>
            <td valign="top">60.00</td>
            <td valign="top">3.50</td>
            <td valign="top">8.00</td>
            <td valign="top">28.00</td>
            <td valign="top">39.50</td>
            <td valign="top">9.75</td>
            <td valign="top">c2</td>
            <td valign="top">28</td>
          </tr>       
          <tr>
          <td valign="top" class="text-left" colspan="12">
           <?php echo $this->lang->line('grading_scale'); ?>: A+  (100% - 90%), B+ (80% - 89.99%), C+ (50% - 79.99%), D (40% - 49.99%), E (0% - 39.99%)
          </td>
          </tr>         
        </table>
      </td>
    </tr>
    <tr><td valign="top" style="height:20px"></td></tr>
    <tr>
      <td>
      <table cellpadding="0" cellspacing="0" width="100%" class="denifittable">
        <tbody>
          <tr>
            <td><?php echo $this->lang->line('overall_marks'); ?> : 270.00/350</td>
            <td><?php echo $this->lang->line('percentage'); ?> 77.14</td>
            <td><?php echo $this->lang->line('grade'); ?> : C+</td>
            <td><?php echo $this->lang->line('rank'); ?> : 1</td>            
          </tr>
        </tbody>
      </table>
    </td>
    </tr>
    <tr><td valign="top" style="height:20px"></td></tr>
    <tr>
      <td>
<b><?php echo $this->lang->line('attendance'); ?> :</b> 200/300 
<br>
 <?php 
                  if($template['is_remark']){
?>
<b><?php echo $this->lang->line('teacher_remark'); ?> :</b> Good Student in Class 
<?php
                  }
                   ?>
</td>
    </tr>
    <tr><td valign="top" style="height:20px"></td></tr>
   <tr>
      <td valign="top" width="100%" align="center">
        <table cellpadding="0" cellspacing="0" width="100%" style="border-bottom:1px solid #999; margin-bottom:10px;">
          <tbody>
            <tr>
              <?php 
                  if($template['left_sign'] !=""){
?>
  <td valign="top" width="33%" class="signature text-center">
              <img src="<?php echo base_url('uploads/cbseexam/template/left_sign/'.$template['left_sign']) ?>" width="100" height="50" style="padding-bottom: 5px;">              
            </td>
<?php
                  }
                   ?>
                    <?php 
                  if($template['middle_sign'] !=""){
?>
  <td valign="top" width="33%" class="signature text-center">
              <img src="<?php echo base_url('uploads/cbseexam/template/middle_sign/'.$template['middle_sign']) ?>" width="100" height="50" style="padding-bottom: 5px;">              
            </td>
<?php
                  }
                   ?>
                    <?php 
                  if($template['right_sign'] !=""){
?>
  <td valign="top" width="33%" class="signature text-center">
              <img src="<?php echo base_url('uploads/cbseexam/template/right_sign/'.$template['right_sign']) ?>" width="100" height="50" style="padding-bottom: 5px;">
              
            </td>
<?php
                  }
                   ?>         
            
          </tr>
        </tbody></table>
      </td>
    </tr>  
    <tr>
      <td valign="top">
        <table cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td valign="top" width="50%">
              <table cellpadding="0" cellspacing="0" width="90%">             
                <tr>
                  <td valign="top">
                    <table cellpadding="0" cellspacing="0" width="100%" class="denifittable-small">
                      <tr>
                        <td valign="top" width="50%"><?php echo $this->lang->line('marks_range'); ?></td>
                        <td valign="top" width="50%"><?php echo $this->lang->line('grade'); ?></td>
                      </tr>
                      <tr>
                        <td valign="top">91-100</td>
                        <td valign="top">A1</td>
                      </tr>
                      <tr>
                        <td valign="top">81-90</td>
                        <td valign="top">A2</td>
                      </tr>
                      <tr>
                        <td valign="top">71-80</td>
                        <td valign="top">B1</td>
                      </tr>
                      <tr>
                        <td valign="top">61-70</td>
                        <td valign="top">B2</td>
                      </tr>
                      <tr>
                        <td valign="top">51-60</td>
                        <td valign="top">C1</td>
                      </tr>
                      <tr>
                        <td valign="top">41-50</td>
                        <td valign="top">C2</td>
                      </tr>
                      <tr>
                        <td valign="top">33-40</td>
                        <td valign="top">D</td>
                      </tr>
                      <tr>
                        <td valign="top">32&Below</td>
                        <td valign="top">E (Needsimprovement)</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
        <td valign="top" width="50%">
              <table cellpadding="0" cellspacing="0" width="90%">
             
                <tr>
                  <td valign="top">
                    <table cellpadding="0" cellspacing="0" width="100%" class="denifittable-small">
                      <tr>
                        <td valign="top" width="50%"><?php echo $this->lang->line('marks_range'); ?></td>
                        <td valign="top" width="50%"><?php echo $this->lang->line('grade'); ?></td>
                      </tr>
                      <tr>
                        <td valign="top">91-100</td>
                        <td valign="top">A1</td>
                      </tr>
                      <tr>
                        <td valign="top">81-90</td>
                        <td valign="top">A2</td>
                      </tr>
                      <tr>
                        <td valign="top">71-80</td>
                        <td valign="top">B1</td>
                      </tr>
                      <tr>
                        <td valign="top">61-70</td>
                        <td valign="top">B2</td>
                      </tr>
                      <tr>
                        <td valign="top">51-60</td>
                        <td valign="top">C1</td>
                      </tr>
                      <tr>
                        <td valign="top">41-50</td>
                        <td valign="top">C2</td>
                      </tr>
                      <tr>
                        <td valign="top">33-40</td>
                        <td valign="top">D</td>
                      </tr>
                      <tr>
                        <td valign="top">32&Below</td>
                        <td valign="top">E (Needsimprovement)</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
     <?php 
                  if($template['content_footer']){
?>
    <tr>
      <td valign="top">
      <?php echo $template['content_footer'] ?>
      </td>
    </tr>
<?php
                  }
                   ?>

  </table>
</div>
</div>
</body>
</html>