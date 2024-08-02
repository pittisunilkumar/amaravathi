<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Conference_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function getByStudentClassSection($class_id, $section_id)
    {
        $this->db->select('conferences.*,classes.class,sections.section,for_create.name as `create_for_name`,for_create.surname as `create_for_surname,for_create.employee_id as `for_create_employee_id`,for_create_role.name as `for_create_role_name`')->from('conference_sections');
        $this->db->join('conferences', 'conferences.id = conference_sections.conference_id');
        $this->db->join('class_sections', 'class_sections.id = conference_sections.cls_section_id');
        $this->db->join('classes', 'classes.id = class_sections.class_id');
        $this->db->join('sections', 'sections.id = class_sections.section_id');
        $this->db->join('staff as for_create', 'for_create.id = conferences.staff_id');
        $this->db->join('staff_roles', 'staff_roles.staff_id = for_create.id');
        $this->db->join('roles as `for_create_role`', 'for_create_role.id = staff_roles.role_id');
        $this->db->where('class_sections.class_id', $class_id);
        $this->db->where('class_sections.section_id', $section_id);
        $this->db->where('conferences.session_id', $this->current_session);
        $this->db->order_by('DATE(`conferences`.`date`)', 'DESC');
        $this->db->order_by('conferences.date', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function updatehistory($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->where('conference_id', $data['conference_id']);
        $this->db->where('student_id', $data['student_id']);
        $q = $this->db->get('conferences_history');

        if ($q->num_rows() > 0) {
            $row               = $q->row();
            $total_hit         = $row->total_hit + 1;
            $data['total_hit'] = $total_hit;
            $this->db->where('id', $row->id);
            $this->db->update('conferences_history', $data);
        } else {
            $this->db->insert('conferences_history', $data);
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
