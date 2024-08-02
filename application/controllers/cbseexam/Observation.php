<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Observation extends MY_Addon_CBSEController
{

    public function __construct()
    {
        parent::__construct();
       
        $this->current_session    = $this->setting_model->getCurrentSession();
        $this->sch_setting_detail = $this->setting_model->getSetting();

    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_observation', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/observation');

        $data['result']    = $this->cbseexam_observation_model->getobservationlist();
        $data['parameter'] = $this->cbseexam_observation_parameter_model->get();

        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/observation/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function studentovservation($id)
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_observation', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/observation');
        $result                 = $this->cbse_observation_term_model->get($id);
        $exams_by_grade         = $this->cbseexam_exam_model->getExamByGrade($result->cbse_term_id);
        $data['exams_by_grade'] = $exams_by_grade;
        $data['result']         = $result;
        $data['id']             = $id;
        $students               = [];
        $data['sch_setting']    = $this->sch_setting_detail;

        $this->form_validation->set_rules('exam_id', $this->lang->line('exam'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {
            $cbse_observation_term_id = $this->input->post('cbse_observation_term_id');
            $exam_id                  = $this->input->post('exam_id');
            $class_id                 = $this->input->post('class_id');
            $section_id               = $this->input->post('section_id');
            $cbse_parameter_students  = $this->cbse_observation_term_model->getStudentSubparameter($exam_id, $cbse_observation_term_id, $class_id, $section_id);
            foreach ($cbse_parameter_students as $student_key => $student_value) {

                if (array_key_exists($student_value->student_session_id, $students)) {
                    $new_subparameter = [
                        'cbse_ovservation_term_id'                      => $student_value->id,
                        'cbse_observation_subparameter_id'              => $student_value->cbse_observation_subparameter_id,
                        'cbse_observation_parameter_name'               => $student_value->cbse_observation_parameter_name,
                        'cbse_observation_term_student_subparameter_id' => $student_value->cbse_observation_term_student_subparameter_id,
                    ];
                    $students[$student_value->student_session_id]['observation_subparameters'][] = $new_subparameter;

                } else {

                    $students[$student_value->student_session_id] = [
                        'student_session_id'        => $student_value->student_session_id,
                        'student_id'                => $student_value->student_id,
                        'firstname'                 => $student_value->firstname,
                        'middlename'                => $student_value->middlename,
                        'lastname'                  => $student_value->lastname,
                        'mobileno'                  => $student_value->mobileno,
                        'email'                     => $student_value->email,
                        'religion'                  => $student_value->religion,
                        'guardian_name'             => $student_value->guardian_name,
                        'guardian_phone'            => $student_value->guardian_phone,
                        'dob'                       => $student_value->dob,
                        'admission_no'              => $student_value->admission_no,
                        'father_name'               => $student_value->father_name,
                        'mother_name'               => $student_value->mother_name,
                        'roll_no'                   => $student_value->roll_no,
                        'student_image'             => $student_value->image,
                        'gender'                    => $student_value->gender,
                        'observation_subparameters' => [[
                            'cbse_ovservation_term_id'                      => $student_value->id,
                            'cbse_observation_subparameter_id'              => $student_value->cbse_observation_subparameter_id,
                            'cbse_observation_parameter_name'               => $student_value->cbse_observation_parameter_name,
                            'cbse_observation_term_student_subparameter_id' => $student_value->cbse_observation_term_student_subparameter_id,
                        ],
                        ],
                    ];

                }
            }
            $data['students'] = $students;

        }

        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/observation/studentovservation', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add_observation_term_marks()
    {

        $row                         = $this->input->post('row[]');
        $cbse_observation_parameters = $this->input->post('cbse_observation_parameters[]');
        $this->form_validation->set_rules('cbse_observation_term_id', $this->lang->line('cbse_observation_term_id'), 'trim|required|xss_clean');
        if (!isset($row)) {

            $this->form_validation->set_rules('row', $this->lang->line('no_paramenter_added'), 'trim|required|xss_clean');            

        } else {

            foreach ($row as $row_key => $row_value) {
                foreach ($cbse_observation_parameters as $parameter_key => $parameter_value) {

                    $student_session = $this->input->post('student_session_' . $row_value);
                    if ($this->input->post('param_value_' . $student_session . '_' . $parameter_value) == "") {
                        $this->form_validation->set_rules('parameter', $this->lang->line('parameter'), 'trim|required|xss_clean');
                        break;      
                    }

                }

            }
        }

        if ($this->form_validation->run() == false) {

            $msg['cbse_observation_term_id'] = form_error('cbse_observation_term_id');
            $msg['row']                      = form_error('row');
            $msg['parameter']                = form_error('parameter');
            $array = array('status' => 0, 'error' => $msg);
        } else {
            $this->input->post('cbse_observation_term_id');
            $insert_array = [];
            $update_array = [];
            $delete_array = [];

            foreach ($row as $row_key => $row_value) {
                foreach ($cbse_observation_parameters as $parameter_key => $parameter_value) {

                    $student_session = $this->input->post('student_session_' . $row_value);
                    if($this->input->post('old_cbse_observation_term_student_subparameter_id_' . $student_session . '_' . $parameter_value) != ""){
                    $update_array[]  = [
                        'id'         => $this->input->post('old_cbse_observation_term_student_subparameter_id_' . $student_session . '_' . $parameter_value),
                        'cbse_ovservation_term_id'         => $this->input->post('cbse_observation_term_id'),
                        'student_session_id'               => $this->input->post('student_session_' . $row_value),
                        'cbse_observation_subparameter_id' => $parameter_value,
                        'obtain_marks'                     => $this->input->post('param_value_' . $student_session . '_' . $parameter_value),

                    ];
                    }else{

                    $insert_array[]  = [
                        'cbse_ovservation_term_id'         => $this->input->post('cbse_observation_term_id'),
                        'student_session_id'               => $this->input->post('student_session_' . $row_value),
                        'cbse_observation_subparameter_id' => $parameter_value,
                        'obtain_marks'                     => $this->input->post('param_value_' . $student_session . '_' . $parameter_value),

                    ];
                    }
                }
            }
         
            $this->cbse_observation_term_student_subparameter_model->add($insert_array, $update_array);
            $array = array('status' => 1, 'message' => $this->lang->line('success_message'));

        }

        echo json_encode($array);
    }

    public function add()
    {

        $row = $this->input->post('row[]');

        $this->form_validation->set_rules(
            'observation', $this->lang->line('observation'), array(
                'required',
                array('observation_exists', array($this->cbseexam_observation_model, 'observation_exists')),
            )
        );

        if (!isset($row)) {

            $this->form_validation->set_rules('row', $this->lang->line('no_paramenter_added'), 'trim|required|xss_clean');         

        } else {

            foreach ($row as $row_key => $row_value) {
                if ($this->input->post('parameter_' . $row_value) == "") {
                    $this->form_validation->set_rules('parameter', $this->lang->line('parameter'), 'trim|required|xss_clean');
                }

                if ($this->input->post('max_marks_' . $row_value) == "") {
                    $this->form_validation->set_rules('max_marks', $this->lang->line('max_marks'), 'trim|required|xss_clean');  
                }

            }
        }

        if ($this->form_validation->run() == false) {
            $msg['observation'] = form_error('observation');
            $msg['row']         = form_error('row');
            $msg['parameter']   = form_error('parameter');
            $msg['max_marks']   = form_error('max_marks');

            $array = array('status' => 0, 'error' => $msg);
        } else {

            $cbse_observation_subparameter        = [];
            $cbse_observation_subparameter_update = [];
            $cbse_exam_observation                = [
                'name'        => $this->input->post('observation'),
                'description' => $this->input->post('description'),
            ];
            $delete_observation_subparameter = [];
            if ($this->input->post('action') == "update") {
                $cbse_exam_observation['id']     = $this->input->post('record_id');
                $prev_ids                        = $this->input->post('prev_ids');
                $update_id                       = $this->input->post('update_id');
                $delete_observation_subparameter = array_diff($prev_ids, $update_id);

            }

            $update_ids = $this->input->post('update_id');

            foreach ($_POST['row'] as $row_key => $row_value) {
                if ($update_ids[$row_key] > 0) {
                    $update_grade_range = array(
                        'id'                            => $update_ids[$row_key],
                        'cbse_exam_observation_id'      => $cbse_exam_observation['id'],
                        'cbse_observation_parameter_id' => $this->input->post('parameter_' . $row_value),
                        'maximum_marks'                 => $this->input->post('max_marks_' . $row_value),
                    );
                    $cbse_observation_subparameter_update[] = $update_grade_range;
                } else {
                    $insert_grade_range = array(
                        'cbse_exam_observation_id'      => '',
                        'cbse_observation_parameter_id' => $this->input->post('parameter_' . $row_value),
                        'maximum_marks'                 => $this->input->post('max_marks_' . $row_value),
                    );
                    $cbse_observation_subparameter[] = $insert_grade_range;
                }

            }

            $this->cbseexam_observation_model->add($cbse_exam_observation, $cbse_observation_subparameter, $cbse_observation_subparameter_update, $delete_observation_subparameter);

            $array = array('status' => 1, 'message' => $this->lang->line('success_message'));

        }

        echo json_encode($array);
    }

    public function get_editdetails()
    {
        $id     = $this->input->post('id');
        $result = $this->cbseexam_observation_model->get_editdetails($id);
        echo json_encode($result);
    }

    public function addobservationparam()
    {
        $row          = $this->input->post('row');
        $insert_array = [];
        foreach ($row as $row_key => $row_value) {
            $insert_array[] = [
                'cbse_ovservation_term_id'         => $this->input->post('cbse_ovservation_term_id_' . $row_value),
                'cbse_observation_subparameter_id' => $this->input->post('cbse_observation_subparameter_id_' . $row_value),
                'obtain_marks'                     => $this->input->post('obtain_marks_' . $row_value),
                'student_session_id'               => $this->input->post('student_session_id'),
            ];

        }
        $this->cbse_observation_term_student_subparameter_model->add($insert_array);
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        echo json_encode($array);
    }

    public function observationform()
    {
        $data               = array();
        $data['action']     = $this->input->post('action');
        $data['record_id']  = $this->input->post('record_id');
        $data['parameters'] = $this->cbseexam_observation_parameter_model->get();
        $total_rows         = 2;
        if ($data['record_id'] > 0) {
            $get_old_data         = $this->cbseexam_observation_parameter_model->getWithParameters($data['record_id']);
            $total_rows           = count($get_old_data['list']) + 1;
            $data['get_old_data'] = $get_old_data;
        }

        $page = $this->load->view("cbseexam/observation/_observationform", $data, true);

        echo json_encode(['status' => 1, 'page' => $page, 'total_rows' => $total_rows]);

    }

    public function add_subparameter()
    {
        $data = array();
        if (isset($_POST['list']) && !empty($_POST['list'])) {
            foreach ($_POST['list'] as $lkey => $lvalue) {
                $data['result']        = $lvalue;
                $data['delete_string'] = $lvalue['id'];
                $view[]                = $this->load->view("cbseexam/observation/_add_subparameter", $data, true);
            }
            echo json_encode($view);
        } else {
            $id                    = $this->input->post('id');
            $result                = $this->cbseexam_observation_model->get_observation_subparameterbyId($id);
            $data['result']        = $result;
            $data['delete_string'] = $this->input->post('delete_string');
            echo json_encode($this->load->view("cbseexam/observation/_add_subparameter", $data, true));
        }
    }

    public function observationtermform()
    {
        $data                          = array();
        $data['action']                = $this->input->post('action');
        $data['record_id']             = $this->input->post('record_id');
        $data['observation_parameter'] = $this->cbseexam_observation_model->getobservationlist();
        $data['terms']                 = $this->cbseexam_term_model->get();

        $total_rows = 2;
        if ($data['record_id'] > 0) {
            $get_old_data         = $this->cbse_observation_term_model->get($data['record_id']);
            $data['get_old_data'] = $get_old_data;
        }

        $page = $this->load->view("cbseexam/observation/partial/_observationtermform", $data, true);
        echo json_encode(['status' => 1, 'page' => $page, 'total_rows' => $total_rows]);
    }

    public function assign()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_assign_observation', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/assign');
        $class                         = $this->class_model->get();
        $data['classlist']             = $class;
        $data['observation_parameter'] = $this->cbseexam_observation_model->getobservationlist();
        $data['terms']                 = $this->cbseexam_term_model->get();
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/observation/assign', $data);
        $this->load->view('layout/footer', $data);
    }

    public function termstudent()
    {
        $this->form_validation->set_error_delimiters('<li>', '</li>');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg = array(
                'class_id' => form_error('class_id'),

            );
            $array = array('status' => 0, 'error' => $msg);
            echo json_encode($array);
        } else {
            $data['sch_setting']      = $this->setting_model->getSetting();
            $cbse_term_id             = $this->input->post('cbse_term_id');
            $cbse_observation_term_id = $this->input->post('cbse_observation_term_id');
            $class_id                 = $this->input->post('class_id');
            $section_id               = $this->input->post('section_id');

            $data['cbse_term_id']             = $this->input->post('cbse_term_id');
            $data['cbse_observation_term_id'] = $this->input->post('cbse_observation_term_id');
            $data['class_id']                 = $this->input->post('class_id');
            $data['section_id']               = $this->input->post('section_id');
            $observationParamsList = $this->cbseexam_observation_parameter_model->getObservationParamsByObservationTerm($cbse_observation_term_id);
            $studentlist = $this->cbseexam_exam_model->searchTermStudentsByClass($cbse_observation_term_id, $data['class_id'], $data['section_id']);

            $student_parameter_data = array();
            if (!empty($studentlist)) {

                foreach ($studentlist as $student_key => $student_value) {

                    $new_array = array();

                    if (!array_key_exists($student_value->student_id, $student_parameter_data)) {
                        $new_array                                                            = clone $student_value;
                        $new_array->{'params'}[$student_value->cbse_observation_parameter_id] = [
                            'cbse_observation_subparameter_id'              => $student_value->cbse_observation_subparameter_id,
                            'cbse_observation_term_student_subparameter_id' => $student_value->cbse_observation_term_student_subparameter_id,
                            'cbse_observation_subparameter_id'              => $student_value->cbse_observation_subparameter_id,
                            'cbse_observation_term_id'                      => $student_value->cbse_observation_term_id,
                            'cbse_observation_parameter_name'               => $student_value->cbse_observation_parameter_name,
                            'cbse_exam_observation_id'                      => $student_value->cbse_exam_observation_id,
                            'cbse_observation_parameter_id'                 => $student_value->cbse_observation_parameter_id,
                            'obtain_marks'                                  => $student_value->obtain_marks,
                        ];
                        $student_parameter_data[$student_value->student_id] = $new_array;

                    } else {
                        $student_parameter_data[$student_value->student_id]->{'params'}[$student_value->cbse_observation_parameter_id] = [
                            'cbse_observation_subparameter_id'              => $student_value->cbse_observation_subparameter_id,
                            'cbse_observation_term_student_subparameter_id' => $student_value->cbse_observation_term_student_subparameter_id,
                            'cbse_observation_subparameter_id'              => $student_value->cbse_observation_subparameter_id,
                            'cbse_observation_term_id'                      => $student_value->cbse_observation_term_id,
                            'cbse_observation_parameter_name'               => $student_value->cbse_observation_parameter_name,
                            'cbse_exam_observation_id'                      => $student_value->cbse_exam_observation_id,
                            'cbse_observation_parameter_id'                 => $student_value->cbse_observation_parameter_id,
                            'obtain_marks'                                  => $student_value->obtain_marks,
                        ];

                    }
                }
            }

            $data['observationParamsList'] = $observationParamsList;
            $data['studentlist']           = $student_parameter_data;
            $student_exam_page             = $this->load->view('cbseexam/observation/partial/_termstudent', $data, true);
            $array                         = array('status' => '1', 'error' => '', 'page' => $student_exam_page);
            echo json_encode($array);
        }
    }

    public function getlist()
    {
        $m = $this->cbse_observation_term_model->getlist();
        $m = json_decode($m);

        $dt_data = array();
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $assignbtn   = '';
                $editbtn     = '';
                $deletebtn   = '';
                $documents   = '';
                $studentsbtn = '';

                if ($this->rbac->hasPrivilege('cbse_exam_assign_observation', 'can_edit')) {

                    $studentsbtn = "<button type='button' data-record_id='" . $value->id . "' data-cbse_term_id='" . $value->cbse_term_id . "'  class='btn btn-default btn-xs' data-action='insert' data-toggle='modal' data-target='#assignModal' data-original-title='".$this->lang->line('assign_marks')."' title='".$this->lang->line('assign_marks')."' autocomplete='off'><i class='fa fa-newspaper-o'></i></button> ";

                    $editbtn = "<button type='button' data-record_id='" . $value->id . "' class='btn btn-default btn-xs' data-action='update' data-toggle='modal' data-target='#myModal' data-original-title='".$this->lang->line('edit_observation_term')."' title='".$this->lang->line('edit_observation_term')."' autocomplete='off'><i class='fa fa-pencil'></i></button> ";

                }   

                if ($this->rbac->hasPrivilege('cbse_exam_assign_observation', 'can_delete')) {
                    $deletebtn = '';
                    $deletebtn = "<a onclick='return confirm("."\"". $this->lang->line('delete_confirm') ."\"". ")' href='" . base_url() . "cbseexam/observation/deleteassignterm/" . $value->id . "' class='btn btn-default btn-xs' title='" . $this->lang->line('delete') . "' data-toggle='tooltip'><i class='fa fa-trash'></i></a>";
                }

                $row   = array();
                $row[] = $value->cbse_observation_parameter_name;
                $row[] = $value->cbse_term_name;
                $row[] = $value->term_code;
                $row[] = $value->description;
                $row[]     = $studentsbtn . '' . $editbtn . ' ' . $deletebtn;
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

    public function get_assignclasssections()
    {
        $class_sections       = $this->cbseexam_observation_model->get_assignclasssections($_POST['cbse_observation_parameter_id'], $_POST['class_id']);
        $data['section_list'] = $this->section_model->getClassBySection($_POST['class_id']);
        $view = $this->load->view('cbseexam/observation/_get_assignclasssections', $data, true);
        echo json_encode(array('acs' => $class_sections, 'view' => $view));
    }

    public function getClassByExam()
    {
        $exam_id = $this->input->get('exam_id');
        $classes = $this->cbseexam_exam_model->getClassByExam($exam_id);
        echo json_encode(array('classes' => $classes));
    }

    public function getExamSectionByClass()
    {
        $exam_id  = $this->input->get('exam_id');
        $class_id = $this->input->get('class_id');
        $classes  = $this->cbseexam_exam_model->getExamSectionByClass($exam_id, $class_id);
        echo json_encode(array('classes' => $classes));
    }

    public function exam_observationstudent()
    {
        $data['observation_id']   = $this->input->post('observation_id');
        $resultlist               = $this->cbseexam_observation_model->get_observation_student($data['observation_id']);
        $observation_subparameter = $this->cbseexam_observation_model->observationsubparameter($data['observation_id']);
        $data['sub_parameter']    = $observation_subparameter;
        $data['resultlist']       = $resultlist;
        $data['sch_setting']      = $this->sch_setting_detail;
        $student_exam_page        = $this->load->view('cbseexam/observation/_exam_observationstudent', $data, true);
        $array                    = array('status' => '1', 'error' => '', 'page' => $student_exam_page);
        echo json_encode($array);
    }

    public function assignObservationTerm()
    {

        $this->form_validation->set_rules('cbse_exam_observation_id', $this->lang->line('observation'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required|xss_clean');
        $this->form_validation->set_rules(
            'cbse_term_id', $this->lang->line('term'), array(
                'required',
                array('observation_term_exists', array($this->cbse_observation_term_model, 'observation_term_exists')),
            )
        );      
         

        if ($this->form_validation->run() == false) {
            $msg['observation'] = form_error('cbse_exam_observation_id');
            $msg['term']        = form_error('cbse_term_id');
            $msg['description']        = form_error('description');

            $array = array('status' => 0, 'error' => $msg);
        } else {

            $insert_array = array(
                'cbse_exam_observation_id' => $this->input->post('cbse_exam_observation_id'),
                'cbse_term_id'             => $this->input->post('cbse_term_id'),
                'session_id'               => 16,
                'description'              => $this->input->post('description'),
            );

            if ($this->input->post('action') == "update") {
                $insert_array['id'] = $this->input->post('record_id');
            }

            $this->cbse_observation_term_model->add($insert_array);

            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));

        }
        echo json_encode($array);
    }

    public function add_observation_marks()
    {
        foreach ($_POST['sub_parameter'] as $studentskey => $students) {
            foreach ($students as $parameterkey => $parameter) {
                foreach ($parameter as $sub_parameterkey => $sub_parameter_marks) {
                    $marks_data = array('cbse_observation_subparameter_id' => $sub_parameterkey, 'student_session_id' => $studentskey, 'marks' => $sub_parameter_marks);
                    $this->cbseexam_observation_model->add_observation_student_marks($marks_data);
                }
            }
        }

        redirect('cbseexam/observation/assign');
    }

    public function delete($id)
    {    
        $this->cbseexam_observation_model->remove($id);     
        redirect('cbseexam/observation');
    }    

    public function deleteassignterm($id)
    {    
        $this->cbse_observation_term_model->remove($id);     
        redirect('cbseexam/observation/assign');
    }

    public function removeassignclass_sections()
    {
        $class_sections = $this->cbseexam_observation_model->get_assignclasssections($_POST['observation_parameter_id'], $_POST['class_id']);
        foreach ($class_sections as $cs_key => $cs_value) {

            $class_sections = $this->cbseexam_observation_model->removeassignclass_sections($_POST['observation_parameter_id'], $cs_value['class_section_id']);

        }

        $array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('delete_message'));
        echo json_encode($array);
    }

}
