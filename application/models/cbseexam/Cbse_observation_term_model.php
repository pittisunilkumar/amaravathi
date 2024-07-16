<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cbse_observation_term_model extends MY_Model {

	public function add($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('cbse_observation_terms', $data);
            $message = UPDATE_RECORD_CONSTANT . " On cbse observation terms id " . $data['id'];
            $action = "Update";
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
            $this->db->insert('cbse_observation_terms', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On cbse observation terms id " . $insert_id;
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
            return $insert_id;
        }
    }

    public function getlist()
    {
         $this->datatables
            ->select('cbse_observation_terms.id,cbse_observation_terms.cbse_term_id,cbse_exam_observations.name as cbse_observation_parameter_name,cbse_terms.name as `cbse_term_name`,cbse_terms.term_code,cbse_observation_terms.description')
            ->join('cbse_exam_observations','cbse_exam_observations.id=cbse_observation_terms.cbse_exam_observation_id')
             ->join('cbse_terms','cbse_terms.id=cbse_observation_terms.cbse_term_id')
            ->searchable('cbse_exam_observations.name,cbse_terms.name,cbse_terms.term_code,cbse_observation_terms.description')
            ->orderable('cbse_exam_observations.name,cbse_terms.name,cbse_terms.term_code,cbse_observation_terms.description')         
            ->sort('cbse_observation_terms.id', 'desc')
            ->from('cbse_observation_terms');
        return $this->datatables->generate('json');
    }

    public function get($id = null) {
        $this->db->select('cbse_observation_terms.*,cbse_exam_observations.name as cbse_observation_parameter_name,cbse_terms.name as `cbse_term_name`,cbse_terms.term_code,cbse_observation_terms.description')->from('cbse_observation_terms');
        $this->db->join('cbse_exam_observations','cbse_exam_observations.id=cbse_observation_terms.cbse_exam_observation_id');
        $this->db->join('cbse_terms','cbse_terms.id=cbse_observation_terms.cbse_term_id');
        if ($id != null) {
            $this->db->where('cbse_observation_terms.id', $id);
        } else {
            $this->db->order_by('cbse_observation_terms.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row();
        } else {
            return $query->result();
        }
    }

    public function observation_term_exists($str) {

        $cbse_term_id = $this->security->xss_clean($str);
        $res = $this->check_data_exists($this->input->post('cbse_exam_observation_id'),$cbse_term_id);

        if ($res) {
            $record_id = $this->input->post('record_id');
            if (isset($record_id)) {
                if ($res->id == $record_id) {
                    return true;
                }
            }
            $this->form_validation->set_message('observation_term_exists', $this->lang->line('observations_with_term_combination_already_exists'));
            return false;
        } else {
            return true;
        }
    }

    public function check_data_exists($cbse_exam_observation_id,$cbse_term_id) {
        
        $this->db->where('cbse_exam_observation_id', $cbse_exam_observation_id);
        $this->db->where('cbse_term_id', $cbse_term_id);

        $query = $this->db->get('cbse_observation_terms');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function getStudentSubparameter($exam_id,$cbse_observation_term_id,$class_id,$section_id){
       
        $sql ="SELECT cbse_observation_terms.*,cbse_observation_parameters.name as cbse_observation_parameter_name,student_session.id as `student_session_id`,cbse_observation_subparameter.id as `cbse_observation_subparameter_id`,cbse_observation_term_student_subparameter.id as cbse_observation_term_student_subparameter_id,cbse_observation_subparameter.maximum_marks,cbse_observation_term_student_subparameter.obtain_marks,students.id as `student_id`,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast,  students.dob ,students.current_address, students.previous_school,students.roll_no,
            students.guardian_is,students.parent_id,students.admission_no,
            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email FROM `cbse_observation_terms` INNER join cbse_exams on cbse_exams.cbse_term_id=cbse_observation_terms.cbse_term_id and cbse_exams.id=".$exam_id." INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id =cbse_exams.id INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER JOIN classes on classes.id=student_session.class_id INNER JOIN sections on sections.id=student_session.section_id INNER JOIN students on students.id=student_session.student_id INNER JOIN cbse_observation_subparameter on cbse_observation_subparameter.cbse_exam_observation_id=cbse_observation_terms.cbse_exam_observation_id LEFT JOIN cbse_observation_term_student_subparameter on cbse_observation_term_student_subparameter.cbse_ovservation_term_id=cbse_observation_terms.id and cbse_observation_term_student_subparameter.cbse_observation_subparameter_id=cbse_observation_subparameter.id and cbse_observation_term_student_subparameter.student_session_id=student_session.id  INNER join cbse_observation_parameters on cbse_observation_parameters.id=cbse_observation_subparameter.cbse_observation_parameter_id WHERE cbse_observation_terms.id=".$cbse_observation_term_id." and classes.id=".$class_id." and sections.id=".$section_id;
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getObservationByTemplate($cbse_template_id) {
        $this->db->select('cbse_observation_terms.*,cbse_exam_observations.name as cbse_observation_name,cbse_terms.name as `term_name`');
        $this->db->join('cbse_template_terms','cbse_template_terms.cbse_term_id=cbse_observation_terms.cbse_term_id');
        $this->db->join('cbse_exam_observations','cbse_exam_observations.id=cbse_observation_terms.cbse_exam_observation_id');
        $this->db->join('cbse_terms','cbse_terms.id=cbse_template_terms.cbse_term_id');
        $this->db->where('cbse_template_terms.cbse_template_id', $cbse_template_id);
        
        $query = $this->db->get('cbse_observation_terms');
        if ($query->num_rows() > 0) {
            $observations= $query->result();
            foreach ($observations as $observation_key => $observation_value) {
               
                $observations[$observation_key]->{'cbse_observation_parameters'}=$this->cbseexam_observation_model->getObservationSubparameter($observation_value->cbse_exam_observation_id);
            }
            return $observations;

        } else {
            return false;
        }
    }

    public function remove($id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id',$id);
        $this->db->delete('cbse_observation_terms');
        $message = DELETE_RECORD_CONSTANT . " On cbse observation terms id " . $id;
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