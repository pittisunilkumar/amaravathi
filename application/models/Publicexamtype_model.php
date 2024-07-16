<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Publicexamtype_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    
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

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('publicexamtype');
        $message   = DELETE_RECORD_CONSTANT . " On publicexamtype id " . $id;
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

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('publicexamtype', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  publicexamtype id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
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
        } else {
            $this->db->insert('publicexamtype', $data);
            $id        = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On  publicexamtype id " . $id;
            $action    = "Insert";
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
            return $id;
        }
    }


    public  function sessions(){

        $this->db->select()->from('sessions');
        $query = $this->db->get();
        return $query->result_array();
        
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

    public function getresultname($id){
        $this->db->select()->from('publicexamtype')->where('publicexamtype.id',$id);

        $query = $this->db->get();
        
        return $query->row_array();

    }

    public function gtstudentdata($id){

        $this->db->select('students.app_key,students.parent_app_key,student_session.id as `student_session_id`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no,students.roll_no,students.admission_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,students.mobileno, students.email ,students.state,students.city,students.pincode,students.note,students.religion,students.cast,students.dob,students.current_address,students.previous_school,students.guardian_is,students.parent_id,  students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email, users.username,users.password,users.id as user_id,students.dis_reason,students.dis_note,students.disable_at')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = students.hostel_room_id', 'left');
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left');
        $this->db->join('school_houses', 'school_houses.id = students.school_house_id', 'left');
        $this->db->join('users', 'users.user_id = students.id', 'left');
        $this->db->where('users.role', 'student');
        
        $this->db->where('students.id', $id);
        
        $query = $this->db->get();
        
        return $query->row_array();
        
    }

    public function getresultstatus($stid,$resid,$acadid){
        $this->db->select()->from('publicresultaddingstatus')
                ->where('publicresultaddingstatus.stid',$stid)
                ->where('publicresultaddingstatus.resultype_id',$resid)
                ->where('publicresultaddingstatus.session_id',$acadid);

        $query = $this->db->get();

        return $query->row_array();

    }

    // public function getstudentresults($hallno,$resultgrp,$sessionid){

    //     $this->db->select()->from('publicresulttable');
    //     $this->db->join('student_admi','student_admi.student_id=publicresulttable.stid');
    //     $this->db->join('resultsubjects','resultsubjects.id=publicresulttable.subjectid');
    //     $this->db->join('student_hallticket','student_hallticket.admi_no_id=student_admi.id');
    //     $this->db->join('student_hallticket.std_hallticket',$hallno);
    //     $this->db->where('publicresulttable.session_id',$sessionid);
    //     $this->db->where('publicresulttable.resulgroup_id',$resultgrp);
        

    //     $query = $this->db->get();
    //     $result = $query->result_array();

    //     return $result;


    // }


    public function getstudentresults($hallno, $resultgrp, $sessionid){
        $this->db->select()->from('publicresulttable');
        $this->db->join('student_admi', 'student_admi.student_id = publicresulttable.stid');
        $this->db->join('resultsubjects', 'resultsubjects.id = publicresulttable.subjectid');
        $this->db->join('student_hallticket', 'student_hallticket.std_hallticket = ' . $hallno);
        $this->db->where('publicresulttable.session_id', $sessionid);
        $this->db->where('publicresulttable.resulgroup_id', $resultgrp);
    
        $query = $this->db->get();
        $result = $query->result_array();
    
        return $result;
    }


    public function getmarksid($id,$yearid){
        $this->db->select()->from('publicresultsubject_group_subjects')
                           ->where('publicresultsubject_group_subjects.id',$id)
                           ->where('publicresultsubject_group_subjects.session_id',$yearid);

        $result = $this->db->get();
        return $result->row_array();
    }



    public function getmarksidd($restype,$subid,$year){
        $query = $this->db->select('id')
                          ->from('publicresultsubject_group_subjects')
                          ->where('resultsubjects_id', $restype)
                          ->where('subject_id', $subid)
                          ->where('session_id', $year)
                          ->get();

        if ($query->num_rows() > 0) {
            // Admission number found, return it
            return $query->row()->id;
        } else {
            // Admission number not found
            return false;
        }
    }


    
    public function updateresult($data){
        $this->db->insert('internalresulttable', $data);
    }
    
    
    
    public function getsubjects($groupid){
        $this->db->select('publicresultsubject_group_subjects.resultsubjects_id,publicresultsubject_group_subjects.subject_id,resultsubjects.examtype');
        $this->db->from('publicresultsubject_group_subjects');
        $this->db->where('publicresultsubject_group_subjects.resultsubjects_id',$groupid);
        $this->db->where('publicresultsubject_group_subjects.session_id', $this->current_session);
        $this->db->join('resultsubjects','resultsubjects.id = publicresultsubject_group_subjects.subject_id');
        
        $query = $this->db->get();
        $resultarray = $query->result_array();
        
        return $resultarray;
    }



    
    public function getstudentresult($resulttyp,$class_id = null, $section_id = null,$subject_id = null,$examstatus = null){
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,students.lastname,students.image,  students.mobileno,students.email,students.state,students.city,students.pincode,students.religion,students.dob ,students.current_address,students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code, students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.app_key,students.parent_app_key,students.rte,students.gender' );
        $this->db->join('students','students.id=publicresultaddingstatus.stid');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('publicresultaddingstatus.resultype_id',$resulttyp);
        $this->db->where('publicresultaddingstatus.session_id',$this->current_session);


        // $this->db->join('resultsubjects','resultsubjects.id=resultsubject_group_subjects.subject_id');

        if ($class_id != null) {
            $this->db->where('student_session.class_id', $class_id);
        }

        // if($subject_id != null){
        //     $this->db->where('resultsubject_group_subjects.subject_id',$subject_id);

        // }

        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }

        $this->db->where('students.is_active', "yes");
        $this->db->from('publicresultaddingstatus');

        $query = $this->db->get();
        $resultarray = $query->result_array();
        
        return $resultarray;
    }

    public function getsinglestudentresult($stid,$resulttyp,$onesub=null){

        $this->db->select('publicresultsubject_group_subjects.minmarks,publicresultsubject_group_subjects.maxmarks,publicresulttable.actualmarks');
        $this->db->where('publicresulttable.stid',$stid);
        $this->db->where('publicresulttable.resulgroup_id',$resulttyp);

        $this->db->join('publicresultsubject_group_subjects','publicresultsubject_group_subjects.id=publicresulttable.markstableid');

        if($onesub != null){
            $this->db->where('publicresultsubject_group_subjects.subject_id',$onesub);
        }

        $this->db->from('publicresulttable');

        $query = $this->db->get();
        $resultarray = $query->result_array();
        
        return $resultarray;
    }


    public function subjlist($subgr_id,$onesub=null){
        $this->db->select('resultsubjects.examtype');
        $this->db->join('resultsubjects','resultsubjects.id=publicresultsubject_group_subjects.subject_id');
        $this->db->where('publicresultsubject_group_subjects.resultsubjects_id',$subgr_id);
        $this->db->from('publicresultsubject_group_subjects');

        if($onesub != null){
            $this->db->where('publicresultsubject_group_subjects.subject_id',$onesub);
        }

        $query = $this->db->get();
        $resultarray = $query->result_array();
        
        return $resultarray;

    }


    public function getsinglestudentresultstatus($stid,$resulttyp,$onesub=null){

        $this->db->select('publicresultsubject_group_subjects.minmarks,publicresultsubject_group_subjects.maxmarks,publicresulttable.actualmarks');
        $this->db->where('publicresulttable.stid',$stid);
        $this->db->where('publicresulttable.resulgroup_id',$resulttyp);

        $this->db->join('publicresultsubject_group_subjects','publicresultsubject_group_subjects.id=publicresulttable.markstableid');

        $this->db->where('publicresulttable.actualmarks < publicresultsubject_group_subjects.minmarks');

        if($onesub != null){
            $this->db->where('publicresultsubject_group_subjects.subject_id',$onesub);
        }

        $this->db->from('publicresulttable');

        $query = $this->db->get();
        $resultarray = $query->result_array();
        
        return $resultarray;
    }

}
