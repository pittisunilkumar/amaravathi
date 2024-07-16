<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>


<style>
    .dropdown-menu-scrollable {
        max-height: 200px; /* Set max height for dropdown */
        overflow-y: auto; /* Enable vertical scrolling */
    }
    
    .form-check {
        margin: 5px 0; /* Adjust spacing */
    }
    
    .form-check-input {
        margin-right: 10px; /* Adjust spacing between checkbox and label */
    }
    
    .form-check-label {
        cursor: pointer; /* Add pointer cursor on hover */
    }
    
    .form-check-input:checked + .form-check-label::before {
        background-color: #007bff; /* Custom color for checked checkbox */
        border-color: #007bff;
    }
</style>

<div class="content-wrapper">
    <section class="content-header"></section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('financereports/_finance');?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull"></div>
                    <div class="box-header ">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form role="form" action="<?php echo site_url('financereports/typewisebalancereport') ?>" method="post" class="">
                        <div class="box-body row">
                            <?php echo $this->customlib->getCSRF(); ?>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sch_session_id"><?php echo $this->lang->line('session'); ?></label><small class="req"> *</small>
                                    <select id="sch_session_id" name="sch_session_id" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($sessionlist as $session): ?>
                                            <option value="<?php echo $session['id'] ?>" <?php echo set_select('sch_session_id', $session['id']); ?>><?php echo $session['session'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('sch_session_id'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label>
                                    <select autofocus="" id="class_id" name="class_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                            foreach ($classlist as $class) {
                                                ?>
                                            <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) {
                                                    echo "selected=selected";
                                                }
                                                ?>><?php echo $class['class'] ?>
                                            </option>
                                            <?php
                                            $count++;
                                            }
                                            ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label>
                                    <select  id="section_id" name="section_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                </div>
                            </div>





                            <!-- <div class="col-sm-3">
                               <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('fees_group'); ?></label>

                                    <select  id="feegroup_id" name="feegroup_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                            foreach ($feegroupList as $feegroup) {
                                                ?>
                                            <option value="<?php echo $feegroup['id'] ?>"<?php
                                                if (set_value('feegroup_id') == $feegroup['id']) {
                                                        echo "selected =selected";
                                                    }
                                                    ?>><?php echo $feegroup['name'] ?>
                                            </option>

                                            <?php
                                        $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('feegroup_id'); ?></span>
                                </div>
                            </div> -->













<div class="col-sm-3">
    <div class="form-group">
        <label for="feegroup_dropdown"><?php echo $this->lang->line('fees_group'); ?></label>
        <div class="dropdown">
            <input type="text" class="form-control" id="feegroup_dropdown" placeholder="<?php echo $this->lang->line('select'); ?>" aria-haspopup="true" aria-expanded="false" readonly data-toggle="dropdown">
            <div class="dropdown-menu dropdown-menu-scrollable" aria-labelledby="feegroup_dropdown">
                <div class="scroll-wrapper">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="check-all" name="check-all">
                        <label class="form-check-label" for="check-all">
                            Check All
                        </label>
                    </div>
                    <?php foreach ($feegroupList as $feegroup): ?>
                        <div class="form-check">
                            <input class="form-check-input feegroup-checkbox" type="checkbox" value="<?php echo $feegroup['id']; ?>" id="feegroup_<?php echo $feegroup['id']; ?>" name="feegroup_ids[]" <?php echo (set_checkbox('feegroup_ids', $feegroup['id']) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="feegroup_<?php echo $feegroup['id']; ?>">
                                <?php echo $feegroup['name']; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <input type="hidden" name="feegroup_id" id="feegroup_id" value="">
        </div>
        <span class="text-danger"><?php echo form_error('feegroup_ids'); ?></span>
    </div>
</div>

















                            <div class="col-sm-3">
                               <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('fees_type'); ?></label><small class="req"> *</small>

                                    <select  id="feetype_id" name="feetype_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                            foreach ($feetypeList as $feetype) {
                                                ?>
                                            <option value="<?php echo $feetype['id'] ?>"<?php
                                                if (set_value('feetype_id') == $feetype['id']) {
                                                        echo "selected =selected";
                                                    }
                                                    ?>><?php echo $feetype['type'] ?>
                                            </option>

                                            <?php
                                        $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('feetype_id'); ?></span>
                                </div>
                            </div>
                            

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" id="search_btn" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>

                        </div>
                    </form>
                    <?php
                    if (empty($results)) {
                        ?>
                        <div class="box-header ptbnull">
                            <div class="alert alert-info">
                            <?php echo $this->lang->line('no_record_found'); ?>
                            </div>
                        </div>
                                        <?php
                    } else {
                        $sn=0;
                        ?>
                    <div class="">
                        <div class="box-header ptbnull"></div>
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-money"></i> <?php ?> <?php echo $this->lang->line('fees_collection_report'); ?></h3>
                        </div>
                        
                       
                                
                                
                        <div class="box-body table-responsive" id="transfee">
                        <div id="printhead"><center><b><h4><?php echo $this->lang->line('typewisebalancereport') . "<br>";
                            $this->customlib->get_postmessage();
                            ?></h4></b></center>
                        </div>
                            <div class="download_label"><?php echo $this->lang->line('typewisebalancereport') . "<br>";
                            $this->customlib->get_postmessage();
                            ?>
                        </div>
    
   

                            <a class="btn btn-default btn-xs pull-right" id="print" onclick="printDiv()" ><i class="fa fa-print"></i></a>
                            <a class="btn btn-default btn-xs pull-right" id="btnExport" onclick="exportToExcel();"> <i class="fa fa-file-excel-o"></i> </a>

                            <table class="table table-striped table-bordered table-hover " id="headerTable">
                                <thead class="header">
                                
                                    <tr>
                                        <th><?php echo $this->lang->line('s_no'); ?></th>
                                        <th><?php echo $this->lang->line('admission_no'); ?></th>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('class'); ?></th>
                                        <th><?php echo $this->lang->line('section'); ?></th>
                                        <th><?php echo $this->lang->line('fees_group');?></th>
                                        <th><?php echo $this->lang->line('fee_type'); ?></th>
                                        <th><?php echo $this->lang->line('total'); ?></th>
                                        <th><?php echo $this->lang->line('fine');?></th>
                                        <th><?php echo $this->lang->line('discount')?></th>
                                        <th><?php echo $this->lang->line('paid'); ?></th>
                                        <th><?php echo $this->lang->line('balance'); ?></th>
                                    </tr>
                                </thead>
                                <>
                                
                                <?php 

                                    if(!empty($results)){
                                        $total_fine = 0;
                                        $total_paid =0;
                                        $total_discount =0;
                                        $total_amount =0;
                                        $total_bal = 0;
                                
                                    foreach ($results as $row){ 
                                        $total_fine += $row['total_fine'];
                                        $total_paid +=$row['total_amount'];
                                        $total_discount +=$row['total_discount'];
                                        $total_amount +=$row['total'];
                                        
                                    $sn++;
                                    ?>
                                   <tr>
                                        <td><?php echo $sn; ?></td>
                                        <td><?php echo $row['admission_no']; ?></td>
                                        <td><?php echo $row['firstname'].' '.$row['middlename'].' '.$row['lastname']; ?></td>
                                        <td><?php echo $row['class']; ?></td>
                                        <td><?php echo $row['section']; ?></td>
                                        <td><?php echo $row['feegroupname'];?></td>
                                        <td><?php echo $row['type']; ?></td>
                                        <td><?php echo $row['total'];?></td>
                                        <td><?php echo $row['total_fine'];?></td>
                                        <td><?php echo $row['total_discount'];?></td>
                                        <td><?php echo $row['total_amount']; ?></td>
                                        <td><?php echo $row['total']-$row['total_amount']-$row['total_discount']; ?></td>
                                        
                                   </tr>
                                <?php } 
                                
                                
                                ?>

                                <tr class="box box-solid total-bg">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><?php echo $total_amount;?></td>
                                    <td><?php echo $total_fine;?></td>
                                    <td><?php echo $total_discount;?></td>
                                    <td><?php echo $total_paid;?></td>
                                    <td><?php echo $total_amount-$total_paid-$total_discount; ?></td>
                                </tr>

                                <?php }?>

                                </tbody>
                            </table>
                        </div>
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
<iframe id="txtArea1" style="display:none"></iframe>

<script>

$(document).ready(function(){
    var class_id = $('#class_id').val();
    var section_id = '<?php echo $selected_section; ?>';
    getSectionByClass(class_id, section_id);
})

$(document).on('change', '#class_id', function (e) {
    $('#section_id').html("");
    var class_id = $(this).val();
    getSectionByClass(class_id, 0);
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
                    if (section_id == obj.section_id) {

                        sel = "selected";
                    }
                    div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                });
                $('#section_id').append(div_data);
            },
            complete: function () {
                $('#section_id').removeClass('dropdownloading');
            }
        });
    }
}


document.getElementById("print").style.display = "block";
document.getElementById("btnExport").style.display = "block";
document.getElementById("printhead").style.display = "none";

function printDiv() {
    document.getElementById("print").style.display = "none";
    document.getElementById("btnExport").style.display = "none";
     document.getElementById("printhead").style.display = "block";
    var divElements = document.getElementById('transfee').innerHTML;
    var oldPage = document.body.innerHTML;
    document.body.innerHTML =
            "<html><head><title><?php echo $this->lang->line('typewisebalancereport'); ?></title></head><body>" +
            divElements + "</body>";
    window.print();
    document.body.innerHTML = oldPage;
    document.getElementById("printhead").style.display = "none";
    location.reload(true);
}

function fnExcelReport(){
    exportToExcel();
}

function exportToExcel(){
    var htmls = "";
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
    var tab_text = "<tr >";
    var textRange;
    var j = 0;
    var val="";
    tab = document.getElementById('headerTable'); // id of table

    for (j = 0; j < tab.rows.length; j++)
    {
        tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
    }

    var ctx = {
        worksheet : 'Worksheet',
        table : tab_text
    }

    var link = document.createElement("a");
    link.download = "studentfee_collection_report.xls";
    link.href = uri + base64(format(template, ctx));
    link.click();
}

</script>





<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle checkbox selection
        $('.dropdown-menu-scrollable').on('click', function(e) {
            e.stopPropagation();
        });

        // Check All functionality
        $('#check-all').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.feegroup-checkbox').prop('checked', isChecked);
            updateDropdownText();
            updateHiddenInput();
        });

        // Handle checkbox change
        $('.feegroup-checkbox').on('change', function() {
            updateDropdownText();
            updateHiddenInput();
        });

        // Update dropdown text based on selection
        function updateDropdownText() {
            var selectedGroups = $('.feegroup-checkbox:checked').map(function() {
                return $(this).next('.form-check-label').text();
            }).get().join(', ');

            if (selectedGroups === '') {
                selectedGroups = '<?php echo $this->lang->line('select'); ?>';
            }

            $('#feegroup_dropdown').val(selectedGroups);
        }

        // Update hidden input with selected IDs
        function updateHiddenInput() {
            var selectedIds = $('.feegroup-checkbox:checked').map(function() {
                return $(this).val();
            }).get().join(',');

            $('#feegroup_id').val(selectedIds);
        }

        // Initialize dropdown text on page load
        updateDropdownText();
    });
</script>



