<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Branch extends MY_Addon_MBController
{

    public function __construct()
    {
        parent::__construct();

    }

    /*
    This function is used to view overview details of all branche
    */
    public function overview()
    {

        $data = array();

        $branches = $this->multibranch_model->getSchoolCurrentSessions();

        $this->load->model("multibranch/multi_common_model");

        $month = date("F", strtotime('-1 month'));
        $year  = date("Y", strtotime('-1 month'));

        $staff_payslip = $this->multi_common_model->getStaffPayslipCount($branches, $month, $year);

        $school_students = $this->multi_common_model->getStudentCount($branches);

        $school_fees = $this->multi_common_model->getCurrentSessionStudentFees($branches);

        $school_transport_fees = $this->multi_common_model->getStudentTransportFees($branches);

        $staff_list = $this->multi_common_model->getStaff($branches);

        $staff_attendance_list = $this->multi_common_model->getStaffAttendance($branches, date('Y-m-d'));

        $student_admission_list = $this->multi_common_model->getOfflineStudentAdmissions($branches);

        $student_online_admission_list = $this->multi_common_model->getOnlineStudentAdmissions($branches);

        $student_books_list = $this->multi_common_model->getBooks($branches);

        $libarary_members_list = $this->multi_common_model->getLibararyMembers($branches);

        $libarary_book_issued_list = $this->multi_common_model->getLibararyBookIssued($branches);

        $alumni_student_list = $this->multi_common_model->getAlumniStudents($branches);

        $user_log_list = $this->multi_common_model->getUserLog($branches);

        foreach ($branches as $_branch_key => $_branch_value) {

//============Staff Payroll==============================
            $payroll_data = $staff_payslip[$_branch_key]['total_payroll_record'];

            $total_net_salary       = 0;
            $salary_generated_staff = 0;
            $salary_paid_staff      = 0;
            $total_amount_paid      = 0;

            if (!empty($payroll_data)) {

                foreach ($payroll_data as $payroll_data_key => $payroll_data_value) {

                    $total_net_salary += $payroll_data_value->net_salary;

                    if ($payroll_data_value->status == "generated") {
                        $salary_generated_staff++;
                    } else {
                        $salary_paid_staff++;
                        $total_amount_paid += $payroll_data_value->net_salary;
                    }
                }

            }

            $staff_payslip[$_branch_key]['staff']                  = $staff_list[$_branch_key]['total_staff'];
            $staff_payslip[$_branch_key]['staff_status_generated'] = $salary_generated_staff;
            $staff_payslip[$_branch_key]['payroll_amount']         = $total_net_salary;
            $staff_payslip[$_branch_key]['staff_status_paid']      = $salary_paid_staff;
            $staff_payslip[$_branch_key]['payroll_amount_paid']    = $total_amount_paid;

//============Staff Payroll end==============================

            //===============fees=======================
            $total_fees    = 0;
            $total_paid    = 0;
            $total_balance = 0;
            if (!empty($school_fees[$_branch_key])) {

                foreach ($school_fees[$_branch_key] as $sch_fee_key => $sch_fee_value) {
                    $total_fees += $sch_fee_value->fee_amount;
                    if (isJSON($sch_fee_value->amount_detail)) {
                        $amount_paid_array = json_decode($sch_fee_value->amount_detail);
                        foreach ($amount_paid_array as $amount_paid_key => $amount_paid_value) {
                            $total_paid += ($amount_paid_value->amount + $amount_paid_value->amount_discount);
                        }

                    }
                }

            }

            $school_students[$_branch_key]['total_fees']    = $total_fees;
            $school_students[$_branch_key]['total_paid']    = $total_paid;
            $school_students[$_branch_key]['total_balance'] = ($total_fees - $total_paid);
//==========================================

            //===============staff attendance=======================

            $staff_present = "0";
            $staff_absent  = "0";

            if (!empty($staff_attendance_list[$_branch_key])) {

                foreach ($staff_attendance_list[$_branch_key] as $staff_attendance_key => $staff_attendance_value) {

                    if ($staff_attendance_value->attendence_id > 0) {

                        if ($staff_attendance_value->att_type == "Absent") {
                            $staff_absent += 1;
                        } else {
                            $staff_present += 1;

                        }

                    }

                }

            }

            $staff_list[$_branch_key]['staff_present'] = $staff_present;
            $staff_list[$_branch_key]['staff_absent']  = $staff_absent;
//==========================================

            //===============student online admission=======================

            $student_admission_list[$_branch_key]['online_admission'] = $student_online_admission_list[$_branch_key]['online_admission'];
//==========================================

            //===============libarary members=======================

            $student_books_list[$_branch_key]['libarary_members'] = $libarary_members_list[$_branch_key]['total_members'];
//==========================================

            //===============libarary book issued=======================

            $student_books_list[$_branch_key]['book_issued'] = $libarary_book_issued_list[$_branch_key]['total_book_issued'];
//==========================================

            //==================Transport Fees Details
            $school_transport_total_fees = 0;
            $school_transport_total_paid = 0;
            if (!empty($school_transport_fees[$_branch_key]['total_fees_record'])) {

                foreach ($school_transport_fees[$_branch_key]['total_fees_record'] as $transport_fee_key => $transport_fee_value) {
                    $school_transport_total_fees += $transport_fee_value->fees;
                    if (isJSON($transport_fee_value->amount_detail)) {
                        $amount_paid_array = json_decode($transport_fee_value->amount_detail);
                        foreach ($amount_paid_array as $amount_paid_key => $amount_paid_value) {
                            $school_transport_total_paid += ($amount_paid_value->amount + $amount_paid_value->amount_discount);
                        }

                    }
                }

            }

            $school_transport_fees[$_branch_key]['total_fees']    = $school_transport_total_fees;
            $school_transport_fees[$_branch_key]['total_paid']    = $school_transport_total_paid;
            $school_transport_fees[$_branch_key]['total_balance'] = ($school_transport_total_fees - $school_transport_total_paid);

        }

        $data['month']                 = $month;
        $data['staff_payslip']         = $staff_payslip;
        $data['school_transport_fees'] = $school_transport_fees;
        $data['staff_list']            = $staff_list;

        $data['school_students']        = $school_students;
        $data['student_admission_list'] = $student_admission_list;
        $data['student_books_list']     = $student_books_list;
        $data['alumni_student_list']    = $alumni_student_list;
        $data['user_log_list']          = $user_log_list;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/multibranch/overview', $data);
        $this->load->view('layout/footer', $data);
    }

    public function upload()
    {

        $data             = array();
        $data['version']  = $this->config->item('version');
        $data['branches'] = $this->multibranch_model->get();

        if (isset($_POST['uploadBtn']) && $_POST['uploadBtn'] == 'Upload') {
            if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK) {
                // get details of the uploaded file
                $fileTmpPath   = $_FILES['uploadedFile']['tmp_name'];
                $fileName      = $_FILES['uploadedFile']['name'];
                $fileSize      = $_FILES['uploadedFile']['size'];
                $fileType      = $_FILES['uploadedFile']['type'];
                $fileNameCmps  = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                // sanitize file-name
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

                // check if file has one of the following extensions
                $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc');

                if (in_array($fileExtension, $allowedfileExtensions)) {
                    // directory in which the uploaded file will be moved
                    $uploadFileDir = dir_path() . '/uploads/';
                    $dest_path     = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $message = 'File is successfully uploaded.';
                    } else {
                        $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
                    }
                } else {
                    $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
                }
            } else {
                $message = 'There is some error in the file upload. Please check the following error.<br>';
                $message .= 'Error:' . $_FILES['uploadedFile']['error'];
            }
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/multibranch/upload', $data);
        $this->load->view('layout/footer', $data);
    }

    /*
    This function is used to show all branch
    */
    public function index()
    {
        $data                                            = array();
        $data['version']                                 = $this->config->item('version');
        $data['branches']                                = $this->multibranch_model->get();
        $setting                                         = $this->setting_model->getSchoolDetail();
        
        $this->load->view('layout/header', $data);
        $this->load->view('admin/multibranch/index', $data);
        $this->load->view('layout/footer', $data);
    }

    /*
    This function is used to load all branch datatabel
    */
    public function getlist()
    {
        $this->load->model("multibranch/multi_income_model");
        $m               = $this->multibranch_model->getlist();
        $m               = json_decode($m);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        if (!empty($m->data)) {
            foreach ($m->data as $branch_key => $branch_value) {
                $edit_btn   = "<button class='btn btn-default btn-xs edit_branch' data-toggle='tooltip' data-recordid=" . $branch_value->id . "    data-loading-text='<i class=" . '" fa fa-spinner fa-spin"' . "  ></i>' title='" . $this->lang->line('edit') . "' ><i class='fa fa fa-pencil'></i></button>";
                $delete_btn = "<button class='btn btn-default btn-xs delete_branch' data-toggle='tooltip' data-recordid=" . $branch_value->id . "    data-loading-text='<i class=" . '" fa fa-spinner fa-spin"' . "  ></i>' title='" . $this->lang->line('delete') . "' ><i class='fa fa fa-remove'></i></button>";

                $row   = array();
                $row[] = $branch_value->branch_name;
                $row[] = $branch_value->branch_url;
                $row[] = $edit_btn . $delete_btn;
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

    /*
    This function is used to switch branch
    */
    public function switchbranchlist()
    {
        $data          = array();
        $active_branch = "";
        if (!is_null(get_cookie('branch_cookie'))) {

            $active_branch = get_cookie('branch_cookie');
            $active_branch = str_replace("branch_", "", $active_branch);
        } else {
            
                $active_branch = 0;
           
        }

        $data['active_branch'] = $active_branch;
        $data['branches']      = $this->multibranch_model->get(null, 1);
        $page                  = $this->load->view('admin/multibranch/_switchbranchlist', $data, true);
        echo json_encode(array('page' => $page));
    }

    /*
    This function is used to verify database
    */
    public function verify()
    {
        $data     = array();
        $host     = $this->input->post('host_name');
        $database = $this->input->post('database');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $result   = $this->multibranch_model->verify_branch($host, $username, $password, $database);
        if (!$result) {
            $array = array('status' => '0', 'error' => '', 'message' => 'Please check Database parameter');
        } else {
            $array = array('status' => '1', 'error' => '', 'message' => 'Database connection verified');
        }

        echo json_encode($array);
    }

   /*
    This function is used to edit branch
    */
    public function edit()
    {
        $data   = array();
        $id     = $this->input->post('recordid');
        $branch = $this->multibranch_model->get($id);
        $array  = array('status' => '1', 'error' => '', 'result' => $branch);
        echo json_encode($array);
    }

    public function switch () {

            $select_branch = $this->input->post('branch');
            $expire        = (60 * 60 * 24 * 365 * 2); //2 Year

            if ($select_branch != 0) {
                $branch = $this->multibranch_model->get($select_branch);
                $branch_group = 'branch_' . $branch->id;
                set_cookie(array(
                    'name'   => 'branch_cookie',
                    'value'  => 'branch_' . $branch->id,
                    'expire' => $expire,
                ));
            } else {
                $branch_group = 'default';
                set_cookie(array(
                    'name'   => 'branch_cookie',
                    'value'  => 'default',
                    'expire' => $expire,
                ));
                 
            }
            $this->new_db  = $this->load->database($branch_group, TRUE);

                $this->new_db->select('sch_settings.id,sch_settings.base_url,sch_settings.folder_path');
                $this->new_db->from('sch_settings');
                $query = $this->new_db->get();
                $db= $query->row();

            $this->session->userdata['admin']['db_array']['db_group']    = $branch_group;
            $this->session->userdata['admin']['db_array']['base_url']    = $db->base_url;
            $this->session->userdata['admin']['db_array']['folder_path'] = $db->folder_path;

            //==================
            $array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);

    }

    /*
    This function is used to add new branch
    */
    public function add()
    { 
        $this->form_validation->set_rules('purchase_code', $this->lang->line('envato_purchase_code'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('host_name', $this->lang->line('hostname'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('database', $this->lang->line('database_name'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('username', $this->lang->line('username'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(                
                'purchase_code' => form_error('purchase_code'),
                'host_name'     => form_error('host_name'),
                'database'      => form_error('database'),
                'username'      => form_error('username'),
                'password'      => form_error('password'),

            );
            $array = array('status' => '0', 'error' => $data);
            echo json_encode($array);
        } else {

           
            $branch_name = ($_POST['branch_name'] != "") ? $this->input->post('branch_name') : null;

            $insert_Arr = array(
                'branch_name' => $branch_name,
                'hostname'    => $this->input->post('host_name'),
                'database_name'    => $this->input->post('database'),
                'username'    => $this->input->post('username'),
                'password'    => $this->input->post('password'),
            );
            $purchase_code = $this->input->post('purchase_code');
            $id            = $this->input->post('id');
            if ($id > 0) {
                $insert_Arr['id'] = $id;
            }

            $result = $this->multibranch_model->verify_branch($insert_Arr);
          

            if (!$result['status']) {
                $array = array('status' => '0', 'error' => array('error' => $result['message']));
            } else {

                $add_status = $this->multibranch_model->add($insert_Arr, $result['result'], $purchase_code);

                if ($add_status) {

                    $response = json_decode($add_status);
                    if ($response->status) {
                        if (is_null($branch_name)) {

                            $branch      = $this->multibranch_model->getName($insert_Arr);
                            $branch_name = $branch->name;

                        }

                        $batch_update_data = array(
                            'id'          => $response->insert_id,
                            'branch_name' => $branch_name,
                           
                            'is_verified' => 1,
                        );

                        $this->multibranch_model->add($batch_update_data, $result, $purchase_code, true);

                        $array = array('status' => '1', 'error' => '', 'message' => 'Database connection verified');

                    } else {
                        print_r($response);
                        $array = array('status' => '0', 'error' => array('error' => $response->response));
                    }
                } else {

                    $array = array('status' => '0', 'error' => array('error' => 'something went wrong Please contact to support'));
                }

            }
            echo json_encode($array);
        }
    }

    /*
    This function is used to delete branch
    */
    public function delete()
    {
        $id = $this->input->post('id');

        $branch = $this->multibranch_model->get($id);
     

        if ($this->db->database == $branch->database_name) {
            $array = array('status' => 0, 'error' => '', 'message' => 'Sorry, You can\'t delete this Database because it is already in Use.');
        } else {
            $this->multibranch_model->remove($id);
            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('delete_message'));
        }

        echo json_encode($array);
    }

}
