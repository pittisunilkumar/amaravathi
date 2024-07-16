<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Accountcategorygroup_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }
    
    public function get($id = null)
    {
        $this->db->select()->from('accountcategorygroup');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function getAll()
    {
        $query = "SELECT accountcategorygroup.*,accountcategory.name as `fee_group_name` FROM `accountcategorygroup` INNER JOIN accountcategory on accountcategorygroup.accountcategory_id=accountcategory.id group BY accountcategory_id";

        $query                = $this->db->query($query);
        $fee_group_type_array = $query->result();

        if (!empty($fee_group_type_array)) {
            foreach ($fee_group_type_array as $key => $value) {
                $value->accounttypes = $this->getaccounttypeByGroup($value->accountcategory_id);
            }
        }
        return $fee_group_type_array;
    }

    public function getSingleFeeGroup($id)
    {
        $query = "SELECT accountcategorygroup.*,accountcategory.name as `fee_group_name` FROM `accountcategorygroup` INNER JOIN accountcategory on accountcategorygroup.accountcategory_id=accountcategory.id WHERE accountcategorygroup.id=$id";
        $query                = $this->db->query($query);
        $fee_group_type_array = $query->result();

        if (!empty($fee_group_type_array)) {
            foreach ($fee_group_type_array as $key => $value) {
                $value->accounttypes = $this->getaccounttypeByGroup($value->accountcategory_id);
            }
        }
        return $fee_group_type_array;
    }

   public function getFeeGroupByIDAndStudentSessionID($id,$student_session_id)
    {
        $query = "SELECT accountcategorygroup.*,accountcategory.name as `fee_group_name`,accounttype.type,accounttype.code,student_fees_master.is_system,student_fees_master.amount as `balance_fee_master_amount` FROM `accountcategorygroup` INNER JOIN accountcategory on accountcategorygroup.accountcategory_id=accountcategory.id INNER JOIN accounttype on accounttype.id=accountcategorygroup.accounttype_id INNER JOIN fee_session_groups on accountcategorygroup.fee_session_group_id=fee_session_groups.id  INNER JOIN student_fees_master on student_fees_master.fee_session_group_id=accountcategorygroup.fee_session_group_id WHERE accountcategorygroup.id=". $this->db->escape($id)." and student_fees_master.student_session_id=" . $this->db->escape($student_session_id);
        $query                = $this->db->query($query);
        $fee_group_type_array = $query->row();
        return $fee_group_type_array;
    }

    public function getFeeGroupByID($id)
    {
        $query                = "SELECT accountcategorygroup.*,accountcategory.name as `fee_group_name`,accounttype.type,accounttype.code FROM `accountcategorygroup` INNER JOIN accountcategory on accountcategorygroup.accountcategory_id=accountcategory.id INNER JOIN accounttype on accounttype.id=accountcategorygroup.accounttype_id WHERE accountcategorygroup.id=" . $this->db->escape($id);
        $query                = $this->db->query($query);
        $fee_group_type_array = $query->row();
        return $fee_group_type_array;
    }

    public function getaccounttypeByGroup($id = null)
    {
        $this->db->select('accountcategorygroup.*,accounttype.type,accounttype.code');
        $this->db->from('accountcategorygroup');
        $this->db->join('accounttype', 'accounttype.id=accountcategorygroup.accounttype_id');
        $this->db->where('accountcategorygroup.accountcategory_id', $id);
        $this->db->order_by('accountcategorygroup.id', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        // $this->db->select()->from('accountcategorygroup');
        // $this->db->where('id', $id);
        // $query                = $this->db->get();
        // $result               = $query->row();
        // $fee_session_group_id = $result->fee_session_group_id;

        // $this->db->where('fee_session_group_id', $fee_session_group_id);
        // $num_rows = $this->db->count_all_results('accountcategorygroup');
        // if ($num_rows == 1) {
        //     $this->db->where('id', $fee_session_group_id);
        //     $this->db->delete('fee_session_groups');
        // }
        $this->db->where('id', $id);
        $this->db->delete('accountcategorygroup');
        $message   = DELETE_RECORD_CONSTANT . " On fee groups fee type id " . $id;
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

    public function add1($data)
    {
        $class_section = $this->input->post('cls_sec');
        $this->db->trans_begin();
        $data_insert = array(
            'accountcategory_id' => $this->input->post('accountcategory_id'),
            'accounttype_id'    => $this->input->post('accounttype_id'),
        );
        $this->db->insert('accountcategorygroup', $data_insert);
        $fee_group_type_id = $this->db->insert_id();
        $array             = array();
        foreach ($class_section as $clssec_key => $clssec_value) {
            $sub_array = array('accountcategorygroup' => $fee_group_type_id, 'class_section_id' => $clssec_value);
            $array[]   = $sub_array;
        }

        $this->db->insert_batch('fee_class_section_group', $array);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('accountcategorygroup', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  fee groups fee type id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('accountcategorygroup', $data);
            $id        = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On fee groups fee type id " . $id;
            $action    = "Insert";
            $record_id = $id;
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
            return $id;
        }

    }

    public function getaccounttypeDueDateReminder($date)
    {
        $query                = "SELECT accountcategorygroup.*,accounttype.type,accounttype.code,accountcategory.name as fee_group_name FROM `accountcategorygroup` INNER JOIN accounttype on accounttype.id=accountcategorygroup.accounttype_id INNER JOIN accountcategory on accountcategorygroup.accountcategory_id=accountcategory.id WHERE due_date= " . $this->db->escape($date);
        $query                = $this->db->query($query);
        $fee_group_type_array = $query->result();
        return $fee_group_type_array;
    }

    public function getaccounttypeStudents($fee_session_group_id, $accountcategorygroup_id)
    {
        $query = "SELECT student_fees_master.*,student_fees_deposite.student_fees_master_id,student_fees_deposite.accountcategorygroup_id,student_fees_deposite.amount_detail, students.id as `student_id`, students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,students.dob ,students.current_address,    students.permanent_address,students.category_id, IFNULL(categories.category, '') as `category`,   students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_email,`classes`.`class`,students.guardian_address,students.is_active,`students`.`father_name`,`students`.`app_key`,`students`.`parent_app_key`,`students`.`gender` FROM `student_fees_master` LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.accountcategorygroup_id = " . $this->db->escape($accountcategorygroup_id) . " INNER JOIN student_session on student_session.id=student_fees_master.student_session_id INNER JOIN students on students.id=student_session.student_id JOIN `classes` ON `student_session`.`class_id` = `classes`.`id` LEFT JOIN `categories` ON `students`.`category_id` = `categories`.`id` WHERE students.is_active='yes' and student_fees_master.fee_session_group_id =" . $this->db->escape($fee_session_group_id);
        $query                = $this->db->query($query);
        $fee_group_type_array = $query->result();
        return $fee_group_type_array;
    }



    




    public function valid_check_exists($str)
    {
        $fee_groups_id = $this->input->post('fee_groups_id');
        $feetype_id    = $this->input->post('feetype_id');
        

        if ($this->check_data_exists($fee_groups_id, $feetype_id)) {
            $this->form_validation->set_message('check_exists', $this->lang->line('feegroup_combination_already_exists'));
            return false;
        } else {
            return true;
        }
    }



    public function check_data_exists($fee_groups_id, $feetype_id)
    {
        
        $this->db->where('accountcategory_id', $fee_groups_id);
        $this->db->where('accounttype_id', $feetype_id);
        $query = $this->db->get('accountcategorygroup');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
        
    }



    public function getFeesByGroup($id = null,$display_system=NULL)
    {
        $this->db->select('accountcategory.id,accountcategory.name as `group_name`,accountcategory.is_system');
        $this->db->from('accountcategory');
        
         if ($display_system !== NULL) {
               $this->db->where('accountcategory.is_system', $display_system);
        }

     
        if ($id != null) {
            $this->db->where('accountcategory.id', $id);
        }
            $this->db->order_by('accountcategory.id', 'asc');
        $query = $this->db->get();
        $result = $query->result();
        foreach ($result as $key => $value) {
            $value->feetypes = $this->getfeeTypeByGroup($value->id);
        }
        return $result;
    }

    public function getfeeTypeByGroup($id = null)
    {
        $this->db->select('accountcategorygroup.*,accounttype.type,accounttype.code');
        $this->db->from('accountcategorygroup');
        $this->db->join('accounttype', 'accounttype.id=accountcategorygroup.accounttype_id');
        $this->db->where('accountcategorygroup.accountcategory_id', $id);
        $this->db->order_by('accountcategorygroup.id', 'asc');
        $query = $this->db->get();
        return $query->result();
    }



    public function removegrp($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $sql = "DELETE FROM accountcategorygroup WHERE accountcategory_id = ?";
        $this->db->query($sql, array($id));
        $this->db->delete('accountcategorygroup');

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
}
