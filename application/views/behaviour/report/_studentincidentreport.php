<div class="col-md-7">
    <div class="scroll-area-inside">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><?php echo $this->lang->line('incident'); ?></th>
                    <th><?php echo $this->lang->line('assign_incident'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($incidentgraph as $value) { ?>
                    <tr>
                        <td><?php echo $value['title']; ?></td>
                        <td><a href="#" class="btn btn-default btn-xs details" data-toggle="tooltip" data-id="<?php echo $value['id']; ?>" title="" data-original-title="<?php echo $this->lang->line('view'); ?>"><?php echo $value['total_student']; ?></a>
                            </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="col-md-5">
    <div class="myChartDiv">
        <canvas id="doughnuts-chart"></canvas>
    </div>
</div>