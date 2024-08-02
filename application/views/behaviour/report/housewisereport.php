<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/behaviour_addon.css">
<div class="content-wrapper">
       <section class="content">
        <?php $this->load->view('behaviour/report/_behaviour_report'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('house_wise_rank_report'); ?></h3>
                    </div>
                    <div class="box-body">
                        <div class="mailbox-messages table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('house_wise_rank_report'); ?></div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('rank'); ?></th>
                                        <th><?php echo $this->lang->line('house'); ?></th>
                                        <th><?php echo $this->lang->line('student'); ?></th>
                                        <th><?php echo $this->lang->line('total_points'); ?></th>
                                        <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($housewise)) { ?>
                                    <?php } else {
                                        $totalpoints =''; $rank =0;
                                        foreach ($housewise as $rank_key => $housewise_value) {
                                            if ($rank_key != 0) {                                
                                                $totalpoints =  $housewise[$rank_key - 1]['totalpoints'] ;
                                            }

                                            if($totalpoints == $housewise_value['totalpoints']){
                                                $rank = $rank;
                                            }else{
                                                $rank = $rank + 1;
                                            }

                                            $pointclass = '';
                                            if($housewise_value['totalpoints'] < 0){
                                                $pointclass = 'danger';
                                            }
                                        ?>
                                            <tr class="<?php echo $pointclass; ?>">
                                                <td class="mailbox-name"> <?php echo $rank; ?></td>
                                                <td class="mailbox-name"> <?php echo $housewise_value['house_name']; ?></td>
                                                <td class="mailbox-name"> <?php echo $housewise_value['total_student']; ?></td>
                                                <td class="mailbox-name"> <?php echo $housewise_value['totalpoints']; ?></td>
                                                <td class="mailbox-date pull-right no-print">
                                                    <a href="#" data-placement="left" data-house-id="<?php echo $housewise_value['school_houses_id']; ?>" data-toggle="modal" data-backdrop="static" class="btn btn-default btn-xs assignstudent" title="" data-original-title="<?php echo $this->lang->line('show'); ?>"><i class="fa fa-reorder"></i></a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table><!-- /.table -->
                        </div>
                    </div>   
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="assignstudentmodel" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-dialogfullwidth" role="document">
        <div class="modal-content modal-media-content mt35">
            <div class="modal-header modal-media-header d-flex justify-content-between">
                <div>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="box-title"><?php echo $this->lang->line('assigned_incident'); ?></h4>
                </div>
                <div class="">    
                    <a class="btn btn-default btn-xs pull-right mr-1" id="print" title="<?php echo $this->lang->line('print'); ?>" onclick="printDiv()" ><i class="fa fa-print"></i></a>
                    <a class="btn btn-default btn-xs pull-right" id="btnExport" title="<?php echo $this->lang->line('excel'); ?>" onclick="fnExcelReport();"> <i class="fa fa-file-excel-o"></i> </a>
                </div>
            </div>

            <div class="modal-body pt0 pb0 pl-1 pr0 relative clearboth">
                <div class="scroll-area-inside">
                    <div class="mailbox-messages table-responsive" id="assignincident">
                        <div id="assignstudentdata"></div>
                    </div>     
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
(function ($) {
  "use strict";
    $(document).on("click",".assignstudent",function() {
        $('#assignstudentmodel').modal({backdrop: "static"});
        var house_id = $(this).attr('data-house-id');
        $.ajax({
            url: '<?php echo base_url(); ?>behaviour/report/housewisepoint',
            method: 'POST',
            data:{house_id:house_id},
            dataType:'JSON',
            beforeSend: function () {
              $('#assignstudentdata').html('<center><?php echo $this->lang->line('loading'); ?>  <i class="fa fa-spinner fa-spin"></i></center>');
            },
            success:function(response){
              $('#assignstudentdata').html(response.page);
            }
        })
    });
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