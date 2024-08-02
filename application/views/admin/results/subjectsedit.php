<div class="content-wrapper">  
    <section class="content-header">
        <h1>
            <i class="fa fa-user-plus"></i> <?php echo $this->lang->line('student_information'); ?> <small><?php echo $this->lang->line('class1'); ?></small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php
            if ($this->rbac->hasPrivilege('student_categories', 'can_add') || $this->rbac->hasPrivilege('student_categories', 'can_edit')) {
                ?>
                <div class="col-md-4">              
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $this->lang->line('edit_subject'); ?></h3>
                        </div>  
                        <form action="<?php echo site_url("admin/results/subjects/edit/" . $id) ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">   
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('subject_name'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="category" name="category" placeholder="" type="text" class="form-control"  value="<?php echo set_value('category', $category['examtype']); ?>" />
                                    <span class="text-danger"><?php echo form_error('category'); ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('subject_code'); ?></label><small class="req"> *</small>
                                    <input autofocus="" id="subjectcode" name="subjectcode" placeholder="" type="text" class="form-control"  value="<?php echo set_value('subjectcode', $category['subject_code']); ?>" />
                                    <span class="text-danger"><?php echo form_error('subjectcode'); ?></span>
                                </div>
                            </div>
                            <div class="box-footer">                          
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>              
                </div>
            <?php } ?>
            <div class="col-md-<?php
                if ($this->rbac->hasPrivilege('student_categories', 'can_add') || $this->rbac->hasPrivilege('student_categories', 'can_edit')) {
                    echo "8";
                } else {
                    echo "12";
                }
                ?>">               
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('subject_list'); ?></h3>                     
                    </div>
                    <div class="box-body">
                        <div class="download_label"><?php echo $this->lang->line('subject_list'); ?></div>
                        <div class="mailbox-messages table-responsive overflow-visible">
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th align="left"><?php echo $this->lang->line('subject'); ?></th>
                                        <th><?php echo $this->lang->line('subject_code'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($categorylist as $category) {
                                        ?>
                                        <tr>                                         
                                            <td class="mailbox-name"><?php echo $category['examtype'] ?></td>
                                            <td class="mailbox-name"><?php echo $category['subject_code'] ?></td>
                                            <td align="right" class="mailbox-date">
                                                <?php
                                                if ($this->rbac->hasPrivilege('student_categories', 'can_edit')) {
                                                    ?>
                                                    <a href="<?php echo base_url(); ?>admin/results/subjects/edit/<?php echo $category['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                <?php } ?>
                                                <?php
                                                if ($this->rbac->hasPrivilege('student_categories', 'can_delete')) {
                                                    ?>
                                                    <a href="<?php echo base_url(); ?>admin/results/subjects/delete/<?php echo $category['id'] ?>"class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                        <i class="fa fa-remove"></i>
                                                    </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </section>
</div>


