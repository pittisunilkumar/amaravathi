<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Teachersubject_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function getSubjectByClsandSection($class_id, $section_id)
    {
        $where = " ";
        $sql   = "SELECT teacher_subjects.*,staff.name as `teacher_name`, staff.surname, subjects.name,subjects.type,subjects.code FROM `teacher_subjects` INNER JOIN subjects ON teacher_subjects.subject_id = subjects.id INNER JOIN class_sections ON teacher_subjects.class_section_id = class_sections.id INNER JOIN staff ON staff.id = teacher_subjects.teacher_id  WHERE class_sections.class_id =" . $this->db->escape($class_id) . " and class_sections.section_id=" . $this->db->escape($section_id) . " and teacher_subjects.session_id=" . $this->db->escape($this->current_session) . " " . $where;

        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
