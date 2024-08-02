<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-map-o"></i> <?php echo $this->lang->line('examinations'); ?> <small><?php echo $this->lang->line('student_fee1'); ?></small></h1>
    </section>
    <!-- Main content -->
    <section class="content">
		<?php $this->load->view('cbseexam/exam/_generate_rank'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('exam_wise_rank'); ?></h3>
                    </div>
                    <div class="box-body">
                        <form role="form" action="<?php echo site_url('cbseexam/exam/exam_ajax_rank') ?>" method="post" class="row class_search_form">
                            <?php echo $this->customlib->getCSRF(); ?>                           
                           
                            <div class="col-md-3">
                                <div class="form-group">
                                 
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('exam') ?></label><small class="req"> *</small>
                                    <select  id="exam" name="exam" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>  
                                        <?php
                                        foreach ($exams as $exam_key => $exam_value) {
                                            ?>
                                            <option value="<?php echo $exam_value['id'] ?>" <?php
                                            if (set_value('exam') == $exam_value['id']) {
                                                echo "selected=selected";
                                            }
                                            ?>><?php echo $exam_value['name'] ?></option>
                                            <?php
                                                }
                                            ?>                                      
                                    </select>
                                    <span class="text-danger"  id="error_exam"><?php echo form_error('exam'); ?></span>
                                </div>
                            </div> 
                            <div class="col-sm-12">
                                <div class="form-group">
                                <button type="submit" name="search" value="search_full" class="btn btn-primary pull-right btn-sm checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="studentlist">

                    </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
(function ($) { 
    "use strict"; 
    
    $(document).ready(function () {
        $('#show_term_wise').hide();
        $('#show_exam_wise').hide();
        $('.select2').select2();
    }); 
    
    var date_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';

 

 $(document).on('click','.download_pdf',function(){
      let $button_ = $(this);
      let template_id = $(this).data('template_id');
      let student_session_id = $(this).data('student_session_id');  
      let admission_no = $(this).data('admission_no');
      let student_name = $(this).data('student_name');  
      var action=($button_.data('action'));
         $.ajax({
            type: 'POST',
            url: baseurl+'/cbseexam/result/printmarksheet',
            data: {
                'marksheet_template':template_id,
                'student_session_id[]':student_session_id,
                'type':action
            },
         
            beforeSend: function() { 
               $button_.button('loading');    
            },
            xhr: function () {// Seems like the only way to get access to the xhr object
                var xhr = new XMLHttpRequest();
                xhr.responseType = 'blob'
                return xhr;
            },
           success: function (data, jqXHR, response) {          
                
                   var blob = new Blob([data], {type: 'application/pdf'});
                   var link = document.createElement('a');
                   link.href = window.URL.createObjectURL(blob);
                   link.download =  student_name+'_'+admission_no+".pdf";
                   document.body.appendChild(link);
                   link.click();
                   document.body.removeChild(link);
                   $button_.button('reset');
            },
            error: function(xhr) { // if error occured
              
                $button_.button('reset');
            },
            complete: function() {
             
                    $button_.button('reset');
              
            }
        });
    });


  $(document).on('click','.email_pdf',function(){
     let $button_ = $(this);
      let template_id = $(this).data('template_id');
      let student_session_id = $(this).data('student_session_id');  
       let admission_no = $(this).data('admission_no');
      let student_name = $(this).data('student_name'); 
        var action=($button_.data('action'));
    $.ajax({
    type: 'POST',
    url: baseurl+'/cbseexam/result/printmarksheet',
    data: {
                'marksheet_template':template_id,
                'student_session_id[]':student_session_id,
                  'type':action
            },
    dataType: 'JSON',
    beforeSend: function() {       
       $button_.button('loading');      
    },
   success: function (data, jqXHR, response) {
             if (data.status == 1) {
                  successMsg(data.message);
                } else {
                  errorMsg(data.message);
                }
            },
    error: function(xhr) { // if error occured      
        $button_.button('reset');
    },
    complete: function() {     
            $button_.button('reset');      
    }
});
    });

    $(document).on('submit', 'form#printMarksheet', function (e) {

        e.preventDefault();
        var form = $(this);
        var subsubmit_button = $(this).find(':submit');
        var formdata = form.serializeArray();
        formdata.push({name: 'type', value: 'download'});

        var list_selected =  $('form#printMarksheet input[name="student_session_id[]"]:checked').length;
        if(list_selected > 0){
            $.ajax({
                type: "POST",
                url: form.attr('action'),
                data: formdata, // serializes the form's elements.
               
                 beforeSend: function () {
                subsubmit_button.button('loading');
            },
             xhr: function () {// Seems like the only way to get access to the xhr object
                var xhr = new XMLHttpRequest();
                xhr.responseType = 'blob'
                return xhr;
            },
            success: function (data, jqXHR, response) {
       
                   var date_time = new Date().getTime();
                   var blob = new Blob([data], {type: 'application/pdf'});
                   var link = document.createElement('a');
                   link.href = window.URL.createObjectURL(blob);
                   link.download = "bulk_download.pdf";
                   document.body.appendChild(link);
                   link.click();
                   document.body.removeChild(link);
                   subsubmit_button.button('reset');
            },
            error: function (xhr) { // if error occured

                alert("dddd");
                subsubmit_button.button('reset');
            },
            complete: function () {
                subsubmit_button.button('reset');
            }
            });
        }else{
            confirm("<?php echo $this->lang->line('please_select_student'); ?>");
        }
    });

    $(document).on('click', '#select_all', function () {
        $(this).closest('table').find('td input:checkbox').prop('checked', this.checked);
    });   
    
    var base_url = '<?php echo base_url() ?>';
    $('#marksheet').change(function(){
        var marksheet_type = $('#marksheet').val();
        var section_id = $('#section_id').val();
        $('#term_wise_id').empty();
        $('#exam_wise_id').empty();
        $.ajax({
            type: "post",
            url: base_url + "cbseexam/result/examtermwise",
            data: {'marksheet_type': marksheet_type, 'section_id':section_id},
            dataType: "json",
            success: function (data) {
                if(data.status == 1){
                    $.each(data.data, function (i, obj)
                    {
                        if(data.type == 'term_wise'){
                            $('#term_wise_id').append("<option value=" + obj.id + ">" + obj.term_name + "</option>");
                            $('#show_term_wise').show();
                            $('#show_exam_wise').hide();
                        }else if(data.type == 'exam_wise'){
                            $('#exam_wise_id').append("<option value=" + obj.id + ">" + obj.cbse_exam_name + "</option>");
                            $('#show_term_wise').hide();
                            $('#show_exam_wise').show();
                        }
                    });
                }else{
                    $('#show_term_wise').hide();
                    $('#show_exam_wise').hide();
                }
            }
        });
    })


    
    function getExamByExamgroup(exam_group_id, exam_id) {

        if (exam_group_id !== "") {
            $('#exam_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "POST",
                url: base_url + "admin/examgroup/getExamByExamgroup",
                data: {'exam_group_id': exam_group_id},
                dataType: "json",
                beforeSend: function () {
                    $('#exam_id').addClass('dropdownloading');
                },
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (exam_id === obj.id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.id + " " + sel + ">" + obj.exam + "</option>";
                    });

                    $('#exam_id').append(div_data);
                    $('#exam_id').trigger('change');
                },
                complete: function () {
                    $('#exam_id').removeClass('dropdownloading');
                }
            });
        }
    }


})(jQuery);

</script>
<script type="text/javascript">
   
    function Popup(data)
    {
        var frame1 = $('<iframe />');
        frame1[0].name = "frame1";
        $("body").append(frame1);
        var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
        frameDoc.document.open();
        //Create a new HTML document.
        frameDoc.document.write('<html>');
        frameDoc.document.write('<head>');
        frameDoc.document.write('<title></title>');
        frameDoc.document.write('</head>');
        frameDoc.document.write('<body>');
        frameDoc.document.write(data);
        frameDoc.document.write('</body>');
        frameDoc.document.write('</html>');
        frameDoc.document.close();
        setTimeout(function () {
            window.frames["frame1"].focus();
            window.frames["frame1"].print();
            frame1.remove();
        }, 500);
        return true;
    }   
</script> 

<script type="text/javascript">

$(document).ready(function(){
$(document).on('submit','.class_search_form',function(e){
   e.preventDefault(); // avoid to execute the actual submit of the form.
    var $this = $(this).find("button[type=submit]:focus");
    var form = $(this);
    var url = form.attr('action');
    var form_data = form.serializeArray();
    form_data.push({name: 'search_type', value: $this.attr('value')});
    $.ajax({
           url: url,
           type: "POST",
           dataType:'JSON',
           data: form_data, // serializes the form's elements.
              beforeSend: function () {
                $('[id^=error]').html("");
                $('.studentlist').html("");

                $this.button('loading');

               },
              success: function(response) { // your success handler

                if(!response.status){
                    $.each(response.error, function(key, value) {
                    $('#error_' + key).html(value);
                    });
                }else{

                    $('.studentlist').html(response.page);

     
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


$(document).on('submit','#rankgenerate',function(e){
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

                  $('.class_search_form').submit();

     
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

    });
</script>