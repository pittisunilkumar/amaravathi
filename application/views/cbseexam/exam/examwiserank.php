<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.js"></script>

<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
        <div class="row">           
            <div class="col-md-12">             
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                    <h3 class="box-title titlefix"><?php echo $this->lang->line('generate_rank'); ?> : <?php echo ($exam['name']); ?></h3>  
                                
                    </div>
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('generate_rank'); ?></div>
                        <?php

if (isset($studentList)) {
if(!empty($studentList)){    
  
     foreach ($studentList as $student_key => $student_value) {
if($student_value->rank != ''){         ?>
    <div class="alert alert-info" role="alert">
<?php echo $this->lang->line('rank_has_already_generated_you_can_update_rank'); ?>
</div>
     <?php break; }}

?>
     <form method="post" action="<?php echo base_url('cbseexam/exam/examrankgenerate') ?>" id="rankgenerate">
         <input type="hidden" name="exam_id" value="<?php echo set_value('exam_id',$exam_id); ?>">
    
   <table class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                     <thead>
                         <tr>
                        
                             <th><?php echo $this->lang->line('admission_no'); ?></th>
                             <th><?php echo $this->lang->line('student_name'); ?></th>
                             <th><?php echo $this->lang->line('class'); ?></th>     
                             <th><?php echo $this->lang->line('father_name'); ?></th>
                             <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                             <th><?php echo $this->lang->line('gender'); ?></th>                                             
                             <th class=""><?php echo $this->lang->line('mobile_no'); ?></th>
                             <th class="text-center"><?php echo $this->lang->line('rank'); ?></th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php
                         if (empty($studentList)) {
                             ?>

                             <?php
                         } else {
                             $count = 1;
                             foreach ($studentList as $student_key => $student_value) {
                             
                                 ?>
                                 <tr>
                                    
                                     <td>  <input type="hidden" name="student_session_id[]" value="<?php echo $student_value->student_session_id?>"/>
                               
                                         <?php echo $student_value->admission_no; ?></td>
                                     <td>
<a href="<?php echo base_url(); ?>student/view/<?php echo $student_value->id; ?>"><?php echo $this->customlib->getFullName($student_value->firstname,$student_value->middlename,$student_value->lastname,$sch_setting->middlename,$sch_setting->lastname); ?>
                                         </a>
                                     </td>
                                     <td><?php echo $student_value->class."(".$student_value->section.")"; ?></td>
                                     <td><?php echo $student_value->father_name; ?></td>
                                     <td><?php 
                                         if (!empty($student_value->dob) && $student_value->dob != '0000-00-00') {
                                         echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student_value->dob)); }?></td>
                                     <td><?php echo $this->lang->line(strtolower($student_value->gender)); ?></td>                  
                                     <td><?php echo $student_value->mobileno; ?></td>
                                     <td class="text-center"><?php echo $student_value->rank;?></td>
                                    
                                 </tr>
                                 <?php
                                 $count++;
                             }
                         }
                         ?>
                     </tbody>
                 </table>
            <div class="col-sm-12">
                <div class="form-group">
                    <button type="submit" name="search"  class="btn btn-primary pull-right btn-sm checkbox-toggle" autocomplete="off"><i class="fa fa-search"></i> <?php echo $this->lang->line('generate_rank'); ?></button>
                </div>
         </div>     
       
     </form>
 </div>
 <?php

}else{
 ?>
 <div class="box-body row">
     <div class="col-md-12">                            
<div class="alert alert-danger">
<?php echo $this->lang->line('no_record_found');?>
</div>
     </div>
 </div>
 <?php
}
}
?>
                    </div>
                </div>
            </div> 
        </div> 
    </section>
</div>


<script type="text/javascript">
 
$(document).on('submit','#rankgenerate',function(e){
   e.preventDefault(); // avoid to execute the actual submit of the form.
    var $this = $(this).find("button[type=submit]:focus");
    var form = $(this);
    var url = form.attr('action');
    var form_data = form.serializeArray();
   
    $.ajax({
           url: url,
           type: "POST",
           dataType:'JSON',
           data: form_data, // serializes the form's elements.
              beforeSend: function () {
                $('[id^=error]').html("");
                $this.button('loading');

               },
              success: function(response) { // your success handler

                if(!response.status){
                    $.each(response.error, function(key, value) {
                    $('#error_' + key).html(value);
                    });
                }else{

                    window.location.reload();

     
                }
              },
             error: function() { // your error handler
                 $this.button('reset');
             },
             complete: function() {
             $this.button('reset');
             }
         });

});
</script>