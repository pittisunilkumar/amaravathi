<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Webservice extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('mailer');
        $this->load->library(array('customlib', 'enc_lib'));

        $this->load->model(array('auth_model', 'route_model', 'student_model', 'setting_model', 'attendencetype_model', 'studentfeemaster_model', 'feediscount_model', 'teachersubject_model', 'timetable_model', 'user_model', 'examgroup_model', 'webservice_model', 'grade_model', 'librarymember_model', 'bookissue_model', 'homework_model', 'event_model', 'vehroute_model', 'timeline_model', 'module_model', 'paymentsetting_model', 'customfield_model', 'subjecttimetable_model', 'onlineexam_model', 'leave_model', 'chatuser_model', 'conference_model', 'syllabus_model', 'gmeet_model', 'category_model', 'student_edit_field_model', 'filetype_model', 'course_model', 'video_tutorial_model', 'visitors_model', 'pickuppoint_model', 'staff_model', 'assign_incident_model','offlinePayment_model'));

        $setting=$this->setting_model->getSchoolDetail();


        if ($setting->timezone != "") {
                date_default_timezone_set($setting->timezone);
            } else {
                date_default_timezone_set('UTC');
            }
        }


    public function geeee(){
        echo date('Y-m-d H:i:s');
    }

    public function check_lock_enabled_old()
    {
       $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {


          $check_auth_client = $this->auth_model->check_auth_client();
          if ($check_auth_client == true) {
              $response = $this->auth_model->auth();
              if ($response['status'] == 200) {

             $params     = json_decode(file_get_contents('php://input'), true);
            $student     = $this->student_model->get($params['student_id']);          
            $result=$this->setting_model->getSchoolDetail();  

            if ($result->is_student_feature_lock) {

                $lock_grace_period = $result->lock_grace_period;
                $date              = date('Y-m-d', strtotime(date("Y-m-d")) - (86400 * $lock_grace_period));

              

    $student_due_fee = $this->studentfeemaster_model->getDueFeesByStudent($student->student_session_id, $date);

                if (!empty($student_due_fee)) {
                    foreach ($student_due_fee as $result_key => $result_value) {

                        if ($result_value->is_system == 0) {
                            $student_due_fee[$result_key]->{'amount'} = $result_value->fee_amount;
                        }
                        $fee_paid     = 0;
                        $fee_discount = 0;
                        $fee_fine     = 0;

                        $feetype_balance = 0;
                        if (isJSON($result_value->amount_detail)) {
                            $fee_deposits = json_decode(($result_value->amount_detail));
                            foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                $fee_paid     = $fee_paid + $fee_deposits_value->amount;
                                $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                $fee_fine     = $fee_fine + $fee_deposits_value->amount_fine;
                            }
                        }

                        $feetype_balance = ($result_value->amount + $result_value->fine_amount) - ($fee_paid + $fee_fine + $fee_discount);
                        if ($feetype_balance > 0) {

                            json_output($response['status'], array('result'=>true));
                        }
                    }
                }

           
                $class_id   = $student->class_id;
                $section_id = $student->section_id;
                $this->student_model->getStudentByClassSectionID($class_id, $section_id, $student->id);
                $transport_fees = $this->studentfeemaster_model->getDueTransportFeeByStudent($student->student_session_id, $student->route_pickup_point_id, $date);

                if (!empty($transport_fees)) {
                    foreach ($transport_fees as $tran_fee_key => $tran_fee_value) {
                        $fee_paid         = 0;
                        $fee_discount     = 0;
                        $fee_fine         = 0;
                        $fees_fine_amount = 0;
                        $feetype_balance  = 0;
                        if (isJSON($tran_fee_value->amount_detail)) {
                            $fee_deposits = json_decode(($tran_fee_value->amount_detail));
                            foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                $fee_paid     = $fee_paid + $fee_deposits_value->amount;
                                $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                $fee_fine     = $fee_fine + $fee_deposits_value->amount_fine;
                            }
                        }
                        $fees_fine_amount = is_null($tran_fee_value->fine_percentage) ? $tran_fee_value->fine_amount : percentageAmount($tran_fee_value->fees, $tran_fee_value->fine_percentage);

                        $feetype_balance = ($tran_fee_value->fees + $fees_fine_amount) - ($fee_paid + $fee_discount + $fee_fine);

                        if ($feetype_balance > 0) {
                         
                            json_output($response['status'], array('result'=>true));
                        }

                    }
                }

              json_output($response['status'], array('result'=>false)); //=== student unlocked to access dashboard
            }
         json_output($response['status'], array('result'=>false)); //=== student unlocked to access dashboard
    }
 }
}
}


    public function getApplyLeave()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $data                 = array();
                    $params               = json_decode(file_get_contents('php://input'), true);
                    $student_id           = $params['student_id'];
                    $student              = $this->student_model->get($student_id);
                    $result               = $this->leave_model->get($student->student_session_id);
                    foreach($result as $key => $value){
                        if($value['docs'] == null){
                            $result[$key]['docs'] = '';
                        }
                        if($value['approve_by'] == null){
                            $result[$key]['approve_by'] = '';
                        }
                        if($value['approve_date'] == null){
                            $result[$key]['approve_date'] = '';
                        }
                    }
                    $data['result_array'] = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function addLeave()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $data = $this->input->POST();

                    $this->form_validation->set_data($data);
                    $this->form_validation->set_error_delimiters('', '');
                    $this->form_validation->set_rules('from_date', 'From', 'required|trim');
                    $this->form_validation->set_rules('to_date', 'To', 'required|trim');
                    $this->form_validation->set_rules('apply_date', 'Apply Date', 'required|trim');
                    $this->form_validation->set_rules('student_id', 'Student ID', 'required|trim');
                    $this->form_validation->set_rules('reason', 'Reason', 'required|trim');
                    $this->form_validation->set_rules('file', 'File', 'callback_handle_upload_file');
                    if ($this->form_validation->run() == false) {

                        $sss = array(
                            'from_date'  => form_error('from_date'),
                            'to_date'    => form_error('to_date'),
                            'apply_date' => form_error('apply_date'),
                            'student_id' => form_error('student_id'),
                            'reason'     => form_error('reason'),
                            'file'       => form_error('file'),
                        );
                        $array = array('status' => '0', 'error' => $sss);
                    } else {
                        //==================
                        $student = $this->student_model->get($this->input->post('student_id'));

                        $class_id   = $student->class_id;
                        $section_id = $student->section_id;

                        $stafflist = $this->leave_model->getclassteacherbyclasssection($class_id, $section_id);

                        $data = array(
                            'from_date'          => $this->input->post('from_date'),
                            'to_date'            => $this->input->post('to_date'),
                            'apply_date'         => $this->input->post('apply_date'),
                            'reason'             => $this->input->post('reason'),
                            'student_session_id' => $student->student_session_id,
                        );

                        $leave_id = $this->leave_model->add($data);
                        $message_title = "Student Leave";
                        $message = $this->input->post('message') . '<br> Apply Date: ' . $this->input->post('apply_date') . '<br> From Date: ' . $this->input->post('from_date') . '<br> To Date: ' . $this->input->post('to_date');

                        if (!empty($stafflist)) {
                            foreach ($stafflist as $stafflist_value) {
                                $this->mailer->send_mail($stafflist_value['email'], $message_title, $message, $_FILES, "");
                            }
                        }

                        $upload_path = $this->config->item('upload_path') . "/student_leavedocuments/";

                        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                            $fileInfo = pathinfo($_FILES["file"]["name"]);
                            $img_name = $leave_id . '.' . $fileInfo['extension'];
                            move_uploaded_file($_FILES["file"]["tmp_name"], $upload_path . $img_name);
                            $data = array('id' => $leave_id, 'docs' => $img_name);
                            $this->leave_model->add($data);
                        }

                        $array = array('status' => '1', 'msg' => 'Success');
                    }
                    json_output(200, $array);
                }
            }
        }
    }

    public function handle_upload_file()
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if ($files = filesize($_FILES['file']['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload_file', 'File Type Not Allowed');
                    return false;
                }
                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload_file', 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('handle_upload_file', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload_file', "File Type / Extension Error Uploading  Image");
                return false;
            }

            return true;
        }
        return true;
    }

    public function handle_upload_file_compulsory()
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

            $file_type         = $_FILES["file"]['type'];
            $file_size         = $_FILES["file"]["size"];
            $file_name         = $_FILES["file"]["name"];
            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if ($files = filesize($_FILES['file']['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload_file_compulsory', 'File Type Not Allowed');
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload_file_compulsory', 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('handle_upload_file_compulsory', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->file_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload_file_compulsory', "File Type / Extension Error Uploading  Image");
                return false;
            }

            return true;
        } else {

            $this->form_validation->set_message('handle_upload_file_compulsory', "The File Field is required");
            return false;
        }
        return true;
    }

    public function updateLeave()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {

                    $data = $this->input->POST();
                    $this->form_validation->set_data($data);
                    $this->form_validation->set_error_delimiters('', '');
                    $this->form_validation->set_rules('id', 'From', 'required|trim');
                    $this->form_validation->set_rules('from_date', 'From', 'required|trim');
                    $this->form_validation->set_rules('to_date', 'To', 'required|trim');
                    $this->form_validation->set_rules('apply_date', 'Apply Date', 'required|trim');

                    if ($this->form_validation->run() == false) {

                        $sss = array(
                            'id'         => form_error('id'),
                            'from_date'  => form_error('from_date'),
                            'to_date'    => form_error('to_date'),
                            'apply_date' => form_error('apply_date'),
                        );
                        $array = array('status' => '0', 'error' => $sss);
                    } else {
                        //==================
                        $leave_id = $this->input->post('id');
                        $data     = array(
                            'id'         => $this->input->post('id'),
                            'from_date'  => $this->input->post('from_date'),
                            'to_date'    => $this->input->post('to_date'),
                            'apply_date' => $this->input->post('apply_date'),
                            'reason'     => $this->input->post('reason'),
                        );
                        $upload_path = $this->config->item('upload_path') . "/student_leavedocuments/";

                        $this->leave_model->add($data);
                        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                            $fileInfo = pathinfo($_FILES["file"]["name"]);
                            $img_name = $leave_id . '.' . $fileInfo['extension'];
                            move_uploaded_file($_FILES["file"]["tmp_name"], $upload_path . $img_name);
                            $data = array('id' => $leave_id, 'docs' => $img_name);
                            $this->leave_model->add($data);
                        }

                        $array = array('status' => '1', 'msg' => 'Success');
                    }
                    json_output(200, $array);
                }
            }
        }
    }

    public function deleteLeave()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params   = json_decode(file_get_contents('php://input'), true);
                    $leave_id = $params['leave_id'];
                    $this->leave_model->delete($leave_id);

                    json_output($response['status'], array('result' => 'Success'));
                }
            }
        }
    }

    public function getSchoolDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {

                    $result                   = $this->setting_model->getSchoolDisplay();
                    $result->start_month_name = ucfirst($this->customlib->getMonthList($result->start_month));

                    json_output($response['status'], $result);
                }
            }
        }
    }

    public function getStudentProfile()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params    = json_decode(file_get_contents('php://input'), true);
                    $studentId = $params['student_id'];
                    $user_type = $params['user_type'];
                    
                    
                    $student_fields = $this->setting_model->student_fields(); 
                    $student_array  = array();
                    $student_result = $this->student_model->get($studentId); 
                    
                    if($student_result->category == ''){
                       $student_result->category = ''; 
                    }  
					if($student_result->pickup_point_name == ''){
                       $student_result->pickup_point_name = ''; 
                    }
					if($student_result->route_pickup_point_id == ''){
                       $student_result->route_pickup_point_id = ''; 
                    }
					if($student_result->parent_app_key == ''){
                       $student_result->parent_app_key = ''; 
                    }
					if($student_result->vehroute_id == ''){
                       $student_result->vehroute_id = ''; 
                    }
					if($student_result->route_id == ''){
                       $student_result->route_id = ''; 
                    }
					if($student_result->vehicle_id == ''){
                       $student_result->vehicle_id = ''; 
                    }
					if($student_result->route_title == ''){
                       $student_result->route_title = ''; 
                    }
					if($student_result->vehicle_no == ''){
                       $student_result->vehicle_no = ''; 
                    }
					if($student_result->driver_name == ''){
                       $student_result->driver_name = ''; 
                    }
					if($student_result->driver_contact == ''){
                       $student_result->driver_contact = ''; 
                    }
					if($student_result->vehicle_model == ''){
                       $student_result->vehicle_model = ''; 
                    }
					if($student_result->manufacture_year == ''){
                       $student_result->manufacture_year = ''; 
                    }
					if($student_result->driver_licence == ''){
                       $student_result->driver_licence = ''; 
                    }
					if($student_result->middlename == ''){
                       $student_result->middlename = ''; 
                    }
					if($student_result->state == ''){
                       $student_result->state = ''; 
                    }
					if($student_result->city == ''){
                       $student_result->city = ''; 
                    }
					if($student_result->pincode == ''){
                       $student_result->pincode = ''; 
                    }
					if($student_result->updated_at == ''){
                       $student_result->updated_at = ''; 
                    }
					if($student_result->mobileno == ''){
                       $student_result->mobileno = ''; 
                    }
					if($student_result->email == ''){
                       $student_result->email = ''; 
                    }
					if($student_result->state == ''){
                       $student_result->state = ''; 
                    }
					if($student_result->city == ''){
                       $student_result->city = ''; 
                    }
					if($student_result->pincode == ''){
                       $student_result->pincode = ''; 
                    }
					if($student_result->note == ''){
                       $student_result->note = ''; 
                    }
					if($student_result->religion == ''){
                       $student_result->religion = ''; 
                    }
					if($student_result->cast == ''){
                       $student_result->cast = ''; 
                    }
					if($student_result->house_name == ''){
                       $student_result->house_name = ''; 
                    }
					if($student_result->room_no == ''){
                       $student_result->room_no = ''; 
                    } 
					if($student_result->hostel_id == ''){
                       $student_result->hostel_id = ''; 
                    } 
					if($student_result->hostel_name == ''){
                       $student_result->hostel_name = ''; 
                    } 
					if($student_result->room_type_id == ''){
                       $student_result->room_type_id = ''; 
                    }  
					if($student_result->room_type == ''){
                       $student_result->room_type = ''; 
                    }                     				
					
                    $student_result->barcode = "/uploads/student_id_card/barcodes/" . $student_result->admission_no . ".png"; 
                    
                    $ModuleExistOrNot = $this->module_model->getModuleExistOrNot($user_type,'behaviour_records');
                   
                    if(!empty($ModuleExistOrNot)){
                        $student_result->behaviou_score = $this->assign_incident_model->totalpoints($studentId)['totalpoints'];
                    }else{
                        $student_result->behaviou_score = '';
                    }                 

                    $student_array['student_result'] = $student_result;
                    $student_array['student_fields'] = $student_fields;
                    
                    $custom_fields_data  = $this->customfield_model->get_custom_table_values($studentId, 'students');                
                    if (!empty($custom_fields_data)) {
                        foreach ($custom_fields_data as $custom_key => $custom_value) { 
							if($custom_value->field_value == null){
								$custom_value->field_value = '';
							}
                            $custom_fields[$custom_value->name] = $custom_value->field_value; 
                        }
                    }
                    $student_array['custom_fields'] = $custom_fields;                
                    
                    json_output($response['status'], $student_array);
                }
            }
        }
    }

    public function addTask()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {

                $_POST = json_decode(file_get_contents("php://input"), true);
                $this->form_validation->set_data($_POST);
                $this->form_validation->set_error_delimiters('', '');
                $this->form_validation->set_rules('event_title', 'Title', 'required|trim');
                $this->form_validation->set_rules('date', 'Date', 'required|trim');
                $this->form_validation->set_rules('user_id', 'user login id', 'required|trim');

                if ($this->form_validation->run() == false) {

                    $sss = array(
                        'event_title' => form_error('event_title'),
                        'date'        => form_error('date'),
                        'user_id'     => form_error('user_id'),
                    );
                    $array = array('status' => '0', 'error' => $sss);
                } else {
                    //==================                    
                    
                    $data = array(
                        'id' => $this->input->post('task_id'),
                        'event_title' => $this->input->post('event_title'),
                        'start_date'  => $this->input->post('date'),
                        'end_date'    => $this->input->post('date'),
                        'event_type'  => 'task',
                        'is_active'   => 'no',
                        'event_for'   => $this->input->post('user_id'),
                        'event_color' => '#000',
                    );
                    
                    
                    $this->event_model->saveEvent($data);
                    $array = array('status' => '1', 'msg' => 'Success');
                }
                json_output(200, $array);
            }
        }
    }

    public function updatetask()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {

                $_POST = json_decode(file_get_contents("php://input"), true);
                $this->form_validation->set_data($_POST);
                $this->form_validation->set_error_delimiters('', '');
                $this->form_validation->set_rules('task_id', 'Task ID', 'required|trim');
                $this->form_validation->set_rules('status', 'Status', 'required|trim');

                if ($this->form_validation->run() == false) {
                    $errors = array(
                        'task_id' => form_error('task_id'),
                        'status'  => form_error('status'),
                    );
                    $array = array('status' => '0', 'error' => $errors);
                } else {
                    //==================
                    $data = array(
                        'id'        => $this->input->post('task_id'),
                        'is_active' => $this->input->post('status'),
                    );
                    $this->event_model->saveEvent($data);
                    $array = array('status' => '1', 'msg' => 'Success');
                }
                json_output(200, $array);
            }
        }
    }

    public function deletetask()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {

                $_POST = json_decode(file_get_contents("php://input"), true);
                $this->form_validation->set_data($_POST);
                $this->form_validation->set_error_delimiters('', '');
                $this->form_validation->set_rules('task_id', 'Task ID', 'required|trim');

                if ($this->form_validation->run() == false) {

                    $errors = array(
                        'task_id' => form_error('task_id'),
                    );
                    $array = array('status' => '0', 'error' => $errors);
                } else {
                    //==================

                    $id = $this->input->post('task_id');
                    $this->event_model->deleteEvent($id);
                    $array = array('status' => '1', 'msg' => 'Success');
                }
                json_output(200, $array);
            }
        }
    }

    public function logout()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {

                $_POST = json_decode(file_get_contents("php://input"), true);
                $this->form_validation->set_data($_POST);
                $this->form_validation->set_error_delimiters('', '');
                $this->form_validation->set_rules('deviceToken', 'deviceToken', 'required|trim');

                if ($this->form_validation->run() == false) {

                    $errors = array(
                        'deviceToken' => form_error('deviceToken'),
                    );
                    $array = array('status' => '0', 'error' => $errors);
                } else {
                    //==================
                    $deviceToken = $this->input->post('deviceToken');
                    $response    = $this->auth_model->logout($deviceToken);

                    $array = array('status' => '1', 'msg' => 'Success');
                }
                json_output(200, $array);
            }
        }
    }

    public function forgot_password()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {

            $_POST = json_decode(file_get_contents("php://input"), true);
            $this->form_validation->set_error_delimiters('', '');
            $this->form_validation->set_data($_POST);
            $this->form_validation->set_rules('site_url', 'URL', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required');
            $this->form_validation->set_rules('usertype', 'User Type', 'trim|required');
            if ($this->form_validation->run() == false) {
                $errors = validation_errors();
            }

            if (isset($errors)) {
                $respStatus = 400;
                $errors     = array(
                    'email'    => form_error('email'),
                    'usertype' => form_error('usertype'),
                    'site_url' => form_error('site_url'),
                );
                $resp = array('status' => 400, 'message' => $errors);
            } else {
                $email    = $this->input->post('email');
                $usertype = $this->input->post('usertype');
                $site_url = $this->input->post('site_url');
                $result   = $this->user_model->forgotPassword($usertype, $email);

                if ($result && $result->email != "") {
                    $template = $this->setting_model->getTemplate('forgot_password');
                    if (!empty($template) && $template->is_mail && $template->template != "") {
                        $verification_code = $this->enc_lib->encrypt(uniqid(mt_rand()));
                        $update_record     = array('id' => $result->user_tbl_id, 'verification_code' => $verification_code);
                        $this->user_model->updateVerCode($update_record);
                        if ($usertype == "student") {
                            $name = $result->firstname . " " . $result->lastname;
                        } else {
                            $name = $result->guardian_email;
                        }
                        $resetPassLink = $site_url . '/user/resetpassword' . '/' . $usertype . "/" . $verification_code;

                        $body       = $this->forgotPasswordBody($name, $resetPassLink, $template->template);
                        $body_array = json_decode($body);

                        if (!empty($this->mail_config)) {
                            $result = $this->mailer->send_mail($email, $body_array->subject, $body_array->body);
                            if ($result) {
                                $respStatus = 200;
                                $resp       = array('status' => 200, 'message' => "Please check your email to recover your password");
                            } else {
                                $respStatus = 200;
                                $resp       = array('status' => 200, 'message' => "Sending of message failed, Please contact to Admin.");
                            }
                        }
                    } else {
                        $respStatus = 200;
                        $resp       = array('status' => 200, 'message' => "Sending of message failed, Please contact to Admin.");
                    }

                } else {
                    $respStatus = 401;
                    $resp       = array('status' => 401, 'message' => "Invalid Email or User Type");
                }
            }
            json_output($respStatus, $resp);
        }
    }

    public function forgotPasswordBody($name, $resetPassLink, $template)
    {
        $mail_detail['name']          = $name;
        $mail_detail['school_name']   = $this->customlib->getSchoolName();
        $mail_detail['resetPassLink'] = $resetPassLink;
        foreach ($mail_detail as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        //===============
        $subject = "Password Update Request";
        $body    = $template;
        //======================
        return json_encode(array('subject' => $subject, 'body' => $body));
    }

    public function dashboard()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $date_list  = array();
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $student_id = $params['student_id'];
                    $date_from  = $params['date_from'];
                    $date_to    = $params['date_to'];
                    $role       = $params['role'];

                    $student       = $this->student_model->get($student_id);
                    $student_login = $this->user_model->getUserLoginDetails($student_id);

                    $user_role_id = $student_login['id'];
                    if ($role == "parent") {
                        $user_role_id = $params['user_id'];
                    }
                    $attendence_percentage = 0;
                    $resp                  = array();
                    $student_session_id    = $student->student_session_id;
                    $student_attendence    = $this->attendencetype_model->getAttendencePercentage($date_from, $date_to, $student_session_id);
                    $student_homework      = $this->homework_model->getStudentHomeworkPercentage($student_session_id, $student->class_id, $student->section_id);
                    if ($student_attendence->present_attendance > 0 && $student_attendence->total_count > 0) {

                        $attendence_percentage = $student_attendence->present_attendance / $student_attendence->total_count * 100;
                    }

                    $school_setting                        = $this->setting_model->getSchoolDetail();
                    $resp['attendence_type']               = $school_setting->attendence_type;
                    $resp['class_id']                      = $student->class_id;
                    $resp['section_id']                    = $student->section_id;
                    $resp['student_attendence_percentage'] = round($attendence_percentage);
                    $resp['student_homework_incomplete']   = round($student_homework->total_homework - $student_homework->completed);
                    $eventcount                            = $this->event_model->incompleteStudentTaskCounter($user_role_id);

                    if (!empty($eventcount)) {
                        $resp['student_incomplete_task'] = count($eventcount);
                    } else {
                        $resp['student_incomplete_task'] = 0;
                    }

                    $resp['public_events'] = $this->event_model->getPublicEvents($user_role_id, $date_from, $date_to);

                    foreach ($resp['public_events'] as &$ev_tsk_value) {
                        $evt_array = array();
                        if ($ev_tsk_value->event_type == "public") {
                            $start = strtotime($ev_tsk_value->start_date);
                            $end   = strtotime($ev_tsk_value->end_date);

                            for ($st = $start; $st <= $end; $st += 86400) {
                                if ($st >= strtotime($date_from) && $st <= strtotime($date_to)) {

                                    $date_list[date('Y-m-d', $st)] = date('Y-m-d', $st);
                                    $evt_array[]                   = date('Y-m-d', $st);
                                }
                            }

                            $ev_tsk_value->events_lists = implode(",", $evt_array);
                        } elseif ($ev_tsk_value->event_type == "task") {

                            $date_list[date('Y-m-d', strtotime($ev_tsk_value->start_date))] = date('Y-m-d', strtotime($ev_tsk_value->start_date));
                            $evt_array[]                                                    = date('Y-m-d', strtotime($ev_tsk_value->start_date));
                            $ev_tsk_value->events_lists                                     = implode(",", $evt_array);
                        }
                    }
                    $resp['date_lists'] = implode(",", $date_list);

                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function getTask()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params  = json_decode(file_get_contents('php://input'), true);
                    $user_id = $params['user_id'];
                    $resp    = array();

                    $resp['tasks'] = $this->event_model->getTask($user_id);
                    
                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function getDocument()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST       = json_decode(file_get_contents("php://input"), true);
                    $student_id  = $this->input->post('student_id');
                    $student_doc = $this->student_model->getstudentdoc($student_id);
                    json_output($response['status'], $student_doc);
                }
            }
        }
    }

    public function getHomework()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST                    = json_decode(file_get_contents("php://input"), true);
                    $student_id               = $this->input->post('student_id');
                    $homework_status          = $this->input->post('homework_status');
                    $subject_group_subject_id = $this->input->post('subject_group_subject_id');

                    $result     = $this->student_model->get($student_id);
                    $class_id   = $result->class_id;
                    $section_id = $result->section_id;

                    $resulthomework = $this->homework_model->getStudentHomework($class_id, $section_id, $result->student_session_id, $student_id, $subject_group_subject_id);

                    $homeworklist = array();
                    foreach ($resulthomework as $key => $value) {                        
                       
                        if ($value['status'] == $homework_status) {
                            if ($value['document'] == null) {
                                $value['document'] = '';
                            }if ($value['note'] == null) {
                                $value['note'] = '';
                            }if ($value['evaluation_marks'] == null) {
                                $value['evaluation_marks'] = '';
                            }if ($value['marks'] == null) {
                                $value['marks'] = '';
                            }if ($value['evaluation_date'] == null) {
                                $value['evaluation_date'] = '';
                            } 
                            
                            if ($value['evaluated_by'] == null) {
                                $value['evaluated_by'] = '';
                            }else{
                                $staffdetails = $this->staff_model->getAll($value['evaluated_by']);
                                $value['evaluated_by'] = $staffdetails['name'].' '.$staffdetails['surname'].' ('.$staffdetails['employee_id'].')'; 
                            }
                        
                            $homeworklist[] = $value;
                        }
                    }

                    $data["homeworklist"] = $homeworklist;
                    $data["class_id"]     = $class_id;
                    $data["section_id"]   = $section_id;

                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getstudentsubject()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST      = json_decode(file_get_contents("php://input"), true);
                    $student_id = $this->input->post('student_id');
                    $result     = $this->student_model->get($student_id);
                    $class_id   = $result->class_id;
                    $section_id = $result->section_id;
                    $subjectlist = $this->syllabus_model->getmysubjects($class_id, $section_id);                   
                    $data["subjectlist"] = $subjectlist;
                    $data["class_id"]    = $class_id;
                    $data["section_id"]  = $section_id;

                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function addaa()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $data = $this->input->POST();

                    $this->form_validation->set_data($data);
                    $this->form_validation->set_error_delimiters('', '');
                    $this->form_validation->set_rules('student_id', 'Student', 'required|trim');
                    $this->form_validation->set_rules('homework_id', 'Homework', 'required|trim');
                    $this->form_validation->set_rules('message', 'Message', 'required|trim');

                    if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                        $this->form_validation->set_rules('file', 'File', 'callback_handle_upload_file');
                    }

                    if ($this->form_validation->run() == false) {

                        $sss = array(
                            'student_id'  => form_error('student_id'),
                            'homework_id' => form_error('homework_id'),
                            'message'     => form_error('message'),
                            'file'        => form_error('file'),
                        );
                        $array = array('status' => '0', 'error' => $sss);
                    } else {
                        //==================
                        $upload_path = $this->config->item('upload_path') . "/homework/assignment/";

                        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                            $time     = md5($_FILES["file"]['name'] . microtime());
                            $fileInfo = pathinfo($_FILES["file"]["name"]);
                            $img_name = $time . '.' . $fileInfo['extension'];
                            move_uploaded_file($_FILES["file"]["tmp_name"], $upload_path . $img_name);
                            $data_insert = array(
                                'homework_id' => $this->input->post('homework_id'),
                                'student_id'  => $this->input->post('student_id'),
                                'message'     => $this->input->post('message'),
                                'docs'        => $img_name,
                                'file_name'   => $_FILES['file']['name'],
                            );
                            $this->homework_model->add($data_insert);
                        }else{
                            $data_insert = array(
                                'homework_id' => $this->input->post('homework_id'),
                                'student_id'  => $this->input->post('student_id'),
                                'message'     => $this->input->post('message')                                
                            );
                            $this->homework_model->add($data_insert);
                        }

                        $array = array('status' => '1', 'msg' => 'Success');
                    }
                    json_output(200, $array);
                }
            }
        }
    }

    // ---------------- Online Exam ------------------

    public function getOnlineExam()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $student_id = $params['student_id'];
                    $exam_type  = $params['exam_type'];

                    $result = $this->student_model->get($student_id);

                    if ($exam_type == 'closed') {
                        $respdata = $this->onlineexam_model->getstudentclosedexamlist($result->student_session_id);
                    } else {
                        $respdata = $this->onlineexam_model->getStudentexam($result->student_session_id);
                    }
                    
                    $resp['onlineexam']= array();
                    $question = array();
                    foreach ($respdata as $key => $value) {

                        $question = $this->onlineexam_model->getquestiondetails($value->id);

                        if (!empty($question)) {
                            $value->total_question    = $question->total_question;
                            $value->total_descriptive = $question->total_descriptive;
                        } else {
                            $value->total_question    = "0";
                            $value->total_descriptive = "0";
                        }
                        $resp['onlineexam'][] = $value;
                    }

                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function getOnlineExamQuestion()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params                        = json_decode(file_get_contents('php://input'), true);
                    $student_id                    = $params['student_id'];
                    $recordid                      = $params['online_exam_id'];
                    $result                        = $this->student_model->get($student_id);
                    $onlineexam                    = array();
                    $exam                          = $this->onlineexam_model->get($recordid);
                    $onlineexam_student            = $this->onlineexam_model->examstudentsID($result->student_session_id, $exam['id']);
                    $exam['onlineexam_student_id'] = $onlineexam_student->id;
                    $exam['student_session_id']    = $onlineexam_student->student_session_id;
                    $exam['is_submitted']          = $onlineexam_student->is_submitted;

                    $exam['questions']                        = $this->onlineexam_model->getExamQuestions($exam['id'], $exam['is_random_question']);
                    $getStudentAttemts                        = $this->onlineexam_model->getStudentAttemts($onlineexam_student->id);
                    $onlineexam['exam_result_publish_status'] = $exam['publish_result'];
                    $onlineexam['exam_attempt_status']        = 0;

                    if (($exam['auto_publish_date'] != "0000-00-00" && $exam['auto_publish_date'] != null) && strtotime(date('Y-m-d')) >= strtotime($exam['auto_publish_date'])) {
                        $question_status                          = 1;
                        $onlineexam['exam_result_publish_status'] = 1;

                    } else if (strtotime(date('Y-m-d H:i:s')) >= strtotime(date($exam['exam_to']))) {

                        $question_status                   = 1;
                        $onlineexam['exam_attempt_status'] = 1;
                    } else if ($exam['attempt'] > $getStudentAttemts) {

                        $this->onlineexam_model->addStudentAttemts(array('onlineexam_student_id' => $onlineexam_student->id));
                    } else {

                        $question_status                   = 1;
                        $onlineexam['exam_attempt_status'] = 1;
                    }

                    $exam['status']             = $onlineexam;
                    $total_remaining_seconds    = round((strtotime($exam['exam_to']) - strtotime(date('Y-m-d H:i:s'))) / 3600 * 60 * 60, 1);
                    $exam_duration              = ($total_remaining_seconds < getSecondsFromHMS($exam['duration'])) ? getHMSFromSeconds($total_remaining_seconds) : $exam['duration'];
                    $exam['remaining_duration'] = $exam_duration;
                    $total_descriptive          = 0;
                    $question                   = $this->onlineexam_model->getquestiondetails($exam['id']);
                    if (!empty($question)) {
                        $total_descriptive = $question->total_descriptive;
                    } else {
                        $total_descriptive = "0";
                    }
                    $exam['descriptive'] = $total_descriptive;
                    json_output($response['status'], array('exam' => $exam));
                }
            }
        }
    }

    public function getOnlineExamResult()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params                = json_decode(file_get_contents('php://input'), true);
                    $onlineexam_student_id = $params['onlineexam_student_id'];
                    $exam_id               = $params['exam_id'];

                    $exam = $this->onlineexam_model->get($exam_id);

                    $resp['question_result'] = $this->onlineexam_model->getResultByStudent($onlineexam_student_id, $exam_id);

                    $onlineexamStudent      = $this->onlineexam_model->getExamByOnlineexamStudent($onlineexam_student_id);
                    $dispaly_negative_marks = $exam['is_neg_marking'];
                    $exam_total_scored      = 0;
                    $exam_total_marks       = 0;
                    $exam_total_neg_marks   = 0;

                    $correct_ans       = 0;
                    $wrong_ans         = 0;
                    $not_attempted     = 0;
                    $total_question    = 0;
                    $total_descriptive = 0;
                    if (!empty($resp['question_result'])) {
                        $total_question = count($resp['question_result']);

                        foreach ($resp['question_result'] as $result_key => $question_value) {

                            $total_marks_json  = $this->getMarks($question_value);
                            $total_marks_array = (json_decode($total_marks_json));
                            $exam_total_marks  = $exam_total_marks + $total_marks_array->get_marks;
                            $exam_total_scored = $exam_total_scored + $total_marks_array->scr_marks;
                            if ($question_value->question_type == "descriptive") {
                                $total_descriptive++;
                            }

                            if ($question_value->select_option != null) {
                                if ($question_value->question_type == "singlechoice" || $question_value->question_type == "true_false") {
                                    if ($question_value->select_option == $question_value->correct) {
                                        $correct_ans++;
                                    } else {
                                        $exam_total_neg_marks = $exam_total_neg_marks + $question_value->neg_marks;
                                        $wrong_ans++;
                                    }
                                } elseif ($question_value->question_type == "multichoice") {

                                    if ($this->array_equal(json_decode($question_value->correct), json_decode($question_value->select_option))) {
                                        $correct_ans++;
                                    } else {
                                        $exam_total_neg_marks = $exam_total_neg_marks + $question_value->neg_marks;
                                        $wrong_ans++;
                                    }

                                }
                            } else {
                                $not_attempted++;
                            }

                        }
                    }
                    if (!$dispaly_negative_marks) {
                        $exam_total_neg_marks = 0;
                    }
                    if ($exam_total_marks > 0) {
                        $score = number_format(((($exam_total_scored - $exam_total_neg_marks) * 100) / $exam_total_marks), 2, '.', '');
                    } else {
                        $score = 0;
                    }
                    $exam['rank']                 = $onlineexamStudent->rank;
                    $exam['correct_ans']          = $correct_ans;
                    $exam['wrong_ans']            = $wrong_ans;
                    $exam['not_attempted']        = $not_attempted;
                    $exam['total_question']       = $total_question;
                    $exam['total_descriptive']    = $total_descriptive;
                    $exam['exam_total_marks']     = $exam_total_marks;
                    $exam['exam_total_neg_marks'] = $exam_total_neg_marks;
                    $exam['exam_total_scored']    = $exam_total_scored - $exam_total_neg_marks;
                    $exam['score']                = $score;
                    $resp['exam']                 = $exam;

                    json_output($response['status'], array('result' => $resp));
                }
            }
        }
    }

    public function saveOnlineExam()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $question_rows = (json_decode($this->input->post('rows')));
                  

                    foreach ($question_rows as $question_key => $question_value) {
                        if ($question_value->question_type == "descriptive") {
                            $qid = $question_value->onlineexam_question_id;
                            if ((isset($_FILES["attachment_" . $qid]) && !empty($_FILES["attachment_" . $qid]['name']))) {
                                //===============
                                $file_name        = $_FILES["attachment_" . $qid]["name"];
                                $fileInfo         = pathinfo($_FILES["attachment_" . $qid]["name"]);
                                $upload_file_name = time() . uniqid(rand()) . '.' . $fileInfo['extension'];
                                $upload_path      = $this->config->item('upload_path') . "/onlinexam_images/";

                                move_uploaded_file($_FILES["attachment_" . $qid]["tmp_name"], $upload_path . $upload_file_name);
                                $question_value->attachment_name        = $file_name;
                                $question_value->attachment_upload_name = $upload_file_name;
                                //================
                            } else {
                                $question_value->attachment_name        = "";
                                $question_value->attachment_upload_name = "";
                            }
                        } else {
                            $question_value->attachment_name        = "";
                            $question_value->attachment_upload_name = "";
                        }

                        unset($question_value->question_type);
                    }

                    $onlineexam_student_id = $this->input->post('onlineexam_student_id');

                    $resp = array();
                    if (!empty($question_rows)) {
                        $save_result = array();

                        $insert_result = $this->onlineexam_model->add($question_rows, $onlineexam_student_id);
                        $this->onlineexam_model->updateExamSubmitted($onlineexam_student_id);
                        if ($insert_result == 1) {
                            $resp = array('status' => 1, 'msg' => 'record inserted');
                        } else if ($insert_result == 2) {
                            $resp = array('status' => 2, 'msg' => 'record already submitted');
                        } else if ($insert_result == 0) {
                            $resp = array('status' => 2, 'msg' => 'something wrong');
                        }
                    } else {
                        $this->onlineexam_model->updateExamSubmitted($onlineexam_student_id);
                        $resp = array('status' => 1, 'msg' => 'record inserted');
                    }

                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function getExamList()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params               = json_decode(file_get_contents('php://input'), true);
                    $student_id           = $params['student_id'];
                    $result               = $this->student_model->get($student_id);
                    $examSchedule         = $this->examgroup_model->studentExams($result->student_session_id);
                    $data['examSchedule'] = $examSchedule;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getExamSchedule()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params                = json_decode(file_get_contents('php://input'), true);
                    $exam_id               = $params['exam_group_class_batch_exam_id'];
                    $exam_subjects         = $this->examgroup_model->getExamSubjects($exam_id);
                    $data['exam_subjects'] = $exam_subjects;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getNotifications()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), true);
                    $type   = $params['type'];
                    $resp   = $this->webservice_model->getNotifications($type);
                    
                    

                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function getSubjectList()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $class_id   = $params['class_id'];
                    $section_id = $params['section_id'];
                    $resp       = $this->subjecttimetable_model->getSubjects($class_id, $section_id);
                    $subjects   = array();
                    if (!empty($resp)) {

                        foreach ($resp as $res_key => $res_value) {

                            $subjects[] = array(
                                'subject_id' => $res_value->subject_id,
                                'subject'    => $res_value->subject_name,
                                'code'       => $res_value->code,
                                'type'       => $res_value->type,
                            );
                        }
                    }

                    json_output($response['status'], array('result_list' => $subjects));
                }
            }
        }
    }

    public function getSubjectTimetable()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $class_id   = $params['class_id'];
                    $section_id = $params['section_id'];
                    $subject_id = $params['subject_id'];
                    $resp       = $this->subjecttimetable_model->getSubjectTimetable($class_id, $section_id, $subject_id);
                    $subjects   = array();

                    json_output($response['status'], array('result_list' => $resp));
                }
            }
        }
    }

    public function getTeachersList()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $user_id    = $params['user_id'];
                    $class_id   = $params['class_id'];
                    $section_id = $params['section_id'];
                    $resp       = $this->subjecttimetable_model->getTeachers($class_id, $section_id);

                    $class_teacher = array();
                    if (!empty($resp)) {

                        foreach ($resp as $res_key => $res_value) {
                            $is_duplicate = false;
                            $rating       = $this->subjecttimetable_model->user_rating($user_id, $res_value->staff_id);
                            $rate         = 0;
                            $comment         = '';
                            if ($rating) {
                                $rate = $rating->rate;
                                $comment = $rating->comment;
                            }
                            

                            if (is_null($res_value->day)) {
                                $total_row = checkDuplicateTeacher($resp, $res_value->staff_id);
                                if ($total_row > 1) {
                                    $is_duplicate = true;
                                }
                            }
                            if (!$is_duplicate) {
                                if (array_key_exists($res_value->staff_id, $class_teacher)) {

                                    $class_teacher[$res_value->staff_id]['subjects'][] = array(
                                        'subject_id'   => $res_value->subject_id,
                                        'subject_name' => $res_value->subject_name,
                                        'code'         => $res_value->code,
                                        'type'         => $res_value->type,
                                        'day'          => $res_value->day,
                                        'time_from'    => $res_value->time_from,
                                        'time_to'      => $res_value->time_to,
                                        'room_no'      => $res_value->room_no,
                                    );
                                } else {

                                    $class_teacher[$res_value->staff_id] = array(
                                        'employee_id'      => $res_value->employee_id,
                                        'staff_id'         => $res_value->staff_id,
                                        'staff_name'       => $res_value->staff_name,
                                        'staff_surname'    => $res_value->staff_surname,
                                        'contact_no'       => $res_value->contact_no,
                                        'email'            => $res_value->email,
                                        'class_teacher_id' => $res_value->class_teacher_id,
                                        'rate'             => $rate,
                                        'comment'             => $comment,
                                        'subjects'         => array(),
                                    );
                                    if (!is_null($res_value->day)) {
                                        $class_teacher[$res_value->staff_id]['subjects'][] = array(
                                            'subject_id'   => $res_value->subject_id,
                                            'subject_name' => $res_value->subject_name,
                                            'code'         => $res_value->code,
                                            'type'         => $res_value->type,
                                            'day'          => $res_value->day,
                                            'time_from'    => $res_value->time_from,
                                            'time_to'      => $res_value->time_to,
                                            'room_no'      => $res_value->room_no,
                                        );
                                    }
                                }
                            }
                        }
                    }
                    json_output($response['status'], array('result_list' => $class_teacher));
                }
            }
        }
    }

    public function getClassTimetable()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $user_id    = $params['user_id'];
                    $class_id   = $params['class_id'];
                    $section_id = $params['section_id'];
                    $resp       = $this->subjecttimetable_model->getTeachers($class_id, $section_id);

                    $class_teacher = array();
                    if (!empty($resp)) {

                        foreach ($resp as $res_key => $res_value) {
                            $is_duplicate = false;
                            $rating       = $this->subjecttimetable_model->user_rating($user_id, $res_value->staff_id);
                            $rate         = 0;
                            if ($rating) {
                                $rate = $rating->rate;
                            }

                            if (is_null($res_value->day)) {
                                $total_row = checkDuplicateTeacher($resp, $res_value->staff_id);
                                if ($total_row > 1) {
                                    $is_duplicate = true;
                                }
                            }
                            if (!$is_duplicate) {

                                $class_teacher[] = array(
                                    'staff_id'         => $res_value->staff_id,
                                    'staff_name'       => $res_value->staff_name,
                                    'staff_surname'    => $res_value->staff_surname,
                                    'contact_no'       => $res_value->contact_no,
                                    'class_teacher_id' => $res_value->class_teacher_id,
                                    'subject_id'       => $res_value->subject_id,
                                    'subject_name'     => $res_value->subject_name,
                                    'code'             => $res_value->code,
                                    'type'             => $res_value->type,
                                    'day'              => $res_value->day,
                                    'time_from'        => $res_value->time_from,
                                    'time_to'          => $res_value->time_to,
                                    'room_no'          => $res_value->room_no,
                                    'rate'             => $rate,
                                );
                            }
                        }
                    }

                    json_output($response['status'], array('result_list' => $class_teacher));
                }
            }
        }
    }

    public function getTeacherSubject()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), true);

                    $staff_id   = $params['staff_id'];
                    $class_id   = $params['class_id'];
                    $section_id = $params['section_id'];
                    $resp       = $this->subjecttimetable_model->getTeacherSubject($class_id, $section_id, $staff_id);

                    json_output($response['status'], array('result_list' => $resp));
                }
            }
        }
    }

    public function addStaffRating()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {

                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), true);
                    $data   = array(
                        'user_id'  => $params['user_id'],
                        'staff_id' => $params['staff_id'],
                        'rate'     => $params['rate'],
                        'comment'  => $params['comment'],
                        'role'     => 'student',
                    );

                    $insert_result = $this->subjecttimetable_model->add_rating($data);
                    if ($insert_result) {
                        $resp = array('status' => 1, 'msg' => 'inserted');
                    } else {
                        $resp = array('status' => 0, 'msg' => 'something wrong or already submitted');
                    }

                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function getLibraryBooks()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {

                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $resp = $this->webservice_model->getLibraryBooks();
                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function getLibraryBookIssued()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {

                    $params      = json_decode(file_get_contents('php://input'), true);
                    $studentId   = $params['studentId'];
                    $member_type = "student";
                    $resp        = $this->librarymember_model->checkIsMember($member_type, $studentId);

                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function getTransportroute()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {

                    $params       = json_decode(file_get_contents('php://input'), true);
                    $student_id   = $params['student_id'];
                    $student      = $this->student_model->get($student_id);
                    $vec_route_id = $student->vehroute_id;
                    $listroute    = $this->vehroute_model->listroute();

                    if ($vec_route_id != "") {
                        if (!empty($listroute)) {
                            foreach ($listroute as $listroute_key => $listroute_value) {

                                if (!empty($listroute_value['vehicles'])) {
                                    foreach ($listroute_value['vehicles'] as $route_key => $route_value) {
                                        if ($route_value->vec_route_id == $vec_route_id) {
                                            $route_value->assigned = "yes";
                                            break;
                                        } else {
                                            $route_value->assigned = "no";
                                        }
                                    }
                                }
                            }
                        }
                    }

                    json_output($response['status'], $listroute);
                }
            }
        }
    }

    public function getHostelList()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {

                    $params      = json_decode(file_get_contents('php://input'), true);
                    $student_id  = $params['student_id'];
                    $studentList = $this->student_model->get($student_id);

                    $resp = $this->webservice_model->getHostelList();
                    foreach ($resp as $key => $value) {
                        if ($studentList->hostel_room_id == $value['id']) {
                            $resp[$key]['assign'] = 1;
                        } else {
                            $resp[$key]['assign'] = 0;
                        }
                        $resp[$key]['cost_per_bed'] = $value['cost_per_bed'];
                    }

                    $data['hostelarray'] = $resp;
                    json_output($response['status'], $data);
                }
            }
        }
    } 

    public function getDownloadsLinks()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params    = json_decode(file_get_contents('php://input'), true);
                     
                    $classId   = $params['classId'];
                    $sectionId = $params['sectionId'];                    
                    $role       = $params['role'];                   
                     
                    $user_role_id = $params['student_id'];
                    if ($role == "parent") {
                        $user_role_id = $params['user_parent_id'];
                    }
                    
                    if ($role == "student") {
                        $resp = $this->webservice_model->getStudentsharelist($user_role_id, $classId, $sectionId);
                    } elseif ($role == "parent") {
                        $resp = $this->webservice_model->getParentsharelist($user_role_id, $classId, $sectionId);
                    }  
                    
                   
                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function getTransportVehicleDetails()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params    = json_decode(file_get_contents('php://input'), true);
                    $vehicleId = $params['vehicleId'];
                    $resp      = $this->webservice_model->getTransportVehicleDetails($vehicleId);
                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function getAttendenceRecords1()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    ///===================
                    $_POST = json_decode(file_get_contents("php://input"), true);

                    $year                 = $this->input->post('year');
                    $month                = $this->input->post('month');
                    $student_id           = $this->input->post('student_id');
                    $student              = $this->student_model->get($student_id);
                    $student_session_id   = $student->student_session_id;
                    $result               = array();
                    $new_date             = "01-" . $month . "-" . $year;
                    $totalDays            = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    $first_day_this_month = date('01-m-Y');
                    $fst_day_str          = strtotime(date($new_date));
                    $array                = array();
                    for ($day = 2; $day <= $totalDays; $day++) {
                        $fst_day_str        = ($fst_day_str + 86400);
                        $date               = date('Y-m-d', $fst_day_str);
                        $student_attendence = $this->attendencetype_model->getStudentAttendence($date, $student_session_id);
                        if (!empty($student_attendence)) {
                            $s         = array();
                            $s['date'] = $date;
                            $type      = $student_attendence->type;
                            $s['type'] = $type;
                            $array[]   = $s;
                        }
                    }
                    $data['status'] = 200;
                    $data['data']   = $array;
                    json_output($response['status'], $data);

                    //======================
                }
            }
        }
    }

    public function getAttendenceRecords()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $school_setting = $this->setting_model->getSchoolDetail();

                    $_POST                   = json_decode(file_get_contents("php://input"), true);
                    $year                    = $this->input->post('year');
                    $month                   = $this->input->post('month');
                    $student_id              = $this->input->post('student_id');
                    $date                    = $this->input->post('date');
                    $student                 = $this->student_model->get($student_id);
                    $student_session_id      = $student->student_session_id;
                    $data                    = array();
                    $data['attendence_type'] = $school_setting->attendence_type;
                    if ($school_setting->attendence_type) {
                        $timestamp         = strtotime($date);
                        $day               = date('l', $timestamp);
                        $attendence_result = $this->attendencetype_model->studentAttendanceByDate($student->class_id, $student->section_id, $day, $date, $student_session_id);
                        $data['data']      = $attendence_result;
                    } else {

                        $result               = array();
                        $new_date             = "01-" . $month . "-" . $year;
                        $totalDays            = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $first_day_this_month = date('01-m-Y');
                        $fst_day_str          = strtotime(date($new_date));
                        $array                = array();

                        for ($day = 1; $day <= $totalDays; $day++) {
                            $date               = date('Y-m-d', $fst_day_str);
                            $student_attendence = $this->attendencetype_model->getStudentAttendence($date, $student_session_id);
                            if (!empty($student_attendence)) {
                                $s         = array();
                                $s['date'] = $date;
                                $type      = $student_attendence->type;
                                $s['type'] = $type;
                                $array[]   = $s;
                            }
                            $fst_day_str = ($fst_day_str + 86400);
                        }

                        $data['data'] = $array;
                    }

                    json_output($response['status'], $data);

                    //======================
                }
            }
        }
    }

    public function examSchedule()
    {

        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST                = json_decode(file_get_contents("php://input"), true);
                    $student_id           = $this->input->post('student_id');
                    $data                 = array();
                    $stu_record           = $this->student_model->getRecentRecord($student_id);
                    $data['status']       = "200";
                    $data['class_id']     = $stu_record->class_id;
                    $data['section_id']   = $stu_record->section_id;
                    $examSchedule         = $this->examschedule_model->getExamByClassandSection($data['class_id'], $data['section_id']);
                    $data['examSchedule'] = $examSchedule;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getexamscheduledetail()
    {

        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST = json_decode(file_get_contents("php://input"), true);
                    $this->form_validation->set_data($_POST);
                    $exam_id      = $this->input->post('exam_id');
                    $section_id   = $this->input->post('section_id');
                    $class_id     = $this->input->post('class_id');
                    $examSchedule = $this->examschedule_model->getDetailbyClsandSection($class_id, $section_id, $exam_id);
                    json_output($response['status'], $examSchedule);
                }
            }
        }
    }

// ---------- Lesson Plan -------------

    public function getlessonplan()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST = json_decode(file_get_contents("php://input"), true);
                    $this->form_validation->set_data($_POST);
                    $student_id = $this->input->post('student_id');
                    $date_from  = $this->input->post('date_from');
                    $date_to    = $this->input->post('date_to');
                    $student    = $this->student_model->get($student_id);
                    $class_id   = $student->class_id;
                    $section_id = $student->section_id;
                    $result     = $this->syllabus_model->getLessonPlanBwDate($class_id, $section_id, $date_from, $date_to);

                    $syllabus['data'] = array();
                    $start            = strtotime($date_from);
                    $end              = strtotime($date_to);
                    for ($i = $start; $i <= $end; $i += 86400) {
                        $syllabus['data'][date('l', $i)] = array();
                    }

                    if (!empty($result)) {
                        foreach ($result as $result_key => $result_value) {
                            $syllabus['data'][date('l', strtotime($result_value->date))][] = $result_value;
                        }
                    }
                    $data['timetable'] = $syllabus['data'];
                    $data['status']    = "200";
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getsyllabus()
    {

        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST = json_decode(file_get_contents("php://input"), true);
                    $this->form_validation->set_data($_POST);
                    $subject_syllabus_id = $this->input->post('subject_syllabus_id');
                    $syllabus['data']    = $this->syllabus_model->getSyllabusDetail($subject_syllabus_id);
                    json_output($response['status'], $syllabus);
                }
            }
        }
    }

    public function getsyllabussubjects()
    {

        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST = json_decode(file_get_contents("php://input"), true);
                    $this->form_validation->set_data($_POST);
                    $student_id           = $this->input->post('student_id');
                    $stu_record           = $this->student_model->getRecentRecord($student_id);
                    $data['class_id']     = $stu_record['class_id'];
                    $data['section_id']   = $stu_record['section_id'];
                    $subjects['subjects'] = $this->syllabus_model->getSyllabusSubjects($data['class_id'], $data['section_id']);
                    
                    json_output($response['status'], $subjects);
                }
            }
        }
    }

    public function getSubjectsLessons()
    {

        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST = json_decode(file_get_contents("php://input"), true);
                    $this->form_validation->set_data($_POST);
                    $subject_group_subject_id        = $this->input->post('subject_group_subject_id');
                    $subject_group_class_sections_id = $this->input->post('subject_group_class_sections_id');

                    $subjects = $this->syllabus_model->getSubjectsLesson($subject_group_subject_id, $subject_group_class_sections_id);
                    json_output($response['status'], $subjects);
                }
            }
        }
    }

    public function getforummessage()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST = json_decode(file_get_contents("php://input"), true);
                    $this->form_validation->set_data($_POST);
                    $subject_syllabus_id      = $this->input->post('subject_syllabus_id');
                    $forummessage = $this->syllabus_model->getstudentmessage($subject_syllabus_id);                      
                    
                    foreach($forummessage as $key => $value){                     
                        if($value['middlename'] == ''){
                            $forummessage[$key]['middlename'] = '';                    
                        } 
                    }
                    
                    $data['syllabus'] = $forummessage;                   
                    
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function addforummessage()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    $subject_syllabus_id = $this->input->post('subject_syllabus_id');
                    $student_id          = $this->input->post('student_id');
                    $message             = $this->input->post('message');

                    $insert_data = array(
                        'subject_syllabus_id' => $subject_syllabus_id,
                        'type'                => 'student',
                        'student_id'          => $student_id,
                        'message'             => $message,
                        'created_date'        => date('Y-m-d H:i:s'),
                    );

                    $this->syllabus_model->addforummessage($insert_data);
                    $array = array('status' => '1', 'msg' => 'Success');

                    json_output($response['status'], $array);
                }
            }
        }
    }
    
    public function deleteforummessage()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {

                $_POST = json_decode(file_get_contents("php://input"), true);
                $this->form_validation->set_data($_POST);
                $this->form_validation->set_error_delimiters('', '');
                $this->form_validation->set_rules('lesson_plan_forum_id', 'Forum ID', 'required|trim');

                if ($this->form_validation->run() == false) {

                    $errors = array(
                        'lesson_plan_forum_id' => form_error('lesson_plan_forum_id'),
                    );
                    $array = array('status' => '0', 'error' => $errors);
                } else {
                    //==================

                    $id = $this->input->post('lesson_plan_forum_id');
                    $this->syllabus_model->deleteforummessage($id);
                    $array = array('status' => '1', 'msg' => 'Success');
                }
                json_output(200, $array);
            }
        }
    }

    
    // ---------- Fees ------------------
     public function fees()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $data       = array();
                    $pay_method = $this->paymentsetting_model->getActiveMethod();
                    $_POST      = json_decode(file_get_contents("php://input"), true);
                    $student_id = $this->input->post('student_id');
                    $student    = $this->student_model->get($student_id);                   
  
                    $transport_fees = $this->studentfeemaster_model->getStudentTransportFees($student->student_session_id, $student->route_pickup_point_id);

                    $student_due_fee        = $this->studentfeemaster_model->getStudentFees($student->student_session_id);
                    $student_discount_fee   = $this->feediscount_model->getStudentFeesDiscount($student->student_session_id);
                    $init_amt               = 0;
                    $grand_amt              = 0;
                    $grand_total_paid       = 0;
                    $grand_total_discount   = 0;
                    $grand_total_fine       = 0;
                    $fees_fine_amount       = 0;
                    $total_fees_fine_amount = 0;
                

                    if (!empty($transport_fees)) {
                        foreach ($transport_fees as $trans_fee_key => $trans_fee_value) {
                                $amt                                     = 0;
                                $total_paid                              = 0;
                                $total_discount                          = 0;
                                $total_fine                              = 0;

                         $trans_fee_value->total_amount_paid      = ($amt);
                         $trans_fee_value->total_amount_discount  = ($amt);
                         $trans_fee_value->total_amount_fine      = ($amt);
                         $trans_fee_value->total_amount_display   = ($amt);
                         $trans_fee_value->total_amount_remaining = ($trans_fee_value->fees);


                               $trans_fee_value->status  = 'unpaid';
                               $trans_fee_value->fees_fine_amount=0;
                                       $grand_amt += $trans_fee_value->fees;
                       
                        if (($trans_fee_value->due_date != "0000-00-00" && $trans_fee_value->due_date != null) && (strtotime($trans_fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                   

                        if($trans_fee_value->fine_type == "percentage"){
                         $trans_fee_value->fees_fine_amount=($trans_fee_value->fees*$trans_fee_value->fine_percentage)/100;
                         }elseif ($trans_fee_value->fine_type == "fix") {
                               $trans_fee_value->fees_fine_amount=$trans_fee_value->fine_amount;
                         }
                        $total_fees_fine_amount += $trans_fee_value->fees_fine_amount;
                 }
  
                           if (is_string($trans_fee_value->amount_detail) 
                            && is_array(json_decode($trans_fee_value->amount_detail, true))
                            && (json_last_error() == JSON_ERROR_NONE)) {
                          

                                   $fess_list = json_decode($trans_fee_value->amount_detail);

                                    foreach ($fess_list as $fee_key => $fee_value) {

                                        $grand_total_paid = $grand_total_paid + $fee_value->amount;
                                        $total_paid       = $total_paid + $fee_value->amount;

                                        $grand_total_discount = $grand_total_discount + $fee_value->amount_discount;
                                        $total_discount       = $total_discount + $fee_value->amount_discount;

                                        $grand_total_fine = $grand_total_fine + $fee_value->amount_fine;
                                        $total_fine       = $total_fine + $fee_value->amount_fine;
                                    }

                                    $trans_fee_value->total_amount_paid     = ($total_paid);
                                    $trans_fee_value->total_amount_discount = ($total_discount);
                                    $trans_fee_value->total_amount_fine     = ($total_fine);
                                    $trans_fee_value->total_amount_display   = ($total_paid + $total_discount);
                                    $trans_fee_value->total_amount_remaining = ($trans_fee_value->fees - (($total_paid + $total_discount)));

                                    if ($trans_fee_value->total_amount_remaining <= '0.00') {
                                        $trans_fee_value->status = 'paid';
                                    } elseif ($trans_fee_value->total_amount_remaining == number_format((float) $trans_fee_value->fees, 2, '.', '')) {
                                        $trans_fee_value->status = 'unpaid';
                                    } else {
                                        $trans_fee_value->status = 'partial';
                                    }

                           }                      
                       
                        }
 
                    }              

                    if (!empty($student_due_fee)) {

                        foreach ($student_due_fee as $student_due_fee_key => $student_due_fee_value) {

                            foreach ($student_due_fee_value->fees as $each_fees_key => $each_fees_value) {

                                $amt                                     = 0;
                                $total_paid                              = 0;
                                $total_discount                          = 0;
                                $total_fine                              = 0;
                                $each_fees_value->total_amount_paid      = ($amt);
                                $each_fees_value->total_amount_discount  = ($amt);
                                $each_fees_value->total_amount_fine      = ($amt);
                                $each_fees_value->total_amount_display   = ($amt);
                                $each_fees_value->total_amount_remaining = ($each_fees_value->amount);
                                $each_fees_value->status                 = 'unpaid';

                                $grand_amt = $grand_amt + $each_fees_value->amount;
                                if (($each_fees_value->due_date != "0000-00-00" && $each_fees_value->due_date != null) && (strtotime($each_fees_value->due_date) < strtotime(date('Y-m-d')))) {
                                    $fees_fine_amount       = $each_fees_value->fine_amount;
                                    $total_fees_fine_amount = $total_fees_fine_amount + $each_fees_value->fine_amount;
                                }
                                $each_fees_value->fees_fine_amount = $fees_fine_amount;

                                if (is_string($each_fees_value->amount_detail) && is_array(json_decode($each_fees_value->amount_detail, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                                    $fess_list = json_decode($each_fees_value->amount_detail);

                                    foreach ($fess_list as $fee_key => $fee_value) {

                                        $grand_total_paid = $grand_total_paid + $fee_value->amount;
                                        $total_paid       = $total_paid + $fee_value->amount;

                                        $grand_total_discount = $grand_total_discount + $fee_value->amount_discount;
                                        $total_discount       = $total_discount + $fee_value->amount_discount;

                                        $grand_total_fine = $grand_total_fine + $fee_value->amount_fine;
                                        $total_fine       = $total_fine + $fee_value->amount_fine;
                                    }

                                    $each_fees_value->total_amount_paid     = number_format((float) $total_paid, 2, '.', '');
                                    $each_fees_value->total_amount_discount = number_format((float) $total_discount, 2, '.', '');
                                    $each_fees_value->total_amount_fine     = number_format((float) $total_fine, 2, '.', '');

                                    $each_fees_value->total_amount_display   = ($total_paid + $total_discount);
                                    $each_fees_value->total_amount_remaining = ($each_fees_value->amount - (($total_paid + $total_discount)));

                                    if ($each_fees_value->total_amount_remaining <= '0.00') {
                                        $each_fees_value->status = 'paid';
                                    } elseif ($each_fees_value->total_amount_remaining == number_format((float) $each_fees_value->amount, 2, '.', '')) {
                                        $each_fees_value->status = 'unpaid';
                                    } else {
                                        $each_fees_value->status = 'partial';
                                    }
                                }

                                if (($each_fees_value->amount - ($each_fees_value->total_amount_paid + $each_fees_value->total_amount_discount)) == 0) {
                                    $each_fees_value->status = 'paid';
                                }
                            }
                        }
                    }

                    $grand_fee = array('amount' => ($grand_amt), 'amount_discount' => ($grand_total_discount), 'amount_fine' => ($grand_total_fine), 'amount_paid' => ($grand_total_paid), 'amount_remaining' => ($grand_amt - ($grand_total_paid + $grand_total_discount)), 'fee_fine' => ($total_fees_fine_amount));
                    
                    if(empty($transport_fees)){
                        $transport_fees = array();
                    }
                    $data['pay_method']             = empty($pay_method) ? 0 : 1;
                    $data['student_due_fee']        = $student_due_fee;
                    $data['transport_fees']         = $transport_fees;
                    $data['student_discount_fee']   = $student_discount_fee;
                    $data['grand_fee']              = $grand_fee;

                    json_output($response['status'], $data);
                }
            }
        }
    }       

    public function class_schedule()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST      = json_decode(file_get_contents("php://input"), true);
                    $student_id = $this->input->post('student_id');
                    $student    = $this->student_model->get($student_id);
                    $class_id   = $student->class_id;
                    $section_id = $student->section_id;

                    $days        = $this->customlib->getDaysname();
                    $days_record = array();
                    foreach ($days as $day_key => $day_value) {

                        $days_record[$day_key] = $this->subjecttimetable_model->getSubjectByClassandSectionDay($class_id, $section_id, $day_key);
                    }
                    $data['timetable'] = $days_record;
                    $data['status']    = "200";
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getExamResult()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST                          = json_decode(file_get_contents("php://input"), true);
                    $exam_group_class_batch_exam_id = $this->input->post('exam_group_class_batch_exam_id');
                    $student_id                     = $this->input->post('student_id');
                    $student                        = $this->student_model->get($student_id);

                    $dt          = array();
                    $exam_result = $this->examgroup_model->searchExamResult($student->student_session_id, $exam_group_class_batch_exam_id, true, true);

                   
                    $exam_grade = $this->grade_model->getGradeDetails();

                    if (!empty($exam_result->exam_result)) {
                        $exam                                 = new stdClass;
                        $exam->exam_group_class_batch_exam_id = $exam_result->exam_group_class_batch_exam_id;
                        $exam->exam_group_id                  = $exam_result->exam_group_id;
                        $exam->exam                           = $exam_result->exam;
                        $exam->exam_group                     = $exam_result->name;
                        $exam->description                    = $exam_result->description;
                        $exam->exam_type                      = $exam_result->exam_type;
                        $exam->rank                           = $exam_result->rank;
                        $exam->is_rank_generated                           = $exam_result->is_rank_generated;
                        $exam->subject_result                 = array();
                        $exam->total_max_marks                = 0;
                        $exam->total_get_marks                = 0;
                        $exam->total_exam_points              = 0;
                        $exam->exam_quality_points            = 0;
                        $exam->exam_credit_hour               = 0;
                        $exam->exam_credit_hour               = 0;
                        $exam->exam_result_status             = "pass";
                        if ($exam_result->exam_result['exam_connection'] == 0) {
                            $exam->is_consolidate = 0;
                            foreach ($exam_result->exam_result['result'] as $exam_result_key => $exam_result_value) {

                                $subject_array = array();
                                if ($exam_result_value->attendence != "present") {
                                    $exam->exam_result_status = "fail";
                                } elseif ($exam_result_value->get_marks < $exam_result_value->min_marks) {

                                    $exam->exam_result_status = "fail";
                                }
                                $exam->total_max_marks                            = $exam->total_max_marks + $exam_result_value->max_marks;
                                $exam->total_get_marks                            = $exam->total_get_marks + $exam_result_value->get_marks;
                                $percentage                                       = ($exam_result_value->get_marks * 100) / $exam_result_value->max_marks;
                                $subject_array['name']                            = $exam_result_value->name;
                                $subject_array['code']                            = $exam_result_value->code;
                                $subject_array['exam_group_class_batch_exams_id'] = $exam_result_value->exam_group_class_batch_exams_id;
                                $subject_array['room_no']                         = $exam_result_value->room_no;
                                $subject_array['max_marks']                       = $exam_result_value->max_marks;
                                $subject_array['min_marks']                       = $exam_result_value->min_marks;
                                $subject_array['subject_id']                      = $exam_result_value->subject_id;
                                $subject_array['attendence']                      = $exam_result_value->attendence;
                                $subject_array['get_marks']                       = is_null($exam_result_value->get_marks) ? "" :$exam_result_value->get_marks ;
                                $subject_array['exam_group_exam_results_id']      = $exam_result_value->exam_group_exam_results_id;
                                $subject_array['note']                            = $exam_result_value->note;
                                $subject_array['duration']                        = $exam_result_value->duration;
                                $subject_array['credit_hours']                    = $exam_result_value->credit_hours;
                                $subject_array['exam_grade']                      = findExamGrade($exam_grade, $exam_result->exam_type, $percentage);

                                if ($exam_result->exam_type == "gpa") {

                                    $point                                = findGradePoints($exam_grade, $exam_result->exam_type, $percentage);
                                    $exam->exam_quality_points            = $exam->exam_quality_points + ($exam_result_value->credit_hours * $point);
                                    $exam->exam_credit_hour               = $exam->exam_credit_hour + $exam_result_value->credit_hours;
                                    $exam->total_exam_points              = $exam->total_exam_points + $point;
                                    $subject_array['exam_grade_point']    = number_format($point, 2, '.', '');
                                    $subject_array['exam_quality_points'] = $exam_result_value->credit_hours * $point;
                                }
                                $exam->subject_result[] = $subject_array;
                            }
                            $exam->percentage = two_digit_float(($exam->total_get_marks * 100) / $exam->total_max_marks);

                            if ($exam_result->exam_type == "average_passing") {

                                if ($exam_result->passing_percentage <=  $exam->percentage) {
                                     $exam->exam_result_status = "pass";
                                   

                                }else{
                                    echo "string";
                    ;
                                     $exam->exam_result_status = "fail";
                                }
                            }

                             $exam_result->passing_percentage;
                             $exam->percentage;


                            $exam->division   = getExamDivision($exam->percentage);
                            $exam->exam_grade = findExamGrade($exam_grade, $exam_result->exam_type, $exam->percentage);
                        } else {
                            $exam->is_consolidate = 1;
                            $exam_connected_exam  = ($exam_result->exam_result['exam_result']['exam_result_' . $exam_result->exam_group_class_batch_exam_id]);

                            if (!empty($exam_connected_exam)) {
                                foreach ($exam_connected_exam as $exam_result_key => $exam_result_value) {

                                    $subject_array = array();
                                    if ($exam_result_value->attendence != "present") {
                                        $exam->exam_result_status = "fail";
                                    } elseif ($exam_result_value->get_marks < $exam_result_value->min_marks) {

                                        $exam->exam_result_status = "fail";
                                    }
                                    $exam->total_max_marks                            = $exam->total_max_marks + $exam_result_value->max_marks;
                                    $exam->total_get_marks                            = $exam->total_get_marks + $exam_result_value->get_marks;
                                    $percentage                                       = two_digit_float(($exam_result_value->get_marks * 100) / $exam_result_value->max_marks);
                                    $subject_array['name']                            = $exam_result_value->name;
                                    $subject_array['code']                            = $exam_result_value->code;
                                    $subject_array['exam_group_class_batch_exams_id'] = $exam_result_value->exam_group_class_batch_exams_id;
                                    $subject_array['room_no']                         = $exam_result_value->room_no;
                                    $subject_array['max_marks']                       = $exam_result_value->max_marks;
                                    $subject_array['min_marks']                       = $exam_result_value->min_marks;
                                    $subject_array['subject_id']                      = $exam_result_value->subject_id;
                                    $subject_array['attendence']                      = $exam_result_value->attendence;
                                    $subject_array['get_marks']                       = is_null($exam_result_value->get_marks) ? "" : $exam_result_value->get_marks;
                                    $subject_array['exam_group_exam_results_id'] = $exam_result_value->exam_group_exam_results_id;
                                    $subject_array['note']                       = $exam_result_value->note;
                                    $subject_array['duration']                   = $exam_result_value->duration;
                                    $subject_array['credit_hours']               = $exam_result_value->credit_hours;
                                    $subject_array['exam_grade']                 = findExamGrade($exam_grade, $exam_result->exam_type, $percentage);

                                    if ($exam_result->exam_type == "gpa") {

                                        $point                             = findGradePoints($exam_grade, $exam_result->exam_type, $percentage);
                                        $exam->exam_quality_points         = $exam->exam_quality_points + ($exam_result_value->credit_hours * $point);
                                        $exam->exam_credit_hour            = $exam->exam_credit_hour + $exam_result_value->credit_hours;
                                        $exam->total_exam_points           = $exam->total_exam_points + $point;
                                        $subject_array['exam_grade_point'] = number_format($point, 2, '.', '');
                                         $subject_array['exam_quality_points'] = $exam_result_value->credit_hours * $point;
                                    }
                                    $exam->subject_result[] = $subject_array;
                                }
                                $exam->percentage = two_digit_float(($exam->total_get_marks * 100) / $exam->total_max_marks);



                            if ($exam_result->exam_type == "average_passing") {

                                if ($exam_result->passing_percentage <=  $exam->percentage) {
                                     $exam->exam_result_status = "pass";
                                   

                                }else{
                                     $exam->exam_result_status = "fail";
                                }
                            }




                                $exam->division   = getExamDivision($exam->percentage);
                                $exam->exam_grade = findExamGrade($exam_grade, $exam_result->exam_type, $exam->percentage);
                            }
                            $consolidate_result                     = new stdClass;
                            $consolidate_get_total                  = 0;
                            $consolidate_get_total_percentage                  = 0;
                            $consolidate_total_points               = 0;
                            $consolidate_max_total                  = 0;
                            $consolidate_subjects_total             = 0;
                            $consolidate_result->exam_array         = array();
                            $consolidate_result->consolidate_result = array();
                            $consolidate_result_status              = "pass";
                            if (!empty($exam_result->exam_result['exams'])) {
                                $consolidate_exam_result = "pass";
                                foreach ($exam_result->exam_result['exams'] as $each_exam_key => $each_exam_value) {
                                    if ($exam_result->exam_type != "gpa") {
                                        $consolidate_each = getCalculatedExam($exam_result->exam_result['exam_result'], $each_exam_value->id);

                                        if( $each_exam_value->exam_group_type == "average_passing"){


                            if ($exam_result->exam_type == "average_passing") {

                                if ($each_exam_value->passing_percentage <  $exam->percentage) {
                                     $exam->exam_result_status = "pass";
                                   

                                }else{
                                     $exam->exam_result_status = "fail";
                                }
                            }


}elseif ($consolidate_each->exam_status == "fail") {
      $consolidate_result_status = "fail";
}


                                        $consolidate_get_percentage_mark = getConsolidateRatio($exam_result->exam_result['exam_connection_list'], $each_exam_value->id, $consolidate_each->get_marks,$consolidate_each->max_marks);
                                       

                                       $each_exam_value->percentage = $consolidate_get_percentage_mark['marks_weight'];
                                     $consolidate_get_total_percentage+=$consolidate_get_percentage_mark['percentage_weight'];
                                       $each_exam_value->weight = $consolidate_get_percentage_mark['exam_weightage'];
                                        $consolidate_get_total       = $consolidate_get_total + ($consolidate_get_percentage_mark['marks_weight']);
                                        $consolidate_max_total       = $consolidate_max_total + ($consolidate_each->max_marks);
                                    }

                                    if ($exam_result->exam_type == "gpa") {

                                      
                                        $consolidate_each = getCalculatedExamGradePoints($exam_result->exam_result['exam_result'], $each_exam_value->id, $exam_grade, $exam_result->exam_type);

                                        $each_exam_value->total_points = $consolidate_each->total_points;
                                        $each_exam_value->total_exams  = $consolidate_each->total_exams;
                                    

                                        $consolidate_exam_result = ($consolidate_each->return_quality_points / $consolidate_each->return_total_credit_hours);
                                        $consolidate_get_percentage_mark = getConsolidateRatio($exam_result->exam_result['exam_connection_list'], $each_exam_value->id, $consolidate_exam_result,100);
                                        $each_exam_value->percentage     = $consolidate_get_percentage_mark['marks_weight'];
                                         $consolidate_get_total_percentage+=$consolidate_get_percentage_mark['percentage_weight'];
                                              $each_exam_value->weight = $consolidate_get_percentage_mark['exam_weightage'];
                                        $consolidate_get_total           = $consolidate_get_total + ($consolidate_get_percentage_mark['marks_weight']);

                                        $consolidate_subjects_total = $consolidate_subjects_total + $consolidate_each->total_exams;

                                        $each_exam_value->exam_result = number_format($consolidate_exam_result, 2, '.', '');
                                    }

                                    $consolidate_result->exam_array[] = $each_exam_value;
                                }

                                $consolidate_result->consolidate_result['marks_obtain'] = $consolidate_get_total;
                                $consolidate_result->consolidate_result['marks_total']  = $consolidate_max_total;

                                $consolidate_result->consolidate_result['percentage'] = two_digit_float($consolidate_get_total_percentage);
                                $consolidate_result->consolidate_result['division']   = getExamDivision($consolidate_get_total_percentage);
                                if ($exam_result->exam_type != "gpa") {

                                  //  $consolidate_percentage_grade                            = ($consolidate_get_total * 100) / $consolidate_max_total;
                                    $consolidate_result->consolidate_result['result']        = $consolidate_get_total . "/" . $consolidate_max_total;
                                    $consolidate_result->consolidate_result['grade']         = findExamGrade($exam_grade, $exam_result->exam_type, $consolidate_get_total_percentage);
                                    $consolidate_result->consolidate_result['result_status'] = $consolidate_result_status;
                                } elseif ($exam_result->exam_type == "gpa") {
                                    // $consolidate_percentage_grade = ($consolidate_get_total * 100) / $consolidate_subjects_total;

                                    $consolidate_result->consolidate_result['result'] = $consolidate_get_total . "/" . $consolidate_subjects_total;

                                    $consolidate_result->consolidate_result['grade'] = findExamGrade($exam_grade, $exam_result->exam_type, $consolidate_get_total_percentage);
                                }

                               // $consolidate_exam_result_percentage = $consolidate_percentage_grade;
                            }
                            $exam->consolidated_exam_result = $consolidate_result;
                        }
                        $data['exam'] = $exam;
                    }

                    $data['status'] = "200";
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getGradeByMarks($marks = 0)
    {
        $gradeList = $this->grade_model->get();
        if (empty($gradeList)) {
            return "empty list";
        } else {

            foreach ($gradeList as $grade_key => $grade_value) {
                if (round($marks) >= $grade_value['mark_from'] && round($marks) <= $grade_value['mark_upto']) {
                    return $grade_value['name'];
                    break;
                }
            }
            return "no record found";
        }
    }

    public function Parent_GetStudentsList()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $array = array();

                    $_POST           = json_decode(file_get_contents("php://input"), true);
                    $parent_id       = $this->input->post('parent_id');
                    $students_array  = $this->student_model->read_siblings_students($parent_id);
                    $array['childs'] = $students_array;
                    json_output($response['status'], $array);
                }
            }
        }
    }

    public function getModuleStatus()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    $user                = $this->input->post('user');
                    $resp['module_list'] = $this->module_model->get($user);
                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function searchuser()
    {

        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $data = array();

                    $params     = json_decode(file_get_contents('php://input'), true);
                    $student_id = $params['student_id'];
                    $keyword = $params['keyword'];

                    $chat_user    = $this->chatuser_model->getMyID($student_id, 'student');
                    $chat_user_id = 0;
                    if (!empty($chat_user)) {
                        $chat_user_id = $chat_user->id;
                    }

                    $resp['chat_user'] = $this->chatuser_model->searchForUser($keyword, $chat_user_id, $student_id, 'student');
                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function addChatUser()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params      = json_decode(file_get_contents('php://input'), true);
                    $user_type   = $params['user_type'];
                    $user_id     = $params['user_id'];
                    $student_id  = $params['student_id'];
                    $first_entry = array(
                        'user_type'  => "student",
                        'student_id' => $student_id,
                    );
                    $insert_data = array('user_type' => strtolower($user_type), 'create_student_id' => null);

                    if ($user_type == "Student") {
                        $insert_data['student_id'] = $user_id;
                    } elseif ($user_type == "Staff") {
                        $insert_data['staff_id'] = $user_id;
                    }
                    $insert_message = array(
                        'message'            => 'you are now connected on chat',
                        'chat_user_id'       => 0,
                        'is_first'           => 1,
                        'chat_connection_id' => 0,
                    );

                    //===================
                    $new_user_record = $this->chatuser_model->addNewUserForStudent($first_entry, $insert_data, $student_id, $insert_message, 'student');
                    $json_record     = json_decode($new_user_record);

                    //==================

                    $new_user           = $this->chatuser_model->getChatUserDetail($json_record->new_user_id);
                    $chat_user          = $this->chatuser_model->getMyID($student_id, 'student');
                    $data['chat_user']  = $chat_user;
                    $chat_connection_id = $json_record->new_user_chat_connection_id;
                    $chat_to_user       = 0;
                    $user_last_chat     = $this->chatuser_model->getLastMessages($chat_connection_id);

                    $chat_connection = $this->chatuser_model->getChatConnectionByID($chat_connection_id);
                    if (!empty($chat_connection)) {
                        $chat_to_user       = $chat_connection->chat_user_one;
                        $chat_connection_id = $chat_connection->id;
                        if ($chat_connection->chat_user_one == $chat_user->id) {
                            $chat_to_user = $chat_connection->chat_user_two;
                        }
                    }

                    $array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('success_message'), 'new_user' => $new_user, 'chat_connection_id' => $json_record->new_user_chat_connection_id, 'chat_records' => $chat_records, 'user_last_chat' => $user_last_chat);
                    json_output($response['status'], $array);
                }
            }
        }
    }

    public function liveclasses()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST        = json_decode(file_get_contents("php://input"), true);
                    $student_id   = $this->input->post('student_id');
                    $result       = $this->student_model->get($student_id);
                    $class_id     = $result->class_id;
                    $section_id   = $result->section_id;
                    $live_classes = $this->conference_model->getByStudentClassSection($class_id, $section_id);
                    if (!empty($live_classes)) {
                        foreach ($live_classes as $lc_key => $lc_value) {
                            $live_url                            = json_decode($lc_value->return_response);
                            $live_classes[$lc_key]->{'join_url'} = $live_url->join_url;
                            unset($lc_value->return_response);
                        }
                    }

                    $data["live_classes"] = $live_classes;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function livehistory()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST       = json_decode(file_get_contents("php://input"), true);
                    $insert_data = array(
                        'student_id'    => $this->input->post('student_id'),
                        'conference_id' => $this->input->post('conference_id'),
                    );
                    $this->conference_model->updatehistory($insert_data);
                    $array = array('status' => '1', 'msg' => 'Success');
                    json_output($response['status'], $array);
                }
            }
        }
    }

    public function gmeetclasses()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST        = json_decode(file_get_contents("php://input"), true);
                    $student_id   = $this->input->post('student_id');
                    $result       = $this->student_model->get($student_id);
                    $class_id     = $result->class_id;
                    $section_id   = $result->section_id;
                    $live_classes = $this->gmeet_model->getByStudentClassSection($class_id, $section_id);

                    $data["live_classes"] = $live_classes;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function gmeethistory()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST = json_decode(file_get_contents("php://input"), true);

                    $insert_data = array(
                        'student_id' => $this->input->post('student_id'),
                        'gmeet_id'   => $this->input->post('gmeet_id'),
                    );
                    $this->gmeet_model->updatehistory($insert_data);
                    $array = array('status' => '1', 'msg' => 'Success');

                    json_output($response['status'], $array);
                }
            }
        }
    }

    public function checkProfileUpdate()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $school_setting = $this->setting_model->getSchoolDetail()->student_profile_edit;
                    $array          = array('status' => '1', 'student_profile_edit' => $school_setting);
                    json_output($response['status'], $array);
                }
            }
        }
    }

    public function profileUpdateFields()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {

                    $_POST      = json_decode(file_get_contents("php://input"), true);
                    $student_id = $this->input->post('student_id');

                    $inserted_fields        = $this->student_edit_field_model->get();
                    $result['id']           = $student_id;
                    $student                = $this->student_model->get($student_id);
                    $genderList             = $this->customlib->getGender();
                    $result['student']      = $student;
                    $result['genderList']   = $genderList;
                    $vehroute_result        = $this->vehroute_model->get();
                    $result['vehroutelist'] = $vehroute_result;
                    $category               = $this->category_model->get();
                    $result['categorylist'] = $category;
                    $result["bloodgroup"]   = $this->config->item('bloodgroup');
                    $array                  = array();
                    $sch_setting_detail     = $this->setting_model->getSetting();
                    if (!empty($inserted_fields)) {
                        foreach ($inserted_fields as $field_key => $field_value) {
                            $obj         = new stdClass();
                            $obj->name   = $field_value->name;
                            $obj->status = check_student_field_status($sch_setting_detail, $field_value);
                            $array[]     = $obj;
                        }
                    }
                    $result['student_details'] = $array;
                    $array                     = array('status' => '1', 'result' => $result);
                    json_output($response['status'], $array);
                }
            }
        }
    }

    public function editprofile()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $post_data = $this->input->POST();
                    $this->form_validation->set_error_delimiters('', '');
                    $student_id = $this->input->post('student_id');
                    $data['id'] = $student_id;
                    $post_data  = $this->input->post();
                    if (isset($post_data['firstname'])) {
                        $this->form_validation->set_rules('firstname', 'first_name', 'trim|required|xss_clean');
                    }
                    if (isset($post_data['guardian_is'])) {
                        $this->form_validation->set_rules('guardian_is', 'guardian', 'trim|required|xss_clean');
                    }
                    if (isset($post_data['dob'])) {
                        $this->form_validation->set_rules('dob', 'date_of_birth', 'trim|required|xss_clean');
                    }
                    if (isset($post_data['gender'])) {
                        $this->form_validation->set_rules('gender', 'gender', 'trim|required|xss_clean');
                    }
                    if (isset($post_data['guardian_name'])) {
                        $this->form_validation->set_rules('guardian_name', 'guardian_name', 'trim|required|xss_clean');
                    }
                    if (isset($post_data['guardian_phone'])) {
                        $this->form_validation->set_rules('guardian_phone', 'guardian_phone', 'trim|required|xss_clean');
                    }
                    if ($this->form_validation->run() == false) {

                        $validation_error = array();

                        if (isset($post_data['firstname'])) {
                            $validation_error['firstname'] = form_error('firstname');
                        }
                        if (isset($post_data['guardian_is'])) {
                            $validation_error['guardian_is'] = form_error('guardian_is');
                        }
                        if (isset($post_data['dob'])) {
                            $validation_error['dob'] = form_error('dob');
                        }
                        if (isset($post_data['gender'])) {
                            $validation_error['gender'] = form_error('gender');
                        }
                        if (isset($post_data['guardian_name'])) {
                            $validation_error['guardian_name'] = form_error('guardian_name');
                        }
                        if (isset($post_data['guardian_phone'])) {
                            $validation_error['guardian_phone'] = form_error('guardian_phone');
                        }
                        $array = array('status' => '0', 'error' => $validation_error);
                    } else {

                        $student_id = $student_id;
                        $data       = array(
                            'id' => $student_id,
                        );
                        $firstname = $this->input->post('firstname');
                        if (isset($firstname)) {
                            $data['firstname'] = $this->input->post('firstname');
                        }
                        $rte = $this->input->post('rte');
                        if (isset($rte)) {
                            $data['rte'] = $this->input->post('rte');
                        }
                        $pincode = $this->input->post('pincode');
                        if (isset($pincode)) {
                            $data['pincode'] = $this->input->post('pincode');
                        }
                        $cast = $this->input->post('cast');
                        if (isset($cast)) {
                            $data['cast'] = $this->input->post('cast');
                        }
                        $guardian_is = $this->input->post('guardian_is');
                        if (isset($guardian_is)) {
                            $data['guardian_is'] = $this->input->post('guardian_is');
                        }
                        $previous_school = $this->input->post('previous_school');
                        if (isset($previous_school)) {
                            $data['previous_school'] = $this->input->post('previous_school');
                        }
                        $dob = $this->input->post('dob');
                        if (isset($dob)) {
                            $data['dob'] = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('dob')));
                        }
                        $current_address = $this->input->post('current_address');
                        if (isset($current_address)) {
                            $data['current_address'] = $this->input->post('current_address');
                        }
                        $permanent_address = $this->input->post('permanent_address');
                        if (isset($permanent_address)) {
                            $data['permanent_address'] = $this->input->post('permanent_address');
                        }
                        $bank_account_no = $this->input->post('bank_account_no');
                        if (isset($bank_account_no)) {
                            $data['bank_account_no'] = $this->input->post('bank_account_no');
                        }
                        $bank_name = $this->input->post('bank_name');
                        if (isset($bank_name)) {
                            $data['bank_name'] = $this->input->post('bank_name');
                        }
                        $ifsc_code = $this->input->post('ifsc_code');
                        if (isset($ifsc_code)) {
                            $data['ifsc_code'] = $this->input->post('ifsc_code');
                        }
                        $guardian_occupation = $this->input->post('guardian_occupation');
                        if (isset($guardian_occupation)) {
                            $data['guardian_occupation'] = $this->input->post('guardian_occupation');
                        }
                        $guardian_email = $this->input->post('guardian_email');
                        if (isset($guardian_email)) {
                            $data['guardian_email'] = $this->input->post('guardian_email');
                        }
                        $gender = $this->input->post('gender');
                        if (isset($gender)) {
                            $data['gender'] = $this->input->post('gender');
                        }
                        $guardian_name = $this->input->post('guardian_name');
                        if (isset($guardian_name)) {
                            $data['guardian_name'] = $this->input->post('guardian_name');
                        }
                        $guardian_relation = $this->input->post('guardian_relation');
                        if (isset($guardian_relation)) {
                            $data['guardian_relation'] = $this->input->post('guardian_relation');
                        }
                        $guardian_phone = $this->input->post('guardian_phone');
                        if (isset($guardian_phone)) {
                            $data['guardian_phone'] = $this->input->post('guardian_phone');
                        }
                        $guardian_address = $this->input->post('guardian_address');
                        if (isset($guardian_address)) {
                            $data['guardian_address'] = $this->input->post('guardian_address');
                        }
                        $adhar_no = $this->input->post('adhar_no');
                        if (isset($adhar_no)) {
                            $data['adhar_no'] = $this->input->post('adhar_no');
                        }
                        $samagra_id = $this->input->post('samagra_id');
                        if (isset($samagra_id)) {
                            $data['samagra_id'] = $this->input->post('samagra_id');
                        }

                        $house             = $this->input->post('house');
                        $blood_group       = $this->input->post('blood_group');
                        $measurement_date  = $this->input->post('measure_date');
                        $roll_no           = $this->input->post('roll_no');
                        $lastname          = $this->input->post('lastname');
                        $category_id       = $this->input->post('category_id');
                        $religion          = $this->input->post('religion');
                        $mobileno          = $this->input->post('mobileno');
                        $email             = $this->input->post('email');
                        $admission_date    = $this->input->post('admission_date');
                        $height            = $this->input->post('height');
                        $weight            = $this->input->post('weight');
                        $father_name       = $this->input->post('father_name');
                        $father_phone      = $this->input->post('father_phone');
                        $father_occupation = $this->input->post('father_occupation');
                        $mother_name       = $this->input->post('mother_name');
                        $mother_phone      = $this->input->post('mother_phone');
                        $mother_occupation = $this->input->post('mother_occupation');

                        if (isset($measurement_date)) {
                            $data['measurement_date'] = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('measure_date')));
                        }

                        if (isset($house)) {
                            $data['school_house_id'] = $this->input->post('house');
                        }

                        if (isset($blood_group)) {

                            $data['blood_group'] = $this->input->post('blood_group');
                        }

                        if (isset($lastname)) {

                            $data['lastname'] = $this->input->post('lastname');
                        }

                        if (isset($category_id)) {

                            $data['category_id'] = $this->input->post('category_id');
                        }

                        if (isset($religion)) {

                            $data['religion'] = $this->input->post('religion');
                        }

                        if (isset($mobileno)) {

                            $data['mobileno'] = $this->input->post('mobileno');
                        }

                        if (isset($email)) {

                            $data['email'] = $this->input->post('email');
                        }

                        if (isset($admission_date)) {

                            $data['admission_date'] = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('admission_date')));
                        }

                        if (isset($height)) {

                            $data['height'] = $this->input->post('height');
                        }

                        if (isset($weight)) {

                            $data['weight'] = $this->input->post('weight');
                        }

                        if (isset($father_name)) {

                            $data['father_name'] = $this->input->post('father_name');
                        }

                        if (isset($father_phone)) {

                            $data['father_phone'] = $this->input->post('father_phone');
                        }

                        if (isset($father_occupation)) {

                            $data['father_occupation'] = $this->input->post('father_occupation');
                        }

                        if (isset($mother_name)) {

                            $data['mother_name'] = $this->input->post('mother_name');
                        }

                        if (isset($mother_phone)) {

                            $data['mother_phone'] = $this->input->post('mother_phone');
                        }

                        if (isset($mother_occupation)) {

                            $data['mother_occupation'] = $this->input->post('mother_occupation');
                        }

                        $this->student_model->add($data);

                        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                            $fileInfo = pathinfo($_FILES["file"]["name"]);
                            $img_name = $student_id . '.' . $fileInfo['extension'];
                            move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/student_images/" . $img_name);
                            $data_img = array('id' => $student_id, 'image' => 'uploads/student_images/' . $img_name);
                            $this->student_model->add($data_img);
                        }

                        if (isset($_FILES["father_pic"]) && !empty($_FILES['father_pic']['name'])) {
                            $fileInfo = pathinfo($_FILES["father_pic"]["name"]);
                            $img_name = $student_id . "father" . '.' . $fileInfo['extension'];
                            move_uploaded_file($_FILES["father_pic"]["tmp_name"], "./uploads/student_images/" . $img_name);
                            $data_img = array('id' => $student_id, 'father_pic' => 'uploads/student_images/' . $img_name);
                            $this->student_model->add($data_img);
                        }

                        if (isset($_FILES["mother_pic"]) && !empty($_FILES['mother_pic']['name'])) {
                            $fileInfo = pathinfo($_FILES["mother_pic"]["name"]);
                            $img_name = $student_id . "mother" . '.' . $fileInfo['extension'];
                            move_uploaded_file($_FILES["mother_pic"]["tmp_name"], "./uploads/student_images/" . $img_name);
                            $data_img = array('id' => $student_id, 'mother_pic' => 'uploads/student_images/' . $img_name);
                            $this->student_model->add($data_img);
                        }

                        if (isset($_FILES["guardian_pic"]) && !empty($_FILES['guardian_pic']['name'])) {
                            $fileInfo = pathinfo($_FILES["guardian_pic"]["name"]);
                            $img_name = $student_id . "guardian" . '.' . $fileInfo['extension'];
                            move_uploaded_file($_FILES["guardian_pic"]["tmp_name"], "./uploads/student_images/" . $img_name);
                            $data_img = array('id' => $student_id, 'guardian_pic' => 'uploads/student_images/' . $img_name);
                            $this->student_model->add($data_img);
                        }

                        $array = array('status' => '1', 'msg' => 'Record Updated Successfully');
                    }
                    json_output(200, $array);
                }
            }
        }
    }

    public function edit_handle_upload($value, $field_name)
    {
        $image_validate = $this->config->item('image_validate');

        if (isset($_FILES[$field_name]) && !empty($_FILES[$field_name]['name'])) {

            $file_type         = $_FILES[$field_name]['type'];
            $file_size         = $_FILES[$field_name]["size"];
            $file_name         = $_FILES[$field_name]["name"];
            $allowed_extension = $image_validate['allowed_extension'];
            $ext               = pathinfo($file_name, PATHINFO_EXTENSION);
            $allowed_mime_type = $image_validate['allowed_mime_type'];
            if ($files = @getimagesize($_FILES[$field_name]['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('edit_handle_upload', 'File Type Not Allowed');
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('edit_handle_upload', 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $image_validate['upload_size']) {
                    $this->form_validation->set_message('edit_handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('edit_handle_upload', "File Type / Extension Error Uploading  Image");
                return false;
            }

            return true;
        }
        return true;
    }
    
    public function getMarks($question)
    {

        if ($question->select_option != null) {

            if ($question->question_type == "singlechoice" || $question->question_type == "true_false") {

                if ($question->correct == $question->select_option) {
                    return json_encode(array('get_marks' => $question->marks, 'scr_marks' => $question->marks));
                }

            } elseif ($question->question_type == "descriptive") {

                return json_encode(array('get_marks' => $question->marks, 'scr_marks' => $question->score_marks));

            } elseif ($question->question_type == "multichoice") {
                $cr_ans  = json_decode($question->correct);
                $sel_ans = json_decode($question->select_option);
                if ($this->array_equal($cr_ans, $sel_ans)) {
                    return json_encode(array('get_marks' => $question->marks, 'scr_marks' => $question->marks));
                }

            }
        }

        return json_encode(array('get_marks' => $question->marks, 'scr_marks' => 0));
    }
    
    public function array_equal($a, $b)
    {
        return (
            is_array($a) && is_array($b) && count($a) == count($b) && array_diff($a, $b) === array_diff($b, $a)
        );
    }

    public function uploadDocument()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $data = $this->input->POST();

                    $this->form_validation->set_data($data);
                    $this->form_validation->set_error_delimiters('', '');
                    $this->form_validation->set_rules('student_id', 'Student ID', 'required|trim');
                    $this->form_validation->set_rules('title', 'Title', 'required|trim');
                    $this->form_validation->set_rules('file', 'File', 'callback_handle_upload_file_compulsory');
                    if ($this->form_validation->run() == false) {

                        $form_error = array(

                            'student_id' => form_error('student_id'),
                            'title'      => form_error('title'),
                            'file'       => form_error('file'),
                        );
                        $array = array('status' => '0', 'error' => $form_error);
                    } else {
                        //==================
                        $student_id  = $this->input->post('student_id');
                        $title       = $this->input->post('title');
                        $upload_path = $this->config->item('upload_path') . "/student_documents/" . $student_id . "/";
                        if (!is_dir($upload_path) && !mkdir($upload_path)) {
                            die("Error creating folder $upload_path");
                        }

                        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                            $fileInfo  = pathinfo($_FILES["file"]["name"]);
                            $file_name = $_FILES['file']['name'];
                            $exp       = explode(' ', $file_name);
                            $imp       = implode('_', $exp);
                            $img_name  = $upload_path . basename($imp);
                            move_uploaded_file($_FILES["file"]["tmp_name"], $img_name);
                            $data_img = array('student_id' => $student_id, 'title' => $title, 'doc' => $imp);
                            $this->student_model->adddoc($data_img);
                        }

                        $array = array('status' => '1', 'msg' => 'Success');
                    }
                    json_output(200, $array);
                }
            }
        }
    }

    /**
     * This function is used to get online course list based on student class_id and section_id
     */
    public function courselist()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST       = json_decode(file_get_contents("php://input"), true);
                    $pay_method  = $this->paymentsetting_model->getActiveMethod();
                    $student_id  = $this->input->post('student_id');
                    $result      = $this->student_model->get($student_id);
                    $class_id    = $result->class_id;
                    $section_id  = $result->section_id;
                    $courselist  = $this->course_model->courselistforstudent($class_id, $section_id);
                    $course_list = array();
                    foreach ($courselist as $key => $courselist_value) {
                        $lesson_count = $this->course_model->totallessonbycourse($courselist_value['id']);

                        $courselist_value['total_lesson']     = count($lesson_count);
                        $courselist_value['total_hour_count'] = $this->course_model->counthours($courselist_value['id']);
                        $courselist_value['paidstatus']       = $this->course_model->paidstatus($courselist_value['id'], $student_id);
                        $courseprogresscount                  = $this->course_model->courseprogresscount($courselist_value['id'], $student_id);
                        $quiz_count                           = $this->course_model->totalquizbycourse($courselist_value['id']);

                        $total_quiz_lession = count($quiz_count) + count($lesson_count);
                        $course_progress    = 0;
                        if ($total_quiz_lession > 0) {
                            $course_progress = (count($courseprogresscount) / $total_quiz_lession) * 100;
                        }
                        
                        $courselist_value['course_progress'] = $course_progress;
                        $course_list[]                       = $courselist_value;

                        $course_list[$key]['image'] = '';
                        if (!empty($courselist_value['image'])) {
                            $course_list[$key]['image'] = $courselist_value['image'];
                        } else {
                            if ($courselist_value['gender'] == 'Female') {
                                $course_list[$key]['image'] = "default_female.jpg";
                            } else {
                                $course_list[$key]['image'] = "default_male.jpg";
                            }
                        }                        
                        
                        $courserating = $this->course_model->getcourserating($courselist_value['id']);
                        
                        $rating            = 0;
                        $averagerating     = 0;
                        $totalcourserating = 0;
            
                        if (!empty($courserating)) {
                            foreach ($courserating as $courserating_value) {
                                $rating = $rating + $courserating_value['rating'];
                            }
            
                            $averagerating = $rating / count($courserating);
                        }
            
                        $course_list[$key]['totalcourserating'] = count($courserating);
                        $course_list[$key]['courserating']      = $averagerating;
                        
                        
                        $course_list[$key]['section']   = $this->course_model->getSectionNameByCourseId($courselist_value['id']);

                    }
                    
                    $data['pay_method'] = empty($pay_method) ? 0 : 1;

                    $data['course_list'] = $course_list;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    /**
     * This function is used to get online course details
     */
    public function coursedetail()
    {
        $this->load->library('Aws3');
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST = json_decode(file_get_contents("php://input"), true);
                    $this->form_validation->set_data($_POST);
                    $course_id    = $this->input->post('course_id');
                    $student_id    = $this->input->post('student_id');
                    
                    $detail = $this->course_model->coursedetail($course_id);
                    $coursedetail['course_detail'] = $detail;
                    
                    
                    $student              = $this->course_model->getcourseratingbystudentid($course_id,$student_id);                    
                    // foreach($student as $key => $value){
                        // if($value['student_id'] != 0){
                            // $student[$key]['image'] = $student[$key]['image'];
                        // }
                    // }                    
                    $coursedetail['course_rating_review'] = $student;                  
                    
                    json_output($response['status'], $coursedetail);
                }
            }
        }
    }

    /**
     * This function is used to get online course section, lesson and quiz details
     */
    public function coursecurriculum()
    {
        $this->load->library('Aws3');
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST = json_decode(file_get_contents("php://input"), true);
                    $this->form_validation->set_data($_POST);
                    $course_id           = $this->input->post('course_id');
                    $student_id          = $this->input->post('student_id');
                    $sectionList         = $this->course_model->getsectionbycourse($course_id, $student_id);
                    $data['sectionList'] = $sectionList;
                    json_output($response['status'], $data);
                }
            }
        }
    }
    
    public function getCourseReviews()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $data                 = array();
                    $params               = json_decode(file_get_contents('php://input'), true);
                    $course_id           = $params['course_id'];
                    $student              = $this->course_model->getcourserating($course_id); 
                    
                    foreach($student as $key => $value){
                        if($value['student_id'] != 0){
                            $student[$key]['image'] = $student[$key]['image'];
                        }elseif($value['guest_id'] != 0){
                            $student[$key]['image'] = 'uploads/guest_images/'.$student[$key]['image'];
                        } 
                    }
                    
                    $data['result_array'] = $student; 
                    
                    json_output($response['status'], $data);
                }
            }
        }
    }
    
    /**
     * This function is used to get online course quiz question based on quiz_id and student_id
     */
    public function getquestionbyquizid()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST = json_decode(file_get_contents("php://input"), true);
                    $this->form_validation->set_data($_POST);
                    $quiz_id              = $this->input->post('quiz_id');
                    $student_id           = $this->input->post('student_id');
                    $questionlist         = $this->course_model->getquestionbyquizid($quiz_id, $student_id);
                    $data['questionlist'] = $questionlist;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    /**
     * This function is used to get online course quiz result based on quiz_id and student_id
     */
    public function quizresult()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST = json_decode(file_get_contents("php://input"), true);
                    $this->form_validation->set_data($_POST);
                    $quiz_id    = $this->input->post('quiz_id');
                    $answerlist = '';
                    $student_id = $this->input->post('student_id');
                    $result     = $this->course_model->quizresult($quiz_id, $student_id);
                    foreach ($result as $result_value) {
                        $answerlist = $this->course_model->quizstudentanswerlist($quiz_id, $student_id);
                    }
                    $data['result']     = $result;
                    $data['answerlist'] = $answerlist;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    /**
     * This function is used to insert online course quiz answer
     */
    public function saveanswer()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $student_id = $params['student_id'];
                    $quiz_id    = $params['quiz_id'];
                    $questionID = $params['question_id'];
                    $result = $this->course_model->getquizanswerexistornot($questionID, $quiz_id, $student_id);

                    $answer1 = $params['answer_1'];
                    $answer2 = $params['answer_2'];
                    $answer3 = $params['answer_3'];
                    $answer4 = $params['answer_4'];
                    $answer5 = $params['answer_5'];

                    $correctAnswer = array($answer1, $answer2, $answer3, $answer4, $answer5);
                    if (empty($result)) {

                        $addData = array(
                            'student_id'              => $student_id,
                            'course_quiz_id'          => $quiz_id,
                            'course_quiz_question_id' => $questionID,
                            'answer'                  => json_encode($correctAnswer),
                            'created_date'            => date('Y-m-d H:i:s'),
                        );

                    } else {

                        $addData = array(
                            'id'     => $result['id'],
                            'answer' => json_encode($correctAnswer),
                        );
                    }
                    $this->course_model->addanswer($addData);
                    $array = array('status' => '1', 'msg' => 'Success');
                    json_output(200, $array);
                }
            }
        }
    }

    /**
     * This function is used to submit online course quiz
     */
    public function submitquiz()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $student_id = $params['student_id'];
                    $quiz_id    = $params['quiz_id'];
                    $questionID = $params['question_id'];

                    $result = $this->course_model->getquizanswerexistornot($questionID, $quiz_id, $student_id);

                    $answer1 = $params['answer_1'];
                    $answer2 = $params['answer_2'];
                    $answer3 = $params['answer_3'];
                    $answer4 = $params['answer_4'];
                    $answer5 = $params['answer_5'];

                    $correctAnswer = array($answer1, $answer2, $answer3, $answer4, $answer5);
                    if (empty($result)) {

                        $addData = array(
                            'student_id'              => $student_id,
                            'course_quiz_id'          => $quiz_id,
                            'course_quiz_question_id' => $questionID,
                            'answer'                  => json_encode($correctAnswer),
                            'created_date'            => date('Y-m-d H:i:s'),
                        );

                    } else {

                        $addData = array(
                            'id'     => $result['id'],
                            'answer' => json_encode($correctAnswer),
                        );

                    }
                    $this->course_model->addanswer($addData);
                    $resultData = array(
                        'student_id'     => $student_id,
                        'course_quiz_id' => $quiz_id,
                        'status'         => 1,
                        'created_date'   => date('Y-m-d H:i:s'),
                    );

                    $lastid        = $this->course_model->addquizstatus($resultData);
                    $studentresult = $this->course_model->getresult($quiz_id, $student_id);
                    $answercount   = array();
                    $wrongcount    = array();
                    $not_attempted = array();
                    if (!empty($studentresult)) {
                        foreach ($studentresult as $studentresult_value) {
                            $result = '';
                            if (!empty($studentresult_value['answer'])) {
                                $submit_answer = json_decode($studentresult_value['answer']);

                                foreach ($submit_answer as $key => $submit_answer_value) {
                                    if (!empty($submit_answer_value)) {
                                        $key = $key + 1;
                                        if ($key == 1) {
                                            $result = "option_1,";
                                        }
                                        if ($key == 2) {
                                            $result = $result . "option_2,";
                                        }
                                        if ($key == 3) {
                                            $result = $result . "option_3,";
                                        }
                                        if ($key == 4) {
                                            $result = $result . "option_4,";
                                        }
                                        if ($key == 5) {
                                            $result = $result . "option_5";
                                        }
                                    }
                                }
                                $result = rtrim($result, ',');
                            }

                            if ($studentresult_value['correct_answer'] == $result) {
                                $answer_value = '1';
                                array_push($answercount, $answer_value);
                            } elseif (empty($result)) {
                                $attempted_value = '1';
                                array_push($not_attempted, $attempted_value);
                            }
                        }
                    }

                    $questioncount = $this->course_model->getquestionbyquizid($quiz_id, $student_id);

                    $questioncount = count($questioncount);
                    $answercount   = count($answercount);
                    $not_attempted = count($not_attempted);
                    $wrong_answer  = $questioncount - ($answercount + $not_attempted);
                    if (!empty($lastid)) {
                        $updateData = array(
                            'id'             => $lastid,
                            'total_question' => $questioncount,
                            'correct_answer' => $answercount,
                            'wrong_answer'   => $wrong_answer,
                            'not_answer'     => $not_attempted,
                        );

                        $this->course_model->addquizstatus($updateData);
                    }

                    $array = array('status' => '1', 'msg' => 'Success');

                    json_output(200, $array);
                }
            }
        }
    }

    /*
    This is used to delete previous record of student if he has given exam
     */
    public function resetquiz()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params         = json_decode(file_get_contents('php://input'), true);
                    $student_id     = $params['student_id'];
                    $course_quiz_id = $params['quiz_id'];

                    $this->course_model->removequizstatus($course_quiz_id, $student_id);
                    $this->course_model->removestudentquizanswer($course_quiz_id, $student_id);

                    $array = array('status' => '1', 'msg' => 'Success');
                    json_output(200, $array);
                }
            }
        }
    }

    /**
     * This function is used to mark quiz and lesson completed or not
     */
    public function markascomplete()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params           = json_decode(file_get_contents('php://input'), true);
                    $student_id       = $params['student_id'];
                    $section_id       = $params['section_id'];
                    $lesson_quiz_type = $params['lesson_quiz_type'];
                    $lesson_quiz_id   = $params['lesson_quiz_id'];
                    $result           = $this->course_model->coursebysection($section_id);
                    $data             = array(
                        "student_id"        => $student_id,
                        "lesson_quiz_id"    => $lesson_quiz_id,
                        "lesson_quiz_type"  => $lesson_quiz_type,
                        "course_section_id" => $section_id,
                        "course_id"         => $result['id'],
                    );

                    $is_completed = $this->course_model->getcourseprogress($result['id'], $student_id, $section_id, $lesson_quiz_type, $lesson_quiz_id);

                    if (!empty($is_completed)) {
                        $this->course_model->markascomplete($data, 0);
                    } else {
                        $this->course_model->markascomplete($data, 1);
                    }

                    $array = array('status' => '1', 'msg' => 'Success');
                    json_output(200, $array);
                }
            }
        }
    }

    /*
    This is used to get student course performance
     */
    public function courseperformance()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params                  = json_decode(file_get_contents('php://input'), true);
                    $course_id               = $params['course_id'];
                    $student_id              = $params['student_id'];
                    $data['result']          = $this->course_model->courseperformance($course_id, $student_id);
                    $lessoncount             = $this->course_model->totallessonbycourse($course_id);
                    $data['lessoncount']     = count($lessoncount);
                    $data['lessoncompleted'] = count($this->course_model->lessoncompleted($course_id, $student_id, 1));

                    $quizcount             = $this->course_model->totalquizbycourse($course_id);
                    $data['quizcount']     = count($quizcount);
                    $data['quizcompleted'] = count($this->course_model->lessoncompleted($course_id, $student_id, 2));

                    $lessonquizcount          = $data['lessoncount'] + $data['quizcount'];
                    $lessonquizcompletedcount = $data['lessoncompleted'] + $data['quizcompleted'];
                    if ($lessonquizcount > 0) {
                        $data['percentage'] = ($lessonquizcompletedcount / $lessonquizcount) * 100;
                    } else {
                        $data['percentage'] = 0;
                    }
                    json_output($response['status'], $data);

                }
            }
        }
    }    
    
    public function addCourseRatingandReview()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params      = json_decode(file_get_contents('php://input'), true);
                    $student_id  = $params['student_id'];
                    $course_id   = $params['course_id'];
                    $rating      = $params['rating'];
                    $review      = $params['review'];
                    $id      = $params['id'];

                    if (empty($result)) {
                        $addData = array(
                            'id' => $id,
                            'student_id' => $student_id,
                            'course_id' => $course_id,
                            'rating' => $rating,
                            'review' => $review,
                            'date' => date('Y-m-d'),
                        );
                    }
                    $this->course_model->addCourseRatingandReview($addData);
                    $array = array('status' => '1', 'msg' => 'Success');
                    json_output(200, $array);
                }
            }
        }
    }

    /**
     * This function is used to update student panel language
     */
    public function updatestudentlanguage()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params      = json_decode(file_get_contents('php://input'), true);
                    $student_id  = $params['student_id'];
                    $language_id = $params['language_id'];

                    if (empty($result)) {

                        $addData = array(
                            'user_id' => $student_id,
                            'lang_id' => $language_id,
                        );

                    }
                    $this->student_model->updatestudentlanguage($addData);
                    $array = array('status' => '1', 'msg' => 'Success');
                    json_output(200, $array);
                }
            }
        }
    }

    /**
     * This function is used to get student current language
     */
    public function getstudentcurrentlanguage()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $student_id = $params['student_id'];

                    $data['result'] = $this->user_model->getstudentcurrentlanguage($student_id);
                    json_output($response['status'], $data);

                }
            }
        }
    }

    public function adddailyassignment()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $data = $this->input->POST();

                    $this->form_validation->set_data($data);
                    $this->form_validation->set_error_delimiters('', '');
                    $this->form_validation->set_rules('subject_id', 'Subject', 'required|trim');
                    $this->form_validation->set_rules('title', 'Title', 'required|trim');

                    if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                        $this->form_validation->set_rules('file', 'File', 'callback_handle_upload_file');
                    }

                    if ($this->form_validation->run() == false) {

                        $sss = array(
                            'student_id' => form_error('student_id'),
                            'title'      => form_error('title'),
                            'file'       => form_error('file'),
                        );
                        $array = array('status' => '0', 'error' => $sss);
                    } else {
                        //==================

                        $student = $this->student_model->get($this->input->post('student_id'));

                        $upload_path = $this->config->item('upload_path') . "/homework/assignment/";

                        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                            $time     = md5($_FILES["file"]['name'] . microtime());
                            $fileInfo = pathinfo($_FILES["file"]["name"]);

                            $img_name = $this->customlib->uniqueFileName() . '.' . $fileInfo['extension'];

                            move_uploaded_file($_FILES["file"]["tmp_name"], $upload_path . $img_name);
                            $data_insert = array(
                                'title'              => $this->input->post('title'),
                                'description'        => $this->input->post('description'),
                                'student_session_id' => $student->student_session_id,
                                'attachment'         => $img_name,
                            );
                            $this->homework_model->adddailyassignment($data_insert);
                        }

                        $array = array('status' => '1', 'msg' => 'Success');
                    }
                    json_output(200, $array);
                }
            }
        }
    }

    public function getVideoTutorial()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {

                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {

                    $params     = json_decode(file_get_contents('php://input'), true);
                    $class_id   = $params['class_id'];
                    $section_id = $params['section_id'];

                    $data['result'] = $this->video_tutorial_model->getvideotutorial($class_id, $section_id);
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function getVisitors()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {

                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {

                    $params             = json_decode(file_get_contents('php://input'), true);
                    $student_id         = $params['student_id'];
                    $student            = $this->student_model->get($student_id);
                    $student_session_id = $student->student_session_id;
                    $result     = $this->visitors_model->visitorbystudentid($student_session_id);
                    foreach($result as $key => $value){
                        if($value['image'] == null){
                            $result[$key]['image'] = '';
                        }                        
                    }
                    $data['result']     = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    // -------- Daily Assignment -------------

    public function getdailyassignment()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params             = json_decode(file_get_contents('php://input'), true);
                    $student_id         = $params['student_id'];
                    $student            = $this->student_model->get($student_id);
                    $student_session_id = $student->student_session_id;
                    $dailyassignment    = $this->homework_model->getdailyassignment($student_id, $student_session_id);
                    
                    foreach($dailyassignment as $key => $value){
                        if($value['evaluation_date'] == null){
                            $dailyassignment[$key]['evaluation_date'] = '';
                        }if($value['attachment'] == null){
                            $dailyassignment[$key]['attachment'] = '';
                        }
                    }
 
                    $data["dailyassignment"] = $dailyassignment;
                    json_output($response['status'], $data);
                }
            }
        }
    }

    public function addeditdailyassignment()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {

                    $data = $this->input->POST();

                    $this->form_validation->set_rules('title', 'title', 'required|trim');
                    $this->form_validation->set_rules('subject', 'subject', 'required|trim');

                    if ($this->form_validation->run() == false) {

                        $sss = array(
                            'title'   => form_error('title'),
                            'subject' => form_error('subject'),
                        );
                        $array = array('status' => '0', 'error' => $sss);
                    } else {
                        //==================
                        $student_id         = $this->input->post('student_id');
                        $student            = $this->student_model->get($student_id);
                        $student_session_id = $student->student_session_id;

                        $data = array(
                            'id'                 => $this->input->post('id'),
                            'title'              => $this->input->post('title'),
                            'subject_group_subject_id'            => $this->input->post('subject'),
                            'description'        => $this->input->post('description'),
                            'date'               => date('Y-m-d'),
                            'student_session_id' => $student_session_id,

                        );

                        $upload_path = $this->config->item('upload_path') . "/homework/daily_assignment/";

                        $insert_id = $this->homework_model->adddailyassignment($data);

                        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                            $fileInfo = pathinfo($_FILES["file"]["name"]);
                            $img_name = $insert_id . '.' . $fileInfo['extension'];
                            move_uploaded_file($_FILES["file"]["tmp_name"], $upload_path . $img_name);
                            $data = array('id' => $insert_id, 'attachment' => $img_name);
                            $this->homework_model->adddailyassignment($data);
                        }

                        $array = array('status' => '1', 'msg' => 'Success');
                    }
                    json_output(200, $array);
                }
            }
        }
    }

    public function deletedailyassignment()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {

                $_POST = json_decode(file_get_contents("php://input"), true);
                $this->form_validation->set_data($_POST);
                $this->form_validation->set_error_delimiters('', '');
                $this->form_validation->set_rules('id', 'Id', 'required|trim');

                if ($this->form_validation->run() == false) {

                    $errors = array(
                        'id' => form_error('id'),
                    );
                    $array = array('status' => '0', 'error' => $errors);
                } else {
                    //==================

                    $id = $this->input->post('id');
                    $this->homework_model->deletedailyassignment($id);
                    $array = array('status' => '1', 'msg' => 'Success');
                }
                json_output(200, $array);
            }
        }
    }

    //--------- Transport Routes -----------------------

    public function gettransportroutes()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params      = json_decode(file_get_contents('php://input'), true);
                    $student_id  = $params['student_id'];
                    
                    $studentList = $this->student_model->get($student_id);
                    $data['pickup_point']        = $this->pickuppoint_model->getPickupPointByRouteID($studentList->route_id);     
                    
                    foreach($studentList as $key => $value){                        
                        if($studentList->$key == ''){
                            $studentList->$key = '';                    
                        } 
                    }
                    
                    $data['route'] = $studentList;

                    json_output($response['status'], $data);
                }
            }
        }
    }

    //--------- Timeline -----------------------

    public function getTimeline()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $student_id = $params['studentId'];
                    $timeline   = $this->timeline_model->getTimeline($student_id);
                    
                    foreach($timeline as $key => $value){                        
                        if($timeline[$key]['document'] == ''){
                            $timeline[$key]['document'] = '';                    
                        } 
                    }
                    
                    json_output($response['status'], $timeline);
                }
            }
        }
    }

    public function addedittimeline()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {

                    $params = json_decode(file_get_contents('php://input'), true);
 
                    $timeline = array(
                        
                        'title'         => $this->input->post('title'),
                        'description'   => $this->input->post('description'),
                        'timeline_date' => $this->input->post('timeline_date'),
                        'status'        => 'yes',
                        'date'          => date('Y-m-d'),
                        'student_id'    => $this->input->post('student_id'),

                    );
                    $id            = $this->input->post('id');
                    if(!empty($id )){
                        $timeline['id'] = $id ;
                    }
                    $insert_id = $this->timeline_model->addedittimeline($timeline);
                   
                    $upload_path = $this->config->item('upload_path') . "/student_timeline/";                   

                    if (isset($_FILES["timeline_doc"]) && !empty($_FILES['timeline_doc']['name'])) {
                        $fileInfo = pathinfo($_FILES["timeline_doc"]["name"]);
                        $img_name = $insert_id . '.' . $fileInfo['extension'];
                        move_uploaded_file($_FILES["timeline_doc"]["tmp_name"], $upload_path . $img_name);
                        $data = array('id' => $insert_id, 'document' => $img_name);
                        $this->timeline_model->addedittimeline($data);
                    }

                    $array = array('status' => '1', 'msg' => 'Success');

                    json_output(200, $array);
                }
            }
        }
    }

    public function deletetimeline()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {

                $_POST = json_decode(file_get_contents("php://input"), true);
                $this->form_validation->set_data($_POST);
                $this->form_validation->set_error_delimiters('', '');
                $this->form_validation->set_rules('id', 'Id', 'required|trim');

                if ($this->form_validation->run() == false) {

                    $errors = array(
                        'id' => form_error('id'),
                    );
                    $array = array('status' => '0', 'error' => $errors);
                } else {
                    //==================

                    $id = $this->input->post('id');
                    $this->timeline_model->deletetimeline($id);
                    $array = array('status' => '1', 'msg' => 'Success');
                }
                json_output(200, $array);
            }
        }
    }

    //-------------- Student Behaviour Addon -------------------
    
    public function getstudentbehaviour()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $student_id = $params['student_id'];
                    
                    $behaviour_settings = $this->assign_incident_model->behaviour_settings();
                    
                    if($behaviour_settings['comment_option'] == 'null'){
                        $behaviour_settings['comment_option'] = '';
                    }
                    
                    $data['behaviour_settings'] = $behaviour_settings;
                    
                    $total_points = $this->assign_incident_model->totalpoints($student_id);
                    
                    $data['behaviour_score'] = $total_points['totalpoints'];            
                    $assigned_incident = $this->assign_incident_model->studentbehaviour($student_id);  
                    
                    foreach($assigned_incident as $key => $value){             
                         
                        $CommentsCount  =     $this->assign_incident_model->getCommentsCount($value['id']);                   
                        $assigned_incident[$key]['comment_count'] = count($CommentsCount); 
                    }
                    
                    $data['assigned_incident'] = $assigned_incident;
                    
                    json_output($response['status'], $data);
                }
            }
        }
    }
	
	public function getincidentcomments()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $student_incident_id = $params['student_incident_id'];  
					$messagelist = $this->assign_incident_model->getincidentcomments($student_incident_id);
                    
                    foreach ($messagelist as $key => $value) { 
                        
                        if ($value['firstname'] == null) {
                            $messagelist[$key]['firstname'] = '';
                        }if ($value['middlename'] == null) {
                            $messagelist[$key]['middlename'] = '';
                        }if ($value['lastname'] == null) {
                            $messagelist[$key]['lastname'] = '';
                        }if ($value['admission_no'] == null) {
                            $messagelist[$key]['admission_no'] = '';
                        }if ($value['student_image'] == null) {
                            $messagelist[$key]['student_image'] = '';
                        }           
                         
                    }                 
                    
                    $data['messagelist'] = $messagelist;
                    json_output($response['status'], $data);
                }
            }
        }
    }
    
	public function addincidentcomments()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {

                    $params = json_decode(file_get_contents('php://input'), true);
					
					$student_id = $params['student_id'];  
					$student_incident_id = $params['student_incident_id'];  
					$type = $params['type'];  
					$comment = $params['comment'];				  
					
                    $timeline = array(
                        
                        'student_incident_id'	=> $student_incident_id, 
                        'comment'	=> $comment, 
                        'type'	=> $type,                        
                        'student_id'    => $student_id,
                        'created_date'    => date('Y-m-d H:i:s'),

                    );
                    
                    $this->assign_incident_model->addincidentcomments($timeline);  
                    $array = array('status' => '1', 'msg' => 'Success');

                    json_output(200, $array);
                }
            }
        }
    }
    
    public function deleteincidentcomments()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params   = json_decode(file_get_contents('php://input'), true);
                    $incident_comment_id = $params['incident_comment_id'];
                    $this->assign_incident_model->delete($incident_comment_id);

                    json_output($response['status'], array('result' => 'Success'));
                }
            }
        }
    }
    
    //-------------------------- Currency List ---------------
    public function get_currency_list()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);                     

                    $data['result'] = $this->setting_model->get_currency_list();
                    json_output($response['status'], $data);

                }
            }
        }
    }
    
    public function getstudentcurrentcurrency()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $student_id = $params['student_id'];

                    $result = $this->setting_model->get();                   
                   
                    $currencyarray = $this->user_model->getstudentcurrentcurrency($student_id);
                    
                    if($currencyarray[0]->currency_id != 0){
                        $result[0]['currency'] = $currencyarray[0]->currency_id; 
                    }else{
                       $result[0]['currency'] = $result[0]['currency']; 
                    }
                        
                    $data['result'] = $result;
                    
                    json_output($response['status'], $data);

                }
            }
        }
    }
    
    public function updatestudentcurrency()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params      = json_decode(file_get_contents('php://input'), true);
                    $student_id  = $params['student_id'];
                    $currency_id = $params['currency_id'];

                    if (empty($result)) {
                        $addData = array(
                            'user_id' => $student_id,
                            'currency_id' => $currency_id,
                        );
                    }
                    $this->student_model->updatestudentlanguage($addData);
                    $array = array('status' => '1', 'msg' => 'Success');
                    json_output(200, $array);
                }
            }
        }
    }

    public function lock_student_panel()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $student_id = $params['student_id'];
                    $studentList = $this->student_model->get($student_id);
 
                    $class_id       =    $studentList->class_id;
                    $session_id     =    $studentList->session_id;
                    $section_id     =    $studentList->section_id;
                    $student_session_id     =    $studentList->student_session_id;
                    $route_pickup_point_id     =    $studentList->route_pickup_point_id;
 
                    $sch_setting = $this->setting_model->getSchoolDetail();                    
                    $is_student_feature_lock    =   $sch_setting->is_student_feature_lock;
                    $lock_grace_period    =   $sch_setting->lock_grace_period;                    

                    $is_lock =  0;
                    if ($is_student_feature_lock) {
             
                        $date = date('Y-m-d', strtotime(date("Y-m-d")) - (86400 * $lock_grace_period));                 

                        $student_due_fee = $this->studentfeemaster_model->getDueFeesByStudent($student_session_id, $date);

                        if (!empty($student_due_fee)) {
                            foreach ($student_due_fee as $result_key => $result_value) {

                                if ($result_value->is_system == 0) {
                                    $student_due_fee[$result_key]->{'amount'} = $result_value->fee_amount;
                                }

                                $fee_paid     = 0;
                                $fee_discount = 0;
                                $fee_fine     = 0;

                                $feetype_balance = 0;
                                if (isJSON($result_value->amount_detail)) {
                                    $fee_deposits = json_decode(($result_value->amount_detail));
                                    foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                        $fee_paid     = $fee_paid + $fee_deposits_value->amount;
                                        $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                        $fee_fine     = $fee_fine + $fee_deposits_value->amount_fine;
                                    }
                                }
        
                                $feetype_balance = ($result_value->amount + $result_value->fine_amount) - ($fee_paid + $fee_fine + $fee_discount);

                                if ($feetype_balance > 0) {        
                                    $is_lock =  1;                                   
                                }
                            }
                        }

                        $transport_fees = $this->studentfeemaster_model->getDueTransportFeeByStudent($student_session_id, $route_pickup_point_id, $date);

                        if (!empty($transport_fees)) {
                            foreach ($transport_fees as $tran_fee_key => $tran_fee_value) {
                                $fee_paid         = 0;
                                $fee_discount     = 0;
                                $fee_fine         = 0;
                                $fees_fine_amount = 0;
                                $feetype_balance  = 0;
                                if (isJSON($tran_fee_value->amount_detail)) {
                                    $fee_deposits = json_decode(($tran_fee_value->amount_detail));
                                    foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                        $fee_paid     = $fee_paid + $fee_deposits_value->amount;
                                        $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                        $fee_fine     = $fee_fine + $fee_deposits_value->amount_fine;
                                    }
                                }

                                $fees_fine_amount = is_null($tran_fee_value->fine_percentage) ? $tran_fee_value->fine_amount : percentageAmount($tran_fee_value->fees, $tran_fee_value->fine_percentage);

                                $feetype_balance = ($tran_fee_value->fees + $fees_fine_amount) - ($fee_paid + $fee_discount + $fee_fine);

                                if ($feetype_balance > 0) {                         
                                    $is_lock =  1;
                                }
                            }
                        }
                    }                      
                     
                    $data['is_lock'] = $is_lock;
                    
                    json_output($response['status'], $data);                    

                }
            }
        }
    }  
     
    
    public function getStudentCurrency()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), true);
                    $student_id = $params['student_id'];

                    $result = $this->user_model->getStudentCurrency($student_id);                   
                   
                    $setting_result = $this->setting_model->get();

                        if ($result[0]->currency_id == 0) {
                            
                            $currency_symbol        = $setting_result[0]['currency_symbol'];                            
                            $currency_short_name    = $setting_result[0]['short_name'];
                            $base_price             = $setting_result[0]['base_price'];
                             
                        } else {                     
                            
                            $currency_symbol        = $result[0]->symbol;
                            $currency_short_name    = $result[0]->name;
                            $base_price                = $result[0]->base_price;
                            
                        } 
                        
                        $data['result'] = array(
                            
                            'name'         => $currency_short_name, 
                            'symbol'       => $currency_symbol, 
                            'base_price'   => $base_price, 
                            
                        );
                    
                    json_output($response['status'], $data);

                }
            }
        }
    }

    public function addofflinepayment()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                
                    $data = $this->input->POST();
                 $this->form_validation->set_data($data);
                 $this->form_validation->set_error_delimiters('', '');
                 $this->form_validation->set_rules('payment_type', 'Payment Type', 'required|trim');
                 $this->form_validation->set_rules('payment_date', 'Date', 'required|trim');
                 $this->form_validation->set_rules('student_session_id', 'Student Session ID', 'required|trim');
                 
                 $this->form_validation->set_rules('bank_account_transferred', 'Payment From', 'required|trim');
                 $this->form_validation->set_rules('amount', 'amount', 'required|trim');
                 $fee_type=$this->input->post('payment_type');
                 
                 if(isset($fee_type) && $fee_type == "fees"){
                 $this->form_validation->set_rules('fee_groups_feetype_id', 'Fee Group Fee Type ID', 'required|trim');
                 $this->form_validation->set_rules('student_fees_master_id', 'Student Fees Master ID', 'required|trim');
                 }elseif (isset($fee_type) && $fee_type == "transport_fees") {
                     
                 $this->form_validation->set_rules('student_transport_fee_id', 'Student Transport Fee ID', 'required|trim');

                 }
                    

                    if ($this->form_validation->run() == false) {

                        $sss = array(
                            'payment_type'         => form_error('payment_type'),
                            'payment_date'         => form_error('payment_date'),
                            'student_session_id'  => form_error('student_session_id'),
                            'fee_groups_feetype_id'    => form_error('fee_groups_feetype_id'),
                            'student_fees_master_id' => form_error('student_fees_master_id'),
                            'bank_account_transferred' => form_error('bank_account_transferred'),
                            'student_transport_fee_id' => form_error('student_transport_fee_id'),
                            'amount' => form_error('amount'),
                        );
                        $array = array('status' => '0', 'error' => $sss);
                    } else {
                        //==================
                             $data = array(
                            'payment_date'         => $this->input->post('payment_date'),
                            'student_session_id'  => $this->input->post('student_session_id'),
                            'bank_account_transferred'     => $this->input->post('bank_account_transferred'),
                            'amount'     => $this->input->post('amount'),
                            'reference'     => $this->input->post('reference'),
                            'bank_from'     => 'Offline',
                            'submit_date'     =>date('Y-m-d H:i:s'),
                        );

                      if($this->input->post('payment_type') == "fees"){
                            $data['fee_groups_feetype_id']    = $this->input->post('fee_groups_feetype_id');
                            $data['student_fees_master_id'] = $this->input->post('student_fees_master_id');
                      }elseif ($this->input->post('payment_type') == "transport_fees") {
                          # code...
                      $data['student_transport_fee_id'] = $this->input->post('student_transport_fee_id');
                      }
                   

                        $upload_path = $this->config->item('upload_path') . "/offline_payments/";

                        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                              $name        = $_FILES["file"]["name"];
                              $file_name   = time() . "-" . uniqid(rand()) . "!" . $name;
                            move_uploaded_file($_FILES["file"]["tmp_name"], $upload_path . $file_name);
                            $data['attachment'] =  $file_name;
                           
                        }

                        $this->offlinePayment_model->add($data);
                        $array = array('status' => '1', 'msg' => 'Success');
                    }
                    json_output(200, $array);   

                
                }
            }
        }
    }    
    
    public function getELearningModuleStatus()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    $user                = $this->input->post('user');
                    
                    $modulearray = array('homework','daily_assignment','lesson_plan','online_examination','download_center','online_course','live_classes','gmeet_live_classes');
                    
                    foreach ($modulearray as $key => $modulearray_value) {
                        
                        if($modulearray_value != 'daily_assignment'){
                            $result =  $this->module_model->getModuleStatusByCategory($user,$modulearray_value);
                            if($result['short_code'] == $modulearray_value){
                                $result_array[$key] = $result;
                            }else{                            
                                $result_array[$key]['name'] = $modulearray_value;
                                $result_array[$key]['short_code'] = $modulearray_value;
                                $result_array[$key]['status'] = 0;                            
                            } 
                        }else{
                            $result =  $this->module_model->getModuleStatusByCategory($user,'homework');
                            
                            $result_array[$key]['name'] = 'Daily Assignment';
                            $result_array[$key]['short_code'] = 'daily_assignment';
                            $result_array[$key]['status'] = $result['status'];    
                        }            
                                               
                    }
                     
                    $resp['module_list'] = $result_array;
                    
                    json_output($response['status'], $resp);
                }
            }
        }
    }
    
    public function getAcademicsModuleStatus()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    $user                = $this->input->post('user');
                    
                    $modulearray = array('class_timetable','syllabus_status','attendance','examinations','student_timeline','mydocuments','behaviour_records');                   
                    
                    $setting = $this->setting_model->getSetting();                   
                    
                    foreach ($modulearray as $key => $modulearray_value) {
                        $result =  $this->module_model->getModuleStatusByCategory($user,$modulearray_value);              
                        
                        if($result['short_code'] == $modulearray_value){
                            $result_array[$key] = $result;
                        }else{                        
                            
                            if($modulearray_value == 'mydocuments'){
                                $result_array[$key]['name'] = "My Documents";
                                $result_array[$key]['short_code'] = "mydocuments";
                                $result_array[$key]['status'] = $setting->upload_documents;
                            }else{
                                $result_array[$key]['name'] = $modulearray_value;
                                $result_array[$key]['short_code'] = $modulearray_value;
                                $result_array[$key]['status'] = 0;
                            }                          
                            
                        }                        
                    }
                     
                    $resp['module_list'] = $result_array;
                    
                    json_output($response['status'], $resp);
                }
            }
        }
    }
    
    public function getCommunicateModuleStatus()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    $user                = $this->input->post('user');
                    
                    $modulearray = array('notice_board');
                    
                    foreach ($modulearray as $key => $modulearray_value) {
                        $result =  $this->module_model->getModuleStatusByCategory($user,$modulearray_value);              
                        
                        if($result['short_code'] == $modulearray_value){
                            $result_array[$key] = $result;
                        }else{
                            $result_array[$key]['name'] = $modulearray_value;
                            $result_array[$key]['short_code'] = $modulearray_value;
                            $result_array[$key]['status'] = 0;
                        }                        
                    }
                     
                    $resp['module_list'] = $result_array;
                    
                    json_output($response['status'], $resp);
                }
            }
        }
    }
    
    public function getOthersModuleStatus()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    $user                = $this->input->post('user');
                    
                    $modulearray = array('fees','apply_leave','visitor_book','transport_routes','hostel_rooms','calendar_to_do_list','library','teachers_rating');
                    
                    foreach ($modulearray as $key => $modulearray_value) {
                        $result =  $this->module_model->getModuleStatusByCategory($user,$modulearray_value);              
                        
                        if($result['short_code'] == $modulearray_value){
                            $result_array[$key] = $result;
                        }else{
                            $result_array[$key]['name'] = $modulearray_value;
                            $result_array[$key]['short_code'] = $modulearray_value;
                            $result_array[$key]['status'] = 0;
                        }                        
                    }
                     
                    $resp['module_list'] = $result_array;
                    
                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function getOfflineBankPayments()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $data              = array();
                    $params            = json_decode(file_get_contents('php://input'), true);
                    $student_id        = $params['student_id'];
                    $student           = $this->student_model->get($student_id);                    
                    
                    $result            = $this->offlinePayment_model->getPaymentlistByUser($student->student_session_id);
                    
                    foreach ($result as $key => $value) { 
                        
                        if ($value->month == null) {
                            $result[$key]->month = '';
                        }if ($value->transport_feemaster_due_date == null) {
                            $result[$key]->transport_feemaster_due_date = '';
                        }if ($value->pickup_point == null) {
                            $result[$key]->pickup_point = '';
                        }if ($value->route_title == null) {
                            $result[$key]->route_title = '';           
                        }if ($value->type == null) {
                            $result[$key]->type = '';           
                        }if ($value->code == null) {
                            $result[$key]->code = '';           
                        }if ($value->fee_group_name == null) {
                            $result[$key]->fee_group_name = '';           
                        }if ($value->reply == null) {
                            $result[$key]->reply = '';           
                        }if ($value->attachment == null) {
                            $result[$key]->attachment = '';           
                        }if ($value->invoice_id == null) {
                            $result[$key]->invoice_id = '';           
                        }
                        
                    } 
                    
                    $data['result_array'] = $result;
                    json_output($response['status'], $data);
                }
            }
        }
    }    
    
    public function getMaintenanceModeStatus()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    
                    $setting = $this->setting_model->getSetting();                     
                    $resp['maintenance_mode'] = $setting->maintenance_mode;
                    
                    json_output($response['status'], $resp);
                }
            }
        }
    }    
    
    public function getStudentTimelineStatus()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    
                    $setting = $this->setting_model->getSetting();   
                   
                    $resp['student_timeline'] = $setting->student_timeline;
                    
                    json_output($response['status'], $resp);
                }
            }
        }
    }
    
    public function getOfflineBankPaymentStatus()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST               = json_decode(file_get_contents("php://input"), true);
                    
                    $setting = $this->setting_model->getSetting();   
                   
                    $resp['is_offline_fee_payment'] = $setting->is_offline_fee_payment;
                    
                    json_output($response['status'], $resp);
                }
            }
        }
    }
    
    public function getProcessingfees()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $_POST            = json_decode(file_get_contents("php://input"), true);
                    $student_id       = $_POST['student_id'];                    
                    $student          = $this->student_model->get($student_id);
                    
                    $student_fee  = $this->studentfeemaster_model->getStudentProcessingFees($student->student_session_id);
                    
                     
                    $fee_paid     = 0;
                    $fee_discount = 0;
                    $fee_fine     =  0 ;
                    $total_balance_amount = 0;
                    
                    foreach($student_fee as $value){ 
                        foreach($value->fees as $result){ 
                            if (isJSON($result->amount_detail)) {
                                
                                $fee_deposits = json_decode(($result->amount_detail));                            
                                
                                $fee_paid     = $fee_paid + $fee_deposits->amount;
                                $fee_discount = $fee_discount + $fee_deposits->amount_discount;
                                $fee_fine     = $fee_fine + $fee_deposits->amount_fine;                                
                                $feetype_balance        = $fee_deposits->amount - ($fee_paid + $fee_discount);
                                $total_balance_amount   = $total_balance_amount + $feetype_balance; 
                            
                            }
                        }   
                    }
                
                    $data['student_fee']     = $student_fee;    
                    
                                       
                    $grand_fee = array('fee_paid' => ($fee_paid), 'fee_discount' => ($fee_discount), 'fee_fine' => ($fee_fine), 'total_paid' => ($fee_paid+$fee_fine));
                    
                    $data['grand_fee'] = $grand_fee;
                    
                    json_output($response['status'], $data);
                }
            }
        }
    }   
   
    
    public function checkStudentStatus()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $params   = json_decode(file_get_contents('php://input'), true); 
                    
                    $id = $params['id']; 
                    $user_type = $params['user_type']; 
                    
                    $response = $this->user_model->checkStudentStatus($id, $user_type);               
                    $data['response'] = $response;
                    
                    json_output(200, $data);
                }
            }
        }
    }
    
    


}
