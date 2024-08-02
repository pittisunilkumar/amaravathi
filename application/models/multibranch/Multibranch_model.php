<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Multibranch_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db_default = $this->load->database('default', true);

    }

    /*
    This function is used to get setting from all branch
    */
    public function getSchoolCurrentSessions()
    {
        $db_array=[];
        $default_db = $this->db_default->database;
        $this->db_default->select('sch_settings.start_month,sch_settings.name,sch_settings.id,sch_settings.session_id,sessions.session');
        $this->db_default->from('sch_settings');
        $this->db_default->join('sessions', 'sessions.id = sch_settings.session_id');
        $this->db_default->order_by('sch_settings.id');
        $query = $this->db_default->get();

        $res = $query->row();
        $res->name = $this->lang->line('home_branch');
      
        $db_array[$default_db]=$res;

        // =============================
        $branches = $this->get();
        $is_branch_available=false;
        if (!empty($branches)) {
        $is_branch_available=true;
            foreach ($branches as $branch_key => $branch_value) {
                $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);
                $db_dynamic_name   = $db_dynamic->database;
               
                //=============================

                $db_dynamic->select('sch_settings.start_month,sch_settings.name,sch_settings.id,sch_settings.session_id,sessions.session');
                $db_dynamic->from('sch_settings');
                $db_dynamic->join('sessions', 'sessions.id = sch_settings.session_id');
                $db_dynamic->order_by('sch_settings.id');
                $query = $db_dynamic->get();
                $res = $query->row();

                $db_array[$db_dynamic_name]=$res;
                //=============================

            }
          
        }
        return $db_array;

    }

    /*
    This function is used to get branch based on id
    */
    public function get($id = null, $verified = false)
    {
        $this->db_default->select()->from('multi_branch');
        if ($verified) {
            $this->db_default->where('is_verified', $verified);
        }
        if ($id != null) {
            $this->db_default->where('id', $id);
        } else {
            $this->db_default->order_by('id');
        }
        $query = $this->db_default->get();
        if ($id != null) {
            return $query->row();
        } else {
            return $query->result();
        }
    }

    /*
    This function is used to get branch list
    */
    public function getlist()
    {

        $default_db = $this->db_default->database;
        $sql        = "SELECT table0.* FROM `$default_db`.`multi_branch` table0";
        $this->datatables->query($sql)
            ->searchable('branch_name,hostname,username,`database_name`')
            ->orderable('branch_name,hostname,username,`database_name`')
            ->query_where_enable(false);
        return $this->datatables->generate('json');
    }

    /*
    This function is used to verify branch
    */
    public function verify_branch($database)
    {
        $config['hostname'] = $database['hostname'];
        $config['username'] = $database['username'];
        $config['password'] = $database['password'];
        $config['database'] = $database['database_name'];
        $config['dbdriver'] = 'mysqli';
        $config['dbprefix'] = "";
        $config['pconnect'] = false;
        $config['cache_on'] = false;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['autoinit'] = false;
        $config['db_debug'] = false;
        $config['dbcollat'] = "utf8_general_ci";

       

        try {
            $db_verify = $this->load->database($config, true);
            $error     = $db_verify->error();

            if ($error['code']) {
              
                return ['status'=>false,'message'=>$error['message']];

                
            }

            $db_verify->select('sch_settings.base_url');
            $db_verify->from('sch_settings');
            $query = $db_verify->get();
        

              return ['status'=>true,'message'=>'','result'=>$query->row()];

        } catch (Exception $e) {
             return ['status'=>false,'message'=> $this->lang->line('something_went_wrong')];
        }

    }

    /*
    This function is used to get brancl list
    */
    public function getDisprove()
    {
        $this->db_default->select()->from('multi_branch');
        $this->db_default->where(array('branch_name' => null));
        $query = $this->db_default->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function getName($database)
    {

        $config['hostname'] = $database['hostname'];
        $config['username'] = $database['username'];
        $config['password'] = $database['password'];
        $config['database'] = $database['database_name'];
        $config['dbdriver'] = 'mysqli';
        $config['dbprefix'] = "";
        $config['pconnect'] = false;
        $config['cache_on'] = false;
        $config['cachedir'] = "";
        $config['char_set'] = "utf8";
        $config['autoinit'] = false;
        $config['db_debug'] = false;
        $config['dbcollat'] = "utf8_general_ci";

        try {
            $db_verify = $this->load->database($config, true);
            $error     = $db_verify->error();
            if ($error['code']) {
                return false;
            }

            $db_verify->select('sch_settings.name');
            $db_verify->from('sch_settings');
            $query = $db_verify->get();
            return $query->row();

        } catch (Exception $e) {
            return false;
        }

    }

    /*
    This function is used to add or update branch
    */
    public function add($data, $setting, $purchase_code, $update_data = false)
    {
        if ($update_data) {
            $this->db_default->where('id', $data['id']);
            $this->db_default->update('multi_branch', $data);
        } else {
            $response = $this->auth->multiupdate($setting->base_url, $purchase_code);

            if ($response) {
                $response = json_decode($response);
                if (!$response->status) {

                    $response = json_encode($response);
                    return $response;
                } else {
//=====
                    $this->db_default->trans_start();
                    $this->db_default->trans_strict(false);
                    $data['branch_url']=$setting->base_url;
                    if (isset($data['id'])) {
                        $this->db_default->where('id', $data['id']);
                        $this->db_default->update('multi_branch', $data);
                        $insert_id = $data['id'];
                    } else {
                        $this->db_default->insert('multi_branch', $data);
                        $insert_id = $this->db_default->insert_id();

                    }
                    

                    $this->db_default->trans_complete();

                    if ($this->db_default->trans_status() === false) {

                        $this->db_default->trans_rollback();
                        return false;
                    } else {

                        $this->db_default->trans_commit();
                        $response->{"insert_id"} = $insert_id;
                        $response                = json_encode($response);
                        return $response;

                    }
                    //=======
                }
            }
        }

    }

    /*
    This function is used to update branch
    */
    public function updateSchoolBranch($update_array)
    {
        $this->db_default->update_batch('multi_branch', $update_array, 'id');
    }

    /*
    This function is used to remove branch
    */
    public function remove($id)
    {
        $this->db_default->trans_start(); # Starting Transaction
        $this->db_default->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db_default->where('id', $id);
        $this->db_default->delete('multi_branch');

        $message   = DELETE_RECORD_CONSTANT . " On  Multi Branch   id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        //======================Code End==============================

        $this->db_default->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db_default->trans_status() === false) {
            # Something went wrong.
            $this->db_default->trans_rollback();
            return false;
        } else {

            return true;
        }
    }

}
