<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Student_referral extends Admin_Controller
{

    public $sch_setting_detail = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->config->load('app-config');
        $this->config->load("payroll");
        $this->load->library('smsgateway');
        $this->load->library('mailsmsconf');
        $this->load->library('encoding_lib');
        $this->load->model("classteacher_model");
        $this->load->model(array("timeline_model", "student_edit_field_model", 'transportfee_model', 'marksdivision_model', 'module_model'));
        $this->blood_group        = $this->config->item('bloodgroup');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->role;
        $this->staff_attendance = $this->config->item('staffattendance');
    }



    public function index()
    {
        if (!$this->rbac->hasPrivilege('staff_referral', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Referral Application');
        $this->session->set_userdata('sub_menu', 'student_referral/index');
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;

        $resultlist          = $this->staff_model->searchFullText('', 1);
        $data['stafflist']  = $resultlist;

        $this->load->view('layout/header', $data);
        $this->load->view('student_referral/staff_referral_search', $data);
        $this->load->view('layout/footer', $data);

    }



    public function search()
    {
        $search_type = $this->input->post('search_type');
        if ($search_type == "class_search") {
            $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'required|trim|xss_clean');
            $this->form_validation->set_rules('reference_id', $this->lang->line('reference_staff'), 'required|trim|xss_clean');
        }
        if ($this->form_validation->run() == false) {
            $error = array();
            if ($search_type == "class_search") {
                $error['class_id'] = form_error('class_id');
                $error['reference_id']=form_error('reference_id');
            }

            $array = array('status' => 0, 'error' => $error);
            echo json_encode($array);
        } else {
            $search_type = $this->input->post('search_type');
            $class_id    = $this->input->post('class_id');
            $staff_id    = $this->input->post('reference_id');
            $section_id  = $this->input->post('section_id');
            $params      = array('class_id' => $class_id, 'reference_id' => $staff_id,'section_id' => $section_id, 'search_type' => $search_type);
            $array       = array('status' => 1, 'error' => '', 'params' => $params);
            echo json_encode($array);
        }
    }

    

    public function ajaxSearch()
    {
        $class       = $this->input->post('class_id');
        $section     = $this->input->post('section_id');
        $staff_id    = $this->input->post('reference_id');
        $search_type = $this->input->post('search_type');
        if ($search_type == "class_search") {
            $students = $this->student_model->getDatatableByClassSectionn($class, $section,$staff_id);
        }

        $sch_setting = $this->sch_setting_detail;
        $students    = json_decode($students);
        $dt_data     = array();
        if (!empty($students->data)) {
            foreach ($students->data as $student_key => $student) {
                $row         = array();
                $row[]       = $student->class;
                $row[]       = $student->section;
                $row[]       = $student->admission_no;
                $row[]       = "<a href='" . base_url() . "student/view/" . $student->id . "'>" . $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";
                $sch_setting = $this->sch_setting_detail;
                if ($sch_setting->father_name) {
                    $row[] = $student->father_name;
                }
                $row[] = $this->customlib->dateformat($student->dob);
                $row[] = $student->guardian_phone;
                $row[] = "<a href='" . site_url() . "admin/content/download_student_application/$student->id'  class='btn btn-default btn-xs'> <i class='fa fa-download'></i></a>"." <a href='" . site_url() . "admin/content/view_pdf/$student->id' target='_blank' class='btn btn-default btn-xs'> <i class='fa fa-file-pdf-o'></i>" ."</a>";

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "staffee"=>$staff_id,
            "draw"            => intval($students->draw),
            "recordsTotal"    => intval($students->recordsTotal),
            "recordsFiltered" => intval($students->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);

    }



}
