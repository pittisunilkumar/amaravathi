<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Testt extends Admin_Controller
{

    protected $current_session;

    public function __construct()
    {
        parent::__construct();


        
        $this->config->load('app-config');
        $this->config->load("payroll");
        
        $this->load->model("classteacher_model");
        
        $this->load->model("student_model");
        $this->load->model(array("timeline_model", "student_edit_field_model", 'transportfee_model', 'marksdivision_model', 'module_model'));
        $this->blood_group        = $this->config->item('bloodgroup');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->role;
        $this->staff_attendance = $this->config->item('staffattendance');
        
        $this->load->library('smsgateway');
        $this->load->library('mailsmsconf');
        $this->load->library('customlib');
        $this->load->library('encoding_lib');
        $this->load->library('media_storage');
        $this->load->model("student_model");
        $this->load->model('test_model');
        $this->load->model('setting_model');
        

        // $this->current_session = $this->setting_model->getCurrentSession();
        // $this->current_date    = $this->setting_model->getDateYmd();
        
        
    }

    // public function import()
    // {
    //     if (!$this->rbac->hasPrivilege('import_student', 'can_view')) {
    //         access_denied();
    //     }
    //     $data['title']      = $this->lang->line('import_student');
    //     $data['title_list'] = $this->lang->line('recently_added_student');
    //     $session            = $this->setting_model->getCurrentSession();
    //     $class              = $this->class_model->get('', $classteacher = 'yes');
    //     $data['classlist']  = $class;
    //     $userdata           = $this->customlib->getUserData();

    //     $category = $this->category_model->get();

    //     $fields = array('admission_no', 'roll_no', 'firstname', 'middlename', 'lastname', 'gender', 'dob', 'category_id', 'religion', 'cast', 'mobileno', 'email', 'admission_date', 'blood_group', 'school_house_id', 'height', 'weight', 'measurement_date', 'father_name', 'father_phone', 'father_occupation', 'mother_name', 'mother_phone', 'mother_occupation', 'guardian_is', 'guardian_name', 'guardian_relation', 'guardian_email', 'guardian_phone', 'guardian_occupation', 'guardian_address', 'current_address', 'permanent_address', 'bank_account_no', 'bank_name', 'ifsc_code', 'adhar_no', 'samagra_id', 'rte', 'previous_school', 'note');

    //     $data["fields"]       = $fields;
    //     $data['categorylist'] = $category;
    //     $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_csv_upload');
    //     if ($this->form_validation->run() == false) {
    //         $this->load->view('layout/header', $data);
    //         $this->load->view('admin/test/import', $data);
    //         $this->load->view('layout/footer', $data);
    //     } else {

    //         $student_categorize = 'class';
    //         if ($student_categorize == 'class') {
    //             $section = 0;
    //         } else if ($student_categorize == 'section') {

    //             $section = $this->input->post('section_id');
    //         }
    //         $class_id   = $this->input->post('class_id');
    //         $section_id = $this->input->post('section_id');

    //         $session = $this->setting_model->getCurrentSession();
    //         if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
    //             $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    //             if ($ext == 'csv') {
    //                 $file = $_FILES['file']['tmp_name'];
    //                 $this->load->library('CSVReader');
    //                 $result = $this->csvreader->parse_file($file);

    //                 if (!empty($result)) {
    //                     $rowcount = 0;
    //                     for ($i = 1; $i <= count($result); $i++) {

    //                         $student_data[$i] = array();
    //                         $n                = 0;
    //                         foreach ($result[$i] as $key => $value) {

    //                             $student_data[$i][$fields[$n]] = $this->encoding_lib->toUTF8($result[$i][$key]);

    //                             $student_data[$i]['is_active'] = 'yes';

    //                             if (date('Y-m-d', strtotime($result[$i]['date_of_birth'])) === $result[$i]['date_of_birth']) {
    //                                 $student_data[$i]['dob'] = date('Y-m-d', strtotime($result[$i]['date_of_birth']));
    //                             } else {
    //                                 $student_data[$i]['dob'] = null;
    //                             }

    //                             if (date('Y-m-d', strtotime($result[$i]['measurement_date'])) === $result[$i]['measurement_date']) {
    //                                 $student_data[$i]['measurement_date'] = date('Y-m-d', strtotime($result[$i]['measurement_date']));
    //                             } else {
    //                                 $student_data[$i]['measurement_date'] = '';
    //                             }

    //                             if (date('Y-m-d', strtotime($result[$i]['admission_date'])) === $result[$i]['admission_date']) {
    //                                 $student_data[$i]['admission_date'] = date('Y-m-d', strtotime($result[$i]['admission_date']));
    //                             } else {
    //                                 $student_data[$i]['admission_date'] = null;
    //                             }
    //                             $n++;
    //                         }

    //                         $roll_no                           = $student_data[$i]["roll_no"];
    //                         $adm_no                            = $student_data[$i]["admission_no"];
    //                         $mobile_no                         = $student_data[$i]["mobileno"];
    //                         $email                             = $student_data[$i]["email"];
    //                         $guardian_phone                    = $student_data[$i]["guardian_phone"];
    //                         $guardian_email                    = $student_data[$i]["guardian_email"];
    //                         $data_setting                      = array();
    //                         $data_setting['id']                = $this->sch_setting_detail->id;
    //                         $data_setting['adm_auto_insert']   = $this->sch_setting_detail->adm_auto_insert;
    //                         $data_setting['adm_update_status'] = $this->sch_setting_detail->adm_update_status;
    //                         //-------------------------

    //                         if ($this->sch_setting_detail->adm_auto_insert) {
    //                             if ($this->sch_setting_detail->adm_update_status) {
    //                                 $last_student                     = $this->student_model->lastRecord();
    //                                 $last_admission_digit             = str_replace($this->sch_setting_detail->adm_prefix, "", $last_student->admission_no);
    //                                 $admission_no                     = $this->sch_setting_detail->adm_prefix . sprintf("%0" . $this->sch_setting_detail->adm_no_digit . "d", $last_admission_digit + 1);
    //                                 $student_data[$i]["admission_no"] = $admission_no;
    //                             } else {
    //                                 $admission_no                     = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;
    //                                 $student_data[$i]["admission_no"] = $admission_no;
    //                             }

    //                             $admission_no_exists = $this->student_model->check_adm_exists($admission_no);
    //                             if ($admission_no_exists) {
    //                                 $insert = "";
    //                             } else {
    //                                 $insert_id = $this->student_model->add($student_data[$i], $data_setting);
    //                             }
    //                         } else {

    //                             if ($this->form_validation->is_unique($adm_no, 'students.admission_no')) {
    //                                 $insert_id = $this->student_model->add($student_data[$i], $data_setting);
    //                             } else {
    //                                 $insert_id = "";
    //                             }
    //                         }

    //                         //-------------------------
    //                         if (!empty($insert_id)) {
    //                             $data_new = array(
    //                                 'student_id' => $insert_id,
    //                                 'class_id'   => $class_id,
    //                                 'section_id' => $section_id,
    //                                 'session_id' => $session,
    //                             );

    //                             $this->student_model->add_student_session($data_new);
    //                             $user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
    //                             $sibling_id    = $this->input->post('sibling_id');

    //                             $data_student_login = array(
    //                                 'username' => $this->student_login_prefix . $insert_id,
    //                                 'password' => $user_password,
    //                                 'user_id'  => $insert_id,
    //                                 'role'     => 'student',
    //                             );

    //                             $this->user_model->add($data_student_login);
    //                             $parent_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);

    //                             $temp              = $insert_id;
    //                             $data_parent_login = array(
    //                                 'username' => $this->parent_login_prefix . $insert_id,
    //                                 'password' => $parent_password,
    //                                 'user_id'  => $insert_id,
    //                                 'role'     => 'parent',
    //                                 'childs'   => $temp,
    //                             );

    //                             $ins_id         = $this->user_model->add($data_parent_login);
    //                             $update_student = array(
    //                                 'id'        => $insert_id,
    //                                 'parent_id' => $ins_id,
    //                             );

    //                             $this->student_model->add($update_student);
    //                             $sender_details = array('student_id' => $insert_id, 'contact_no' => $guardian_phone, 'email' => $guardian_email);
    //                             $this->mailsmsconf->mailsms('student_admission', $sender_details);

    //                             $student_login_detail = array('id' => $insert_id, 'credential_for' => 'student', 'username' => $this->student_login_prefix . $insert_id, 'password' => $user_password, 'contact_no' => $mobile_no, 'email' => $email, 'admission_no' => $admission_no);
    //                             $this->mailsmsconf->mailsms('student_login_credential', $student_login_detail);

    //                             $parent_login_detail = array('id' => $insert_id, 'credential_for' => 'parent', 'username' => $this->parent_login_prefix . $insert_id, 'password' => $parent_password, 'contact_no' => $guardian_phone, 'email' => $guardian_email, 'admission_no' => $admission_no);

    //                             $this->mailsmsconf->mailsms('student_login_credential', $parent_login_detail);

    //                             $data['csvData'] = $result;
    //                             $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('students_imported_successfully') . '</div>');

    //                             $rowcount++;
    //                             $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('total') . ' ' . count($result) . $this->lang->line('records_found_in_csv_file_total') . $rowcount . ' ' . $this->lang->line('records_imported_successfully') . '</div>');

    //                         } else {

    //                             $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exist') . '</div>');
    //                         }
    //                     }
    //                 } else {

    //                     $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
    //                 }
    //             } else {

    //                 $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
    //             }
    //         }

    //         redirect('admin/testt/import');
    //     }
    // }

    // public function import()
    // {
    //     if (!$this->rbac->hasPrivilege('import_student', 'can_view')) {
    //         access_denied();
    //     }
    //     $data['title']      = $this->lang->line('import_student');
    //     $data['title_list'] = $this->lang->line('recently_added_student');
    //     $session            = $this->setting_model->getCurrentSession();
    //     $class              = $this->class_model->get('', $classteacher = 'yes');
    //     $data['classlist']  = $class;
    //     $userdata           = $this->customlib->getUserData();

    //     $category = $this->category_model->get();

    //     $fields = array('admission_no', 'roll_no', 'firstname', 'middlename', 'lastname', 'gender', 'dob', 'category_id', 'religion', 'cast', 'mobileno', 'email', 'admission_date', 'blood_group', 'school_house_id', 'height', 'weight', 'measurement_date', 'father_name', 'father_phone', 'father_occupation', 'mother_name', 'mother_phone', 'mother_occupation', 'guardian_is', 'guardian_name', 'guardian_relation', 'guardian_email', 'guardian_phone', 'guardian_occupation', 'guardian_address', 'current_address', 'permanent_address', 'bank_account_no', 'bank_name', 'ifsc_code', 'adhar_no', 'samagra_id', 'rte', 'previous_school', 'note');

    //     $data["fields"]       = $fields;
    //     $data['categorylist'] = $category;
    //     $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_csv_upload');
    //     if ($this->form_validation->run() == false) {
    //         $this->load->view('layout/header', $data);
    //         $this->load->view('admin/test/import', $data);
    //         $this->load->view('layout/footer', $data);
    //     } else {

    //         $student_categorize = 'class';
    //         if ($student_categorize == 'class') {
    //             $section = 0;
    //         } else if ($student_categorize == 'section') {

    //             $section = $this->input->post('section_id');
    //         }
    //         $class_id   = $this->input->post('class_id');
    //         $section_id = $this->input->post('section_id');

    //         $session = $this->setting_model->getCurrentSession();
    //         if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
    //             $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    //             if ($ext == 'csv') {
    //                 $file = $_FILES['file']['tmp_name'];
    //                 $this->load->library('CSVReader');
    //                 $result = $this->csvreader->parse_file($file);

    //                 if (!empty($result)) {
    //                     $rowcount = 0;
    //                     for ($i = 1; $i <= count($result); $i++) {

    //                         $student_data[$i] = array();
    //                         $n                = 0;
    //                         foreach ($result[$i] as $key => $value) {

    //                             $student_data[$i][$fields[$n]] = $this->encoding_lib->toUTF8($result[$i][$key]);

    //                             $student_data[$i]['is_active'] = 'yes';

    //                             if (date('Y-m-d', strtotime($result[$i]['date_of_birth'])) === $result[$i]['date_of_birth']) {
    //                                 $student_data[$i]['dob'] = date('Y-m-d', strtotime($result[$i]['date_of_birth']));
    //                             } else {
    //                                 $student_data[$i]['dob'] = null;
    //                             }

    //                             if (date('Y-m-d', strtotime($result[$i]['measurement_date'])) === $result[$i]['measurement_date']) {
    //                                 $student_data[$i]['measurement_date'] = date('Y-m-d', strtotime($result[$i]['measurement_date']));
    //                             } else {
    //                                 $student_data[$i]['measurement_date'] = '';
    //                             }

    //                             if (date('Y-m-d', strtotime($result[$i]['admission_date'])) === $result[$i]['admission_date']) {
    //                                 $student_data[$i]['admission_date'] = date('Y-m-d', strtotime($result[$i]['admission_date']));
    //                             } else {
    //                                 $student_data[$i]['admission_date'] = null;
    //                             }
    //                             $n++;
    //                         }

    //                         $roll_no                           = $student_data[$i]["roll_no"];
    //                         $adm_no                            = $student_data[$i]["admission_no"];
    //                         $mobile_no                         = $student_data[$i]["mobileno"];
    //                         $email                             = $student_data[$i]["email"];
    //                         $guardian_phone                    = $student_data[$i]["guardian_phone"];
    //                         $guardian_email                    = $student_data[$i]["guardian_email"];
    //                         $data_setting                      = array();
    //                         $data_setting['id']                = $this->sch_setting_detail->id;
    //                         $data_setting['adm_auto_insert']   = $this->sch_setting_detail->adm_auto_insert;
    //                         $data_setting['adm_update_status'] = $this->sch_setting_detail->adm_update_status;
    //                         //-------------------------

    //                         if ($this->sch_setting_detail->adm_auto_insert) {
    //                             if ($this->sch_setting_detail->adm_update_status) {
    //                                 $last_student                     = $this->student_model->lastRecord();
    //                                 $last_admission_digit             = str_replace($this->sch_setting_detail->adm_prefix, "", $last_student->admission_no);
    //                                 $admission_no                     = $this->sch_setting_detail->adm_prefix . sprintf("%0" . $this->sch_setting_detail->adm_no_digit . "d", $last_admission_digit + 1);
    //                                 $student_data[$i]["admission_no"] = $admission_no;
    //                             } else {
    //                                 $admission_no                     = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;
    //                                 $student_data[$i]["admission_no"] = $admission_no;
    //                             }

    //                             $admission_no_exists = $this->student_model->check_adm_exists($admission_no);
    //                             if ($admission_no_exists) {
    //                                 $insert = "";
    //                             } else {
    //                                 $insert_id = $this->student_model->add($student_data[$i], $data_setting);
    //                             }
    //                         } else {

    //                             if ($this->form_validation->is_unique($adm_no, 'students.admission_no')) {
    //                                 $insert_id = $this->student_model->add($student_data[$i], $data_setting);
    //                             } else {
    //                                 $insert_id = "";
    //                             }
    //                         }

    //                         //-------------------------
    //                         if (!empty($insert_id)) {
    //                             $data_new = array(
    //                                 'student_id' => $insert_id,
    //                                 'class_id'   => $class_id,
    //                                 'section_id' => $section_id,
    //                                 'session_id' => $session,
    //                             );

    //                             $this->student_model->add_student_session($data_new);
    //                             $user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
    //                             $sibling_id    = $this->input->post('sibling_id');

    //                             $data_student_login = array(
    //                                 'username' => $this->student_login_prefix . $insert_id,
    //                                 'password' => $user_password,
    //                                 'user_id'  => $insert_id,
    //                                 'role'     => 'student',
    //                             );

    //                             $this->user_model->add($data_student_login);
    //                             $parent_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);

    //                             $temp              = $insert_id;
    //                             $data_parent_login = array(
    //                                 'username' => $this->parent_login_prefix . $insert_id,
    //                                 'password' => $parent_password,
    //                                 'user_id'  => $insert_id,
    //                                 'role'     => 'parent',
    //                                 'childs'   => $temp,
    //                             );

    //                             $ins_id         = $this->user_model->add($data_parent_login);
    //                             $update_student = array(
    //                                 'id'        => $insert_id,
    //                                 'parent_id' => $ins_id,
    //                             );

    //                             $this->student_model->add($update_student);
    //                             $sender_details = array('student_id' => $insert_id, 'contact_no' => $guardian_phone, 'email' => $guardian_email);
    //                             $this->mailsmsconf->mailsms('student_admission', $sender_details);

    //                             $student_login_detail = array('id' => $insert_id, 'credential_for' => 'student', 'username' => $this->student_login_prefix . $insert_id, 'password' => $user_password, 'contact_no' => $mobile_no, 'email' => $email, 'admission_no' => $admission_no);
    //                             $this->mailsmsconf->mailsms('student_login_credential', $student_login_detail);

    //                             $parent_login_detail = array('id' => $insert_id, 'credential_for' => 'parent', 'username' => $this->parent_login_prefix . $insert_id, 'password' => $parent_password, 'contact_no' => $guardian_phone, 'email' => $guardian_email, 'admission_no' => $admission_no);

    //                             $this->mailsmsconf->mailsms('student_login_credential', $parent_login_detail);

    //                             $data['csvData'] = $result;
    //                             $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('students_imported_successfully') . '</div>');

    //                             $rowcount++;
    //                             $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('total') . ' ' . count($result) . $this->lang->line('records_found_in_csv_file_total') . $rowcount . ' ' . $this->lang->line('records_imported_successfully') . '</div>');

    //                         } else {

    //                             $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exist') . '</div>');
    //                         }
    //                     }
    //                 } else {

    //                     $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
    //                 }
    //             } else {

    //                 $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
    //             }
    //         }

    //         redirect('admin/testt/import');
    //     }
    // }


    // public function importt()
    // {
    //     if (!$this->rbac->hasPrivilege('import_student', 'can_view')) {
    //         access_denied();
    //     }
    //     $data['title']      = $this->lang->line('import_student');
    //     $data['title_list'] = $this->lang->line('recently_added_student');
    //     $session            = $this->setting_model->getCurrentSession();
    //     $class              = $this->class_model->get('', $classteacher = 'yes');
    //     $data['classlist']  = $class;
    //     $userdata           = $this->customlib->getUserData();

    //     $category = $this->category_model->get();

    //     $fields = array('admission_no', 'roll_no', 'firstname', 'middlename', 'lastname', 'gender', 'dob', 'category_id', 'religion', 'cast', 'mobileno', 'email', 'admission_date', 'blood_group', 'school_house_id', 'height', 'weight', 'measurement_date', 'father_name', 'father_phone', 'father_occupation', 'mother_name', 'mother_phone', 'mother_occupation', 'guardian_is', 'guardian_name', 'guardian_relation', 'guardian_email', 'guardian_phone', 'guardian_occupation', 'guardian_address', 'current_address', 'permanent_address', 'bank_account_no', 'bank_name', 'ifsc_code', 'adhar_no', 'samagra_id', 'rte', 'previous_school', 'note');

    //     $data["fields"]       = $fields;
    //     $data['categorylist'] = $category;
    //     $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_csv_upload');
    //     if ($this->form_validation->run() == false) {
    //         $this->load->view('layout/header', $data);
    //         $this->load->view('admin/test/import', $data);
    //         $this->load->view('layout/footer', $data);
    //     } else { 
            
    //         $class_id   = '';
    //         $section_id = '';

    //         $session = $this->setting_model->getCurrentSession();

    //         if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
    //             $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    //             if ($ext == 'csv') {
    //                 $file = $_FILES['file']['tmp_name'];
    //                 $this->load->library('CSVReader');
    //                 $result = $this->csvreader->parse_file($file);

    //                 if (!empty($result)) {
    //                     $rowcount = 0;
    //                     for ($i = 1; $i <= count($result); $i++) {

    //                         $student_data[$i] = array();
    //                         $n                = 0;
    //                         $hh =0;
                            
    //                         foreach ($result[$i] as $key => $value) {

    //                             if ($key === 'class') {
    //                                 $class_id = $this->test_model->getclassid($this->encoding_lib->toUTF8($value));
    //                             } elseif ($key === 'section') {
    //                                 $section_id = $this->test_model->sectionid($this->encoding_lib->toUTF8($value));
    //                             }else{

    //                                 $student_data[$i][$fields[$n]] = $this->encoding_lib->toUTF8($result[$i][$key]);

    //                                 $student_data[$i]['is_active'] = 'yes';

    //                                 if (date('Y-m-d', strtotime($result[$i]['date_of_birth'])) === $result[$i]['date_of_birth']) {
    //                                     $student_data[$i]['dob'] = date('Y-m-d', strtotime($result[$i]['date_of_birth']));
    //                                 } else {
    //                                     $student_data[$i]['dob'] = null;
    //                                 }

    //                                 if (date('Y-m-d', strtotime($result[$i]['measurement_date'])) === $result[$i]['measurement_date']) {
    //                                     $student_data[$i]['measurement_date'] = date('Y-m-d', strtotime($result[$i]['measurement_date']));
    //                                 } else {
    //                                     $student_data[$i]['measurement_date'] = '';
    //                                 }

    //                                 if (date('Y-m-d', strtotime($result[$i]['admission_date'])) === $result[$i]['admission_date']) {
    //                                     $student_data[$i]['admission_date'] = date('Y-m-d', strtotime($result[$i]['admission_date']));
    //                                 } else {
    //                                     $student_data[$i]['admission_date'] = null;
    //                                 }
    //                             }
    //                             $n++;
    //                         }

    //                         $roll_no                           = $student_data[$i]["roll_no"];
    //                         $adm_no                            = $student_data[$i]["admission_no"];
    //                         $mobile_no                         = $student_data[$i]["mobileno"];
    //                         $email                             = $student_data[$i]["email"];
    //                         $guardian_phone                    = $student_data[$i]["guardian_phone"];
    //                         $guardian_email                    = $student_data[$i]["guardian_email"];
    //                         $data_setting                      = array();
    //                         $data_setting['id']                = $this->sch_setting_detail->id;
    //                         $data_setting['adm_auto_insert']   = $this->sch_setting_detail->adm_auto_insert;
    //                         $data_setting['adm_update_status'] = $this->sch_setting_detail->adm_update_status;
    //                         //-------------------------

    //                         if ($this->sch_setting_detail->adm_auto_insert) {
    //                             if ($this->sch_setting_detail->adm_update_status) {
    //                                 $last_student                     = $this->student_model->lastRecord();
    //                                 $last_admission_digit             = str_replace($this->sch_setting_detail->adm_prefix, "", $last_student->admission_no);
    //                                 $admission_no                     = $this->sch_setting_detail->adm_prefix . sprintf("%0" . $this->sch_setting_detail->adm_no_digit . "d", $last_admission_digit + 1);
    //                                 $student_data[$i]["admission_no"] = $admission_no;
    //                             } else {
    //                                 $admission_no                     = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;
    //                                 $student_data[$i]["admission_no"] = $admission_no;
    //                             }

    //                             $admission_no_exists = $this->student_model->check_adm_exists($admission_no);
    //                             if ($admission_no_exists) {
    //                                 $insert = "";
    //                             } else {
    //                                 $insert_id = $this->student_model->add($student_data[$i], $data_setting);
    //                             }
    //                         } else {

    //                             if ($this->form_validation->is_unique($adm_no, 'students.admission_no')) {
    //                                 $insert_id = $this->student_model->add($student_data[$i], $data_setting);
    //                             } else {
    //                                 $insert_id = "";
    //                             }
    //                         }

    //                         //-------------------------
    //                         if (!empty($insert_id)) {
    //                             $data_new = array(
    //                                 'student_id' => $insert_id,
    //                                 'class_id'   => $class_id,
    //                                 'section_id' => $section_id,
    //                                 'session_id' => $session,
    //                             );

    //                             $this->student_model->add_student_session($data_new);
    //                             $user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
    //                             $sibling_id    = $this->input->post('sibling_id');

    //                             $data_student_login = array(
    //                                 'username' => $this->student_login_prefix . $insert_id,
    //                                 'password' => $user_password,
    //                                 'user_id'  => $insert_id,
    //                                 'role'     => 'student',
    //                             );

    //                             $this->user_model->add($data_student_login);
    //                             $parent_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);

    //                             $temp              = $insert_id;
    //                             $data_parent_login = array(
    //                                 'username' => $this->parent_login_prefix . $insert_id,
    //                                 'password' => $parent_password,
    //                                 'user_id'  => $insert_id,
    //                                 'role'     => 'parent',
    //                                 'childs'   => $temp,
    //                             );

    //                             $ins_id         = $this->user_model->add($data_parent_login);
    //                             $update_student = array(
    //                                 'id'        => $insert_id,
    //                                 'parent_id' => $ins_id,
    //                             );

    //                             $this->student_model->add($update_student);
    //                             $sender_details = array('student_id' => $insert_id, 'contact_no' => $guardian_phone, 'email' => $guardian_email);
    //                             $this->mailsmsconf->mailsms('student_admission', $sender_details);

    //                             $student_login_detail = array('id' => $insert_id, 'credential_for' => 'student', 'username' => $this->student_login_prefix . $insert_id, 'password' => $user_password, 'contact_no' => $mobile_no, 'email' => $email, 'admission_no' => $admission_no);
    //                             $this->mailsmsconf->mailsms('student_login_credential', $student_login_detail);

    //                             $parent_login_detail = array('id' => $insert_id, 'credential_for' => 'parent', 'username' => $this->parent_login_prefix . $insert_id, 'password' => $parent_password, 'contact_no' => $guardian_phone, 'email' => $guardian_email, 'admission_no' => $admission_no);

    //                             $this->mailsmsconf->mailsms('student_login_credential', $parent_login_detail);

    //                             $data['csvData'] = $result;
    //                             $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('students_imported_successfully') . '</div>');

    //                             $rowcount++;
    //                             $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('total') . ' ' . count($result) . $this->lang->line('records_found_in_csv_file_total') . $rowcount . ' ' . $this->lang->line('records_imported_successfully') . '</div>');

    //                         } else {

    //                             $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exist') . '</div>');
    //                         }
    //                     }
    //                 } else {

    //                     $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
    //                 }
    //             } else {

    //                 $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
    //             }
    //         }

    //         redirect('admin/testt/import');
    //     }
    // }



    public function import()
    {
        if (!$this->rbac->hasPrivilege('import_student', 'can_view')) {
            access_denied();
        }
        $data['title']      = $this->lang->line('import_student');
        $data['title_list'] = $this->lang->line('recently_added_student');
        $session            = $this->setting_model->getCurrentSession();
        $class              = $this->class_model->get('', $classteacher = 'yes');
        $data['classlist']  = $class;
        $userdata           = $this->customlib->getUserData();

        $category = $this->category_model->get();

        $fields = array('admission_no', 'roll_no', 'firstname', 'middlename', 'lastname', 'gender', 'dob', 'category_id', 'religion', 'cast', 'mobileno', 'email', 'admission_date', 'blood_group', 'school_house_id', 'height', 'weight', 'measurement_date', 'father_name', 'father_phone', 'father_occupation', 'mother_name', 'mother_phone', 'mother_occupation', 'guardian_is', 'guardian_name', 'guardian_relation', 'guardian_email', 'guardian_phone', 'guardian_occupation', 'guardian_address', 'current_address', 'permanent_address', 'bank_account_no', 'bank_name', 'ifsc_code', 'adhar_no', 'samagra_id', 'rte', 'previous_school', 'note');

        $data["fields"]       = $fields;
        $data['categorylist'] = $category;
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_csv_upload');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/test/import', $data);
            $this->load->view('layout/footer', $data);
        } else { 
            
            

            

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'csv') {
                    $file = $_FILES['file']['tmp_name'];
                    $this->load->library('CSVReader');
                    $result = $this->csvreader->parse_file($file);

                    if (!empty($result)) {
                        $rowcount = 0;
                        for ($i = 1; $i <= count($result); $i++) {

                            $student_data[$i] = array();
                            $n                = 0;
                            $hh =0;
                            $class_id   = '';
                            $section_id = '';
                            // $session = $this->setting_model->getCurrentSession();
                            $session = '';
                            foreach ($result[$i] as $key => $value) {

                                if ($key === 'class') {
                                    $class_id = $this->test_model->getclassid($this->encoding_lib->toUTF8($value));
                                } elseif ($key === 'section') {
                                    $section_id = $this->test_model->sectionid($this->encoding_lib->toUTF8($value));
                                }elseif ($key === 'session'){
                                    $session = $this->test_model->getsessionid($this->encoding_lib->toUTF8($value));
                                }else{

                                    $student_data[$i][$fields[$n]] = $this->encoding_lib->toUTF8($result[$i][$key]);

                                    $student_data[$i]['is_active'] = 'yes';

                                    if (date('Y-m-d', strtotime($result[$i]['date_of_birth'])) === $result[$i]['date_of_birth']) {
                                        $student_data[$i]['dob'] = date('Y-m-d', strtotime($result[$i]['date_of_birth']));
                                    } else {
                                        $student_data[$i]['dob'] = null;
                                    }

                                    if (date('Y-m-d', strtotime($result[$i]['measurement_date'])) === $result[$i]['measurement_date']) {
                                        $student_data[$i]['measurement_date'] = date('Y-m-d', strtotime($result[$i]['measurement_date']));
                                    } else {
                                        $student_data[$i]['measurement_date'] = '';
                                    }

                                    if (date('Y-m-d', strtotime($result[$i]['admission_date'])) === $result[$i]['admission_date']) {
                                        $student_data[$i]['admission_date'] = date('Y-m-d', strtotime($result[$i]['admission_date']));
                                    } else {
                                        $student_data[$i]['admission_date'] = null;
                                    }
                                }
                                $n++;
                            }

                            $roll_no                           = $student_data[$i]["roll_no"];
                            $adm_no                            = $student_data[$i]["admission_no"];
                            $mobile_no                         = $student_data[$i]["mobileno"];
                            $email                             = $student_data[$i]["email"];
                            $guardian_phone                    = $student_data[$i]["guardian_phone"];
                            $guardian_email                    = $student_data[$i]["guardian_email"];
                            $data_setting                      = array();
                            $data_setting['id']                = $this->sch_setting_detail->id;
                            $data_setting['adm_auto_insert']   = $this->sch_setting_detail->adm_auto_insert;
                            $data_setting['adm_update_status'] = $this->sch_setting_detail->adm_update_status;
                            //-------------------------

                            if ($this->sch_setting_detail->adm_auto_insert) {
                                if ($this->sch_setting_detail->adm_update_status) {
                                    $last_student                     = $this->student_model->lastRecord();
                                    $last_admission_digit             = str_replace($this->sch_setting_detail->adm_prefix, "", $last_student->admission_no);
                                    $admission_no                     = $this->sch_setting_detail->adm_prefix . sprintf("%0" . $this->sch_setting_detail->adm_no_digit . "d", $last_admission_digit + 1);
                                    $student_data[$i]["admission_no"] = $admission_no;
                                } else {
                                    $admission_no                     = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;
                                    $student_data[$i]["admission_no"] = $admission_no;
                                }

                                $admission_no_exists = $this->student_model->check_adm_exists($admission_no);
                                if ($admission_no_exists) {
                                    $insert = "";
                                } else {
                                    $insert_id = $this->student_model->add($student_data[$i], $data_setting);
                                    $admi_no_array = array(
                                        'student_id' =>  $insert_id,
                                        'admi_status'=> 0,
                                    );
                                        
                                    $this->student_model->admi_no_add($admi_no_array);
                                }
                            } else {

                                if ($this->form_validation->is_unique($adm_no, 'students.admission_no')) {
                                    $insert_id = $this->student_model->add($student_data[$i], $data_setting);
                                    $admi_no_array = array(
                                        'student_id' =>  $insert_id,
                                        'admi_status'=> 0,
                                    );
                                        
                                    $this->student_model->admi_no_add($admi_no_array);
                                } else {
                                    $insert_id = "";
                                }
                            }

                            //-------------------------
                            if (!empty($insert_id)) {
                                $data_new = array(
                                    'student_id' => $insert_id,
                                    'class_id'   => $class_id,
                                    'section_id' => $section_id,
                                    'session_id' => $session,
                                );

                                $this->student_model->add_student_session($data_new);
                                $user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
                                $sibling_id    = $this->input->post('sibling_id');

                                $data_student_login = array(
                                    'username' => $this->student_login_prefix . $insert_id,
                                    'password' => $user_password,
                                    'user_id'  => $insert_id,
                                    'role'     => 'student',
                                );

                                $this->user_model->add($data_student_login);
                                $parent_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);

                                $temp              = $insert_id;
                                $data_parent_login = array(
                                    'username' => $this->parent_login_prefix . $insert_id,
                                    'password' => $parent_password,
                                    'user_id'  => $insert_id,
                                    'role'     => 'parent',
                                    'childs'   => $temp,
                                );

                                $ins_id         = $this->user_model->add($data_parent_login);
                                $update_student = array(
                                    'id'        => $insert_id,
                                    'parent_id' => $ins_id,
                                );

                                $this->student_model->add($update_student);
                                $sender_details = array('student_id' => $insert_id, 'contact_no' => $guardian_phone, 'email' => $guardian_email);
                                $this->mailsmsconf->mailsms('student_admission', $sender_details);

                                $student_login_detail = array('id' => $insert_id, 'credential_for' => 'student', 'username' => $this->student_login_prefix . $insert_id, 'password' => $user_password, 'contact_no' => $mobile_no, 'email' => $email, 'admission_no' => $admission_no);
                                $this->mailsmsconf->mailsms('student_login_credential', $student_login_detail);

                                $parent_login_detail = array('id' => $insert_id, 'credential_for' => 'parent', 'username' => $this->parent_login_prefix . $insert_id, 'password' => $parent_password, 'contact_no' => $guardian_phone, 'email' => $guardian_email, 'admission_no' => $admission_no);

                                $this->mailsmsconf->mailsms('student_login_credential', $parent_login_detail);

                                $data['csvData'] = $result;
                                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('students_imported_successfully') . '</div>');

                                $rowcount++;
                                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('total') . ' ' . count($result) . $this->lang->line('records_found_in_csv_file_total') . $rowcount . ' ' . $this->lang->line('records_imported_successfully') . '</div>');

                            } else {

                                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exist') . '</div>');
                            }
                        }
                    } else {

                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
                    }
                } else {

                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
                }
            }

            redirect('admin/testt/import');
        }
    }


    public function importt()
    {
        if (!$this->rbac->hasPrivilege('import_student', 'can_view')) {
            access_denied();
        }
        $data['title']      = $this->lang->line('import_student');
        $data['title_list'] = $this->lang->line('recently_added_student');
        $session            = $this->setting_model->getCurrentSession();
        $class              = $this->class_model->get('', $classteacher = 'yes');
        $data['classlist']  = $class;
        $userdata           = $this->customlib->getUserData();

        $category = $this->category_model->get();

        $fields = array('admission_no', 'roll_no', 'firstname', 'middlename', 'lastname', 'gender', 'dob', 'category_id', 'religion', 'cast', 'mobileno', 'email', 'admission_date', 'blood_group', 'school_house_id', 'height', 'weight', 'measurement_date', 'father_name', 'father_phone', 'father_occupation', 'mother_name', 'mother_phone', 'mother_occupation', 'guardian_is', 'guardian_name', 'guardian_relation', 'guardian_email', 'guardian_phone', 'guardian_occupation', 'guardian_address', 'current_address', 'permanent_address', 'bank_account_no', 'bank_name', 'ifsc_code', 'adhar_no', 'samagra_id', 'rte', 'previous_school', 'note');

        $data["fields"]       = $fields;
        $data['categorylist'] = $category;
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_csv_upload');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/test/import', $data);
            $this->load->view('layout/footer', $data);
        } else { 
            
            

            

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'csv') {
                    $file = $_FILES['file']['tmp_name'];
                    $this->load->library('CSVReader');
                    $result = $this->csvreader->parse_file($file);

                    if (!empty($result)) {
                        $rowcount = 0;
                        for ($i = 1; $i <= count($result); $i++) {

                            $student_data[$i] = array();
                            $n                = 0;
                            $hh =0;

                            $class_id   = '';
                            $section_id = '';
                            // $session = $this->setting_model->getCurrentSession();
                            $session = '';
                            
                            foreach ($result[$i] as $key => $value) {

                                if ($key === 'class') {
                                    $class_id = $this->test_model->getclassid($this->encoding_lib->toUTF8($value));
                                    // $class_id = $this->encoding_lib->toUTF8($value);
                                } elseif ($key === 'section') {
                                    $section_id = $this->test_model->sectionid($this->encoding_lib->toUTF8($value));
                                    // $section_id = 1;
                                } elseif ($key === 'session'){
                                    $session = $this->test_model->getsessionid($this->encoding_lib->toUTF8($value));
                                }else{

                                    $student_data[$i][$fields[$n]] = $this->encoding_lib->toUTF8($result[$i][$key]);

                                    $student_data[$i]['is_active'] = 'yes';

                                    if (date('Y-m-d', strtotime($result[$i]['date_of_birth'])) === $result[$i]['date_of_birth']) {
                                        $student_data[$i]['dob'] = date('Y-m-d', strtotime($result[$i]['date_of_birth']));
                                    } else {
                                        $student_data[$i]['dob'] = null;
                                    }

                                    if (date('Y-m-d', strtotime($result[$i]['measurement_date'])) === $result[$i]['measurement_date']) {
                                        $student_data[$i]['measurement_date'] = date('Y-m-d', strtotime($result[$i]['measurement_date']));
                                    } else {
                                        $student_data[$i]['measurement_date'] = '';
                                    }

                                    if (date('Y-m-d', strtotime($result[$i]['admission_date'])) === $result[$i]['admission_date']) {
                                        $student_data[$i]['admission_date'] = date('Y-m-d', strtotime($result[$i]['admission_date']));
                                    } else {
                                        $student_data[$i]['admission_date'] = null;
                                    }
                                }
                                $n++;
                            }

                            $roll_no                           = $student_data[$i]["roll_no"];
                            $adm_no                            = $student_data[$i]["admission_no"];
                            $mobile_no                         = $student_data[$i]["mobileno"];
                            $email                             = $student_data[$i]["email"];
                            $guardian_phone                    = $student_data[$i]["guardian_phone"];
                            $guardian_email                    = $student_data[$i]["guardian_email"];
                            $data_setting                      = array();
                            $data_setting['id']                = $this->sch_setting_detail->id;
                            $data_setting['adm_auto_insert']   = $this->sch_setting_detail->adm_auto_insert;
                            $data_setting['adm_update_status'] = $this->sch_setting_detail->adm_update_status;
                            //-------------------------

                            if ($this->sch_setting_detail->adm_auto_insert) {
                                if ($this->sch_setting_detail->adm_update_status) {
                                    $last_student                     = $this->student_model->lastRecord();
                                    $last_admission_digit             = str_replace($this->sch_setting_detail->adm_prefix, "", $last_student->admission_no);
                                    $admission_no                     = $this->sch_setting_detail->adm_prefix . sprintf("%0" . $this->sch_setting_detail->adm_no_digit . "d", $last_admission_digit + 1);
                                    $student_data[$i]["admission_no"] = $admission_no;
                                } else {
                                    $admission_no                     = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;
                                    $student_data[$i]["admission_no"] = $admission_no;
                                }

                                $admission_no_exists = $this->student_model->check_adm_exists($admission_no);
                                if ($admission_no_exists) {
                                    $insert = "";
                                } else {
                                    $insert_id = $this->student_model->add($student_data[$i], $data_setting);
                                    $admi_no_array = array(
                                        'student_id' =>  $insert_id,
                                        'admi_status'=> 0,
                                    );
                                        
                                    $this->student_model->admi_no_add($admi_no_array);
                                }
                            } else {

                                if ($this->form_validation->is_unique($adm_no, 'students.admission_no')) {
                                    $insert_id = $this->student_model->add($student_data[$i], $data_setting);
                                    $admi_no_array = array(
                                        'student_id' =>  $insert_id,
                                        'admi_status'=> 0,
                                    );
                                        
                                    $this->student_model->admi_no_add($admi_no_array);
                                } else {
                                    $insert_id = "";
                                }
                            }

                            //-------------------------
                            if (!empty($insert_id)) {
                                $data_new = array(
                                    'student_id' => $insert_id,
                                    'class_id'   => $class_id,
                                    'section_id' => $section_id,
                                    'session_id' => $session,
                                );

                                $this->student_model->add_student_session($data_new);
                                $user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
                                $sibling_id    = $this->input->post('sibling_id');

                                $data_student_login = array(
                                    'username' => $this->student_login_prefix . $insert_id,
                                    'password' => $user_password,
                                    'user_id'  => $insert_id,
                                    'role'     => 'student',
                                );

                                $this->user_model->add($data_student_login);
                                $parent_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);

                                $temp              = $insert_id;
                                $data_parent_login = array(
                                    'username' => $this->parent_login_prefix . $insert_id,
                                    'password' => $parent_password,
                                    'user_id'  => $insert_id,
                                    'role'     => 'parent',
                                    'childs'   => $temp,
                                );

                                $ins_id         = $this->user_model->add($data_parent_login);
                                $update_student = array(
                                    'id'        => $insert_id,
                                    'parent_id' => $ins_id,
                                );

                                $this->student_model->add($update_student);
                                $sender_details = array('student_id' => $insert_id, 'contact_no' => $guardian_phone, 'email' => $guardian_email);
                                $this->mailsmsconf->mailsms('student_admission', $sender_details);

                                $student_login_detail = array('id' => $insert_id, 'credential_for' => 'student', 'username' => $this->student_login_prefix . $insert_id, 'password' => $user_password, 'contact_no' => $mobile_no, 'email' => $email, 'admission_no' => $admission_no);
                                $this->mailsmsconf->mailsms('student_login_credential', $student_login_detail);

                                $parent_login_detail = array('id' => $insert_id, 'credential_for' => 'parent', 'username' => $this->parent_login_prefix . $insert_id, 'password' => $parent_password, 'contact_no' => $guardian_phone, 'email' => $guardian_email, 'admission_no' => $admission_no);

                                $this->mailsmsconf->mailsms('student_login_credential', $parent_login_detail);

                                $data['csvData'] = $result;
                                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('students_imported_successfully') . '</div>');

                                $rowcount++;
                                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('total') . ' ' . count($result) . $this->lang->line('records_found_in_csv_file_total') . $rowcount . ' ' . $this->lang->line('records_imported_successfully') . '</div>');

                            } else {

                                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exist') . '</div>');
                            }
                        }
                    } else {

                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
                    }
                } else {

                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
                }
            }

            redirect('admin/testt/import');
        }
    }





    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/new_import_student_sample_file.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_student_sample_file.csv';

        force_download($name, $data);
    }

    
    public function getfeetype()
    {
        $class_id = $this->input->get('class_id');
    
        $data     = $this->test_model->fee_type($class_id);
        echo json_encode($data);
    }



    public function handle_csv_upload()
    {
        $error = "";
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('csv');
            $mimes       = array('text/csv',
                'text/plain',
                'application/csv',
                'text/comma-separated-values',
                'application/excel',
                'application/vnd.ms-excel',
                'application/vnd.msexcel',
                'text/anytext',
                'application/octet-stream',
                'application/txt');
            $temp      = explode(".", $_FILES["file"]["name"]);
            $extension = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if (!in_array($_FILES['file']['type'], $mimes)) {
                $error .= "Error opening the file<br />";
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array($extension, $allowedExts)) {
                $error .= "Error opening the file<br />";
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($error == "") {
                return true;
            }
        } else {
            $this->form_validation->set_message('handle_csv_upload', $this->lang->line('please_select_file'));
            return false;
        }
    }


    public function importfee()
    {
        // if (!$this->rbac->hasPrivilege('import_student', 'can_view')) {
        //     access_denied();
        // }
        $data['title']      = $this->lang->line('import_student');
        $data['title_list'] = $this->lang->line('recently_added_student');
        $session            = $this->setting_model->getCurrentSession();
        // $class              = $this->class_model->get('', $classteacher = 'yes');

        $class   = $this->test_model->get_fee_group();

        $data['classlist']  = $class;
        $userdata           = $this->customlib->getUserData();

        $category = $this->category_model->get();

        $fields = array('admission_no', 'roll_no', 'firstname', 'middlename', 'lastname', 'gender', 'dob', 'category_id', 'religion', 'cast', 'mobileno', 'email', 'admission_date', 'blood_group', 'school_house_id', 'height', 'weight', 'measurement_date', 'father_name', 'father_phone', 'father_occupation', 'mother_name', 'mother_phone', 'mother_occupation', 'guardian_is', 'guardian_name', 'guardian_relation', 'guardian_email', 'guardian_phone', 'guardian_occupation', 'guardian_address', 'current_address', 'permanent_address', 'bank_account_no', 'bank_name', 'ifsc_code', 'adhar_no', 'samagra_id', 'rte', 'previous_school', 'note');

        $data["fields"]       = $fields;
        $data['categorylist'] = $category;
       $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_csv_upload');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/test/importfee', $data);
            $this->load->view('layout/footer', $data);
        } else {

            $student_categorize = 'class';
            if ($student_categorize == 'class') {
                $section = 0;
            } else if ($student_categorize == 'section') {
                $section = $this->input->post('section_id');
            }

            $class_id   = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');

            $session = $this->setting_model->getCurrentSession();
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'csv') {
                    $file = $_FILES['file']['tmp_name'];
                    $this->load->library('CSVReader');
                    $result = $this->csvreader->parse_file($file);

                    if (!empty($result)) {
                        $rowcount = 0;
                        for ($i = 1; $i <= count($result); $i++) {

                            $student_data[$i] = array();
                            $n                = 0;
                            foreach ($result[$i] as $key => $value) {

                                $student_data[$i][$fields[$n]] = $this->encoding_lib->toUTF8($result[$i][$key]);

                                $student_data[$i]['is_active'] = 'yes';

                                if (date('Y-m-d', strtotime($result[$i]['date_of_birth'])) === $result[$i]['date_of_birth']) {
                                    $student_data[$i]['dob'] = date('Y-m-d', strtotime($result[$i]['date_of_birth']));
                                } else {
                                    $student_data[$i]['dob'] = null;
                                }

                                if (date('Y-m-d', strtotime($result[$i]['measurement_date'])) === $result[$i]['measurement_date']) {
                                    $student_data[$i]['measurement_date'] = date('Y-m-d', strtotime($result[$i]['measurement_date']));
                                } else {
                                    $student_data[$i]['measurement_date'] = '';
                                }

                                if (date('Y-m-d', strtotime($result[$i]['admission_date'])) === $result[$i]['admission_date']) {
                                    $student_data[$i]['admission_date'] = date('Y-m-d', strtotime($result[$i]['admission_date']));
                                } else {
                                    $student_data[$i]['admission_date'] = null;
                                }
                                $n++;
                            }

                            $roll_no                           = $student_data[$i]["roll_no"];
                            $adm_no                            = $student_data[$i]["admission_no"];
                            $mobile_no                         = $student_data[$i]["mobileno"];
                            $email                             = $student_data[$i]["email"];
                            $guardian_phone                    = $student_data[$i]["guardian_phone"];
                            $guardian_email                    = $student_data[$i]["guardian_email"];
                            $data_setting                      = array();
                            $data_setting['id']                = $this->sch_setting_detail->id;
                            $data_setting['adm_auto_insert']   = $this->sch_setting_detail->adm_auto_insert;
                            $data_setting['adm_update_status'] = $this->sch_setting_detail->adm_update_status;
                            //-------------------------

                            if ($this->sch_setting_detail->adm_auto_insert) {
                                if ($this->sch_setting_detail->adm_update_status) {
                                    $last_student                     = $this->student_model->lastRecord();
                                    $last_admission_digit             = str_replace($this->sch_setting_detail->adm_prefix, "", $last_student->admission_no);
                                    $admission_no                     = $this->sch_setting_detail->adm_prefix . sprintf("%0" . $this->sch_setting_detail->adm_no_digit . "d", $last_admission_digit + 1);
                                    $student_data[$i]["admission_no"] = $admission_no;
                                } else {
                                    $admission_no                     = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;
                                    $student_data[$i]["admission_no"] = $admission_no;
                                }

                                $admission_no_exists = $this->student_model->check_adm_exists($admission_no);
                                if ($admission_no_exists) {
                                    $insert = "";
                                } else {
                                    $insert_id = $this->student_model->add($student_data[$i], $data_setting);
                                }
                            } else {

                                if ($this->form_validation->is_unique($adm_no, 'students.admission_no')) {
                                    $insert_id = $this->student_model->add($student_data[$i], $data_setting);
                                } else {
                                    $insert_id = "";
                                }
                            }

                            //-------------------------
                            if (!empty($insert_id)) {
                                $data_new = array(
                                    'student_id' => $insert_id,
                                    'class_id'   => $class_id,
                                    'section_id' => $section_id,
                                    'session_id' => $session,
                                );

                                $this->student_model->add_student_session($data_new);
                                $user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
                                $sibling_id    = $this->input->post('sibling_id');

                                $data_student_login = array(
                                    'username' => $this->student_login_prefix . $insert_id,
                                    'password' => $user_password,
                                    'user_id'  => $insert_id,
                                    'role'     => 'student',
                                );

                                $this->user_model->add($data_student_login);
                                $parent_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);

                                $temp              = $insert_id;
                                $data_parent_login = array(
                                    'username' => $this->parent_login_prefix . $insert_id,
                                    'password' => $parent_password,
                                    'user_id'  => $insert_id,
                                    'role'     => 'parent',
                                    'childs'   => $temp,
                                );

                                $ins_id         = $this->user_model->add($data_parent_login);
                                $update_student = array(
                                    'id'        => $insert_id,
                                    'parent_id' => $ins_id,
                                );

                                $this->student_model->add($update_student);
                                $sender_details = array('student_id' => $insert_id, 'contact_no' => $guardian_phone, 'email' => $guardian_email);
                                $this->mailsmsconf->mailsms('student_admission', $sender_details);

                                $student_login_detail = array('id' => $insert_id, 'credential_for' => 'student', 'username' => $this->student_login_prefix . $insert_id, 'password' => $user_password, 'contact_no' => $mobile_no, 'email' => $email, 'admission_no' => $admission_no);
                                $this->mailsmsconf->mailsms('student_login_credential', $student_login_detail);

                                $parent_login_detail = array('id' => $insert_id, 'credential_for' => 'parent', 'username' => $this->parent_login_prefix . $insert_id, 'password' => $parent_password, 'contact_no' => $guardian_phone, 'email' => $guardian_email, 'admission_no' => $admission_no);

                                $this->mailsmsconf->mailsms('student_login_credential', $parent_login_detail);

                                $data['csvData'] = $result;
                                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('students_imported_successfully') . '</div>');

                                $rowcount++;
                                $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('total') . ' ' . count($result) . $this->lang->line('records_found_in_csv_file_total') . $rowcount . ' ' . $this->lang->line('records_imported_successfully') . '</div>');

                            } else {

                                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exist') . '</div>');
                            }
                        }
                    } 
                    
                    else {

                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
                    }


                } else {

                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
                }
            }

            redirect('admin/testt/importfee');
        }
    }



    // public function addstudentfee()
    // {
    //     $data['title']      = $this->lang->line('import_student');
    //     $data['title_list'] = $this->lang->line('recently_added_student');
    //     $session            = $this->setting_model->getCurrentSession();
    //     // $class              = $this->class_model->get('', $classteacher = 'yes');

    //     $class   = $this->test_model->get_fee_group();

    //     $data['classlist']  = $class;
    //     $userdata           = $this->customlib->getUserData();

    //     $category = $this->category_model->get();

    //     $fields = array('admission_no', 'roll_no', 'firstname', 'middlename', 'lastname', 'gender', 'dob', 'category_id', 'religion', 'cast', 'mobileno', 'email', 'admission_date', 'blood_group', 'school_house_id', 'height', 'weight', 'measurement_date', 'father_name', 'father_phone', 'father_occupation', 'mother_name', 'mother_phone', 'mother_occupation', 'guardian_is', 'guardian_name', 'guardian_relation', 'guardian_email', 'guardian_phone', 'guardian_occupation', 'guardian_address', 'current_address', 'permanent_address', 'bank_account_no', 'bank_name', 'ifsc_code', 'adhar_no', 'samagra_id', 'rte', 'previous_school', 'note');

    //     $data["fields"]       = $fields;
    //     $data['categorylist'] = $category;
        


    //     // $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
    //     // $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_csv_upload');
        
        
    //     if ($this->form_validation->run() == false) {
    //         $this->load->view('layout/header', $data);
    //         $this->load->view('admin/test/importfee', $data);
    //         $this->load->view('layout/footer', $data);
    //     }else {

    //         // $class_id   = $this->input->post('class_id');
    //         // $section_id = $this->input->post('section_id');

    //         // $feessionggropid = $this->test_model->feesiongroupid($class_id);
    //         // $fee_groups_feetype_id=$this->test_model->fee_groups_feetypeid($feessionggropid,$class_id,$section_id);

    //         $session = $this->setting_model->getCurrentSession();

    //         if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
    //             $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    //             if ($ext == 'csv') {
    //                 $file = $_FILES['file']['tmp_name'];
    //                 $this->load->library('CSVReader');
    //                 $result = $this->csvreader->parse_file($file);

    //                 if (!empty($result)) {
    //                     $rowcount = 0;
    //                     for ($i = 1; $i <= count($result); $i++) {

    //                         $student_data[$i] = array();
    //                         $n                = 0;

    //                         // $class_id   = $this->input->post('class_id');
    //                         $class_id = $this->test_model->get_fee_group_id($result[$i]['fee_group']);
    //                         $section_id = $this->test_model->fee_type_id($result[$i]['fee_type']);


    //                         $feessionggropid = $this->test_model->feesiongroupid($class_id);
    //                         $fee_groups_feetype_id=$this->test_model->fee_groups_feetypeid($feessionggropid,$class_id,$section_id);



    //                         $studentsessionid = $this->test_model->getstudentid($result[$i]['application_no']);

                            
    //                         $student_fees_master_id=$this->test_model->student_fee_master_id($studentsessionid,$feessionggropid);

    //                         $student_fees_discount_id = '';
                            

    //                         $json_array               = array(
    //                             'amount'          => convertCurrencyFormatToBaseAmount($result[$i]['amount']),
    //                             'amount_discount' => convertCurrencyFormatToBaseAmount($result[$i]['amount_discount']),
    //                             'amount_fine'     => convertCurrencyFormatToBaseAmount($result[$i]['amount_fine']),
    //                             'date'            => date('Y-m-d', $this->customlib->datetostrtotime($result[$i]['date'])),
    //                             'description'     => $result[$i]['description'],
    //                             'collected_by'    => $result[$i]['collected_by'],
    //                             'payment_mode'    => $result[$i]['payment_mode'],
    //                             'received_by'     => $result[$i]['received_by'],
    //                         );

    //                         // $transport_fees_id      = $this->input->post('transport_fees_id');
    //                         // $fee_category           = $this->input->post('fee_category');

    //                         $data = array(
    //                             'fee_category'           => $result[$i]['fee_category'],
    //                             'student_fees_master_id' => $student_fees_master_id,
    //                             'fee_groups_feetype_id'  => $fee_groups_feetype_id,
    //                             'amount_detail'          => $json_array,
    //                             'status'                 => $result[$i]['receipt_id'],
    //                         );

                        
    //                         $send_to            = '';

    //                         $inserted_id        = $this->test_model->fee_deposit($data, $send_to, $student_fees_discount_id);


                        
    //                     }
    //                 }
                    
    //                 else {

    //                     $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
    //                 }
                
    //             } else {

    //                 $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
    //             }
    //         }

    //         redirect('admin/testt/importfee');
    //     }
    // }
    
    
    
    public function addstudentfee()
    {
        $data['title']      = $this->lang->line('import_student');
        $data['title_list'] = $this->lang->line('recently_added_student');
        $session            = $this->setting_model->getCurrentSession();
        // $class              = $this->class_model->get('', $classteacher = 'yes');

        $class   = $this->test_model->get_fee_group();

        $data['classlist']  = $class;
        $userdata           = $this->customlib->getUserData();

        $category = $this->category_model->get();

        $fields = array('admission_no', 'roll_no', 'firstname', 'middlename', 'lastname', 'gender', 'dob', 'category_id', 'religion', 'cast', 'mobileno', 'email', 'admission_date', 'blood_group', 'school_house_id', 'height', 'weight', 'measurement_date', 'father_name', 'father_phone', 'father_occupation', 'mother_name', 'mother_phone', 'mother_occupation', 'guardian_is', 'guardian_name', 'guardian_relation', 'guardian_email', 'guardian_phone', 'guardian_occupation', 'guardian_address', 'current_address', 'permanent_address', 'bank_account_no', 'bank_name', 'ifsc_code', 'adhar_no', 'samagra_id', 'rte', 'previous_school', 'note');

        $data["fields"]       = $fields;
        $data['categorylist'] = $category;
        


        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_csv_upload');
        
        
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/test/importfee', $data);
            $this->load->view('layout/footer', $data);
        }else {

            $session = $this->setting_model->getCurrentSession();

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'csv') {
                    $file = $_FILES['file']['tmp_name'];
                    $this->load->library('CSVReader');
                    $result = $this->csvreader->parse_file($file);

                    if (!empty($result)) {
                        $rowcount = 0;
                        for ($i = 1; $i <= count($result); $i++) {

                            $student_data[$i] = array();
                            $n                = 0;

                            // $class_id   = $this->input->post('class_id');
                            $class_id = $this->test_model->get_fee_group_id($result[$i]['fee_group']);
                            $section_id = $this->test_model->fee_type_id($result[$i]['fee_type']);

                            $feessionggropid = $this->test_model->feesiongroupid($class_id);
                            $studentsessionid = $this->test_model->getstudentid($result[$i]['application_no']);

                            $assigfeestatus     = $this->test_model->assignfeecheck($studentsessionid,$feessionggropid);

                            if(!$assigfeestatus){
                                $feeimport = array(
                                    'student_session_id' => $studentsessionid,
                                    'fee_session_group_id' => $feessionggropid,
                                );
                                $this->test_model->feemasterimport($feeimport);
                                    
                            }
                            
                            $fee_groups_feetype_id=$this->test_model->fee_groups_feetypeid($feessionggropid,$class_id,$section_id);

                            $student_fees_master_id=$this->test_model->student_fee_master_id($studentsessionid,$feessionggropid);

                            $student_fees_discount_id = '';
                            
                            $json_array               = array(
                                'amount'          => convertCurrencyFormatToBaseAmount($result[$i]['amount']),
                                'amount_discount' => convertCurrencyFormatToBaseAmount($result[$i]['amount_discount']),
                                'amount_fine'     => convertCurrencyFormatToBaseAmount($result[$i]['amount_fine']),
                                'date'            => $result[$i]['date'],
                                'description'     => $result[$i]['description'],
                                'collected_by'    => $result[$i]['collected_by'],
                                'payment_mode'    => $result[$i]['payment_mode'],
                                'received_by'     => $result[$i]['received_by'],
                            );

                            // $transport_fees_id      = $this->input->post('transport_fees_id');
                            // $fee_category           = $this->input->post('fee_category');
                            $data = array(
                                'fee_category'           => $result[$i]['fee_category'],
                                'student_fees_master_id' => $student_fees_master_id,
                                'fee_groups_feetype_id'  => $fee_groups_feetype_id,
                                'amount_detail'          => $json_array,
                                'status'                 => $result[$i]['receipt_id'],
                            );
                            
                            $send_to            = '';

                            $inserted_id        = $this->test_model->fee_deposit($data, $send_to, $student_fees_discount_id);
                            if($inserted_id){
                                $rowcount++;
                            }

                        }
                        $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('total') . ' ' . count($result) . $this->lang->line('records_found_in_csv_file_total') . $rowcount .'</div>');

                    }
                    
                    else {

                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
                    }
                
                } else {

                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
                }
            }

            redirect('admin/testt/importfee');
        }
    }




    public function feeexportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/importing_fee.csv";
        $data     = file_get_contents($filepath);
        $name     = 'importing_fee.csv';

        force_download($name, $data);
    }




}


