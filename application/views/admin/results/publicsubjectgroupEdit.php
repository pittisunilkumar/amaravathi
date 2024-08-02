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
                            <h3 class="box-title"><?php echo $this->lang->line('edit_subject_group'); ?></h3>
                        </div><!-- /.box-header -->
                        <form id="form1" action="<?php echo site_url('admin/results/resultsubjectgroup/edit/' . $subjectgroup[0]->id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">

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
                                <input type="hidden" value="<?php echo set_value('id', $subjectgroup[0]->id); ?>" name="id">
                                
                                
                                <!-- <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?></label> <small class="req">*</small>
                                    <input autofocus="" id="name" name="name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('name', $subjectgroup[0]->id); ?>" />
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
                                            if ($subjectgroup[0]->id == $resulttype['id']) {
                                                echo "selected";
                                            }
                                            // if ($stafff['id'] == $staff['id']) {
                                            //     echo "selected";
                                            // }
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
                                                <?php
                                                    ?>
                                                <input type="checkbox" name="subject[]" value="<?php echo $subject['id'] ?>" <?php echo set_checkbox('subject[]', $subject['id'], getSelectedSubjects($subjectgroup, $subject['id']) ? true : false); ?>
                                                       ><?php echo $subject['examtype'] ?>
                                            </label>
                                        </div>


                                        <?php 
                                        $marksdata= $this->publicresultsubjectgroup_model->getmarks($id,$subject['id']);
                                       ?>

                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                                <tr>
                                                <div class="col-md-6"> 
                                                    <div class="form-group">
                                                        <label for="minmarks<?php echo $subject['id'];?>"><?php echo $this->lang->line('min_marks'); ?></label><small class="req">*</small>
                                                        <input autofocus="" id="minmarks<?php echo $subject['id'];?>" name="minmarks<?php echo $subject['id'];?>" placeholder="" type="text" class="form-control" value="<?php echo $marksdata['minmarks']?>" />
                                                        <span class="text-danger error-message" id="error-message-minmarks<?php echo $subject['id']; ?>"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="maxmarks<?php echo $subject['id'];?>"><?php echo $this->lang->line('max_marks'); ?></label><small class="req">*</small>
                                                        <input autofocus="" id="maxmarks<?php echo $subject['id'];?>" name="maxmarks<?php echo $subject['id'];?>" placeholder="" type="text" class="form-control" value="<?php echo $marksdata['maxmarks']?>" />
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
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('subject_group_list'); ?></h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('subject_group_list'); ?></div>
                        <div class="table-responsive mailbox-messages" id="subject_list">
                        
                            <a class="btn btn-default btn-xs pull-right" title="<?php echo $this->lang->line('print'); ?>"  id="print" onclick="printDiv()" ><i class="fa fa-print"></i></a> 
                            <a class="btn btn-default btn-xs pull-right" title="<?php echo $this->lang->line('export'); ?>"  id="btnExport" onclick="fnExcelReport();"> <i class="fa fa-file-excel-o"></i> </a>
                            
                            <table class="table table-striped  table-hover " id="headerTable">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
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
                                                    <?php
                                                    foreach ($subjectgroup->group_subject as $group_subject_key => $group_subject_value) {
                                                        echo "<div>" . $group_subject_value->examtype . " (Min:".$group_subject_value->minmarks.")"."(Max:".$group_subject_value->maxmarks.")". "</div>";
                                                        }
                                                        ?>
                                                </td>
                                                <td class="mailbox-date pull-right no_print">

                                                    <?php
                                                    if ($this->rbac->hasPrivilege('subject_group', 'can_edit')) {
                                                            ?>
                                                        <a href="<?php echo base_url(); ?>admin/results/resultsubjectgroup/edit/<?php echo $subjectgroup->id; ?>" class="btn btn-default btn-xs no_print displayinline"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php }?>
                                                    <?php
                                                    if ($this->rbac->hasPrivilege('subject_group', 'can_delete')) {
                                                            ?>
                                                        <a href="<?php echo base_url(); ?>admin/results/resultsubjectgroup/delete/<?php echo $subjectgroup->id; ?>"class="btn btn-default btn-xs no_print displayinline"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
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

<?php

function getSelectedSubjects($subjectgroup, $find)
{
    if (isset($_POST['subject'])) {
        if (!empty($_POST['subject'])) {
            foreach ($_POST['subject'] as $selected_subject_key => $selected_subject_value) {
                if ($selected_subject_value == $find) {
                    return true;
                }
            }
            return false;
        }
    } else {
        foreach ($subjectgroup[0]->group_subject as $subjetct_key => $subjetct_value) {

            if ($subjetct_value->subject_id == $find) {
                return true;
            }
        }
    }

    return false;
}
?>

<script type="text/javascript">
    var post_section_array = <?php echo json_encode($section_array); ?>;
    $(document).ready(function () {
        var post_class_id = '<?php echo set_value('class_id', $class_id) ?>';
        getSectionByClass(post_class_id, 0);
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });

        $(document).on('change', '#class_id', function (e) {
            var class_id = $(this).val();
            getSectionByClass(class_id, 0);
        });
    });

    function getSectionByClass(class_id, section_array) {
        $('.section_checkbox').html('');
        if (class_id != "" && class_id != 0) {
            var div_data = "";
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                beforeSend: function () {

                },
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        console.log(post_section_array);

                        var check = "";
                        if (jQuery.inArray(obj.id, post_section_array) != -1) {
                            check = "checked";
                        }

                        div_data += "<div class='checkbox'>";
                        div_data += "<label>";
                        div_data += "<input type='checkbox' class='content_available' name='sections[]' value='" + obj.id + "' " + check + ">" + obj.section;
                        div_data += "</label>";
                        div_data += "</div>";

                    });
                    $('.section_checkbox').html(div_data);
                },
                error: function (xhr) { // if error occured
                    alert("Error occured.please try again");

                },
                complete: function () {

                }
            });
        }
    }
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