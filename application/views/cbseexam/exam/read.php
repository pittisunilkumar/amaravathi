<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.js"></script>
<?php $this->load->view('layout/cbseexam_css.php'); ?>
<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
        <div class="row">           
            <div class="col-md-12">             
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('exam'); ?></h3>                                   
                    </div>
                    <div class="box-body">
                        <div class="row mb10">
                
                           <div class="col-lg-3 col-md-3 col-sm-12">
                             <b> Exam</b> :<?php echo $result['name'] ?>
                            </div>
                           
                        </div>
                        <div class="questiondetail"><b>Description:</b>
                            <span>
                                <?php echo $result['description']; ?>
                            </span>                         
                        </div>
                    </div>
                </div>
            </div> 
        </div> 
    </section>
</div>