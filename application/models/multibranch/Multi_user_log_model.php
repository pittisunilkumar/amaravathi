<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Multi_user_log_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db_default = $this->load->database('default', true);
    }

    /*
    This function is used to get user log from all branch based on date
    */
    public function search($start_date = null, $end_date = null)
    {
        $default_db = $this->db_default->database;
        $sql        = "SELECT table0.*,'".$this->lang->line('home_branch')."' as branch_name ,IFNULL(`$default_db`.`classes`.`class`, '') as `class_name`,IFNULL(`$default_db`.`sections`.`section`, '') as `section_name` FROM `$default_db`.`userlog` table0 LEFT JOIN  `$default_db`.`class_sections` ON  `$default_db`.`class_sections`.`id` = `table0`.`class_section_id` LEFT JOIN  `$default_db`.`classes` ON  `$default_db`.`classes`.`id` =  `$default_db`.`class_sections`.`class_id` LEFT JOIN  `$default_db`.`sections` ON  `$default_db`.`sections`.`id` =  `$default_db`.`class_sections`.`section_id` WHERE   DATE(table0.`login_datetime`) <= " . $this->db->escape($end_date) . " AND   DATE(table0.`login_datetime`) >= " . $this->db->escape($start_date) . " UNION ALL ";
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

                $condition[] = "SELECT  table$branch_value->id.*,'$branch_value->branch_name' as branch_name ,IFNULL(`$db_dynamic`.`classes`.`class`,'') as `class_name`,IFNULL(`$db_dynamic`.`sections`.`section`,'') as `section_name` FROM `$db_dynamic`.`userlog` table$branch_value->id LEFT JOIN  `$db_dynamic`.`class_sections` ON  `$db_dynamic`.`class_sections`.`id` = table$branch_value->id.`class_section_id` LEFT JOIN  `$db_dynamic`.`classes` ON  `$db_dynamic`.`classes`.`id` =  `$db_dynamic`.`class_sections`.`class_id` LEFT JOIN  `$db_dynamic`.`sections` ON  `$db_dynamic`.`sections`.`id` =  `$db_dynamic`.`class_sections`.`section_id` WHERE  DATE(table$branch_value->id.`login_datetime`) <= " . $this->db->escape($end_date) . " AND  DATE(table$branch_value->id.`login_datetime`) >= " . $this->db->escape($start_date);

                //=============================

            }
            $sql = $sql . implode(" UNION ALL ", $condition);
        } else {
            $sql = substr($sql, 0, -11);
        }
        
        $sql ="SELECT * FROM ($sql) as tempTable";  
        $this->datatables->query($sql)
            ->searchable('branch_name,user,class_name,section_name,role,ipaddress,user_agent,login_datetime')
            ->orderable('branch_name,user,role,class_name,ipaddress,login_datetime,user_agent')
            ->query_where_enable(false);
        return $this->datatables->generate('json');
    }

}