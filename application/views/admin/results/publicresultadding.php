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
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('adding_external_results'); ?></h3>
                    </div>


                    <div class="box-body">
                        <div class="row">

                            <form role="form" action="<?php echo site_url('admin/results/addpublicresult/search') ?>" method="post" class="">
                                <?php echo $this->customlib->getCSRF(); ?>

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
                                        <label><?php echo $this->lang->line('section'); ?></label>
                                        <select  id="section_id" name="section_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                    </div>   
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('internal_result_type'); ?></label>
                                        <select  id="result_id" name="result_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($resulttypelist as $resulttype) {
                                                ?>
                                                <option value="<?php echo $resulttype['id'] ?>"<?php
                                                if (set_value('result_id') == $resulttype['id']) {
                                                    echo "selected =selected";
                                                }
                                                ?>><?php echo $resulttype['examtype'] ?></option>

                                                <?php
                                                $count++;
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('result_id'); ?></span>
                                    </div>   
                                </div>
                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('resultadding'); ?></label><small class="req">*</small>
                                        <select class="form-control" name="progress_id" id="progress_id">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php
                                                    foreach ($progresslist as $key => $value) {
                                                        ?>
                                                    <option value="<?php echo $key; ?>" 
                                                        <?php
                                                            if (set_value('progress_id') == $key) {echo "selected";}
                                                        ?>>
                                                        <?php echo $value; ?>
                                                    </option>
                                                <?php 
                                            }
                                            ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('progress_id'); ?></span>
                            
                                
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


                                                        <?php if($status) {?>
                                                            <td>

                                                               
                                                               
                                                                <a class='btn btn-info btn-xs admi_no_view' onclick="resultview('<?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>','<?php echo $student['id']; ?>')" data-toggle="modal" data-target="#myAdmiviewModal" data-student_id="<?php echo $student['id']; ?>" data-student_name="<?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>" ><i class='fa fa-reorder'></i></a>

                                                                <a class='btn btn-info btn-xs admi_no_edit' onclick="resultediting('<?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>','<?php echo $student['id']; ?>')" data-toggle="modal" data-target="#myAdmiEditModal" data-student_id="<?php echo $student['id']; ?>" data-student_name="<?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>"><i class='fa fa-pencil'></i></a>
        
                                                            </td>
                                                        <?php }else {?>
                                                            <td>

                                                                <!-- <a class='btn btn-info btn-xs admi_no_btn' onclick="add()" data-target="#myAdmiEditModal" data-student_id="<?php echo $student['id']; ?>" data-student_name="<?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>"><?php echo $this->lang->line('add_admi_no');?></i></a> -->

                                                                <a class='btn btn-info btn-xs admi_no_btn' onclick="add('<?php echo $this->customlib->getFullName($student['firstname'],$student['middlename'],$student['lastname'],$sch_setting->middlename,$sch_setting->lastname); ?>','<?php echo $student['id']; ?>')" data-target="#myAdmiEditModal" data-student_id="<?php echo $student['id']; ?>"><?php echo $this->lang->line('add_results');?></i></a>

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




<div id="add" class="modal fade " role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" >&times;</button>
                <h4 class="modal-title" id="modal-title" ></h4>
            </div>
            

            
            <form id="form1" name="employeeform" method="post" accept-charset="utf-8">
                <div class="">
                    <div class="">
                        <div class="modal-body">

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
                                <input type ="hidden" name="student_id" id="student_id">
                                <input type ="hidden" name="resulttype_id" id="resulttype_id" value="">
                                <!-- <div id="delete_ides"></div> -->

                                <!-- <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('route_list'); ?></label><small class="req"> *</small>
                                    <input type="hidden" name="action_type" id="action_type">
                                </div> -->
                               
                                <div id="pickuppoint_result"></div>
                                </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white relative z-index-1 bordertoplightgray">
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('saving') ?>" class="btn btn-info pull-right" id="submit" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait') ?>"><?php echo $this->lang->line('save') ?></button>
                </div>
            </form>
           </div>
       </div>
    </div>
</div>


<div id="resultview" class="modal fade " role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" >&times;</button>
                <h4 class="modal-title" id="modal-titlee" ></h4>
            </div>
            

            
                <div class="">
                    <div class="">
                        <div class="modal-body">

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
                                <input type ="hidden" name="student_idd" id="student_idd">
                                <input type ="hidden" name="resulttype_idd" id="resulttype_idd" value="">
                                <!-- <div id="delete_ides"></div> -->

                                <!-- <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('route_list'); ?></label><small class="req"> *</small>
                                    <input type="hidden" name="action_type" id="action_type">
                                </div> -->
                               
                                <div id="viewpointresult"></div>
                                </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white relative z-index-1 bordertoplightgray">
                    
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('cancel'); ?></button>

                    <!-- <button type="submit" data-loading-text="<?php echo $this->lang->line('saving') ?>" class="btn btn-info pull-right" id="submit" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait') ?>"><?php echo $this->lang->line('save') ?></button> -->
                </div>
           </div>
       </div>
    </div>
</div>



<div id="resultedit" class="modal fade " role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" >&times;</button>
                <h4 class="modal-title" id="modal-titleee" ></h4>
            </div>
            

            
            <form id="form2" name="employeeform" method="post" accept-charset="utf-8">
                <div class="">
                    <div class="">
                        <div class="modal-body">

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
                                <input type ="hidden" name="student_iddd" id="student_iddd">
                                <input type ="hidden" name="resulttype_iddd" id="resulttype_iddd" value="">
                                <!-- <div id="delete_ides"></div> -->

                                <!-- <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('route_list'); ?></label><small class="req"> *</small>
                                    <input type="hidden" name="action_type" id="action_type">
                                </div> -->
                               
                                <div id="resulteditt"></div>
                                </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-white relative z-index-1 bordertoplightgray">
                    <button type="submit" data-loading-text="<?php echo $this->lang->line('saving') ?>" class="btn btn-info pull-right" id="submit" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('please_wait') ?>"><?php echo $this->lang->line('save') ?></button>
                </div>
            </form>
           </div>
       </div>
    </div>
</div>


<script type="text/javascript">

    
    function resultediting(studentName,stid){

        $('#action_type').val('add');
        $('#student_iddd').val(stid);
        $('#resulttype_iddd').val(<?php echo $result_id;?>);
        // $('#delete_ides').html('');
        $('#resultedit').modal('show');
        $('#route_id').val('');
        $('#modal-titleee').html(studentName);
        // $('#modal-title').html('<?php echo $this->lang->line('add') ?>');
        $('#resulteditt').html('');
        add_result_edit(stid);
    }

    function add_result_edit(stid){

        var subjectsData = <?php echo json_encode($subjects); ?>;
        var resid = <?php echo $result_id;?>

        for (var i = 0; i < subjectsData.length; i++) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/results/addpublicresult/resulteditdata',
                type: "POST",
                data:{delete_string:makeid(8),
                    subjectsData:subjectsData[i],
                    resulttypeid:resid,
                    stid:stid,
                    },
                dataType: 'json',
                beforeSend: function() {
                },
                success: function(res) {
                $('#resulteditt').append(res);
                },
                error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function() {
            }
            });

        }

    }


    function resultview(studentName,stid){

        $('#action_type').val('add');
        $('#student_idd').val(stid);
        $('#resulttype_idd').val(<?php echo $result_id;?>);
        // $('#delete_ides').html('');
        $('#resultview').modal('show');
        $('#route_id').val('');
        $('#modal-titlee').html(studentName);
        // $('#modal-title').html('<?php echo $this->lang->line('add') ?>');
        $('#viewpointresult').html('');
        viewpiont(stid);
    }

    function viewpiont(stid){

        // var subjectsData = <?php echo json_encode($subjects); ?>;
        var subjectsData = <?php echo $subjects; ?>;
        var resid = <?php echo $result_id;?>

        // for (var i = 0; i < subjectsData.length; i++) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/results/addpublicresult/viewpionts',
                type: "POST",
                data:{
                    resulttype_id:resid,
                    subjectidd:stid,
                },
                dataType: 'json',
                beforeSend: function() {
                },
                success: function(res) {
                $('#viewpointresult').append(res);
                },
                error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function() {
            }
            });

        // }

    }


    function refreshpage(){
        window.location.reload(true);
    }

    function makeid(length) {
        var result = '';
        var characters = '0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    function add(studentName,stid){

        $('#action_type').val('add');
        $('#student_id').val(stid);
        $('#resulttype_id').val(<?php echo $result_id;?>);
        // $('#delete_ides').html('');
        $('#add').modal('show');
        $('#route_id').val('');
        $('#modal-title').html(studentName);
        // $('#modal-title').html('<?php echo $this->lang->line('add') ?>');
        $('#pickuppoint_result').html('');
        add_pickuppoint();

    }

    function add_pickuppoint(){

        var subjectsData = <?php echo json_encode($subjects); ?>;
        var resid = <?php echo $result_id;?>

        for (var i = 0; i < subjectsData.length; i++) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/results/addpublicresult/addmore_point',
                type: "POST",
                data:{delete_string:makeid(8),
                      subjectsData:subjectsData[i],
                    },
                dataType: 'json',
                 beforeSend: function() {
                },
                success: function(res) {
                   $('#pickuppoint_result').append(res);
                },
                error: function(xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
            },
            complete: function() {
            }
            });

        }

    }

    $("#form1").on('submit', (function (e) {
        e.preventDefault();

        var $this = $(this).find("button[type=submit]:focus");
        var inps = document.getElementsByName('lessons[]');
        $.ajax({
            url: base_url+"admin/results/addpublicresult/create",
            type: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,

            beforeSend: function () {
                $this.button('loading');
            },
            success: function (res)
            {
                if (res.status == "fail") {
                    var message = "";
                    $.each(res.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(res.message);
                    window.location.reload(true);
                }
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.button('reset');
            },
            complete: function () {
                $this.button('reset');
            }

        });
    }));

    $("#form2").on('submit', (function (e) {
        e.preventDefault();

        var $this = $(this).find("button[type=submit]:focus");
        var inps = document.getElementsByName('lessons[]');
        $.ajax({
            url: base_url+"admin/results/addpublicresult/update",
            type: "POST",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,

            beforeSend: function () {
                $this.button('loading');
            },
            success: function (res)
            {
                if (res.status == "fail") {
                    var message = "";
                    $.each(res.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                    successMsg(res.message);
                    window.location.reload(true);
                }
            },
            error: function (xhr) { // if error occured
                alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");
                $this.button('reset');
            },
            complete: function () {
                $this.button('reset');
            }

        });
    }));


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




