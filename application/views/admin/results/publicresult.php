<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper">  
    <section class="content-header">
        <h1><i class="fa fa-newspaper-o"></i> <?php //echo $this->lang->line('certificate'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">

        <?php if ($this->session->flashdata('msg')) { ?>
            <?php 
                echo $this->session->flashdata('msg');
                $this->session->unset_userdata('msg');
            ?>
        <?php } ?> 

        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">


                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('public_result'); ?></h3>
                    </div>


                    <div class="box-body">
                        <div class="row">
                            <form role="form" action="<?php echo site_url('admin/results/publicresult/search') ?>" method="post" class="">
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="col-md-4 col-sm-2">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('hall_no'); ?></label><small class="req">*</small>
                                        <input autofocus="" id="hallticket_no" name="hallticket_no" placeholder="" type="text" class="form-control"  value="<?php echo set_value('hallticket_no')?>" />
                                        <span class="text-danger"><?php echo form_error('hallticket_no'); ?></span>
                                
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-2">
                                    <div class="form-group"> 
                                        <label><?php echo $this->lang->line('academic_year'); ?></label><small class="req"> *</small>
                                        <select autofocus="" id="academic_id" name="academic_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($sessionss as $class) {
                                                ?>
                                                <option value="<?php echo $class['id']; ?>" <?php if (set_value('academic_id') == $class['id']) echo "selected=selected" ?>><?php echo $class['session']; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('academic_id'); ?></span>
                                    </div>  
                                </div>

                                <div class="col-md-4 col-sm-2">
                                    <div class="form-group"> 
                                        <label><?php echo $this->lang->line('exam'); ?></label><small class="req"> *</small>
                                        <select  id="exam_id" name="exam_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($categorylist as $category) {?>
                                                <option value="<?php echo $category['id'] ?>" <?php
                                            if (set_value('exam_id') == $category['id']) {
                                                    echo "selected=selected";
                                                }
                                                    ?>><?php echo $category['examtype'] ?></option>
                                                    <?php $count++;
                                                }
                                                ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('exam_id'); ?></span>
                                    </div>  
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                </div>


                            </form>
                        </div>  
                    </div>

                    
                    <div class="row" id="print_result">
                        <!-- left column -->
                        <div class="col-md-12">
                            <div class="box box-primary">


                                <div class="box-header ptbnull">
                                    <h3 class="box-title titlefix"> <?php echo $resultname['examtype']; ?></h3>

                                    <a class="btn btn-default btn-xs pull-right" id="print" title="<?php echo $this->lang->line('print'); ?>" onclick="printDiv()"><i class="fa fa-print"></i></a>
                                </div>


                                <?php if($resultstatus['assign_status']){
                                    
                                    ?>

                                    <div class="box-body" style="padding-top:0;">
                                        <div class="row">
                                            <div class="col-md-12">
                                                
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
                                                            <td class="bolds"><?php echo $this->lang->line('hall_no') ;?></td>
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

                                                                $markdata = $this->publicexamtype_model->getmarksid($res['markstableid'],$academicid);
                                                                
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
                                    } else {
                                ?>
                                    <div class="alert alert-info">
                                        <?php echo $this->lang->line('no_record_found'); ?>
                                    </div>
                                <?php
                                    }
                                ?>
                                <!-- /.box-body -->


                            </div>
                        </div>
                        <!--/.col (left) -->
                    </div>

                </div>  
            </div>  
        </div> 
    </section>
</div>
<div class="response"> 
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




<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



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




