<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('class_timetable'); ?></h3>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('class_timetable'); ?></div>

                            <?php
if (!empty($timetable)) {
    ?>
                                <table class="table table-stripped">
                                    <thead>
                                        <tr>
                                            <?php
foreach ($timetable as $tm_key => $tm_value) {
        ?>
                                                <th class="text"><?php echo $this->lang->line(strtolower($tm_key)); ?></th>
                                                <?php
}
    ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <?php
foreach ($timetable as $tm_key => $tm_value) {
        ?>
                                                <td class="text" width="14%">

                                                    <?php
if (!$timetable[$tm_key]) {
            ?>
                                                        <div class="attachment-block block-b-noraml clearfix">
                                                            <b class="text text-danger"><i class="fa fa-times-circle text-danger"></i><?php echo $this->lang->line('not_scheduled'); ?></b>
                                                        </div>
                                                        <?php
} else {
            foreach ($timetable[$tm_key] as $tm_k => $tm_kue) {
                ?>
                                                            <div class="attachment-block attachment-block-normal clearfix">

                                                                <div class="relative attachment-left-space"><i class="fa fa-book"></i><?php echo $this->lang->line('subject') ?>: <?php
echo $tm_kue->subject_name;
                if ($tm_kue->code != '') {
                    echo " (" . $tm_kue->code . ")";
                }
                ?>
                                                                </div>
                                                                <div class="relative attachment-left-space"><i class="fa fa-clock-o"></i><?php echo $tm_kue->time_from ?>
                                                                <b class="text text-center">-</b>
                                                                <strong class="text-green"><?php echo $tm_kue->time_to; ?></strong>
                                                               </div>
                                                                <div class="relative attachment-left-space"><i class="fa fa-building"></i><?php echo $this->lang->line('room_no'); ?>: <?php echo $tm_kue->room_no; ?>
                                                            </div>
                                                            </div>
                                                            <?php
}
        }
        ?>
                                                </td>
                                                <?php
}
    ?>
                                        </tr>
                                    </tbody>
                                </table>
                                <?php
}
?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>