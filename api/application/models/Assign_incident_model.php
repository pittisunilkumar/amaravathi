<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class assign_incident_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->current_session = $this->setting_model->getCurrentSession();

    }

    public function totalpoints($student_id)
    {
        $this->db->select('sum(point) as totalpoints');
        $this->db->from('student_incidents');
        $this->db->join('student_behaviour', 'student_behaviour.id=student_incidents.incident_id');
        $this->db->where('student_incidents.session_id', $this->current_session);
        $this->db->where('student_incidents.student_id', $student_id);
        $result = $this->db->get();
        return $result->row_array();
    }
    
    public function behaviour_settings()
    {
        $this->db->select('behaviour_settings.*');
        $this->db->from('behaviour_settings');        
        $result = $this->db->get();
        return $result->row_array();
    }

    public function studentbehaviour($student_id)
    {
        $this->db->select('student_behaviour.title,student_behaviour.point,student_behaviour.description,student_incidents.id,student_incidents.created_at,students.id as student_id,students.firstname,students.middlename,students.lastname,students.admission_no,sessions.session,staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,roles.name as role_name,roles.id as role_id');
        $this->db->from('student_incidents');
        $this->db->join('students', 'students.id=student_incidents.student_id');
        $this->db->join('student_behaviour', 'student_behaviour.id=student_incidents.incident_id');
        $this->db->join('sessions', 'sessions.id=student_incidents.session_id');
        $this->db->join('staff', 'staff.id=student_incidents.assign_by');
        $this->db->join('staff_roles', 'staff_roles.staff_id=staff.id');
        $this->db->join('roles', 'roles.id=staff_roles.role_id');        
        $this->db->where('student_incidents.session_id', $this->current_session);
        $this->db->where('student_incidents.student_id', $student_id);
        $this->db->order_by('student_incidents.id', 'desc');
        $result = $this->db->get();
        return $result->result_array();
    }
	
	public function getincidentcomments($student_incident_id=NULL) {
        $this->db->select('student_incident_comments.comment, student_incident_comments.type,student_incident_comments.created_date, staff.name as staff_name,staff.surname as staff_surname,staff.employee_id as staff_employee_id,staff.image as staff_image,staff.gender,students.firstname,students.middlename,students.lastname,students.admission_no,students.image as student_image,student_incident_comments.id, student_incident_comments.staff_id,student_incident_comments.student_id,roles.name as role_name,students.gender as stud_gender');
        $this->db->from('student_incident_comments');
        $this->db->join('staff', 'staff.id=student_incident_comments.staff_id','left');
        $this->db->join('staff_roles', 'staff_roles.staff_id=staff.id','left');
        $this->db->join('roles', 'roles.id=staff_roles.role_id','left');        
        $this->db->join('students', 'students.id=student_incident_comments.student_id','left');     
        $this->db->where('student_incident_comments.student_incident_id', $student_incident_id);
        $this->db->order_by('student_incident_comments.id','desc');
        $result = $this->db->get();
        return $result->result_array();
    }
	
	public function addincidentcomments($data)
    {
         
        $this->db->insert('student_incident_comments', $data);
        $insert_id = $this->db->insert_id();
        
    }
    
    public function delete($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        $this->db->where('id', $id);
        $this->db->delete('student_incident_comments');
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
    
    public function getCommentsCount($student_incident_id) {
        $this->db->select('student_incident_comments.id');
        $this->db->from('student_incident_comments');    
        $this->db->where('student_incident_comments.student_incident_id', $student_incident_id);
         
        $result = $this->db->get();
        return $result->result_array();
    }

}
