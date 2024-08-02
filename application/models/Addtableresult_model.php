<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Addtableresult_model extends MY_Model
{

    protected $current_session;

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date    = $this->setting_model->getDateYmd();
    }

    public function getsession(){
        $this->db->select()->from('sessions');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getexamstype($session_id){
        $this->db->select()->from('examtype');
        $this->db->where('examtype.session_id',$session_id);
        $query = $this->db->get();
        return $query->result_array();
    }

}