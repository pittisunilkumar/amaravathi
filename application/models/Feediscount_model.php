<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feediscount_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function get($id = null, $order = "desc")
    {
        $this->db->select()->from('fees_discounts');
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

    public function getbyasc($id = null)
    {
        $this->db->select()->from('fees_discounts');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('fees_discounts');
        $message   = DELETE_RECORD_CONSTANT . " On  fees discounts id " . $id;
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

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('fees_discounts', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  fees discounts id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
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
        } else {
            $data['session_id'] = $this->current_session;
            $this->db->insert('fees_discounts', $data);
            $id        = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On  fees discounts id " . $id;
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
            return $id;
        }
    }

    public function updateStudentDiscount($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('student_fees_discounts', $data);
        }
    }

    public function allotdiscount($data)
    {
        $this->db->where('student_session_id', $data['student_session_id']);
        $this->db->where('fees_discount_id', $data['fees_discount_id']);
        $q = $this->db->get('student_fees_discounts');
        if ($q->num_rows() > 0) {
            return $q->row()->id;
        } else {
            $this->db->insert('student_fees_discounts', $data);
            return $this->db->insert_id();
        }
    }

    public function searchAssignFeeByClassSection($class_id = null, $section_id = null, $fees_discount_id = null, $category = null, $gender = null, $rte = null)
    {
        $sql = "SELECT IFNULL(`student_fees_discounts`.`id`, '0') as `student_fees_discount_id`,"
        . "`classes`.`id` AS `class_id`, `student_session`.`id` as `student_session_id`,"
        . " `students`.`id`, `classes`.`class`, `sections`.`id` AS `section_id`,"
        . " `sections`.`section`, `students`.`id`, `students`.`admission_no`,"
        . " `students`.`roll_no`, `students`.`admission_date`, `students`.`firstname`,"
        . " `students`.`lastname`,`students`.`middlename`, `students`.`image`, `students`.`mobileno`,"
        . " `students`.`email`, `students`.`state`, `students`.`city`, `students`.`pincode`,"
        . " `students`.`religion`, `students`.`dob`, `students`.`current_address`,"
        . " `students`.`permanent_address`, IFNULL(students.category_id, 0) as `category_id`,"
        . " IFNULL(categories.category, '') as `category`, `students`.`adhar_no`,"
        . " `students`.`samagra_id`, `students`.`bank_account_no`, `students`.`bank_name`,"
        . " `students`.`ifsc_code`, `students`.`guardian_name`, `students`.`guardian_relation`,"
        . " `students`.`guardian_phone`, `students`.`guardian_address`, `students`.`is_active`,"
        . " `students`.`created_at`, `students`.`updated_at`, `students`.`father_name`,"
        . " `students`.`rte`, `students`.`gender` FROM `students` JOIN `student_session` ON"
        . " `student_session`.`student_id` = `students`.`id` JOIN `classes` ON"
        . " `student_session`.`class_id` = `classes`.`id` JOIN `sections` ON"
        . " `sections`.`id` = `student_session`.`section_id` LEFT JOIN `categories` ON"
        . " `students`.`category_id` = `categories`.`id` LEFT JOIN"
        . " student_fees_discounts on student_fees_discounts.student_session_id=student_session.id"
        . " AND student_fees_discounts.fees_discount_id=" . $this->db->escape($fees_discount_id) .
        " WHERE `student_session`.`session_id` = " . $this->current_session;

        if ($class_id != null) {
            $sql .= " AND `student_session`.`class_id` = " . $this->db->escape($class_id);
        }
        if ($section_id != null) {
            $sql .= " AND `student_session`.`section_id` =" . $this->db->escape($section_id);
        }
        if ($category != null) {
            $sql .= " AND `students`.`category_id` =" . $this->db->escape($category);
        }
        if ($gender != null) {
            $sql .= " AND `students`.`gender` =" . $this->db->escape($gender);
        }
        if ($rte != null) {
            $sql .= " AND `students`.`rte` =" . $this->db->escape($rte);
        }
        $sql .= " AND students.is_active='yes'";
        $sql .= " ORDER BY `students`.`id`";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function deletedisstd($fees_discount_id, $array)
    {
        $this->db->where('fees_discount_id', $fees_discount_id);
        $this->db->where_in('student_session_id', $array);
        $this->db->delete('student_fees_discounts');
    }

    public function getStudentFeesDiscount($student_session_id = null)
    {
        $this->db->select('student_fees_discounts.id ,student_fees_discounts.student_session_id,student_fees_discounts.status,student_fees_discounts.payment_id,student_fees_discounts.description as `student_fees_discount_description`, student_fees_discounts.fees_discount_id, fees_discounts.name,fees_discounts.code,fees_discounts.amount,fees_discounts.description,fees_discounts.session_id,fees_discounts.type,fees_discounts.percentage')->from('student_fees_discounts');
        $this->db->join('fees_discounts', 'fees_discounts.id = student_fees_discounts.fees_discount_id');

        $this->db->where('student_fees_discounts.student_session_id', $student_session_id);
        $this->db->order_by('student_fees_discounts.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getDiscountNotApplied($student_session_id = null)
    {
        $query = "SELECT fees_discounts.*,student_fees_discounts.id as `student_fees_discount_id`,student_fees_discounts.status,student_fees_discounts.student_session_id,student_fees_discounts.payment_id FROM `student_fees_discounts` INNER JOIN fees_discounts on fees_discounts.id=student_fees_discounts.fees_discount_id WHERE student_session_id=$student_session_id and (student_fees_discounts.payment_id IS NULL OR student_fees_discounts.payment_id = '')";
        $query = $this->db->query($query);
        return $query->result();
    }





    

     // i am chaning

     public function allotdiscountapproval($data)
     {
         $this->db->where('student_session_id', $data['student_session_id']);
         $this->db->where('fees_discount_id', $data['fees_discount_id']);
         $q = $this->db->get('fees_discount_approval');
         if ($q->num_rows() > 0) {
             return $q->row()->id;
         } else {
             $this->db->insert('fees_discount_approval', $data);
             return $this->db->insert_id();
         }
     }
 
 
     // public function getStudentFeesDiscountallocated($student_session_id = null,$fees_discount_id)
     // {
     //     $this->db->select('fees_discount_approval.approval_status AS `disstatus`,fees_discount_approval.id ,fees_discount_approval.student_session_id,fees_discount_approval.fees_discount_id, fees_discounts.name,fees_discounts.code,fees_discounts.amount,fees_discounts.description,fees_discounts.session_id,fees_discounts.type,fees_discounts.percentage')->from('fees_discount_approval');
     //     $this->db->join('fees_discounts', 'fees_discounts.id = fees_discount_approval.fees_discount_id');
 
     //     $this->db->where('fees_discount_approval.student_session_id', $student_session_id);
     //     $this->db->where('fees_discount_approval.fees_discount_id',$fees_discount_id);
     //     $this->db->order_by('fees_discount_approval.id');
     //     $query = $this->db->get();
     //     return $query->result_array();
 
     // }
 
     public function getStudentFeesDiscountallocated($fees_discount_id)
     {
         $this->db->select('fees_discount_approval.approval_status AS `disstatus`,fees_discount_approval.id ,fees_discount_approval.student_session_id,fees_discount_approval.fees_discount_id, fees_discounts.name,fees_discounts.code,fees_discounts.amount,fees_discounts.description,fees_discounts.session_id,fees_discounts.type,fees_discounts.percentage')->from('fees_discount_approval');
         $this->db->join('fees_discounts', 'fees_discounts.id = fees_discount_approval.fees_discount_id');
 
         // $this->db->where('fees_discount_approval.student_session_id', $student_session_id);
         $this->db->where('fees_discount_approval.fees_discount_id',$fees_discount_id);
         $this->db->order_by('fees_discount_approval.id');
         $query = $this->db->get();
         return $query->result_array();
 
     }
 
 
     public function getfeegroups($student_id){
 
         // $this->db->select('student_fees_master.fee_session_group_id ,student_fees_master.id ,fee_groups.name,feetype.id AS `feetypeid`,feetype.type')->from('student_fees_master');
         // $this->db->join('fee_groups','fee_groups.id=student_fees_master.fee_session_group_id');
         // $this->db->join('fee_groups_feetype','fee_groups_feetype.fee_groups_id=student_fees_master.fee_session_group_id');
         // $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
         // $this->db->where('student_fees_master.student_session_id',$student_id);
         // $query = $this->db->get();
         // return $query->result_array();
         
         
         $this->db->select('student_fees_master.fee_session_group_id ,student_fees_master.id ,fee_groups.name,feetype.id AS `feetypeid`,feetype.type')->from('student_fees_master');
         $this->db->join('fee_groups','fee_groups.id=student_fees_master.fee_session_group_id');
         $this->db->join('fee_groups_feetype','student_fees_master.fee_session_group_id=fee_groups_feetype.fee_groups_id');
         $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
         $this->db->where('student_fees_master.student_session_id',$student_id);
         $query = $this->db->get();
         return $query->result_array();
 
 
         
     }
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
     public function discountlistgeting($classid,$section_id=null,$discountid,$statuss=null){
 
 
         $this->db->select('classes.class,sections.section,categories.category ,students.firstname,students.lastname,students.mobileno,students.dob,students.gender,students.father_name,students.admission_no')->from('fees_discount_approval');
         $this->db->join('students','students.id=fees_discount_approval.student_session_id');
         $this->db->join('categories','categories.id=students.category_id');
         $this->db->join('student_session','student_session.student_id=fees_discount_approval.student_session_id');
         $this->db->join('classes','classes.id=student_session.class_id');
         $this->db->join('sections','sections.id=student_session.section_id');
         $this->db->where('fees_discount_approval.fees_discount_id',$discountid);
         $this->db->where('student_session.class_id', $class_id);
 
 
         if($statuss!=null){
             if($statuss =="approved"){
                 $this->db->where('fees_discount_approval.approval_status',1);
             }
             if($statuss =="rejected"){
                 $this->db->where('fees_discount_approval.approval_status',2);
             }
             if($statuss =="pending"){
                 $this->db->where('fees_discount_approval.approval_status',0);
             }
         }
 
         if ($section_id != null) {
             $this->db->where('student_session.section_id', $section_id);
         }
 
         $query = $this->db->get();
         return $query->result_array();
 
     }
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
     public function getfeetypeid($student_id,$feetypeid){
         $this->db->select('student_fees_master.fee_session_group_id,student_fees_master.id,fee_groups.name,feetype.id AS `feetypeid`,feetype.type')->from('student_fees_master');
         $this->db->join('fee_groups','fee_groups.id=student_fees_master.fee_session_group_id');
         $this->db->join('fee_groups_feetype','fee_groups_feetype.fee_groups_id=student_fees_master.fee_session_group_id');
         $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
         $this->db->where('feetype.id',$feetypeid);
         $this->db->where('student_fees_master.student_session_id',$student_id);
         $query = $this->db->get();
         return $query->row_array();  
     }
 
     public function getfeeamount($discountid){
         $this->db->select('fees_discounts.id,fees_discounts.name,fees_discounts.amount')->from('fees_discounts');
         $this->db->where('fees_discounts.id',$discountid);
         $query = $this->db->get();
         return $query->row_array();
     }
 
     // public function updateapprovalstatus($fees_discount_id, $student_session_id,$approvalstatus)
     // {
     //     $data =  array();
     //     $data['approval_status'] = $approvalstatus;
     //     $this->db->where('fees_discount_id', $fees_discount_id);
     //     $this->db->where('student_session_id', $student_session_id);
     //     $this->db->update('fees_discount_approval',$data);
     //     return "success";
     // }
 
     // public function updateapprovalstatus($fees_discount_id, $student_session_id, $approvalstatus)
     // {
     //     $data = array('approval_status' => $approvalstatus);
     //     $this->db->where('fees_discount_id', $fees_discount_id);
     //     $this->db->where('student_session_id', $student_session_id);
 
     //     // Perform the update and return true if successful, false otherwise
     //     return $this->db->update('fees_discount_approval', $data);
     // }
 
 
 
 
     
     // public function updateApprovalStatus($certificateId, $studentId, $approvalStatus) {
     //     // Ensure the parameters are valid
     //     if (empty($certificateId) || empty($studentId)) {
     //         return false;
     //     }
 
     //     // Prepare data for update
     //     $data = array('approval_status' => $approvalStatus);
 
     //     // Set update conditions
     //     $this->db->where('certificate_id', $certificateId);
     //     $this->db->where('student_id', $studentId);
 
     //     // Perform the update and return true if successful, false otherwise
     //     return $this->db->update('fees_discount_approval', $data);
     // }
 
 
 
 
     public function updateApprovalStatus($certificateId, $studentId, $approvalStatus) {
         // Ensure the parameters are valid
         if (empty($certificateId) || empty($studentId)) {
             return false;
         }
     
         // Prepare data for update
         $data = array('approval_status' => $approvalStatus);
     
         // Set update conditions
         $this->db->where('fees_discount_id', $certificateId);
         $this->db->where('student_session_id', $studentId);
     
         // Perform the update
         $updateResult = $this->db->update('fees_discount_approval', $data);
     
         // Check for errors
         if (!$updateResult) {
             // Capture and log the database error
             $dbError = $this->db->error();
             error_log("Database error: " . $dbError['message']);
             return false;
         }
     
         // Return true if update was successful
         return true;
     }
     
     
     

     public function deletedisstdapproval($fees_discount_id, $array)
    {
        $this->db->where('fees_discount_id', $fees_discount_id);
        $this->db->where_in('student_session_id', $array);
        $this->db->delete('fees_discount_approval');
    }



    public function searchAssignFeeByClassSectionapproval($class_id = null, $section_id = null, $fees_discount_id = null, $category = null, $gender = null, $rte = null)
    {
        $sql = "SELECT IFNULL(`fees_discount_approval`.`id`, '0') as `student_fees_discount_id`,"
        . "`classes`.`id` AS `class_id`, `student_session`.`id` as `student_session_id`,"
        . " `students`.`id`, `classes`.`class`, `sections`.`id` AS `section_id`,"
        . " `sections`.`section`, `students`.`id`, `students`.`admission_no`,"
        . " `students`.`roll_no`, `students`.`admission_date`, `students`.`firstname`,"
        . " `students`.`lastname`,`students`.`middlename`, `students`.`image`, `students`.`mobileno`,"
        . " `students`.`email`, `students`.`state`, `students`.`city`, `students`.`pincode`,"
        . " `students`.`religion`, `students`.`dob`, `students`.`current_address`,"
        . " `students`.`permanent_address`, IFNULL(students.category_id, 0) as `category_id`,"
        . " IFNULL(categories.category, '') as `category`, `students`.`adhar_no`,"
        . " `students`.`samagra_id`, `students`.`bank_account_no`, `students`.`bank_name`,"
        . " `students`.`ifsc_code`, `students`.`guardian_name`, `students`.`guardian_relation`,"
        . " `students`.`guardian_phone`, `students`.`guardian_address`, `students`.`is_active`,"
        . " `students`.`created_at`, `students`.`updated_at`, `students`.`father_name`,"
        . " `students`.`rte`, `students`.`gender` FROM `students` JOIN `student_session` ON"
        . " `student_session`.`student_id` = `students`.`id` JOIN `classes` ON"
        . " `student_session`.`class_id` = `classes`.`id` JOIN `sections` ON"
        . " `sections`.`id` = `student_session`.`section_id` LEFT JOIN `categories` ON"
        . " `students`.`category_id` = `categories`.`id` LEFT JOIN"
        . " fees_discount_approval on fees_discount_approval.student_session_id=student_session.id"
        . " AND fees_discount_approval.fees_discount_id=" . $this->db->escape($fees_discount_id) .
        " WHERE `student_session`.`session_id` = " . $this->current_session;

        if ($class_id != null) {
            $sql .= " AND `student_session`.`class_id` = " . $this->db->escape($class_id);
        }
        if ($section_id != null) {
            $sql .= " AND `student_session`.`section_id` =" . $this->db->escape($section_id);
        }
        if ($category != null) {
            $sql .= " AND `students`.`category_id` =" . $this->db->escape($category);
        }
        if ($gender != null) {
            $sql .= " AND `students`.`gender` =" . $this->db->escape($gender);
        }
        if ($rte != null) {
            $sql .= " AND `students`.`rte` =" . $this->db->escape($rte);
        }
        $sql .= " AND students.is_active='yes'";
        $sql .= " ORDER BY `students`.`id`";

        $query = $this->db->query($sql);
        return $query->result_array();
    }


}
