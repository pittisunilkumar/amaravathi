<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studenttransportfee_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function add($data_insert, $student_session_id, $remove_ids, $route_pickup_point_id)
    {

        $new_inserted = array();
        $this->db->trans_begin();

        $this->db->where('route_pickup_point_id !=', $route_pickup_point_id);
        $this->db->where('student_session_id', $student_session_id);
        $this->db->delete('student_transport_fees');

        if (!empty($remove_ids)) {

            $this->db->where_in('id', $remove_ids);
            $this->db->where('student_session_id', $student_session_id);
            $this->db->delete('student_transport_fees');
        }

        if (!empty($data_insert)) {
            foreach ($data_insert as $insert_key => $insert_value) {
                $this->db->insert('student_transport_fees', $insert_value);

            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function update($data_insert, $student_session_id)
    {

        $new_inserted = array();
        $this->db->trans_begin();

        if (!empty($data_insert)) {
            $row_insert = array();
            foreach ($data_insert as $insert_key => $insert_value) {
                # code...
                $this->db->where('student_session_id', $student_session_id);
                $this->db->where('route_pickup_point_id', $insert_value['route_pickup_point_id']);
                $this->db->where('transport_feemaster_id', $insert_value['transport_feemaster_id']);
                $q = $this->db->get('student_transport_fees');

                if ($q->num_rows() > 0) {
                    $row_insert[] = $q->row()->id;
                } else {

                    $this->db->insert('student_transport_fees', $data_insert[$insert_key]);
                    $row_insert[] = $this->db->insert_id();
                }
            }
            $this->db->where('student_session_id', $student_session_id);
            $this->db->where_not_in('id', $row_insert);
            $this->db->delete('student_transport_fees');

        } else {

            $this->db->where('student_session_id', $student_session_id);
            $this->db->delete('student_transport_fees');
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function getTransportFeeByStudentSession($student_session_id, $route_pickup_point_id)
    {

        if ($student_session_id != null && $route_pickup_point_id != null) {

            $sql = "SELECT transport_feemaster.*,student_transport_fees.id as student_transport_fee_id FROM `transport_feemaster` LEFT JOIN student_transport_fees on transport_feemaster.id = student_transport_fees.transport_feemaster_id and student_transport_fees.route_pickup_point_id=" . $route_pickup_point_id . " and student_transport_fees.student_session_id=" . $student_session_id . " WHERE transport_feemaster.session_id=" . $this->current_session . " ORDER by transport_feemaster.id";

        } elseif ($student_session_id != null && $route_pickup_point_id == null) {
            $sql = "SELECT transport_feemaster.*,IFNULL(student_transport_fees.id,0) as student_transport_fee_id FROM `transport_feemaster` LEFT JOIN student_transport_fees on transport_feemaster.id = student_transport_fees.transport_feemaster_id and student_transport_fees.student_session_id=" . $student_session_id . " WHERE transport_feemaster.session_id=" . $this->current_session . " ORDER by transport_feemaster.id";
        }

        $query = $this->db->query($sql);
        return $query->result_array();

    }

     public function getTransportFeeByMonthStudentSession($student_session_id, $route_pickup_point_id,$month)
    {

        if ($student_session_id != null && $route_pickup_point_id != null) {

            $sql = "SELECT transport_feemaster.*,student_transport_fees.id as student_transport_fee_id FROM `transport_feemaster` LEFT JOIN student_transport_fees on transport_feemaster.id = student_transport_fees.transport_feemaster_id and student_transport_fees.route_pickup_point_id=" . $route_pickup_point_id . " and student_transport_fees.student_session_id=" . $student_session_id . " WHERE transport_feemaster.session_id=" . $this->current_session . " and transport_feemaster.month='" . $month . "' ORDER by transport_feemaster.id";

        } elseif ($student_session_id != null && $route_pickup_point_id == null) {
            $sql = "SELECT transport_feemaster.*,IFNULL(student_transport_fees.id,0) as student_transport_fee_id FROM `transport_feemaster` LEFT JOIN student_transport_fees on transport_feemaster.id = student_transport_fees.transport_feemaster_id and student_transport_fees.student_session_id=" . $student_session_id . " WHERE transport_feemaster.session_id=" . $this->current_session . " and transport_feemaster.month='". $month . "' ORDER by transport_feemaster.id";
        }

        $query = $this->db->query($sql);
        return $query->row_array();

    }

    public function getTransportFeeMasterByStudentTransportID($student_transport_fee_id)
    {

        $this->db->select('transport_feemaster.*,route_pickup_point.fees as `amount`');
        $this->db->join('transport_feemaster', 'transport_feemaster.id=student_transport_fees.transport_feemaster_id');
        $this->db->join('route_pickup_point', 'route_pickup_point.id=student_transport_fees.route_pickup_point_id');
        $this->db->where('student_transport_fees.id', $student_transport_fee_id);
        $q = $this->db->get('student_transport_fees');

        return $q->row();

    }

}
