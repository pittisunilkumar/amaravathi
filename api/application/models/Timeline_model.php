<?php

class Timeline_model extends CI_Model
{

    public function getTimeline($id)
    {

        $query = $this->db->where(array("student_id" => $id, 'status' => 'yes'))->order_by("timeline_date", "asc")->get("student_timeline");
        return $query->result_array();
    }

    public function addedittimeline($data)
    {
        if (isset($data["id"])) {
            $this->db->where("id", $data["id"])->update("student_timeline", $data);
            return $data["id"];
        } else {
            $this->db->insert("student_timeline", $data);
            return $this->db->insert_id();
        }
    }

    public function deletetimeline($id)
    {
        $this->db->where("id", $id)->delete("student_timeline");
    }

}
