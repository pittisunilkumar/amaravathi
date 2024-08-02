<?php $this->load->view('layout/cbseexam_css.php'); ?>
<div class="content-wrapper">
    <!-- Main content -->
        <section class="content">
    <?php $this->load->view('cbseexam/report/_cbsereport'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('subject_wise_marks_report'); ?> </h3>
                    </div>
                    <div class="box-body">
                        <form role="form" action="<?php echo site_url('cbseexam/report/examsubject') ?>" method="post" class="row">
                            <?php echo $this->customlib->getCSRF(); ?>                           
                            <div class="col-md-3">
                                <div class="form-group">   
                                    <label><?php echo $this->lang->line('exam'); ?></label><small class="req"> *</small>
                                    <select id="exam_id" name="exam_id" class="form-control select2" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($exams as $exam_key =>$exam_value) {
                                            ?>
                                            <option value="<?php echo $exam_value['id'] ?>" <?php
                                            if (set_value('exam_id') == $exam_value['id']) {
                                                echo "selected=selected";
                                            }
                                            ?>><?php echo $exam_value['name'] ?></option>
                                            <?php
                                                }
                                            ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('exam_id'); ?></span>
                                </div>  
                            </div>                          
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary pull-right btn-sm checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php 
                    if(isset($subjects)){

if(!empty($subjects) && !empty($students)){

    //===============
    ?>
     <div class="btn-group  container pb10" role="group" aria-label="First group"> 
          <button type="button" class="btn btn-default btn-xs" title="<?php echo $this->lang->line('print'); ?>" onclick="printDiv('div_print')"><i class="fa fa-print"></i></button>
          <button type="button" class="btn btn-default btn-xs" title="<?php echo $this->lang->line('download_excel'); ?>" onclick="exportToExcel('div_print')"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
     </div>
    <div class="table-responsive box-body" id="div_print">  
        
 
     <h4 id="print_title"><?php echo $exam->name;?></h4>

<table class="table table-bordered table-b vertical-middle">
    <thead>
        <tr>
            <th rowspan="2" class=" white-space-nowrap border-t"><?php echo $this->lang->line('student'); ?></th>
            <th rowspan="2" class=" white-space-nowrap border-t"><?php echo $this->lang->line('admission_no'); ?></th>
            <th rowspan="2" class=" white-space-nowrap border-t"><?php echo $this->lang->line('father_name'); ?></th>
           <?php 
            foreach ($subjects as $subject_key => $subject_value) {
                       ?>
                    <th colspan="<?php echo count($exam_assessments);?>" class="text-center">
                        <?php echo $subject_value->subject_name."(".$subject_value->subject_code.")"; ?>    
                    </th>
                       <?php
            }
            ?>
                 <th rowspan="2" class="white-space-nowrap text-center"><?php echo $this->lang->line('total_marks'); ?></th>
                 <th rowspan="2" class="white-space-nowrap text-center"><?php echo $this->lang->line('percentage'); ?> (%)</th>
                 <th rowspan="2" class="white-space-nowrap text-center"><?php echo $this->lang->line('grade'); ?></th>
                 <th rowspan="2" class="white-space-nowrap text-center"><?php echo $this->lang->line('rank'); ?></th>
        </tr>
        <tr>
           
            <?php 
                foreach ($subjects as $subject_key => $subject_value) {
                 foreach ($exam_assessments as $exam_assessment_key => $exam_assessment_value) {
                    ?>
                <th class="vertical-middle text-center">                    
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
  
    foreach ($students as $student_key => $student_value) {
  
        $total_marks=0;
        $total_max_marks=0;
        ?>
<tr>
    <td><?php echo $student_value['firstname']." ". $student_value['middlename']." ".$student_value['lastname']; ?></td>
    <td><?php echo $student_value['admission_no']; ?></td>
    <td><?php echo $student_value['father_name']; ?></td>
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
<div class="box-body pb0">
    <div class="alert alert-info">
        <?php echo $this->lang->line('no_record_found'); ?>
    </div>
</div>
    <?php
}
}

                     ?>
               
            </div>
        </div>
    </section>
</div>
</section>
</div>

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

<script type="text/javascript">

function printDiv(tagid) {
        let hashid = "#"+ tagid;
//             var tagname =  $(hashid).prop("tagName").toLowerCase() ;
//             var attributes = ""; 
//             var attrs = document.getElementById(tagid).attributes;
//               $.each(attrs,function(i,elem){
//                 attributes +=  " "+  elem.name+" ='"+elem.value+"' " ;
//               })
//             var divToPrint= $(hashid).html() ;
//             var head = "<html><head>"+ $("head").html() + "</head>" ;
//             var allcontent = head + "<body  onload='window.print()' >"+ "<" + tagname + attributes + ">" +  divToPrint + "</" + tagname + ">" +  "</body></html>"  ;
//             var newWin=window.open('','Print-Window');
//             newWin.document.open();
//             newWin.document.write(allcontent);
//             newWin.document.close();
         
// //   setTimeout(function(){newWin.close();},10);
// setTimeout(function(){newWin.close();},10);
var tagname =  $(hashid).prop("tagName").toLowerCase() ;
            var attributes = ""; 
            var attrs = document.getElementById(tagid).attributes;
              $.each(attrs,function(i,elem){
                attributes +=  " "+  elem.name+" ='"+elem.value+"' " ;
              })
            var divToPrint= $(hashid).html() ;
            var head = "<html><head>"+ $("head").html() + "</head>" ;
            var allcontent = head + "<body  onload='window.print()' >"+ "<" + tagname + attributes + ">" +  divToPrint + "</" + tagname + ">" +  "</body></html>"  ;


var allcontent = head + "<body>"+ "<" + tagname + attributes + ">" +  divToPrint + "</" + tagname + ">" +  "</body></html>"  ;
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({ "position": "absolute", "top": "-1000000px" });
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
   
        frameDoc.document.write(allcontent);
 
        frameDoc.document.close();
        setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);



    }


    function exportToExcel(tagid){
var htmls = "";
let hashid = "#"+ tagid;
            var uri = 'data:application/vnd.ms-excel;base64,';
            var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'; 
            var base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            };

            var format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                })
            };

            htmls =   $(hashid).html() ;

            var ctx = {
                worksheet : 'Worksheet',
                table : htmls
            }


            var link = document.createElement("a");
            link.download = "export.xls";
            link.href = uri + base64(format(template, ctx));
            link.click();
}
</script>