<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class OfflinePayment_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

    }


    public function add($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('offline_fees_payments', $data);
        } else {
            $this->db->insert('offline_fees_payments', $data);
            return $this->db->insert_id();
        }
    }
    
    public function getPaymentlistByUser($student_session_id)
    {
        $this->db
            ->select('offline_fees_payments.*,student_fees_master.id as `student_fees_master_id`,fee_groups_feetype.due_date,feetype.type,feetype.code,fee_groups.name as `fee_group_name`,transport_feemaster.month,transport_feemaster.due_date as `transport_feemaster_due_date`,pickup_point.name as `pickup_point`,transport_route.route_title,classes.id AS `class_id`,student_session.id as student_session_id,students.id as `student_id`,classes.class,sections.id AS `section_id`,sections.section,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`, students.cast')
            ->join("student_fees_master", "student_fees_master.id=offline_fees_payments.student_fees_master_id", "left")
            ->join("fee_groups_feetype", "fee_groups_feetype.id=offline_fees_payments.fee_groups_feetype_id", "left")
            ->join("student_transport_fees", "student_transport_fees.id=offline_fees_payments.student_transport_fee_id", "left")

            ->join('fee_groups', 'fee_groups_feetype.fee_groups_id = fee_groups.id', 'left')
            ->join('feetype', 'fee_groups_feetype.feetype_id = feetype.id', 'left')
            ->join('transport_feemaster', 'student_transport_fees.transport_feemaster_id = transport_feemaster.id', 'left')
            ->join('route_pickup_point', 'student_transport_fees.route_pickup_point_id = route_pickup_point.id', 'left')
            ->join('pickup_point', 'route_pickup_point.pickup_point_id = pickup_point.id', 'left')
            ->join('transport_route', 'route_pickup_point.transport_route_id = transport_route.id', 'left')
            ->join('student_session', 'student_session.id = offline_fees_payments.student_session_id')
            ->join('students', 'student_session.student_id = students.id')
            ->join('classes', 'student_session.class_id = classes.id')
            ->join('sections', 'sections.id = student_session.section_id')
            ->join('categories', 'students.category_id = categories.id', 'left')
            ->where('offline_fees_payments.student_session_id', $student_session_id)             
            ->order_by('offline_fees_payments.submit_date', 'desc')
            ->from('offline_fees_payments');
            $query = $this->db->get();       
            return $query->result();
       

    }


}
