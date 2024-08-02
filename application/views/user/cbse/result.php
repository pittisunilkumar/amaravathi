
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/ss-print.css"> 
<div class="content-wrapper">
 
    <!-- /.control-sidebar -->
    <section class="content">
        <div class="row" id="print_result">
            <!-- left column -->
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('cbse_exam_result'); ?></h3>

                        <a class="btn btn-default btn-xs pull-right" id="print" title="<?php echo $this->lang->line('print'); ?>" onclick="printDiv()"><i class="fa fa-print"></i></a>
                    </div>
                    <div class="box-body" style="padding-top:0;">
                        <div class="row">
                            <div class="col-md-12">

                                <?php
                                if (!empty($exams)) {
                                    foreach ($exams as $exam_key => $exam_value) {

                                        $total_marks = 0;
                                        $total_max_marks = 0;
                                ?>

                                        <div class="shadow-sm mb30">
                                            <h3 class="pagetitleh2 mt10 border-b-none pl-5"><?php echo  $exam_value->name; ?></h3>

                                            <div class="table-responsive">

                                                <?php
                                                if (!empty($exam_value->subjects)) {
                                                ?>
                                                    <table class="table table-bordered table-b mb0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="bolds">
                                                                    <?php echo $this->lang->line('subject') ;?>
                                                                </td>
                                                                <?php

                                                                foreach ($exam_value->exam_assessments as $exam_assessment_key => $exam_assessment_value) {
                                                                ?>
                                                                    <td class="text-center bolds">

                                                                        <?php echo $exam_assessment_value->name . " (" . $exam_assessment_value->code . ")"; ?>
                                                                        <br />
                                                                        (<?php echo $this->lang->line('max'); ?> <?php echo $exam_assessment_value->maximum_marks; ?>)
                                                                    </td>
                                                                <?php
                                                                }

                                                                ?>
                                                                <td class="bolds">
                                                                     <?php echo $this->lang->line('total') ;?>
                                                                </td>
                                                            </tr>


                                                            <?php
                                                            foreach ($exam_value->subjects as $subject_key => $subject_value) {
                                                                $subject_total = 0;
                                                            ?>
                                                                <tr>

                                                                    <td>
                                                                        <?php echo $subject_value->subject_name . " (" . $subject_value->subject_code . ")"; ?>
                                                                    </td>
                                                                    <?php
                                                                    foreach ($exam_value->exam_assessments as $exam_assessment_key => $exam_assessment_value) {
                                                                    ?>
                                                                        <td class="text-center">
                                                                            <?php

                                                                            $assessment_array = findAssessmentValue($subject_value->subject_id, $exam_assessment_value->id, $exam_value);
                                                                            echo ($assessment_array['is_absent']) ? $this->lang->line('abs') : $assessment_array['marks'];
                                                                            if ($assessment_array['marks'] == "N/A") {
                                                                                $assessment_array['marks'] = 0;
                                                                            }
                                                                            $subject_total += $assessment_array['marks'];
                                                                            $total_max_marks += $assessment_array['maximum_marks'];
                                                                            $total_marks += $assessment_array['marks'];
                                                                            ?>
                                                                        </td>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    <td class="bolds">
                                                                        <?php echo  two_digit_float($subject_total); ?>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                            }
                                                            ?>


                                                        </tbody>


                                                    </table>
                                                <?php

                                                }
                                                $exam_percentage = getPercent($total_max_marks, $total_marks);
                                                ?>


                                            
                                            <table class="table mb0 bg-gray-light">
                                                <tr>
                                                    <td class="bolds"><?php echo $this->lang->line('total_marks'); ?> : <?php echo $total_marks . "/" . $total_max_marks; ?></td>
                                                    <td class="bolds"><?php echo $this->lang->line('percentage'); ?> (%) : <?php echo $exam_percentage; ?></td>
                                                    <td class="bolds"><?php echo $this->lang->line('grade'); ?> : <?php echo getGrade($exam_value->grades, $exam_percentage); ?></td>
                                                    <td class="bolds"><?php echo $this->lang->line('rank'); ?> : <?php echo $exam_value->rank; ?></td>
                                                </tr>
                                            </table>
                                        </div>  

                                            
                                        </div>

                                    <?php


                                    }
                                } else {
                                    ?>
                                    <div class="alert alert-info">
                                        <?php echo $this->lang->line('no_exam_assigned'); ?>
                                    </div>
                                <?php
                                }

                                ?>

                            </div>

                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            <!--/.col (left) -->
        </div>
    </section>
</div>


<?php

function findAssessmentValue($find_subject_id, $find_cbse_exam_assessment_type_id, $student_value)
{


    $return_array = [
        'maximum_marks' => "",
        'marks' => "",
        'note' => "",
        'is_absent' => "",
    ];


    if (array_key_exists('subjects', $student_value)) {

        if (array_key_exists($find_subject_id, $student_value->exam_data['subjects'])) {

            $result_array = ($student_value->exam_data['subjects'][$find_subject_id]['exam_assessments'][$find_cbse_exam_assessment_type_id]);


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

function unique_array($my_array, $key)
{
    $result = array();
    $i = 1;
    $key_array = array();

    foreach ($my_array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $result[$i] = $val['rank_percentage'];
            $i++;
        }
    }
    return $result;
}

function searcharray($value, $key, $array)
{
    foreach ($array as $k => $val) {

        if ($val[$key] == $value) {
            return $val['rank'];
        }
    }
    return null;
}

?>

<script type="text/javascript">
    document.getElementById("print").style.display = "block";

    function printDiv() {
        $("#visible").removeClass("hide");
        $(".pull-right").addClass("hide");

        document.getElementById("print").style.display = "none";

        var divElements = document.getElementById('print_result').innerHTML;
        var oldPage = document.body.innerHTML;
        document.body.innerHTML =
            "<html><head><title></title></head><body>" +
            divElements + "</body>";
        window.print();
        document.body.innerHTML = oldPage;
        location.reload(true);
    }
</script>