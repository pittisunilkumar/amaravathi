<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Adminoimport extends Admin_Controller
{

    public $sch_setting_detail = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->config->load('app-config');
        $this->load->library('mailsmsconf');
        $this->load->library('encoding_lib');
        $this->load->model('student_model');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        
    }

    // public function import()
    // {
    //     if (!$this->rbac->hasPrivilege('import_student', 'can_view')) {
    //         access_denied();
    //     }
    //     $data['title']      = $this->lang->line('import_student');
    //     $data['title_list'] = $this->lang->line('recently_added_student');

    //     $userdata           = $this->customlib->getUserData();


    //     $fields = array('admi_no','admission_no','session');

    //     $data["fields"]       = $fields;
    //     // $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
    //     // $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
    //     $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_csv_upload');
    //     if ($this->form_validation->run() == false) {
    //         $this->load->view('layout/header', $data);
    //         $this->load->view('admin/hallticket/hallticketbulkimport', $data);
    //         $this->load->view('layout/footer', $data);
    //     } else {

    //         // $student_categorize = 'class';
    //         // if ($student_categorize == 'class') {
    //         //     $section = 0;
    //         // } else if ($student_categorize == 'section') {
    //         //     $section = $this->input->post('section_id');
    //         // }
            
    //         $session = $this->setting_model->getCurrentSession();
    //         if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
    //             $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    //             if ($ext == 'csv') {
    //                 $file = $_FILES['file']['tmp_name'];
    //                 $this->load->library('CSVReader');
    //                 $result = $this->csvreader->parse_file($file);

    //                 // if (!empty($result)) {
    //                 //     $rowcount = 0;
    //                 //     for ($i = 1; $i <= count($result); $i++) {

    //                 //         $student_data[$i] = array();
    //                 //         $n                = 0;

    //                 //         foreach ($result[$i] as $key => $value) {

    //                 //             $student_data[$i][$fields[$n]] = $this->encoding_lib->toUTF8($result[$i][$key]);

    //                 //             $student_data[$i]['is_active'] = 'yes';

    //                 //             if (date('Y-m-d', strtotime($result[$i]['date_of_birth'])) === $result[$i]['date_of_birth']) {
    //                 //                 $student_data[$i]['dob'] = date('Y-m-d', strtotime($result[$i]['date_of_birth']));
    //                 //             } else {
    //                 //                 $student_data[$i]['dob'] = null;
    //                 //             }

    //                 //             if (date('Y-m-d', strtotime($result[$i]['measurement_date'])) === $result[$i]['measurement_date']) {
    //                 //                 $student_data[$i]['measurement_date'] = date('Y-m-d', strtotime($result[$i]['measurement_date']));
    //                 //             } else {
    //                 //                 $student_data[$i]['measurement_date'] = '';
    //                 //             }

    //                 //             if (date('Y-m-d', strtotime($result[$i]['admission_date'])) === $result[$i]['admission_date']) {
    //                 //                 $student_data[$i]['admission_date'] = date('Y-m-d', strtotime($result[$i]['admission_date']));
    //                 //             } else {
    //                 //                 $student_data[$i]['admission_date'] = null;
    //                 //             }
    //                 //             $n++;
    //                 //         }

    //                 //         $roll_no                           = $student_data[$i]["roll_no"];
    //                 //         $adm_no                            = $student_data[$i]["admission_no"];
    //                 //         $mobile_no                         = $student_data[$i]["mobileno"];
    //                 //         $email                             = $student_data[$i]["email"];
    //                 //         $guardian_phone                    = $student_data[$i]["guardian_phone"];
    //                 //         $guardian_email                    = $student_data[$i]["guardian_email"];
    //                 //         $data_setting                      = array();
    //                 //         $data_setting['id']                = $this->sch_setting_detail->id;
    //                 //         $data_setting['adm_auto_insert']   = $this->sch_setting_detail->adm_auto_insert;
    //                 //         $data_setting['adm_update_status'] = $this->sch_setting_detail->adm_update_status;
    //                 //         //-------------------------

    //                 //         if ($this->sch_setting_detail->adm_auto_insert) {
    //                 //             if ($this->sch_setting_detail->adm_update_status) {
    //                 //                 $last_student                     = $this->student_model->lastRecord();
    //                 //                 $last_admission_digit             = str_replace($this->sch_setting_detail->adm_prefix, "", $last_student->admission_no);
    //                 //                 $admission_no                     = $this->sch_setting_detail->adm_prefix . sprintf("%0" . $this->sch_setting_detail->adm_no_digit . "d", $last_admission_digit + 1);
    //                 //                 $student_data[$i]["admission_no"] = $admission_no;
    //                 //             } else {
    //                 //                 $admission_no                     = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;
    //                 //                 $student_data[$i]["admission_no"] = $admission_no;
    //                 //             }

    //                 //             $admission_no_exists = $this->student_model->check_adm_exists($admission_no);
    //                 //             if ($admission_no_exists) {
    //                 //                 $insert = "";
    //                 //             } else {
    //                 //                 $insert_id = $this->student_model->add($student_data[$i], $data_setting);
    //                 //             }
    //                 //         } else {

    //                 //             if ($this->form_validation->is_unique($adm_no, 'students.admission_no')) {
    //                 //                 $insert_id = $this->student_model->add($student_data[$i], $data_setting);
    //                 //             } else {
    //                 //                 $insert_id = "";
    //                 //             }
    //                 //         }

    //                 //         //-------------------------
    //                 //         if (!empty($insert_id)) {
    //                 //             $data_new = array(
    //                 //                 'student_id' => $insert_id,
    //                 //                 'class_id'   => $class_id,
    //                 //                 'section_id' => $section_id,
    //                 //                 'session_id' => $session,
    //                 //             );

    //                 //             $this->student_model->add_student_session($data_new);
    //                 //             $user_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
    //                 //             $sibling_id    = $this->input->post('sibling_id');

    //                 //             $data_student_login = array(
    //                 //                 'username' => $this->student_login_prefix . $insert_id,
    //                 //                 'password' => $user_password,
    //                 //                 'user_id'  => $insert_id,
    //                 //                 'role'     => 'student',
    //                 //             );

    //                 //             $this->user_model->add($data_student_login);
    //                 //             $parent_password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);

    //                 //             $temp              = $insert_id;
    //                 //             $data_parent_login = array(
    //                 //                 'username' => $this->parent_login_prefix . $insert_id,
    //                 //                 'password' => $parent_password,
    //                 //                 'user_id'  => $insert_id,
    //                 //                 'role'     => 'parent',
    //                 //                 'childs'   => $temp,
    //                 //             );

    //                 //             $ins_id         = $this->user_model->add($data_parent_login);
    //                 //             $update_student = array(
    //                 //                 'id'        => $insert_id,
    //                 //                 'parent_id' => $ins_id,
    //                 //             );

    //                 //             $this->student_model->add($update_student);
    //                 //             $sender_details = array('student_id' => $insert_id, 'contact_no' => $guardian_phone, 'email' => $guardian_email);
    //                 //             $this->mailsmsconf->mailsms('student_admission', $sender_details);

    //                 //             $student_login_detail = array('id' => $insert_id, 'credential_for' => 'student', 'username' => $this->student_login_prefix . $insert_id, 'password' => $user_password, 'contact_no' => $mobile_no, 'email' => $email, 'admission_no' => $admission_no);
    //                 //             $this->mailsmsconf->mailsms('student_login_credential', $student_login_detail);

    //                 //             $parent_login_detail = array('id' => $insert_id, 'credential_for' => 'parent', 'username' => $this->parent_login_prefix . $insert_id, 'password' => $parent_password, 'contact_no' => $guardian_phone, 'email' => $guardian_email, 'admission_no' => $admission_no);

    //                 //             $this->mailsmsconf->mailsms('student_login_credential', $parent_login_detail);

    //                 //             $data['csvData'] = $result;
    //                 //             $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('students_imported_successfully') . '</div>');

    //                 //             $rowcount++;
    //                 //             $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('total') . ' ' . count($result) . $this->lang->line('records_found_in_csv_file_total') . $rowcount . ' ' . $this->lang->line('records_imported_successfully') . '</div>');

    //                 //         } else {

    //                 //             $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('record_already_exist') . '</div>');
    //                 //         }
    //                 //     }
    //                 // } else {
    //                 //     $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
    //                 // }
    //             } else {

    //                 $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
    //             }
    //         }

    //         redirect('admin/hallticket/hallticketimport/import');

    //     }
    // }

    public function import()
    {
        if (!$this->rbac->hasPrivilege('admibulkimport', 'can_view')) {
            access_denied();
        }
        $data['title']      = $this->lang->line('import_student');
        $data['title_list'] = $this->lang->line('recently_added_student');

        $userdata           = $this->customlib->getUserData();


        $fields = array('admi_no','admission_no','session');

        $csvfileds=array('admi_no','application_no','session');

        $data["csvfileds"] = $csvfileds;

        $data["fields"]       = $fields;
        $this->form_validation->set_rules('file', $this->lang->line('select_csv_file'), 'callback_handle_csv_upload');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/hallticket/adminobulkimport', $data);
            $this->load->view('layout/footer', $data);
        } else {

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
                                $n++;
                            }

                            $admi_no                           = $student_data[$i]["admi_no"];
                            $application_no                    = $student_data[$i]["admission_no"];
                            $session                           = $student_data[$i]["session"];

                            $check_admi_no_data_exists = $this->student_model->check_admi_no_data_exists($admi_no);
                            if(!$check_admi_no_data_exists){
                                $stid=$this->student_model->getuserid($application_no);
                                $data= array(
                                    "admi_no"=>$admi_no,
                                    "admi_status"=>1
                                );

                                // $this->student_model->admi_no_update($data,$stid);
                                $cc = $this->student_model->admi_no_update($data, $stid);
                                if($cc) {
                                    $rowcount++;
                                } 

                            }

                        }
                      $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('total') . ' ' . count($result) . $this->lang->line('records_found_in_csv_file_total') . $rowcount . ' ' . $this->lang->line('records_imported_successfully') .'</div>');

                    } else {
                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
                    }

                } else {

                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
                }
            }

            redirect('admin/hallticket/adminoimport/import');

        }
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


    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/import_student_admission_no.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_student_admission_no.csv';

        force_download($name, $data);
    }


}


?>