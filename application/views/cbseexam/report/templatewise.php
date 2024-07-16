<?php $this->load->view('layout/cbseexam_css.php'); ?>
<div class="content-wrapper">        
    <!-- Main content -->
    <section class="content">
    <?php $this->load->view('cbseexam/report/_cbsereport'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('template_marks_report'); ?>  </h3>
                    </div>
                    <div class="box-body">                      
                        <form role="form" action="<?php echo site_url('cbseexam/report/getTemplateWiseResult') ?>" method="post" class="row" id="template_wise_exam">
                            <?php echo $this->customlib->getCSRF(); ?>                           
                           <div class="col-md-3">
                                <div class="form-group">   
                                    <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                                    <select id="class_id" name="class_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($classlist as $class) {
                                            ?>
                                            <option value="<?php echo $class['id'] ?>" <?php
                                            if (set_value('class_id') == $class['id']) {
                                                echo "selected=selected";
                                            }
                                            ?>><?php echo $class['class'] ?></option>
                                            <?php
                                                }
                                            ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                </div>  
                            </div>
                            <div class="col-md-3">
                                <div class="form-group"> 
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label><small class="req"> *</small>
                                    <select  id="section_id" name="class_section_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('class_section_id'); ?></span>
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('template') ?></label><small class="req"> *</small>
                                    <select  id="template_id" name="template_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>                                        
                                    </select>
                                    <span class="text-danger"><?php echo form_error('template'); ?></span>
                                </div>
                            </div>                           
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary pull-right btn-sm checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </form>
                    </div> 
                                       
              <div class="exam_data box-body pb0">
                
              </div>               
            </div>
        </div>
    </section>
</div>

</section>
</div>
<script type="text/javascript">


    $(document).on('submit', 'form#template_wise_exam', function (e) {

        e.preventDefault();
        var form = $(this);
        var subsubmit_button = $(this).find(':submit');
        var formdata = form.serializeArray();     

            $.ajax({
                type: "POST",
                url: form.attr('action'),
                data: formdata, // serializes the form's elements.
                dataType:'JSON',
               
                 beforeSend: function () {
                subsubmit_button.button('loading');
                $('.exam_data').html("");          
                },
           
            success: function (response) {
           
                if (response.status == 0) {
                    var message = "";
                    $.each(response.error, function (index, value) {
                        message += value;
                    });
                    errorMsg(message);
                } else {
                   $('.exam_data').html(response.page);                  
                   subsubmit_button.button('reset');

                }                 
            },
            error: function (xhr) { // if error occured

                subsubmit_button.button('reset');
            },
            complete: function () {
                subsubmit_button.button('reset');
            }
            });       
    });

  $(document).on('change','.template_id',function(){
    let template_id=$(this).val();
    getTermByClass(template_id);
  });
  
        function getTermByClass(template_id) {

            if (template_id != "") {
                $('#exam_id').html("");
                var base_url = '<?php echo base_url() ?>';
                var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';                

                $.ajax({
                    type: "POST",
                    url: base_url + "cbseexam/report/getTermTemplateWise",
                    data: {'template_id': template_id},
                    dataType: "json",
                   beforeSend: function () {
                    $('.custom-select-option-box').closest('div').find("input[name='select_all']").attr('checked', false);
                    $('.custom-select-option-box').children().not(':first').remove();
                  },
                    success: function (data) {
                               $('.custom-select-option-box').append(data.page);
                    },
                    complete: function () {
                      
                    }
                });
            }
        }

        $(document).on('change', '#class_id', function (e) {
        $('#section_id').html("");        
        var class_id = $(this).val();
        getSectionByClass(class_id, 0);
    });

    $(document).on('change', '#section_id', function (e) {
        var class_id = $('#class_id').val();
        var class_section_id = $(this).val();
        get_template(class_section_id);       
    });

    function getSectionByClass(class_id, section_id) {
        if (class_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';

            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                beforeSend: function () {
                    $('#section_id').addClass('dropdownloading');
                },
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (section_id == obj.id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                },
                complete: function () {
                    $('#section_id').removeClass('dropdownloading');
                }
            });
        }
    }
        
    function get_template(class_section_id,template){
        var div_data="";
         $.ajax({
            type:'POST',
            url:'<?php echo base_url()?>cbseexam/template/get',
            data:{class_section_id:class_section_id},
            dataType:'JSON',
            beforeSend: function(){

            },
            success: function(data){
                div_data="<option value=''><?php echo $this->lang->line('select')?></option>";
                  $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if(template==obj.id){
                            sel="selected";
                        }
                        div_data += "<option value=" + obj.id + " " + sel + ">" + obj.name + "</option>";
                    });
                    $('#template_id').html(div_data);
            },
            error: function(){

            },
        });
    }

</script>


<script type="text/javascript">

function printDiv(tagid) {
        let hashid = "#"+ tagid;

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


    function exportToExcel(tagid){
var htmls = "";
let hashid = "#"+ tagid;
            var uri = 'data:application/vnd.ms-excel;base64,';
            var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'; 
            var base64 = function(s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            };

            var format = function(s, c) {
                return s.replace(/{(\w+)}/g, function(m, p) {
                    return c[p];
                })
            };

            htmls =   $(hashid).html() ;

            var ctx = {
                worksheet : 'Worksheet',
                table : htmls
            }


            var link = document.createElement("a");
            link.download = "export.xls";
            link.href = uri + base64(format(template, ctx));
            link.click();
}
</script>