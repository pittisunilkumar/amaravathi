<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Multi_student_fee_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db_default = $this->load->database('default', true);
        $this->current_session = $this->setting_model->getCurrentSession();

    }    

    /*
    This function is used to get expense
    */
    public function getexpenselist()
    {
        $default_db = $this->db_default->database;
        $sql        = "SELECT table0.*,`$default_db`.`expense_head`.`exp_category`,'".$this->lang->line('home_branch')."' as branch_name FROM `$default_db`.`expenses` table0  JOIN `$default_db`.`expense_head` ON `table0`.`exp_head_id` = `expense_head`.`id` UNION ALL ";
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
                $condition[]  = "SELECT table$branch_value->id.*,`$db_dynamic`.`expense_head`.`exp_category`,'$branch_value->branch_name' as branch_name FROM `$db_dynamic`.`expenses` table$branch_value->id  JOIN `$db_dynamic`.`expense_head` ON `table$branch_value->id`.`exp_head_id` = `expense_head`.`id`";
        //=============================
            }
            $sql = $sql . implode(" UNION ALL ", $condition);
        } else {
            $sql = substr($sql, 0, -11);
        }
        $sql= "SELECT * FROM ($sql) as tempTable";
      
        $this->datatables->query($sql)
            ->searchable('name,invoice_no,date,amount,exp_category')
            ->orderable('branch_name,name,note,invoice_no,date,exp_category,amount')
            ->query_where_enable(false);
        return $this->datatables->generate('json');
    }

    /*
    This function is used to get student fees 
    */
    public function getCurrentSessionStudentFees()
    {
        $default_db = $this->db_default->database;
        $sql ="SELECT '$default_db' as db_name, '".$this->lang->line('home_branch')."' as branch_name, table0.*,`$default_db`.fee_session_groups.fee_groups_id,`$default_db`.fee_session_groups.session_id,`$default_db`.fee_groups.name,`$default_db`.fee_groups.is_system as `fee_groups_is_system`,`$default_db`.fee_groups_feetype.amount as `fee_amount`,`$default_db`.fee_groups_feetype.id as fee_groups_feetype_id,`$default_db`.student_fees_deposite.id as `student_fees_deposite_id`,`$default_db`.student_fees_deposite.amount_detail,`$default_db`.students.admission_no , `$default_db`.students.roll_no,`$default_db`.students.admission_date,`$default_db`.students.firstname,`$default_db`.students.middlename,  `$default_db`.students.lastname,`$default_db`.students.father_name,`$default_db`.students.image, `$default_db`.students.mobileno, `$default_db`.students.email ,`$default_db`.students.state ,   `$default_db`.students.city , `$default_db`.students.pincode ,classes.class,sections.section FROM `$default_db`.`student_fees_master` table0 INNER JOIN `$default_db`.fee_session_groups on `$default_db`.fee_session_groups.id=table0.fee_session_group_id INNER JOIN `$default_db`.student_session on `$default_db`.student_session.id=table0.student_session_id INNER JOIN `$default_db`.students on `$default_db`.students.id=`$default_db`.student_session.student_id inner join `$default_db`.classes on `$default_db`.student_session.class_id=`$default_db`.classes.id INNER JOIN `$default_db`.sections on `$default_db`.sections.id=`$default_db`.student_session.section_id inner join `$default_db`.fee_groups on `$default_db`.fee_groups.id=`$default_db`.fee_session_groups.fee_groups_id INNER JOIN `$default_db`.fee_groups_feetype on `$default_db`.fee_groups.id=`$default_db`.fee_groups_feetype.fee_groups_id LEFT JOIN `$default_db`.student_fees_deposite on `$default_db`.student_fees_deposite.student_fees_master_id=table0.id and `$default_db`.student_fees_deposite.fee_groups_feetype_id=`$default_db`.fee_groups_feetype.id WHERE `$default_db`.student_session.session_id='" . $this->current_session . "' and  `$default_db`.fee_session_groups.session_id='" . $this->current_session . "'  UNION ALL ";

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
                $condition[]  = "SELECT '$db_dynamic' as db_name, '$branch_value->branch_name' as branch_name,table$branch_value->id.*,`$db_dynamic`.fee_session_groups.fee_groups_id,`$db_dynamic`.fee_session_groups.session_id,`$db_dynamic`.fee_groups.name,`$db_dynamic`.fee_groups.is_system as `fee_groups_is_system`,`$db_dynamic`.fee_groups_feetype.amount as `fee_amount`,`$db_dynamic`.fee_groups_feetype.id as fee_groups_feetype_id,`$db_dynamic`.student_fees_deposite.id as `student_fees_deposite_id`,`$db_dynamic`.student_fees_deposite.amount_detail,`$db_dynamic`.students.admission_no , `$db_dynamic`.students.roll_no,`$db_dynamic`.students.admission_date,`$db_dynamic`.students.firstname,`$db_dynamic`.students.middlename,  `$db_dynamic`.students.lastname,`$db_dynamic`.students.father_name,`$db_dynamic`.students.image, `$db_dynamic`.students.mobileno, `$db_dynamic`.students.email ,`$db_dynamic`.students.state ,   `$db_dynamic`.students.city , `$db_dynamic`.students.pincode ,classes.class,sections.section FROM `$db_dynamic`.`student_fees_master` table$branch_value->id INNER JOIN `$db_dynamic`.fee_session_groups on `$db_dynamic`.fee_session_groups.id=table$branch_value->id.fee_session_group_id INNER JOIN `$db_dynamic`.student_session on `$db_dynamic`.student_session.id=table$branch_value->id.student_session_id INNER JOIN  `$db_dynamic`.students on `$db_dynamic`.students.id=student_session.student_id INNER JOIN `$db_dynamic`.classes on student_session.class_id=classes.id INNER JOIN `$db_dynamic`.sections on sections.id=student_session.section_id INNER JOIN `$db_dynamic`.fee_groups on `$db_dynamic`.fee_groups.id=`$db_dynamic`.fee_session_groups.fee_groups_id INNER JOIN `$db_dynamic`.fee_groups_feetype on `$db_dynamic`.fee_groups.id=`$db_dynamic`.fee_groups_feetype.fee_groups_id LEFT JOIN `$db_dynamic`.student_fees_deposite on `$db_dynamic`.student_fees_deposite.student_fees_master_id=table$branch_value->id.id and `$db_dynamic`.student_fees_deposite.fee_groups_feetype_id=`$db_dynamic`.fee_groups_feetype.id WHERE `$db_dynamic`.student_session.session_id='" . $this->current_session . "' and  `$db_dynamic`.fee_session_groups.session_id='" . $this->current_session . "'";

        //=============================

            }
            $sql = $sql . implode(" UNION ALL ", $condition);
        } else {
            $sql = substr($sql, 0, -11);
        }
        $sql=($is_branch_available) ? "SELECT * FROM ($sql) as tempTable" :$sql;
        $query  = $this->db->query($sql);
        $result = $query->result();
        return $result;
    }

    /*
    This function is used to get student deposit fees 
    */
    public function getFeesDepositeByIdArray($id_array = array())
    {
        $default_db = $this->db_default->database;       
        $array_implode_value =array_key_exists($default_db, $id_array) ? implode("','", $id_array[$default_db]) :0;
        $id_implode = $imp = "'" . $array_implode_value . "'";
        $sql ="SELECT '$default_db' as db_name, '".$this->lang->line('home_branch')."' as branch_name, table0.*,`$default_db`.fee_session_groups.fee_groups_id,`$default_db`.fee_session_groups.session_id,`$default_db`.fee_groups.name,`$default_db`.fee_groups_feetype.amount as `fee_amount`,`$default_db`.fee_groups_feetype.id as fee_groups_feetype_id,`$default_db`.student_fees_deposite.id as `student_fees_deposite_id`,`$default_db`.student_fees_deposite.amount_detail,`$default_db`.students.admission_no , `$default_db`.students.roll_no,`$default_db`.students.admission_date,`$default_db`.students.firstname,`$default_db`.students.middlename,  `$default_db`.students.lastname,`$default_db`.students.father_name,`$default_db`.students.image, `$default_db`.students.mobileno, `$default_db`.students.email ,`$default_db`.students.state ,   `$default_db`.students.city , `$default_db`.students.pincode ,`$default_db`.classes.class,`$default_db`.sections.section FROM `$default_db`.`student_fees_master` table0  INNER JOIN `$default_db`.fee_session_groups on `$default_db`.fee_session_groups.id=table0.fee_session_group_id INNER JOIN `$default_db`.student_session on `$default_db`.student_session.id=table0.student_session_id INNER JOIN `$default_db`.students on `$default_db`.students.id=`$default_db`.student_session.student_id inner join `$default_db`.classes on `$default_db`.student_session.class_id=`$default_db`.classes.id INNER JOIN `$default_db`.sections on `$default_db`.sections.id=`$default_db`.student_session.section_id inner join `$default_db`.fee_groups on `$default_db`.fee_groups.id=`$default_db`.fee_session_groups.fee_groups_id INNER JOIN `$default_db`.fee_groups_feetype on `$default_db`.fee_groups.id=`$default_db`.fee_groups_feetype.fee_groups_id  JOIN `$default_db`.student_fees_deposite on `$default_db`.student_fees_deposite.student_fees_master_id=table0.id and `$default_db`.student_fees_deposite.fee_groups_feetype_id=`$default_db`.fee_groups_feetype.id WHERE `$default_db`.student_session.session_id='" . $this->current_session . "' and  `$default_db`.fee_session_groups.session_id='" . $this->current_session . "' and `$default_db`.student_fees_deposite.id in (" . $id_implode . ")  UNION ALL ";     

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
                $array_dy_namic_implode_value =array_key_exists($db_dynamic, $id_array) ? implode("','", $id_array[$db_dynamic]) :0;
                $id_implode_dynamic = $imp = "'" . $array_dy_namic_implode_value . "'";
                $condition[]  = "SELECT '$db_dynamic' as db_name, '$branch_value->branch_name' as branch_name, table$branch_value->id.*,`$db_dynamic`.fee_session_groups.fee_groups_id,`$db_dynamic`.fee_session_groups.session_id,`$db_dynamic`.fee_groups.name,`$db_dynamic`.fee_groups_feetype.amount as `fee_amount`,`$db_dynamic`.fee_groups_feetype.id as fee_groups_feetype_id,`$db_dynamic`.student_fees_deposite.id as `student_fees_deposite_id`,`$db_dynamic`.student_fees_deposite.amount_detail,`$db_dynamic`.students.admission_no , `$db_dynamic`.students.roll_no,`$db_dynamic`.students.admission_date,`$db_dynamic`.students.firstname,`$db_dynamic`.students.middlename,  `$db_dynamic`.students.lastname,`$db_dynamic`.students.father_name,`$db_dynamic`.students.image, `$db_dynamic`.students.mobileno, `$db_dynamic`.students.email ,`$db_dynamic`.students.state ,   `$db_dynamic`.students.city , `$db_dynamic`.students.pincode ,`$db_dynamic`.classes.class,`$db_dynamic`.sections.section FROM `$db_dynamic`.`student_fees_master` table$branch_value->id  INNER JOIN `$db_dynamic`.fee_session_groups on `$db_dynamic`.fee_session_groups.id=table$branch_value->id.fee_session_group_id INNER JOIN `$db_dynamic`.student_session on `$db_dynamic`.student_session.id=table$branch_value->id.student_session_id INNER JOIN `$db_dynamic`.students on `$db_dynamic`.students.id=`$db_dynamic`.student_session.student_id inner join `$db_dynamic`.classes on `$db_dynamic`.student_session.class_id=`$db_dynamic`.classes.id INNER JOIN `$db_dynamic`.sections on `$db_dynamic`.sections.id=`$db_dynamic`.student_session.section_id inner join `$db_dynamic`.fee_groups on `$db_dynamic`.fee_groups.id=`$db_dynamic`.fee_session_groups.fee_groups_id INNER JOIN `$db_dynamic`.fee_groups_feetype on `$db_dynamic`.fee_groups.id=`$db_dynamic`.fee_groups_feetype.fee_groups_id  JOIN `$db_dynamic`.student_fees_deposite on `$db_dynamic`.student_fees_deposite.student_fees_master_id=table$branch_value->id.id and `$db_dynamic`.student_fees_deposite.fee_groups_feetype_id=`$db_dynamic`.fee_groups_feetype.id WHERE `$db_dynamic`.student_session.session_id='" . $this->current_session . "' and  `$db_dynamic`.fee_session_groups.session_id='" . $this->current_session . "' and `$db_dynamic`.student_fees_deposite.id in (" . $id_implode_dynamic . ")";

        //=============================

            }
            $sql = $sql . implode(" UNION ALL ", $condition);
        } else {
            $sql = substr($sql, 0, -11);
        }
        $sql=($is_branch_available) ? "SELECT * FROM ($sql) as tempTable" :$sql;
        $query  = $this->db->query($sql);
        $result = $query->result();
        return $result;

    } 
}