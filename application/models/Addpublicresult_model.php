<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Addpublicresult_model extends MY_Model
{

    protected $current_session;

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date    = $this->setting_model->getDateYmd();
    }


    public function add($data)
    {

        $this->db->insert('publicresulttable', $data);
        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On transport route id " . $insert_id;
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
            //return $return_value;
        }
        return $insert_id;
        
    }


    public function hallticketnostatusgetDatatableByClassSection($class_id, $section_id = null, $status, $resulttype)
    {
        $this->db
            ->select('classes.id as `class_id`, student_session.id as student_session_id, students.id, classes.class, sections.id as `section_id`, sections.section, students.id, students.admission_no, students.roll_no, students.admission_date, students.firstname, students.middlename, students.lastname, students.image, students.mobileno, students.email, students.state, students.city, students.pincode, students.religion, students.dob, students.current_address, students.permanent_address, IFNULL(students.category_id, 0) as `category_id`, IFNULL(categories.category, "") as `category`, students.adhar_no, students.samagra_id, students.bank_account_no, students.bank_name, students.ifsc_code, students.guardian_name, students.guardian_relation, students.guardian_phone, students.guardian_address, students.is_active, students.created_at, students.updated_at, students.father_name, students.app_key, students.parent_app_key, students.rte, students.gender')
            ->join('student_session', 'student_session.student_id = students.id')
            ->join('classes', 'student_session.class_id = classes.id')
            ->join('sections', 'sections.id = student_session.section_id')
            ->join('categories', 'students.category_id = categories.id', 'left')
            ->join('student_admi', 'students.id = student_admi.student_id')
            ->join('student_hallticket','student_hallticket.admi_no_id=student_admi.id')
            ->where('student_session.session_id', $this->current_session)
            ->where('students.is_active', 'yes');

            if ($status == 0) {
                $this->db->where('students.id NOT IN (SELECT stid FROM publicresultaddingstatus WHERE resultype_id = ' . $resulttype . ')');
            }

            if($status==1){

                $this->db->join('publicresultaddingstatus', 'students.id = publicresultaddingstatus.stid');
                $this->db->where('publicresultaddingstatus.resultype_id',$resulttype);
                
            }

        $this->db->where('student_session.class_id', $class_id);
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }

        $this->db->from('students');

        $query = $this->db->get();
        $result = $query->result_array(); // Fetch the result as an array

        // Return the result as an array
        return $result;
    }


    public function subjectsgroup($resulttype){

        $this->db->select()
                ->where('publicresultsubject_group_subjects.session_id', $this->current_session)
                ->where('publicresultsubject_group_subjects.resultsubjects_id',$resulttype)
                ->join('resultsubjects','resultsubjects.id=publicresultsubject_group_subjects.subject_id')
                ->from('publicresultsubject_group_subjects');

        $query = $this->db->get();
        $result = $query->result_array(); // Fetch the result as an array

        // Return the result as an array
        return $result;

    }


    public function getmarks($resid,$subid){
        $this->db->select()->from('publicresultsubject_group_subjects')
                           ->where('publicresultsubject_group_subjects.resultsubjects_id',$resid)
                           ->where('publicresultsubject_group_subjects.subject_id',$subid)
                           ->where('publicresultsubject_group_subjects.session_id',$this->current_session);

        $result = $this->db->get();
        return $result->row_array();
    }


    public function getmarksid($id,$yearid){
        $this->db->select()->from('publicresultsubject_group_subjects')
                           ->where('publicresultsubject_group_subjects.id',$id)
                           ->where('publicresultsubject_group_subjects.session_id',$yearid);

        $result = $this->db->get();
        return $result->row_array();
    }


    public function addresult($data){
        $this->db->insert('publicresultaddingstatus', $data);
        $insert_id = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On transport route id " . $insert_id;
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
            //return $return_value;
        }
        return $insert_id;
    }

    public function getmarksidd($restype,$subid,$year){
        $query = $this->db->select('id')
                          ->from('publicresultsubject_group_subjects')
                          ->where('publicresultsubject_group_subjects.resultsubjects_id', $restype)
                          ->where('publicresultsubject_group_subjects.subject_id', $subid)
                          ->where('publicresultsubject_group_subjects.session_id', $year)
                          ->get();

        if ($query->num_rows() > 0) {
            return $query->row()->id;
        } else {
            return false;
        }
    }

    public function subjectsgroupp($resulttype,$session){

        $this->db->select('resultsubjects.id as subid,resultsubjects.examtype,resultsubjects.subject_code,publicresultsubject_group_subjects.*')
                ->where('publicresultsubject_group_subjects.session_id', $session)
                ->where('publicresultsubject_group_subjects.resultsubjects_id',$resulttype)
                ->join('resultsubjects','resultsubjects.id=publicresultsubject_group_subjects.subject_id')
                ->from('publicresultsubject_group_subjects');


        $query = $this->db->get();
        $result = $query->result_array(); // Fetch the result as an array

        // Return the result as an array
        return $result;

    }


    public function get($id = null)
    {

        $this->db->select()->from('publicexamtype')->where('session_id',$this->current_session);
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    
    public function getstudentid($hallno){
        $this->db->select('student_id')->from('student_hallticket')
                 ->join('student_admi','student_admi.id=student_hallticket.admi_no_id')
                 ->where('student_hallticket.std_hallticket',$hallno);
                 
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            // Admission number found, return it
            return $query->row()->student_id;
        } else {
            // Admission number not found
            return false;
        }
    }


    public function getmarkforsubject($stid,$resid,$subid,$sessionid){
        $query = $this->db->select('actualmarks')
                        ->from('publicresulttable')
                        ->where('stid', $stid)
                        ->where('resulgroup_id', $resid)
                        ->where('subjectid', $subid)
                        ->where('session_id', $sessionid)
                        ->get();
        if ($query->num_rows() > 0) {
            // Admission number found, return it
            return $query->row()->actualmarks;
        } else {
            // Admission number not found
            return false;
        }
    }

    public function resultupdate($stid,$resid,$subid,$data){
        $this->db->where('stid', $stid);
        $this->db->where('resulgroup_id', $resid);
        $this->db->where('subjectid', $subid);
        $this->db->update('publicresulttable', $data);
        
        $this->db->trans_complete(); # Completing transaction

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }


    public function searchstudentresultassign($class=null,$section_id=null,$catagoryid=null,$gender=null,$id,$sessionid){
        
        $this->db->select('students.id AS `stid`,publicresultaddingstatus.assign_status,classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,students.lastname,students.image,  students.mobileno,students.email,students.state,students.city,students.pincode,students.religion,students.dob ,students.current_address,students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code, students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.app_key,students.parent_app_key,students.rte,students.gender,vehicles.vehicle_no,transport_route.route_title,route_pickup_point.id as `route_pickup_point_id`,pickup_point.name as `pickup_point`,' . $field_variable)->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('publicresultaddingstatus','students.id = publicresultaddingstatus.stid');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->join('route_pickup_point', 'student_session.route_pickup_point_id = route_pickup_point.id', 'left');
        $this->db->join('pickup_point', 'pickup_point.id = route_pickup_point.pickup_point_id', 'left');
        $this->db->join('vehicle_routes', 'student_session.vehroute_id = vehicle_routes.id', 'left');
        $this->db->join('transport_route', 'vehicle_routes.route_id = transport_route.id', 'left');
        $this->db->join('vehicles', 'vehicle_routes.vehicle_id = vehicles.id', 'left');
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('students.is_active', "yes");
        $this->db->where('publicresultaddingstatus.resultype_id', $id);
        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }
        if($catagoryid != null){
            $this->db->where('students.category_id', $catagoryid);
        }
        if($gender != null ){
            $this->db->where('students.gender',$gender);
        }
        
        $query = $this->db->get();
        $result = $query->result_array(); // Fetch the result as an array

        // Return the result as an array
        return $result;
    }

    public function updatingresult($stid,$resid,$data,$sessionid){
        $this->db->where('stid', $stid);
        $this->db->where('resultype_id', $resid);
        $this->db->where('session_id', $sessionid);
        $this->db->update('publicresultaddingstatus', $data);
        
        $this->db->trans_complete(); # Completing transaction

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }



}


?>