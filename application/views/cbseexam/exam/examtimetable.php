<div class="content-wrapper"> 
    <!-- Main content -->
    <section class="content">
        <div class="row">           
            <div class="col-md-12">             
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><?php echo $this->lang->line('exam_schedule'); ?></h3>  
                                   
                    </div>
                    <div class="box-body">                        
                     
                    <?php
                    $i=0;
                                if (!empty($exams)) {

                                    
                                    foreach ($exams as $exam_key => $exam_value) {
                                        ?>
                                        
                                    <a class="btn btn-default btn-xs pull-right mt8 mr-1" id="print" onclick="printDiv('print_<?php echo $i;?>')"><i class="fa fa-print"></i></a>
                                    
                                <div id="print_<?php echo $i;?>">
                                    
                                    
                                        <h4 class="pagetitleh2 border-b-none">
                                            <?php echo $exam_value->name; ?>
                                        </h4>
                                        <?php

                                        if (!empty($exam_value->time_table)) {
                                            ?>
                                           
                                        <div class="table-responsive">    
                                            <table class="table table-hover table-bordered table-stripped table-b">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo $this->lang->line('subject'); ?></th>
                                                        <th class="text text-center"><?php echo $this->lang->line('date'); ?> </th>
                                                        <th class="text text-center"><?php echo $this->lang->line('start_time'); ?> </th>
                                                        <th class="text text-center"><?php echo $this->lang->line('duration_minute'); ?> </th>
                                                        <th class="text text-center"><?php echo $this->lang->line('room_no'); ?> </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($exam_value->time_table as $time_table_key => $time_table_value) {

                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?php echo $time_table_value->subject_name . " (" . $time_table_value->subject_code . ")" ?>
                                                            </td>
                                                            <td class="text text-center">
                                                                <?php echo $this->customlib->dateformat($time_table_value->date); ?>
                                                            </td>
                                                            <td class="text text-center">
                                                                <?php echo $time_table_value->time_from; ?>
                                                            </td>
                                                            <td class="text text-center">
                                                                <?php echo $time_table_value->duration; ?>
                                                            </td>
                                                            <td class="text text-center">
                                                                <?php echo $time_table_value->room_no; ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>    
                                            <?php


                                        }
?></div><?php
$i++;
                                    }

                                }else{
                                    ?>
                                    <div class="alert alert-danger">
                                        <?php echo $this->lang->line('no_record_found'); ?>
                                    </div>
                                    <?php
                                }

                                ?>

                    </div>
                </div>
            </div> 
        </div> 
    </section>
</div>


<script type="text/javascript">
  

    function printDiv(tagid) {
        let hashid = "#"+ tagid;
//             var tagname =  $(hashid).prop("tagName").toLowerCase() ;
//             var attributes = ""; 
//             var attrs = document.getElementById(tagid).attributes;
//               $.each(attrs,function(i,elem){
//                 attributes +=  " "+  elem.name+" ='"+elem.value+"' " ;
//               })
//             var divToPrint= $(hashid).html() ;
//             var head = "<html><head>"+ $("head").html() + "</head>" ;
//             var allcontent = head + "<body  onload='window.print()' >"+ "<" + tagname + attributes + ">" +  divToPrint + "</" + tagname + ">" +  "</body></html>"  ;
//             var newWin=window.open('','Print-Window');
//             newWin.document.open();
//             newWin.document.write(allcontent);
//             newWin.document.close();
         
// //   setTimeout(function(){newWin.close();},10);
// setTimeout(function(){newWin.close();},10);
var tagname =  $(hashid).prop("tagName").toLowerCase() ;
            var attributes = ""; 
            var attrs = document.getElementById(tagid).attributes;
              $.each(attrs,function(i,elem){
                attributes +=  " "+  elem.name+" ='"+elem.value+"' " ;
              })
            var divToPrint= $(hashid).html() ;
            var head = "<html><head>"+ $("head").html() + "</head>" ;
            var allcontent = head + "<body  onload='window.print()' >"+ "<" + tagname + attributes + ">" +  divToPrint + "</" + tagname + ">" +  "</body></html>"  ;


var allcontent = head + "<body>"+ "<" + tagname + attributes + ">" +  divToPrint + "</" + tagname + ">" +  "</body></html>"  ;
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        frame1.css({ "position": "absolute", "top": "-1000000px" });
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
   
        frameDoc.document.write(allcontent);
 
        frameDoc.document.close();
        setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);



    }
</script>