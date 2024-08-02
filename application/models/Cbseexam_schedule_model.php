<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
class Cbseexam_schedule_model extends MY_Model {


	public function add($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('cbse_exam_assessments', $data);
            $message = UPDATE_RECORD_CONSTANT . " On  transport route id " . $data['id'];
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
            $this->db->insert('cbse_exam_assessments', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On transport route id " . $insert_id;
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

    public function add_type($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('cbse_exam_assessment_types', $data);
            $message = UPDATE_RECORD_CONSTANT . " On  transport route id " . $data['id'];
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
            $this->db->insert('cbse_exam_assessment_types', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On transport route id " . $insert_id;
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
 
    public function getassessmentlist(){
        $list=$this->get();
        if(!empty($list)){
            foreach ($list as $key => $value) {
           $list[$key]['data']=$this->db->select('*')->from('cbse_exam_assessment_types')->where('cbse_exam_assessment_id',$value['id'])->get()->result_array();
        }
        }
        return $list;
        
    }

    public function get(){
    	return $this->db->select('*')->get('cbse_exam_assessments')->result_array();
    }



public function get_editdetails($id){
    $result=$this->db->select('*')->where('id',$id)->get('cbse_exam_assessments')->row_array();
    	$result['list']= $this->db->select('*')->from('cbse_exam_assessment_types')->where('cbse_exam_assessment_id',$id)->get()->result_array();
        return $result;
    }

    public function get_assessmentTypebyId($id){
$result= $this->db->select('*')->from('cbse_exam_assessment_types')->where('id',$id)->get()->row_array();
        return $result;
    }

    public function remove_assessment_type($id){
    	
    	 $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id',$id);
        $this->db->delete('cbse_exam_assessment_types');
        $message = DELETE_RECORD_CONSTANT . " On transport route id " . $id;
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

     public function remove($id){
        
         $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id',$id);
        $this->db->delete('cbse_exam_assessments');
        $message = DELETE_RECORD_CONSTANT . " On transport route id " . $id;
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