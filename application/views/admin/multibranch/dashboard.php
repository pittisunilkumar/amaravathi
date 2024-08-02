<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> Dashboard</h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <h3>School Fee Details --r</h3>



                      <div class="table-responsive">
                        <table class="table table-hover  table-striped  table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo ('school --r'); ?></th>
                                    <th><?php echo ('current session --r'); ?></th>
                                    <th><?php echo ('total_students --r'); ?></th>
                                    <th class="text text-center"><?php echo ('total_Fees --r'); ?></th>
                                    <th class="text text-center"><?php echo ('total_paid_fees --r'); ?></th>
                                    <th class="text text-center"><?php echo ('total_balance_Fees --r'); ?></th>

                                </tr>
                            </thead>
                            <tbody>

                                   <?php
                                if (!empty($school_students)) {
                                    foreach ($school_students as $school_key => $school_value) {
                                       
                                        ?>
                                <tr>
                                    <td><?php echo $school_value['name']; ?></td>
                                    <td><?php echo $school_value['session']; ?></td>
                                    <td><?php echo $school_value['total_student']; ?></td>
                                    <td class="text text-right"><?php echo $school_value['total_fees']; ?></td>
                                    <td class="text text-right"><?php echo $school_value['total_paid']; ?></td>
                                    <td class="text text-right"><?php echo $school_value['total_balance']; ?></td>

                                </tr>
                                <?php

                                    }

                                } else {

                                }

                                ?>
                            </tbody>
                        </table>
                    </div>


                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->

        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
