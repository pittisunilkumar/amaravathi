<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Student_edit_field_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function add($record)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        $this->db->where('name', $record['name']);
        $q = $this->db->get('student_edit_fields');

        if ($q->num_rows() > 0) {
            $results = $q->row();
            $this->db->where('id', $results->id);
            $this->db->update('student_edit_fields', $record);
        } else {
            $this->db->insert('student_edit_fields', $record);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    public function get()
    {
        $this->db->select('*');
        $this->db->from('student_edit_fields');
        $query = $this->db->get();
        return $query->result();
    }

}
