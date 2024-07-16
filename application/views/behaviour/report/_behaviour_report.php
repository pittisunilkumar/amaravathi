<div class="row">
    <div class="col-md-12">
        <div class="box box-primary border0 mb0 margesection">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i>  <?php echo $this->lang->line('reports'); ?></h3>
            </div>
            <div class="">
                <ul class="reportlists">
                    <?php if($this->rbac->hasPrivilege('student_incident_report', 'can_view')){ ?>   
                    
                    <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('behaviour/student_incident_report'); ?>"><a href="<?php echo base_url(); ?>behaviour/report/studentincidentreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('student_incident_report'); ?></a></li>  
                    
                    <?php } if($this->rbac->hasPrivilege('student_behaviour_rank_report', 'can_view')){ ?> 
                    
                    <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('behaviour/student_rank_report'); ?>"><a href="<?php echo base_url(); ?>behaviour/report/studentbehaviorsrankreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('student_behaviour_rank_report'); ?></a></li> 
                    
                    <?php } if($this->rbac->hasPrivilege('class_wise_rank_report', 'can_view')){ ?>   
                    
                    <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('behaviour/classwise_rank_report'); ?>"><a href="<?php echo base_url(); ?>behaviour/report/classwiserankreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('class_wise_rank_report'); ?></a></li> 
                    
                    <?php } if($this->rbac->hasPrivilege('class_section_wise_rank_report', 'can_view')){ ?>
                    
                    <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('behaviour/classsectionwise'); ?>"><a href="<?php echo base_url(); ?>behaviour/report/classsectionwiserank"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('class_section_wise_rank_report'); ?></a></li>
                    
                    <?php } if($this->rbac->hasPrivilege('house_wise_rank_report', 'can_view')){ ?>
                    
                    <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('behaviour/housewisereport'); ?>"><a href="<?php echo base_url(); ?>behaviour/report/housewiserank"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('house_wise_rank_report'); ?></a></li>
                    
                    <?php } if($this->rbac->hasPrivilege('incident_wise_report', 'can_view')){ ?>
                    
                    <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('behaviour/incidentwisereport'); ?>"><a href="<?php echo base_url(); ?>behaviour/report/incidentwisereport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('incident_wise_report'); ?></a></li>  
                    
                    <?php } ?>
                </ul>
            </div>
        </div> 
    </div>
</div>