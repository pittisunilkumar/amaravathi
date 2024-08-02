<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Studentincidents extends MY_Addon_BRController {

    function __construct() {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    /*
    This function is used for show assign incident list
    */
    public function index() {

        if (!$this->rbac->hasPrivilege('behaviour_records_assign_incident', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'behaviour');
        $this->session->set_userdata('sub_menu', 'behaviour/studentincidents');
        $data['classlist'] = $this->class_model->get();
        $data['incidentlist']   = $this->studentbehaviour_model->get();
        $this->load->view('layout/header');
        $this->load->view('behaviour/studentincidents/assignincidentlist', $data);
        $this->load->view('layout/footer');
    }

    /*
    This function is used to send search parameter for assign incident
    */
    public function search()
    {
        $class_id    = $this->input->post('class_id');
        $section_id  = $this->input->post('section_id');
        $params      = array('class_id' => $class_id, 'section_id' => $section_id);
        $array       = array('status' => 1, 'error' => '', 'params' => $params);
        echo json_encode($array);
    }

    /*
    This function is used to search assign incident list based on class or section for datatable
    */
    public function dtassignincidentlist($class_id=NULL,$section_id=NULL)
    {
        $class       = $this->input->post('class_id');
        $section     = $this->input->post('section_id');
        $sch_setting = $this->sch_setting_detail;

        if($class_id !=NULL){
            $class = $class_id;
        }else{
            $class = $class;
        }

        if($section_id !=NULL){
            $section = $section_id;
        }else{
            $section = $section;
        }

        $resultlist  = $this->student_model->searchdtByClassSection($class, $section);
 
        $students = array();
        $students = json_decode($resultlist);
        $dt_data  = array();
        $viewbtn = '';
        $assignedview = '';
        if (!empty($students->data)) {
            foreach ($students->data as $student_key => $student) {
                if($this->rbac->hasPrivilege('behaviour_records_assign_incident', 'can_add')){
                    $viewbtn = "<a href='#' data-placement='left' data-student-id=".$student->id." data-toggle='modal' data-backdrop='static' class='btn btn-default btn-xs assignstudent'  data-toggle='tooltip' title='".$this->lang->line('assign_incident')."'><i class='fa fa-plus'></i></a>";
                }
                if($this->rbac->hasPrivilege('behaviour_records_assign_incident', 'can_view')){
                    $assignedview = "<a href='#' data-placement='left' data-student-id=".$student->id." data-toggle='modal' data-backdrop='static' class='btn btn-default btn-xs viewassignedincidents'  data-toggle='tooltip' title='".$this->lang->line('view_assigned_incidents')."'><i class='fa fa-reorder'></i></a>";
                }
                $total_points = $this->studentincidents_model->totalpoints($student->id);                
                            
                $row   = array();
                $row[] = "<a href='" . base_url() . "student/view/" . $student->id . "'>" . $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";
                $row[] = $student->admission_no;
                $row[] = $student->class . "(" . $student->section . ")";
                $row[] = $student->gender;
                $row[] = $student->mobileno;
                $row[] = $total_points['totalpoints'];
                $row[] = $viewbtn.' '. $assignedview ;

                $dt_data[] = $row;
            }

        }
      
        $json_data           = array(
            "draw"                => intval($students->draw),
            "recordsTotal"        => intval($students->recordsTotal),
            "recordsFiltered"     => intval($students->recordsFiltered),
            "data"                => $dt_data,
        );

        echo json_encode($json_data);
    }

    /*
    This function is used to assign incident to student
    */
    public function create()
    {
        $this->form_validation->set_rules('incident_id[]', '', 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {

            $msg = array(
                'incident_id[]' => $this->lang->line('atleast_one_incident_should_be_checked'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');

        } else {

            $incident_id = $this->input->post('incident_id');
            $student_id = $this->input->post('student_id');
            $userdata           = $this->customlib->getUserData();

            $studentdetails = $this->student_model->get($student_id);

            foreach ($incident_id as $key => $incident_id_value) {
                $data = array(
                    'student_id'   => $this->input->post("student_id"),
                    'session_id'   => $this->setting_model->getCurrentSession(),
                    'incident_id'  => $incident_id_value,
                    'assign_by'    => $userdata["id"],
                );

                $this->studentincidents_model->add($data);
                $incidentdetail = $this->studentbehaviour_model->get($incident_id_value);
                
                $student_detail = array('id' => $studentdetails['id'],  'incident_title' => $incidentdetail['title'], 'incident_point' => $incidentdetail['point'],'student_name' => $studentdetails['firstname'].' '.$studentdetails['lastname'], 'class' => $studentdetails['class'] ,  'section' => $studentdetails['section'],  'admission_no' => $studentdetails['admission_no'],  'mobileno' => $studentdetails['mobileno'] ,  'email' => $studentdetails['email'] ,  'guardian_name' => $studentdetails['guardian_name'], 'guardian_phone' => $studentdetails['guardian_phone'], 'guardian_email' => $studentdetails['guardian_email'], 'parent_app_key' => $studentdetails['parent_app_key'], 'app_key' => $studentdetails['app_key'] ); 
                
                $this->behaviour_mail_sms->mailsms('behaviour_incident_assigned', $student_detail);                
            }

            $msg   = $this->lang->line('success_message');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg , 'student_id' => $student_id);
        }

        echo json_encode($array);
    }

    /*
    This function is used to show assigned incident list based on student id
    */
    public function viewassignedincidentslist()
    {
        $getStaffRole       = $this->customlib->getStaffRole();
        $data['staffrole']  = json_decode($getStaffRole);       
        $student_id = $this->input->post('student_id');
        $data['superadmin_visible'] = $this->customlib->superadmin_visible();
        $data['assignstudentlist']  = $this->studentincidents_model->studentbehaviour($student_id);
        $page = $this->load->view('behaviour/studentincidents/_viewassignedincidentslist', $data , true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    /*
    This function is used to delete assigned incident by student id and assigned id
    */
    public function delete() {
        
        $student_id = $this->input->post('student_id');
        $id = $this->input->post('assigned_id');
        $this->studentincidents_model->delete($id);
        echo json_encode(array('student_id' => $student_id, 'message' => $this->lang->line('delete_message')));
    }
}