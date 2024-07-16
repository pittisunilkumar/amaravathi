<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary" id="route">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix pt5"> Multi Branch Setup --r</h3>     
                    </div>
                    <div class="box-body">

               <form class="form-horizontal" action="<?php echo base_url('admin/multibranch/branch/upload'); ?>" Method="POST" enctype="multipart/form-data">
  <div class="form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">form</label>
    <div class="col-sm-10">
      <input type="file" class="form-control" name="uploadedFile" id="inputEmail3" placeholder="Email">
    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
 <input type="submit" name="uploadBtn" value="Upload" />
    </div>
  </div>
</form>

                    </div>
                
                </div>
            </div>
        </div>
    </section>
</div>
