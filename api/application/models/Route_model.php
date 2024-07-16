<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Route_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null)
    {
        $this->db->select()->from('transport_route');
        if ($id != null) {
            $this->db->where('transport_route.id', $id);
        } else {
            $this->db->order_by('transport_route.id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row();
        } else {
            return $query->result();
        }
    }

    public function add($data)
    {
        $this->db->insert('transport_route', $data);
        return array('status' => 201, 'message' => 'Data has been created.');
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id)->update('transport_route', $data);
        return array('status' => 200, 'message' => 'Data has been updated.');
    }

    public function delete($id)
    {
        $this->db->where('id', $id)->delete('transport_route');
        return array('status' => 200, 'message' => 'Data has been deleted.');
    }

}
