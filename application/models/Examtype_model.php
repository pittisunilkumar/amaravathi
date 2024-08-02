<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Examtype_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        
    }


    public  function sessions(){

        $this->db->select()->from('sessions');
        $query = $this->db->get();
        return $query->result_array();
        
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function get($id = null)
    {

        $this->db->select()->from('examtype')->where('session_id',$this->current_session);
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
        $this->db->delete('examtype');
        $message   = DELETE_RECORD_CONSTANT . " On examtype id " . $id;
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
            $this->db->update('examtype', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  examtype id " . $data['id'];
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
            $this->db->insert('examtype', $data);
            $id        = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On  examtype id " . $id;
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


    public function getsubjectid($subjectcode){
        $query = $this->db->select('id')
                          ->from('resultsubjects')
                          ->where('subject_code', $subjectcode)
                          ->get();

        if ($query->num_rows() > 0) {
            // Admission number found, return it
            return $query->row()->id;
        } else {
            // Admission number not found
            return false;
        }
    }


    public function checkresultadd($stid,$examtype,$subid){
        $query = $this->db->select()
                          ->from('internalresulttable')
                          ->where('stid', $stid)
                          ->where('resulgroup_id', $examtype)
                          ->where('subjectid',$subid)
                          ->where('session_id',$this->current_session)
                          ->get();

        if ($query->num_rows() > 0) {
            // Admission number found, return it
            return true;
        } else {
            // Admission number not found
            return false;
        }
    }


    public function checksubidinsubjecgrou($subid,$examid){
        $query = $this->db->select()
                          ->from('resultsubject_group_subjects')
                          ->where('subject_id', $subid)
                          ->where('resultsubjects_id', $examid)
                          ->where('session_id',$this->current_session)
                          ->get();

        if ($query->num_rows() > 0) {
            // Admission number found, return it
            return true;
        } else {
            // Admission number not found
            return false;
        }

    }


    public function getstudentid($admino){
        $query = $this->db->select('student_id')
                          ->from('student_admi')
                          ->where('admi_no', $admino)
                          ->get();

        if ($query->num_rows() > 0) {
            // Admission number found, return it
            return $query->row()->student_id;
        } else {
            // Admission number not found
            return false;
        }
    }



    public function getmarksid($restype,$subid,$year){
        $query = $this->db->select('id')
                          ->from('resultsubject_group_subjects')
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
    
    

    //results model funtions

    public function getsubjects($groupid){
        $this->db->select('resultsubject_group_subjects.resultsubjects_id,resultsubject_group_subjects.subject_id,resultsubjects.examtype');
        $this->db->from('resultsubject_group_subjects');
        $this->db->where('resultsubject_group_subjects.resultsubjects_id',$groupid);
        $this->db->where('resultsubject_group_subjects.session_id', $this->current_session);
        $this->db->join('resultsubjects','resultsubjects.id = resultsubject_group_subjects.subject_id');
        
        $query = $this->db->get();
        $resultarray = $query->result_array();
        
        return $resultarray;
    }


    public function getstudentresult($resulttyp,$class_id = null, $section_id = null,$subject_id = null,$examstatus = null){
        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,students.lastname,students.image,  students.mobileno,students.email,students.state,students.city,students.pincode,students.religion,students.dob ,students.current_address,students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code, students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.app_key,students.parent_app_key,students.rte,students.gender' );
        $this->db->join('students','students.id=resultaddingstatus.stid');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('resultaddingstatus.resultype_id',$resulttyp);
        $this->db->where('resultaddingstatus.session_id',$this->current_session);


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
        $this->db->from('resultaddingstatus');

        $query = $this->db->get();
        $resultarray = $query->result_array();
        
        return $resultarray;
    }

    public function getsinglestudentresult($stid,$resulttyp,$onesub=null){

        $this->db->select('resultsubject_group_subjects.minmarks,resultsubject_group_subjects.maxmarks,internalresulttable.actualmarks');
        $this->db->where('internalresulttable.stid',$stid);
        $this->db->where('internalresulttable.resulgroup_id',$resulttyp);

        $this->db->join('resultsubject_group_subjects','resultsubject_group_subjects.id=internalresulttable.markstableid');

        if($onesub != null){
            $this->db->where('resultsubject_group_subjects.subject_id',$onesub);
        }

        $this->db->from('internalresulttable');

        $query = $this->db->get();
        $resultarray = $query->result_array();
        
        return $resultarray;
    }


    public function subjlist($subgr_id,$onesub=null){
        $this->db->select('resultsubjects.examtype');
        $this->db->join('resultsubjects','resultsubjects.id=resultsubject_group_subjects.subject_id');
        $this->db->where('resultsubject_group_subjects.resultsubjects_id',$subgr_id);
        $this->db->from('resultsubject_group_subjects');

        if($onesub != null){
            $this->db->where('resultsubject_group_subjects.subject_id',$onesub);
        }

        $query = $this->db->get();
        $resultarray = $query->result_array();
        
        return $resultarray;

    }


    public function getsinglestudentresultstatus($stid,$resulttyp,$onesub=null){

        $this->db->select('resultsubject_group_subjects.minmarks,resultsubject_group_subjects.maxmarks,internalresulttable.actualmarks');
        $this->db->where('internalresulttable.stid',$stid);
        $this->db->where('internalresulttable.resulgroup_id',$resulttyp);

        $this->db->join('resultsubject_group_subjects','resultsubject_group_subjects.id=internalresulttable.markstableid');

        $this->db->where('internalresulttable.actualmarks < resultsubject_group_subjects.minmarks');

        if($onesub != null){
            $this->db->where('resultsubject_group_subjects.subject_id',$onesub);
        }

        $this->db->from('internalresulttable');

        $query = $this->db->get();
        $resultarray = $query->result_array();
        
        return $resultarray;
    }


}
