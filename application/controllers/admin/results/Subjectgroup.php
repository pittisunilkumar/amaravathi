<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Subjectgroup extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('resultsubjectgroup_model');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('internal_result_subject_group', 'can_view')) {
            access_denied();
        }

        $json_array = array();
        $this->session->set_userdata('top_menu', 'Results');
        $this->session->set_userdata('sub_menu', 'admin/results/subjectgroup/index');


        $data['resulttypelist'] = $this->resultsubjectgroup_model->resulttype();

        $data['section_array'] = $json_array;



        $this->form_validation->set_rules('subject[]', $this->lang->line('subject'), 'trim|required|xss_clean');

        $this->form_validation->set_rules('fee_groups_id', $this->lang->line('resut_type'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

        } else {
            $name        = $this->input->post('fee_groups_id');
            $subject  = $this->input->post('subject');

            $subject_group_subject_Array = array();

            foreach ($subject as $sub_group_key => $sub_group_value) {
                $minmarks = $this->input->post('minmarks' . $sub_group_value);
                $maxmarks = $this->input->post('maxmarks' . $sub_group_value);

                $subject_data = array(
                    'resultsubjects_id' => $name,
                    'subject_id' => $sub_group_value,
                    'session_id' => $this->setting_model->getCurrentSession(),
                );

                // Add minimum and maximum marks to the subject data
                $subject_data['minmarks'] = $minmarks;
                $subject_data['maxmarks'] = $maxmarks;

                $subject_group_subject_Array[] = $subject_data;
            }

            // Insert the data into the 'resultsubject_group_subjects' table
            $this->db->insert_batch('resultsubject_group_subjects', $subject_group_subject_Array);
                
            
            // $this->resultsubjectgroup_model->add($name, $subject);

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            // $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $subject . '</div>');
            redirect('admin/results/subjectgroup');
        }

        $subject_list             = $this->resultsubjectgroup_model->subjects();
        $data['subjectlist']      = $subject_list;

        $subjectgroupList         = $this->resultsubjectgroup_model->getByID();
        $data['subjectgroupList'] = $subjectgroupList;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/results/subjectgroupList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('internal_result_subject_group', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Fees Master List';
        $this->resultsubjectgroup_model->remove($id);
        redirect('admin/results/subjectgroup');
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('internal_result_subject_group', 'can_edit')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Academics');
        $this->session->set_userdata('sub_menu', 'subjectgroup/index');
      
        $old_subjects      = array();
        $data['id']        = $id;

        $data['resulttypelist'] = $this->resultsubjectgroup_model->resulttype();

        $subject_list             = $this->resultsubjectgroup_model->subjects();
        $data['subjectlist']      = $subject_list;


        $subjectgroupList         = $this->resultsubjectgroup_model->getByID();
        $data['subjectgroupList'] = $subjectgroupList;
        $subjectgroup             = $this->resultsubjectgroup_model->getByID($id);

        if (!empty($subjectgroup[0]->group_subject)) {

            foreach ($subjectgroup[0]->group_subject as $key => $value) {

                $old_subjects[] = $value->subject_id;
            }
        }


        $data['subjectgroup'] = $subjectgroup;


        $this->form_validation->set_rules('fee_groups_id', $this->lang->line('resut_type'), 'trim|required|xss_clean');


        $this->form_validation->set_rules('subject[]', $this->lang->line('subject'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            if ($this->input->server('REQUEST_METHOD') == "POST") {
            }
            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/subjectgroupEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {

            $class_array = array(
                'id'          => $this->input->post('id'),
            );

            $subject         = $this->input->post('subject');
            
            $delete_subjects = array_diff($old_subjects, $subject);
            $add_subjects    = array_diff($subject, $old_subjects);



            if(!empty($add_subjects)){
                $subject_group_subject_Array = array();

                foreach ($add_subjects as $sub_group_key => $sub_group_value) {
                    $minmarks = $this->input->post('minmarks' . $sub_group_value);
                    $maxmarks = $this->input->post('maxmarks' . $sub_group_value);

                    $subject_data = array(
                        'resultsubjects_id' => $class_array['id'],
                        'subject_id' => $sub_group_value,
                        'session_id' => $this->setting_model->getCurrentSession(),
                    );

                    // Add minimum and maximum marks to the subject data
                    $subject_data['minmarks'] = $minmarks;
                    $subject_data['maxmarks'] = $maxmarks;

                    $subject_group_subject_Array[] = $subject_data;
                }

                // Insert the data into the 'resultsubject_group_subjects' table
                $this->db->insert_batch('resultsubject_group_subjects', $subject_group_subject_Array);
            }

            foreach ($subject as $subject_id) {
                $minmarks = $this->input->post('minmarks' . $subject_id);
                $maxmarks = $this->input->post('maxmarks' . $subject_id);
    
                // Update the marks in the database for this subject
                $this->resultsubjectgroup_model->updatemarks($class_array['id'], $subject_id, $minmarks, $maxmarks);
            }

            
            $this->resultsubjectgroup_model->edit($class_array,$delete_subjects, $add_subjects);
            
            redirect('admin/results/subjectgroup');

        }
    }

    public function addsubjectgroup()
    {
        if (!$this->rbac->hasPrivilege('internal_result_subject_group', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('subject_group_id', $this->lang->line('fee_group'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'subject_group_id' => form_error('subject_group_id'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $student_session_id     = $this->input->post('student_session_id');
            $subject_group_id       = $this->input->post('subject_group_id');
            $student_sesssion_array = isset($student_session_id) ? $student_session_id : array();
            $student_ids            = $this->input->post('student_ids');
            $delete_student         = array_diff($student_ids, $student_sesssion_array);

            $preserve_record = array();
            if (!empty($student_sesssion_array)) {
                foreach ($student_sesssion_array as $key => $value) {

                    $insert_array = array(
                        'student_session_id' => $value,
                        'subject_group_id'   => $subject_group_id,
                    );
                    $inserted_id       = $this->studentresultsubjectgroup_model->add($insert_array);
                    $preserve_record[] = $inserted_id;
                }
            }

            if (!empty($delete_student)) {
                $this->studentresultsubjectgroup_model->delete($subject_group_id, $delete_student);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function getGroupByClassandSection()
    {
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $session_id = $this->input->post('session_id');
        if(!isset($session_id)){
            $session_id=NULL;
        }
        $data       = $this->resultsubjectgroup_model->getGroupByClassandSection($class_id, $section_id,$session_id);
        echo json_encode($data);
    }

    public function getSubjectByClassandSectionDate()
    {
        $date       = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date')));
        $day        = date('l', strtotime($date));
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $data       = $this->subjecttimetable_model->getSubjectByClassandSectionDay($class_id, $section_id, $day);
        echo json_encode($data);
    }

    public function getAllSubjectByClassandSection()
    {
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $data       = $this->resultsubjectgroup_model->getAllsubjectByClassSection($class_id, $section_id);
        echo json_encode($data);
    }

    public function getSubjectByClassandSection()
    {
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $data       = $this->subjecttimetable_model->getSubjectByClassandSection($class_id, $section_id);
        echo json_encode($data);
    }

    public function getGroupsubjects()
    {
        $subject_group_id = $this->input->post('subject_group_id');
         $session_id = $this->input->post('session_id');
        if(!isset($session_id)){
            $session_id=NULL;
        }
        $data             = $this->resultsubjectgroup_model->getGroupsubjects($subject_group_id,$session_id);      
        echo json_encode($data);
    }

}
