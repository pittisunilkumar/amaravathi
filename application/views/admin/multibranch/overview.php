<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

?>
<style type="text/css">
    @media print {
    .displaynone { display:block !important;}
}
</style>
 <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/multi_branch.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('overview'); ?> </h3>
                      
                        <div class="box-tools pull-right">
                            <button class="btn btn-primary btn-xs print_page" title="<?php echo $this->lang->line('print'); ?>">
                                
                        <i class="fa fa-print"></i>
                            </button>
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div id="print_div">
                    <div class="box-body">
                        <h3 class="displaynone"><?php echo $this->lang->line('overview'); ?> </h3>
                        <div class="box-table-card mb25">
                        <h4 class="pagetitleh-box"><?php echo $this->lang->line('fees_details'); ?></h4>
                        <div class="table-responsive mt5">
                        <table class="table table-hover mb5 table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="26%"><?php echo $this->lang->line('branch'); ?> </th>
                                    <th><?php echo $this->lang->line('current_session'); ?></th>
                                    <th><?php echo $this->lang->line('total_students'); ?></th>
                                    <th class="text text-right"><?php echo $this->lang->line('total_fees'); ?></th>
                                    <th class="text text-right"><?php echo $this->lang->line('total_paid_fees'); ?></th>
                                    <th class="text text-right"><?php echo $this->lang->line('total_balance_fees'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                   <?php
                                if (!empty($school_students)) {
                                    foreach ($school_students as $school_key => $school_value) {
                                                                  
                                        ?>
                                <tr>
                                    <td width="26%"><?php echo $school_value['name']; ?></td>
                                    <td><?php echo $school_value['session']; ?></td>
                                    <td><?php echo $school_value['total_student']; ?></td>
                                    <td class="text text-right"><?php echo $currency_symbol.amountFormat($school_value['total_fees']); ?></td>
                                    <td class="text text-right"><?php echo $currency_symbol.amountFormat($school_value['total_paid']); ?></td>
                                    <td class="text text-right"><?php echo $currency_symbol.amountFormat($school_value['total_balance']); ?></td>
                                </tr>
                                <?php

                                    }

                                } else {

                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                    <div class="box-table-card mb25">
                        <h4 class="pagetitleh-box"><?php echo $this->lang->line('transport_fees_details'); ?></h4>

                      <div class="table-responsive mt5">
                        <table class="table table-hover mb5 table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="26%"><?php echo $this->lang->line('branch'); ?></th>
                                    <th><?php echo $this->lang->line('current_session'); ?></th>                  
                                    <th class="text text-right"><?php echo $this->lang->line('total_fees'); ?></th>
                                    <th class="text text-right"><?php echo $this->lang->line('total_paid_fees'); ?></th>
                                    <th class="text text-right"><?php echo $this->lang->line('total_balance_fees'); ?></th>
                                </tr>
                            </thead>
                            <tbody>

                                   <?php
                                if (!empty($school_transport_fees)) {
                                    foreach ($school_transport_fees as $school_key => $school_value) {
                                       
                                        ?>
                                <tr>
                                    <td width="26%"><?php echo $school_value['name']; ?></td>
                                    <td ><?php echo $school_value['session']; ?></td>                                
                                    <td class="text text-right"><?php echo $currency_symbol.amountFormat($school_value['total_fees']); ?></td>
                                    <td class="text text-right"><?php echo $currency_symbol.amountFormat($school_value['total_paid']); ?></td>
                                    <td class="text text-right"><?php echo $currency_symbol.amountFormat($school_value['total_balance']); ?></td>
                                </tr>
                                <?php

                                    }

                                } else {

                                }

                                ?>
                            </tbody>
                        </table>
                    </div>     
                    </div>
                    <div class="box-table-card mb25">
                     <h4 class="pagetitleh-box"><?php echo $this->lang->line('student_admission'); ?></h4>
                      <div class="table-responsive mt5">
                        <table class="table table-hover mb5 table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('branch'); ?></th>
                                    <th><?php echo $this->lang->line('current_session'); ?></th>
                                    <th><?php echo $this->lang->line('offline_admission'); ?></th>
                                    <th><?php echo $this->lang->line('online_admission'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                   <?php

                                if (!empty($student_admission_list)) {
                                    foreach ($student_admission_list as $student_key => $student_value) {                                       
                                        ?>
                                <tr>
                                    <td width="26%"><?php echo $student_value['name']; ?></td>
                                    <td><?php echo $student_value['session']; ?></td>
                                    <td><?php echo $student_value['offline_admission']; ?></td>
                                    <td><?php echo $student_value['online_admission']; ?></td>                              

                                </tr>
                                <?php

                                    }

                                } else {

                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                    <div class="box-table-card mb25">
                       <h4 class="pagetitleh-box"><?php echo $this->lang->line('library_details'); ?></h4>
                      <div class="table-responsive mt5">
                        <table class="table table-hover mb5 table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('branch'); ?></th>
                                    <th><?php echo $this->lang->line('total_books'); ?></th>
                                    <th><?php echo $this->lang->line('members'); ?></th>
                                    <th><?php echo $this->lang->line('book_issued'); ?></th>                                
                                </tr>
                            </thead>
                            <tbody>
                                   <?php

                                if (!empty($student_books_list)) {
                                    foreach ($student_books_list as $books_key => $books_value) {
                                       
                                        ?>
                                <tr>
                                    <td width="26%"><?php echo $books_value['name']; ?></td>
                                    <td><?php echo $books_value['total_books']; ?></td>
                                    <td><?php echo $books_value['libarary_members']; ?></td>
                                    <td><?php echo $books_value['book_issued']; ?></td>

                                </tr>
                                <?php

                                    }

                                } else {

                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                    </div>

                    <div class="box-table-card mb25">
                      <h4 class="pagetitleh-box"><?php echo $this->lang->line('alumni_students'); ?></h4>
                      <div class="table-responsive mt5">
                        <table class="table table-hover mb5 table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('branch'); ?></th>                                  
                                    <th><?php echo $this->lang->line('alumni_students'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                   <?php

                                if (!empty($alumni_student_list)) {
                                    foreach ($alumni_student_list as $alumini_student_key => $alumini_student_value) {
                                       
                                        ?>
                                <tr>
                                    <td width="26%"><?php echo $alumini_student_value['name']; ?></td>
                                    <td><?php echo $alumini_student_value['total_alumni_student']; ?></td>             

                                </tr>
                                <?php

                                    }

                                } else {

                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                    <div class="box-table-card mb25">
                     <h4 class="pagetitleh-box"><?php echo $this->lang->line('staff_payroll_of'); ?> <?php echo $this->lang->line(strtolower($month)) ; ?> </h4>
                      <div class="table-responsive mt5">
                        <table class="table table-hover mb5 table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="26%"><?php echo $this->lang->line('branch'); ?></th>
                                    <th width="150"><?php echo $this->lang->line('total_staff'); ?></th>
                                    <th width="140"><?php echo $this->lang->line('payroll_generated'); ?> </th>
                                    <th><?php echo $this->lang->line('payroll_not_generated'); ?></th>
                                    <th><?php echo $this->lang->line('payroll_paid'); ?></th>
                                    <th class="text-right"><?php echo $this->lang->line('net_amount'); ?> </th>
                                    <th class="text-right"><?php echo $this->lang->line('paid_amount'); ?></th>
                                  
                                </tr>
                            </thead>
                            <tbody>

                                   <?php
                                if (!empty($staff_payslip)) {
                                    foreach ($staff_payslip as $staff_payslip_key => $staff_payslip_value) {
                                    
                                        ?>
                                <tr>
                                    <td width="26%"><?php echo $staff_payslip_value['name']; ?></td>
                                    <td><?php echo $staff_payslip_value['staff']; ?></td>
                                    <td><?php echo $staff_payslip_value['staff_status_generated']; ?></td>
                                    <td><?php echo $staff_payslip_value['staff'] - $staff_payslip_value['staff_status_generated']-$staff_payslip_value['staff_status_paid']; ?></td>
                                    <td><?php echo $staff_payslip_value['staff_status_paid']; ?></td>
                                    <td class="text text-right"><?php echo $currency_symbol.amountFormat($staff_payslip_value['payroll_amount']); ?></td>
                                    <td class="text text-right"><?php echo $currency_symbol.amountFormat($staff_payslip_value['payroll_amount_paid']); ?></td>                             

                                </tr>
                                <?php

                                    }

                                } else {

                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                    </div>
                    <div class="box-table-card mb25">
                    <h4 class="pagetitleh-box"><?php echo $this->lang->line('staff_attendance_details'); ?> <?php echo $this->customlib->dateformat(date("Y/m/d")) ?> </h4>

                      <div class="table-responsive mt5">
                        <table class="table table-hover mb5 table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('branch'); ?></th>
                                    <th><?php echo $this->lang->line('total_staff'); ?></th>
                                    <th><?php echo $this->lang->line('present'); ?></th>
                                    <th><?php echo $this->lang->line('absent'); ?></th>                                 

                                </tr>
                            </thead>
                            <tbody>
                                   <?php

                                if (!empty($staff_list)) {
                                    foreach ($staff_list as $staff_key => $staff_value) {                                    
                                       
                                        ?>
                                <tr>
                                    <td width="26%"><?php echo $staff_value['name']; ?></td>
                                    <td><?php echo $staff_value['total_staff']; ?></td>
                                    <td><?php echo ($staff_value['staff_present'] || $staff_value['staff_absent']) ? $staff_value['staff_present'] : "-"; ?></td>
                                    <td><?php  echo ($staff_value['staff_present'] || $staff_value['staff_absent']) ? $staff_value['staff_absent'] : "-"; ?> </td>                     

                                </tr>
                                <?php

                                    }

                                } else {

                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                    <div class="box-table-card mb25">
                      <h4 class="pagetitleh-box"><?php echo $this->lang->line('user_log_details'); ?></h4>
                      <div class="table-responsive mt5">
                        <table class="table table-hover mb5 table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo $this->lang->line('branch'); ?></th>
                                    <th><?php echo $this->lang->line('total_user_log'); ?></th>                            

                                </tr>
                            </thead>
                            <tbody>
                                   <?php

                                if (!empty($user_log_list)) {
                                    foreach ($user_log_list as $user_log_key => $user_log_value) {

                                        ?>
                                <tr>
                                    <td width="26%"><?php echo $user_log_value['name']; ?></td>
                                    <td><?php echo $user_log_value['total_userlog']; ?></td>                                 

                                </tr>
                                <?php

                                    }

                                } else {

                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                    </div><!-- /.box-body -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->            <!-- right column -->

        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->


<script type="text/javascript">
    $(document).on('click','.print_page',function(){
       
        var page_content= document.getElementById("print_div").innerHTML;
             Popup(page_content,false);

    });
       function Popup(data, winload = false)
    {
        var frame1 = $('<iframe />').attr("id", "printDiv");
        frame1[0].name = "frame1";
        frame1.css({"position": "absolute", "top": "-1000000px"});
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
        //Create a new HTML document.
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/bootstrap/css/bootstrap.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/font-awesome.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/ionicons.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/AdminLTE.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/dist/css/skins/_all-skins.min.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/iCheck/flat/blue.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/morris/morris.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/datepicker/datepicker3.css">');
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'backend/plugins/daterangepicker/daterangepicker-bs3.css">');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function () {
        document.getElementById('printDiv').contentWindow.focus();
        document.getElementById('printDiv').contentWindow.print();
        $("#printDiv", top.document).remove();
            if (winload) {
                window.location.reload(true);
            }
        }, 500);

        return true;
    }
</script>