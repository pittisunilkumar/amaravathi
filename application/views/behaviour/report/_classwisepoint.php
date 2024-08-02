<div class="hide" id="visible"><center><b><?php echo $this->lang->line('assign_incident'); ?></b></center></div>
<div class="download_label"><?php echo $this->lang->line('class_wise_rank_report'); ?></div>
    <table class="table table-striped table-bordered" id="assignincidentexcell">
        <thead>
            <tr class="white-space-nowrap">
                <th><?php echo $this->lang->line('admission_no'); ?></th>
                <th><?php echo $this->lang->line('student'); ?></th>
                <th><?php echo $this->lang->line('class').' ('.$this->lang->line('section').')'; ?></th>     
                <th >
                    <table width="100%" >
                        <tr>
                            <td width="15%" valign="top" class="border0 pb5"><?php echo $this->lang->line('assigned_incident'); ?> </td>
                            <td width="78%" valign="top" class="border0 pb5"><?php echo $this->lang->line('description'); ?> </td>
                            <td width="7%" valign="top" class="border0 pb5 pl-lg-1"> <?php echo $this->lang->line('point'); ?> </td>
                        </tr>
                    </table>
                </th>
                 
            </tr>
        </thead>
        <tbody>
            <?php if (empty($classwisepoint)) { ?>
            <?php } else {
                foreach ($classwisepoint as $classwisepoint_value) { ?>
                    <tr>
                        <td class="white-space-nowrap"> <?php echo $classwisepoint_value['admission_no']; ?></td>
                        <td class="white-space-nowrap"> <?php echo $this->customlib->getFullName($classwisepoint_value['firstname'], $classwisepoint_value['middlename'], $classwisepoint_value['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?></td>
                        <td class="white-space-nowrap"> <?php echo $classwisepoint_value['class'].' ('.$classwisepoint_value['section'].')'; ?></td>
                        <td> 
                            <table width="100%">
                                <?php foreach ($classwisepoint_value['incident'] as $incident_value) {
                                    $pointclass = '';
                                    if($incident_value['point'] < 0){
                                        $pointclass = 'danger';
                                    }
                                 ?>
                                <tr class="<?php echo $pointclass; ?>">
                                    <td width="15%" valign="top" class="border0 pb5"> 
                                        <?php echo $incident_value['title']; ?>
                                    </td>
                                    <td width="78%" valign="top" class="border0 pb5"> 
                                        <?php echo $incident_value['description']; ?>
                                    </td>
                                    <td width="7%" valign="top" class="border0 pb5 pl-lg-1"> 
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
</div>