<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Librarymember_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function checkIsMember($member_type, $id)
    {
        $this->db->select()->from('libarary_members');
        $this->db->where('libarary_members.member_id', $id);
        $this->db->where('libarary_members.member_type', $member_type);
        $query = $this->db->get();
        $result = $query->num_rows();
        if ($result > 0) {
            $row        = $query->row();
            $book_lists = $this->bookissue_model->book_issuedByMemberID($row->id);
            return $book_lists;
        } else {
            return array('success' => 0, 'status' => 401, 'errorMsg' => 'No books issued.');
        }
    }

}
