<div class="form-group">
    <label><?php echo $this->lang->line('template'); ?></label><small class="req"> *</small>
    <input type="text" id="name" name="name" class="form-control" value="<?php echo $result['name']; ?>">
    <input type="hidden" name="templateid" value="<?php echo $result['id']; ?>">
</div> 
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
    <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
    <select autofocus="" id="editclassid" name="class_id" onchange="getSectionByClassedit(this.value, 0, 'edit_sections')"  class="form-control" >
        <option value=""><?php echo $this->lang->line('select'); ?></option>
        <?php
        foreach ($classlist as $class) {
            $selected = '';
            if($selected_class_id == $class['id']){
                $selected = 'selected';
            }
        ?>
            <option value="<?php echo $class['id'] ?>" <?php echo $selected; ?>><?php echo $class['class'] ?></option>
                <?php
        }
        ?>
    </select>
    <span class="text-danger" id="error_class_id"></span>
</div>    

    </div>
    <div class="col-md-4">
           <div class="form-group relative">
        <label><?php echo $this->lang->line('section'); ?></label>
        <small class="req"> *</small>
         <div id="checkbox-dropdown-container">
            <div class="">
               <div class="custom-select" id="custom-select"><?php echo $this->lang->line('select'); ?></div>               
                <div class="custom-select-option-box displaynone" id="custom-select-option-box">
                    <div class="custom-select-option checkbox">
                        <label class="vertical-middle line-h-18">
                            <input  class="custom-select-option-checkbox select_all" type="checkbox"  name="select_all" id="select_all"> <?php echo $this->lang->line('select_all'); ?> 
                        </label> 
                    </div>                  
                </div>
            </div>
          </div>
      <span class="text-danger" id="error_class_id"></span>
    </div>
    </div>
   <div class="col-md-4">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="input-type"><?php echo $this->lang->line('marksheet_type'); ?></label>
                    <div id="input-type" class="row">
                        <div class="col-sm-4">
                            <label class="radio-inline">                               
                                <input name="orientation" class="orientation" id="input-type-student" value="L" type="radio" <?php echo ($result['orientation'] == "L") ? "checked='checked'":"" ?>><?php echo $this->lang->line('landscape'); ?> </label>
                        </div>
                      <div class="col-sm-4">
                          <label class="radio-inline">
                              <input name="orientation" class="orientation" id="input-type-student" value="P" type="radio" <?php echo ($result['orientation'] == "P") ? "checked='checked'":"" ?>><?php echo $this->lang->line('portrait'); ?> </label>
                          </div>                                                    
                      </div>
                  </div>
              </div>
        </div>
</div>
<div class="row">
    <div class="col-md-4">
           <div class="form-group">            
                        <label><?php echo $this->lang->line('school_name') ?></label>
                        <input autofocus="" id="line" value="<?php echo set_value('school_name',$result['school_name']); ?>" name="school_name" placeholder="" type="text" class="form-control" />
                        <span class="text-danger"><?php echo form_error('line'); ?></span>
                    </div> 
    </div>
     <div class="col-md-4">
            <div class="form-group">
                        <label><?php echo $this->lang->line('exam_center'); ?></label>
                        <input autofocus="" id="exam_center" value="<?php echo set_value('exam_center',$result['exam_center']); ?>" name="exam_center" placeholder="" type="text" class="form-control" />
                        <span class="text-danger"><?php echo form_error('exam_center'); ?></span>
                    </div>             
    </div>
     <div class="col-md-4">
         <div class="form-group">
                            <label><?php echo $this->lang->line('printing_date'); ?></label>
                            <input autofocus="" id="date" name="date" value="<?php echo set_value('date',$this->customlib->dateformat($result['date'])); ?>" placeholder="" type="text" class="form-control date" />
                            <span class="text-danger"><?php echo form_error('date'); ?></span>
                        </div> 
    </div>    
</div>         
                     <div class="form-group">
                        <label><?php echo set_value('header'); ?><?php echo $this->lang->line('header_image'); ?> (965px X 150px)</label>
                        <input autofocus="" id="header_image" value="<?php echo set_value('header_image'); ?>" name="header_image" placeholder="" type="file" class="filestyle form-control" data-height="40" />
                        <span class="text-danger"><?php echo form_error('header_image'); ?></span>
                    </div>  
      <div class="form-group">
                        <label><?php echo $this->lang->line('footer_text'); ?></label>
                         <textarea class="form-control" id="ckeditor" name="content_footer"><?php echo $result['content_footer'] ?></textarea>
                        <span class="text-danger"><?php echo form_error('content_footer'); ?></span>
                    </div> 
<div class="form-group">
    <label><?php echo $this->lang->line('left_sign'); ?> (100px X 50px)</label>
    <input id="documents" name="left_sign" placeholder="" type="file" class="filestyle form-control" data-height="40"  name="left_sign">
    <span class="text-danger"><?php echo form_error('left_sign'); ?></span>
</div>
<div class="form-group">
    <label><?php echo $this->lang->line('middle_sign') ?> (100px X 50px)</label>
    <input id="documents" name="middle_sign" placeholder="" type="file" class="filestyle form-control" data-height="40" name="middle_sign">
    <span class="text-danger"><?php echo form_error('middle_sign'); ?></span>
</div>
<div class="form-group">
    <label><?php echo $this->lang->line('right_sign'); ?> (100px X 50px)</label>
    <input id="documents" name="right_sign" placeholder="" type="file" class="filestyle form-control" data-height="40"  name="right_sign">
    <span class="text-danger"><?php echo form_error('right_sign'); ?></span>
</div>
<div class="form-group">
    <label><?php echo $this->lang->line('background_image') ?></label>
    <input id="documents" name="background_img" placeholder="" type="file" class="filestyle form-control" data-height="40" name="background_image">
    <span class="text-danger"><?php echo form_error('background_img'); ?></span>
</div>
<div class="form-group">
    <label><?php echo $this->lang->line('template_description'); ?></label>
     <textarea type="text" name="description" cols="115" rows="3" class="form-control"><?php echo $result['description']; ?></textarea>
</div>
<div class="form-group switch-inline">
    <label><?php echo $this->lang->line('student_name') ?></label>
    <div class="material-switch switchcheck">
        <input id="stu_name" name="stu_name" type="checkbox" class="chk" value="1" <?php if ($result['is_name'] == 1) {echo "checked"; } ?>>
        <label for="stu_name" class="label-success"></label>
    </div>
</div>
<div class="form-group switch-inline">
    <label><?php echo $this->lang->line('father_name') ?></label>
    <div class="material-switch switchcheck">
        <input id="father_name" name="father_name" type="checkbox" class="chk" value="1" <?php if ($result['is_father_name'] == 1) {echo "checked"; } ?>>
        <label for="father_name" class="label-success"></label>
    </div>
</div>
<div class="form-group switch-inline">
    <label><?php echo $this->lang->line('mother_name') ?></label>
    <div class="material-switch switchcheck">
        <input id="mother_name" name="mother_name" type="checkbox" class="chk" value="1" <?php if ($result['is_mother_name'] == 1) {echo "checked"; } ?>>
        <label for="mother_name" class="label-success"></label>
    </div>
</div>
<div class="form-group switch-inline">
    <label><?php echo $this->lang->line('template_academic_session') ?></label>
    <div class="material-switch switchcheck">
        <input id="examsession" name="examsession" type="checkbox" class="chk" value="1" <?php if ($result['exam_session'] == 1) {echo "checked"; } ?>>
        <label for="examsession" class="label-success"></label>
    </div>
</div> 
<div class="form-group switch-inline">
    <label><?php echo $this->lang->line('admission_no') ?></label>
    <div class="material-switch switchcheck">
        <input id="admission_no" name="admission_no" type="checkbox" class="chk" value="1" <?php if ($result['is_admission_no'] == 1) {echo "checked"; } ?>>
        <label for="admission_no" class="label-success"></label>
    </div>
</div>
<div class="form-group switch-inline">
    <label><?php echo $this->lang->line('roll_no'); ?></label>
    <div class="material-switch switchcheck">
        <input id="roll_no" name="roll_no" type="checkbox" class="chk" value="1" <?php if ($result['is_roll_no'] == 1) {echo "checked"; } ?>>
        <label for="roll_no" class="label-success"></label>
    </div>
</div>
<div class="form-group switch-inline">
    <label><?php echo $this->lang->line('photo') ?></label>
    <div class="material-switch switchcheck">
        <input id="photo" name="photo" type="checkbox" class="chk" value="1" <?php if ($result['is_photo'] == 1) {echo "checked"; } ?> >
        <label for="photo" class="label-success"></label>
    </div>
</div>
<div class="form-group switch-inline">
    <label><?php echo $this->lang->line('class') ?></label>
    <div class="material-switch switchcheck">
        <input id="class" name="class" type="checkbox" class="chk" value="1" <?php if ($result['is_class'] == 1) {echo "checked"; } ?>>
        <label for="class" class="label-success"></label>
    </div>
</div>
<div class="form-group switch-inline">
    <label><?php echo $this->lang->line('section') ?></label>
    <div class="material-switch switchcheck">
        <input id="section" name="is_section" type="checkbox" class="chk" value="1" <?php if ($result['is_section'] == 1) {echo "checked"; } ?>>
        <label for="section" class="label-success"></label>
    </div>
</div>
<div class="form-group switch-inline">
    <label><?php echo $this->lang->line('date_of_birth') ?></label>
    <div class="material-switch switchcheck">
        <input id="date_of_birth" name="date_of_birth" type="checkbox" class="chk" value="1" <?php if ($result['is_dob'] == 1) {echo "checked"; } ?>>
        <label for="date_of_birth" class="label-success"></label>
    </div>
</div>
<div class="form-group switch-inline">
    <label><?php echo $this->lang->line('teacher_remark'); ?></label>
    <div class="material-switch switchcheck">         
        <input id="is_remark" name="is_remark" type="checkbox" class="chk" value="1" <?php if ($result['is_remark'] == 1) {echo "checked"; } ?>>
        <label for="is_remark" class="label-success"></label>
    </div>
</div>                        

<script>
(function ($) {
    "use strict"; 
    
    $(document).ready(function(){
        var class_id = $('#editclassid').val();
        getSectionByClassedit(class_id, 0, 'edit_sections');
    });
    
    $('.filestyle').dropify();
    
})(jQuery); 

    function getSectionByClassedit(class_id,section_id, select_control) {
        var sections_id = '<?php echo $selected_section_id; ?>';
        if (class_id != "") {
            $('#' + select_control).html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                beforeSend: function () {
                    $('#editModal .custom-select-option-box').closest('div').find("input[name='select_all']").attr('checked', false);
                    $('#editModal .custom-select-option-box').children().not(':first').remove();
                },
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var checked = false;

                        $.each(JSON.parse(sections_id), function (index, val)
                        {
                            if(obj.id == val.class_section_id){
                                checked = true;
                            }
                        });

                    var s=  $('<div>', {   
                        class: 'custom-select-option checkbox'
                    }).append($('<label>', {   
                        class: 'vertical-middle line-h-18',

                    }).append($('<input />', {   
                        class: 'custom-select-option-checkbox',
                        type: 'checkbox',
                        name:"section[]",
                        val:obj.id,
                        checked:checked
                    })).append(obj.section));

                    $('.custom-select-option-box',$('#editModal .modal-body')).append(s);      
                        
                    });
                   
                },
                complete: function () {
                   
                }
            });
        }else{
            $('#edit_sections').html('');
        }
    }  
    
    
    
    CKEDITOR.replace('ckeditor', {
        toolbar: 'Ques',
        allowedContent: true,
        enterMode: CKEDITOR.ENTER_BR,
        shiftEnterMode: CKEDITOR.ENTER_P,
        customConfig: baseurl + '/backend/js/ckeditor_config.js'
    });   
</script>