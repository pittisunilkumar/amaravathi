<div class="hide" id="visible"><center><b><?php echo $this->lang->line('assign_incident'); ?></b></center></div>
<div class="download_label"><?php echo $this->lang->line('class_section_wise_rank_report'); ?></div>
<table class="table table-striped table-bordered example" id="assignincidentexcell">
    <thead>
        <tr class="white-space-nowrap">
            <th><?php echo $this->lang->line('admission_no'); ?></th>
            <th><?php echo $this->lang->line('student'); ?></th>
            <th><?php echo $this->lang->line('class').' ('.$this->lang->line('section').')'; ?></th> 
            <th class="pl-1"><?php echo $this->lang->line('assigned_incident'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($classsectionpoint)) {
            ?>
            <?php
        } else {
            foreach ($classsectionpoint as $classsectionpoint_value) {
                ?>
                <tr>
                    <td class="white-space-nowrap"> <?php echo $classsectionpoint_value['admission_no']; ?></td>
                    <td class="white-space-nowrap"> <?php echo $this->customlib->getFullName($classsectionpoint_value['firstname'], $classsectionpoint_value['middlename'], $classsectionpoint_value['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?></td>
                    <td class="white-space-nowrap"> <?php echo $classsectionpoint_value['class'].' ('.$classsectionpoint_value['section'].')'; ?></td>
                    <td> 
                        <table width="100%">
                            <?php foreach ($classsectionpoint_value['incident'] as $incident_value) { ?>
                            <tr>
                                <td width="15%" valign="top" class="border0 pb5"> 
                                    <?php echo $incident_value['title']; ?>
                                </td>
                                <td width="78%" valign="top" class="border0 pb5"> 
                                    <?php echo $incident_value['description']; ?>
                                </td>
                                <td width="7%" valign="top" class="border0 pb5 text-right"> 
                                    <?php echo $incident_value['point']; ?>
                                </td>                                
                            </tr>
                            <?php  } ?>
                        </table>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table><!-- /.table -->