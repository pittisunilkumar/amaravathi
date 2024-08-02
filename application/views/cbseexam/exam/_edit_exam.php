<input type="hidden" name="exam_id" value="<?php echo $result['id']; ?>">
<div id="<?php echo $delete_string;?>">  
    <div class="row">
         <div class="col-md-12">
            <div class="form-group" >
                <label for="exampleInputEmail1"><?php echo $this->lang->line('name'); ?></label> <small class="req"> *</small>
               <input class="form-control" value="<?php echo $result['name']?>" name="exam_name" />   
            </div>
         </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
            <div class="checkbox-inline">
                <label>
                    <input type="checkbox" value="1" name="is_active" <?php if($result['is_active']){ echo 'checked'; } ?>> <?php echo $this->lang->line('publish'); ?>
                </label>
            </div>
        </div>
             <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
            <div class="checkbox-inline">
                <label>
                    <input type="checkbox" value="1" name="is_publish" autocomplete="off" <?php if($result['is_publish']){ echo 'checked'; } ?>> <?php echo $this->lang->line('publish')." ".$this->lang->line('result'); ?>
                </label>
            </div>
        </div> 
    </div>
    <div class="row">
            <div class="col-md-12">
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo $this->lang->line('description'); ?></label>
                <textarea type="text" class="form-control" name="exam_description" cols="115" rows="2"><?php echo $result['description']?></textarea>
            </div>
        </div>
    </div>
</div>
  <hr>
<div class="row">
    <div class="col-md-4 col-lg-4 col-sm-6">
        <div class="form-group">
            <label><?php echo $this->lang->line('term'); ?></label><small class="req"> *</small>
            <select name="exam_term_id" id="e_exam_term_id"  class="form-control" >
                 <option value=""><?php echo $this->lang->line('select')?></option>             

                    <?php 
                             foreach ($term_list as $term_key => $term_value) {
                                  $term_selected = '';
                    if($term_value->id == $result['cbse_term_id']){
                        $term_selected = 'selected';
                    }

                                ?>
                                <option value="<?php echo $term_value->id;?>" <?php echo $term_selected; ?>> <?php echo $term_value->name." (".$term_value->term_code.")";?></option>
                                <?php
                             }
                             ?>

            </select> 
            <input type="hidden" name="action" id="action"  class="form-control">
        </div>
    </div>
    <div class="col-md-4 col-lg-4 col-sm-6">
        <div class="form-group">
            <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
            <select autofocus="" id="class_id" name="class_id" onchange="editSectionByClass(this.value, 0, 'e_sections')"  class="form-control" >
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                    <?php
                    foreach ($classlist as $class) { 
                        $class_selected = '';
                        if($class['id'] == $class_id){
                            $class_selected = 'selected';
                        }
                    ?>
                        <option value="<?php echo $class['id'] ?>" <?php echo $class_selected; ?>><?php echo $class['class'] ?></option>
                    <?php } ?>
            </select>
            <span class="text-danger" id="error_class_id"></span>
        </div>
    </div>
    <div class="col-md-4 col-lg-4 col-sm-6"> 
       <div class="form-group relative">
        <label><?php echo $this->lang->line('section'); ?></label>
        <small class="req"> *</small>
         <div id="checkbox-dropdown-container" class="checkbox-dropdown-container">
            <div class="">
               <div class="custom-select" id="custom-select"><?php echo $this->lang->line('select'); ?></div>               
                <div id="custom-select-option-box" class="custom-select-option-box">
                    <div class="custom-select-option checkbox">
                        <label class="vertical-middle line-h-18">
                            <input  class="custom-select-option-checkbox" type="checkbox"  name="select_all" id="select_all"> <?php echo $this->lang->line('select_all'); ?> 
                        </label> 
                    </div>                  
                </div>
            </div>
          </div>
      <span class="text-danger" id="error_class_id"></span>
    </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4 col-lg-4 col-sm-6">
        <div class="form-group">
            <label><?php echo $this->lang->line('assessment'); ?></label><small class="req"> *</small>
            <select autofocus="" id="searchclassid" name="assessment_id"   class="form-control" >
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                    <?php
                    foreach ($assessment_result as $assessment) {
                        $assessment_selected = '';
                        if($result['cbse_exam_assessment_id'] == $assessment['id']){
                           $assessment_selected = 'selected';
                        }
                        ?>
                        <option value="<?php echo $assessment['id'] ?>" <?php echo $assessment_selected; ?>><?php echo $assessment['name'] ?></option>
                    <?php } ?>
            </select>
            <span class="text-danger" id="error_class_id"></span>
        </div>
    </div>
    <div class="col-md-4 col-lg-4 col-sm-6">
        <div class="form-group">
            <label><?php echo $this->lang->line('grade'); ?></label><small class="req"> *</small>
            <select autofocus="" id="searchclassid" name="grade_id"   class="form-control" >
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                    <?php
                    foreach ($grade_result as $grade) {
                        $grade_selected = '';
                        if($result['cbse_exam_grade_id'] == $grade['id']){
                           $grade_selected = 'selected';
                        }
                    ?>
                        <option value="<?php echo $grade['id'] ?>" <?php echo $grade_selected; ?>><?php echo $grade['name'] ?></option>
                    <?php } ?>
            </select>
            <span class="text-danger" id="error_class_id"></span>
        </div>
    </div>
</div>

<div class="row">
    <div class="modal-footer clearboth mx-nt-lr-15 pb0">
       <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <button type="submit" class="btn btn-primary pull-right" data-loading-text="<?php echo $this->lang->line('submitting') ?>" value=""><?php echo $this->lang->line('save'); ?></button>
    </div>
       </div>
</div>

<script>
(function ($){
    "use stict";
    
    $(document).ready(function(){
        var class_id = $('#class_id').val();
        editSectionByClass(class_id, 'e_sections');
    });

    $("#e_exam_term_id").on('change',function(){
        var term_id = $(this).val();
        var base_url = '<?php echo base_url() ?>';
            if(term_id!=''){
            $.ajax({
                type: "GET",
                url: base_url + "cbseexam/term/get_ClassSectionByTermId/"+term_id,
                dataType: "json",
                beforeSend: function () {
                     
                },
                success: function (data) {
                   
                   $('#class_id').val(data.class_id);
                   editSectionByClass(data.class_id, 'e_sections');
                },
                complete: function () {
                  
                }
            });
            }else{
                $('#class_id').val('');
                $('#e_sections').html('');
            }
    });

})(jQuery);     

    function editSectionByClass(class_id, select_control) {
        var sections_id = '<?php echo $class_section_list; ?>';
        if (class_id != "") {

            var base_url = '<?php echo base_url() ?>';
            var div_data = '';
            $.ajax({
                type: "GET",
                url: baseurl + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                beforeSend: function () {
                    $('#editexamModal .custom-select-option-box').closest('div').find("input[name='select_all']").attr('checked', false);
                    $('#editexamModal .custom-select-option-box').children().not(':first').remove();
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
                        checked:checked,
                        val:obj.id
                    })).append(obj.section));

                    $('.custom-select-option-box').append(s);
                     
                    });
                    
                },
                complete: function () {
                   
                }
            });
        }else{
            $('#sections').html('');
        }
    }

</script>