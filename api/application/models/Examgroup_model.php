<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Examgroup_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function studentExams($student_session_id)
    {
        $sql          = "SELECT exam_group_class_batch_exam_students.*,exam_group_class_batch_exams.id as `exam_group_class_batch_exam_id`,exam_group_class_batch_exams.exam,exam_group_class_batch_exams.description,exam_group_class_batch_exams.is_active  as `exam_active`,exam_group_class_batch_exams.is_publish as `result_publish` FROM `exam_group_class_batch_exam_students` INNER JOIN exam_group_class_batch_exams on exam_group_class_batch_exam_students.exam_group_class_batch_exam_id=exam_group_class_batch_exams.id WHERE student_session_id=" . $this->db->escape($student_session_id) . " and exam_group_class_batch_exams.is_active=1";
        $query        = $this->db->query($sql);
        $student_exam = $query->result();
        return $student_exam;
    }

    public function getExamSubjects($id = null)
    {
        $this->db->select('exam_group_class_batch_exam_subjects.*,subjects.name as `subject_name`,subjects.code as `subject_code`,subjects.type as `subject_type`')->from('exam_group_class_batch_exam_subjects');
        $this->db->join('subjects', 'subjects.id = exam_group_class_batch_exam_subjects.subject_id');
        $this->db->where('exam_group_class_batch_exam_subjects.exam_group_class_batch_exams_id', $id);
        $this->db->order_by('exam_group_class_batch_exam_subjects.id');
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function searchExamResult($student_session_id, $exam_id, $is_active = false, $is_publish = false)
    {
        $inner_sql = "";
        if ($is_active) {
            $inner_sql = "and exam_group_class_batch_exams.is_active=1 ";
        }
        if ($is_publish) {
            $inner_sql .= "and exam_group_class_batch_exams.is_publish=1 ";
        }
        $sql = "SELECT exam_group_class_batch_exam_students.*,exam_group_class_batch_exams.exam_group_id,exam_group_class_batch_exams.exam,exam_group_class_batch_exams.passing_percentage,exam_group_class_batch_exams.date_from,exam_group_class_batch_exams.date_to,exam_group_class_batch_exams.is_rank_generated,exam_group_class_batch_exams.description,exam_groups.name,exam_groups.exam_type FROM `exam_group_class_batch_exam_students` INNER JOIN exam_group_class_batch_exams on exam_group_class_batch_exams.id=exam_group_class_batch_exam_students.exam_group_class_batch_exam_id  INNER JOIN exam_groups on exam_groups.id=exam_group_class_batch_exams.exam_group_id WHERE student_session_id=" . $this->db->escape($student_session_id) . $inner_sql . "and exam_group_class_batch_exams.id=" . $this->db->escape($exam_id) . " ORDER BY id asc";
        $query        = $this->db->query($sql);
        $student_exam = $query->row();
        if (!empty($student_exam)) {
            $student_exam->exam_result = $this->getStudentExamResults($student_exam->exam_group_class_batch_exam_id, $student_exam->exam_group_id, $student_exam->id, $student_exam->student_id);
        }
        return $student_exam;
    }

    public function getStudentExamResults($exam_id, $post_exam_group_id, $exam_group_class_batch_exam_student_id, $student_id)
    {
        $result           = array('exam_connection' => 0, 'result' => array(), 'exams' => array(), 'exam_connection_list' => array());
        $exam_connection  = false;
        $exam_connections = $this->getExamGroupConnectionList($post_exam_group_id);
        if (!empty($exam_connections)) {
            $lastkey = key(array_slice($exam_connections, -1, 1, true));
            if ($exam_connections[$lastkey]->exam_group_class_batch_exams_id == $exam_id) {
                $exam_connection           = true;
                $result['exam_connection'] = 1;
            }
        }
        $result['exam_connection_list'] = $exam_connections;
        if ($exam_connection) {
            $new_array = array();
            foreach ($exam_connections as $exam_connection_key => $exam_connection_value) {

                $exam_group_class_batch_exam_student  = $this->getStudentByExamAndStudentID($student_id, $exam_connection_value->exam_group_class_batch_exams_id);
                $exam = $this->examgroup_model->getExamByID($exam_connection_value->exam_group_class_batch_exams_id);
                $result['exam_result']['exam_result_' . $exam_connection_value->exam_group_class_batch_exams_id] = $this->getStudentResultByExam($exam_connection_value->exam_group_class_batch_exams_id, $exam_group_class_batch_exam_student->id);
                $result['exams']['exam_' . $exam_connection_value->exam_group_class_batch_exams_id]              = $exam;
            }

        } else {
            $result['exam_connection_list'] = $exam_connections;
            $result['result'] = $this->getStudentResultByExam($exam_id, $exam_group_class_batch_exam_student_id);
        }

        return $result;
    }

    public function getExamGroupConnectionList($exam_group_id = null)
    {
        $this->db->select('exam_group_exam_connections.*')->from('exam_group_exam_connections');
        $this->db->where('exam_group_exam_connections.exam_group_id', $exam_group_id);
        $this->db->order_by('exam_group_exam_connections.id', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function getStudentResultByExam($exam_id, $student_id)
    {
        $sql   = "SELECT exam_group_class_batch_exam_subjects.*,exam_group_exam_results.id as `exam_group_exam_results_id`,exam_group_exam_results.attendence,exam_group_exam_results.get_marks,exam_group_exam_results.note,subjects.name,subjects.code FROM `exam_group_class_batch_exam_subjects` inner JOIN exam_group_exam_results on exam_group_exam_results.exam_group_class_batch_exam_subject_id=exam_group_class_batch_exam_subjects.id INNER JOIN exam_group_class_batch_exam_students on exam_group_exam_results.exam_group_class_batch_exam_student_id=exam_group_class_batch_exam_students.id and exam_group_class_batch_exam_students.id=" . $this->db->escape($student_id) . " INNER JOIN subjects on subjects.id=exam_group_class_batch_exam_subjects.subject_id WHERE exam_group_class_batch_exam_subjects.exam_group_class_batch_exams_id=" . $this->db->escape($exam_id);
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getStudentByExamAndStudentID($student_id, $exam_group_class_batch_exam_id)
    {
        $this->db->select()->from('exam_group_class_batch_exam_students');
        $this->db->where('student_id', $student_id);
        $this->db->where('exam_group_class_batch_exam_id', $exam_group_class_batch_exam_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }

    public function getExamByID($id = null)
    {
        $sql   = "SELECT exam_groups.name as `exam_group_name`,exam_groups.exam_type as `exam_group_type`,exam_groups.id as `exam_group_id`,exam_group_class_batch_exams.*,sessions.session FROM `exam_group_class_batch_exams` INNER JOIN exam_groups on exam_groups.id= exam_group_class_batch_exams.exam_group_id INNER JOIN sessions on sessions.id = exam_group_class_batch_exams.session_id WHERE exam_group_class_batch_exams.id=" . $this->db->escape($id);
        $query = $this->db->query($sql);
        return $query->row();
    }

}
