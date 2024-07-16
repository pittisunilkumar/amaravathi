<div class="row">
    <div class="col-md-12">
        <div class="box box-primary border0 mb0 margesection">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i>  <?php echo ('Multi Branch Report'); ?></h3>

            </div>
            <div class="">
                <ul class="reportlists">  
                
                    <?php if ($this->rbac->hasPrivilege('multi_branch_daily_collection_report', 'can_view')) { ?>
                    
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('finance/dailycollectionreport'); ?>"><a href="<?php echo base_url(); ?>admin/multibranch/finance/dailycollectionreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('daily_collection_report'); ?></a></li> 
                        
                    <?php } if ($this->rbac->hasPrivilege('multi_branch_payroll', 'can_view')) { ?>
                    
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('finance/payroll'); ?>"><a href="<?php echo base_url(); ?>admin/multibranch/finance/payroll"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('payroll_report'); ?> </a></li> 
                    
                    <?php } if ($this->rbac->hasPrivilege('multi_branch_income_report', 'can_view')) { ?>
                    
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('finance/incomereport'); ?>"><a href="<?php echo base_url(); ?>admin/multibranch/finance/incomereport"><i class="fa fa-file-text-o"></i>  <?php echo $this->lang->line('income_report'); ?></a></li>
                    
                    <?php } if ($this->rbac->hasPrivilege('multi_branch_expense_report', 'can_view')) { ?>
                    
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('finance/expensereport'); ?>"><a href="<?php echo base_url(); ?>admin/multibranch/finance/expensereport"><i class="fa fa-file-text-o"></i>  <?php echo $this->lang->line('expense_report'); ?></a></li>
                    
                    <?php } if ($this->rbac->hasPrivilege('multi_branch_user_log_report', 'can_view')) { ?>
                    
                        <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('finance/userlogreport'); ?>"><a href="<?php echo base_url(); ?>admin/multibranch/finance/userlogreport"><i class="fa fa-file-text-o"></i>  <?php echo $this->lang->line('user_log_report'); ?></a></li>
                    
                    <?php } ?>                   
                             

                </ul>
            </div>
        </div>
    </div>
</div>