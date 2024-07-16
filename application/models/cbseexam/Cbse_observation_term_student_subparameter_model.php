<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cbse_observation_term_student_subparameter_model extends MY_Model {

    public function add($insert_array, $update_array) {

        $this->db->trans_begin();

        if (!empty($insert_array)) {
          $this->db->insert_batch('cbse_observation_term_student_subparameter', $insert_array);
        }

        if (isset($update_array) && !empty($update_array)) {
            $this->db->update_batch('cbse_observation_term_student_subparameter', $update_array, 'id');
        }
     
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

}