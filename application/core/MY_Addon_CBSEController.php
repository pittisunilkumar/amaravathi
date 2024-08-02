<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_Addon_CBSEController extends Admin_Controller
{

    public function __construct()
    {

        parent::__construct();

        $this->load->helper("cbse");
        $this->load->config('cbse_config');
        $this->load->library('cbse_mail_sms');
        $this->load->model(array("cbseexam/cbseexam_assessment_model", "cbseexam/cbseexam_exam_model", "cbseexam/cbseexam_grade_model", "cbseexam/cbseexam_term_model", "cbseexam/cbseexam_result_model", "cbseexam/cbseexam_template_model", "cbseexam/cbseexam_student_rank_model", "cbseexam/cbseexam_observation_model", "cbseexam/cbse_observation_term_model", "cbseexam/cbse_observation_term_student_subparameter_model", "cbseexam/cbseexam_observation_parameter_model", "section_model"));

        // if ($this->uri->segment(1) == "cbseexam" && ($this->router->fetch_class() != "setting" xor $this->router->fetch_method() != "index")) {

        //     $this->auth->addonchk('sscbse', site_url('cbseexam/setting/index'));
        // }
        // if ($this->uri->segment(1) == "cbseexam" && $this->router->fetch_class() != "setting") {

        //     $this->auth->addonchk('sscbse', site_url('cbseexam/setting/index'));
        // } elseif ($this->uri->segment(1) != "cbseexam") {

        //     redirect('admin/unauthorized');
        // }
    }

    
}
