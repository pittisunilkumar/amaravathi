<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Attendencetype_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function getStudentAttendence($date, $student_session_id)
    {
        $sql = "SELECT attendence_type.type FROM `student_attendences` INNER JOIN attendence_type ON attendence_type.id=student_attendences.attendence_type_id where  student_attendences.`student_session_id`=" . $this->db->escape($student_session_id) . " and student_attendences.date=" . $this->db->escape($date);
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function getAttendencePercentage($date_from, $date_to, $student_session_id)
    {
        $sql = "SELECT IFNULL(count(*), 0) as `total_count`,IFNULL((SELECT count(*) as `total_count` FROM `student_attendences` as a WHERE (date BETWEEN " . $this->db->escape($date_from) . " and " . $this->db->escape($date_to) . ") and student_session_id=" . $this->db->escape($student_session_id) . "  and attendence_type_id !=4 ),0) as `present_attendance` FROM `student_attendences` as a WHERE (date BETWEEN " . $this->db->escape($date_from) . " and " . $this->db->escape($date_to) . ") and student_session_id=" . $this->db->escape($student_session_id);
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function studentAttendanceByDate($class_id, $section_id, $day, $date, $student_session_id)
    {
        $sql        = "SELECT subject_timetable.*,subject_group_subjects.subject_group_id,subjects.id as `subject_id`,subjects.name,subjects.code,subjects.type,student_subject_attendances.student_session_id,student_subject_attendances.attendence_type_id,student_subject_attendances.date,student_subject_attendances.remark,student_subject_attendances.id as `student_subject_attendance_id`,student_subject_attendances.date,student_subject_attendances.date,attendence_type.type,attendence_type.key_value FROM `subject_timetable` INNER JOIN subject_group_subjects on subject_group_subjects.id = subject_timetable.subject_group_subject_id and subject_group_subjects.session_id=" . $this->current_session . " INNER JOIN subjects on subjects.id=subject_group_subjects.subject_id LEFT JOIN student_subject_attendances on student_subject_attendances.subject_timetable_id=subject_timetable.id and student_subject_attendances.student_session_id=" . $this->db->escape($student_session_id) . "INNER JOIN attendence_type on attendence_type.id=student_subject_attendances.attendence_type_id WHERE subject_timetable.class_id=" . $this->db->escape($class_id) . " AND subject_timetable.section_id=" . $this->db->escape($section_id) . " and subject_timetable.day=" . $this->db->escape($day) . "and student_subject_attendances.date=" . $this->db->escape($date);
        $query      = $this->db->query($sql);
        $attendance = $query->result();
        return $attendance;
    }

}
