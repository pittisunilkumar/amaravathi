<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Conferencehistory_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function updatehistory($data, $type) {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('conference_id', $data['conference_id']);
        if ($type == "student") {
            $this->db->where('student_id', $data['student_id']);
        } elseif ($type == "staff") {
            $this->db->where('staff_id', $data['staff_id']);
        }

        $q = $this->db->get('conferences_history');

        if ($q->num_rows() > 0) {
            $row = $q->row();
            $total_hit = $row->total_hit + 1;
            $data['total_hit'] = $total_hit;
            $this->db->where('id', $row->id);
            $this->db->update('conferences_history', $data);
			$message = UPDATE_RECORD_CONSTANT . " On conferences history id " . $row->id;
            $action = "Update";
            $record_id = $row->id;
            $this->log($message, $record_id, $action);
        } else {

            $this->db->insert('conferences_history', $data);
			$id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On conferences history id " . $id;
            $action = "Insert";
            $record_id = $id;
            $this->log($message, $record_id, $action);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    public function getmeeting() {
        $sql = "SELECT conferences.*,(SELECT COUNT(*) FROM conferences_history WHERE conferences_history.conference_id=conferences.id) as `total_viewers`,`create_by`.`name` as `create_by_name`, `create_by`.`surname` as `create_by_surname`  FROM `conferences` JOIN `staff` as `create_by` ON `create_by`.`id` = `conferences`.`created_id` WHERE purpose='meeting' and status=2 ORDER BY DATE(`conferences`.`date`) DESC, `conferences`.`date` ASC";
        $query = $this->db->query($sql);
        return $query->result();
    }  

     public function getclass($class_id, $section_id) {
        $sql = "SELECT conferences.*,conference_sections.id as conferences_section_id,create_by.employee_id as create_bystaffid,for_create.employee_id as for_creatstaffid,(SELECT COUNT(*) FROM conferences_history INNER JOIN students on students.id=conferences_history.student_id INNER JOIN student_session on student_session.student_id=students.id WHERE student_session.class_id=" . $this->db->escape($class_id) . " and student_session.section_id= " . $this->db->escape($section_id) . " and conferences_history.conference_id=conferences.id) as `total_viewers`,`create_by`.`name` as `create_by_name`, `create_by`.`surname` as `create_by_surname`,`for_create`.`name` as `for_create_name`, `for_create`.`surname` as `for_create_surname`,roles.name as `create_by_role_name`,for_create_role.name as `create_for_role_name` FROM `conferences` JOIN `staff` as `create_by` ON `create_by`.`id` = `conferences`.`created_id` JOIN `staff` as `for_create` ON `for_create`.`id` = `conferences`.`staff_id` INNER JOIN staff_roles on staff_roles.staff_id=`conferences`.`created_id` INNER join roles on roles.id =staff_roles.role_id INNER JOIN staff_roles as `for_create_staff_role` on for_create_staff_role.staff_id=`conferences`.`staff_id` INNER join roles as `for_create_role`on for_create_role.id =for_create_staff_role.role_id INNER JOIN conference_sections on conferences.id=conference_sections.conference_id INNER JOIN class_sections on class_sections.id =conference_sections.cls_section_id WHERE purpose='class' and status=2 and conferences.session_id=" . $this->current_session . " and class_sections.class_id = " . $this->db->escape($class_id) . " and class_sections.section_id= " . $this->db->escape($section_id) . " ORDER BY DATE(`conferences`.`date`) DESC, `conferences`.`date` DESC";
        $query = $this->db->query($sql);
        return $query->result();
    } 

    public function getMeetingStaff($conference_id) {
        $this->db->select('conferences_history.*,for_create.name as `create_for_name`,for_create.surname as `create_for_surname,roles.name as `role_name`,for_create.employee_id')->from('conferences_history');
        $this->db->join('staff as for_create', 'for_create.id = conferences_history.staff_id');
        $this->db->join('staff_roles', 'staff_roles.id = for_create.id');
        $this->db->join('roles', 'roles.id = staff_roles.role_id');
        $this->db->where('conference_id', $conference_id);
        $this->db->order_by('conferences_history.id');
        $query = $this->db->get();
        return $query->result();
    }

        public function getLiveStudent($conference_id,$class_id,$section_id) {
        $this->db->select('conferences_history.*,student_session.class_id,student_session.section_id,students.admission_no, students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,students.mobileno, students.email,students.father_name')->from('conferences_history');
        $this->db->join('students', 'students.id = conferences_history.student_id');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->where('conference_id', $conference_id);
        $this->db->where('student_session.class_id', $class_id);
        $this->db->where('student_session.section_id', $section_id);
        $this->db->order_by('conferences_history.id');
        $query = $this->db->get();
        return $query->result();
    }

}
