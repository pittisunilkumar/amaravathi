<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-user-plus"></i> <?php echo $this->lang->line('student_information'); ?> <small><?php echo $this->lang->line('student1'); ?></small></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('admin/multibranch/finance/_report'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">
                          <form role="form" action="<?php echo site_url('report/searchreportvalidation') ?>" method="post" class="" id="reportform" >


                        <?php if ($this->session->flashdata('msg')) {?> <div class="alert alert-success">  <?php echo $this->session->flashdata('msg') ?> </div> <?php }?>
                              <div class="row">
                            
                            <div class="col-sm-6 col-md-3" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search') . " " . $this->lang->line('type'); ?><small class="req"> *</small></label>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">

                                        <?php foreach ($searchlist as $key => $search) {
                                            ?>
                                            <option value="<?php echo $key ?>" <?php
                                            if ((isset($search_type)) && ($search_type == $key)) {

                                                echo "selected";
                                            }
                                            ?>><?php echo $search ?></option>
                                                <?php } ?>
                                    </select>
                                    <span class="text-danger" id="error_search_type"></span>
                                </div>
                            </div>
                             <div id='date_result'>

                            </div>

                            </div>
                    </div>
                         <div class="box-footer">
                            <div class="resp">
                                
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search') ?></button>  
                             </div>


                             <div class="nav-tabs-custom border0 navnoshadow">
                     
                  
                        <div class="box-header ptbnull"></div>
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-money"></i> <?php echo $this->lang->line('income_report') ; ?></h3>
                        </div>
                        <div class="box-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('income_report');
                                                $this->customlib->get_postmessage();
                                                ?></div>
                            
                                 <table class="table table-striped table-bordered table-hover income-list" data-export-title="<?php echo $this->lang->line('income_report');
                                                $this->customlib->get_postmessage();   ?>">
                                        <thead>
                                            <tr>
                                               <th><?php echo $this->lang->line('branch'); ?></th>
                                               <th><?php echo $this->lang->line('name'); ?></th>
                                                <th><?php echo $this->lang->line('invoice_no'); ?></th>
                                                <th><?php echo $this->lang->line('income_head'); ?></th>
                                                <th><?php echo $this->lang->line('date'); ?></th>
                                                <th class="text text-right"><?php echo $this->lang->line('amount'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                </table>
                        </div>
                  
                             </div>

             
                                                          </div><!--./box box-primary -->
       
                                                        </div>
                                                        </div>
                                                        </section>
                                                        </div>



<script type="text/javascript">


$(document).ready(function() {
     initDatatable('income-list','admin/multibranch/finance/getincomelistbydate',[],[],100);

});


$(document).on('submit','#reportform',function(e){
    e.preventDefault(); // avoid to execute the actual submit of the form.
    var $this = $(this).find("button[type=submit]:focus");  
    var form = $(this);
    var url = form.attr('action');
    var form_data = form.serializeArray();
    $.ajax({
           url: url,
           type: "POST",
           dataType:'JSON',
           data: form_data, // serializes the form's elements.
              beforeSend: function () {
                $('[id^=error]').html("");
                $this.button('loading');
                
               },
              success: function(response) { // your success handler
                
                if(!response.status){
                    $.each(response.error, function(key, value) {
                    $('#error_' + key).html(value);
                    });
                }else{
                 
           initDatatable('income-list','admin/multibranch/finance/getincomelistbydate',response.params,[],100);
                }
              },
             error: function() { // your error handler
                 $this.button('reset');
             },
             complete: function() {
             $this.button('reset');
             }
         });

});


    
</script>