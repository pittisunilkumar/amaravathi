<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Homework_model extends CI_Model
{
    private $current_session;
    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function getStudentHomeworkPercentage($student_session_id, $class_id, $section_id)
    {
        $sql = "SELECT count(*) as total_homework,(SELECT COUNT(homework_evaluation.id) as `aa` FROM `homework` LEFT JOIN homework_evaluation on homework_evaluation.homework_id=homework.id and homework_evaluation.student_session_id= " . $this->db->escape($student_session_id) . " WHERE homework.class_id=" . $this->db->escape($class_id) . " AND homework.section_id=" . $this->db->escape($section_id) . " AND homework.session_id=" . $this->current_session . ") as `completed`  FROM `homework` WHERE class_id=" . $this->db->escape($class_id) . " AND section_id=" . $this->db->escape($section_id) . " AND session_id=" . $this->current_session;

        $query = $this->db->query($sql);
        return $query->row();
    }

    public function getStudentHomework_old($class_id, $section_id, $student_session_id)
    {
        $where_condition = array(
            'homework.class_id'   => $class_id,
            'homework.section_id' => $section_id,
            'homework.session_id' => $this->current_session);
        $this->db->select("`homework`.*, `subjects`.`name`, `sections`.`section`, `classes`.`class`, `create_staff`.`name` as `staff_created`, IFNULL(`evaluate_staff`.`name`,0) as `staff_evaluated`,IFNULL( homework_evaluation.id, 0) as homework_evaluation_id, subjects.id as subject_id");
        $this->db->join("classes", "classes.id = homework.class_id");
        $this->db->join("sections", "sections.id = homework.section_id");
        $this->db->join("subject_group_subjects", "subject_group_subjects.id = homework.subject_group_subject_id");
        $this->db->join("subjects", "subjects.id = subject_group_subjects.subject_id");
        $this->db->join('homework_evaluation', 'homework_evaluation.student_session_id=' . $student_session_id . ' and homework_evaluation.homework_id=homework.id', 'left');
        $this->db->join("staff create_staff", "create_staff.id = homework.created_by", 'left');
        $this->db->join("staff evaluate_staff", "evaluate_staff.id = homework.evaluated_by", 'left');
        $this->db->where($where_condition);
        $this->db->order_by('homework.homework_date', 'desc');
        $query = $this->db->get("homework");
        return $query->result_array();
    }

    public function getStudentHomework($class_id, $section_id, $student_session_id, $student_id, $subject_group_subject_id)
    {
        $condition = "";
        if (!empty($subject_group_subject_id)) {
            $condition = " and homework.subject_group_subject_id = $subject_group_subject_id";
        }

        $sql = "SELECT `homework`.*,IFNULL(homework_evaluation.id,0) as homework_evaluation_id,IFNULL(submit_assignment.id,0) as homework_submitted_id,homework_evaluation.note,homework_evaluation.marks as evaluation_marks, `classes`.`class`, `sections`.`section`, `subject_group_subjects`.`subject_id`, `subject_group_subjects`.`id` as `subject_group_subject_id`, `subjects`.`name` as `subject_name`,`subjects`.`code` as `subject_code`, `subject_groups`.`id` as `subject_groups_id`, `subject_groups`.`name`, staff.name as created_by_name, staff.surname as created_by_surname, staff.employee_id as created_by_employee_id FROM `homework`
        LEFT JOIN homework_evaluation on homework_evaluation.homework_id=homework.id and homework_evaluation.student_session_id=" . $this->db->escape($student_session_id) . "
        LEFT JOIN submit_assignment on submit_assignment.homework_id=homework.id and submit_assignment.student_id=" . $this->db->escape($student_id) . "
        JOIN `staff` ON `staff`.`id` = `homework`.`created_by`
        JOIN `classes` ON `classes`.`id` = `homework`.`class_id`
        JOIN `sections` ON `sections`.`id` = `homework`.`section_id`
        JOIN `subject_group_subjects` ON `subject_group_subjects`.`id` = `homework`.`subject_group_subject_id`
        JOIN `subjects` ON `subjects`.`id` = `subject_group_subjects`.`subject_id`
        JOIN `subject_groups` ON `subject_group_subjects`.`subject_group_id`=`subject_groups`.`id`
        WHERE `homework`.`class_id` = " . $this->db->escape($class_id) . " AND `homework`.`section_id` = " . $this->db->escape($section_id) . " AND `homework`.`session_id` = " . $this->current_session . $condition . "  order by homework.homework_date desc";

        $query  = $this->db->query($sql);
        $result = $query->result_array();

        foreach ($result as $key => $value) {
            $result[$key]['status'] = 'pending';
            $checkstatus            = $this->homework_model->checkstatus($value['id'], $student_id);
            if ($checkstatus['record_count'] != 0) {
                $result[$key]['status'] = 'submitted';
            }
            if ($value['homework_evaluation_id'] != 0) {
                $result[$key]['status'] = 'evaluated';
            }
        }

        return $result;

    }

    public function checkstatus($homework_id, $student_id)
    {
        return $this->db->select('count(submit_assignment.id) as record_count')->from('submit_assignment')
            ->where('submit_assignment.homework_id', $homework_id)->where('submit_assignment.student_id', $student_id)->get()->row_array();
    }

    public function add($data)
    {
        $this->db->where('homework_id', $data['homework_id']);
        $this->db->where('student_id', $data['student_id']);
        $q = $this->db->get('submit_assignment');
        if ($q->num_rows() > 0) {
            $this->db->where('homework_id', $data['homework_id']);
            $this->db->where('student_id', $data['student_id']);
            $this->db->update('submit_assignment', $data);
        } else {
            $this->db->insert('submit_assignment', $data);
        }
    }

    public function adddailyassignment_old($data)
    {
        $this->db->insert('daily_assignment', $data);
        return $this->db->insert_id();
    }

    public function getdailyassignment($student_id, $student_session_id)
    {
        return $this->db->select('daily_assignment.*,subjects.name as subject_name,subjects.code as subject_code')
            ->from('daily_assignment')
            ->join('student_session', 'student_session.session_id=daily_assignment.student_session_id', 'left')
            ->join('subject_group_subjects', 'subject_group_subjects.id=daily_assignment.subject_group_subject_id', 'left')

            ->join('subjects', 'subjects.id=subject_group_subjects.subject_id')
            ->where('daily_assignment.student_session_id', $student_session_id)
            ->or_where('student_session.student_id', $student_id)
            ->get()
            ->result_array();
    }

    public function adddailyassignment($data)
    {
        if (isset($data["id"]) && $data["id"] > 0) {
            $this->db->where("id", $data["id"])->update("daily_assignment", $data);
            $insert_id = $data["id"];
        } else {
            $this->db->insert("daily_assignment", $data);
            $insert_id = $this->db->insert_id();
        }

        return $insert_id;
    }

    public function deletedailyassignment($id)
    {
        $this->db->where("id", $id)
            ->delete("daily_assignment");
    }

}
