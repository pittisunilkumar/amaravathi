<div class="hide" id="visible"><center><b><?php echo $this->lang->line('assigned_student_list'); ?></b></center></div>
<div class="download_label"><?php echo $this->lang->line('assigned_student_list'); ?></div>
<table class="table table-striped table-bordered table-hover example" id="assignincidentexcell">
    <thead>
        <tr>
            <th><?php echo $this->lang->line('admission_no'); ?></th>
            <th><?php echo $this->lang->line('student'); ?></th>
            <th><?php echo $this->lang->line('class').' ('.$this->lang->line('section').')'; ?></th>
        </tr>
    </thead>
    <tbody>    
        <?php   
        if(!empty($studentlist)){
            foreach ($studentlist as $studentlist_value) { ?>
                <tr>
                    <td class="mailbox-name"> <?php echo  $studentlist_value['admission_no'] ; ?></td>
                    <td class="mailbox-name"> <?php echo $this->customlib->getFullName($studentlist_value['firstname'], $studentlist_value['middlename'], $studentlist_value['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?></td>
                    <td class="mailbox-name"> <?php echo $studentlist_value['class'].' ('.$studentlist_value['section'].')'; ?></td>
                </tr>
                <?php
            }        
        }else{ ?>
            <tr>
                <td  colspan="3">
                    <div class="alert alert-danger"><?php echo $this->lang->line('no_record_found'); ?></div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table><!-- /.table -->