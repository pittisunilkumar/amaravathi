<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Multi_payroll_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db_default = $this->load->database('default', true);

    }
    
    /*
    This function is used to get staff payroll from all branch based on date
    */
    public function getbetweenpayrollReport($start_date, $end_date)
    {
      $default_db = $this->db_default->database;
        $sql        = "SELECT  '".$this->lang->line('home_branch')."' as branch_name,table0.`employee_id`, table0.`name`, `$default_db`.`roles`.`name` as `user_type`, table0.`surname`, `$default_db`.`staff_designation`.`designation`, `$default_db`.`department`.`department_name` as `department`, `$default_db`.`staff_payslip`.* FROM `$default_db`.`staff` table0 INNER JOIN `$default_db`.`staff_payslip` ON `$default_db`.`staff_payslip`.`staff_id` = table0.`id` LEFT JOIN  `$default_db`.`staff_designation` ON table0.`designation` = `$default_db`.`staff_designation`.`id` LEFT JOIN  `$default_db`.`department` ON table0.`department` = `$default_db`.`department`.`id` LEFT JOIN  `$default_db`.`staff_roles` ON  `$default_db`.`staff_roles`.`staff_id` = table0.`id` LEFT JOIN  `$default_db`.`roles` ON  `$default_db`.`staff_roles`.`role_id` = `$default_db`.`roles`.`id` WHERE date_format( `$default_db`.staff_payslip.payment_date,'%Y-%m-%d') between ".$this->db->escape($start_date)." and  ".$this->db->escape($end_date)." UNION ALL ";
        $condition  = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches = $this->multibranch_model->get();
        $is_branch_available=false;
        if (!empty($branches)) {
        $is_branch_available=true;
            foreach ($branches as $branch_key => $branch_value) {
                $this->{'db_' . $branch_value->id} = $this->load->database('branch_' . $branch_value->id, true);
                $db_dynamic   = $this->{'db_' . $branch_value->id}->database;
              

                $condition[]       = "SELECT  '$branch_value->branch_name' as branch_name,table$branch_value->id.`employee_id`, table$branch_value->id.`name`, `$db_dynamic`.`roles`.`name` as `user_type`, table$branch_value->id.`surname`, `$db_dynamic`.`staff_designation`.`designation`, `$db_dynamic`.`department`.`department_name` as `department`, `$db_dynamic`.`staff_payslip`.* FROM `$db_dynamic`.`staff` table$branch_value->id INNER JOIN `$db_dynamic`.`staff_payslip` ON `$db_dynamic`.`staff_payslip`.`staff_id` = table$branch_value->id.`id` LEFT JOIN  `$db_dynamic`.`staff_designation` ON table$branch_value->id.`designation` = `$db_dynamic`.`staff_designation`.`id` LEFT JOIN  `$db_dynamic`.`department` ON table$branch_value->id.`department` = `$db_dynamic`.`department`.`id` LEFT JOIN  `$db_dynamic`.`staff_roles` ON  `$db_dynamic`.`staff_roles`.`staff_id` = table$branch_value->id.`id` LEFT JOIN  `$db_dynamic`.`roles` ON  `$db_dynamic`.`staff_roles`.`role_id` = `$db_dynamic`.`roles`.`id` WHERE date_format( `$db_dynamic`.staff_payslip.payment_date,'%Y-%m-%d') between ".$this->db->escape($start_date)." and  ".$this->db->escape($end_date);

                //=============================

            }
            $sql = $sql . implode(" UNION ALL ", $condition);
        } else {
            $sql = substr($sql, 0, -11);
        }
        $sql=($is_branch_available) ? "SELECT * FROM ($sql) as tempTable" :$sql;
        $query = $this->db->query($sql);

        return $query->result_array();
     
    }

}