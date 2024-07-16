<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Addaccount_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null)
    {
        $this->db->select()->from('addaccount');
        $this->db->where('is_system', 0);
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
        $this->db->where('is_system', 0);
        $this->db->delete('addaccount');
        $message   = DELETE_RECORD_CONSTANT . " On  fee type id " . $id;
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




    // public function add($data)
    // {
    //     $this->db->trans_start(); # Starting Transaction
    //     $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        
    //     $dataa['type']=$data['type'];

    //     $existingRecord = $this->db->get_where('accounttype', $dataa)->row();
    //     if ($existingRecord) {
    //         // A record with the same values already exists, handle this situation as needed
    //         $this->db->trans_rollback();
    //         return false; // You can return an error message or handle it differently
    //     }

    //     $dataaa['code']=$data['code'];

    //     $existingRecord = $this->db->get_where('accounttype', $dataaa)->row();
    //     if ($existingRecord) {
    //         // A record with the same values already exists, handle this situation as needed
    //         $this->db->trans_rollback();
    //         return false; // You can return an error message or handle it differently
    //     }


    //     //=======================Code Start===========================
    //     if (isset($data['id'])) {
    //         $this->db->where('id', $data['id']);
    //         $this->db->update('accounttype', $data);
    //         $message   = UPDATE_RECORD_CONSTANT . " On  fee type id " . $data['id'];
    //         $action    = "Update";
    //         $record_id = $data['id'];
    //         $this->log($message, $record_id, $action);
    //     } else {

    //         $this->db->insert('accounttype', $data);
    //         $id        = $this->db->insert_id();
    //         $message   = INSERT_RECORD_CONSTANT . " On  fee type id " . $id;
    //         $action    = "Insert";
    //         $record_id = $id;
    //         $this->log($message, $record_id, $action);

    //     }
    //     //======================Code End==============================

    //     $this->db->trans_complete(); # Completing transaction
    //     /* Optional */

    //     if ($this->db->trans_status() === false) {
    //         # Something went wrong.
    //         $this->db->trans_rollback();
    //         return false;
    //     } else {
    //         return $id;
    //     }
    // }


    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('addaccount', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  fee type id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('addaccount', $data);
            $id        = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On  fee type id " . $id;
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

    public function check_exists($str)
    {
        $name = $this->security->xss_clean($str);
        $id   = $this->input->post('id');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_data_exists($name, $id)) {
            $this->form_validation->set_message('check_exists', $this->lang->line('already_exists'));
            return false;
        } else {
            return true;
        }
    }


    public function check_exists_code($str)
    {
        $name = $this->security->xss_clean($str);
        $id   = $this->input->post('id');
        if (!isset($id)) {
            $id = 0;
        }
        if ($this->check_code_data_exists($name, $id)) {
            $this->form_validation->set_message('check_exists', $this->lang->line('already_exists'));
            return false;
        } else {
            return true;
        }
    }

    public function check_code_data_exists($name, $id)
    {
        $this->db->where('code', $name);
        $this->db->where('id !=', $id);
        $query = $this->db->get('addaccount');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function check_data_exists($name, $id)
    {
        $this->db->where('name', $name);
        $this->db->where('id !=', $id);
        $query = $this->db->get('addaccount');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkFeetypeByName($name)
    {
        $this->db->where('name', $name);
        $query = $this->db->get('addaccount');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }



    public function getaccounttypee($classid) {
        $this->db->select('accounttype.id as accounttypeid,accounttype.type as accounttypename');
        $this->db->from('accountcategorygroup');
        $this->db->join('accounttype','accounttype.id = accountcategorygroup.accounttype_id');
        $this->db->where('accountcategorygroup.accountcategory_id',$classid);
        $this->db->order_by('accountcategorygroup.id');
        $query = $this->db->get();
        $res = $query->result_array();
        return $res;
    }



    public function getaddedaccounts() {
        $this->db->select('accountcategory.name as accountcategoryname,accounttype.type,addaccount.*');
        $this->db->from('addaccount');
        $this->db->join('accounttype','accounttype.id = addaccount.account_type');
        $this->db->join('accountcategory','accountcategory.id = addaccount.account_category');

        $query = $this->db->get();
        $res = $query->result_array();
        return $res;
    }

    public function getaddedaccountsfee($payment) {
        $this->db->select('addaccount.*');
        $this->db->from('addaccount');
        $this->db->where('addaccount.'.$payment,1);
        $this->db->where_not_in('addaccount.account_role', array('debitor'));

        $query = $this->db->get();
        $res = $query->result_array();
        return $res;
    }


    public function getaddedaccount($id) {
        $this->db->select('accountcategory.name as accountcategoryname,accounttype.type,addaccount.*');
        $this->db->from('addaccount');
        $this->db->where('addaccount.id',$id);
        $this->db->join('accounttype','accounttype.id = addaccount.account_type');
        $this->db->join('accountcategory','accountcategory.id = addaccount.account_category');

        $query = $this->db->get();
        return $query->row_array();
        // $res = $query->result_array();
        // return $res;
    }



    public function getaddeddebitedaccounts() {
        $this->db->select('accountcategory.name as accountcategoryname,accounttype.type,addaccount.*');
        $this->db->from('addaccount');
        $this->db->join('accounttype','accounttype.id = addaccount.account_type');
        $this->db->join('accountcategory','accountcategory.id = addaccount.account_category');
        $this->db->where_not_in('addaccount.account_role', array('creditor'));
        $query = $this->db->get();
        $res = $query->result_array();
        return $res;
    }


    public function getaddedcreditedaccounts() {
        $this->db->select('accountcategory.name as accountcategoryname,accounttype.type,addaccount.*');
        $this->db->from('addaccount');
        $this->db->join('accounttype','accounttype.id = addaccount.account_type');
        $this->db->join('accountcategory','accountcategory.id = addaccount.account_category');
        $this->db->where_not_in('addaccount.account_role', array('debitor'));
        $query = $this->db->get();
        $res = $query->result_array();
        return $res;
    }


    public function addingtranscation($data){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('accountreceipts', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  fee type id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('accountreceipts', $data);
            $id        = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On  fee type id " . $id;
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


    public function transcationremove($id,$type = null)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('receiptid', $id);
        if ($type != null){
            $this->db->where('type', $type);
        }
        $this->db->delete('accountreceipts');
        $message   = DELETE_RECORD_CONSTANT . " On  fee type id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        
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


    // public function gettranscations($classid){
    //     $this->db->select('accountreceipts.*');
    //     $this->db->from('accountreceipts');
    //     $this->db->where('accountreceipts.accountid',$classid);
    //     $this->db->order_by('accountreceipts.date');
    //     $query = $this->db->get();
    //     $res = $query->result_array();
    //     return $res;
    // }


    public function gettranscations($classid, $start_date = null, $end_date = null,$status = null) {
        $this->db->select('accountreceipts.*');
        $this->db->from('accountreceipts');
        $this->db->where('accountreceipts.accountid', $classid);
        if($start_date != null && $end_date != null ){
            $this->db->where('accountreceipts.date >=', $start_date);
            $this->db->where('accountreceipts.date <=', $end_date);
        }
        if($status == 'credit' || $status == 'debit'){
            $this->db->where('accountreceipts.status', $status);
        }
        $this->db->order_by('accountreceipts.date');
        $query = $this->db->get();
        $res = $query->result_array();
        return $res;
    }

    public function addtranscation($data){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('accounttranscations', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On  fee type id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('accounttranscations', $data);
            $id        = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On  fee type id " . $id;
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

    public function getaccounttranscations($classid, $start_date = null, $end_date = null) {
        $this->db->select('accounttranscations.*');
        $this->db->from('accounttranscations');
        $this->db->where('accountreceipts.accountid', $classid);
        if($start_date != null && $end_date != null ){
            $this->db->where('accounttranscations.date >=', $start_date);
            $this->db->where('accounttranscations.date <=', $end_date);
        }
        $this->db->order_by('accounttranscations.date');
        $query = $this->db->get();
        $res = $query->result_array();
        return $res;
    }
    

    public function gettranscationsreport($start_date = null, $end_date = null) {
        
        $this->db->select('accounttranscations.*');
        $this->db->from('accounttranscations');
        if($start_date != null && $end_date != null ){
            $this->db->where('accounttranscations.date >=', $start_date);
            $this->db->where('accounttranscations.date <=', $end_date);
        }
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }


    public function deletetrans($id){

        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('accounttranscations');
        $message   = DELETE_RECORD_CONSTANT . " On  fee type id " . $id;
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



    public function gettransactionssum($classid, $status, $start_date, $end_date) {
        $this->db->select('SUM(accountreceipts.amount) AS total_amount');
        $this->db->from('accountreceipts');
        $this->db->where('accountreceipts.accountid', $classid);
        $this->db->where('accountreceipts.status', $status);
        $this->db->where('accountreceipts.date >=', $start_date);
        $this->db->where('accountreceipts.date <=', $end_date);
        
        $query = $this->db->get();
        // echo $this->db->last_query(); // This will echo the SQL query being executed
        
        $row = $query->row_array();
        return $row['total_amount'];
    }
    


    public function getactivefinancialyear() {
        $this->db->select('*');
        $this->db->where('is_active',1);
        $query = $this->db->get('financialyear');
        return $query->row(); // Assuming only one active financial year at a time
    }
    
    


}
