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
                           <form role="form" action="<?php echo site_url('admin/multibranch/finance/payroll') ?>" method="post" class="">
                    <div class="box-body">


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

  </form>
                             <div class="nav-tabs-custom border0 navnoshadow">
                     
                  
                        <div class="box-header ptbnull"></div>
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-money"></i> <?php echo $this->lang->line('payroll') . " " . $this->lang->line('report'); ?></h3>
                        </div>
                        <div class="box-body table-responsive">
                        <div class="download_label"><?php
                                echo $this->lang->line('payroll') . " " . $this->lang->line('report');
                                $this->customlib->get_postmessage();
                                ;
                                ?></div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>                                        <th><?php echo $this->lang->line('branch'); ?></th>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('role'); ?></th>
                                        <th><?php echo $this->lang->line('designation'); ?></th>
                                        <th><?php echo $this->lang->line('month'); ?> - <?php echo $this->lang->line('year') ?></th>
                                        <th><?php echo $this->lang->line('payslip'); ?></th>
                                        <th class="text text-right"><?php echo $this->lang->line('basic_salary'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                        <th class="text text-right"><?php echo $this->lang->line('earning'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                        <th class="text text-right"><?php echo $this->lang->line('deduction'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                        <th class="text text-right"><?php echo $this->lang->line('gross_salary'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                        <th class="text text-right"><?php echo $this->lang->line('tax'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                        <th class="text text-right"><?php echo $this->lang->line('net_salary'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $basic = 0;
                                    $gross = 0;
                                    $net = 0;
                                    $earnings = 0;
                                    $deduction = 0;
                                    $tax = 0;

                                    if (empty($payrollList)) {
                                        ?>

                                        <?php
                                    } else {
                                        $count = 1;

                                        foreach ($payrollList as $key => $value) {


                                            $basic += $value["basic"];
                                            $gross += $value["basic"] + $value["total_allowance"];
                                            $net += $value["net_salary"];
                                            $earnings += $value["total_allowance"];
                                            $deduction += $value["total_deduction"];
                                            if ($value["tax"] != '') {
                                                $taxdata = $value["tax"];
                                            } else {
                                                $taxdata = 0;
                                            }
                                            $tax += $taxdata;
                                            $total = 0;
                                            $grd_total = 0;
                                            ?>
                                            <tr>
   <td>
        <?php echo $value['branch_name']; ?>
                                                </td>

                                                <td style="text-transform: capitalize;">
                                                    <span data-toggle="popover" class="detail_popover" data-original-title="" title=""><a href="<?php echo base_url() ?>admin/staff/profile/<?php echo $value['staff_id']; ?>"><?php echo $value['name'] . " " . $value['surname']." (".$value['employee_id'].")"; ?></a></span>
                                                   
                                                </td>
                                                <td><?php echo $value['user_type']; ?></td>
                                                <td>
                                                    <span  data-original-title="" title=""><?php
                                                        echo $value['designation'];
                                                        ;
                                                        ?></span>

                                                </td>
                                                <td><?php echo $this->lang->line(strtolower($value['month'])) . " - " . $value['year']; ?></td>
                                                <td>
                                                    <span data-toggle="popover" class="detail_popover" ><a href="#"><?php echo $value['id']; ?></a></span>
                                                    <div class="fee_detail_popover" style="display: none"><?php echo $this->lang->line('mode'); ?>: <?php
                                                        if (array_key_exists($value["payment_mode"], $payment_mode)) {
                                                            echo $payment_mode[$value["payment_mode"]];
                                                        }
                                                        ?></div>
                                                </td>
                                                <td class="text text-right"><?php echo amountFormat($value['basic']); ?></td>

                                                <td class="text text-right">
                                                    <?php echo amountFormat($value['total_allowance']); ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    $t = ($value['total_deduction']);
                                                    echo (amountFormat($t))
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php echo amountFormat($value['basic'] + $value['total_allowance'] - $t); ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    if ($value['tax'] != '') {
                                                        $t = $value['tax'];
                                                    } else {
                                                        $t = 0;
                                                    }

                                                    echo amountFormat($t)
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    $t = ($value['net_salary']);
                                                    echo amountFormat($t)
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $count++;
                                        }
                                        ?>
                                        
                                </tbody>
                                <tr class="box box-solid total-bg">

                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right"><?php echo $this->lang->line('grand_total'); ?> </td>
                                            <td class="text text-right"><?php echo ($currency_symbol . amountFormat($basic)); ?></td>

                                            <td class="text text-right"><?php echo ($currency_symbol . amountFormat($earnings)); ?></td>
                                            <td class="text text-right"><?php echo ($currency_symbol . amountFormat($deduction)); ?></td>
                                            <td class="text text-right"><?php echo ($currency_symbol . amountFormat($basic+$earnings-$deduction)); ?></td>
                                            <td class="text text-right"><?php echo ($currency_symbol . amountFormat($tax)); ?></td>
                                            <td class="text text-right"><?php echo ($currency_symbol . amountFormat($net)); ?></td>

                                        </tr>
                                    <?php } ?>
                            </table>
                        </div>
                  
                             </div>

             
                                                          </div><!--./box box-primary -->
       
                                                        </div>
                                                        </div>
                                                        </section>
                                                        </div>

<script type="text/javascript">
    
    var search_type ="<?php echo set_value('search_type',"") ?>";
    $(document).ready(function(){

    if(search_type == "period"){


        showdate(search_type);
    }
    })
</script>