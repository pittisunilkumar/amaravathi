<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feediscount_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function getStudentFeesDiscount($student_session_id = null)
    {
        $this->db->select('student_fees_discounts.id ,student_fees_discounts.student_session_id,student_fees_discounts.status,IFNULL(student_fees_discounts.payment_id, "") as `payment_id`,student_fees_discounts.description as `student_fees_discount_description`, student_fees_discounts.fees_discount_id, fees_discounts.name,fees_discounts.code,fees_discounts.amount,fees_discounts.description,fees_discounts.session_id,IFNULL(fees_discounts.percentage, "") as percentage ,IFNULL(fees_discounts.type, "fix") as type')->from('student_fees_discounts');
        $this->db->join('fees_discounts', 'fees_discounts.id = student_fees_discounts.fees_discount_id');
        $this->db->where('student_fees_discounts.student_session_id', $student_session_id);
        $this->db->order_by('student_fees_discounts.id');
        $query = $this->db->get();
        return $query->result_array();
    }

}
