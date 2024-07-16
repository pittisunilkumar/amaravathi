<?php if (empty($assignstudentlist)) { ?>
                    
<div class="alert alert-danger"><?php echo $this->lang->line('no_record_found'); ?></div>

<?php }else{ ?>

<div class="mailbox-messages">
    <div class="table-responsive">
        <div class="download_label"><?php echo $this->lang->line('admission_enquiry'); ?> <?php echo $this->lang->line('list'); ?></div>
        <table class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('title'); ?></th>
                    <th><?php echo $this->lang->line('point'); ?></th>
                    <th><?php echo $this->lang->line('date'); ?></th>
                    <th><?php echo $this->lang->line('description'); ?></th>
                    <th><?php echo $this->lang->line('assign_by'); ?></th>
                    <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php                
                 
                    foreach ($assignstudentlist as $key => $value) {
                        $staff_id = '';
                        if($value['staff_employee_id'] !=""){
                            $staff_id = ' ('.$value['staff_employee_id'].')';
                        }
                        
                        $pointclass = '';
                        if($value['point'] < 0){
                            $pointclass = 'danger';
                        }
                        ?>
                        <tr class="<?php echo $pointclass; ?>">
                            <td class="mailbox-name"><?php echo $value['title']; ?></td>
                            <td class="mailbox-name"><?php echo $value['point']; ?> </td>
                            <td class="mailbox-name"><?php echo $this->customlib->dateformat($value['created_at']); ?></td>
                            <td class="mailbox-name"><?php echo $value['description']; ?></td>
                            
                            <td class="mailbox-name"><?php                            
                            if($staffrole->id == 7){
                                echo $value['staff_name'].' '.$value['staff_surname'].$staff_id; 
                            }else{
                                if($superadmin_visible == 'disabled' && $value['role_id'] == 7){
                                    echo '';               
                                }else{
                                    echo $value['staff_name'].' '.$value['staff_surname'].$staff_id; 
                                }
                            }                           
                            ?></td>
                            
                            <td class="mailbox-name text-right  ">
                                <?php if($this->rbac->hasPrivilege('behaviour_records_assign_incident', 'can_delete')){ ?>
                                <a href="#" class="btn btn-default btn-xs delete_assign" data-student-id="<?php echo $value['student_id']; ?>" data-id="<?php echo $value['id']; ?>" title="" data-toggle="tooltip" data-original-title="<?php echo $this->lang->line('delete'); ?>"><i class="fa fa-trash"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                    }                
                ?>
            </tbody>
        </table><!-- /.table -->
    </div>  
</div><!-- /.mail-box-messages -->  
<?php } ?>