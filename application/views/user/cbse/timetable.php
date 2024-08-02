<div class="content-wrapper">   
    <!-- /.control-sidebar -->
    <section class="content">
        <div class="row" id="print_result">
            <!-- left column -->
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">
                            <?php echo $this->lang->line('cbse_exam_timetable'); ?>
                        </h3>

                        <a class="btn btn-default btn-xs pull-right" id="print" title="<?php echo $this->lang->line('print'); ?>" onclick="printDiv()"><i class="fa fa-print"></i></a>
                    </div>
                    <div class="box-body" style="padding-top:0;">
                        <div class="row">
                            <div class="col-md-12">

                                <?php
                                if (!empty($exams)) {
                                    foreach ($exams as $exam_key => $exam_value) {
                                        ?>
                                        <h4 class="pagetitleh2 mt10 border-b-none">
                                            <?php echo $exam_value->name; ?>
                                        </h4>
                                        <?php

                                        if (!empty($exam_value->time_table)) {
                                            ?>
                                            <table class="table table-hover table-bordered table-stripped table-b">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo $this->lang->line('subject'); ?></th>
                                                        <th class="text text-center"><?php echo $this->lang->line('date'); ?></th>
                                                        <th class="text text-center"><?php echo $this->lang->line('start_time'); ?></th>
                                                        <th class="text text-center"><?php echo $this->lang->line('duration_minute'); ?></th>
                                                        <th class="text text-center"><?php echo $this->lang->line('room_no'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($exam_value->time_table as $time_table_key => $time_table_value) {

                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?php echo $time_table_value->subject_name . "(" . $time_table_value->subject_code . ")" ?>
                                                            </td>
                                                            <td class="text text-center">
                                                                <?php echo $this->customlib->dateformat($time_table_value->date); ?>
                                                            </td>
                                                            <td class="text text-center">
                                                                <?php echo $time_table_value->time_from; ?>
                                                            </td>
                                                            <td class="text text-center">
                                                                <?php echo $time_table_value->duration; ?>
                                                            </td>
                                                            <td class="text text-center">
                                                                <?php echo $time_table_value->room_no; ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                            <?php


                                        }

                                    }

                                }else{
                                    ?>
                                    <div class="alert alert-danger">
                                        <?php echo $this->lang->line('no_record_found'); ?>
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