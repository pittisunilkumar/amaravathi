<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bookissue_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function book_issuedByMemberID($member_id)
    {
        $this->db->select('book_issues.return_date,book_issues.duereturn_date as `due_return_date`,books.book_no,book_issues.issue_date,book_issues.is_returned,books.book_title,books.author,books.book_no,books.subject')
            ->from('book_issues')
            ->join('libarary_members', 'libarary_members.id = book_issues.member_id', 'left')
            ->join('books', 'books.id = book_issues.book_id', 'left')
            ->where('libarary_members.id', $member_id)
            ->order_by('book_issues.is_returned', 'asc');
        $result = $this->db->get();
        return $result->result_array();
    }

}
