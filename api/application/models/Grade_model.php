<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Grade_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function getGradeDetails()
    {
        $grade_types     = array();
        $grade_type_list = $this->config->item('exam_type');

        if (!empty($grade_type_list)) {
            foreach ($grade_type_list as $exm_type_key => $exm_type_value) {
                $grade_types[] = array(
                    'exam_key'          => $exm_type_key,
                    'exm_type_value'    => $exm_type_value,
                    'exam_grade_values' => $this->getfeeTypeByGroup($exm_type_key),
                );
            }
        }
        return $grade_types;
    }

    public function getfeeTypeByGroup($exm_type_key)
    {
        $this->db->select()->from('grades');
        $this->db->where('grades.exam_type', $exm_type_key);
        $this->db->order_by('grades.id');
        $query = $this->db->get();
        return $query->result();
    }

}
