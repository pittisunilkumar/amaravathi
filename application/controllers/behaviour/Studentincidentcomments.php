<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Studentincidentcomments extends MY_Addon_BRController {

    function __construct() {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    /*
    This function is used to add comment
    */

    public function addmessage()
    {
        $userdata = $this->customlib->getUserData();
        $this->form_validation->set_rules('comment', $this->lang->line('comment'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $msg = array(
                'comment' => form_error('comment'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $data = array(
                'student_incident_id' => $this->input->post("student_incident_id"),
                'type'                => 'staff',
                'comment'             => $this->input->post("comment"),
                'staff_id'            => $userdata["id"],
                'created_date'        => date('Y-m-d H:i:s'),
            );

            $this->studentincidents_model->addmessage($data);
            $msg   = $this->lang->line('success_message');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }

        echo json_encode($array);
    }

    /*
    This function is used to get incident comment
    */
    public function getmessage()
    {
        $student_incident_id = $this->input->post("student_incident_id");
        $data['messagelist'] = $this->studentincidents_model->getmessage($student_incident_id);
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['student_incident_id'] = $student_incident_id;
        $userdata = $this->customlib->getUserData();
        $data['staff_id'] = $userdata["id"];
        
        $page                = $this->load->view('behaviour/student/_get_message', $data, true);
        $array               = array('status' => 'success', 'error' => '', 'page' => $page, 'message' => $this->lang->line('success_message'));
        echo json_encode($array);
    }
    
    /*
    This function is used to delete comment
    */
    public function delete_comment(){
        $id =   $_POST['id'];
        $this->studentincidents_model->delete_comment($id);
    }
}