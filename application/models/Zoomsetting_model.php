<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Zoomsetting_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get()
    {
        $this->db->select('*');
        $this->db->from('zoom_settings');
        $this->db->order_by('zoom_settings.id');
        $query = $this->db->get();
        return $query->row();
    }

    public function add($data) 
    {

        $this->db->trans_begin();

        $q = $this->db->get('zoom_settings');

        if ($q->num_rows() > 0) {
            $results = $q->row();
            $this->db->where('id', $results->id);
            $this->db->update('zoom_settings', $data);
			$message = UPDATE_RECORD_CONSTANT . " On zoom settings id " . $results->id;
            $action = "Update";
            $record_id = $results->id;
            $this->log($message, $record_id, $action);
        } else {

            $this->db->insert('zoom_settings', $data);
			$id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On zoom settings id " . $id;
            $action = "Insert";
            $record_id = $id;
            $this->log($message, $record_id, $action);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

}
