<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Gmeet_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function getByStudentClassSection($class_id, $section_id)
    {
        $this->db->select('gmeet.*,classes.class,sections.section,staff.name as `staff_name`,staff.surname as `staff_surname`,staff.employee_id as `staff_id`,classes.class,sections.section,roles.name as `staff_role`')->from('gmeet');
        $this->db->join('gmeet_sections', 'gmeet_sections.gmeet_id=gmeet.id');
        $this->db->join('class_sections', ' gmeet_sections.cls_section_id=class_sections.id');
        $this->db->join('classes', 'class_sections.class_id=classes.id');
        $this->db->join('sections', 'sections.id=class_sections.section_id');
        $this->db->join('staff', 'staff.id = gmeet.staff_id');
        $this->db->join('staff_roles', 'staff_roles.staff_id=staff.id');
        $this->db->join('roles', 'roles.id=staff_roles.role_id');
        $this->db->where('classes.id', $class_id);
        $this->db->where('sections.id', $section_id);
        $this->db->where('gmeet.session_id', $this->current_session);
        $this->db->order_by('DATE(`gmeet`.`date`)', 'DESC');
        $this->db->order_by('gmeet.date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function updatehistory($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->where('gmeet_id', $data['gmeet_id']);
        $this->db->where('student_id', $data['student_id']);
        $q = $this->db->get('gmeet_history');
        if ($q->num_rows() > 0) {
            $row               = $q->row();
            $total_hit         = $row->total_hit + 1;
            $data['total_hit'] = $total_hit;
            $this->db->where('id', $row->id);
            $this->db->update('gmeet_history', $data);
        } else {

            $this->db->insert('gmeet_history', $data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

}
