<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Multi_income_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db_default = $this->load->database('default', true);
    }

    /*
    This function is used to get income from all branch
    */
    public function get()
    {
        $default_db = $this->db_default->database;
        $sql        = "SELECT * FROM `$default_db`.`income` table0 UNION ALL ";
        $condition  = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches = $this->multibranch_model->get();
        if (!empty($branches)) {
            foreach ($branches as $branch_key => $branch_value) {
                $this->{'db_' . $branch_value->id} = $this->load->database('branch_' . $branch_value->id, true);
                $db_dynamic                        = $this->{'db_' . $branch_value->id}->database;
                $condition[]                       = "SELECT * FROM `$db_dynamic`.`income` table$branch_value->id";

                //=============================

            }
            $sql = $sql . implode(" UNION ALL ", $condition);
        } else {
            $sql = substr($sql, 0, -11);
        }

        $query  = $this->db->query($sql);
        $result = $query->result();
        return $result;

    }

    /*
    This function is used to get income with income head from all branch
    */
    public function getincomelist()
    {
        $default_db = $this->db_default->database;
        $sql        = "SELECT table0.*,`$default_db`.`income_head`.`income_category`,'".$this->lang->line('home_branch')."' as branch_name FROM `$default_db`.`income` table0  JOIN `$default_db`.`income_head` ON `table0`.`income_head_id` = `income_head`.`id` UNION ALL ";
        $condition  = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {
                $this->{'db_' . $branch_value->id} = $this->load->database('branch_' . $branch_value->id, true);
                $db_dynamic                        = $this->{'db_' . $branch_value->id}->database;
                $condition[]                       = "SELECT table$branch_value->id.*,`$db_dynamic`.`income_head`.`income_category`,'$branch_value->branch_name' as branch_name FROM `$db_dynamic`.`income` table$branch_value->id  JOIN `$db_dynamic`.`income_head` ON `table$branch_value->id`.`income_head_id` = `income_head`.`id`";

                //=============================

            }
            $sql = $sql . implode(" UNION ALL ", $condition);
        } else {
            $sql = substr($sql, 0, -11);
        }
        $sql = ($is_branch_available) ? "SELECT * FROM ($sql) as tempTable" : $sql;

        $this->datatables->query($sql)
            ->searchable('name,invoice_no,date,amount,income_category')
            ->orderable('branch_name,name,note,invoice_no,date,income_category,amount')
            ->sort('id','desc')
            ->query_where_enable(false);
        return $this->datatables->generate('json');
    }

    /*
    This function is used to get income from all branch based on date
    */
    public function search($start_date = null, $end_date = null)
    {
        $default_db = $this->db_default->database;
        $sql        = "SELECT table0.`id`, table0.`date`, table0.`name`, table0.`invoice_no`, table0.`amount`, table0.`documents`, table0.`note`,  `$default_db`.`income_head`.`income_category`, table0.`income_head_id`,'".$this->lang->line('home_branch')."' as branch_name  FROM `$default_db`.`income`  table0 JOIN  `$default_db`.`income_head` ON  table0.`income_head_id` =  `$default_db`.`income_head`.`id` WHERE  table0.`date` <= " . $this->db->escape($end_date) . " AND  table0.`date` >= " . $this->db->escape($start_date) . " UNION ALL ";
        $condition  = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {
                $this->{'db_' . $branch_value->id} = $this->load->database('branch_' . $branch_value->id, true);
                $db_dynamic                        = $this->{'db_' . $branch_value->id}->database;

                $condition[] = "SELECT  table$branch_value->id.`id`,  table$branch_value->id.`date`,  table$branch_value->id.`name`,  table$branch_value->id.`invoice_no`,  table$branch_value->id.`amount`,  table$branch_value->id.`documents`,  table$branch_value->id.`note`, `$db_dynamic`.`income_head`.`income_category`,  table$branch_value->id.`income_head_id`,'$branch_value->branch_name' as branch_name FROM `$db_dynamic`.`income`   table$branch_value->id JOIN `$db_dynamic`.`income_head` ON   table$branch_value->id.`income_head_id` = `$db_dynamic`.`income_head`.`id` WHERE   table$branch_value->id.`date` <= " . $this->db->escape($end_date) . " AND table$branch_value->id.`date` >= " . $this->db->escape($start_date);

                //=============================

            }
            $sql = $sql . implode(" UNION ALL ", $condition);
        } else {
            $sql = substr($sql, 0, -11);
        }
        $sql ="SELECT * FROM ($sql) as tempTable";       
         

        $this->datatables->query($sql)
            ->searchable('branch_name,name,invoice_no,date,amount,income_category')
            ->orderable('branch_name,name,note,invoice_no,date,income_category,amount')
            ->query_where_enable(false);
        return $this->datatables->generate('json');
    }   

}