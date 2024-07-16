<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Route extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();       
        $this->load->library('mailer');
        $this->load->library(array('customlib', 'enc_lib'));
        $this->load->model(array('auth_model', 'route_model', 'student_model', 'setting_model', 'attendencetype_model', 'studentfeemaster_model', 'feediscount_model', 'teachersubject_model', 'timetable_model', 'user_model', 'examschedule_model', 'grade_model'));
    }

    public function index()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $resp = $this->route_model->get();
                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function detail($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET' || $this->uri->segment(3) == '' || is_numeric($this->uri->segment(3)) == false) {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $resp = $this->route_model->get($id);
                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function create()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response   = $this->auth_model->auth();
                $respStatus = $response['status'];
                if ($response['status'] == 200) {
                    //==================================
                    $this->load->library('form_validation');
                    $_POST = json_decode(file_get_contents("php://input"), true);

                    $this->form_validation->set_rules('route_title', 'route_title', 'required');
                    $this->form_validation->set_rules('note', 'note', 'required');
                    if ($this->form_validation->run() == false) {
                        $errors = validation_errors();
                    }

                    if (isset($errors)) {
                        $respStatus = 400;
                        $resp       = array('status' => 400, 'message' => $errors);
                    } else {

                        $resp = $this->route_model->add($_POST);
                    }
                    //===================================

                    json_output($respStatus, $resp);
                }
            }
        }
    }

    public function update($id)
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'PUT' || $this->uri->segment(3) == '' || is_numeric($this->uri->segment(3)) == false) {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response   = $this->auth_model->auth();
                $respStatus = $response['status'];
                if ($response['status'] == 200) {

                    //==================================
                    $this->load->library('form_validation');
                    $_POST = json_decode(file_get_contents("php://input"), true);
                    $this->form_validation->set_data($_POST);
                    $this->form_validation->set_rules('route_title', 'route_title', 'required');
                    $this->form_validation->set_rules('note', 'note', 'required');
                    if ($this->form_validation->run() == false) {
                        $errors = validation_errors();
                    }

                    if (isset($errors)) {
                        $respStatus = 400;
                        $resp       = array('status' => 400, 'message' => $errors);
                    } else {

                        $resp = $this->route_model->update($id, $_POST);
                    }
                    //===================================

                    json_output($respStatus, $resp);
                }
            }
        }
    }

    public function delete($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'DELETE' || $this->uri->segment(3) == '' || is_numeric($this->uri->segment(3)) == false) {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->auth_model->auth();
                if ($response['status'] == 200) {
                    $resp = $this->route_model->delete($id);
                    json_output($response['status'], $resp);
                }
            }
        }
    }

    public function marklist()
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

                    $student      = $this->student_model->get($student_id);
                    $class_id     = $student['class_id'];
                    $section_id   = $student['section_id'];
                    $gradeList    = $this->grade_model->get();
                    $examList     = $this->examschedule_model->getExamByClassandSection($student['class_id'], $student['section_id']);
                    $examSchedule = array();
                    if (!empty($examList)) {
                        $new_array              = array();
                        $examSchedule['status'] = "yes";
                        foreach ($examList as $ex_key => $ex_value) {
                            $array         = array();
                            $x             = array();
                            $exam_id       = $ex_value['exam_id'];
                            $exam_subjects = $this->examschedule_model->getresultByStudentandExam($exam_id, $student['id']);
                            foreach ($exam_subjects as $key => $value) {
                                $exam_array                     = array();
                                $exam_array['exam_schedule_id'] = $value['exam_schedule_id'];
                                $exam_array['exam_id']          = $value['exam_id'];
                                $exam_array['full_marks']       = $value['full_marks'];
                                $exam_array['passing_marks']    = $value['passing_marks'];
                                $exam_array['exam_name']        = $value['name'];
                                $exam_array['exam_type']        = $value['type'];
                                $exam_array['attendence']       = $value['attendence'];
                                $exam_array['get_marks']        = $value['get_marks'];
                                $x[]                            = $exam_array;
                            }
                            $array['exam_name']   = $ex_value['name'];
                            $array['exam_result'] = $x;
                            $new_array[]          = $array;
                        }
                        $examSchedule = $new_array;
                    }

                    json_output($response['status'], array('student' => $student, 'gradeList' => $gradeList, 'examSchedule' => $examSchedule));
                }
            }
        }
    }

}
