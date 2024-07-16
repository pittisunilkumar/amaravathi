<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Syllabus_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session    = $this->setting_model->getCurrentSession();
        $this->superadmin_visible = $this->setting_model->get();

    }

    public function getSyllabusSubjects($class_id, $section_id)
    {
        $sql = "SELECT subject_group_class_sections.*,subject_groups.name,subject_group_subjects.subject_id,subject_group_subjects.id as `subject_group_subject_id`,subjects.name as `subject_name` , subjects.code as `subject_code` ,(select count(*) from lesson INNER JOIN topic on topic.lesson_id=lesson.id WHERE lesson.subject_group_subject_id=subject_group_subjects.id and lesson.subject_group_class_sections_id=subject_group_class_sections.id) as `total`,(select count(*) from lesson INNER JOIN topic on topic.lesson_id=lesson.id WHERE lesson.subject_group_subject_id=subject_group_subjects.id and lesson.subject_group_class_sections_id=subject_group_class_sections.id and topic.status=1) as `total_complete` from class_sections INNER join subject_group_class_sections on subject_group_class_sections.class_section_id=class_sections.id INNER JOIN subject_groups on subject_groups.id = subject_group_class_sections.subject_group_id INNER JOIN subject_group_subjects on subject_group_subjects.subject_group_id=subject_groups.id INNER JOIN subjects on subjects.id = subject_group_subjects.subject_id WHERE class_sections.class_id=" . $this->db->escape($class_id) . " and subject_group_class_sections.session_id=" . $this->current_session . " and class_sections.section_id=" . $this->db->escape($section_id);

        $query = $this->db->query($sql);
        return $query->result();

    }

    public function getSubjectsLesson($subject_group_subject_id, $subject_group_class_sections_id)
    {
        $result = array();
        $sql    = "SELECT lesson.*,(select count(*) from topic WHERE topic.lesson_id=lesson.id) as `total`,(select count(*) from topic WHERE topic.lesson_id=lesson.id and topic.status=1) as `total_complete` FROM `lesson` WHERE subject_group_subject_id=" . $this->db->escape($subject_group_subject_id) . " and subject_group_class_sections_id=" . $this->db->escape($subject_group_class_sections_id);
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            foreach ($result as $result_key => $result_value) {
                $lesson_id = $result_value->id;
                $this->db->from('topic');
                $this->db->where('topic.lesson_id', $lesson_id);
                $this->db->order_by('topic.id');
                $query                           = $this->db->get();
                $topics                          = $query->result();
                $result[$result_key]->{"topics"} = $topics;
            }
        }
        return $result;
    }

    public function getLessonPlanBwDate($class_id, $section_id, $date_from, $date_to)
    {
        $sql   = "SELECT subject_syllabus.id as `subject_syllabus_id`,subject_syllabus.time_from,subject_syllabus.time_to,subject_group_class_sections.*,classes.class,sections.section,lesson.id as lesson_id,topic.name as `topic_name`,lesson.name as `lesson_name`,subject_syllabus.date,subjects.name,subjects.code FROM `subject_group_class_sections` INNER JOIN class_sections on class_sections.id=subject_group_class_sections.class_section_id INNER JOIN classes on classes.id=class_sections.class_id INNER JOIN sections on sections.id=class_sections.section_id INNER JOIN lesson on lesson.subject_group_class_sections_id = subject_group_class_sections.id INNER JOIN topic on topic.lesson_id=lesson.id INNER JOIN subject_syllabus on subject_syllabus.topic_id=topic.id INNER JOIN subject_group_subjects on lesson.subject_group_subject_id=subject_group_subjects.id INNER join subjects on subjects.id=subject_group_subjects.subject_id WHERE classes.id=" . $this->db->escape($class_id) . " and sections.id=" . $this->db->escape($section_id) . " and subject_syllabus.date BETWEEN " . $this->db->escape($date_from) . " and " . $this->db->escape($date_to) . " and lesson.session_id=" . $this->current_session;
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getSyllabusDetail($subject_syllabus_id)
    {
        $sql   = "SELECT subject_syllabus.*,topic.id as `topic_id`,topic.name as `topic_name`,lesson.name as `lesson_name` FROM `subject_syllabus` INNER JOIN topic on topic.id= subject_syllabus.topic_id INNER join lesson on lesson.id = topic.lesson_id WHERE subject_syllabus.id =" . $this->db->escape($subject_syllabus_id);
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function getmysubjects($class_id, $section_id)
    {
        $sql   = "SELECT subject_group_subjects.id as subject_group_subjects_id,subject_group_class_sections.id as subject_group_class_sections_id,subjects.name,subjects.code,subjects.id as subject_id FROM `class_sections` join subject_group_class_sections on subject_group_class_sections.class_section_id=class_sections.id join subject_group_subjects on subject_group_subjects.subject_group_id=subject_group_class_sections.subject_group_id join subjects on subject_group_subjects.subject_id=subjects.id WHERE subject_group_class_sections.session_id=" . $this->current_session . " and class_sections.class_id=" . $this->db->escape($class_id) . " and class_sections.section_id=" . $this->db->escape($section_id);
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getstudentmessage($subject_syllabus_id = null)
    {
        $this->db->select("lesson_plan_forum.id as lesson_plan_forum_id,lesson_plan_forum.message,lesson_plan_forum.created_date,lesson_plan_forum.type,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,staff.image as staff_image,staff.gender,students.firstname,students.middlename,students.lastname,students.image as student_image,students.admission_no,staff.id as staff_id");
        $this->db->join("staff", "staff.id = lesson_plan_forum.staff_id", 'left');
        $this->db->join("students", "students.id = lesson_plan_forum.student_id", 'left');

        if ($subject_syllabus_id != null) {
            $this->db->where("lesson_plan_forum.subject_syllabus_id", $subject_syllabus_id);
        }
        $this->db->order_by("lesson_plan_forum.id", 'desc');
        $query  = $this->db->get("lesson_plan_forum");
        $result = $query->result_array();

        if ($this->superadmin_visible[0]['superadmin_restriction'] == 'disabled') {

            foreach ($result as $key => $value) {
                if ($value['type'] == 'staff') {
                    $staff_id    = $value['staff_id'];
                    $staffresult = $this->staff_model->getAll($staff_id);
                    if ($staffresult['role_id'] != 7) {
                        $result1[] = $value;
                    }
                } else {
                    $result1[] = $value;
                }               

            }

        } else {
            $result1 = $result;
        }

        return $result1;

    }

    public function addforummessage($data)
    {
        $this->db->insert("lesson_plan_forum", $data);

    }
    
    public function deleteforummessage($id)
    {
        $this->db->where("id", $id)->delete("lesson_plan_forum");
    }

}
