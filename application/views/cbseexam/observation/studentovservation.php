<?php $this->load->view('layout/cbseexam_css.php'); ?>
<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <?php echo ($result->cbse_observation_parameter_name);  ?>
                    <?php echo ($result->cbse_term_name);  ?>
                    <form action="<?php echo site_url('cbseexam/observation/studentovservation/'.$id) ?>"  method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <input type="hidden" name="cbse_observation_term_id" value="<?php echo $id; ?>">
                            <div class="row">
                            	 <div class="col-lg-4 col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('exam'); ?></label><small class="req"> *</small>
                                           <select autofocus="" id="exam_id" name="exam_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($exams_by_grade as $exams_by_grade_key => $exams_by_grade_value) {
                                                ?>
                                                <option value="<?php echo $exams_by_grade_value->id ?>" <?php
                                                if (set_value('exam_id') == $exams_by_grade_value->id) {
                                                    echo "selected=selected";
                                                }
                                                ?>><?php echo $exams_by_grade_value->name; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('exam_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                                        <select autofocus="" id="class_id" name="class_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($classlist as $class) {
                                                ?>
                                                <option value="<?php echo $class['id'] ?>" <?php
                                                if (set_value('class_id') == $class['id']) {
                                                    echo "selected=selected";
                                                }
                                                ?>><?php echo $class['class'] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label><small class="req"> *</small>
                                        <select  id="section_id" name="section_id" class="form-control" >
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>

                <?php
                if (isset($students)) {
                    ?>

                    <div class="ptt10">
                      <div class="bordertop">  
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('students'); ?></h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <?php
                                foreach ($students as $student_key => $student_value) {
                                    ?>
                                    <form action="<?php echo site_url('cbseexam/observation/addobservationparam/'); ?>" method="POST"  class="form-horizontal update" >
                                        <div class="col-md-6">
                                            <div class="panel panel-info">
                                                <div class="panel-body">
                                                    <?php
                                                 echo $this->customlib->getFullName($student_value['firstname'],$student_value['middlename'],$student_value['lastname'],$sch_setting->middlename,$sch_setting->lastname)." (".$student_value['admission_no'].")";
                                                    ?>
                                                    <input type="hidden" value="<?php echo $student_value['student_session_id'] ?>" name="student_session_id">                                                   
                                                    <div class="append_row">
<?php 
$row=1;

foreach ($student_value['observation_subparameters'] as $observation_parameter_key => $observation_parameter_value) {

   ?>
   <input type="hidden" name="row[]" value="<?php echo $row; ?>">
   <input type="hidden" name="cbse_ovservation_term_id_<?php echo $row;?>" value="<?php echo $observation_parameter_value['cbse_ovservation_term_id']; ?>">
   <input type="hidden" name="cbse_observation_subparameter_id_<?php echo $row;?>" value="<?php echo $observation_parameter_value['cbse_observation_subparameter_id']; ?>">  
       
         <div class="form-group">
    <label class="control-label col-sm-4" for="email"><?php echo $observation_parameter_value['cbse_observation_parameter_name'] ?>:</label>
    <div class="col-sm-8">
 <input type="text"  class="form-control" name="obtain_marks_<?php echo $row;?>" value=""> 
    </div>
  </div>                                                     

   <?php
   $row++;
}
 ?>
                                                    </div>
                                                </div>
                                                <div class="panel-footer panel-fo">
                                                    <div class="row text-center">
                                                        <div class="col-xs-12 col-xs-offset-0 col-sm-3 col-sm-offset-9">
                                                            <?php if($this->rbac->hasPrivilege('multi_class_student','can_edit')){ ?>
                                                            <button type="submit" class="btn btn-default btn-sm pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Updating...">
                                                                <?php echo $this->lang->line('update'); ?>
                                                            </button>
                                                        <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                  </div>  
                  </div>  
                    <?php
                } else {
                    
                }
                ?>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
    <div class="clearfix"></div>
</div>

<script type="text/javascript">
       $(document).on('change', '#exam_id', function (e) {
            $('#class_id').html("");
            var exam_id = $(this).val();
            getExamByClass(exam_id, 0);
        });

        $(document).on('change', '#class_id', function (e) {
            $('#section_id').html("");
            var exam_id = $('#exam_id').val();
            var class_id = $(this).val();
            getSectionByClass(exam_id,class_id, 0);
        });

        function getExamByClass(exam_id, class_id) {

            if (exam_id != "") {
                $('#class_id').html("");
           
                var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';                

                $.ajax({
                    type: "GET",
                    url: base_url + "cbseexam/observation/getClassByExam",
                    data: {'exam_id': exam_id},
                    dataType: "json",
                    beforeSend: function () {
                        $('#class_id').addClass('dropdownloading');
                    },
                    success: function (data) {
                        $.each(data.classes, function (i, obj)
                        {
                            var sel = "";
                            if (class_id == obj.class_id) {
                                sel = "selected";
                            }
                            div_data += "<option value=" + obj.class_id + " " + sel + ">" + obj.class + "</option>";
                        });
                        $('#class_id').append(div_data);
                    },
                    complete: function () {
                        $('#class_id').removeClass('dropdownloading');
                    }
                });
            }
        }

        function getSectionByClass(exam_id,class_id, section_id) {

            if (class_id != "") {
                $('#section_id').html("");
                var base_url = '<?php echo base_url() ?>';
                var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';            

                $.ajax({
                    type: "GET",
                    url: base_url + "cbseexam/observation/getExamSectionByClass",
                    data: {'exam_id': exam_id,'class_id': class_id},
                    dataType: "json",
                    beforeSend: function () {
                        $('#section_id').addClass('dropdownloading');
                    },
                    success: function (data) {
                        $.each(data.classes, function (i, obj)
                        {
                            var sel = "";
                            if (section_id == obj.section_id) {
                                sel = "selected";
                            }
                            div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                        });
                        $('#section_id').append(div_data);
                    },
                    complete: function () {
                        $('#section_id').removeClass('dropdownloading');
                    }
                });
            }
        }

    $(document).on('submit', '.update', function (e) {
        var submit_btn = $(this).find("button[type=submit]");
        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            dataType: "json",
            beforeSend: function () {
                submit_btn.button('loading');
            },
            success: function (data)
            {

                if (data.status == 1) {

                    successMsg(data.message);
                } else {
                    errorMsg(data.message);
                }
                submit_btn.button('reset');
            },
            error: function (xhr) { // if error occured
                alert("Error occured.please try again");

            },
            complete: function () {
                submit_btn.button('reset');
            }
        });
    });
</script>