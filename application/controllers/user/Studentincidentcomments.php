<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Studentincidentcomments extends Student_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("studentincidents_model");
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function addmessage()
    {
        $student_id = $this->customlib->getStudentSessionUserID();
        $role    = $this->customlib->getUserRole();  
        $this->form_validation->set_rules('comment', $this->lang->line('comment'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            $msg = array(
                'comment' => form_error('comment'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');

        } else {

            $data = array(
                'student_incident_id' => $this->input->post("student_incident_id"),
                'type'                => $role,
                'comment'             => $this->input->post("comment"),
                'student_id'          => $student_id,
                'created_date'        => date('Y-m-d H:i:s'),
            );

            $this->studentincidents_model->addmessage($data);

            $msg   = $this->lang->line('success_message');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }

        echo json_encode($array);
    }

    public function getmessage()
    {
        $student_incident_id = $this->input->post("student_incident_id");
        $data['messagelist'] = $this->studentincidents_model->getmessage($student_incident_id);
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['student_id'] = $this->customlib->getStudentSessionUserID();
        $data['student_incident_id'] = $student_incident_id;
        $data['role']    = $this->customlib->getUserRole();         
        $page                = $this->load->view('user/behaviour/_get_message', $data, true);
        $array               = array('status' => 'success', 'error' => '', 'page' => $page, 'message' => $this->lang->line('success_message'));
        echo json_encode($array);
    }
    
    public function delete_comment(){
        $id =   $_POST['id'];
        $this->studentincidents_model->delete_comment($id);
    }
}