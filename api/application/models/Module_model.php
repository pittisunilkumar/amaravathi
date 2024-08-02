<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Module_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get($user)
    {
        if ($user == "student") {
            $this->db->select("id,name,short_code,student as `status`")->from('permission_student');
            $this->db->order_by('permission_student.id');
            $query = $this->db->get();
            return $query->result_array();
        } else {
            $this->db->select("id,name,short_code,parent as `status`")->from('permission_student');
            $this->db->order_by('permission_student.id');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function getModuleStatusByCategory($user,$modulearray)
    {
        if ($user == "student") {
            $this->db->select("name,short_code,student as `status`")->from('permission_student');
            $this->db->like('short_code', $modulearray);            
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->select("name,short_code,parent as `status`")->from('permission_student');
            $this->db->like('short_code', $modulearray); 
            $query = $this->db->get();
            return $query->row_array();
        }
    }
    
    public function getModuleExistOrNot($user,$module)
    {
        if ($user == "student") {
            $this->db->select("id,name,short_code,student as `status`")->from('permission_student');
            $this->db->where('permission_student.short_code',$module);
            $this->db->order_by('permission_student.id');
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->select("id,name,short_code,parent as `status`")->from('permission_student');
            $this->db->where('permission_student.short_code',$module);
            $this->db->order_by('permission_student.id');
            $query = $this->db->get();
            return $query->row_array();
        }
    }

}
