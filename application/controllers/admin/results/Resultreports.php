<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Resultreports extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->library('media_storage');
        $this->load->model('examtype_model');
        $this->load->model('publicexamtype_model');
        
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function internalexamreport(){

        $class             = $this->class_model->get();
        $data['classlist'] = $class;

        $progresslist            = $this->customlib->passstatus();
        $data['progresslist']    = $progresslist;

        // $sessionss               = $this->examtype_model->sessions();
        // $data['sessionss']       = $sessionss;
        $data['sch_setting']     = $this->sch_setting_detail;

        $category                   = $this->examtype_model->get();
        $data['categorylist']       = $category;

        $this->load->view('layout/header');
        $this->load->view('admin/results/internalexamreport', $data);
        $this->load->view('layout/footer');
    }

    public function externalexamreport(){
        $class             = $this->class_model->get();
        $data['classlist'] = $class;

        $progresslist            = $this->customlib->passstatus();
        $data['progresslist']    = $progresslist;

        // $sessionss               = $this->examtype_model->sessions();
        // $data['sessionss']       = $sessionss;
        $data['sch_setting']     = $this->sch_setting_detail;

        $category                   = $this->publicexamtype_model->get();
        $data['categorylist']       = $category;

        $this->load->view('layout/header');
        $this->load->view('admin/results/externalexamreport', $data);
        $this->load->view('layout/footer');
    }

    public function interexamsearch(){
        $class             = $this->class_model->get();
        $data['classlist'] = $class;

        $progresslist            = $this->customlib->passstatus();
        $data['progresslist']    = $progresslist;

        // $sessionss               = $this->examtype_model->sessions();
        // $data['sessionss']       = $sessionss;
        $data['sch_setting']     = $this->sch_setting_detail;

        $exam_id = $this->input->post('exam_id');
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $subject_id = $this->input->post('pickup_point_id');
        $examstatus = $this->input->post('progress_id');

        $data['examm'] = $exam_id;
        $data['classs'] = $class_id;
        $data['sectionn'] = $section_id;
        $data['subjectt'] = $subject_id;
        $data['examstatuss'] = $examstatus;


        $category                   = $this->examtype_model->get();
        $data['categorylist']       = $category;

        $studentresult              = $this->examtype_model->getstudentresult($exam_id,$class_id,$section_id,$subject_id,$examstatus);
        $data['studentresults']     = $studentresult;

        $this->load->view('layout/header');
        $this->load->view('admin/results/internalexamreport', $data);
        $this->load->view('layout/footer');
    }

    public function getsubjects(){
        $class_id = $this->input->get('class_id');
        $data     = $this->examtype_model->getsubjects($class_id);
        echo json_encode($data);
    }

    public function exterexamsearch(){
        $class             = $this->class_model->get();
        $data['classlist'] = $class;

        $progresslist            = $this->customlib->passstatus();
        $data['progresslist']    = $progresslist;

        // $sessionss               = $this->examtype_model->sessions();
        // $data['sessionss']       = $sessionss;
        $data['sch_setting']     = $this->sch_setting_detail;

        $exam_id = $this->input->post('exam_id');
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $subject_id = $this->input->post('pickup_point_id');
        $examstatus = $this->input->post('progress_id');

        $data['examm'] = $exam_id;
        $data['classs'] = $class_id;
        $data['sectionn'] = $section_id;
        $data['subjectt'] = $subject_id;
        $data['examstatuss'] = $examstatus;


        $category                   = $this->publicexamtype_model->get();
        $data['categorylist']       = $category;

        $studentresult              = $this->publicexamtype_model->getstudentresult($exam_id,$class_id,$section_id,$subject_id,$examstatus);
        $data['studentresults']     = $studentresult;

        $this->load->view('layout/header');
        $this->load->view('admin/results/externalexamreport', $data);
        $this->load->view('layout/footer');
    }

    public function externalgetsubjects(){
        $class_id = $this->input->get('class_id');
        $data     = $this->publicexamtype_model->getsubjects($class_id);
        echo json_encode($data);
    }


}
