<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/behaviour_addon.css">

<?php 
  
  foreach ($messagelist as $messagelist_value) {
    $staff_profile_pic = '';
    if($messagelist_value['staff_image'] !=''){
        $staff_profile_pic = 'uploads/staff_images/'.$messagelist_value['staff_image'];
    }else{
        if($messagelist_value['gender'] == 'Male'){
            $staff_profile_pic = 'uploads/staff_images/default_male.jpg';
         }else{
            $staff_profile_pic = 'uploads/staff_images/default_female.jpg';
        } 
    }
    
    $employee_id = '';
    if($messagelist_value['staff_employee_id'] !=''){
        $employee_id = ' ('.$messagelist_value['staff_employee_id'].')';
    }

    $admission_no = '';
    if($messagelist_value['admission_no'] !=''){
        $admission_no = ' ('.$messagelist_value['admission_no'].')';
    }

    $student_profile_pic = '';
    if($messagelist_value['student_image'] !=''){
        $student_profile_pic = $messagelist_value['student_image'];
    }else{
        if($messagelist_value['stud_gender'] == 'Male'){
            $student_profile_pic = 'uploads/student_images/default_male.jpg';
         }else{
            $student_profile_pic = 'uploads/student_images/default_female.jpg';
        } 
    }
    ?>    
        <?php       
        
        if($messagelist_value['type'] == 'staff'){  ?>
            <div class="d-flex justify-content-start mb-3-5">
                <div class="img_cont_msg">
                    <img src="<?php echo base_url(); ?><?php echo $staff_profile_pic; ?>" class="user_img_msg">
                </div>
                <div class="msg_cotainer">
                    <div class="media-title bolds"><?php echo $messagelist_value['staff_name'].' '.$messagelist_value['staff_surname'].$employee_id; ?> - <?php echo $messagelist_value['role_name']; ?></div>
                    <div class=""><?php echo $messagelist_value['comment']; ?></div>
                    <span class="msg_time"><?php echo $this->customlib->dateyyyymmddToDateTimeformat($messagelist_value['created_date'], false); ?></span>
                </div>
            </div>
        <?php }else{ ?>
            <?php if($messagelist_value['student_id'] == $student_id  &&  $messagelist_value['type'] == $role){  ?>
            <div class="d-flex justify-content-end mb-3-5">
                <div class="msg_cotainer_send">                    
                    <div class=""><?php echo $messagelist_value['comment']; ?> </div>
                    <span class="msg_time_send"><?php echo $this->customlib->dateyyyymmddToDateTimeformat($messagelist_value['created_date'], false); ?>
                    <?php if($student_id == $messagelist_value['student_id']){ ?>
                    <a class="d-inline cursor-pointer d-inline-after" onclick = "delete_comment(<?php echo $messagelist_value['id']; ?>, '<?php echo $student_incident_id; ?>')" ><?php echo $this->lang->line('delete'); ?></a>
                    <?php } ?>
                    </span>                    
                </div>
                <div class="img_cont_msg">
                    <img src="<?php echo base_url(); ?><?php echo $student_profile_pic; ?>" class="user_img_msg" />
                </div>
            </div>
            
            <?php }else{ ?>          
            
            <div class="d-flex justify-content-start mb-3-5">
                <div class="img_cont_msg">
                    <img src="<?php echo base_url(); ?><?php echo $student_profile_pic; ?>" class="user_img_msg">
                </div>
                <div class="msg_cotainer">
                    <div class="media-title bolds"> <?php echo $this->customlib->getFullname($messagelist_value['firstname'], $messagelist_value['middlename'], $messagelist_value['lastname'], $sch_setting->middlename, $sch_setting->lastname).$admission_no; ?> - <?php if($messagelist_value['type'] == 'parent'){echo $this->lang->line('guardian'); }else{ echo $this->lang->line($messagelist_value['type']); }; ?> </div>
                    <div class=""><?php echo $messagelist_value['comment']; ?></div>
                    <span class="msg_time"><?php echo $this->customlib->dateyyyymmddToDateTimeformat($messagelist_value['created_date'], false); ?></span>
                </div>
            </div>
            <?php } ?>
        <?php } ?>
<?php }   ?> 