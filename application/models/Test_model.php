<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Test_model extends MY_Model
{


    // public function getAll($id = null)
    // {
    //     $this->db->select()->from('classes');
    //     if ($id != null) {
    //         $this->db->where('id', $id);
    //     } else {
    //         $this->db->order_by('id');
    //     }
    //     $query = $this->db->get();
    //     if ($id != null) {
    //         $classlist = $query->row_array();
    //     } else {
    //         $classlist = $query->result_array();
    //     }

    //     return $classlist;
    // }
    
    public function fee_type($fgid){
        $this->db->select('feetype.id as typeid, feetype.type');
        $this->db->from('fee_groups_feetype');
        $this->db->join('feetype', 'fee_groups_feetype.feetype_id = feetype.id');
        $this->db->where('fee_groups_feetype.fee_groups_id', $fgid);
    
        $query = $this->db->get();
        return $query->result_array();
    }
    

    public function add($data, $data_setting = array())
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('students', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On students id " . $data['id'];
            $action    = "Update";
            $record_id = $insert_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            if (!empty($data_setting)) {

                if ($data_setting['adm_auto_insert']) {
                    if ($data_setting['adm_update_status'] == 0) {
                        $data_setting['adm_update_status'] = 1;
                        $this->setting_model->add($data_setting);
                    }
                }
                
                if ($data_setting['sroll_auto_insert']) {
                    if ($data_setting['sroll_update_status'] == 0) {
                        $data_setting['sroll_update_status'] = 1;
                        $this->setting_model->add($data_setting);
                    }
                }
                
                $this->db->insert('students', $data);
                $insert_id = $this->db->insert_id();
                $message   = INSERT_RECORD_CONSTANT . " On students id " . $insert_id;
                $action    = "Insert";
                $record_id = $insert_id;
                $this->log($message, $record_id, $action);

                return $insert_id;
            }
        }
    }


    public function add_student_session($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('session_id', $data['session_id']);
        $this->db->where('student_id', $data['student_id']);
        $q = $this->db->get('student_session');
        if ($q->num_rows() > 0) {
            $rec = $q->row_array();
            $this->db->where('id', $rec['id']);
            $this->db->update('student_session', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  student session id " . $rec['id'];
            $action    = "Update";
            $record_id = $rec['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('student_session', $data);
            $id        = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On  student session id " . $id;
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
            return $record_id;
        }
    }

    public function get_fee_group(){
        $this->db->select()->from('fee_groups');
        $query = $this->db->get();
        return $query->result_array();
    }

    
    public function fee_deposit($data, $send_to, $student_fees_discount_id = null)
    {
        if ($data['fee_category'] == "fees") {
            # code...
            $this->db->where('student_fees_master_id', $data['student_fees_master_id']);
            $this->db->where('fee_groups_feetype_id', $data['fee_groups_feetype_id']);
        }elseif($data['student_transport_fee_id'] > 0 && $data['fee_category'] == "transport"){
            $this->db->where('student_transport_fee_id', $data['student_transport_fee_id']);
        }
        
        unset($data['fee_category']);
        $q = $this->db->get('student_fees_deposite');
        if ($q->num_rows() > 0) {
            $desc = $data['amount_detail']['description'];
            $this->db->trans_start(); // Query will be rolled back
            $row = $q->row();
            $this->db->where('id', $row->id);
            $a                               = json_decode($row->amount_detail, true);
            $inv_no                          = max(array_keys($a)) + 1;
            $data['amount_detail']['inv_no'] = $inv_no;
            $a[$inv_no]                      = $data['amount_detail'];
            $data['amount_detail']           = json_encode($a);
            $this->db->update('student_fees_deposite', $data);

            if ($student_fees_discount_id != null) {
                $this->db->where('id', $student_fees_discount_id);
                $this->db->update('student_fees_discounts', array('status' => 'applied', 'description' => $desc, 'payment_id' => $row->id . "//" . $inv_no));
				
				$message = UPDATE_RECORD_CONSTANT . " On  student fees discounts id " . $student_fees_discount_id;
				$action = "Update";
				$record_id = $student_fees_discount_id;
				$this->log($message, $record_id, $action);
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();

                return false;
            } else {
                $this->db->trans_commit();
                return json_encode(array('invoice_id' => $row->id, 'sub_invoice_id' => $inv_no));
            }
            
        } else {

            $this->db->trans_start(); // Query will be rolled back
            $data['amount_detail']['inv_no'] = 1;
            $desc                            = $data['amount_detail']['description'];
            $data['amount_detail']           = json_encode(array('1' => $data['amount_detail']));
            $this->db->insert('student_fees_deposite', $data);
            $inserted_id = $this->db->insert_id();
            if ($student_fees_discount_id != null) {
                $this->db->where('id', $student_fees_discount_id);
                $this->db->update('student_fees_discounts', array('status' => 'applied', 'description' => $desc, 'payment_id' => $inserted_id . "//" . "1"));
            }

            $this->db->trans_complete(); # Completing transaction

            if ($this->db->trans_status() === false) {

                $this->db->trans_rollback();
                return false;
            } else {
                $this->db->trans_commit();
                return json_encode(array('invoice_id' => $inserted_id, 'sub_invoice_id' => 1));
            }
        }
    }


    public function getstudentid($adminNo) {
        $this->db->select('student_session.id'); // Select only the 'id' column
        $this->db->from('students');
        $this->db->join('student_session','student_session.student_id=students.id');
        $this->db->where('students.admission_no', $adminNo);
    
        // Execute the query and get the result
        $query = $this->db->get();
    
        // Check if there is a result
        if ($query->num_rows() > 0) {
            // Return the first row's ID
            return $query->row()->id;
        } else {
            // Return null or false, indicating no record found
            return null;
        }
    }

    public function feesiongroupid($grouid){
        $this->db->select('id');
        $this->db->from('fee_session_groups');
        $this->db->where('fee_session_groups.fee_groups_id',$grouid);

        $query = $this->db->get();
    
        // Check if there is a result
        if ($query->num_rows() > 0) {
            // Return the first row's ID
            return $query->row()->id;
        } else {
            // Return null or false, indicating no record found
            return null;
        }

    }

    public function student_fee_master_id($studentsessionid,$feegroupid){

        $this->db->select('id');
        $this->db->from('student_fees_master');
        $this->db->where('student_fees_master.student_session_id',$studentsessionid);
        $this->db->where('student_fees_master.fee_session_group_id',$feegroupid);

        $query = $this->db->get();
    
        // Check if there is a result
        if ($query->num_rows() > 0) {
            // Return the first row's ID
            return $query->row()->id;
        } else {
            // Return null or false, indicating no record found
            return null;
        }
    }

    public function fee_groups_feetypeid($feessessiongroupid,$feegroupid,$feetypeid){

        $this->db->select('id');
        $this->db->from('fee_groups_feetype');
        $this->db->where('fee_groups_feetype.fee_session_group_id',$feessessiongroupid);
        $this->db->where('fee_groups_feetype.fee_groups_id',$feegroupid);
        $this->db->where('fee_groups_feetype.feetype_id',$feetypeid);

        $query = $this->db->get();
    
        if ($query->num_rows() > 0) {
            return $query->row()->id;
        } else {
            return null;
        }
    }



    public function get_fee_group_id($name){
        $this->db->select()->from('fee_groups');
        $this->db->where('fee_groups.name',$name);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            // Return the first row's ID
            return $query->row()->id;
        } else {
            return null;
        }
        // return $query->result_array();
    }
    

    public function fee_type_id($name){

        // $this->db->select('feetype.id as typeid, feetype.type');
        // $this->db->from('fee_groups_feetype');
        // $this->db->join('feetype', 'fee_groups_feetype.feetype_id = feetype.id');
        // $this->db->where('fee_groups_feetype.fee_groups_id', $fgid);
    
        $this->db->select();
        $this->db->from('feetype');
        $this->db->where('feetype.code',$name);

        $query = $this->db->get();
        // Check if there is a result
        if ($query->num_rows() > 0) {
            // Return the first row's ID
            return $query->row()->id;
        } else {
            // Return null or false, indicating no record found
            return null;
        }
        // return $query->result_array();
    }
    

    public function getidstfdeposite($id){
        $this->db->select();
        $this->db->from('student_fees_deposite');
        $this->db->where('student_fees_deposite.id',$id);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            // Return the first row's ID
            return $query->row()->status;
        } else {
            // Return null or false, indicating no record found
            return null;
        }
    }


    public function assignfeecheck($stid,$feegroupid){
        $this->db->select();
        $this->db->from('student_fees_master');
        $this->db->where('student_fees_master.student_session_id',$stid);
        $this->db->where('student_fees_master.fee_session_group_id',$feegroupid);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {            
            return true;
        } else {
            return false;
        }

    }

    public function feemasterimport($data) {
        if ($this->db->insert('student_fees_master', $data)) {
            $insert_id = $this->db->insert_id();
            return true;
        } else {
            return false;
        }
    }
    
    
    
    public function getclassid($classname) {
        $this->db->select('classes.id'); // Select only the 'id' column
        $this->db->from('classes');
        $this->db->where('classes.class', $classname);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->id;
        } else {
            return null;
        }
    }


    public function sectionid($sectionname) {
        $this->db->select('sections.id'); // Select only the 'id' column
        $this->db->from('sections');
        $this->db->where('sections.section', $sectionname);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->id;
        } else {
            return null;
        }
    }
    
    public function getsessionid($name) {
        $this->db->select('sessions.id'); // Select only the 'id' column
        $this->db->from('sessions');
        $this->db->where('sessions.session', $name);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->id;
        } else {
            return null;
        }
    }
    
    
    public function getfeematerid($data)
    {       
        $this->db->where('student_session_id', $data['student_session_id']);
        $this->db->where('fee_session_group_id', $data['fee_session_group_id']);
        $q = $this->db->get('student_fees_master');

        if ($q->num_rows() > 0) {
            return $q->row()->id;
        } else {
            $this->db->insert('student_fees_master', $data);
            $id = $this->db->insert_id();            
            $message   = INSERT_RECORD_CONSTANT . " On student fees master id " . $id;
            $action    = "Insert";
            $record_id = $id;
            $this->log($message, $record_id, $action); 
                     
        } 
    }
    
    
    

}