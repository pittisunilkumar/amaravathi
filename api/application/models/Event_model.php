<?php

class Event_model extends CI_Model
{

    public function saveEvent($data)
    {
        if (!empty($data["id"])) {
            $this->db->where("id", $data["id"])->update("events", $data);
        } else {
            $this->db->insert("events", $data);
        }
    }

    public function deleteEvent($id)
    {
        $this->db->where("id", $id)->delete("events");
    }

    public function getPublic()
    {
        $condition = "event_type = 'public' or event_type = 'task' ";
        $query     = $this->db->where($condition)->get("events");
        return $query->result();
    }

    public function incompleteStudentTaskCounter($id)
    {
        $where_array = array("event_type" => "task", "is_active" => "no", "role_id" => 0, "event_for" => $id, "start_date" => date("Y-m-d"));
        $query       = $this->db->where($where_array)->get("events");
        return $query->result();
    }

    public function getPublicEvents($student_login_id, $date_from, $date_to)
    {
        $this->db->where("(event_type='public' OR (event_type='task' and event_for=" . $this->db->escape($student_login_id) . "))", null, false);
        $this->db->where('start_date BETWEEN "' . $date_from . '" AND "' . $date_to . '" OR (event_type="public" OR (event_type="task" and event_for=' . $this->db->escape($student_login_id) . ')) AND "' . $date_from . '" BETWEEN start_date AND end_date');
        $query = $this->db->get('events');
        return $query->result();
    }

    public function getTask($student_login_id)
    {     
        
        $query = $this->db->where(array('event_type' => 'task', 'event_for' => $student_login_id, 'role_id' => NULL))->order_by("is_active,start_date", "asc")->get("events");
        return $query->result_array();       
        
    }

}
