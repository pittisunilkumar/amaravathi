<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Leave_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function get($student_session_id = null, $id = null)
    {
        $this->db->select('student_applyleave.*,students.firstname,students.lastname,IFNULL(`staff`.`name`,0) as `staff_name`,students.id as student_id,staff.surname,IFNULL(`staff`.`name`,0) as `staff_name`')->from('student_applyleave')->join('student_session', 'student_session.id = student_applyleave.student_session_id')->join('students', 'students.id=student_session.student_id', 'inner')->join('staff', 'staff.id=student_applyleave.approve_by', 'left')->join('sections', 'sections.id = student_session.section_id');
        if ($id != null) {
            $this->db->where('student_applyleave.id', $id);
        }

        $this->db->where('student_applyleave.student_session_id', $student_session_id);
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->order_by('student_applyleave.id', 'desc');
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function add($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id'])->update('student_applyleave', $data);
            $id = $data['id'];
        } else {
            $this->db->insert('student_applyleave', $data);
            $id = $this->db->insert_id();
        }

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $id;
        }
    }

    public function update1($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('student_applyleave', $data);
        $id = $this->db->insert_id();
        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $id;
        }
    }

    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        $this->db->where('id', $id);
        $this->db->delete('student_applyleave');
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    public function getclassteacherbyclasssection($class_id, $section_id)
    {
        $this->db->select('staff.email');
        $this->db->from('class_teacher');
        $this->db->join('staff', 'staff.id=class_teacher.staff_id');
        $this->db->where('class_teacher.class_id', $class_id);
        $this->db->where('class_teacher.section_id', $section_id);
        $result = $this->db->get();
        return $result->result_array();
    }

}
