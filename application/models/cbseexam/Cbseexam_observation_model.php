<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cbseexam_observation_model extends MY_Model {

    /*
    This function is used to check cbse exam observation already exists or not
    */
    public function observation_exists($str) {

        $class = $this->security->xss_clean($str);
        $res = $this->check_data_exists($class);

        if ($res) {
            $record_id = $this->input->post('record_id');
            if (isset($record_id)) {
                if ($res->id == $record_id) {
                    return true;
                }
            }
            $this->form_validation->set_message('observation_exists', 'observation already exists');
            return false;
        } else {
            return true;
        }
    }

    /*
    This function is used to check cbse exam observation already exists or not
    */
    public function check_data_exists($data) {
        $this->db->where('name', $data);

        $query = $this->db->get('cbse_exam_observations');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    
    /*
    This function is used to and and update cbse exam observation and cbse exam observation parameter
    */
    public function add($cbse_exam_observation, $cbse_observation_subparameter, $cbse_observation_subparameter_update, $delete_observation_subparameter)
    {     
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($cbse_exam_observation['id']) && !empty($cbse_exam_observation['id'])) {

            foreach ($cbse_observation_subparameter as $grade_range_key => $grade_range_value) {
                $cbse_observation_subparameter[$grade_range_key]['cbse_exam_observation_id'] = $cbse_exam_observation['id'];
            }

               $this->db->where('id', $cbse_exam_observation['id']);
               $this->db->update('cbse_exam_observations', $cbse_exam_observation);

            if (!empty($cbse_observation_subparameter)) {
                $this->db->insert_batch('cbse_observation_subparameter', $cbse_observation_subparameter);
            }
            if (!empty($cbse_observation_subparameter_update)) {
                $this->db->update_batch('cbse_observation_subparameter', $cbse_observation_subparameter_update, 'id');
            }
            if (!empty($delete_observation_subparameter)) {
                $this->db->where_in('id', $delete_observation_subparameter);
                $this->db->delete('cbse_observation_subparameter');
            }

        } else {
            $this->db->insert('cbse_exam_observations', $cbse_exam_observation);
            $insert_id = $this->db->insert_id();
            foreach ($cbse_observation_subparameter as $grade_range_key => $grade_range_value) {
                $cbse_observation_subparameter[$grade_range_key]['cbse_exam_observation_id'] = $insert_id;
            }
            $this->db->insert_batch('cbse_observation_subparameter', $cbse_observation_subparameter);
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

    /*
    This function is used to get cbse exam observation parameter based on cbse exam observation id
    */
    public function getobservationlist(){
        $list=$this->get();
        if(!empty($list)){
            foreach ($list as $key => $value) {
           $this->db->select('cbse_observation_subparameter.*,cbse_observation_parameters.name')->from('cbse_observation_subparameter');
           $this->db->join('cbse_observation_parameters','cbse_observation_parameters.id=cbse_observation_subparameter.cbse_observation_parameter_id');
           $this->db->where('cbse_exam_observation_id',$value['id']);
           $this->db->order_by('cbse_observation_subparameter.id','asc');
           $result=$this->db->get();
           $parameters=$result->result_array();
           $list[$key]['data']=$parameters;

        }
        }
        return $list;        
    }

    /*
    This function is used to get cbse exam observation parameter based on cbse exam observation id
    */
    public function getObservationSubparameter($cbse_exam_observation_id){    
           $this->db->select('cbse_observation_subparameter.*,cbse_observation_parameters.name')->from('cbse_observation_subparameter');
           $this->db->join('cbse_observation_parameters','cbse_observation_parameters.id=cbse_observation_subparameter.cbse_observation_parameter_id');
           $this->db->where('cbse_exam_observation_id',$cbse_exam_observation_id);
           $this->db->order_by('cbse_observation_subparameter.id','asc');
           $result=$this->db->get();
           $parameters=$result->result();     
        return $parameters;        
    }

    /*
    This function is used to get cbse exam observation
    */
    public function get()
    {
    	return $this->db->select('*')->get('cbse_exam_observations')->result_array();
    }

    /*
    This function is used to delete cbse exam observation
    */
    public function remove($id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id',$id);
        $this->db->delete('cbse_exam_observations');
        $message = DELETE_RECORD_CONSTANT . " On cbse exam observations id " . $id;
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