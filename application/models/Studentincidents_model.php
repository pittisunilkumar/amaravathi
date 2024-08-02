<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studentincidents_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /*
    This function is used to insert or update student incidents record
    */
    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('student_incidents', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  student incidents id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id']; 
            $this->log($message, $record_id, $action);            
        } else {            
            $this->db->insert('student_incidents', $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On student incidents id " . $insert_id;
            $action    = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);           
        }
        //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                 return $record_id;
            }           
    }

    /*
    This function is used to get assign student record by student id and session for datatable
    */
    public function assignstudent($student_id=NULL, $session_value=NULL) 
    {
        if($session_value == 'current_session'){
            $this->datatables->where('student_incidents.session_id', $this->current_session);
            $this->datatables->where('student_incidents.student_id', $student_id);
        }else{
            $this->datatables->where('student_incidents.student_id', $student_id);
        }
        
        $this->datatables->where('students.is_active =', 'yes');
        $this->datatables->select('student_behaviour.title,student_behaviour.point,student_behaviour.description,student_incidents.created_at,students.id as student_id,students.firstname,students.middlename,students.lastname,students.admission_no,sessions.session,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,staff_roles.role_id')->from('student_incidents')
        ->join('students','students.id=student_incidents.student_id')
        ->join('student_behaviour','student_behaviour.id=student_incidents.incident_id')
        ->join('sessions','sessions.id=student_incidents.session_id')
        ->join('staff','staff.id=student_incidents.assign_by')
        ->join('staff_roles', 'staff_roles.staff_id=staff.id','left')
        ->searchable('student_behaviour.title," ",sessions.session," ",student_behaviour.description," "')
        ->orderable('student_behaviour.title," ",sessions.session," ",student_behaviour.description," "');
        return $this->datatables->generate('json');
    }

    /*
    This function is used to get student total points by student id
    */
    public function totalpoints($student_id){
        $this->db->select('sum(point) as totalpoints');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id=student_incidents.incident_id');
        $this->db->where('student_incidents.session_id', $this->current_session);
        $this->db->where('student_incidents.student_id', $student_id);
        $result = $this->db->get();
        return $result->row_array();
    }

    /*
    This function is used to get student total points of session by student id and session
    */
    public function totalpointsbysession($session_value=NULL, $student_id=NULL){

        if($session_value == 'current_session'){
            $this->db->where('student_incidents.session_id', $this->current_session);
            $this->db->where('student_incidents.student_id', $student_id);
        }else{
            $this->db->where('student_incidents.student_id', $student_id);
        }

        $this->db->select('sum(point) as totalpoints, count(student_incidents.id) as total_incidents');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id=student_incidents.incident_id');
        $result = $this->db->get();
        return $result->row_array();
    }

    /*
    This function is used to get student rank record by class id, section id, session, type, points for datatable
    */
    public function studentrank($class_id=NULL, $section_id=NULL, $session_value=NULL, $type=NULL, $point=NULL){
        $sign = '';
        if($type == 'greaterthanequal'){
            if($point !=NULL){
                $sign = '>='.$point;
            }
        }else{
            if($point !=NULL){
                $sign = '<='.$point;
            }
        }

        $where = '';
        if($session_value == 'current_session'){
            $where .= ' And `student_incidents`.`session_id` = '.$this->current_session;
        }

        if($class_id != NULL){
            $where .= ' And `student_session`.`class_id` = '.$class_id;
        }

        if($section_id != NULL){
            $where .= ' And `student_session`.`section_id` = '.$section_id;
        }

          $userdata = $this->customlib->getUserData();

 if (($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes") && (empty($class_section_array))) {
            $class_section_array = $this->customlib->get_myClassSection();
        }

         
 if (!empty($class_section_array)) {
           $where .= " and (";
           $s=0;
            foreach ($class_section_array as $class_sectionkey => $class_sectionvalue) {
                $where .= "(";
                foreach ($class_sectionvalue as $class_sectionvaluekey => $class_sectionvaluevalue) {
                   $where .= "student_session.class_id=" . $class_sectionkey . " and student_session.section_id=" . $class_sectionvaluevalue;
                    
                }
               
             
                    $s++;
                if($s==(count($class_section_array))){
                $where .= ")";
                }else{
                   $where .= ") or ";  
                }
                 
                }

             

$where .= ")"; 
            }
            
        
       
   
       $sql = "SELECT sum(student_behaviour.point) as totalpoints, `students`.`id`, `students`.`firstname`, `students`.`middlename`, `students`.`lastname`, `students`.`admission_no`, `students`.`mobileno`, `students`.`gender`, classes.class,sections.section FROM `student_incidents` JOIN `student_behaviour` ON `student_behaviour`.`id`=`student_incidents`.`incident_id` JOIN `students` ON `students`.`id`=`student_incidents`.`student_id` JOIN student_session ON (student_session.student_id = students.id AND student_session.session_id = $this->current_session) JOIN classes on classes.id = student_session.class_id JOIN sections ON sections.id = student_session.section_id WHERE  students.is_active= 'yes' $where GROUP BY `student_incidents`.`student_id` HAVING SUM(student_behaviour.point) $sign";

        $this->datatables->query($sql)
            ->searchable('" ",students.firstname,students.admission_no,classes.class,students.gender,students.mobileno,totalpoints," "')
            ->orderable('" ",students.firstname,students.admission_no,classes.class,students.gender,students.mobileno,totalpoints," "') 
            ->sort('totalpoints','desc') 
            ->query_where_enable(TRUE);
            
        return $this->datatables->generate('json');
    }

    /*
    This function is used to get student behaviour record for datatable
    */
    public function incident() {
        $this->datatables->select('*')->from('student_behaviour')->searchable('" ",title,point,description')
            ->orderable('" ",title,point,description');
        return $this->datatables->generate('json');
    }

    /*
    This function is used to get student class wise rank record for datatable
    */
    public function classwiserank() {
        $userdata = $this->customlib->getUserData();
        if (($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes") && (empty($class_section_array))) {
            $class_section_array = $this->customlib->get_myClassSection();
        }
        
        $this->db->select('sum(student_behaviour.point) as totalpoints,classes.id as class_id, classes.class,student_incidents.session_id,student_incidents.student_id');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id=student_incidents.incident_id');
        $this->db->join('students', 'students.id=student_incidents.student_id');
        $this->db->join('student_session', 'student_session.student_id=students.id AND student_session.session_id='.$this->current_session);
        $this->db->join('classes', 'classes.id=student_session.class_id'); 
        if (!empty($class_section_array)) {
            $this->datatables->group_start();
            foreach ($class_section_array as $class_sectionkey => $class_sectionvalue) {
                foreach ($class_sectionvalue as $class_sectionvaluekey => $class_sectionvaluevalue) {
                    $this->datatables->or_group_start();
                    $this->datatables->where('student_session.class_id', $class_sectionkey);
                    $this->datatables->where('student_session.section_id', $class_sectionvaluevalue);
                    $this->datatables->group_end();

                }
            }
            $this->datatables->group_end();
        }
        $this->db->where('student_incidents.session_id', $this->current_session);
        $this->db->where('students.is_active =', 'yes');        
        $this->db->group_by('student_incidents.session_id,classes.id');
        $this->db->order_by('totalpoints','DESC');
        $result = $this->db->get();
        $result = $result->result_array();

        foreach ($result as $key => $value) {
            $result[$key]['total_student'] = count($this->totalstudent($value['class_id']));
        }
        return $result;
    }

    /*
    This function is used to get total student record by class id and section id
    */
    public function totalstudent($class_id=NULL, $section_id = NULL) {
        $this->db->select('student_incidents.id');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id=student_incidents.incident_id');
        $this->db->join('students', 'students.id=student_incidents.student_id');
        $this->db->join('student_session', 'student_session.student_id=students.id AND student_session.session_id='.$this->current_session);
        $this->db->join('classes', 'classes.id=student_session.class_id');

        if($section_id !=NULL){
            $this->db->join('sections', 'sections.id=student_session.section_id');
            $this->db->where('sections.id', $section_id);
        }
        $this->db->where('students.is_active =', 'yes');
        $this->db->where('student_incidents.session_id', $this->current_session);
        $this->db->where('classes.id', $class_id);
        $this->db->group_by('students.id');
        $result = $this->db->get();
        return $result->result_array();
    }

    /*
    This function is used to get student class wise point record by class id
    */
    public function classwisepoint($class_id=NULL) {
        $this->db->select('classes.class, sections.section, student_behaviour.point, student_behaviour.title, students.id as student_id, students.firstname, students.middlename, students.lastname, students.admission_no');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id=student_incidents.incident_id');
        $this->db->join('students', 'students.id=student_incidents.student_id');
        $this->db->join('student_session', 'student_session.student_id=students.id AND student_session.session_id='.$this->current_session);
        $this->db->join('classes', 'classes.id=student_session.class_id');
        $this->db->join('sections', 'sections.id=student_session.section_id');
        $this->db->where('students.is_active =', 'yes');
        $this->db->where('student_incidents.session_id', $this->current_session);
        $this->db->where('classes.id', $class_id);
        $this->db->group_by('students.id');
        $result = $this->db->get();
        $result = $result->result_array();

        foreach ($result as $key => $result_value) {
            $result[$key]['incident'] = $this->studentincidents_model->pointbystudent($result_value['student_id']);
        }

        return $result;
    }

    /*
    This function is used to get student class section wise rank record
    */
    public function classsectionwiserank() {
        $userdata = $this->customlib->getUserData();
        if (($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes") && (empty($class_section_array))) {
            $class_section_array = $this->customlib->get_myClassSection();
        }
        
        $this->db->select('sum(student_behaviour.point) as totalpoints, classes.id as class_id, sections.id as section_id, classes.class,sections.section,student_incidents.session_id,student_incidents.student_id');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id=student_incidents.incident_id');
        $this->db->join('students', 'students.id=student_incidents.student_id');
        $this->db->join('student_session', 'student_session.student_id=students.id AND student_session.session_id='.$this->current_session);
        $this->db->join('classes', 'classes.id=student_session.class_id');
        $this->db->join('sections', 'sections.id=student_session.section_id');
        if (!empty($class_section_array)) {
            $this->datatables->group_start();
            foreach ($class_section_array as $class_sectionkey => $class_sectionvalue) {
                foreach ($class_sectionvalue as $class_sectionvaluekey => $class_sectionvaluevalue) {
                    $this->datatables->or_group_start();
                    $this->datatables->where('student_session.class_id', $class_sectionkey);
                    $this->datatables->where('student_session.section_id', $class_sectionvaluevalue);
                    $this->datatables->group_end();

                }
            }
            $this->datatables->group_end();
        }
        $this->db->where('students.is_active =', 'yes');
        $this->db->where('student_incidents.session_id', $this->current_session);
        $this->db->group_by('student_incidents.session_id,classes.id,sections.id');
        $this->db->order_by('totalpoints','DESC');
        $result = $this->db->get();
        $result = $result->result_array();
        
        foreach ($result as $key => $value) {
            $result[$key]['total_student'] = count($this->totalstudent($value['class_id'], $value['section_id']));
        }
        return $result;
    }

    /*
    This function is used to get student class section wise point record by class id and section id
    */
    public function classsectionwisepoint($class_id=NULL, $section_id=NULL) {
        $this->db->select('classes.class, sections.section, student_behaviour.point, student_behaviour.title, students.id as student_id, students.firstname, students.middlename, students.lastname, students.admission_no');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id=student_incidents.incident_id');
        $this->db->join('students', 'students.id=student_incidents.student_id');
        $this->db->join('student_session', 'student_session.student_id=students.id AND student_session.session_id='.$this->current_session);
        $this->db->join('classes', 'classes.id=student_session.class_id');
        $this->db->join('sections', 'sections.id=student_session.section_id');
        $this->db->where('students.is_active =', 'yes');
        $this->db->where('sections.id', $section_id);
        $this->db->where('student_incidents.session_id', $this->current_session);
        $this->db->where('classes.id', $class_id);
        $this->db->group_by('students.id');
        $result = $this->db->get();
        $result = $result->result_array();

        foreach ($result as $key => $result_value) {
            $result[$key]['incident'] = $this->studentincidents_model->pointbystudent($result_value['student_id']);
        }
        return $result;
    }

    /*
    This function is used to get student house wise rank record
    */ 
    public function housewiserank() {
        $userdata = $this->customlib->getUserData();
        if (($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes") && (empty($class_section_array))) {
            $class_section_array = $this->customlib->get_myClassSection();
        }
        
        $this->db->select('sum(student_behaviour.point) as totalpoints, school_houses.id as school_houses_id, school_houses.house_name,student_incidents.session_id,student_incidents.student_id');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id=student_incidents.incident_id');
        $this->db->join('students', 'students.id=student_incidents.student_id');
        $this->db->join('student_session', 'student_session.student_id=students.id AND student_session.session_id='.$this->current_session);
        $this->db->join('school_houses', 'school_houses.id=students.school_house_id');
        if (!empty($class_section_array)) {
            $this->datatables->group_start();
            foreach ($class_section_array as $class_sectionkey => $class_sectionvalue) {
                foreach ($class_sectionvalue as $class_sectionvaluekey => $class_sectionvaluevalue) {
                    $this->datatables->or_group_start();
                    $this->datatables->where('student_session.class_id', $class_sectionkey);
                    $this->datatables->where('student_session.section_id', $class_sectionvaluevalue);
                    $this->datatables->group_end();

                }
            }
            $this->datatables->group_end();
        }
        $this->db->where('students.is_active =', 'yes');
        $this->db->where('student_incidents.session_id', $this->current_session);
        $this->db->group_by('student_incidents.session_id,school_houses.id');
        $this->db->order_by('totalpoints','DESC');
        $result = $this->db->get();
        $result = $result->result_array();

        foreach ($result as $key => $value) {
            $result[$key]['total_student'] = count($this->totalstudentbyhouse($value['school_houses_id']));
        }
        return $result;
    }

    /*
    This function is used to get total student of house by school house id
    */
    public function totalstudentbyhouse($school_houses_id=NULL) {
        $userdata = $this->customlib->getUserData();

		if (($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes") && (empty($class_section_array))) {
            $class_section_array = $this->customlib->get_myClassSection();
        }
        
        $this->db->select('student_incidents.id');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id=student_incidents.incident_id');
        $this->db->join('students', 'students.id=student_incidents.student_id');
        $this->db->join('student_session', 'student_session.student_id=students.id AND student_session.session_id='.$this->current_session);
        $this->db->join('school_houses', 'school_houses.id=students.school_house_id');
        if (!empty($class_section_array)) {
            $this->datatables->group_start();
            foreach ($class_section_array as $class_sectionkey => $class_sectionvalue) {
                foreach ($class_sectionvalue as $class_sectionvaluekey => $class_sectionvaluevalue) {
                    $this->datatables->or_group_start();
                    $this->datatables->where('student_session.class_id', $class_sectionkey);
                    $this->datatables->where('student_session.section_id', $class_sectionvaluevalue);
                    $this->datatables->group_end();

                }
            }
            $this->datatables->group_end();
        }
        $this->db->where('students.is_active =', 'yes');
        $this->db->where('student_incidents.session_id', $this->current_session);
        $this->db->where('school_houses.id', $school_houses_id);
        $this->db->group_by('students.id');
        $result = $this->db->get();
        return $result->result_array();
    }

    /*
    This function is used to get student house wise point record by house id
    */
    public function housewisepoint($house_id=NULL) {
        $userdata = $this->customlib->getUserData();

		if (($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes") && (empty($class_section_array))) {
            $class_section_array = $this->customlib->get_myClassSection();
        }
        
        $this->db->select('school_houses.house_name, student_behaviour.point, student_behaviour.title, students.id as student_id, students.firstname, students.middlename, students.lastname, students.admission_no, classes.class, sections.section');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id=student_incidents.incident_id');
        $this->db->join('students', 'students.id=student_incidents.student_id');
        $this->db->join('student_session', 'student_session.student_id=students.id AND student_session.session_id='.$this->current_session);
        $this->db->join('school_houses', 'school_houses.id=students.school_house_id');
        $this->db->join('classes', 'classes.id=student_session.class_id');
        $this->db->join('sections', 'sections.id=student_session.section_id');
        if (!empty($class_section_array)) {
            $this->datatables->group_start();
            foreach ($class_section_array as $class_sectionkey => $class_sectionvalue) {
                foreach ($class_sectionvalue as $class_sectionvaluekey => $class_sectionvaluevalue) {
                    $this->datatables->or_group_start();
                    $this->datatables->where('student_session.class_id', $class_sectionkey);
                    $this->datatables->where('student_session.section_id', $class_sectionvaluevalue);
                    $this->datatables->group_end();

                }
            }
            $this->datatables->group_end();
        }
        $this->db->where('students.is_active =', 'yes');
        $this->db->where('student_incidents.session_id', $this->current_session);
        $this->db->where('school_houses.id', $house_id);
        $this->db->group_by('students.id');
        $result = $this->db->get();
        $result = $result->result_array();

        foreach ($result as $key => $result_value) {
            $result[$key]['incident'] = $this->studentincidents_model->pointbystudent($result_value['student_id']);
        }
        return $result;
    }

    /*
    This function is used to get student behaviour record by student id
    */
    public function studentbehaviour($student_id) {

        $this->db->select('student_behaviour.title,student_behaviour.point,student_behaviour.description,student_incidents.id,student_incidents.created_at,students.id as student_id,students.firstname,students.middlename,students.lastname,students.admission_no,sessions.session,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,staff_roles.role_id');
        $this->db->from('student_incidents');
        $this->db->join('students','students.id=student_incidents.student_id');
        $this->db->join('student_behaviour','student_behaviour.id=student_incidents.incident_id');
        $this->db->join('sessions','sessions.id=student_incidents.session_id');
        $this->db->join('staff','staff.id=student_incidents.assign_by');
        $this->db->join('staff_roles', "staff_roles.staff_id = staff.id", "left");      
        $this->db->where('students.is_active =', 'yes');
        $this->db->where('student_incidents.session_id', $this->current_session);
        $this->db->where('student_incidents.student_id', $student_id);
        $this->db->order_by('student_incidents.id', 'desc');
        $result = $this->db->get();
        $result = $result->result_array();

        foreach ($result as $key => $value) {
           $result[$key]['totalcomments'] = $this->studentincidents_model->totalcomments($value['id']);
        }

        return $result;
    }

    /*
    This function is used to delete student incident by id
    */
    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        $this->db->where('student_incidents.id', $id);
        $this->db->delete('student_incidents');

        $message   = DELETE_RECORD_CONSTANT . " On student incidents id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    /*
    This function is used to get points of student by student id
    */
    public function pointbystudent($student_id=NULL) {
        $this->db->select('student_behaviour.point, student_behaviour.title, student_behaviour.description');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id=student_incidents.incident_id');
        $this->db->join('students', 'students.id=student_incidents.student_id');
        $this->db->join('student_session', 'student_session.student_id=students.id AND student_session.session_id='.$this->current_session);
        $this->db->join('classes', 'classes.id=student_session.class_id');
        $this->db->where('students.is_active =', 'yes');
        $this->db->where('student_incidents.session_id', $this->current_session);
        $this->db->where('student_incidents.student_id', $student_id);
        $result = $this->db->get();
        return $result->result_array();
    }

    /*
    This function is used to get student count or total student record by incident id and session
    */
    public function studentcount($incident_id=NULL, $session_value=NULL) {
        $userdata = $this->customlib->getUserData();

		if (($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes") && (empty($class_section_array))) {
            $class_section_array = $this->customlib->get_myClassSection();
        }
        
        $this->db->select('students.firstname,students.lastname,students.middlename,students.admission_no,classes.class,sections.section,student_session.session_id');
        $this->db->from('student_incidents');
        $this->db->join('students', 'students.id=student_incidents.student_id'); 
        $this->db->join('student_session', 'student_session.student_id=students.id');
        $this->db->join('classes', 'classes.id=student_session.class_id');
        $this->db->join('sections', 'sections.id=student_session.section_id');
        if (!empty($class_section_array)) {
            $this->datatables->group_start();
            foreach ($class_section_array as $class_sectionkey => $class_sectionvalue) {
                foreach ($class_sectionvalue as $class_sectionvaluekey => $class_sectionvaluevalue) {
                    $this->datatables->or_group_start();
                    $this->datatables->where('student_session.class_id', $class_sectionkey);
                    $this->datatables->where('student_session.section_id', $class_sectionvaluevalue);
                    $this->datatables->group_end();

                }
            }
            $this->datatables->group_end();
        }
        $this->db->where('students.is_active =', 'yes');
        if($session_value == 'current_session'){
            $this->db->where('student_incidents.session_id', $this->current_session);
            $this->db->where('student_session.session_id', $this->current_session);
        }
        $this->db->where('student_incidents.incident_id', $incident_id);
        $this->db->group_by('student_incidents.student_id');
        $result = $this->db->get();        
        return $result->result_array();
    }

    /*
    This function is used to insert student incident comment
    */
    public function addmessage($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert('student_incident_comments', $data);
        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On student incident comments id " . $insert_id;
        $action    = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
             return $record_id;
        }  
    }

    /*
    This function is used to get student comment record by student incident id
    */
    public function getmessage($student_incident_id=NULL) {
        $this->db->select('student_incident_comments.comment, student_incident_comments.type,student_incident_comments.created_date, staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,staff.image as staff_image,staff.gender,students.firstname,students.middlename,students.lastname,students.admission_no,students.image as student_image,student_incident_comments.id, student_incident_comments.staff_id,student_incident_comments.student_id,roles.name as role_name,students.gender as stud_gender');
        $this->db->from('student_incident_comments');
        $this->db->join('staff', 'staff.id=student_incident_comments.staff_id','left');
        $this->db->join('staff_roles', 'staff_roles.staff_id=staff.id','left');
        $this->db->join('roles', 'roles.id=staff_roles.role_id','left');        
        $this->db->join('students', 'students.id=student_incident_comments.student_id','left');     
        $this->db->where('student_incident_comments.student_incident_id', $student_incident_id);
        $this->db->order_by('student_incident_comments.id','desc');
        $result = $this->db->get();
        return $result->result_array();
    }

    /*
    This function is used to get total student comment by student incident id
    */
    public function totalcomments($student_incident_id=NULL) {
        $this->db->select('count(student_incident_comments.id) as totalcomments');
        $this->db->from('student_incident_comments');
        $this->db->where('student_incident_comments.student_incident_id', $student_incident_id);
        $result = $this->db->get();
        return $result->row_array();
    }
    
    /*
    This function is used to delete student comment by id
    */
    public function delete_comment($id){
        $this->db->where('id',$id);
        $this->db->delete('student_incident_comments');
    }
}