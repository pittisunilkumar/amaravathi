<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat();?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php
                if ($this->rbac->hasPrivilege('subject_group', 'can_add')) {
                    ?>
                <div class="col-md-4">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('add_subject_group'); ?></h3>
                        </div><!-- /.box-header -->
                        <form id="form1" action="<?php echo site_url('admin/results/subjectgroup') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) {
                                    ?>
                                    <?php echo $this->session->flashdata('msg');
                                    $this->session->unset_userdata('msg'); ?>
                                <?php }?>
                                <?php
                                if (isset($error_message)) {
                                        echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                    }
                                    ?>
                                <?php echo $this->customlib->getCSRF(); ?>
 
                                <!-- <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?></label> <small class="req">*</small>
                                    <input autofocus="" id="name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name'); ?>" />
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div> -->

                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('resut_type'); ?></label> <small class="req">*</small>
                                    <select autofocus="" id="fee_groups_id" name="fee_groups_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($resulttypelist as $resulttype) {
                                            ?>
                                            <option value="<?php echo $resulttype['id'] ?>"<?php
                                            if (set_value('fee_groups_id') == $resulttype['id']) {
                                                echo "selected =selected";
                                            }
                                            ?>><?php echo $resulttype['examtype'] ?></option>

                                            <?php
                                            $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('fee_groups_id'); ?></span>
                                </div>
                                
                                
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('subject') ?></label><small class="req"> *</small>

                                    <?php
                                    foreach ($subjectlist as $subject) {
                                            ?>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="subject[]" value="<?php echo $subject['id']?>" <?php echo set_checkbox('subject[]', $subject['id']); ?> ><?php echo $subject['examtype'] ?>
                                            </label>
                                        </div>

                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                                <tr>
                                                <div class="col-md-6"> 
                                                    <div class="form-group">
                                                        <label for="minmarks<?php echo $subject['id'];?>"><?php echo $this->lang->line('min_marks'); ?></label><small class="req">*</small>
                                                        <input autofocus="" id="minmarks<?php echo $subject['id'];?>" name="minmarks<?php echo $subject['id'];?>" placeholder="" type="text" class="form-control" value="" />
                                                        <span class="text-danger error-message" id="error-message-minmarks<?php echo $subject['id']; ?>"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="maxmarks<?php echo $subject['id'];?>"><?php echo $this->lang->line('max_marks'); ?></label><small class="req">*</small>
                                                        <input autofocus="" id="maxmarks<?php echo $subject['id'];?>" name="maxmarks<?php echo $subject['id'];?>" placeholder="" type="text" class="form-control" value="" />
                                                        <span class="text-danger error-message" id="error-message-maxmarks<?php echo $subject['id']; ?>"></span>
                                                    </div>
                                                </div>

                                                </tr>
                                            </tbody>
                                        </table>
                                        

                                        <?php
                                        }
                                            ?>
                                    <span class="text-danger"><?php echo form_error('subject[]'); ?></span>
                                </div>
                                
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>
                </div><!--/.col (right) -->
                <!-- left column -->
            <?php }?>
            <div class="col-md-<?php
                if ($this->rbac->hasPrivilege('subject_group', 'can_add')) {
                    echo "8";
                } else {
                    echo "12";
                }
                ?>">
                <!-- general form elements -->
                <div class="box box-primary" id="subject_list">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('subject_group_list'); ?></h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive mailbox-messages"> 
                            <div class="download_label"><?php echo $this->lang->line('subject_group_list'); ?></div>

                            <a class="btn btn-default btn-xs pull-right" title="<?php echo $this->lang->line('print'); ?>" id="print" onclick="printDiv()" ><i class="fa fa-print"></i></a> 
                            <a class="btn btn-default btn-xs pull-right" title="<?php echo $this->lang->line('export'); ?>"  id="btnExport" onclick="fnExcelReport();"> <i class="fa fa-file-excel-o"></i> </a>
                            
                            <table class="table table-striped  table-hover " id="headerTable">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('resut_type'); ?></th>
                                        <th><?php echo $this->lang->line('subject'); ?></th>
                                        <th class="text-right no_print"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($subjectgroupList as $subjectgroup) {
                                            ?>

                                            <?php if($subjectgroup->group_subject){?>
                                                <tr>
                                                    <td class="mailbox-name">
                                                        <a href="#" data-toggle="popover" class="detail_popover"><?php echo $subjectgroup->examtype; ?></a>
                                                        
                                                    </td>
                                                
                                                    <td>
                                                        <table width="100%">
                                                            <?php
                                                                foreach ($subjectgroup->group_subject as $group_subject_key => $group_subject_value) {
                                                                        ?>
                                                                <tr>
                                                                    <td>
                                                                        <?php echo "<div>" . $group_subject_value->examtype . " (Min:".$group_subject_value->minmarks.")"."(Max:".$group_subject_value->maxmarks.")". "</div>"; ?>
                                                                    </td>
                                                                </tr>

                                                                <?php
                                                            }
                                                                ?>
                                                        </table>
                                                    </td>

                                                    <td class="mailbox-date pull-right no_print">
                                                        <?php
                                                            if ($this->rbac->hasPrivilege('subject_group', 'can_edit')) {
                                                                    ?>
                                                            <a href="<?php echo base_url(); ?>admin/results/subjectgroup/edit/<?php echo $subjectgroup->id; ?>" class="btn btn-default btn-xs no_print displayinline" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php }?>
                                                        
                                                        <a data-placement="top" href="<?php echo base_url(); ?>admin/results/addresult/assign/<?php echo $subjectgroup->id; ?>"
                                                            class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('assign_view_student'); ?>">
                                                            <i class="fa fa-tag"></i>
                                                        </a>

                                                        <?php
                                                        if ($this->rbac->hasPrivilege('subject_group', 'can_delete')) {
                                                                ?>
                                                            <a  href="<?php echo base_url(); ?>admin/results/subjectgroup/delete/<?php echo $subjectgroup->id; ?>"class="btn btn-default btn-xs no_print displayinline" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                                <i  class="fa fa-remove"></i>
                                                            </a>
                                                        <?php }?>

                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php
                                        }
                                        ?>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
        <div class="row">
            <!-- left column -->
            <!-- right column -->
            <div class="col-md-12">
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>

   

    
    
    $(".no_print").css("display", "block");
    document.getElementById("print").style.display = "block";
    document.getElementById("btnExport").style.display = "block";

    function printDiv() {
        $(".no_print").css("display", "none");
        document.getElementById("print").style.display = "none";
        document.getElementById("btnExport").style.display = "none";
        var divElements = document.getElementById('subject_list').innerHTML;
        var oldPage = document.body.innerHTML;
        document.body.innerHTML =
                "<html><head><title></title></head><body>" +
                divElements + "</body>";
        window.print();
        document.body.innerHTML = oldPage;
        location.reload(true);
    }

    function fnExcelReport()
    {
        var tab_text = "<table border='2px'><tr >";
        var textRange;
        var j = 0;
        tab = document.getElementById('headerTable'); // id of table

        for (j = 0; j < tab.rows.length; j++)
        {
            tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
        }

        tab_text = tab_text + "</table>";
        tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
        tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
        tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");

        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
        {
            txtArea1.document.open("txt/html", "replace");
            txtArea1.document.write(tab_text);
            txtArea1.document.close();
            txtArea1.focus();
            sa = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
        } else                 //other browser not tested on IE 11
            sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

        return (sa);
    }

</script>


<script>
    // Add event listeners to each checkbox
    <?php
    foreach ($subjectlist as $subject) {
        ?>
        
        var checkbox<?php echo $subject['id']; ?> = document.querySelector('input[name="subject[]"][value="<?php echo $subject['id']; ?>"]');
        var minmarks<?php echo $subject['id']; ?> = document.getElementById('minmarks<?php echo $subject['id']; ?>');
        var maxmarks<?php echo $subject['id']; ?> = document.getElementById('maxmarks<?php echo $subject['id']; ?>');
        var errorElementMinmarks<?php echo $subject['id']; ?> = document.getElementById('error-message-minmarks<?php echo $subject['id']; ?>');
        var errorElementMaxmarks<?php echo $subject['id']; ?> = document.getElementById('error-message-maxmarks<?php echo $subject['id']; ?>');

        checkbox<?php echo $subject['id']; ?>.addEventListener('change', function() {
            if (checkbox<?php echo $subject['id']; ?>.checked) {
                // Validate the input here, for example, check if minmarks and maxmarks are filled.
                var minmarksValue = minmarks<?php echo $subject['id']; ?>.value;
                var maxmarksValue = maxmarks<?php echo $subject['id']; ?>.value;

                if (minmarksValue === "" || maxmarksValue === "") {
                    errorElementMinmarks<?php echo $subject['id']; ?>.textContent = 'minmarks are required.';
                    errorElementMaxmarks<?php echo $subject['id']; ?>.textContent = 'maxmarks are required.';

                } else {
                    errorElementMinmarks<?php echo $subject['id']; ?>.textContent = '';
                    errorElementMaxmarks<?php echo $subject['id']; ?>.textContent = '';
                }
            } else {
                errorElementMinmarks<?php echo $subject['id']; ?>.textContent = '';
                errorElementMaxmarks<?php echo $subject['id']; ?>.textContent = '';
            }

            
        });
        <?php
    }
    ?>
</script>




