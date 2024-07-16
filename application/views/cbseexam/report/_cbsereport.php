<div class="row">
    <div class="col-md-12">
        <div class="box box-primary border0 mb0 margesection">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i>  <?php echo $this->lang->line('reports') ?></h3>
            </div>
            <div class="">
                <ul class="reportlists">                 

                    <?php if ($this->rbac->hasPrivilege('subject_marks_report', 'can_view')) { ?>                        
                        <li class="col-lg-4 col-md-4 col-sm-6  <?php echo set_SubSubmenu('cbse_exam/examsubject'); ?>"><a href="<?php echo site_url('cbseexam/report/examsubject'); ?>"><i class="fa fa-file-text-o"></i><?php echo $this->lang->line('subject_marks_report'); ?></a></li>                       
                    <?php } ?>                    
                    <?php if ($this->rbac->hasPrivilege('template_marks_report', 'can_view')) { ?>                        
                        <li class="col-lg-4 col-md-4 col-sm-6  <?php echo set_SubSubmenu('cbse_exam/templatewise'); ?>"><a href="<?php echo site_url('cbseexam/report/templatewise'); ?>"><i class="fa fa-file-text-o"></i><?php echo $this->lang->line('template_marks_report'); ?></a></li>                        
                    <?php } ?>
                   
                </ul>
            </div>
        </div> 
    </div>
</div>