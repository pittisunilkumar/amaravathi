<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Report extends MY_Addon_BRController {

    function __construct() {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    /*
    This function is used to show behaviour report dashboard
    */
    public function index() {         

        $this->session->set_userdata('top_menu', 'behaviour');
        $this->session->set_userdata('sub_menu', 'behaviour/behaviour_report');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('behaviour/report/behaviour_report');
        $this->load->view('layout/footer');
    }

    /*
    This function is used to show student incident report
    */
    public function studentincidentreport() 
    {
        if (!$this->rbac->hasPrivilege('student_incident_report', 'can_view')) {
            access_denied();
        }        
        $this->session->set_userdata('top_menu', 'behaviour');
        $this->session->set_userdata('sub_menu', 'behaviour/behaviour_report');
        $this->session->set_userdata('subsub_menu', 'behaviour/student_incident_report');
        $data['classlist'] = $this->class_model->get();
        $this->load->view('layout/header');
        $this->load->view('behaviour/report/studentincidentreport', $data);
        $this->load->view('layout/footer');
    }

    /*
    This function is used to send parameter to get Student Incident List
    */
    public function search()
    {
        $class_id    = $this->input->post('class_id');
        $section_id  = $this->input->post('section_id');
        $session_id  = $this->input->post('session_id');

        if($session_id == 'current_session'){
            $session_value = 'current_session';
        }else{
            $session_value = 'overall';
        }

        $params      = array('class_id' => $class_id, 'section_id' => $section_id, 'session_value' => $session_value);
        $array       = array('status' => 1, 'error' => '', 'params' => $params);
        echo json_encode($array);
    }

    /*
    This function is used to get student list and show on datatable
    */
    public function dtstudentlist($session=null)
    {
        $class           = $this->input->post('class_id');
        $section         = $this->input->post('section_id');
        $session_value   = $this->input->post('session_value');

        if($session_value !=""){
            $session_value = $session_value;
        }else{
            $session_value = $session;
        }

        $sch_setting = $this->sch_setting_detail;
        $resultlist = $this->student_model->searchdtByClassSection($class, $section);
 
        $students = array();
        $students = json_decode($resultlist);
        $dt_data  = array();
        if (!empty($students->data)) {
            foreach ($students->data as $student_key => $student) {

                $viewbtn = "<a href='#' data-placement='left' data-student-id=".$student->id." data-session-value=".$session_value." data-toggle='modal' data-backdrop='static' class='btn btn-default btn-xs assignstudent'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

                $total_points = $this->studentincidents_model->totalpointsbysession($session_value, $student->id);
              
                $row   = array();
                
                $row[] = $student->admission_no;
                $row[] = "<a href='" . base_url() . "student/view/" . $student->id . "'>" . $this->customlib->getFullName($student->firstname, $student->middlename, $student->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";
                $row[] = $student->class . " (" . $student->section . ")";
                $row[] = $student->gender;
                $row[] = $student->mobileno;
                $row[] = $total_points['total_incidents'];
                $row[] = $total_points['totalpoints'];
                $row[] = $viewbtn;

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
    This function is used to get student incident based on student id and session for datatable
    */
    public function assignstudent($student_id,$session_value)
    {
        $superadmin_visible = $this->customlib->superadmin_visible();
        $sch_setting = $this->sch_setting_detail;
        $assignstudentlist            = $this->studentincidents_model->assignstudent($student_id, $session_value);
        $assignstudentlist            = json_decode($assignstudentlist);

        $dt_data=array();
        if (!empty($assignstudentlist->data)) {
            foreach ($assignstudentlist->data as $key => $value) {
                $staff_id = '';
                if($value->staff_employee_id !=""){
                    $staff_id = ' ('.$value->staff_employee_id.')';
                }

                $row   = array();
                
                $row[] = $value->title;
                $row[] = $value->point;
                $row[] = $value->session;
                $row[] = $this->customlib->dateformat($value->created_at);
                $row[] = $value->description;           
                
                if($superadmin_visible == 'disabled' && $value->role_id == 7){
                    $row[]     = '';               
                }else{
                    $row[] = $value->staff_name.' '.$value->staff_surname.$staff_id;
                }
                
                $dt_data[] = $row;  
            }
        }
        $json_data = array(
            "draw"            => intval($assignstudentlist->draw),
            "recordsTotal"    => intval($assignstudentlist->recordsTotal),
            "recordsFiltered" => intval($assignstudentlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data); 
    }

    /*
    This function is used to show student behaviour rank report
    */
    public function studentbehaviorsrankreport() 
    {
        if (!$this->rbac->hasPrivilege('student_behaviour_rank_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'behaviour');
        $this->session->set_userdata('sub_menu', 'behaviour/behaviour_report');
        $this->session->set_userdata('subsub_menu', 'behaviour/student_rank_report');
        $data['classlist'] = $this->class_model->get();
        $this->load->view('layout/header');
        $this->load->view('behaviour/report/studentbehaviorsrankreport', $data);
        $this->load->view('layout/footer');
    }

    /*
    This function is used to send parameter to get student rank report
    */
    public function studentranksearch()
    {
        $class_id    = $this->input->post('class_id');
        $section_id  = $this->input->post('section_id');
        $session_id  = $this->input->post('session_id');
        $type  = $this->input->post('type');
        $point  = $this->input->post('point');

        if($session_id == 'current_session'){
            $session_value = 'current_session';
        }else{
            $session_value = 'overall';
        }

        if($type !=''){
            $this->form_validation->set_rules('point', $this->lang->line('point'), 'trim|required|numeric|xss_clean');
        }
        
        if ($this->form_validation->run() == false) {
            if($type !=''){
                $msg = array(
                    'point' => form_error('point'),
                );
                $array = array('status' => 0, 'error' => $msg, 'message' => '');
            }else{
                $params      = array('class_id' => $class_id, 'section_id' => $section_id, 'session_value' => $session_value, 'type' => $type, 'point' => $point);
                $array       = array('status' => 1, 'error' => '', 'params' => $params);
            }
        } else {

            $params      = array('class_id' => $class_id, 'section_id' => $section_id, 'session_value' => $session_value, 'type' => $type, 'point' => $point);
            $array       = array('status' => 1, 'error' => '', 'params' => $params);
        }

        echo json_encode($array);
    }

    /*
    This function is used to get student rank report 
    */
    public function dtstudentranklist()
    {
        $class           = $this->input->post('class_id');
        $section         = $this->input->post('section_id');
        $session_value   = $this->input->post('session_value');
        $type    = $this->input->post('type');
        $point   = $this->input->post('point');

        $sch_setting = $this->sch_setting_detail;
        $ranklist = $this->studentincidents_model->studentrank($class, $section, $session_value, $type, $point);
 
        $ranklists = array();
        $ranklists = json_decode($ranklist);
        $result = $ranklists->data;
        $dt_data  = array();
        if (!empty($result)) {  $totalpoints =''; $rank =0;
            foreach ($result as $rank_key => $value) {          
                      
                if ($rank_key != 0) {                                
                        $totalpoints =  $result[$rank_key - 1]->totalpoints ;
                }

                if($totalpoints == $value->totalpoints){
                    $rank = $rank;
                }else{
                    $rank = $rank + 1;
                }                

                $viewbtn = "<a href='#' data-placement='left' data-student-id=".$value->id." data-session-value=".$session_value." data-toggle='modal' data-backdrop='static' class='btn btn-default btn-xs assignstudent'  data-toggle='tooltip' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

                $row   = array();

                $row[] = $rank;
                $row[] = $value->admission_no;
                $row[] = "<a href='" . base_url() . "student/view/" . $value->id . "'>" . $this->customlib->getFullName($value->firstname, $value->middlename, $value->lastname, $sch_setting->middlename, $sch_setting->lastname) . "</a>";
                $row[] = $value->class . " (" . $value->section . ")";
                $row[] = $value->gender;
                $row[] = $value->mobileno;
                $row[] = $value->totalpoints;
                $row[] = $viewbtn;

                $dt_data[] = $row;
            }
        }
      
        $json_data           = array(
            "draw"                => intval($ranklists->draw),
            "recordsTotal"        => intval($ranklists->recordsTotal),
            "recordsFiltered"     => intval($ranklists->recordsFiltered),
            "data"                => $dt_data,
        );

        echo json_encode($json_data);
    }

    /*
    This function is used to get assign student rank list by student id or session like current or overall
    */
    public function assignstudentrank($student_id,$session_value)
    {
        $superadmin_visible = $this->customlib->superadmin_visible();
        $sch_setting = $this->sch_setting_detail;
        $assignstudentlist            = $this->studentincidents_model->assignstudent($student_id, $session_value);
        $assignstudentlist            = json_decode($assignstudentlist);

        $dt_data=array();
        if (!empty($assignstudentlist->data)) {
            foreach ($assignstudentlist->data as $key => $value) {
                $staff_id = '';
                if($value->staff_employee_id !=""){
                    $staff_id = ' ('.$value->staff_employee_id.')';
                }

                $row   = array();
                
                $row[] = $value->title;
                $row[] = $value->point;
                $row[] = $value->session;
                $row[] = $this->customlib->dateformat($value->created_at);
                $row[] = $value->description;                
                
                if($superadmin_visible == 'disabled' && $value->role_id == 7){
                    $row[]     = '';               
                }else{
                    $row[] = $value->staff_name.' '.$value->staff_surname.$staff_id;
                }
                
                $dt_data[] = $row;  
            }
        }
        $json_data = array(
            "draw"            => intval($assignstudentlist->draw),
            "recordsTotal"    => intval($assignstudentlist->recordsTotal),
            "recordsFiltered" => intval($assignstudentlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data); 
    }

 
    /*
    This function is used to get class wise rank record
    */
    public function classwiserankreport() 
    {
        if (!$this->rbac->hasPrivilege('class_wise_rank_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'behaviour');
        $this->session->set_userdata('sub_menu', 'behaviour/behaviour_report');
        $this->session->set_userdata('subsub_menu', 'behaviour/classwise_rank_report');
        $data['classwiserank'] = $this->studentincidents_model->classwiserank();
        $this->load->view('layout/header');
        $this->load->view('behaviour/report/classwiserankreport',$data);
        $this->load->view('layout/footer');
    }

    /*
    This function is used to get class wise point record
    */
    public function classwisepoint() {
        $class_id = $this->input->post('class_id');
        $classwisepoint = $this->studentincidents_model->classwisepoint($class_id);
        $data['classwisepoint'] = $classwisepoint;
        $data['sch_setting'] = $this->sch_setting_detail;
        $page = $this->load->view('behaviour/report/_classwisepoint',$data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    /*
    This function is used to get class section wise rank record
    */
    public function classsectionwiserank() 
    {
        if (!$this->rbac->hasPrivilege('class_section_wise_rank_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'behaviour');
        $this->session->set_userdata('sub_menu', 'behaviour/behaviour_report');
        $this->session->set_userdata('subsub_menu', 'behaviour/classsectionwise');
        $data['classsectionwise'] = $this->studentincidents_model->classsectionwiserank();
        $this->load->view('layout/header');
        $this->load->view('behaviour/report/classsectionwiserank',$data);
        $this->load->view('layout/footer');
    }

    /*
    This function is used to get class section wise point record
    */
    public function classsectionwisepoint() {
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $classsectionpoint = $this->studentincidents_model->classsectionwisepoint($class_id, $section_id);
        $data['classsectionpoint'] = $classsectionpoint;
        $data['sch_setting'] = $this->sch_setting_detail;
        $page = $this->load->view('behaviour/report/_classsectionwisepoint',$data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    /*
    This function is used to get house wise rank record
    */
    public function housewiserank() 
    {
        if (!$this->rbac->hasPrivilege('house_wise_rank_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'behaviour');
        $this->session->set_userdata('sub_menu', 'behaviour/behaviour_report');
        $this->session->set_userdata('subsub_menu', 'behaviour/housewisereport');
        $data['housewise'] = $this->studentincidents_model->housewiserank();
        $this->load->view('layout/header');
        $this->load->view('behaviour/report/housewisereport',$data);
        $this->load->view('layout/footer');
    }

    /*
    This function is used to get house wise point record
    */
    public function housewisepoint() {
        $house_id = $this->input->post('house_id');
        $housewisepoint = $this->studentincidents_model->housewisepoint($house_id);
        $data['housewisepoint'] = $housewisepoint;
        $data['sch_setting'] = $this->sch_setting_detail;
        $page = $this->load->view('behaviour/report/_housewisepoint',$data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    /*
    This function is used to get incident wise rank record
    */
    public function incidentwisereport() 
    {
        if (!$this->rbac->hasPrivilege('incident_wise_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'behaviour');
        $this->session->set_userdata('sub_menu', 'behaviour/behaviour_report');
        $this->session->set_userdata('subsub_menu', 'behaviour/incidentwisereport');

        $data = array();
        $incident = array();
         
        $session_type = $this->input->post('session_type');
        if(isset($_POST['search'])){            
            
            if($session_type == 'current_session'){
                $session_value = 'current_session';
            }else{
                $session_value = 'overall';
            }

            $incident = $this->studentbehaviour_model->get();

            foreach ($incident as $key => $incident_value) {
                $incident[$key]['total_student'] = count($this->studentincidents_model->studentcount($incident_value['id'], $session_value)); 
            }          
            
        }else{
            
            $session_value = 'current_session';
            $incident = $this->studentbehaviour_model->get();

            foreach ($incident as $key => $incident_value) {
                $incident[$key]['total_student'] = count($this->studentincidents_model->studentcount($incident_value['id'], $session_value)); 
            }     
        }
        
        $data['incidentgraph'] = $incident;
        $data['session_type'] = $session_type;
        
        $this->load->view('layout/header');
        $this->load->view('behaviour/report/incidentwisereport',$data);
        $this->load->view('layout/footer');
    }

    /*
    This function is used to get total count of student by incident id or session  type
    */
    public function studentdetails() {
        $incident_id = $this->input->post('incident_id');
        $session_type = $this->input->post('session_type');
        $data['studentlist'] = $this->studentincidents_model->studentcount($incident_id,$session_type);         
        $data['sch_setting'] = $this->sch_setting_detail;
        $page = $this->load->view('behaviour/report/_studentlist',$data, true);
        echo json_encode(array('page' => $page));
    }
}