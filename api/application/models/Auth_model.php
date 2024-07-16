<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model
{

    public $client_service               = "smartschool";
    public $auth_key                     = "schoolAdmin@";
    public $security_authentication_flag = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('user_model', 'setting_model', 'student_model'));
    }

    public function check_auth_client()
    {
        $client_service = $this->input->get_request_header('Client-Service', true);
        $auth_key       = $this->input->get_request_header('Auth-Key', true);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(200, array('status' => 0, 'message' => 'Unauthorized.'));
        }
    }

    public function login($username, $password, $app_key)
    {
        $resultdata    = $this->setting_model->getSetting();
        
        if($resultdata->student_panel_login){
            $q = $this->checkLogin($username, $password);
        }else{
            return array('status' => 0, 'message' => 'Your account is suspended'); 
        }
        
        if (empty($q)) {
            return array('status' => 0, 'message' => 'Invalid Username or Password');
        } else {

            if ($q->is_active == "yes") {
                if ($q->role == "student") {

                    $result = $this->user_model->read_user_information($q->id);

                    if ($result != false) {

                        $setting_result = $this->setting_model->get();

                        if ($result->currency_id == 0) {
                            $currency_symbol    = $setting_result[0]['currency_symbol'];
                            $currency           = $setting_result[0]['currency'];
                            $currency_short_name           = $setting_result[0]['short_name'];
                             
                        } else {
                             
                            $currencyarray = $this->user_model->getstudentcurrentcurrency($result->user_id);
                            $currency               = $currencyarray[0]->id;
                            $currency_symbol        = $currencyarray[0]->symbol;
                            $currency_short_name        = $currencyarray[0]->short_name;
                        }
                        
                        if ($result->lang_id == 0) {
                            $lang_id    = $setting_result[0]['lang_id'];
                            $language   = $setting_result[0]['language'];
                            $short_code = $setting_result[0]['short_code'];
                        } else {
                            $lang_id    = $result->lang_id;
                            $curentlang = $this->user_model->getstudentcurrentlanguage($result->user_id);
                            $language   = $curentlang[0]->language;
                            $short_code = $curentlang[0]->short_code;
                        }

                        if ($result->role == "student") {

                            $last_login = date('Y-m-d H:i:s');
                            $token      = $this->getToken();
                            $expired_at = date("Y-m-d H:i:s", strtotime('+8760 hours'));
                            $this->db->trans_start();
                            $this->db->insert('users_authentication', array('users_id' => $q->id, 'token' => $token, 'expired_at' => $expired_at));

                            $updateData = array(
                                'app_key' => $app_key,
                            );

                            $this->db->where('id', $result->user_id);
                            $this->db->update('students', $updateData);
                            $fullname = getFullName($result->firstname, $result->middlename, $result->lastname, $setting_result[0]['middlename'], $setting_result[0]['lastname']);

                            if (empty($fullname)) {$fullname = '';}

                            $session_data = array(
                                'id'              => $result->id,
                                'student_id'      => $result->user_id,
                                'admission_no'    => $result->admission_no,
                                'role'            => $result->role,
                                'mobileno'        => $result->mobileno,
                                'email'           => $result->email,
                                'username'        => $fullname,
                                'class'           => $result->class,
                                'section'         => $result->section,
                                'date_format'     => $setting_result[0]['date_format'],
                                'currency_symbol' => $currency_symbol,
                                'currency_short_name'      => $currency_short_name,
                                'currency_id'     => $currency,                                
                                'timezone'        => $setting_result[0]['timezone'],
                                'sch_name'        => $setting_result[0]['name'],
                                'language'        => array('lang_id' => $lang_id, 'language' => $language, 'short_code' => $short_code),
                                'is_rtl'          => $setting_result[0]['is_rtl'],
                                'theme'           => $setting_result[0]['theme'],
                                'image'           => $result->image,
                                'student_session_id'           => $result->student_session_id,
                                'start_week'      => $setting_result[0]['start_week'],
                                'superadmin_restriction'      => $setting_result[0]['superadmin_restriction'],
                            );
                            $this->session->set_userdata('student', $session_data);
                            if ($this->db->trans_status() === false) {
                                $this->db->trans_rollback();

                                return array('status' => 0, 'message' => 'Internal server error.');
                            } else {
                                $this->db->trans_commit();
                                return array('status' => 1, 'message' => 'Successfully login.', 'id' => $q->id, 'token' => $token, 'role' => $q->role, 'record' => $session_data);
                            }
                        }
                    } else {
                        return array('status' => 0, 'message' => 'Your account is suspended');
                    }
                } else if ($q->role == "parent") {
                    $login_post = array(
                        'username' => $username,
                        'password' => $password,
                    );                  
                    
                        $resultdata    = $this->setting_model->getSetting();                    
         
                        if ($resultdata->parent_panel_login) {
                            $result = $this->user_model->checkLoginParent($login_post);
                        } else {
                            $result = false;
                        }                   
                    
                    if ($result != false) {
                        
                        
                    $curentlang = $this->user_model->getstudentcurrentlanguage($result->id);
                    $setting_result = $this->setting_model->get();

                    if (empty($curentlang)) {
                        $lang_id    = $setting_result[0]['lang_id'];
                        $language   = $setting_result[0]['language'];
                        $short_code = $setting_result[0]['short_code'];
                    } else {
                        $lang_id    = $curentlang[0]->lang_id;
                        $language   = $curentlang[0]->language;
                        $short_code = $curentlang[0]->short_code;
                    }

                    if ($result->role == "parent") {                        

                        $last_login = date('Y-m-d H:i:s');
                        $token      = $this->getToken();
                        $expired_at = date("Y-m-d H:i:s", strtotime('+8760 hours'));

                        $this->db->insert('users_authentication', array('users_id' => $q->id, 'token' => $token, 'expired_at' => $expired_at));

                        if ($result->guardian_relation == "Father") {
                            $image = $result->father_pic;
                        } else if ($result->guardian_relation == "Mother") {
                            $image = $result->mother_pic;
                        } else {
                            $image = $result->guardian_pic;
                        }

                        $guardian_name = $result->guardian_name;
                        if (empty($guardian_name)) {$guardian_name = '';}

                        $session_data = array(
                            'id'              => $result->id,
                            'role'            => $result->role,
                            'username'        => $guardian_name,
                            'student_session_id'           => $result->student_session_id,
                            'date_format'     => $setting_result[0]['date_format'],
                            'timezone'        => $setting_result[0]['timezone'],
                            'sch_name'        => $setting_result[0]['name'],
                            'currency_symbol' => $setting_result[0]['currency_symbol'],
                            'currency_short_name' => $setting_result[0]['currency_short_name'],                        
                            'language'        => array('lang_id' => $lang_id, 'language' => $language, 'short_code' => $short_code),
                            'is_rtl'          => $setting_result[0]['is_rtl'],
                            'theme'           => $setting_result[0]['theme'],
                            'image'           => $image,
                            'start_week'      => $setting_result[0]['start_week'],
                            'superadmin_restriction'      => $setting_result[0]['superadmin_restriction'],
                        );

                        $user_id        = ($result->id);
                        $students_array = $this->student_model->read_siblings_students($user_id);
                        $child_student  = array();
                        $update_student = array();
                        foreach ($students_array as $std_key => $std_val) {
                            $child = array(
                                'student_id' => $std_val->id,
                                'class'      => $std_val->class,
                                'section'    => $std_val->section,
                                'class_id'   => $std_val->class_id,
                                'section_id' => $std_val->section_id,
                                'name'       => $std_val->firstname . " " . $std_val->lastname,
                                'image'      => $std_val->image,
                            );
                            $child_student[] = $child;
                            $stds            = array(
                                'id'             => $std_val->id,
                                'parent_app_key' => $app_key,
                            );
                            $update_student[] = $stds;
                        }
                        if (!empty($update_student)) {
                            $this->db->update_batch('students', $update_student, 'id');
                        }

                        $session_data['parent_childs'] = $child_student;
                        $this->session->set_userdata('student', $session_data);

                        return array('status' => 1, 'message' => 'Successfully login.', 'id' => $q->id, 'token' => $token, 'role' => $q->role, 'record' => $session_data);
                        
                    }else{
                        return array('status' => 0, 'message' => 'Invalid Username or Password');
                    }
                    
                    }else{
                        return array('status' => 0, 'message' => 'Your account is suspended');
                    }                    
                    
                }
            } else {
                return array('status' => '0', 'message' => 'Your account is disabled please contact to administrator');
            }
        }
    }

    public function checkLogin($username, $password)
    {
        $resultdata    = $this->setting_model->get();
        $student_login = json_decode($resultdata[0]['student_login']);
        $parent_login  = json_decode($resultdata[0]['parent_login']);
        
        $this->db->select('users.id as id, username, password,role,users.is_active as is_active,lang_id');
        $this->db->from('users');
        $this->db->join('students', 'students.id = users.user_id');
        $this->db->where('password', $password);
        
        $this->db->group_start();        
        $this->db->where('username', $username); 
        
        if(!empty($student_login)){
            if (in_array("admission_no", $student_login)) {
                $this->db->or_where('students.admission_no', $username);
            }
            if (in_array("mobile_number", $student_login)) {
                $this->db->or_where('students.mobileno', $username);
            }
            if (in_array("email", $student_login)) {
                $this->db->or_where('students.email', $username);
            }
        }
        
        $this->db->group_end();
        
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row();
        } else {

            $this->db->select('users.id as id, username, password,role,users.is_active as is_active,lang_id');
            $this->db->from('users');
            $this->db->join('students', 'students.parent_id = users.id');
            $this->db->where('password', $password);                       
            
            $this->db->group_start();            
            $this->db->where('username', $username); 
            
            if(!empty($parent_login)){
                if (in_array("mobile_number", $parent_login)) {
                    $this->db->or_where('students.guardian_phone', $username);
                }
                if (in_array("email", $parent_login)) {
                    $this->db->or_where('students.guardian_email', $username);
                }
            }
            
            $this->db->group_end();
            
            $this->db->limit(1);
            $query = $this->db->get();
            if ($query->num_rows() == 1) {
                return $query->row();
            } else {
                return false;
            }
        }
    }

    public function getToken($randomIdLength = 10)
    {
        $token = '';
        do {
            $bytes = rand(1, $randomIdLength);
            $token .= str_replace(
                ['.', '/', '='], '', base64_encode($bytes)
            );
        } while (strlen($token) < $randomIdLength);
        return $token;
    }

    public function logout($deviceToken)
    {
        $users_id = $this->input->get_request_header('User-ID', true);
        $token    = $this->input->get_request_header('Authorization', true);
        $this->session->unset_userdata('student');
        $this->session->sess_destroy();
        $this->db->where('app_key', $deviceToken)->update('students', array('app_key' => null));
        $this->db->where('users_id', $users_id)->where('token', $token)->delete('users_authentication');
        return array('status' => 200, 'message' => 'Successfully logout.');
    }

    public function auth()
    {
        if ($this->security_authentication_flag) {
            $users_id = $this->input->get_request_header('User-ID', true);
            $token    = $this->input->get_request_header('Authorization', true);
            $q        = $this->db->select('expired_at')->from('users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
            if ($q == "") {
                return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
            } else {
                if ($q->expired_at < date('Y-m-d H:i:s')) {
                    return json_output(401, array('status' => 401, 'message' => 'Your session has been expired.'));
                } else {
                    $updated_at = date('Y-m-d H:i:s');
                    $expired_at = date("Y-m-d H:i:s", strtotime('+8760 hours'));
                    $this->db->where('users_id', $users_id)->where('token', $token)->update('users_authentication', array('expired_at' => $expired_at, 'updated_at' => $updated_at));
                    return array('status' => 200, 'message' => 'Authorized.');
                }
            }
        } else {
            return array('status' => 200, 'message' => 'Authorized.');
        }
    }

}
