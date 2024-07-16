<?php 
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$this->load->model('addresult_model');
?>

<div class="box-body" style="padding-top:0;">
    <div class="row">
        <div class="col-md-12">

        <!-- <?php echo $stid; ?>
        <?php echo $resulttype; ?>
        <?php echo $academicid; ?> -->
            
            <table class="table table-bordered table-b mb0">
                <tbody>
                    <tr>
                        <td class="bolds"><?php echo $this->lang->line('student_name') ;?></td>
                        <td><?php echo $studentdata['firstname'];?> <?php echo $studentdata['middlename'];?> <?php echo $studentdata['lastname'];?></td>
                        <td class="bolds"><?php echo $this->lang->line('admission_no') ;?></td>
                        <td><?php echo $studentdata['admission_no'];?></td>
                    
                    </tr>
                    <tr>
                        <td class="bolds"><?php echo $this->lang->line('gender') ;?></td>
                        <td><?php echo $studentdata['gender'];?></td>
                        <td class="bolds"><?php echo $this->lang->line('admi_no') ;?></td>
                        <td><?php echo $admissionno;?></td>
                    
                    </tr>
                    <tr>
                        <td class="bolds"><?php echo $this->lang->line('exam') ;?></td>
                        <td><?php echo $resultname['examtype'];?></td>
                        <td class="bolds"><?php echo $this->lang->line('mobile_no') ;?></td>
                        <td><?php echo $studentdata['mobileno'];?></td>
                    
                    </tr>
                </tbody>
            </table><br>
            
            <table class="table table-bordered table-b mb0">
                <tbody>

                    <tr>
                        <td class="bolds" style="text-align: center">
                            <?php echo $this->lang->line('subject') ;?>
                        </td>
                        <td class="bolds" style="text-align: center">
                            <?php echo $this->lang->line('total') ;?>
                        </td>
                        <td class="bolds" style="text-align: center">
                            <?php echo $this->lang->line('percentage');?>
                        </td>
                        <td class="bolds" style="text-align: center">
                            <?php echo $this->lang->line('status') ;?>
                        </td>
                    </tr>

                    <?php 
                        $total_marks = 0;
                        $total_max_marks=0;
                        $finalresultstatus=1;
                        foreach($resultdata as $res){
                            $markdata = $this->addpublicresult_model->getmarksid($res['markstableid'],$academicid);
                            $total_marks=$total_marks+$res['actualmarks'];
                            $total_max_marks=$total_max_marks+$markdata['maxmarks'];
                    ?>

                    <tr>
                        <td style="text-align: center"><?php echo $res['examtype'];?></td>
                        <td style="text-align: center"><?php echo $res['actualmarks'];?>/<?php echo $markdata['maxmarks'];?></td>
                        <td style="text-align:center"><?php echo getPercent($markdata['maxmarks'],$res['actualmarks']);?>%</td>
                        <td style="text-align: center">
                            <!-- <?php echo $res['maxmarks'];?> -->
                            <?php if($markdata['minmarks']>$res['actualmarks']){
                                $finalresultstatus=0;
                            ?>
                                <span class="label label-danger"><?php echo $this->lang->line('fail');?></span>
                            <?php }else{?>
                                <span class="label label-success" ><?php echo $this->lang->line('pass');?></span>
                            <?php }?>
                        </td>
                    </tr>

                    <?php 
                        }
                        $exam_percentage = getPercent($total_max_marks,$total_marks);
                    ?>
                    
                    <tr>
                        <td class="bolds"><?php echo $this->lang->line('total_marks'); ?> : <?php echo $total_marks . "/" . $total_max_marks; ?></td>
                        <td class="bolds"><?php echo $this->lang->line('percentage'); ?> (%) : <?php echo $exam_percentage; ?></td>
                        <td class="bolds">
                            <?php echo $this->lang->line('result_status');?>
                            <?php if(!$finalresultstatus){?>
                                <span class="label label-danger"><?php echo $this->lang->line('fail');?></span>
                            <?php }else{?>
                                <span class="label label-success" ><?php echo $this->lang->line('pass');?></span>
                            <?php }?>
                        </td>

                    </tr>

                </tbody>
            </table>

            

        </div>

    </div>
</div>


<?php 

function getPercent($maxtotal, $total) {
    if ($maxtotal == 0) {
        return 0; // Avoid division by zero
    }
    
    $percentage = ($total / $maxtotal) * 100;
    
    return intval($percentage); // Convert the percentage to an integer
}

?>