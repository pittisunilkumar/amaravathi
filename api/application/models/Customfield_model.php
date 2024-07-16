<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class customfield_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_custom_fields($belongs_to, $display_table = 0)
    {
        $this->db->from('custom_fields');
        $this->db->where('belong_to', $belongs_to);
        if ($display_table) {
            $this->db->where('visible_on_table', $display_table);
        }
        $this->db->order_by("custom_fields.weight", "asc");
        $query  = $this->db->get();
        $result = $query->result();
        return $result;
    }

    public function student_fields()
    {
        $fields     = $this->get_custom_fields('students', 0);
        $new_object = array();
        if (!empty($fields)) {
            foreach ($fields as $field_key => $field_value) {
                $new_object[$field_value->name] = 1;
                // $new_object[$field_value->id] = $field_value->id;
            }
        }
        return $new_object;
    }
    
    function get_custom_table_values($table_id, $belongs_to)
    {
        
        $sql = 'SELECT custom_field_values.*,custom_fields.name,custom_fields.type,custom_fields.belong_to  FROM `custom_field_values` RIGHT JOIN custom_fields on custom_fields.id=custom_field_values.custom_field_id  and belong_table_id=' . $this->db->escape($table_id) . ' WHERE custom_fields.belong_to=' . $this->db->escape($belongs_to) . ' ORDER by custom_fields.weight asc';

        $query = $this->db->query($sql);
        return $query->result();
    }

}
