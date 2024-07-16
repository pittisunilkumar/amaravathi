<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cbseexam_observation_parameter_model extends MY_Model {

    /*
    This function is used get cbse exam observation parameters
    */
    public function get($id = null) {
        $this->db->select()->from('cbse_observation_parameters');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row();
        } else {
            return $query->result();
        }
    }

    public function getObservationParamsByObservationTerm($cbse_observation_term_id){
        $sql   = "SELECT cbse_observation_terms.*,cbse_observation_subparameter.id as `cbse_observation_subparameter_id`,cbse_observation_subparameter.maximum_marks,cbse_observation_subparameter.description,cbse_observation_parameters.id as `cbse_observation_parameter_id`,cbse_observation_parameters.name as cbse_observation_parameter_name FROM `cbse_observation_terms` INNER JOIN cbse_observation_subparameter on cbse_observation_subparameter.cbse_exam_observation_id=cbse_observation_terms.cbse_exam_observation_id INNER JOIN cbse_observation_parameters on cbse_observation_parameters.id=cbse_observation_subparameter.cbse_observation_parameter_id WHERE cbse_observation_terms.id=".$this->db->escape($cbse_observation_term_id);
            $query = $this->db->query($sql);
        return $query->result();
    }

    public function getWithParameters($id)
    {
        $result         = $this->db->select('*')->where('id', $id)->get('cbse_exam_observations')->row_array();
        $result['list'] = $this->db->select('*')->from('cbse_observation_subparameter')->where('cbse_exam_observation_id', $id)->get()->result_array();
        return $result;
    }

    public function parameter_exists($str) {

        $class = $this->security->xss_clean($str);
        $res = $this->check_data_exists($class);

        if ($res) {
            $pre_parameter_id = $this->input->post('pre_parameter_id');
            if (isset($pre_parameter_id)) {
                if ($res->id == $pre_parameter_id) {
                    return true;
                }
            }
            $this->form_validation->set_message('parameter_exists', 'Record already exists');
            return false;
        } else {
            return true;
        }
    }


   public function check_data_exists($data) {
        $this->db->where('name', $data);

        $query = $this->db->get('cbse_observation_parameters');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    
    /*
    This function is used to add and edit cbse exam observation parameters
    */
	public function add($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('cbse_observation_parameters', $data);
            $message = UPDATE_RECORD_CONSTANT . " On cbse observation parameters id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $insert_id=$data['id'];
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
            $this->db->insert('cbse_observation_parameters', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On cbse observation parameters id " . $insert_id;
            $action = "Insert";
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
           
        }
        return $insert_id;
    }


    public function getStudentTermObservation($cbse_template_id,$students){
        $students = implode(', ', array_map(function($val){return sprintf("'%s'", $val);}, $students));

        $sql   = "SELECT cbse_template_terms.*,cbse_exam_observations.id as `cbse_exam_observation_id`, cbse_exam_observations.name as `cbse_exam_observation_name`, cbse_observation_terms.id as `cbse_observation_term_id`,cbse_observation_subparameter.cbse_exam_observation_id, cbse_observation_subparameter.cbse_observation_parameter_id,cbse_observation_subparameter.maximum_marks,cbse_observation_term_student_subparameter.student_session_id,cbse_observation_term_student_subparameter.obtain_marks,cbse_observation_parameters.name as `cbse_observation_parameter_name` FROM  `cbse_template_terms` INNER join cbse_observation_terms on cbse_observation_terms.cbse_term_id=cbse_template_terms.cbse_term_id INNER JOIN cbse_exam_observations on cbse_exam_observations.id=cbse_observation_terms.cbse_exam_observation_id INNER join cbse_observation_subparameter on cbse_observation_subparameter.cbse_exam_observation_id= cbse_exam_observations.id INNER JOIN cbse_observation_parameters on cbse_observation_parameters.id=cbse_observation_subparameter.cbse_observation_parameter_id INNER JOIN cbse_observation_term_student_subparameter on cbse_observation_term_student_subparameter.cbse_ovservation_term_id=cbse_observation_terms.id and cbse_observation_term_student_subparameter.cbse_observation_subparameter_id=cbse_observation_subparameter.id INNER JOIN student_session on student_session.id=cbse_observation_term_student_subparameter.student_session_id  INNER join students on students.id =student_session.student_id  INNER join  classes on student_session.class_id = classes.id LEFT join  sections on sections.id = student_session.section_id  WHERE cbse_template_id=".$this->db->escape($cbse_template_id)." and cbse_observation_terms.session_id=16 and cbse_observation_term_student_subparameter.student_session_id in (".$students.")";             
        
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getTermObservationParams($cbse_template_id){
        $sql   = "SELECT cbse_template_terms.*,cbse_exam_observations.name as `cbse_exam_observation_name`,cbse_observation_subparameter.cbse_observation_parameter_id ,cbse_observation_parameters.name as `cbse_observation_parameter_name`  FROM `cbse_template_terms` INNER join cbse_observation_terms on cbse_observation_terms.cbse_term_id=cbse_template_terms.cbse_term_id  INNER JOIN cbse_exam_observations on cbse_exam_observations.id=cbse_observation_terms.cbse_exam_observation_id INNER join cbse_observation_subparameter on cbse_observation_subparameter.cbse_exam_observation_id=cbse_exam_observations.id INNER join cbse_observation_parameters on cbse_observation_parameters.id=cbse_observation_subparameter.cbse_observation_parameter_id WHERE cbse_template_id=".$this->db->escape($cbse_template_id)." GROUP BY cbse_observation_parameters.id";
        $query = $this->db->query($sql);
        return $query->result();
    }

    /*
    This function is used delete cbse exam observation parameters
    */
    public function remove($id) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('cbse_observation_parameters');
        $message = DELETE_RECORD_CONSTANT . " On cbse observation parameters id " . $id;
        $action = "Delete";
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
   
}