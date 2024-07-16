<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cbseexam_assessment_model extends MY_Model {

    /*
    This function is used to add and update cbse exam assessments
    */
	public function add($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('cbse_exam_assessments', $data);
            $message = UPDATE_RECORD_CONSTANT . " On  cbse exam assessments id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $insert_id=$data['id'];
            $this->log($message, $record_id, $action);           
        } else {
            $this->db->insert('cbse_exam_assessments', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On cbse exam assessments id " . $insert_id;
            $action = "Insert";
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
            return $insert_id;
        }
    }

    /*
    This function is used to add and update cbse exam assessments types
    */
    public function add_type($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('cbse_exam_assessment_types', $data);
            $message = UPDATE_RECORD_CONSTANT . " On cbse exam assessment types id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);            
        } else {
            $this->db->insert('cbse_exam_assessment_types', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On cbse exam assessment types id " . $insert_id;
            $action = "Insert";
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

    public function getassessmentlist(){
        $list=$this->get();
        if(!empty($list)){
            foreach ($list as $key => $value) {
           $list[$key]['data']=$this->db->select('*')->from('cbse_exam_assessment_types')->where('cbse_exam_assessment_id',$value['id'])->get()->result_array();
        }
        }
        return $list;
        
    }

    /*
    This function is used to get cbse exam assessments list
    */
    public function get(){
    	return $this->db->select('*')->get('cbse_exam_assessments')->result_array();
    }

    /*
    This function is used to get cbse exam assessments based on id
    */
    public function get_editdetails($id)
    {
        $result=$this->db->select('*')->where('id',$id)->get('cbse_exam_assessments')->row_array();
    	$result['list']= $this->db->select('*')->from('cbse_exam_assessment_types')->where('cbse_exam_assessment_id',$id)->get()->result_array();
        return $result;
    }

    /*
    This function is used to get cbse exam assessments types based on id
    */
    public function get_assessmentTypebyId($id)
    {
        $result= $this->db->select('*')->from('cbse_exam_assessment_types')->where('id',$id)->get()->row_array();
        return $result;
    }

    /*
    This function is used to delete cbse exam assessments types based on id
    */
    public function remove_assessment_type($id){
    	
    	$this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id',$id);
        $this->db->delete('cbse_exam_assessment_types');
        $message = DELETE_RECORD_CONSTANT . " On cbse exam assessment types id " . $id;
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

    /*
    This function is used to delete cbse exam assessments based on id
    */
    public function remove($id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id',$id);
        $this->db->delete('cbse_exam_assessments');        
        $message = DELETE_RECORD_CONSTANT . " On cbse exam assessments id " . $id;
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
            return $record_id;
        }
    }

    public function add_graderange($insert_grade, $cbse_exam_grades_range, $cbse_exam_grades_range_update, $delete_grade_range)
    {     
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($insert_grade['id']) && !empty($insert_grade['id'])) {

            $this->db->where('id', $insert_grade['id']);
            $this->db->update('cbse_exam_assessments', $insert_grade);

            foreach ($cbse_exam_grades_range as $grade_range_key => $grade_range_value) {
                $cbse_exam_grades_range[$grade_range_key]['cbse_exam_assessment_id'] = $insert_grade['id'];
            }
            if (!empty($cbse_exam_grades_range)) {
                $this->db->insert_batch('cbse_exam_assessment_types', $cbse_exam_grades_range);
            }
            if (!empty($cbse_exam_grades_range_update)) {
                $this->db->update_batch('cbse_exam_assessment_types', $cbse_exam_grades_range_update, 'id');
            }
            if (!empty($delete_grade_range)) {
                $this->db->where_in('id', $delete_grade_range);
                $this->db->delete('cbse_exam_assessment_types');
            }
        } else {
            $this->db->insert('cbse_exam_assessments', $insert_grade);
            $insert_id = $this->db->insert_id();
            foreach ($cbse_exam_grades_range as $grade_range_key => $grade_range_value) {
                $cbse_exam_grades_range[$grade_range_key]['cbse_exam_assessment_id'] = $insert_id;
            }
            $this->db->insert_batch('cbse_exam_assessment_types', $cbse_exam_grades_range);
        }

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
        return true;
    }

    public function getWithAssessmentType($id)
    {
        $result         = $this->db->select('*')->where('id', $id)->get('cbse_exam_assessments')->row_array();
        $result['list'] = $this->db->select('*')->from('cbse_exam_assessment_types')->where('cbse_exam_assessment_id', $id)->get()->result_array();
        return $result;
    }

    public function getWithAssessmentTypeByAssessmentID($cbse_exam_assessment_id)
    {      
        $assement_types= $this->db->select('*')->from('cbse_exam_assessment_types')->where('cbse_exam_assessment_id', $cbse_exam_assessment_id)->order_by('id','asc')->get()->result();
        return $assement_types;
    }

    public function check_exists($str) {

        $assessment = $this->security->xss_clean($str);
        $res = $this->check_data_exists($assessment);

        if ($res) {
            $record_id = $this->input->post('record_id');
            if (isset($record_id)) {
                if ($res->id == $record_id) {
                    return true;
                }
            }
            $this->form_validation->set_message('check_exists', $this->lang->line('assessment_name_already_exists'));
            return false;
        } else {
            return true;
        }
    }

    public function check_data_exists($data) {
        $this->db->where('name', $data);

        $query = $this->db->get('cbse_exam_assessments');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }


}