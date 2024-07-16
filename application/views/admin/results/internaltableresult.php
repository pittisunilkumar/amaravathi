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
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('admi_check'); ?></h3>
                    </div>


                    <div class="box-body">
                        <div class="row">
                            <form role="form" action="<?php echo site_url('admin/results/tableresult/search') ?>" method="post" class="">
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="col-md-3">
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

                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label><?php echo $this->lang->line('exam'); ?></label><small class="req"> *</small>
                                        <select  id="exam_id" name="exam_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('exam_id'); ?></span>
                                    </div>  
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group"> 
                                        <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                                        <select autofocus="" id="class_id" name="class_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($classlist as $class) {
                                                ?>
                                                <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) echo "selected=selected" ?>><?php echo $class['class'] ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                    </div>  
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('section'); ?></label><small class="req"> *</small>
                                        <select  id="section_id" name="section_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
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

                    <?php
                    if (isset($resultlist)) {
                        ?>
                        <div  class="" id="duefee">
                            <div class="box-header ptbnull"></div>   
                            <div class="box-header ptbnull">
                                <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('student_list'); ?></h3>
                            </div>

                            <div class="box-body table-responsive overflow-visible">
                                <div class="download_label"><?php echo $this->lang->line('student_list'); ?></div>

                                <div class="tab-pane active table-responsive no-padding" id="tab_1">
                                    <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                        <thead>
                                            <tr> 
                                                
                                                <?php if (!$adm_auto_insert) { ?>
                                                    <th><?php echo $this->lang->line('admission_no'); ?></th>
                                                <?php } ?>
                                                <th><?php echo $this->lang->line('student_name'); ?></th>
                                                <th><?php echo $this->lang->line('class'); ?></th>
                                                <?php if ($sch_setting->father_name) { ?>
                                                    <th><?php echo $this->lang->line('father_name'); ?></th>
                                                <?php } ?>
                                                <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                                <th><?php echo $this->lang->line('gender'); ?></th>
                                                <?php if ($sch_setting->category) { ?>
                                                    <th><?php echo $this->lang->line('category'); ?></th>
                                                <?php } if ($sch_setting->mobile_no) { ?>
                                                    <th><?php echo $this->lang->line('mobile_number'); ?></th>
                                                <?php } ?>
                                                <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (empty($resultlist)) {
                                                ?>

                                                <?php
                                            } else {
                                                $count = 1;
                                                foreach ($resultlist as $student) {
                                                    ?>
                                                    <tr>
                                                        <!-- <td class="text-center"><input type="hidden" class="checkbox center-block" data-student_id="<?php echo $student['id'] ?>"  name="check" id="check" value="<?php echo $student['id'] ?>">
                                                            <input type="hidden" name="class_id" id="class_id" value="<?php echo $student['class_id'] ?>">
                                                            <input type="hidden" name="id_card_id" id="id_card_id" value="<?php echo $idcardResult[0]->id ?>">
                                                        </td> -->
                                                        
                                                        <?php if (!$adm_auto_insert) { ?>
                                                            <td><?php echo $student['admission_no']; ?></td>
                                                        <?php } ?>
                                                        <td>
                                                            <input type="hidden" name="class_id" id="class_id" value="<?php echo $student['class_id'] ?>">
                                                            <!-- <?php echo $student['id'] ?> -->
                                                            <a href="<?php echo base_url(); ?>student/view/<?php echo $student['id']; ?>"><?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo $student['class'] . "(" . $student['section'] . ")" ?></td>
                                                        <?php if ($sch_setting->father_name) { ?>
                                                            <td><?php echo $student['father_name']; ?></td>
                                                        <?php } ?>
                                                        <td>
                                                            <?php if(!empty($student['dob'])){ echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob'])); } ?>
                                                        </td>
                                                        <td><?php echo $this->lang->line(strtolower($student['gender'])); ?></td>
                                                        <?php if ($sch_setting->category) { ?>
                                                            <td><?php echo $student['category']; ?></td>
                                                        <?php } if ($sch_setting->mobile_no) { ?>
                                                            <td><?php echo $student['mobileno']; ?></td>
                                                        <?php }?>

                                                        <?php if($student['admi_status']) {?>
                                                            <td>
                                                            <!-- $row[] = "<a href=" . site_url('student/view/' . $student->id) . " class='btn btn-default btn-xs'> <i class='fa fa-reorder'></i></a>" . "<a href=" . site_url('student/edit/' . $student->id) . " class='btn btn-default btn-xs'> <i class='fa fa-pencil'></i></a>" ; -->

                                                                <!-- <a href="<?php echo base_url(); ?>student/view/<?php echo $student['id']; ?>"  class='btn btn-info btn-xs'><i class='fa fa-reorder'></i></a> -->
                                                               
                                                               
                                                                <a class='btn btn-info btn-xs admi_no_view' data-toggle="modal" data-target="#myAdmiviewModal" data-student_id="<?php echo $student['id']; ?>" data-student_name="<?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>" ><i class='fa fa-reorder'></i></a>

                                                                <a class='btn btn-info btn-xs admi_no_edit' data-toggle="modal" data-target="#myAdmiEditModal" data-student_id="<?php echo $student['id']; ?>" data-student_name="<?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>"><i class='fa fa-pencil'></i></a>
        
                                                            </td>
                                                        <?php }else {?>
                                                            <td>
                                                            <!-- <a href=" . site_url('studentfee/addfee/' . $student->student_session_id) . "  class='btn btn-info btn-xs'>" .  . "</a> -->

                                                                <a class='btn btn-info btn-xs admi_no_btn' data-toggle="modal" data-target="#myAdmiEditModal" data-student_id="<?php echo $student['id']; ?>" data-student_name="<?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>"><?php echo $this->lang->line('add_results');?></i></a>

                                                            </td>
                                                        <?php }?>
                                                    </tr>
                                                    <?php
                                                    $count++;
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>                                                                           
                            </div>                                                         
                        </div>
                        <?php
                    }
                    ?>



                </div>  
            </div>  
        </div> 
    </section>
</div>
<div class="response"> 
</div>










<script type="text/javascript">
    
    function getexamtype(session_id,exam_id){
        
        $('#exam_id').html("");
        var base_url = '<?php echo base_url() ?>';
        var div_dataa = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type:"GET",
            url:base_url + "admin/results/tableresult/examtype",
            data:{'session_id':session_id},
            dataType:"json",
            success:function(data){
                $.each(data, function (i, obj)
                    {
                        var sel = "";
                    if (exam_id == obj.id) {
                        sel = "selected";
                    }
                    div_dataa += "<option value=" + obj.id + " " + sel + ">" + obj.examtype + "</option>";
                });
                $('#exam_id').append(div_dataa);
            }
        });
        
    }



    $(document).ready(function () {
    // Initially, when the page loads, get exam types based on the selected academic year.
        var session_id = $('#academic_id').val();
        var exam_id = '<?php echo set_value('exam_id') ?>';
        getexamtype(session_id, exam_id);

        // Add a change event handler to the academic year dropdown.
        $(document).on('change', '#academic_id', function (e) {
            $('#exam_id').html(""); // Clear the exam dropdown.
            var session_id = $(this).val();
            getexamtype(session_id, ''); // Pass an empty exam_id for the initial selection.
        });
    });



    // $(document).ready(function (){
    //     var session_id = $('academic_id').val();
    //     var exam_id = '<?php echo set_value('exam_id') ?>';
    //     getexamtype(session_id,exam_id);
    //     $(document).on('change', '#academic_id', function (e) {
    //         $('#exam_id').html("");
    //         var session_id = $(this).val();
    //         var base_url = '<?php echo base_url() ?>';
    //         var div_dataa = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
    //         $.ajax({
    //             type:"GET",
    //             url:base_url + "admin/results/tableresult/examtype",
    //             data:{'session_id':session_id},
    //             dataType:"json",
    //             success:function(data){
    //                 $.each(data, function (i, obj)
    //                 {
                        
    //                     div_dataa += "<option value=" + obj.id + ">" + obj.examtype + "</option>";
    //                 });
    //                 $('#exam_id').append(div_dataa);
    //             }
    //         });
    //     });
    // });



</script>





<script type="text/javascript">



    function getSectionByClass(class_id, section_id) {
        if (class_id != "" && section_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        }
    }




    
    $(document).ready(function () {
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);


        $(document).on('change', '#class_id', function (e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });

        

    });

</script>





<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', '.printSelected', function () {
            var array_to_print = [];
            var classId = $("#class_id").val();
            var idCard = $("#id_card_id").val();
            $.each($("input[name='check']:checked"), function () {
                var studentId = $(this).data('student_id');
                item = {}
                item ["student_id"] = studentId;
                array_to_print.push(item);
            });
            if (array_to_print.length == 0) {
                alert("<?php echo $this->lang->line('no_record_selected'); ?>");
            } else {
                $.ajax({
                    url: '<?php echo site_url("admin/generateidcard/generatemultiple") ?>',
                    type: 'post',
                    dataType: 'JSON',
                    data: {'data': JSON.stringify(array_to_print), 'class_id': classId, 'id_card': idCard, },
                    success: function (response) {

                        Popup(response.page);
                    }
                });
            }
        });
    });
</script>


<script type="text/javascript">

    var base_url = '<?php echo base_url() ?>';
    function Popup(data)
    {

        var frame1 = $('<iframe>', {
           id:  'printDiv',
           name:  'frame1'
        });

        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function () {
        document.getElementById('printDiv').contentWindow.focus();
        document.getElementById('printDiv').contentWindow.print();
            frame1.remove();
        }, 500);

        return true;
    }
</script>

