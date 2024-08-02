<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Subjecttimetable_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function getSubjectByClassandSectionDay($class_id, $section_id, $day)
    {
        $sql = "SELECT `subject_group_subjects`.`subject_id`,subjects.name as `subject_name`,subjects.code,subjects.type,staff.name,staff.surname,staff.employee_id,`subject_timetable`.*,subject_group_class_sections.id as `subject_group_class_sections_id` FROM `subject_timetable` JOIN `subject_group_subjects` ON `subject_timetable`.`subject_group_subject_id` = `subject_group_subjects`.`id`inner JOIN subjects on subject_group_subjects.subject_id = subjects.id INNER JOIN staff on staff.id=subject_timetable.staff_id inner JOIN class_sections on class_sections.class_id=subject_timetable.class_id and class_sections.section_id=subject_timetable.section_id INNER JOIN subject_group_class_sections on subject_group_class_sections.class_section_id=class_sections.id and subject_group_class_sections.session_id=subject_timetable.session_id WHERE `subject_timetable`.`class_id` = " . $class_id . " AND `subject_timetable`.`section_id` = " . $section_id . " AND `subject_timetable`.`day` = " . $this->db->escape($day) . " AND `subject_timetable`.`session_id` = " . $this->current_session . " AND `staff`.`is_active`=1 ORDER by subject_timetable.start_time asc";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getSubjects($class_id, $section_id)
    {
        $sql = "SELECT subject_timetable.*,staff.name as `staff_name`,staff.surname as `staff_surname`,staff.contact_no,staff.email,subject_group_subjects.subject_id,subjects.name as `subject_name`,subjects.code,subjects.type FROM `subject_timetable` INNER JOIN subject_group_subjects on subject_group_subjects.id=subject_timetable.subject_group_subject_id INNER join staff on  subject_timetable.staff_id=staff.id inner join subjects on subjects.id=subject_group_subjects.subject_id WHERE class_id=" . $this->db->escape($class_id) . " and section_id=" . $this->db->escape($section_id) . " and subject_timetable.session_id=" . $this->current_session . " GROUP by subjects.id DESC";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getSubjectTimetable($class_id, $section_id, $subject_id)
    {
        $sql = "SELECT subject_timetable.*,staff.name as `staff_name`,staff.surname as `staff_surname`,staff.contact_no,staff.email,subject_group_subjects.subject_id,subjects.name as `subject_name`,subjects.code,subjects.type FROM `subject_timetable` INNER JOIN subject_group_subjects on subject_group_subjects.id=subject_timetable.subject_group_subject_id INNER join staff on  subject_timetable.staff_id=staff.id inner join subjects on subjects.id=subject_group_subjects.subject_id WHERE class_id=" . $this->db->escape($class_id) . " and section_id=" . $this->db->escape($section_id) . " and subject_timetable.session_id=" . $this->current_session . " and subjects.id=" . $subject_id;
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getTeachers($class_id, $section_id)
    {
        $sql = "select time.*,staff.name,staff.name as `staff_name`,staff.surname as `staff_surname`,staff.contact_no,staff.email,staff.employee_id,subject_group_subjects.subject_id,subjects.name as `subject_name`,subjects.code,subjects.type FROM (SELECT subject_timetable.id,subject_timetable.staff_id,subject_timetable.day,subject_timetable.subject_group_id,subject_timetable.subject_group_subject_id,subject_timetable.time_from,subject_timetable.time_to,subject_timetable.room_no,IFNULL(class_teacher.id,'0') as class_teacher_id FROM `subject_timetable` LEFT JOIN class_teacher on class_teacher.staff_id=subject_timetable.staff_id and class_teacher.class_id=subject_timetable.class_id and class_teacher.section_id=subject_timetable.section_id and class_teacher.session_id=" . $this->current_session . " WHERE subject_timetable.class_id=" . $this->db->escape($class_id) . " and subject_timetable.section_id=" . $this->db->escape($section_id) . " and subject_timetable.session_id=" . $this->current_session . "  UNION ALL SELECT class_teacher.id,class_teacher.staff_id,Null as day,Null as subject_group_id,Null as subject_group_subject_id,Null as time_from,Null as time_to,Null as room_no,class_teacher.id as class_teacher_id FROM `class_teacher` WHERE class_id=" . $this->db->escape($class_id) . " and section_id=" . $this->db->escape($section_id) . ") as time INNER JOIN staff on staff.id=time.staff_id LEFT JOIN subject_group_subjects on subject_group_subjects.id=time.subject_group_subject_id LEFT join subjects on subjects.id=subject_group_subjects.subject_id WHERE staff.is_active=1 order by staff.name asc";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getTeacherSubject($class_id, $section_id, $staff_id)
    {
        $sql = "SELECT subject_timetable.*,staff.name as `staff_name`,staff.surname as `staff_surname`,staff.contact_no,staff.email,subject_group_subjects.subject_id,subjects.name as `subject_name`,subjects.code,subjects.type,IFNULL(class_teacher.id,'0') as `class_teacher_id` FROM `subject_timetable`  INNER JOIN subject_group_subjects on subject_group_subjects.id=subject_timetable.subject_group_subject_id INNER join staff on  subject_timetable.staff_id=staff.id  and staff.id=" . $this->db->escape($staff_id) . " inner join subjects on subjects.id=subject_group_subjects.subject_id LEFT JOIN class_teacher on class_teacher.class_id=" . $this->db->escape($class_id) . " and class_teacher.staff_id=staff.id and class_teacher.section_id= " . $this->db->escape($section_id) . " WHERE subject_timetable.class_id=" . $this->db->escape($class_id) . " and subject_timetable.section_id=" . $this->db->escape($section_id) . " and subject_timetable.session_id=" . $this->current_session . " ORDER BY class_teacher.id desc";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function user_rating($student_id, $staff_id)
    {
        $this->db->select('staff_rating.rate,staff_rating.comment')->from('staff_rating')->join("users", "users.id = staff_rating.user_id", "inner")->join("staff", "staff_rating.staff_id = staff.id", "inner");
        $this->db->where('staff.is_active', 1);
        $this->db->where('staff_rating.staff_id', $staff_id);
        $this->db->where('staff_rating.user_id', $student_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function add_rating($data)
    {
        $this->db->where('user_id', $data['user_id']);
        $this->db->where('staff_id', $data['staff_id']);
        $q = $this->db->get('staff_rating');

        if ($q->num_rows() > 0) {
            return false;
        } else {
            $this->db->insert("staff_rating", $data);
            return true;
        }
    }

}
