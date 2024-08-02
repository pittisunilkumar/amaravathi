<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class visitors_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

    }

    public function visitorbystudentid($student_session_id)
    {
        $this->db->select('visitors_book.*')->from('visitors_book');
        $this->db->where('visitors_book.student_session_id', $student_session_id);
        $this->db->order_by('visitors_book.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

}
