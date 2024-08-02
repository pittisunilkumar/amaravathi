<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admino extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('smsgateway');
        $this->load->library('mailsmsconf');
        $this->load->library('customlib');
        $this->load->library('media_storage');
        $this->load->model("module_model");
        $this->search_type        = $this->config->item('search_type');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('collect_fees', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', $this->lang->line('fees_collection'));
        $this->session->set_userdata('sub_menu', 'studentfee/index');
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['title']       = 'student fees';
        $class               = $this->class_model->get();
        $data['classlist']   = $class;
        $progresslist            = $this->customlib->checkadminstatus();
        $data['progresslist']    = $progresslist;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/hallticket/admissionnocheck', $data);
        $this->load->view('layout/footer', $data);
    }


    public function search(){
        
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('progress_id', $this->lang->line('admi_status'), 'required|trim|xss_clean');
        $this->load->view('layout/header', $data);
        $this->load->view('admin/hallticket/admissionnocheck', $data);
        $this->load->view('layout/footer', $data);
    }



    // public function search()
    // {
    //     $search_type = $this->input->post('search_type');
    //     if ($search_type == "class_search") {
    //         $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'required|trim|xss_clean');
    //         $this->form_validation->set_rules('progress_id', $this->lang->line('admi_status'), 'required|trim|xss_clean');
    //     }
    //     if ($this->form_validation->run() == false) {
    //         $error = array();
    //         if ($search_type == "class_search") {
    //             $error['class_id'] = form_error('class_id');
    //             $error['progress_id'] = form_error('progress_id');
    //         }
    //         $array = array('status' => 0, 'error' => $error);
    //         echo json_encode($array);
    //     } else {
    //         $search_type = $this->input->post('search_type');
    //         $class_id    = $this->input->post('class_id');
    //         $section_id  = $this->input->post('section_id');

    //         $admistatus   = $this->input->post('progress_id');

    //         $params      = array('class_id' => $class_id, 'admistatus' => $admistatus ,'section_id' => $section_id, 'search_type' => $search_type);
    //         $array       = array('status' => 1, 'error' => '', 'params' => $params);
    //         echo json_encode($array);
    //     }
    // }

    
    public function ajaxSearch()
    {
        $class       = $this->input->post('class_id');
        $section     = $this->input->post('section_id');
        $search_type = $this->input->post('search_type');
        $admistatus   = $this->input->post('admistatus');
        if($admistatus=="withadmissionno"){
            $vall=1;
        }
        if($admistatus=="noadmissionno"){
            $vall=0;
        }

        if ($search_type == "class_search") {
            $students = $this->student_model->admissionnostatusgetDatatableByClassSection($class, $section,$vall);
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


                if($vall==0){
                    $row[] = "
                        <a href=" . site_url('studentfee/addfee/' . $student->student_session_id) . "  class='btn btn-info btn-xs'>" . $this->lang->line('add_admi_no') . "</a>
                    ";
                }
                if ($vall==1) {
                    //$row[] = "<a class='btn btn-default btn-xs'> <i class='fa fa-reorder'></i></a>"."<a class='btn btn-default btn-xs'> <i class='fa fa-pencil'></i>" ."</a>";
                    $row[] = "<a href=" . site_url('student/view/' . $student->id) . " class='btn btn-default btn-xs'> <i class='fa fa-reorder'></i></a>" . "<a href=" . site_url('student/edit/' . $student->id) . " class='btn btn-default btn-xs'> <i class='fa fa-pencil'></i></a>" ;

                }

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($students->draw),
            "recordsTotal"    => intval($students->recordsTotal),
            "recordsFiltered" => intval($students->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);

    }


}
