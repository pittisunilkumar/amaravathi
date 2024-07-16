<style type="text/css">
     @media print {
      .page-break { 
            display: block; 
            page-break-before: always;
        }
    }
    .tdstyle {border: 1.5px solid;padding:4px;}
    .tdnestedstyle {border: 1px solid;padding: 0px 3px;text-align: center;}
    .firstchild {width: 5%; }
    .tdd{ width: 50%;}
    *{ margin:0; padding: 0;}
    .tc-container{width: 100%;position: relative; text-align: center;}
    .sttext1{font-size: 24px;font-weight: bold;line-height: 30px;}
</style>

<?php 




foreach ($students as $student) {
    $i++;
?>
    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <div style="padding-top: 15px,padding-bottom: 15px; float: right;">
                ORIGINAL
            </div>
        </tr>
        <tr>
            <td valign="top" width="32%" style="padding: 3px;">
                <table cellpadding="0" cellspacing="0" width="100%" class="tc-container" style="background: #efefef;">

                    <tr>
                        <td valign="top">
                            <div class="row">

                                <!-- Left Side (Logo) -->
                                <div style="padding-top: 15px; float: left;">
                                    <img src="<?php echo $this->media_storage->getImageURL('uploads/tcgeneration/logo/'. $certificate[0]->logo); ?>" width="100" height="100"/>
                                </div>

                                
                                <!-- Middle Part (Data) -->
                                <div class="align-items-center justify-content-center" style="padding: 20px;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php echo $certificate[0]->tc_head_tittle; ?>
                                            <!-- MAVILLAPALLI VENUGOPAL EDUCATIONAL SOCIETY -->
                                            <!-- <?php echo $certificate->tc_head_tittle; ?> -->
                                        </div>
                                        <div class="col-md-12">
                                            <!-- College Name -->
                                            <div class="sttext1">
                                                <!-- AMARAVATHI JUNIOR COLLEGE -->
                                                <?php echo $certificate[0]->school_name; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <!-- Additional Information -->
                                            <?php echo $certificate[0]->tc_description; ?>
                                            <!-- (Recognised by Govt. A.P Affiliated by BIE A.P Vijayavada RC No: 081704/Acad/C10/2017-18 Dated: 01-11-2017) (College Code: 08199) -->
                                        </div>
                                        <div class="col-md-12">
                                            <!-- Address -->
                                            <?php echo $certificate[0]->tc_address; ?>
                                            <!-- #11-253, RANIPETA, VENKATAGIRI TOWN, TIRUPATI Dt. -->
                                        </div>
                                    </div>
                                </div>

                            
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td valign="top" style="border-top: 1px solid black;"></td>
                    </tr>

                    <tr>
                        <td valign="top" align="center" style="padding: 1px 0;">
                            <!-- [ No Change in any entry in this certificate shall be made except by the authority issuing it and any inringement of this
                                requirement is liable to involve the imposition of penalty such as that of rustication ] -->
                                <?php echo $certificate[0]->tc_body; ?>
                        </td>
                    </tr>

                    <tr>
                        <td valign="top" style="border-top: 1px solid black;"></td>
                    </tr>

                    <tr>
                        <td valign="top" style="font-size: 16px; padding-top:15px; padding-bottom:10px; position: relative; z-index: 1;text-transform: uppercase;">
                            <div class="sttext1">
                                <!-- <?php echo $idcard->school_name; ?> -->
                                TRANSFER CERTIFICATE
                            </div>
                        </td>
                    </tr>




                    
                    <tr>
                        <td>
                            
                            <span style="display:inline-block;  float:left;"><?php echo $this->lang->line('s_no'); ?> : <?php echo $student->id; ?></span>
                            <span style="display:inline-block;"><?php echo $this->lang->line('admi_no'); ?> : <?php echo $student->admi_no; ?></span>
                            <span style="display:inline-block;  float:right;"><?php echo $this->lang->line('date'); ?> : <?php echo $currentDate = date('d/m/Y');?></span>
                            
                        </td>
                    </tr>






                    <?php $sno=1 ?>



                    <tr>
                        <td valign="top">

                            <div class="staround">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tbody>
                                        <?php if($certificate[0]->enable_student_name){?>
                                            <tr>
                                                <td  class="tdstyle firstchild"><?php echo $sno++;?></td>
                                                <td  class="tdstyle tdd"><?php echo $this->lang->line('student_tc_name');?></td>
                                                <td  class="tdstyle tdd"><?php echo $student->name;?></td>
                                            </tr>
                                        <?php }?>

                                        <?php if($certificate[0]->enable_parents_name){?>
                                            <tr>
                                                <td  class="tdstyle firstchild"><?php echo $sno++; ?></td>
                                                <td  class="tdstyle tdd"><?php echo $this->lang->line('student_tc_guardian'); ?></td>
                                                <td  class="tdstyle tdd">
                                                    <?php if($student->father_name){
                                                        echo $student->father_name;
                                                    ?><?php }elseif($student->mother_name){
                                                        echo $student->mother_name;
                                                    ?>
                                                    <?php }else{
                                                        echo $student->guardian_name;
                                                        ?>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                        <?php } ?>


                                        <tr>
                                            <td  class="tdstyle firstchild"><?php echo $sno++;?></td>
                                            <td  class="tdstyle tdd"><?php echo $this->lang->line('student_tc_nationality'); ?></td>
                                            <td  class="tdstyle tdd"><?php echo $certificate[0]->tc_nationality ?></td>
                                        </tr>

                                        <?php if($certificate[0]->enable_caste){?>
                                        <tr>
                                            <td  class="tdstyle firstchild"><?php echo $sno++; ?></td>
                                            <td  class="tdstyle tdd"><?php echo $this->lang->line('student_tc_caste'); ?></td>
                                            <td  class="tdstyle tdd"><?php echo $student->category; ?></td>
                                        </tr>
                                        <?php }?>

                                        <?php if($certificate[0]->enable_dob){?>
                                            <tr>
                                                <td  class="tdstyle firstchild"><?php echo $sno++; ?></td>
                                                <td  class="tdstyle tdd"><?php echo $this->lang->line('student_tc_dob'); ?></td>
                                                <td  class="tdstyle tdd">
                                                    <!-- <?php 
                                                        echo $student->dob; 
                                                        $dateOfBirth = new DateTime($student->dob);
                                                        $formattedDateOfBirth = $dateOfBirth->format('d/m/Y');
                                                        echo $formattedDateOfBirth;
                                                    ?> -->
                                                    <?php
                                                        // Assuming $student->dob contains the date of birth in Y-m-d format
                                                        $formattedDateOfBirth = date('d/m/Y', strtotime($student->dob));
                                                        echo $formattedDateOfBirth;
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php }?>
                                        <tr>
                                            <td rowspan="4" class="tdstyle firstchild"><?php echo $sno++; ?></td> 
                                            <td class="tdstyle tdd">
                                                <?php echo $this->lang->line('student_tc_class'); ?>                                         
                                            </td>  
                                            <td class="tdstyle tdd">
                                                <?php echo $student->class; ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            
                                            <td class="tdstyle tdd">
                                                <?php echo $this->lang->line('student_tc_firstlang'); ?>
                                            </td>
                                            <td class="tdstyle tdd">
                                               <?php echo $firstlang; ?>
                                            </td>
                                            
                                        </tr>

                                        <tr>
                                            <td class="tdstyle tdd">
                                                <?php echo $this->lang->line('student_tc_secondlang'); ?>
                                            </td>
                                            
                                            <td class="tdstyle tdd">
                                            <?php echo $secondlang; ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="tdstyle tdd">
                                                <?php echo $this->lang->line('student_tc_optionallang'); ?>
                                            </td>
                                            <td class="tdstyle tdd">
                                                <?php echo $certificate[0]->tc_optional_lang ?>
                                            </td>
                                        </tr>

                                        <?php if($certificate[0]->enable_mother_tongue){?>
                                        <tr>
                                            <td  class="tdstyle firstchild"><?php echo $sno++; ?></td>
                                            <td  class="tdstyle tdd"><?php echo $this->lang->line('student_tc_mothertong'); ?></td>
                                            <td  class="tdstyle tdd"><?php echo $certificate[0]->tc_mother_tongue;?></td>
                                        </tr>
                                        <?php }?>

                                        <?php if($certificate[0]->enable_admission_date){?>
                                            <tr>
                                                <td  class="tdstyle firstchild"><?php echo $sno++; ?></td>
                                                <td  class="tdstyle tdd"><?php echo $this->lang->line('student_tc_date_admission'); ?></td>
                                                <td  class="tdstyle tdd">
                                                    <!-- <?php 
                                                        echo $student->admission_date; 

                                                        $dateOfBirth = new DateTime($student->admission_date);
                                                        $formattedDateOfBirth = $dateOfBirth->format('d/m/Y');
                                                        echo $formattedDateOfBirth;
                                                    
                                                    ?> -->

                                                    <?php
                                                        // Assuming $student->dob contains the date of birth in Y-m-d format
                                                        $formattedDateOfBirth = date('d/m/Y', strtotime($student->admission_date));
                                                        echo $formattedDateOfBirth;
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php }?>

                                        <tr>
                                            <td  class="tdstyle firstchild"><?php echo $sno++; ?></td>
                                            <td  class="tdstyle tdd"><?php echo $this->lang->line('student_tc_qualified'); ?></td>
                                            <td  class="tdstyle tdd"><?php echo $certificate[0]->tc_second_year_course ?></td>
                                        </tr>

                                        <tr>
                                            <td  class="tdstyle firstchild"><?php echo $sno++; ?></td>
                                            <td  class="tdstyle tdd"><?php echo $this->lang->line('student_tc_declared'); ?></td>
                                            <td  class="tdstyle tdd"><?php echo $certificate[0]->tc_eligible_university_course ?></td>
                                        </tr>
                                        <tr>
                                            <td rowspan="3" class="tdstyle firstchild"><?php echo $sno++; ?></td> 
                                            <tr>
                                                <td class="tdstyle tdd">
                                                    <?php echo $this->lang->line('student_tc_scholarship'); ?>                                            
                                                </td>
                                                <td class="tdstyle tdd">
                                                    <?php echo $certificate[0]->tc_receipt_scholarship; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="tdstyle tdd">
                                                    <?php echo $this->lang->line('student_tc_specified'); ?>                                            
                                                </td>
                                                <td class="tdstyle tdd">
                                                <?php echo $certificate[0]->tc_receipt_concession; ?>
                                                </td>
                                            </tr>
                                                                                     
                                        </tr>

                                        <tr>
                                            <td  class="tdstyle firstchild"><?php echo $sno++; ?></td>
                                            <td  class="tdstyle tdd"><?php echo $this->lang->line('student_tc_actually'); ?></td>
                                            <td  class="tdstyle tdd">
                                                <?php 
                                                   echo $certificate[0]->tc_date_left;


                                                    // $dateOfBirth = new DateTime($certificate[0]->tc_date_left);
                                                    // $formattedDateOfBirth = $dateOfBirth->format('d/m/Y');
                                                    // echo $formattedDateOfBirth;
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td  class="tdstyle firstchild"><?php echo $sno++; ?></td>
                                            <td  class="tdstyle tdd">                                                    
                                                <?php echo $this->lang->line('student_tc_transfercertificate'); ?>
                                            </td>
                                            <td  class="tdstyle tdd"><?php echo date('d/m/Y');?></td>
                                        </tr>
                                        <tr>
                                            <td  class="tdstyle firstchild"><?php echo $sno++; ?></td>
                                            <td  class="tdstyle tdd">
                                                <?php echo $this->lang->line('student_tc_candidate'); ?>
                                            </td>
                                            <td  class="tdstyle tdd"><?php echo $certificate[0]->tc_punishment_during_period; ?></td>
                                        </tr>
                                        <tr>
                                            <td  class="tdstyle firstchild"><?php echo $sno++; ?></td>
                                            <td  class="tdstyle tdd">
                                                <?php echo $this->lang->line('student_tc_conduct'); ?>
                                            </td>
                                            <td  class="tdstyle tdd"><?php echo $certificate[0]->tc_conduct;?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--./staround-->
                        </td>
                    </tr>

                    <tr>
                        <td valign="top" align="center" style="padding-top:20px">
                            <?php echo $certificate[0]->tc_footer;?>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-top:85px; padding-bottom:10px; padding-right:10px;" align="right" class="principal">
                            
                        <div style="font-size: 15px; font-weight: bold; color: black;">
                            PRINCIPAL
                        </div>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php
}
?>


