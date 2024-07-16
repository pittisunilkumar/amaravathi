<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feesessiongroupadding_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $parentid                     = $this->group_exists($data['fee_groups_id']);
        $data['fee_session_group_id'] = $parentid;
        $this->db->insert('fee_groups_feetypeadding', $data);
        $id        = $this->db->insert_id();
        $message   = INSERT_RECORD_CONSTANT . " On  fee groups feetype id " . $id;
        $action    = "Insert";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    public function getFeesByGroupByStudent($student_session_id)
    {
        $this->db->select('fee_session_groupsadding.*,fee_groupsadding.name as `group_name`,IFNULL(student_fees_masteradding.id,0) as `student_fees_master_id`');
        $this->db->from('fee_session_groupsadding');
        $this->db->join('fee_groupsadding', 'fee_groupsadding.id = fee_session_groupsadding.fee_groups_id');
        $this->db->join('student_fees_masteradding', 'student_fees_masteradding.student_session_id=' . $student_session_id . ' and student_fees_masteradding.fee_session_group_id=fee_session_groupsadding.id', 'LEFT');
        $this->db->where('fee_session_groupsadding.session_id', $this->current_session);
        $this->db->where('fee_groupsadding.is_system', 0);
        $this->db->order_by('student_fees_master_id', 'desc');
        $query  = $this->db->get();
        $result = $query->result();
        foreach ($result as $key => $value) {
            $value->feetypes = $this->getfeeTypeByGroup($value->id, $value->fee_groups_id);
        }
        return $result;
    }

    public function getFeesByGroup($id = null,$display_system=NULL)
    {
        $this->db->select('fee_session_groupsadding.*,fee_groupsadding.name as `group_name`,fee_groupsadding.is_system');
        $this->db->from('fee_session_groupsadding');
        $this->db->join('fee_groupsadding', 'fee_groupsadding.id = fee_session_groupsadding.fee_groups_id');
        $this->db->where('fee_session_groupsadding.session_id', $this->current_session);

         if ($display_system !== NULL) {
               $this->db->where('fee_groupsadding.is_system', $display_system);
        }

     
        if ($id != null) {
            $this->db->where('fee_session_groupsadding.id', $id);
        }
            $this->db->order_by('fee_groupsadding.is_system', 'DESC');
        $query = $this->db->get();
        $result = $query->result();
        foreach ($result as $key => $value) {
            $value->feetypes = $this->getfeeTypeByGroup($value->id, $value->fee_groups_id);
        }
        return $result;
    }

    public function getfeeTypeByGroup($fee_session_group_id, $id = null)
    {
        $this->db->select('fee_groups_feetypeadding.*,feetypeadding.type,feetypeadding.code');
        $this->db->from('fee_groups_feetypeadding');
        $this->db->join('feetypeadding', 'feetypeadding.id=fee_groups_feetypeadding.feetype_id');
        $this->db->where('fee_groups_feetypeadding.fee_groups_id', $id);
        $this->db->where('fee_groups_feetypeadding.fee_session_group_id', $fee_session_group_id);
        $this->db->order_by('fee_groups_feetypeadding.id', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function group_exists($fee_groups_id)
    {
        $this->db->where('fee_groups_id', $fee_groups_id);
        $this->db->where('session_id', $this->current_session);
        $query = $this->db->get('fee_session_groupsadding');
        if ($query->num_rows() > 0) {
            return $query->row()->id;
        } else {
            $data = array('fee_groups_id' => $fee_groups_id, 'session_id' => $this->current_session);
            $this->db->insert('fee_session_groupsadding', $data);
            return $this->db->insert_id();
        }
    }

    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $sql = "delete fee_groups_feetypeadding.* FROM fee_groups_feetypeadding JOIN fee_session_groupsadding ON fee_session_groupsadding.id = fee_groups_feetypeadding.fee_session_group_id WHERE fee_session_groupsadding.id = ?";
        $this->db->query($sql, array($id));
        $this->db->where('id', $id);
        $this->db->delete('fee_session_groupsadding');

        $message   = DELETE_RECORD_CONSTANT . " On fee session groups id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    public function checkExists($data)
    {
        $this->db->where('fee_session_group_id', $data['fee_session_group_id']);
        $this->db->where('fee_groups_id', $data['fee_groups_id']);
        $this->db->where('feetype_id', $data['feetype_id']);
        $this->db->where('session_id', $this->current_session);
        $q = $this->db->get('fee_groups_feetypeadding');

        if ($q->num_rows() > 0) {
            return $q->row()->id;
        } else {
            return false;
        }
    }

    public function valid_check_exists($str)
    {
        $fee_groups_id = $this->input->post('fee_groups_id');
        $feetype_id    = $this->input->post('feetype_id');
        $id            = $this->input->post('id');

        if (!isset($id)) {
            $id = 0;
        }

        if ($this->check_data_exists($fee_groups_id, $feetype_id, $id)) {
            $this->form_validation->set_message('check_exists', $this->lang->line('feegroup_combination_already_exists'));
            return false;
        } else {
            return true;
        }
    }

    public function check_data_exists($fee_groups_id, $feetype_id, $id)
    {
        $this->db->where('fee_groups_id', $fee_groups_id);
        $this->db->where('session_id', $this->current_session);
        $query = $this->db->get('fee_session_groupsadding');

        if ($query->num_rows() > 0) {
            $fee_session_group_id = $query->row()->id;
            $this->db->where('fee_session_group_id', $fee_session_group_id);
            $this->db->where('fee_groups_id', $fee_groups_id);
            $this->db->where('feetype_id', $feetype_id);
            $this->db->where('id !=', $id);
            $query = $this->db->get('fee_groups_feetypeadding');
            if ($query->num_rows() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    
    public function getgroups($id = null, $order = "desc")
    {
        $this->db->select()->from('fee_groupsadding');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id ' . $order);
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }


    public function getClassBySection($id){
        $this->db->select('feetypeadding.id,feetypeadding.type');
        $this->db->from('fee_groups_feetypeadding');
        $this->db->where('fee_groups_feetypeadding.fee_groups_id',$id);
        $this->db->where('fee_groups_feetypeadding.session_id',$this->current_session);
        $this->db->join('feetypeadding','feetypeadding.id=fee_groups_feetypeadding.feetype_id');
        $query = $this->db->get();
        return $query->result_array();
    }
    

    // public function searchByClassSectionAnddiscountStatus($class_id,$certifid,$section_id = null,$statuss)
    // {   

    //     $this->db->select('fee_groups_feetypeadding.id as fee_groups_feetypeadding_id,fee_groupsadding.name as grpname,feetypeadding.type,student_fees_amountadding.amount as amt,classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,students.lastname,students.image,  students.mobileno,students.email,students.state,students.city,students.pincode,students.religion,students.dob ,students.current_address,students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code, students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.app_key,students.parent_app_key,students.rte,students.gender,vehicles.vehicle_no,transport_route.route_title,route_pickup_point.id as `route_pickup_point_id`,pickup_point.name as `pickup_point`')->from('students');
    //     $this->db->join('student_session', 'student_session.student_id = students.id');
    //     $this->db->join('classes', 'student_session.class_id = classes.id');
    //     $this->db->join('sections', 'sections.id = student_session.section_id');
    //     $this->db->join('categories', 'students.category_id = categories.id', 'left');
    //     $this->db->join('route_pickup_point', 'student_session.route_pickup_point_id = route_pickup_point.id', 'left');
    //     $this->db->join('pickup_point', 'pickup_point.id = route_pickup_point.pickup_point_id', 'left');
    //     $this->db->join('vehicle_routes', 'student_session.vehroute_id = vehicle_routes.id', 'left');
    //     $this->db->join('transport_route', 'vehicle_routes.route_id = transport_route.id', 'left');
    //     $this->db->join('vehicles', 'vehicle_routes.vehicle_id = vehicles.id', 'left');

    //     $this->db->join('student_fees_amountadding','student_fees_amountadding.student_session_id=student_session.id');

    //     $this->db->join('fee_groups_feetypeadding','fee_groups_feetypeadding.id=student_fees_amountadding.fee_groups_feetype_id');
    //     $this->db->where('fee_groups_feetypeadding.fee_groups_id',$certifid);
    //     $this->db->where('fee_groups_feetypeadding.feetype_id',$statuss);
    //     $this->db->join('feetypeadding','feetypeadding.id='.$statuss);
    //     $this->db->join('fee_groupsadding','fee_groupsadding.id='.$certifid);

    //     $this->db->where('student_session.session_id', $this->current_session);
    //     $this->db->where('students.is_active', "yes");
    
    //     $this->db->where('student_session.class_id', $class_id);
        
    //     if ($section_id != null) {
    //         $this->db->where('student_session.section_id', $section_id);
    //     }

    //     $this->db->order_by('students.admission_no', 'asc');
    //     $query = $this->db->get();
    //     $result = $query->result_array();
    //     return $result;
    // }



    public function searchByClassSectionAnddiscountStatus($class_id,$certifid,$section_id = null,$statuss)
    {   

        $this->db->select('student_fees_amountadding.id as amountid,fee_groups_feetypeadding.id as fee_groups_feetypeadding_id,fee_groupsadding.name as grpname,feetypeadding.type,student_fees_amountadding.amount as amt,classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,students.lastname,students.image,  students.mobileno,students.email,students.state,students.city,students.pincode,students.religion,students.dob ,students.current_address,students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code, students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.app_key,students.parent_app_key,students.rte,students.gender,vehicles.vehicle_no,transport_route.route_title,route_pickup_point.id as `route_pickup_point_id`,pickup_point.name as `pickup_point`')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->join('route_pickup_point', 'student_session.route_pickup_point_id = route_pickup_point.id', 'left');
        $this->db->join('pickup_point', 'pickup_point.id = route_pickup_point.pickup_point_id', 'left');
        $this->db->join('vehicle_routes', 'student_session.vehroute_id = vehicle_routes.id', 'left');
        $this->db->join('transport_route', 'vehicle_routes.route_id = transport_route.id', 'left');
        $this->db->join('vehicles', 'vehicle_routes.vehicle_id = vehicles.id', 'left');

        $this->db->join('student_fees_amountadding','student_fees_amountadding.student_session_id=student_session.id');

        $this->db->join('fee_groups_feetypeadding','fee_groups_feetypeadding.id=student_fees_amountadding.fee_groups_feetype_id');
        $this->db->where('fee_groups_feetypeadding.fee_groups_id',$certifid);
        $this->db->where('fee_groups_feetypeadding.feetype_id',$statuss);
        $this->db->join('feetypeadding','feetypeadding.id='.$statuss);
        $this->db->join('fee_groupsadding','fee_groupsadding.id='.$certifid);

        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('students.is_active', "yes");
    
        $this->db->where('student_session.class_id', $class_id);
        
        if ($section_id != null) {
            $this->db->where('student_session.section_id', $section_id);
        }

        $this->db->order_by('students.admission_no', 'asc');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }



    public function updateadditionalfee($data){
        
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('student_fees_amountadding', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  student sibling id " . $data['id'];
            $action    = "Update";
            $record_id = $insert_id = $data['id'];
            $this->log($message, $record_id, $action);
        } 
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
        
    
    }



}
