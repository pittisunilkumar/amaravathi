<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$language = $this->customlib->getLanguage();
$language_name = $language["short_code"];
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('admin/multibranch/finance/_report'); ?>
        <div class="row">        
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"> <?php echo $this->lang->line('expense_list'); ?></h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                       
                        <div class="table-responsive mailbox-messages">
                                 <table class="table table-striped table-bordered table-hover expense-list" data-export-title="<?php echo $this->lang->line('expense_list'); ?>">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('branch'); ?></th>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('description'); ?></th>
                                        <th><?php echo $this->lang->line('invoice_no'); ?></th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('expense_head'); ?></th>
                                        <th><?php echo $this->lang->line('amount'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->

        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script>
    ( function ( $ ) {
    'use strict';
    $(document).ready(function () {
        initDatatable('expense-list','admin/multibranch/finance/dtexpenselist',[],[],100);
    });
} ( jQuery ) )
</script>