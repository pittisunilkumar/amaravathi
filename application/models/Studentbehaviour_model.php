<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studentbehaviour_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /*
    This function is used to get student behaviour record by id, or get all record of student behaviour 
    */
    public function get($id = null)
    {
        $this->db->select('*')->from('student_behaviour');
        if ($id != null) {
            $this->db->where('student_behaviour.id', $id);
        } else {
            $this->db->order_by('student_behaviour.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /*
    This function is used to insert or update student behaviour record
    */
    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('student_behaviour', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  student behaviour id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
            $insert_id = $data['id'];
            $this->log($message, $record_id, $action);
            
        } else {
            $this->db->insert('student_behaviour', $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On student behaviour id " . $insert_id;
            $action    = "Insert";
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
    This function is used to get student behaviour record for datatable
    */
    public function incident() {
        $this->datatables->select('*')->from('student_behaviour')->searchable('title,point,description')
            ->orderable('title,point,description')->sort('student_behaviour.id','desc');
        return $this->datatables->generate('json');
    }

    /*
    This function is used to delete student behaviour record by id
    */
    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        
        $this->db->where('student_incidents.incident_id', $id);
        $this->db->delete('student_incidents');

        $this->db->where('student_behaviour.id', $id);
        $this->db->delete('student_behaviour');
        $message   = DELETE_RECORD_CONSTANT . " On student behaviour and student incidents where behaviour id and student incidents id " . $id;
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
    
    /*
    This function is used to get student behaviour setting record
    */
    public function getsettings($id = null)
    {
        $this->db->select('*')->from('behaviour_settings');         
        $query = $this->db->get();  
        return $query->row_array();        
    }
    
    /*
    This function is used to update student behaviour setting record
    */
    public function updatesetting($data)
    { 
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('behaviour_settings', $data); 
            $message   = UPDATE_RECORD_CONSTANT . " On Behaviour Settings id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
            $insert_id = $data['id'];
            $this->log($message, $record_id, $action);
            
        } else {
            $this->db->insert('behaviour_settings', $data);        
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On Behaviour Settings id " . $insert_id;
            $action    = "Insert";
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
}