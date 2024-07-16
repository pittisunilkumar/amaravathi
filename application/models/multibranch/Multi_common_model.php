<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Multi_common_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db_default = $this->load->database('default', true);
    }

    /*
    This function is used to get student
    */
    public function getStudentCount($school_array = [])
    {
        $results = [];
        //===================

        $default_db = $this->db_default->database;
        $current_db = $school_array[$default_db];
        $school         = [];
        $school['name'] = $current_db->name;
        $this->db_default->join('student_session', 'student_session.student_id = students.id');
        $this->db_default->join('classes', 'student_session.class_id = classes.id');
        $this->db_default->join('sections', 'sections.id = student_session.section_id');
        $this->db_default->join('categories', 'students.category_id = categories.id', 'left');
        $this->db_default->join('users', 'users.user_id = students.id', 'left');
        $this->db_default->where('student_session.session_id', $current_db->session_id);
        $this->db_default->where('users.role', 'student');
        $this->db_default->where('students.is_active', 'yes');
        $school['total_student'] = $this->db_default->count_all_results('students');
        $school['db_name']       = $default_db;
        $school['session']       = $current_db->session;
        //====================

        $results[$default_db] = $school;

        $condition = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {

                $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);

                //===================
                $db_dynamic_name = $db_dynamic->database;

                $current_db     = $school_array[$db_dynamic_name];
                $school         = [];
                $school['name'] = $current_db->name;

                $db_dynamic->join('student_session', 'student_session.student_id = students.id');
                $db_dynamic->join('classes', 'student_session.class_id = classes.id');
                $db_dynamic->join('sections', 'sections.id = student_session.section_id');
                $db_dynamic->join('categories', 'students.category_id = categories.id', 'left');
                $db_dynamic->join('users', 'users.user_id = students.id', 'left');
                $db_dynamic->where('student_session.session_id', $current_db->session_id);
                $db_dynamic->where('users.role', 'student');
                $db_dynamic->where('students.is_active', 'yes');
                $school['total_student']   = $db_dynamic->count_all_results('students');
                $school['db_name']         = $db_dynamic_name;
                $school['session']         = $current_db->session;
                $results[$db_dynamic_name] = $school;
                //====================

            }

        }
        //=========================================
        return $results;
    }

    /*
    This function is used to get student fees based on active current session
    */
    public function getCurrentSessionStudentFees($school_array = [])
    {
        $results = [];
        //===================

        $default_db = $this->db_default->database;

        $current_db = $school_array[$default_db];

        $school = [];

        $sql = "SELECT table0.*,$default_db.fee_session_groups.fee_groups_id,$default_db.fee_session_groups.session_id,$default_db.fee_groups.name,$default_db.fee_groups.is_system,$default_db.fee_groups_feetype.amount as `fee_amount`,$default_db.fee_groups_feetype.id as fee_groups_feetype_id,$default_db.student_fees_deposite.id as `student_fees_deposite_id`,$default_db.student_fees_deposite.amount_detail,$default_db.students.id as student_id,$default_db.classes.class,$default_db.sections.section FROM $default_db.`student_fees_master` table0 INNER JOIN $default_db.fee_session_groups on $default_db.fee_session_groups.id=table0.fee_session_group_id INNER JOIN $default_db.student_session on student_session.id=table0.student_session_id INNER JOIN $default_db.students on $default_db.students.id=student_session.student_id inner join $default_db.classes on student_session.class_id=$default_db.classes.id INNER JOIN $default_db.sections on $default_db.sections.id=student_session.section_id inner join $default_db.fee_groups on $default_db.fee_groups.id=$default_db.fee_session_groups.fee_groups_id INNER JOIN $default_db.fee_groups_feetype on $default_db.fee_session_groups.id=$default_db.fee_groups_feetype.fee_session_group_id LEFT JOIN $default_db.student_fees_deposite on $default_db.student_fees_deposite.student_fees_master_id=table0.id and $default_db.student_fees_deposite.fee_groups_feetype_id=$default_db.fee_groups_feetype.id WHERE $default_db.student_session.session_id='" . $current_db->session_id . "' and  $default_db.fee_session_groups.session_id='" . $current_db->session_id . "'";
        
        $query  = $this->db->query($sql);
        $result = $query->result();

        //====================

        $results[$default_db] = $result;

        $condition = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {

                $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);

                //===================
                $db_dynamic_name = $db_dynamic->database;

                $current_db = $school_array[$db_dynamic_name];
                $school     = [];

                $sql = "SELECT table$branch_value->id.*,$db_dynamic_name.fee_session_groups.fee_groups_id,$db_dynamic_name.fee_session_groups.session_id,$db_dynamic_name.fee_groups.name,$db_dynamic_name.fee_groups.is_system,$db_dynamic_name.fee_groups_feetype.amount as `fee_amount`,$db_dynamic_name.fee_groups_feetype.id as fee_groups_feetype_id,$db_dynamic_name.student_fees_deposite.id as `student_fees_deposite_id`,$db_dynamic_name.student_fees_deposite.amount_detail,$db_dynamic_name.students.id as student_id,$db_dynamic_name.classes.class,$db_dynamic_name.sections.section FROM $db_dynamic_name.`student_fees_master` table$branch_value->id INNER JOIN $db_dynamic_name.fee_session_groups on $db_dynamic_name.fee_session_groups.id=table$branch_value->id.fee_session_group_id INNER JOIN $db_dynamic_name.student_session on student_session.id=table$branch_value->id.student_session_id INNER JOIN $db_dynamic_name.students on $db_dynamic_name.students.id=student_session.student_id inner join $db_dynamic_name.classes on student_session.class_id=$db_dynamic_name.classes.id INNER JOIN $db_dynamic_name.sections on $db_dynamic_name.sections.id=student_session.section_id inner join $db_dynamic_name.fee_groups on $db_dynamic_name.fee_groups.id=$db_dynamic_name.fee_session_groups.fee_groups_id INNER JOIN $db_dynamic_name.fee_groups_feetype on $db_dynamic_name.fee_session_groups.id=$db_dynamic_name.fee_groups_feetype.fee_session_group_id LEFT JOIN $db_dynamic_name.student_fees_deposite on $db_dynamic_name.student_fees_deposite.student_fees_master_id=table$branch_value->id.id and $db_dynamic_name.student_fees_deposite.fee_groups_feetype_id=$db_dynamic_name.fee_groups_feetype.id WHERE $db_dynamic_name.student_session.session_id='" . $current_db->session_id . "' and  $db_dynamic_name.fee_session_groups.session_id='" . $current_db->session_id . "'";

                $query  = $this->db->query($sql);
                $result = $query->result();

                $results[$db_dynamic_name] = $result;
                //====================

            }

        }
        //=========================================
        return $results;
    }

    /*
    This function is used to get staff list
    */
    public function getStaff($school_array = [])
    {
        $results = [];
        //===================

        $default_db = $this->db_default->database;
        $current_db = $school_array[$default_db];
        $school         = [];
        $school['name'] = $current_db->name;

        $this->db_default->join('staff_designation', "staff_designation.id = staff.designation", "left");
        $this->db_default->join('staff_roles', "staff_roles.staff_id = staff.id", "left");
        $this->db_default->join('roles', "roles.id = staff_roles.role_id", "left");
        $this->db_default->join('department', "department.id = staff.department", "left");
        $this->db_default->where('staff.is_active', 1);
        $school['total_staff'] = $this->db_default->count_all_results('staff');
        $school['db_name']     = $default_db;

        //====================

        $results[$default_db] = $school;

        $condition = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {

                $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);

                //===================
                $db_dynamic_name = $db_dynamic->database;

                $current_db     = $school_array[$db_dynamic_name];
                $school         = [];
                $school['name'] = $current_db->name;

                $db_dynamic->join('staff_designation', "staff_designation.id = staff.designation", "left");
                $db_dynamic->join('staff_roles', "staff_roles.staff_id = staff.id", "left");
                $db_dynamic->join('roles', "roles.id = staff_roles.role_id", "left");
                $db_dynamic->join('department', "department.id = staff.department", "left");
                $db_dynamic->where('staff.is_active', 1);

                $school['total_staff'] = $db_dynamic->count_all_results('staff');
                $school['db_name']     = $db_dynamic_name;

                $results[$db_dynamic_name] = $school;
                //====================
            }

        }
        //=========================================
        return $results;
    }

    /*
    This function is used to get staff attendance based on date
    */
    public function getStaffAttendance($school_array = [], $date)
    {
        $results = [];
        //===================

        $default_db = $this->db_default->database;
        $current_db = $school_array[$default_db];
        $school['name'] = $current_db->name;
        $sql            = "select $default_db.staff_attendance.staff_attendance_type_id,$default_db.staff_attendance_type.type as `att_type`,$default_db.staff_attendance_type.key_value as `key`,$default_db.staff_attendance.remark,table0.name,table0.surname,table0.employee_id,table0.contact_no,table0.email,$default_db.roles.name as user_type,IFNULL($default_db.staff_attendance.date, 'xxx') as date, IFNULL($default_db.staff_attendance.id, 0) as attendence_id, table0.id as id from $default_db.`staff` table0  left join $default_db.staff_roles on (table0.id = $default_db.staff_roles.staff_id) left join $default_db.roles on ($default_db.roles.id = $default_db.staff_roles.role_id) left join $default_db.staff_attendance on (table0.id = $default_db.staff_attendance.staff_id) and $default_db.staff_attendance.date = " . $this->db->escape($date) . " left join $default_db.staff_attendance_type on $default_db.staff_attendance_type.id = $default_db.staff_attendance.staff_attendance_type_id  where table0.is_active = 1 ";

        $query  = $this->db->query($sql);
        $result = $query->result();

        //====================

        $results[$default_db] = $result;

        $condition = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {

                $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);

                //===================
                $db_dynamic_name = $db_dynamic->database;

                $current_db = $school_array[$db_dynamic_name];

                $school['name'] = $current_db->name;
                $sql            = "select $db_dynamic_name.staff_attendance.staff_attendance_type_id,$db_dynamic_name.staff_attendance_type.type as `att_type`,$db_dynamic_name.staff_attendance_type.key_value as `key`,$db_dynamic_name.staff_attendance.remark,table$branch_value->id.name,table$branch_value->id.surname,table$branch_value->id.employee_id,table$branch_value->id.contact_no,table$branch_value->id.email,$db_dynamic_name.roles.name as user_type,IFNULL($db_dynamic_name.staff_attendance.date, 'xxx') as date, IFNULL($db_dynamic_name.staff_attendance.id, 0) as attendence_id, table$branch_value->id.id as id from $db_dynamic_name.`staff` table$branch_value->id  left join $db_dynamic_name.staff_roles on (table$branch_value->id.id = $db_dynamic_name.staff_roles.staff_id) left join $db_dynamic_name.roles on ($db_dynamic_name.roles.id = $db_dynamic_name.staff_roles.role_id) left join $db_dynamic_name.staff_attendance on (table$branch_value->id.id = $db_dynamic_name.staff_attendance.staff_id) and $db_dynamic_name.staff_attendance.date = " . $this->db->escape($date) . " left join $db_dynamic_name.staff_attendance_type on $db_dynamic_name.staff_attendance_type.id = $db_dynamic_name.staff_attendance.staff_attendance_type_id  where table$branch_value->id.is_active = 1 ";

                $query  = $this->db->query($sql);
                $result = $query->result();

                $results[$db_dynamic_name] = $result;
                //====================

            }

        }
        //=========================================
        return $results;
    }

    /*
    This function is used to get offline admitted student list
    */
    public function getOfflineStudentAdmissions($school_array = [])
    {
        $results = [];
        //===================

        $default_db = $this->db_default->database;
        $current_db = $school_array[$default_db];

        $school_arr         = sessionYearDetails($current_db->session, $current_db->start_month);
        $school_month_start = $school_arr['month_start'];
        $school_month_end   = $school_arr['month_end'];

        $school            = [];
        $school['name']    = $current_db->name;
        $school['session'] = $current_db->session;
        $this->db_default->join('student_session', 'student_session.student_id = students.id');
        $this->db_default->join('classes', 'student_session.class_id = classes.id');
        $this->db_default->join('sections', 'sections.id = student_session.section_id');
        $this->db_default->join('categories', 'students.category_id = categories.id', 'left');
        $this->db_default->join('users', 'users.user_id = students.id', 'left');
        $this->db_default->where('student_session.session_id', $current_db->session_id);
        $this->db_default->where('admission_date >=', $school_month_start);
        $this->db_default->where('admission_date <=', $school_month_end);
        $this->db_default->where('users.role', 'student');
        $school['offline_admission'] = $this->db_default->count_all_results('students');
        $school['db_name']         = $default_db;
        $results[$default_db] = $school;

        //====================

        $condition = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {

                $school     = [];
                $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);

                //===================
                $db_dynamic_name = $db_dynamic->database;
                $db_dynamic_array = $school_array[$db_dynamic_name];
                $school_arr         = sessionYearDetails($db_dynamic_array->session, $db_dynamic_array->start_month);
                $school_month_start = $school_arr['month_start'];
                $school_month_end   = $school_arr['month_end'];
                $school['name']    = $db_dynamic_array->name;
                $school['session'] = $db_dynamic_array->session;
                $db_dynamic->join('student_session', 'student_session.student_id = students.id');
                $db_dynamic->join('classes', 'student_session.class_id = classes.id');
                $db_dynamic->join('sections', 'sections.id = student_session.section_id');
                $db_dynamic->join('categories', 'students.category_id = categories.id', 'left');
                $db_dynamic->join('users', 'users.user_id = students.id', 'left');
                $db_dynamic->where('student_session.session_id', $db_dynamic_array->session_id);
                $db_dynamic->where('admission_date >=', $school_month_start);
                $db_dynamic->where('admission_date <=', $school_month_end);
                $db_dynamic->where('users.role', 'student');
                $school['offline_admission'] = $db_dynamic->count_all_results('students');
                $school['db_name']         = $db_dynamic_name;

                $results[$db_dynamic_name] = $school;

                //====================
            }

        }
        //=========================================
        return $results;
    }

    /*
    This function is used to get online admitted admission student list
    */
    public function getOnlineStudentAdmissions($school_array = [])
    {
        $results = [];
        //===================

        $default_db = $this->db_default->database;
        $current_db = $school_array[$default_db];
        $school_arr         = sessionYearDetails($current_db->session, $current_db->start_month);
        $school_month_start = $school_arr['month_start'];
        $school_month_end   = $school_arr['month_end'];
        $school            = [];
        $school['name']    = $current_db->name;
        $school['session'] = $current_db->session;     
        $this->db_default->join('class_sections', 'online_admissions.class_section_id = class_sections.id');
        $this->db_default->where('admission_date >=', $school_month_start);
        $this->db_default->where('admission_date <=', $school_month_end);
        $school['online_admission'] = $this->db_default->count_all_results('online_admissions');
        $school['db_name']         = $default_db;
        $results[$default_db] = $school;

        //====================

        $condition = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {

                $school     = [];
                $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);

                //===================
                $db_dynamic_name = $db_dynamic->database;
                $db_dynamic_array = $school_array[$db_dynamic_name];
                $school_arr         = sessionYearDetails($db_dynamic_array->session, $db_dynamic_array->start_month);
                $school_month_start = $school_arr['month_start'];
                $school_month_end   = $school_arr['month_end'];
                $school['name']    = $db_dynamic_array->name;
                $school['session'] = $db_dynamic_array->session;
                $db_dynamic->join('class_sections', 'online_admissions.class_section_id = class_sections.id');                $db_dynamic->where('admission_date >=', $school_month_start);
                $db_dynamic->where('admission_date <=', $school_month_end);
                $school['online_admission'] = $db_dynamic->count_all_results('online_admissions');
                $school['db_name']         = $db_dynamic_name;
                $results[$db_dynamic_name] = $school;
                //====================

            }
        }
        //=========================================
        return $results;
    }

    /*
    This function is used to get book
    */
    public function getBooks($school_array = [])
    {
        $results = [];
        //===================

        $default_db = $this->db_default->database;
        $current_db = $school_array[$default_db];

        $school            = [];
        $school['name']    = $current_db->name;
        $school['session'] = $current_db->session;     
        $school['total_books'] = $this->db_default->count_all_results('books');
        $school['db_name']         = $default_db;

        $results[$default_db] = $school;

        //====================

        $condition = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {

                $school     = [];
                $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);
  
                //===================
                $db_dynamic_name = $db_dynamic->database;
                $db_dynamic_array = $school_array[$db_dynamic_name];            

        $school            = [];
        $school['name']    = $db_dynamic_array->name;
        $school['session'] = $db_dynamic_array->session;     
        $school['total_books'] = $db_dynamic->count_all_results('books');
        $school['db_name']     = $db_dynamic_name;
        $results[$db_dynamic_name] = $school;
        //====================

            }
        }
        //=========================================
        return $results;
    }

    /*
    This function is used to get library members
    */
    public function getLibararyMembers($school_array = [])
    {
        $results = [];
        //===================
        $default_db = $this->db_default->database;
        $current_db = $school_array[$default_db];
        $school            = [];
        $school['name']    = $current_db->name;
        $school['session'] = $current_db->session;      
        $school['total_members'] = $this->db_default->count_all_results('libarary_members');
        $school['db_name']         = $default_db;

        $results[$default_db] = $school;
        //====================

        $condition = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {

                $school     = [];
                $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);  
                //===================
                $db_dynamic_name = $db_dynamic->database;
                $db_dynamic_array = $school_array[$db_dynamic_name];       

                $school            = [];
                $school['name']    = $db_dynamic_array->name;
                $school['session'] = $db_dynamic_array->session;    
                $school['total_members'] = $db_dynamic->count_all_results('libarary_members');
                $school['db_name']         = $db_dynamic_name;
                $results[$db_dynamic_name] = $school;
                //====================
            }
        }
        //=========================================
        return $results;
    }

    /*
    This function is used to get issue book
    */
    public function getLibararyBookIssued($school_array = [])
    {
        $results = [];
        //===================

        $default_db = $this->db_default->database;
        $current_db = $school_array[$default_db];
        $school            = [];
        $school['name']    = $current_db->name;
        $school['session'] = $current_db->session;
        $this->db_default->where('is_returned',0);     
        $school['total_book_issued'] = $this->db_default->count_all_results('book_issues');
        $school['db_name']         = $default_db;
        $results[$default_db] = $school;

        //====================

        $condition = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {

                $school     = [];
                $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);
  
                //===================
                $db_dynamic_name = $db_dynamic->database;
                $db_dynamic_array = $school_array[$db_dynamic_name]; 
                $school            = [];
                $school['name']    = $db_dynamic_array->name;
                $school['session'] = $db_dynamic_array->session;
                $db_dynamic->where('is_returned',0);     
                $school['total_book_issued'] = $db_dynamic->count_all_results('book_issues');
                $school['db_name']         = $db_dynamic_name;
                $results[$db_dynamic_name] = $school;
                //====================
            }
        }
        //=========================================
        return $results;
    }

    /*
    This function is used to get alumni student
    */
    public function getAlumniStudents($school_array = [])
    {
        $results = [];
        //===================

        $default_db = $this->db_default->database;
        $current_db = $school_array[$default_db];
        $school            = [];
        $school['name']    = $current_db->name;
        $school['session'] = $current_db->session;    
        $school['total_alumni_student'] = $this->db_default->count_all_results('alumni_students');
        $school['db_name']         = $default_db;
        $results[$default_db] = $school;
        
        //====================

        $condition = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {

                $school     = [];
                $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);
  
                //===================
                $db_dynamic_name = $db_dynamic->database;
                $db_dynamic_array = $school_array[$db_dynamic_name];
                $school            = [];
                $school['name']    = $db_dynamic_array->name;
                $school['session'] = $db_dynamic_array->session;    
                $school['total_alumni_student'] = $db_dynamic->count_all_results('alumni_students');
                $school['db_name']         = $db_dynamic_name;
                $results[$db_dynamic_name] = $school;
                //====================
            }
        }
        //=========================================
        return $results;
    }

    /*
    This function is used to get user log detail
    */
    public function getUserLog($school_array = [])
    {
        $results = [];
        //===================

        $default_db = $this->db_default->database;
        $current_db = $school_array[$default_db];
        $school            = [];
        $school['name']    = $current_db->name;
        $school['session'] = $current_db->session;    
        $school['total_userlog'] = $this->db_default->count_all_results('userlog');
        $school['db_name']         = $default_db;
        $results[$default_db] = $school;
        //====================

        $condition = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {

                $school     = [];
                $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);  
                //===================
                $db_dynamic_name = $db_dynamic->database;
                $db_dynamic_array = $school_array[$db_dynamic_name];
                $school            = [];
                $school['name']    = $db_dynamic_array->name;
                $school['session'] = $db_dynamic_array->session;    
                $school['total_userlog'] = $db_dynamic->count_all_results('userlog');
                $school['db_name']         = $db_dynamic_name;
                $results[$db_dynamic_name] = $school;
                //====================
            }
        }
        //=========================================
        return $results;
    }

    /*
    This function is used to get student transport fees
    */
    public function getStudentTransportFees($school_array = [])
    {
        $results = [];
        //===================

        $default_db = $this->db_default->database;
        $current_db = $school_array[$default_db];
        $school            = [];
        $school['name']    = $current_db->name;
        $school['session'] = $current_db->session;
         $this->db_default->select('student_transport_fees.*,route_pickup_point.fees,transport_feemaster.month,transport_feemaster.due_date ,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id, IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail`,students.id as `student_id`');
        $this->db_default->from('student_transport_fees');
        $this->db_default->join('transport_feemaster' ,'transport_feemaster.id =student_transport_fees.transport_feemaster_id');   
        $this->db_default->join('student_fees_deposite' ,'student_fees_deposite.student_transport_fee_id=student_transport_fees.id','LEFT');
        $this->db_default->join('student_session' ,'student_session.id= student_transport_fees.student_session_id'); 
        $this->db_default->join('classes' ,'classes.id= student_session.class_id');  
        $this->db_default->join('sections' ,'sections.id= student_session.section_id');  
        $this->db_default->join('students' ,'students.id=student_session.student_id');  
        $this->db_default->join('route_pickup_point' ,'route_pickup_point.id = student_transport_fees.route_pickup_point_id'); 
        $this->db_default->join('categories' ,'students.category_id = categories.id','LEFT');
        $q =$this->db_default->get();
        $total_fees=$q->result();     
        $school['total_fees_record'] = $total_fees;
        $school['db_name']         = $default_db;
        $results[$default_db] = $school;
        
        //====================

        $condition = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
                $is_branch_available = true;
                foreach ($branches as $branch_key => $branch_value) {

                        $school     = [];
                        $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);
  
                        //===================
                        $db_dynamic_name = $db_dynamic->database;

                        $db_dynamic_array = $school_array[$db_dynamic_name];
                        $school            = [];
                        $school['name']    = $db_dynamic_array->name;
                        $school['session'] = $db_dynamic_array->session;

                        $db_dynamic->select('student_transport_fees.*,route_pickup_point.fees,transport_feemaster.month,transport_feemaster.due_date ,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id, IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail`,students.id as `student_id`');
                        $db_dynamic->from('student_transport_fees');
                        $db_dynamic->join('transport_feemaster' ,'transport_feemaster.id =student_transport_fees.transport_feemaster_id');   
                        $db_dynamic->join('student_fees_deposite' ,'student_fees_deposite.student_transport_fee_id=student_transport_fees.id','LEFT');
                        $db_dynamic->join('student_session' ,'student_session.id= student_transport_fees.student_session_id'); 
                        $db_dynamic->join('classes' ,'classes.id= student_session.class_id');  
                        $db_dynamic->join('sections' ,'sections.id= student_session.section_id');  
                        $db_dynamic->join('students' ,'students.id=student_session.student_id');  
                        $db_dynamic->join('route_pickup_point' ,'route_pickup_point.id = student_transport_fees.route_pickup_point_id');  
                        $db_dynamic->join('categories' ,'students.category_id = categories.id','LEFT');
                        $q=$db_dynamic->get();
                        $total_fees=$q->result();     
                        $school['total_fees_record'] = $total_fees;
                        $school['db_name']         = $db_dynamic_name;
                        $results[$db_dynamic_name] = $school;
                        //====================
                }
        }
        //=========================================
        return $results;
    }

    /*
    This function is used to get payrol of staff from all branch based of month and year
    */
    public function getStaffPayslipCount($school_array = [],$month,$year)
    {
        $results = [];
        //===================

        $default_db = $this->db_default->database;
        $current_db = $school_array[$default_db];
        $school            = [];
        $school['name']    = $current_db->name;
        $school['session'] = $current_db->session;
        $this->db_default->select('staff_payslip.*,');
        $this->db_default->from('staff_payslip');
        $this->db_default->join('staff' ,'staff.id =staff_payslip.staff_id');   
        $this->db_default->where('staff_payslip.month' ,$month);   
        $this->db_default->where('staff_payslip.year' ,$year);        
        $q =$this->db_default->get();
        $total_fees=$q->result();     
        $school['total_payroll_record'] = $total_fees;
        $school['db_name']         = $default_db;
        $results[$default_db] = $school;
        //====================

        $condition = array();
        $this->load->model("multibranch_model");
        //=============================
        $branches            = $this->multibranch_model->get();
        $is_branch_available = false;
        if (!empty($branches)) {
            $is_branch_available = true;
            foreach ($branches as $branch_key => $branch_value) {

                $school     = [];
                $db_dynamic = $this->load->database('branch_' . $branch_value->id, true);
  
                //===================
                $db_dynamic_name = $db_dynamic->database;
                $db_dynamic_array = $school_array[$db_dynamic_name];
                $school            = [];
                $school['name']    = $db_dynamic_array->name;
                $school['session'] = $db_dynamic_array->session;
                $db_dynamic->select('staff_payslip.*,');
                $db_dynamic->from('staff_payslip');
                $db_dynamic->join('staff' ,'staff.id =staff_payslip.staff_id');   
                $db_dynamic->where('staff_payslip.month' ,$month);  
                $db_dynamic->where('staff_payslip.year' ,$year);  
                $q=$db_dynamic->get();
                $total_fees=$q->result();     
                $school['total_payroll_record'] = $total_fees;
                $school['db_name']         = $db_dynamic_name;
                $results[$db_dynamic_name] = $school;
                //====================
            }
        }
        //=========================================
        return $results;
    }
}