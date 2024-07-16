<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Financialsession_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get($id = null) {
        $this->db->select()->from('financialyear');
        if ($id != null) {
            $this->db->where('year_id', $id);
        } else {
            $this->db->order_by('year_id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getAllSession() {
        $sql = "SELECT financialyear.* FROM `financialyear`";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    // public function getPreSession($session_id) {
    //     $sql = "select * from financialyear where id in (select max(id) from financialyear where id < $session_id)";

    //     $query = $this->db->query($sql);
    //     return $query->row();
    // }

    public function unassignfinancialyear($id){
        $data=array('is_active' => 0);
        $this->db->where('year_id', $id);
        $this->db->update('financialyear', $data);
    }

    public function assignfinancialyear($id){
        $data=array('is_active' => 1);
        $this->db->where('year_id', $id);
        $this->db->update('financialyear', $data);
    }

    public function remove($id) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('year_id', $id);
        $this->db->delete('financialyear');
        $message = DELETE_RECORD_CONSTANT . " On financialyear id " . $id;
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

    public function get_active_id() {
        // Assuming your table name is 'your_table_name'
        $query = $this->db->select('year_id')
                          ->from('financialyear')
                          ->where('is_active', 1)
                          ->get();
                          
        return $query->row_array();
    }
    

    public function add($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['year_id'])) {
            $this->db->where('year_id', $data['year_id']);
            $this->db->update('financialyear', $data);
            $message = UPDATE_RECORD_CONSTANT . " On financialyear year_id " . $data['id'];
            $action = "Update";
            $record_id = $data['year_id'];
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
            $this->db->insert('financialyear', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On financialyear id " . $insert_id;
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
    }

}
