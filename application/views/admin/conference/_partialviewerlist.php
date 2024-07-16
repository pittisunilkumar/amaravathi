<div class="downloadlabel hide" id="downloadlabel"><?php echo $this->lang->line('join') . ' ' . $this->lang->line('list'); ?></div>
<?php
if (!empty($viewerDetail)) {
    if ($type == "staff") {
        ?>
         <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered viewer-list-datatable">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('staff'); ?></th>
                        <th class="pull-right"><?php echo $this->lang->line('last') . ' ' . $this->lang->line('join'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($viewerDetail as $viewer_key => $viewer_value) {
                        ?>
                        <tr>
                            <td> <?php
                                echo $viewer_value->create_for_name . " " . $viewer_value->create_for_surname . " (" . $viewer_value->role_name . " : " . $viewer_value->employee_id . ")";
                                ?></td>
                            <td class="pull-right"><?php echo $this->customlib->dateyyyymmddToDateTimeformat($viewer_value->created_at); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
          </div>  
        <?php
    } elseif ($type == "student") {
        ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered viewer-list-datatable">
                <thead>
                    <tr>
                        <th><?php echo $this->lang->line('admission_no'); ?></th>
                        <th><?php echo $this->lang->line('student_name'); ?></th>
                        <th><?php echo $this->lang->line('father_name'); ?></th>
                        <th><?php echo $this->lang->line('last') . ' ' . $this->lang->line('join'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($viewerDetail as $viewer_key => $viewer_value) {
                       
                        ?>
                        <tr>
                            <td class="tablewidth25"><?php echo $viewer_value->admission_no; ?></td>
                            <td class="tablewidth25"><?php echo $this->customlib->getFullName($viewer_value->firstname,$viewer_value->middlename,$viewer_value->lastname,$sch_setting->middlename,$sch_setting->lastname) ?></td>
                            <td class="tablewidth25"><?php echo $viewer_value->father_name; ?></td>
                            <td ><?php echo $this->customlib->dateyyyymmddToDateTimeformat($viewer_value->created_at); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
         </div>
        <?php
    }
} else {
    ?>
    <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
    <?php
}
?>