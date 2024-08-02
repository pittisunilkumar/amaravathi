<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Staff_model extends CI_Model
{

    public function getAll($id = null, $is_active = null)
    {
        $this->db->select("staff.*,staff_designation.designation,department.department_name as department, roles.id as role_id, roles.name as role");
        $this->db->from('staff');
        $this->db->join('staff_designation', "staff_designation.id = staff.designation", "left");
        $this->db->join('staff_roles', "staff_roles.staff_id = staff.id", "left");
        $this->db->join('roles', "roles.id = staff_roles.role_id", "left");
        $this->db->join('department', "department.id = staff.department", "left");

        if ($id != null) {
            $this->db->where('staff.id', $id);
        } else {
            if ($is_active != null) {
                $this->db->where('staff.is_active', $is_active);
            }
            $this->db->order_by('staff.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

}
