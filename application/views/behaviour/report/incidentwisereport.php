<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/behaviour_addon.css">
<div class="content-wrapper">
       <section class="content">
        <?php $this->load->view('behaviour/report/_behaviour_report'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">
                        <form  action="<?php echo site_url('behaviour/report/incidentwisereport') ?>" method="post">
                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('session'); ?></label>
                                            <select id="session_type" name="session_type" class="form-control">
                                                <option value="current_session" <?php if($session_type == 'current_session'){ echo "selected"; }?>  ><?php echo $this->lang->line('current_session_points'); ?></option>                                               
                                                
                                                <option value="overall" <?php if($session_type == 'overall'){ echo "selected"; }?> ><?php echo $this->lang->line('all_session_points'); ?></option>
                                            </select>
                                            <span class="text-danger"><?php echo form_error('session_type'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-sm pull-right" name="search" data-loading-text="<?php echo $this->lang->line('please_wait'); ?>" value="search"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>                           
                        </div>
                    </form>
                    </div>

                    <div class="">
                        <div class="box-header ptbnull"></div>
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('incident_wise_report'); ?></h3>
                            <div class="box-tools pull-right"></div>
                        </div>
                        <div class="box-body">
                            <div class="mailbox-messages table-responsive overflow-visible">
                                <?php if(!empty($incidentgraph)){ ?>
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="scroll-area-inside">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo $this->lang->line('incident'); ?></th>
                                                        <th><?php echo $this->lang->line('students'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($incidentgraph as $value) { ?>
                                                        <tr>
                                                            <td><?php echo $value['title']; ?></td>
                                                            <td><a href="#" class="btn btn-default btn-xs details text-aqua wise-hover-type" data-toggle="tooltip" data-id="<?php echo $value['id']; ?>" title="" data-original-title="<?php echo $this->lang->line('view'); ?>"><?php echo $value['total_student']; ?></a>
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
                                </div>
                            <?php }else{ ?>
                                    <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                            <?php } ?>
                            </div>
                        </div> 
                    </div>  
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="detailsmodel" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-media-content">
            <div class="modal-header modal-media-header d-flex justify-content-between">
                <div>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="box-title"><?php echo $this->lang->line('assigned_student_list'); ?></h4>
                </div>
                <div class="">    
                    <a class="btn btn-default btn-xs pull-right mr-1" id="print" title="<?php echo $this->lang->line('print'); ?>" onclick="printDiv()" ><i class="fa fa-print"></i></a>
                    <a class="btn btn-default btn-xs pull-right" id="btnExport" title="<?php echo $this->lang->line('excel'); ?>" onclick="fnExcelReport();"> <i class="fa fa-file-excel-o"></i> </a>
                </div>
            </div>
            <div class="modal-body pt0 pb0 relative">
                <div class="box-body">
                    <div class="scroll-area-inside">
                        <div class="mailbox-messages table-responsive" id="assignincident">
                            <div id="detailsdata"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script>
(function ($) {
  "use strict";
    new Chart(document.getElementById("doughnuts-chart"), {
        type: 'pie',
        data: {
          labels: [<?php foreach ($incidentgraph as $value) {?>"<?php echo $value['title']; ?>", <?php }?>],
          datasets: [{
            backgroundColor: ['#52d726', '#f93939', '#36a2eb', '#ff9f40', '#CD5C5C', '#FFBF00', '#FF7F50', '#DE3163', '#9FE2BF', '#40E0D0', '#6495ED', '#CCCCFF', '#808000', '#00FF00', '#008000', '#008080', '#0000FF', '#000080', '#FF00FF', '#800080', '#52d726', '#f93939', '#36a2eb', '#ff9f40', '#CD5C5C', '#FFBF00', '#FF7F50', '#DE3163', '#9FE2BF', '#40E0D0', '#6495ED', '#CCCCFF', '#808000', '#00FF00', '#008000', '#008080', '#0000FF', '#000080', '#FF00FF', '#800080'],
            data: [<?php foreach ($incidentgraph as $value) {?>"<?php echo $value['total_student']; ?>", <?php }?>]
          }]
        },
        options: {
          title: {
            display: true,
            text: "<?php echo $this->lang->line('incident_performance'); ?>"
          }
        }
    });
})(jQuery);
</script>
<script>
(function ($) {
  "use strict";
    $(document).on('click', '.details', function(){
        $('#detailsmodel').modal({
            backdrop: 'static',
            keyboard: false
        });

        var incident_id = $(this).attr('data-id');
        var session_type = $('#session_type').val();
        
        $.ajax({
            url:'<?php echo base_url(); ?>behaviour/report/studentdetails',
            method:'post',
            data:{incident_id:incident_id,session_type:session_type,},
            dataType:'JSON',
            beforeSend: function () {
              $('#detailsdata').html('<center><?php echo $this->lang->line('loading'); ?>  <i class="fa fa-spinner fa-spin"></i></center>');
            },
            success:function(response){
                $('#detailsdata').html(response.page);
            }
        })
    })
})(jQuery);
</script>
<script>
function printDiv() {

    $("#visible").removeClass("hide");
    $(".download_label").addClass("hide");

    document.getElementById("print").style.display = "none";
    document.getElementById("btnExport").style.display = "none";
    var divElements = document.getElementById('assignincident').innerHTML;
    var oldPage = document.body.innerHTML;
    document.body.innerHTML =
            "<html><head><title></title></head><body>" +
            divElements + "</body>";
    window.print();
    document.body.innerHTML = oldPage;

    location.reload(true);
}

function fnExcelReport()
{
    var tab_text = "<table border='2px'><tr >";
    var textRange;
    var j = 0;
    tab = document.getElementById('assignincidentexcell'); // id of table

    for (j = 0; j < tab.rows.length; j++)
    {
        tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
    }
    $("#visible").removeClass("hide");

    tab_text = tab_text + "</table>";
    tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
    tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");
    $("#visible").addClass("hide");
    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
    {
        txtArea1.document.open("txt/html", "replace");
        txtArea1.document.write(tab_text);
        txtArea1.document.close();
        txtArea1.focus();
        sa = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
    } else                 //other browser not tested on IE 11
        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

    return (sa);
}
</script>