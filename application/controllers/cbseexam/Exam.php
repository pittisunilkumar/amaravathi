<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Exam extends MY_Addon_CBSEController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('cbse_mail_sms');
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam', 'can_view')) {
            access_denied();
        }
        
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'exam/index');
        $data['result'] = $this->cbseexam_exam_model->getexamlist();
        $data['term_list'] = $this->cbseexam_term_model->get();
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $data['assessment_result'] = $this->cbseexam_assessment_model->get();
        $data['grade_result'] = $this->cbseexam_grade_model->getgradelist();
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/exam/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function read($id)
    {
        if (!$this->rbac->hasPrivilege('cbse_exam', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/exam');
        $data['result'] = $this->cbseexam_exam_model->get_exambyId($id);
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/exam/read', $data);
        $this->load->view('layout/footer', $data);
    }


    public function examstudent()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_assign_view_student', 'can_view')) {
            access_denied();
        }

        $data['sch_setting'] = $this->setting_model->getSetting();
        $examid = $_POST['examid'];
        $exam_class_section = $this->cbseexam_exam_model->get_class_sectionbyexamid($examid);
        $resultlist = $this->cbseexam_exam_model->searchExamStudents($exam_class_section, $examid);
        $data['exam_id'] = $examid;
        $data['resultlist'] = $resultlist;
        $student_exam_page = $this->load->view('cbseexam/exam/_partialexamstudent', $data, true);
        $array = array('status' => '1', 'error' => '', 'page' => $student_exam_page);
        echo json_encode($array);
    }

    public function getexamSubjects()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_subjects', 'can_view')) {
            access_denied();
        }

        $exam_id = $this->input->post('exam_id');
        $class_batch_id = $this->input->post('class_batch_id');
        $exam_group_ids = $this->input->post('exam_group_id');
        $data['examDetail'] = $this->cbseexam_exam_model->getexamdetails($exam_id);
        $data['exam_subjects'] = $this->cbseexam_exam_model->getexamsubjects($exam_id);
        $data['batch_subjects'] = $this->subject_model->get();
        $data['exam_id'] = $exam_id;
        $data['exam_subjects_count'] = count($data['exam_subjects']);
        $data['batch_subject_dropdown'] = $this->load->view('cbseexam/exam/_partialexamSubjectDropdown', $data, true);
        $data['subject_page'] = $this->load->view('cbseexam/exam/_partialexamSubjects', $data, true);
        echo json_encode($data);
    }

    public function exam_rank()
    {

        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/generate_rank');
        $this->session->set_userdata('subsub_menu', 'cbse_exam/exam_wise_rank');

        $data = array();
        $exams = $this->cbseexam_exam_model->getexamlist();

        $data['exams'] = $exams;
        $data['title'] = 'Add Batch';
        $data['title_list'] = 'Recent Batch';
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/exam/exam_rank', $data);
        $this->load->view('layout/footer', $data);
    }

    public function exam_ajax_rank()
    {
        $this->form_validation->set_rules('exam', $this->lang->line('exam'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {
            $data['sch_setting'] = $this->sch_setting_detail;
            $class_section_id = $this->input->post('class_section_id');
            $exam = $this->input->post('exam');
            $data['exam_id'] = $exam;
            $data['studentList'] = $this->cbseexam_exam_model->getExamStudents($exam);
            $page = $this->load->view('cbseexam/exam/_studentrank', $data, true);
            $array = array('status' => 1, 'error' => '', 'page' => $page);
        } else {

            $msg = array(
                'exam' => form_error('exam')
            );
            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        }
        echo json_encode($array);
    }

    public function examrankgenerate()
    {
        $this->form_validation->set_rules('exam_id', $this->lang->line('exam_id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('student_session_id[]', $this->lang->line('class'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {

            $exam_id = $this->input->post('exam_id');
            $student_session_ids = $this->input->post('student_session_id');
            $this->updateExamRank($exam_id);
            $array = array('status' => 1, 'msg' => $this->lang->line('record_updated_successfully'));  
            
        } else {

            $msg = array(
                'exam_id' => form_error('exam_id'),
                'student_session_id' => form_error('student_session_id'),
            );

            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        }
        echo json_encode($array);
    }



    public function updateExamRank($exam_id)
    {

        $exam_id          = $this->input->post('exam_id'); 
        $exam             = $this->cbseexam_exam_model->getExamWithGrade($exam_id);
        $exam_assessments = $this->cbseexam_assessment_model->getWithAssessmentTypeByAssessmentID($exam->cbse_exam_assessment_id);
        $data['exam_assessments'] = $exam_assessments;
        $students = [];
        $cbse_exam_result = $this->cbseexam_exam_model->getExamResultByExamId($exam_id);

        if (!empty($cbse_exam_result)) {

            foreach ($cbse_exam_result as $student_key => $student_value) {

                $exam_assessments[$student_value->cbse_exam_assessment_type_id] = $student_value->cbse_exam_assessment_type_id;

                if (array_key_exists($student_value->student_session_id, $students)) {

                    if (!array_key_exists($student_value->subject_id, $students[$student_value->student_session_id]['subjects'])) {

                        $new_subject = [
                            'subject_id'       => $student_value->subject_id,
                            'subject_name'     => $student_value->subject_name,
                            'subject_code'     => $student_value->subject_code,
                            'exam_assessments' => [
                                $student_value->cbse_exam_assessment_type_id => [
                                    'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                    'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                    'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                    'maximum_marks'                  => $student_value->maximum_marks,
                                    'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                    'marks'                          => $student_value->marks,
                                    'note'                           => $student_value->note,
                                    'is_absent'                      => $student_value->is_absent,
                                ],
                            ],
                        ];

                        $students[$student_value->student_session_id]['subjects'][$student_value->subject_id] = $new_subject;
                    } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_session_id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

                        $new_assesment = [
                            'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                            'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                            'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                            'maximum_marks'                  => $student_value->maximum_marks,
                            'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                            'marks'                          => $student_value->marks,
                            'note'                           => $student_value->note,
                            'is_absent'                      => $student_value->is_absent,
                        ];

                        $students[$student_value->student_session_id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                    }
                } else {

                    $students[$student_value->student_session_id] = [
                        'student_id'         => $student_value->student_id,
                        'student_session_id' => $student_value->student_session_id,
                        'firstname'          => $student_value->firstname,
                        'middlename'         => $student_value->middlename,
                        'lastname'           => $student_value->lastname,
                        'mobileno'           => $student_value->mobileno,
                        'email'              => $student_value->email,
                        'religion'           => $student_value->religion,
                        'guardian_name'      => $student_value->guardian_name,
                        'guardian_phone'     => $student_value->guardian_phone,
                        'dob'                => $student_value->dob,
                        'remark'             => $student_value->remark,
                        'admission_no'       => $student_value->admission_no,
                        'father_name'        => $student_value->father_name,
                        'mother_name'        => $student_value->mother_name,
                        'class_id'           => $student_value->class_id,
                        'class'              => $student_value->class,
                        'section_id'         => $student_value->section_id,
                        'section'            => $student_value->section,
                        'roll_no'            => $student_value->roll_no,
                        'student_image'      => $student_value->image,
                        'gender'             => $student_value->gender,
                        'total_present_days' => $student_value->total_present_days,
                        'total_working_days' => $student_value->total_working_days,
                        'subjects'           => [
                            $student_value->subject_id => [
                                'subject_id'       => $student_value->subject_id,
                                'subject_name'     => $student_value->subject_name,
                                'subject_code'     => $student_value->subject_code,
                                'exam_assessments' => [
                                    $student_value->cbse_exam_assessment_type_id => [
                                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                        'cbse_exam_assessment_type_id'   => $student_value->cbse_exam_assessment_type_id,
                                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                        'maximum_marks'                  => $student_value->maximum_marks,
                                        'cbse_student_subject_marks_id'  => $student_value->cbse_student_subject_marks_id,
                                        'marks'                          => $student_value->marks,
                                        'note'                           => $student_value->note,
                                        'is_absent'                      => $student_value->is_absent,
                                    ],
                                ],
                            ],
                        ],
                    ];
                }
            }
        }

        $data['students'] = $students;

        if (!empty($students)) {
            //===============
            // Rank

            $student_allover_rank = [];
            $subject_rank = [];
            foreach ($students as $student_key => $student_value) {
                $total_max_marks = 0;
                $total_gain_marks = 0;

                foreach ($student_value['subjects'] as $subject_key => $subject_value) {
                    $subject_total = 0;
                    $subject_max_total = 0;

                    foreach ($subject_value['exam_assessments'] as $assessment_key => $assessment_value) {
                        $subject_total += $assessment_value['marks'];
                        $subject_max_total += $assessment_value['maximum_marks'];

                        $total_gain_marks += $assessment_value['marks'];
                        $total_max_marks += $assessment_value['maximum_marks'];
                    }

                    if (!array_key_exists($subject_key, $subject_rank)) {
                        $subject_rank[$subject_key] = [];
                    }

                    $subject_rank[$subject_key][] = [
                        'student_session_id' => $student_value['student_session_id'],
                        'rank_percentage'    => $subject_total,
                        'rank' => 0
                    ];
                }

                $exam_percentage = getPercent($total_max_marks, $total_gain_marks);
                $student_allover_rank[$student_value['student_session_id']] = [
                    'student_session_id' => $student_value['student_session_id'],
                    'cbse_exam_id' => $exam_id,
                    'rank_percentage' => $exam_percentage,
                    'rank' => 0,
                ];
            }

            //-=====================start term calculation Rank=============

            $rank_overall_percentage_keys = array_column($student_allover_rank, 'rank_percentage');

            array_multisort($rank_overall_percentage_keys, SORT_DESC, $student_allover_rank);

            $term_rank_allover_list = unique_array($student_allover_rank, "rank_percentage");

            foreach ($student_allover_rank as $term_rank_key => $term_rank_value) {

                $student_allover_rank[$term_rank_key]['rank'] = array_search($term_rank_value['rank_percentage'], $term_rank_allover_list);
            }

            //-=====================end term calculation Rank=============

            //===============

            $this->cbseexam_student_rank_model->add_exam_rank($student_allover_rank, $exam_id);
        }
    }

    public function examwiserank($exam_id)
    {

        $data = array();
        $data['sch_setting'] = $this->sch_setting_detail;
        $exam = $exam_id;
        $data['exam_id'] = $exam;

        $data['exam'] = $this->cbseexam_exam_model->get_exambyId($exam);
        $data['studentList'] = $this->cbseexam_exam_model->getExamStudents($exam);

        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/exam/examwiserank', $data);
        $this->load->view('layout/footer', $data);
    }

    public function rank()
    {
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/generate_rank');
        $this->session->set_userdata('subsub_menu', 'cbse_exam/template_wise_rank');
        $data = array();
        $templates = $this->cbseexam_template_model->gettemplatelist();
        $data['templates'] = $templates;
        $data['title'] = 'Add Batch';
        $data['title_list'] = 'Recent Batch';
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/exam/rank', $data);
        $this->load->view('layout/footer', $data);
    }

    public function term_wise($cbse_template_id, $students) //multiple exam in single term
    {
        $data['template'] = $this->cbseexam_template_model->get($cbse_template_id);
        $data['sch_setting'] = $this->sch_setting_detail;
        $cbse_template_subject_term_exam = $this->cbseexam_template_model->getTemplateTermExamWithAssessment($cbse_template_id);
        //==================================       
        $cbse_exam_result = $this->cbseexam_exam_model->getStudentExamResultTermwiseByTemplateId($cbse_template_id, $students);
        $template_subjects = $this->cbseexam_exam_model->getTemplateAssessment($cbse_template_id);
        $subject_array = $cbse_template_subject_term_exam['subjects'];
        $exam_term_exam_assessment = $cbse_template_subject_term_exam['terms'];
        $gradeexam_id = "";
        $remarkexam_id = "";
        $data['subject_array'] = $subject_array;
        $data['exam_term_exam_assessment'] = $exam_term_exam_assessment;

        $students = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {

            $gradeexam_id = $student_value->gradeexam_id;
            $remarkexam_id = $student_value->remarkexam_id;

            if (array_key_exists($student_value->student_id, $students)) {

                if (!array_key_exists($student_value->cbse_term_id, $students[$student_value->student_id]['terms'])) {
                    $new_cbse_term_id = [

                        'cbse_term_id' => $student_value->cbse_term_id,
                        'cbse_term_name' => $student_value->cbse_term_name,
                        'cbse_term_code' => $student_value->cbse_term_code,
                        'cbse_term_weight' => $student_value->cbse_template_terms_weightage,
                        'term_total_assessments' => 1,

                        'exams' => [
                            $student_value->id => [
                                'name' => $student_value->name,
                                'total_assessments' => 1,
                                'total_present_days' => $student_value->total_present_days,
                                'total_working_days' => $student_value->total_working_days,
                                'subjects' => [
                                    $student_value->subject_id => [
                                        'subject_id' => $student_value->subject_id,
                                        'subject_name' => $student_value->subject_name,
                                        'subject_code' => $student_value->subject_code,
                                        'exam_assessments' => [
                                            $student_value->cbse_exam_assessment_type_id => [
                                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                'maximum_marks' => $student_value->maximum_marks,
                                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                                'marks' => $student_value->marks,
                                                'note' => $student_value->note,
                                                'is_absent' => $student_value->is_absent,

                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id] = $new_cbse_term_id;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->id, $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'])) {

                    $new_exam = [
                        'name' => $student_value->name,
                        'total_assessments' => 1,
                        'total_present_days' => $student_value->total_present_days,
                        'total_working_days' => $student_value->total_working_days,
                        'subjects' => [
                            $student_value->subject_id => [
                                'subject_id' => $student_value->subject_id,
                                'subject_name' => $student_value->subject_name,
                                'subject_code' => $student_value->subject_code,
                                'exam_assessments' => [
                                    $student_value->cbse_exam_assessment_type_id => [
                                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                        'maximum_marks' => $student_value->maximum_marks,
                                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                        'marks' => $student_value->marks,
                                        'note' => $student_value->note,
                                        'is_absent' => $student_value->is_absent,

                                    ]
                                ]
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id] = $new_exam;
                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->subject_id, $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'])) {


                    $new_subject = [
                        'subject_id' => $student_value->subject_id,
                        'subject_name' => $student_value->subject_name,
                        'subject_code' => $student_value->subject_code,
                        'exam_assessments' => [
                            $student_value->cbse_exam_assessment_type_id => [
                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                'maximum_marks' => $student_value->maximum_marks,
                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                'marks' => $student_value->marks,
                                'note' => $student_value->note,
                                'is_absent' => $student_value->is_absent,
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

                    $new_assesment = [
                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                        'maximum_marks' => $student_value->maximum_marks,
                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                        'marks' => $student_value->marks,
                        'note' => $student_value->note,
                        'is_absent' => $student_value->is_absent,
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;
                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['total_assessments'] = count($students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments']);
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                }
            } else {
                $students[$student_value->student_id] = [
                    'student_id' => $student_value->student_id,
                    'student_session_id' => $student_value->student_session_id,
                    'firstname' => $student_value->firstname,
                    'middlename' => $student_value->middlename,
                    'lastname' => $student_value->lastname,
                    'mobileno' => $student_value->mobileno,
                    'email' => $student_value->email,
                    'religion' => $student_value->religion,
                    'guardian_name' => $student_value->guardian_name,
                    'guardian_phone' => $student_value->guardian_phone,
                    'dob' => $student_value->dob,
                    'admission_no' => $student_value->admission_no,
                    'father_name' => $student_value->father_name,
                    'mother_name' => $student_value->mother_name,
                    'class_id' => $student_value->class_id,
                    'class' => $student_value->class,
                    'section_id' => $student_value->section_id,
                    'section' => $student_value->section,
                    'roll_no' => $student_value->roll_no,
                    'student_image' => $student_value->image,
                    'gender' => $student_value->gender,
                    'terms' => [
                        $student_value->cbse_term_id => [

                            'cbse_term_id' => $student_value->cbse_term_id,
                            'cbse_term_name' => $student_value->cbse_term_name,
                            'cbse_term_code' => $student_value->cbse_term_code,
                            'cbse_term_weight' => $student_value->cbse_template_terms_weightage,
                            'term_total_assessments' => 1,

                            'exams' => [
                                $student_value->id => [
                                    'name' => $student_value->name,
                                    'total_assessments' => 1,
                                    'total_present_days' => $student_value->total_present_days,
                                    'total_working_days' => $student_value->total_working_days,
                                    'subjects' => [
                                        $student_value->subject_id => [
                                            'subject_id' => $student_value->subject_id,
                                            'subject_name' => $student_value->subject_name,
                                            'subject_code' => $student_value->subject_code,
                                            'exam_assessments' => [
                                                $student_value->cbse_exam_assessment_type_id => [
                                                    'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                    'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                                    'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                    'maximum_marks' => $student_value->maximum_marks,
                                                    'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                                    'marks' => $student_value->marks,
                                                    'note' => $student_value->note,
                                                    'is_absent' => $student_value->is_absent,

                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]

                    ]
                ];
                if ($student_value->remarkexam_id == $remarkexam_id) {
                    $students[$student_value->student_id]['remark'] = $student_value->remark;
                }
            }
        }

        $data['result'] = $students;
        $data['gradeexam_id'] = $gradeexam_id;
        $data['remarkexam_id'] = $remarkexam_id;
        $data['exam_grades'] = $this->cbseexam_grade_model->getExamGrades($gradeexam_id);

        if (!empty($students)) {
            $student_allover_exam_rank = [];
            $subject_term_rank = [];
            foreach ($students as $student_key => $student_value) {

                $grand_total_term_percentage = 0;

                foreach ($subject_array as $subject_array_key => $subject_array_value) {
                    $subject_grand_total = 0;
                    $subject_total_exam_percentage = 0;

                    foreach ($exam_term_exam_assessment as $assess_key => $assess_value) {
                        $subject_grand_total = 0;
                        $subject_total_exam_percentage = 0;

                        foreach ($assess_value['exams'] as $exam_key => $exam_value) {
                            $exam_subject_total = 0;
                            $exam_subject_maximum_total = 0;
                            foreach ($exam_value['exam_assessments'] as $exam_assement_key => $exam_assement_value) {

                                $subject_marks_array = getSubjectDataTerm($student_value['terms'], $assess_value['cbse_term_id'], $exam_key, $subject_array_key, $exam_assement_value['cbse_exam_assessment_type_id']);


                                if (!$subject_marks_array['marks'] <= 0 || $subject_marks_array['marks'] == "N/A") {

                                    $exam_subject_total += ($subject_marks_array['marks'] == "N/A") ? 0 : $subject_marks_array['marks'];
                                    $exam_subject_maximum_total += $subject_marks_array['maximum_marks'];
                                } else {

                                    $exam_subject_total += 0;
                                    $exam_subject_maximum_total += 0;
                                }
                            }
                            $subject_percentage = getPercent($exam_subject_maximum_total, $exam_subject_total);
                            $subject_total_exam_percentage += ($subject_percentage * ($exam_value['exam_weightage'] / 100));
                            $grand_total_term_percentage += ($subject_percentage * ($exam_value['exam_weightage'] / 100));
                        }
                    }

                    //===============
                    if (!array_key_exists($subject_array_key, $subject_term_rank)) {
                        $subject_term_rank[$subject_array_key] = [];
                    }

                    $subject_term_rank[$subject_array_key][] = [
                        'student_session_id' => $student_value['student_session_id'],
                        'rank_percentage' => $subject_total_exam_percentage,
                        'rank' => 0,
                        'cbse_template_id' => $cbse_template_id,

                    ];

                    //==============

                }

                $overall_percentage = getPercent((count($subject_array) * 100), $grand_total_term_percentage);

                $student_allover_exam_rank[$student_value['student_session_id']] = [
                    'student_session_id' => $student_value['student_session_id'],
                    'cbse_template_id' => $cbse_template_id,
                    'rank_percentage' => $overall_percentage,
                    'rank' => 0,
                ];
            }

            //-=====================start term calculation Rank=============

            $rank_overall_term_percentage_keys = array_column($student_allover_exam_rank, 'rank_percentage');

            array_multisort($rank_overall_term_percentage_keys, SORT_DESC,  $student_allover_exam_rank);

            $term_rank_allover_list = unique_array($student_allover_exam_rank, "rank_percentage");

            foreach ($student_allover_exam_rank as $term_rank_key => $term_rank_value) {
                $student_allover_exam_rank[$term_rank_key]['rank'] = array_search($term_rank_value['rank_percentage'], $term_rank_allover_list);
            }

            //-=====================end term calculation Rank=============

            //-=====================start subject term calculation Rank=============

            foreach ($subject_term_rank as $subject_term_key => $subject_term_value) {


                $rank_overall_subject = array_column($subject_term_rank[$subject_term_key], 'rank_percentage');

                array_multisort($rank_overall_subject, SORT_DESC, $subject_term_rank[$subject_term_key]);

                $subject_rank_allover_list = unique_array($subject_term_rank[$subject_term_key], "rank_percentage");

                foreach ($subject_term_rank[$subject_term_key] as $subject_rank_key => $subject_rank_value) {

                    $subject_term_rank[$subject_term_key][$subject_rank_key]['rank'] = array_search($subject_rank_value['rank_percentage'], $subject_rank_allover_list);
                }
            }

            $this->cbseexam_student_rank_model->add_rank($student_allover_exam_rank, $cbse_template_id, $subject_term_rank);
        }
    }

    public function all_term($cbse_template_id, $students) //for multiple terms
    {
        $data['template'] = $this->cbseexam_template_model->get($cbse_template_id);
        $data['sch_setting'] = $this->sch_setting_detail;
        $cbse_template_subject_term_exam = $this->cbseexam_template_model->getTemplateTermExamWithAssessment($cbse_template_id);
        $cbse_exam_result = $this->cbseexam_exam_model->getStudentExamResultTermwiseByTemplateId($cbse_template_id, $students);
        $template_subjects = $this->cbseexam_exam_model->getTemplateAssessment($cbse_template_id);
        $subject_array = $cbse_template_subject_term_exam['subjects'];
        $exam_term_exam_assessment = $cbse_template_subject_term_exam['terms'];
        $gradeexam_id = "";
        $remarkexam_id = "";

        $data['subject_array'] = $subject_array;
        $data['exam_term_exam_assessment'] = $exam_term_exam_assessment;

        $students = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {
            $gradeexam_id = $student_value->gradeexam_id;
            $remarkexam_id = $student_value->remarkexam_id;

            if (array_key_exists($student_value->student_id, $students)) {

                if (!array_key_exists($student_value->cbse_term_id, $students[$student_value->student_id]['terms'])) {

                    $new_cbse_term_id = [

                        'cbse_term_id' => $student_value->cbse_term_id,
                        'cbse_term_name' => $student_value->cbse_term_name,
                        'cbse_term_code' => $student_value->cbse_term_code,
                        'cbse_term_weight' => $student_value->cbse_template_terms_weightage,
                        'term_total_assessments' => 1,

                        'exams' => [
                            $student_value->id => [
                                'name' => $student_value->name,
                                'total_assessments' => 1,
                                'total_present_days' => $student_value->total_present_days,
                                'total_working_days' => $student_value->total_working_days,
                                'subjects' => [
                                    $student_value->subject_id => [
                                        'subject_id' => $student_value->subject_id,
                                        'subject_name' => $student_value->subject_name,
                                        'subject_code' => $student_value->subject_code,
                                        'exam_assessments' => [
                                            $student_value->cbse_exam_assessment_type_id => [
                                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                'maximum_marks' => $student_value->maximum_marks,
                                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                                'marks' => $student_value->marks,
                                                'note' => $student_value->note,
                                                'is_absent' => $student_value->is_absent,

                                            ]

                                        ]
                                    ]

                                ]

                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id] = $new_cbse_term_id;
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->id, $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'])) {

                    $new_exam = [
                        'name' => $student_value->name,
                        'total_assessments' => 1,
                        'total_present_days' => $student_value->total_present_days,
                        'total_working_days' => $student_value->total_working_days,
                        'subjects' => [
                            $student_value->subject_id => [
                                'subject_id' => $student_value->subject_id,
                                'subject_name' => $student_value->subject_name,
                                'subject_code' => $student_value->subject_code,
                                'exam_assessments' => [
                                    $student_value->cbse_exam_assessment_type_id => [
                                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                        'maximum_marks' => $student_value->maximum_marks,
                                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                        'marks' => $student_value->marks,
                                        'note' => $student_value->note,
                                        'is_absent' => $student_value->is_absent,
                                    ]
                                ]
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id] = $new_exam;
                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->subject_id, $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'])) {


                    $new_subject = [
                        'subject_id' => $student_value->subject_id,
                        'subject_name' => $student_value->subject_name,
                        'subject_code' => $student_value->subject_code,
                        'exam_assessments' => [
                            $student_value->cbse_exam_assessment_type_id => [
                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                'maximum_marks' => $student_value->maximum_marks,
                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                'marks' => $student_value->marks,
                                'note' => $student_value->note,
                                'is_absent' => $student_value->is_absent,
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

                    $new_assesment = [
                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                        'maximum_marks' => $student_value->maximum_marks,
                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                        'marks' => $student_value->marks,
                        'note' => $student_value->note,
                        'is_absent' => $student_value->is_absent,

                    ];

                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;
                    $students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['total_assessments'] = count($students[$student_value->student_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments']);
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                }
            } else {

                $students[$student_value->student_id] = [
                    'student_id' => $student_value->student_id,
                    'student_session_id' => $student_value->student_session_id,
                    'firstname' => $student_value->firstname,
                    'middlename' => $student_value->middlename,
                    'lastname' => $student_value->lastname,
                    'mobileno' => $student_value->mobileno,
                    'email' => $student_value->email,
                    'religion' => $student_value->religion,
                    'guardian_name' => $student_value->guardian_name,
                    'guardian_phone' => $student_value->guardian_phone,
                    'dob' => $student_value->dob,
                    'admission_no' => $student_value->admission_no,
                    'father_name' => $student_value->father_name,
                    'mother_name' => $student_value->mother_name,
                    'class_id' => $student_value->class_id,
                    'class' => $student_value->class,
                    'section_id' => $student_value->section_id,
                    'section' => $student_value->section,
                    'roll_no' => $student_value->roll_no,
                    'student_image' => $student_value->image,
                    'gender' => $student_value->gender,
                    'terms' => [
                        $student_value->cbse_term_id => [

                            'cbse_term_id' => $student_value->cbse_term_id,
                            'cbse_term_name' => $student_value->cbse_term_name,
                            'cbse_term_code' => $student_value->cbse_term_code,
                            'cbse_term_weight' => $student_value->cbse_template_terms_weightage,
                            'term_total_assessments' => 1,

                            'exams' => [
                                $student_value->id => [
                                    'name' => $student_value->name,
                                    'total_assessments' => 1,
                                    'total_present_days' => $student_value->total_present_days,
                                    'total_working_days' => $student_value->total_working_days,
                                    'subjects' => [
                                        $student_value->subject_id => [
                                            'subject_id' => $student_value->subject_id,
                                            'subject_name' => $student_value->subject_name,
                                            'subject_code' => $student_value->subject_code,
                                            'exam_assessments' => [
                                                $student_value->cbse_exam_assessment_type_id => [
                                                    'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                    'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                                    'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                    'maximum_marks' => $student_value->maximum_marks,
                                                    'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                                    'marks' => $student_value->marks,
                                                    'note' => $student_value->note,
                                                    'is_absent' => $student_value->is_absent,

                                                ]
                                            ]
                                        ]
                                    ]

                                ]
                            ]
                        ]

                    ]
                ];
                if ($student_value->remarkexam_id == $remarkexam_id) {
                    $students[$student_value->student_id]['remark'] = $student_value->remark;
                }
            }
        }

        $data['result'] = $students;
        $data['gradeexam_id'] = $gradeexam_id;
        $data['remarkexam_id'] = $remarkexam_id;
        $data['exam_grades'] = $this->cbseexam_grade_model->getExamGrades($gradeexam_id);

        if (!empty($students)) {

            $student_allover_term_rank = [];
            $subject_term_rank = [];
            foreach ($students as $student_key => $student_value) {
                $grand_total_marks = 0;
                $grand_total_term_percentage = 0;
                $grand_total_gain_marks = 0;
                $terms_weight_array = [];

                foreach ($subject_array as $subject_array_key => $subject_array_value) {
                    $subject_grand_total = 0;
                    $subject_total_term_percentage = 0;

                    foreach ($exam_term_exam_assessment as $assess_key => $assess_value) {
                        $subject_total = 0;
                        $subject_maximum_total = 0;

                        foreach ($assess_value['exams'] as $exam_key => $exam_value) {
                            foreach ($exam_value['exam_assessments'] as $exam_assement_key => $exam_assement_value) {
                                $subject_marks_array = getSubjectDataTerm($student_value['terms'], $assess_value['cbse_term_id'], $exam_key, $subject_array_key, $exam_assement_value['cbse_exam_assessment_type_id']);
                                if (!$subject_marks_array['marks'] <= 0 || $subject_marks_array['marks'] == "N/A") {

                                    $subject_total += ($subject_marks_array['marks'] == "N/A") ? 0 : $subject_marks_array['marks'];
                                    $subject_maximum_total += $subject_marks_array['maximum_marks'];
                                } else {

                                    $subject_total += 0;
                                    $subject_maximum_total += 0;
                                }
                            }
                        }

                        if ($subject_maximum_total <= 0 && $subject_total <= 0) {
                            $subject_maximum_total = 100;
                            $subject_total = 100;
                        }

                        $subject_percentage = getPercent($subject_maximum_total, $subject_total);
                        $total_term_ = (($subject_total * 100) / $subject_maximum_total);
                        $subject_total_term_percentage += ($total_term_ * ($assess_value['cbse_term_weight'] / 100));
                        $grand_total_term_percentage += ($total_term_ * ($assess_value['cbse_term_weight'] / 100));
                        $grand_total_gain_marks += $subject_total;
                        $grand_total_marks += $subject_maximum_total;
                    }

                    //===============
                    if (!array_key_exists($subject_array_key, $subject_term_rank)) {
                        $subject_term_rank[$subject_array_key] = [];
                    }

                    $subject_term_rank[$subject_array_key][] = [
                        'student_session_id' => $student_value['student_session_id'],
                        'rank_percentage' => $subject_total_term_percentage,
                        'cbse_template_id' => $cbse_template_id,
                        'rank' => 0

                    ];
                    //==============
                }

                $overall_percentage = getPercent((count($subject_array) * 100), $grand_total_term_percentage);

                $student_allover_term_rank[$student_value['student_session_id']] = [
                    'student_session_id' => $student_value['student_session_id'],
                    'cbse_template_id' => $cbse_template_id,
                    'rank_percentage' => $overall_percentage,
                    'rank' => 0,
                ];
            }

            //-=====================start term calculation Rank=============

            $rank_overall_term_percentage_keys = array_column($student_allover_term_rank, 'rank_percentage');

            array_multisort($rank_overall_term_percentage_keys, SORT_DESC, $student_allover_term_rank);

            $term_rank_allover_list = unique_array($student_allover_term_rank, "rank_percentage");

            foreach ($student_allover_term_rank as $term_rank_key => $term_rank_value) {
                $student_allover_term_rank[$term_rank_key]['rank'] = array_search($term_rank_value['rank_percentage'], $term_rank_allover_list);
            }

            //-=====================end term calculation Rank=============
            //-=====================start subject term calculation Rank=============

            foreach ($subject_term_rank as $subject_term_key => $subject_term_value) {

                $rank_overall_subject = array_column($subject_term_rank[$subject_term_key], 'rank_percentage');

                array_multisort($rank_overall_subject, SORT_DESC, $subject_term_rank[$subject_term_key]);

                $subject_rank_allover_list = unique_array($subject_term_rank[$subject_term_key], "rank_percentage");

                foreach ($subject_term_rank[$subject_term_key] as $subject_rank_key => $subject_rank_value) {

                    $subject_term_rank[$subject_term_key][$subject_rank_key]['rank'] = array_search($subject_rank_value['rank_percentage'], $subject_rank_allover_list);
                }
            }

            //-=====================end subject term calculation Rank=============

            $this->cbseexam_student_rank_model->add_rank($student_allover_term_rank, $cbse_template_id, $subject_term_rank);
        }
    }

    public function getExamAssesment($array, $find_cbse_term_id)
    {
        $return_array = [];
        foreach ($array as $_arrry_key => $_arrry_value) {
            if ($_arrry_value->cbse_exam_id == $find_cbse_term_id) {
                $return_array[] = [
                    'assesment_type_id' => $_arrry_value->cbse_exam_assessment_type_id,
                    'assesment_type_name' => $_arrry_value->name,
                    'assesment_type_code' => $_arrry_value->code,
                    'assesment_type_maximum_marks' => $_arrry_value->maximum_marks,
                    'assesment_type_pass_percentage' => $_arrry_value->pass_percentage,
                ];
            }
        }

        return $return_array;
    }

    public function multi_exam_without_term($cbse_template_id, $students) //multiple exam without term wise
    {
        $data['template'] = $this->cbseexam_template_model->get($cbse_template_id);
        $data['sch_setting'] = $this->sch_setting_detail;
        $cbse_exam_result = $this->cbseexam_exam_model->getStudentExamResultByTemplateId($cbse_template_id, $students);
        $template_subjects = $this->cbseexam_exam_model->getTemplateAssessmentWithoutTerm($cbse_template_id);
        $subject_array = [];
        $exam_term_exam_assessment = [];
        $gradeexam_id = "";
        $remarkexam_id = "";
        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {
            $subject_array[$cbse_exam_result_value->subject_id] = $cbse_exam_result_value->subject_name . " (" . $cbse_exam_result_value->subject_code . ")";
        }

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {

            if ((!array_key_exists($cbse_exam_result_value->id, $exam_term_exam_assessment))) {

                $assessment_array = $this->getExamAssesment($template_subjects, $cbse_exam_result_value->id);

                $new_terms = [

                    'exam_id' => $cbse_exam_result_value->id,
                    'exam_name' => $cbse_exam_result_value->name,
                    'weightage' => $cbse_exam_result_value->weightage,
                    'exam_total_assessments' => $assessment_array,

                ];
                $exam_term_exam_assessment[$cbse_exam_result_value->id] = $new_terms;
            }
        }

        $data['subject_array'] = $subject_array;
        $data['exam_term_exam_assessment'] = $exam_term_exam_assessment;
        $students = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {

            $gradeexam_id = $student_value->gradeexam_id;
            $remarkexam_id = $student_value->remarkexam_id;

            if (array_key_exists($student_value->student_id, $students)) {

                if (!array_key_exists($student_value->id, $students[$student_value->student_id]['exams'])) {

                    $new_exam = [
                        'name' => $student_value->name,
                        'total_assessments' => 1,
                        'total_present_days' => $student_value->total_present_days,
                        'total_working_days' => $student_value->total_working_days,
                        'subjects' => [
                            $student_value->subject_id => [
                                'subject_id' => $student_value->subject_id,
                                'subject_name' => $student_value->subject_name,
                                'subject_code' => $student_value->subject_code,
                                'exam_assessments' => [
                                    $student_value->cbse_exam_assessment_type_id => [
                                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                        'maximum_marks' => $student_value->maximum_marks,
                                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                        'marks' => $student_value->marks,
                                        'note' => $student_value->note,
                                        'is_absent' => $student_value->is_absent,

                                    ]

                                ]
                            ]

                        ]

                    ];

                    $students[$student_value->student_id]['exams'][$student_value->id] = $new_exam;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->subject_id, $students[$student_value->student_id]['exams'][$student_value->id]['subjects'])) {

                    $new_subject = [
                        'subject_id' => $student_value->subject_id,
                        'subject_name' => $student_value->subject_name,
                        'subject_code' => $student_value->subject_code,
                        'exam_assessments' => [
                            $student_value->cbse_exam_assessment_type_id => [
                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                'maximum_marks' => $student_value->maximum_marks,
                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                'marks' => $student_value->marks,
                                'note' => $student_value->note,
                                'is_absent' => $student_value->is_absent,
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

                    $new_assesment = [
                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                        'maximum_marks' => $student_value->maximum_marks,
                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                        'marks' => $student_value->marks,
                        'note' => $student_value->note,
                        'is_absent' => $student_value->is_absent,
                    ];

                    $students[$student_value->student_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_id]['remark'] = $student_value->remark;
                    }
                }
            } else {
                $students[$student_value->student_id] = [
                    'student_id' => $student_value->student_id,
                    'student_session_id' => $student_value->student_session_id,
                    'firstname' => $student_value->firstname,
                    'middlename' => $student_value->middlename,
                    'lastname' => $student_value->lastname,
                    'mobileno' => $student_value->mobileno,
                    'email' => $student_value->email,
                    'religion' => $student_value->religion,
                    'guardian_name' => $student_value->guardian_name,
                    'guardian_phone' => $student_value->guardian_phone,
                    'dob' => $student_value->dob,
                    'admission_no' => $student_value->admission_no,
                    'father_name' => $student_value->father_name,
                    'mother_name' => $student_value->mother_name,
                    'class_id' => $student_value->class_id,
                    'class' => $student_value->class,
                    'section_id' => $student_value->section_id,
                    'section' => $student_value->section,
                    'roll_no' => $student_value->roll_no,
                    'student_image' => $student_value->image,
                    'gender' => $student_value->gender,
                    'exams' => [
                        $student_value->id => [
                            'name' => $student_value->name,
                            'total_assessments' => 1,
                            'total_present_days' => $student_value->total_present_days,
                            'total_working_days' => $student_value->total_working_days,
                            'subjects' => [
                                $student_value->subject_id => [
                                    'subject_id' => $student_value->subject_id,
                                    'subject_name' => $student_value->subject_name,
                                    'subject_code' => $student_value->subject_code,
                                    'exam_assessments' => [
                                        $student_value->cbse_exam_assessment_type_id => [
                                            'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                            'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                            'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                            'maximum_marks' => $student_value->maximum_marks,
                                            'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                            'marks' => $student_value->marks,
                                            'note' => $student_value->note,
                                            'is_absent' => $student_value->is_absent,

                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
                if ($student_value->remarkexam_id == $remarkexam_id) {
                    $students[$student_value->student_id]['remark'] = $student_value->remark;
                }
            }
        }

        $data['result'] = $students;
        $data['gradeexam_id'] = $gradeexam_id;
        $data['remarkexam_id'] = $remarkexam_id;

        if (!empty($students)) {
            $student_allover_exam_rank = [];
            $subject_wise_rank = [];
            foreach ($students as $student_key => $student_value) {
                $grand_total_term_percentage = 0;
                $grand_total_exam_weight_percentage = 0;

                foreach ($subject_array as $subject_array_key => $subject_array_value) {
                    $subject_grand_total = 0;

                    $subject_total_weight_percentage = 0;

                    foreach ($exam_term_exam_assessment as $exam_key => $exam_value) {

                        $exam_subject_total = 0;
                        $exam_subject_maximum_total = 0;
                        foreach ($exam_value['exam_total_assessments'] as $exam_assessment_key => $exam_assessment_value) {

                            $subject_marks_array = getSubjectData($student_value, $exam_value['exam_id'], $subject_array_key, $exam_assessment_value['assesment_type_id']);

                            if (!$subject_marks_array['marks'] <= 0 || $subject_marks_array['marks'] == "N/A") {

                                $exam_subject_total += ($subject_marks_array['marks'] == "N/A") ? 0 : $subject_marks_array['marks'];
                                $exam_subject_maximum_total += $subject_marks_array['maximum_marks'];
                            } else {

                                $exam_subject_total += 0;
                                $exam_subject_maximum_total += 0;
                            }
                        }

                        $subject_percentage = getPercent($exam_subject_maximum_total, $exam_subject_total);
                        $subject_total_weight_percentage += ($subject_percentage * ($exam_value['weightage'] / 100));
                    }
                    if (!array_key_exists($subject_array_key, $subject_wise_rank)) {
                        $subject_wise_rank[$subject_array_key] = [];
                    }

                    $subject_wise_rank[$subject_array_key][] = [
                        'student_session_id' => $student_value['student_session_id'],
                        'rank_percentage' => $subject_total_weight_percentage,
                        'cbse_template_id' => $cbse_template_id,
                        'rank' => 0

                    ];

                    $grand_total_exam_weight_percentage += $subject_total_weight_percentage;
                }

                $overall_percentage = getPercent((count($subject_array) * 100), $grand_total_exam_weight_percentage);

                $student_allover_exam_rank[$student_value['student_session_id']] = [
                    'student_session_id' => $student_value['student_session_id'],
                    'cbse_template_id' => $cbse_template_id,
                    'rank_percentage' => $overall_percentage,
                    'rank' => 0,
                ];
            }

            // //-=====================start term calculation Rank=============

            $rank_overall_term_percentage_keys = array_column($student_allover_exam_rank, 'rank_percentage');

            array_multisort($rank_overall_term_percentage_keys, SORT_DESC, $student_allover_exam_rank);

            $term_rank_allover_list = unique_array($student_allover_exam_rank, "rank_percentage");

            foreach ($student_allover_exam_rank as $term_rank_key => $term_rank_value) {
                $student_allover_exam_rank[$term_rank_key]['rank'] = array_search($term_rank_value['rank_percentage'], $term_rank_allover_list);
            }

            //-=====================end term calculation Rank=============

            //=====================start subject term calculation Rank=============

            foreach ($subject_wise_rank as $subject_term_key => $subject_term_value) {

                $rank_overall_subject = array_column($subject_wise_rank[$subject_term_key], 'rank_percentage');

                array_multisort($rank_overall_subject, SORT_DESC, $subject_wise_rank[$subject_term_key]);

                $subject_rank_allover_list = unique_array($subject_wise_rank[$subject_term_key], "rank_percentage");

                foreach ($subject_wise_rank[$subject_term_key] as $subject_rank_key => $subject_rank_value) {

                    $subject_wise_rank[$subject_term_key][$subject_rank_key]['rank'] = array_search($subject_rank_value['rank_percentage'], $subject_rank_allover_list);
                }
            }

            $this->cbseexam_student_rank_model->add_rank($student_allover_exam_rank, $cbse_template_id, $subject_wise_rank);
        }
    }

    public function exam_wise_rank($cbse_template_id, $cbse_exam_id, $students)
    {
        $data['template'] = $this->cbseexam_template_model->get($cbse_template_id);
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['exam'] = $this->cbseexam_exam_model->getExamWithGrade($cbse_exam_id);
        $cbse_exam_result = $this->cbseexam_exam_model->getStudentExamResultByExamId($cbse_template_id, $cbse_exam_id, $students);
        $data['cbse_exam_result'] = $cbse_exam_result;
        $exam_assessments = [];
        $students = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {

            $exam_assessments[$student_value->cbse_exam_assessment_type_id] = $student_value->cbse_exam_assessment_type_id;

            if (array_key_exists($student_value->student_id, $students)) {

                if (!array_key_exists($student_value->subject_id, $students[$student_value->student_id]['term']['exams'][$student_value->id]['subjects'])) {

                    $new_subject = [
                        'subject_id' => $student_value->subject_id,
                        'subject_name' => $student_value->subject_name,
                        'subject_code' => $student_value->subject_code,
                        'exam_assessments' => [
                            $student_value->cbse_exam_assessment_type_id => [
                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                'maximum_marks' => $student_value->maximum_marks,
                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                'marks' => $student_value->marks,
                                'note' => $student_value->note,
                                'is_absent' => $student_value->is_absent,
                            ]
                        ]
                    ];

                    $students[$student_value->student_id]['term']['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_id]['term']['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

                    $new_assesment = [
                        'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                        'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                        'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                        'maximum_marks' => $student_value->maximum_marks,
                        'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                        'marks' => $student_value->marks,
                        'note' => $student_value->note,
                        'is_absent' => $student_value->is_absent,
                    ];
                    $students[$student_value->student_id]['term']['total_assessments'] += 1;
                    $students[$student_value->student_id]['term']['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                }
            } else {
                $students[$student_value->student_id] = [
                    'student_id' => $student_value->student_id,
                    'student_session_id' => $student_value->student_session_id,
                    'firstname' => $student_value->firstname,
                    'middlename' => $student_value->middlename,
                    'lastname' => $student_value->lastname,
                    'mobileno' => $student_value->mobileno,
                    'email' => $student_value->email,
                    'religion' => $student_value->religion,
                    'guardian_name' => $student_value->guardian_name,
                    'guardian_phone' => $student_value->guardian_phone,
                    'dob' => $student_value->dob,
                    'remark' => $student_value->remark,
                    'admission_no' => $student_value->admission_no,
                    'father_name' => $student_value->father_name,
                    'mother_name' => $student_value->mother_name,
                    'class_id' => $student_value->class_id,
                    'class' => $student_value->class,
                    'section_id' => $student_value->section_id,
                    'section' => $student_value->section,
                    'roll_no' => $student_value->roll_no,
                    'student_image' => $student_value->image,
                    'gender' => $student_value->gender,
                    'total_present_days' => $student_value->total_present_days,
                    'total_working_days' => $student_value->total_working_days,
                    'term' => [
                        'cbse_term_id' => $student_value->cbse_term_id,
                        'cbse_term_name' => $student_value->cbse_term_name,
                        'cbse_term_code' => $student_value->cbse_term_code,
                        'total_assessments' => 1,
                        'exams' => [
                            $student_value->id => [
                                'name' => $student_value->name,
                                'subjects' => [
                                    $student_value->subject_id => [
                                        'subject_id' => $student_value->subject_id,
                                        'subject_name' => $student_value->subject_name,
                                        'subject_code' => $student_value->subject_code,
                                        'exam_assessments' => [
                                            $student_value->cbse_exam_assessment_type_id => [
                                                'cbse_exam_assessment_type_name' => $student_value->cbse_exam_assessment_type_name,
                                                'cbse_exam_assessment_type_id' => $student_value->cbse_exam_assessment_type_id,
                                                'cbse_exam_assessment_type_code' => $student_value->cbse_exam_assessment_type_code,
                                                'maximum_marks' => $student_value->maximum_marks,
                                                'cbse_student_subject_marks_id' => $student_value->cbse_student_subject_marks_id,
                                                'marks' => $student_value->marks,
                                                'note' => $student_value->note,
                                                'is_absent' => $student_value->is_absent,

                                            ]
                                        ]
                                    ]
                                ]

                            ]
                        ]
                    ]
                ];
            }
        }

        $data['result'] = $students;
        $data['exam_assessments'] = $exam_assessments;
        //========================calculate Rank=======================

        if (!empty($students)) {
            $student_allover_rank = [];
            $subject_rank = [];
            foreach ($students as $student_key => $student_value) {
                $total_max_marks = 0;
                $total_gain_marks = 0;

                foreach ($student_value['term']['exams'] as $student_exam_key => $student_exam_value) {
                    foreach ($student_exam_value['subjects'] as $subject_key => $subject_value) {
                        $subject_total = 0;
                        $subject_max_total = 0;

                        foreach ($subject_value['exam_assessments'] as $assessment_key => $assessment_value) {
                            $subject_total += $assessment_value['marks'];
                            $subject_max_total += $assessment_value['maximum_marks'];

                            $total_gain_marks += $assessment_value['marks'];
                            $total_max_marks += $assessment_value['maximum_marks'];
                        }
                        if (!array_key_exists($subject_key, $subject_rank)) {
                            $subject_rank[$subject_key] = [];
                        }

                        $subject_rank[$subject_key][] = [
                            'student_session_id' => $student_value['student_session_id'],
                            'rank_percentage' => $subject_total,
                            'cbse_template_id' => $cbse_template_id,
                            'rank' => 0

                        ];
                    }
                }

                $exam_percentage = getPercent($total_max_marks, $total_gain_marks);

                $student_allover_rank[$student_value['student_session_id']] = [
                    'student_session_id' => $student_value['student_session_id'],
                    'cbse_template_id' => $cbse_template_id,
                    'rank_percentage' => $exam_percentage,
                    'rank' => 0,
                ];
            }

            //-=====================start term calculation Rank=============

            $rank_overall_percentage_keys = array_column($student_allover_rank, 'rank_percentage');

            array_multisort($rank_overall_percentage_keys, SORT_DESC, $student_allover_rank);

            $term_rank_allover_list = unique_array($student_allover_rank, "rank_percentage");

            foreach ($student_allover_rank as $term_rank_key => $term_rank_value) {

                $student_allover_rank[$term_rank_key]['rank'] = array_search($term_rank_value['rank_percentage'], $term_rank_allover_list);
            }

            //-=====================end term calculation Rank=============

            foreach ($subject_rank as $subject_term_key => $subject_term_value) {

                $rank_overall_subject = array_column($subject_rank[$subject_term_key], 'rank_percentage');

                array_multisort($rank_overall_subject, SORT_DESC, $subject_rank[$subject_term_key]);

                $subject_rank_allover_list = unique_array($subject_rank[$subject_term_key], "rank_percentage");

                foreach ($subject_rank[$subject_term_key] as $subject_rank_key => $subject_rank_value) {

                    $subject_rank[$subject_term_key][$subject_rank_key]['rank'] = array_search($subject_rank_value['rank_percentage'], $subject_rank_allover_list);
                }
            }

            $this->cbseexam_student_rank_model->add_rank($student_allover_rank, $cbse_template_id, $subject_rank);
        }
        //===============================================
    }

    public function rank_ajax()
    {
        $this->form_validation->set_rules('template', $this->lang->line('template'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {
            $data['sch_setting'] = $this->sch_setting_detail;
            $class_section_id = $this->input->post('class_section_id');
            $template = $this->input->post('template');
            $data['studentList'] = $this->cbseexam_result_model->getTemplateStudents($template);
            $data['cbse_template_id'] = $template;

            $page = $this->load->view('cbseexam/exam/_templatewiserank', $data, true);
            $array = array('status' => 1, 'error' => '', 'page' => $page);
        } else {

            $msg = array(

                'template' => form_error('template'),
            );

            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        }
        echo json_encode($array);
    }

    public function rankgenerate()
    {
        $this->form_validation->set_rules('cbse_template_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('student_session_id[]', $this->lang->line('class'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {

            $cbse_template_id = $this->input->post('cbse_template_id');
            $student_session_ids = $this->input->post('student_session_id');
            $template = $this->cbseexam_template_model->get($cbse_template_id);

            if ($template['marksheet_type'] == "exam_wise") {
                $cbse_temp_term_exam = $this->cbseexam_exam_model->getTemplateSingleExam($cbse_template_id);

                $return_page = $this->exam_wise_rank($cbse_template_id, $cbse_temp_term_exam->cbse_exam_id, $student_session_ids);
            } elseif ($template['marksheet_type'] == "without_term") {

                $return_page = $this->multi_exam_without_term($cbse_template_id, $student_session_ids);
            } elseif ($template['marksheet_type'] == "all_term") {

                $return_page = $this->all_term($cbse_template_id, $student_session_ids);
            } elseif ($template['marksheet_type'] == "term_wise") {

                $return_page = $this->term_wise($cbse_template_id, $student_session_ids);
            }

            $array = array('status' => 1, 'msg' => 'Record updated Successfully --r');
        } else {

            $msg = array(
                'cbse_template_id' => form_error('cbse_template_id'),
                'student_session_id[]' => form_error('student_session_id[]')
            );

            $array = array('status' => 0, 'error' => $msg, 'message' => '');
        }
        echo json_encode($array);
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('exam_term_id', $this->lang->line('term'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section[]', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_name', $this->lang->line('exam_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('grade_id', $this->lang->line('grade'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('assessment_id', $this->lang->line('assessment'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $msg['term'] = form_error('exam_term_id');
            $msg['class_id'] = form_error('class_id');
            $msg['section'] = form_error('section[]');
            $msg['exam_name'] = form_error('exam_name');
            $msg['grade_id'] = form_error('grade_id');
            $msg['assessment_id'] = form_error('assessment_id');

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            if (isset($_POST['is_publish'])) {
                $is_publish = 1;
            } else {
                $is_publish = 0;
            }

            if (isset($_POST['is_active'])) {
                $is_active = 1;
            } else {
                $is_active = 0;
            }
            $data = array(
                'cbse_term_id' => $this->input->post('exam_term_id'),
                'cbse_exam_assessment_id' => $this->input->post('assessment_id'),
                'cbse_exam_grade_id' => $this->input->post('grade_id'),
                'name' => $this->input->post('exam_name'),
                'description' => $this->input->post('exam_description'),
                'is_active' => $is_active,
                'is_publish' => $is_publish,
                'created_by' => $this->customlib->getStaffID(),
                'session_id' => $this->current_session,
            );

            if (!empty($_POST['section'])) {
                $exam_id = $this->cbseexam_exam_model->add($data);
                foreach ($_POST['section'] as $key => $value) {
                    $exam_class_section = array(
                        'cbse_exam_id' => $exam_id,
                        'class_section_id' => $value,
                    );
                    $this->cbseexam_exam_model->add_exam_class_section($exam_class_section);
                }

                //================
                if (isset($_POST['is_publish'])) {
                    $exam = $this->cbseexam_exam_model->get_exambyId($this->input->post('exam_id'));
                    $exam_students = $this->cbseexam_exam_model->get_examstudents($this->input->post('exam_id'));

                    $student_exams = array('exam' => $exam, 'exam_result' => $exam_students);
                    $this->cbse_mail_sms->mailsms('cbse_exam_result', $student_exams);
                }
                //=================
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $msg['section'] = $this->lang->line('please_select_atleast_one_section');

                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }
        }

        echo json_encode($array);
    }

    public function get_editdetails()
    {
        $id = $this->input->post('id');
        $result = $this->cbseexam_exam_model->get_editdetails($id);
        echo json_encode($result);
    }

    public function remove($id)
    {
        if (!$this->rbac->hasPrivilege('cbse_exam', 'can_delete')) {
            access_denied();
        }
        $this->cbseexam_exam_model->remove($id);
        redirect('cbseexam/exam');
    }

    public function add_exam()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam', 'can_add')) {
            access_denied();
        }

        $data = array();
        $id = $this->input->post('id');
        $result = $this->cbseexam_exam_model->get_exambyId($id);
        $data['result'] = $result;
        $data['delete_string'] = $this->input->post('delete_string');
        echo json_encode($this->load->view("cbseexam/exam/_add_exam", $data, true));
    }

    public function entrystudents()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('exam_id', $this->lang->line('exam'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array('exam_id' => form_error('exam_id'));
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {

            $check_alreay_inserted_students = array();
            $state = 1;
            $exam_id = $this->input->post('exam_id');
            $student_session = $this->input->post('student_session_id');
            $all_students = $this->input->post('all_students');
            $insert_array = array();
            if (isset($student_session) && !empty($student_session)) {
                foreach ($student_session as $student_key => $student_value) {
                    $check_alreay_inserted_students[] = $this->input->post('student_' . $student_value);
                    $insert_array[] = array(
                        'cbse_exam_id' => $exam_id,
                        'student_session_id' => $student_value,
                    );
                }
            }

            $this->cbseexam_exam_model->add_student($insert_array, $exam_id, $all_students);
            $array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('success_message'));

            echo json_encode($array);
        }
    }

    public function addexamsubject()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_subjects', 'can_edit')) {
            access_denied();
        }

        $student_id = '';
        $this->form_validation->set_rules('exam_id', $this->lang->line('exam') . " " . $this->lang->line('id'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('rows[]', $this->lang->line('subject'), 'trim|required|xss_clean');
        $rows = $this->input->post('rows');
        if (isset($rows) && !empty($rows)) {
            foreach ($rows as $row_key => $row_value) {
                if (
                    $this->input->post('subject_' . $row_value) == "" ||
                    $this->input->post('time_from' . $row_value) == "" ||
                    $this->input->post('duration' . $row_value) == "" ||
                    $this->input->post('room_no_' . $row_value) == "" ||
                    $this->input->post('date_from_' . $row_value) == ""
                ) {
                    $this->form_validation->set_rules('parameter', 'parameter', 'trim|required|xss_clean', array('required' => $this->lang->line('fields_values_required')));
                }
            }
        }

        if ($this->form_validation->run() == false) {

            $msg = array(

                'parameter' => form_error('parameter'),
                'exam_id' => form_error('exam_id'),
                'rows' => form_error('rows[]'),
            );

            $array = array('status' => '0', 'error' => $msg, 'message' => '');
        } else {
            $insert_array = array();
            $update_array = array();
            $subject_array = array();
            $not_be_del = array();

            $rows = $this->input->post('rows');
            foreach ($rows as $row_key => $row_value) {

                $update_id = $this->input->post('prev_row[' . $row_value . ']');
                if ($update_id == 0) {

                    if ($this->input->post('exam_id') != "" && $this->input->post('subject_' . $row_value) != "" && $this->input->post('date_from_' . $row_value) != "" && $this->input->post('time_from' . $row_value) != "" && $this->input->post('duration' . $row_value) != "") {

                        $insert_array[] = array(
                            'cbse_exam_id' => $this->input->post('exam_id'),
                            'subject_id' => $this->input->post('subject_' . $row_value),
                            'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_from_' . $row_value))),
                            'time_from' => $this->input->post('time_from' . $row_value),
                            'duration' => $this->input->post('duration' . $row_value),
                            'room_no' => $this->input->post('room_no_' . $row_value),
                        );
                    }
                } else {
                    $not_be_del[] = $update_id;
                    $update_array[] = array(
                        'id' => $update_id,
                        'cbse_exam_id' => $this->input->post('exam_id'),
                        'subject_id' => $this->input->post('subject_' . $row_value),
                        'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date_from_' . $row_value))),
                        'time_from' => $this->input->post('time_from' . $row_value),
                        'duration' => $this->input->post('duration' . $row_value),
                        'room_no' => $this->input->post('room_no_' . $row_value),
                    );
                }
            }

            $this->cbseexam_exam_model->add_examsubject($insert_array, $update_array, $not_be_del, $this->input->post('exam_id'));

            $array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function getSubjectByExam()
    {
        $exam_id = $this->input->post('recordid');
        $data['examDetail'] = $this->cbseexam_exam_model->getexamdetails($exam_id);
        $data['exam_subjects'] = $this->cbseexam_exam_model->getexamsubjects($exam_id);
        $data['batch_subjects'] = $this->subject_model->get();
        $data['exam_id'] = $exam_id;
        $data['exam_subjects_count'] = count($data['exam_subjects']);
        $data['subject_page'] = $this->load->view('cbseexam/exam/_getSubjectByExam', $data, true);
        echo json_encode($data);
    }

    public function subjectstudent()
    {
        $data['timetable_id'] = $this->input->post('timetable_id');
        $data['subject_id'] = $this->input->post('subject_id');
        $data['exam_id'] = $this->input->post('exam_id');
        $examdetails = $this->cbseexam_exam_model->get_exambyId($data['exam_id']);
        $data['exam'] = $examdetails;
        $resultlist = $this->cbseexam_exam_model->get_markexamstudents($data['timetable_id']);
        $data['resultlist'] = $resultlist;
        $data['exam_assessment_types'] = $this->cbseexam_exam_model->get_exam_assessment_types($examdetails['cbse_exam_assessment_id']);
        $subject_detail = $this->batchsubject_model->getExamSubject($data['subject_id']);
        $data['subject_detail'] = $subject_detail;
        $data['sch_setting'] = $this->sch_setting_detail;
        $student_exam_page = $this->load->view('cbseexam/exam/_partialstudentmarkEntry', $data, true);
        $array = array('status' => '1', 'error' => '', 'page' => $student_exam_page);
        echo json_encode($array);
    }

    public function teacherRemark()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_teacher_remark', 'can_view')) {
            access_denied();
        }

        $data['exam_id'] = $this->input->post('exam_id');
        $data['resultlist'] = $this->cbseexam_exam_model->get_teacher_remark($data['exam_id']);
        $data['sch_setting'] = $this->sch_setting_detail;
        $student_exam_page = $this->load->view('cbseexam/exam/_teacherRemark', $data, true);
        $array = array('status' => '1', 'error' => '', 'page' => $student_exam_page);
        echo json_encode($array);
    }

    public function entrymarks()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_marks', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('exam_student_id[]', $this->lang->line('subject'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $data = array('subject_id' => form_error('exam_student_id'));
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {

            $cbse_exam_timetable_id = $this->input->post('cbse_exam_timetable_id');
            $insert_array = [];

            foreach ($this->input->post('exam_student_id') as $key => $exam_student_id) {
                $note = $this->input->post('exam_student_note');
                $new_nt = $note[$exam_student_id];
                foreach ($_POST['mark'][$exam_student_id] as $assement_key => $assement_value) {

                    $absent = 0;
                    if (isset($_POST['absent'][$exam_student_id][$assement_key])) {
                        $absent = 1;
                        $assement_value = 0;
                    }

                    $insert_array[] = array(
                        'cbse_exam_timetable_id' => $cbse_exam_timetable_id,
                        'cbse_exam_student_id' => $exam_student_id,
                        'cbse_exam_assessment_type_id' => $assement_key,
                        'marks' => $assement_value,
                        'is_absent' => $absent,
                        'note' => $new_nt,
                    );
                }
            }

            $this->cbseexam_exam_model->addresultmark_data($insert_array, $cbse_exam_timetable_id);
            $array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function exam_attendance()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_attendance', 'can_view')) {
            access_denied();
        }

        $data['exam_id'] = $this->input->post('exam_id');
        $data['exam'] = $this->cbseexam_exam_model->get_exambyId($this->input->post('exam_id'));
        $resultlist = $this->cbseexam_exam_model->get_examstudents($data['exam_id']);
        $data['resultlist'] = $resultlist;
        $data['sch_setting'] = $this->sch_setting_detail;
        $student_exam_page = $this->load->view('cbseexam/exam/_exam_attendancestudent', $data, true);
        $array = array('status' => '1', 'error' => '', 'page' => $student_exam_page);
        echo json_encode($array);
    }

    public function get_observation_parameter()
    {
        $data['exam_id'] = $this->input->post('exam_id');
        $resultlist = $this->cbseexam_exam_model->get_observation_parameter($data['exam_id']);
        $data['resultlist'] = $resultlist;
        $data['sch_setting'] = $this->sch_setting_detail;
        $student_exam_page = $this->load->view('cbseexam/exam/_add_observation_marks', $data, true);
        $array = array('status' => '1', 'error' => '', 'page' => $student_exam_page);
        echo json_encode($array);
    }

    public function addattendance()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_attendance', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('total_working_days', $this->lang->line('total_attendance_days'), 'trim|numeric|required|xss_clean|greater_than[0]', array("greater_than" => $this->lang->line('total_attendance_days_should_be_greater_than_zero')));
        $total_present_days = $this->input->post('total_present_days');
        $total_working_days = $this->input->post('total_working_days');
        if (!isset($total_present_days)) {
            $this->form_validation->set_rules('total_present_days', $this->lang->line('total_present_days'), 'trim|numeric|required');
        } elseif (!empty($total_present_days)) {
            foreach ($total_present_days as $present_key => $present_value) {
                if ($total_working_days != "" || $present_value == "") {
                    $this->form_validation->set_rules('total_present_days', $this->lang->line('total_present_days'), "callback_check_teacher_remark[" . $present_value . "]");
                    break;
                }
            }
        }

        if ($this->form_validation->run() == false) {

            $msg = array(
                'total_working_days' => form_error('total_working_days'),
                'total_present_days' => form_error('total_present_days'),
            );

            $array = array('status' => '0', 'error' => $msg, 'message' => '');
        } else {

            $exam_id = $this->input->post('exam_id');
            $total_working_days = $this->input->post('total_working_days');
            $exam_student_id = $this->input->post('exam_student_id');
            $total_present_days = $this->input->post('total_present_days');

            if ($exam_id) {

                $examdata = array(
                    'id' => $exam_id,
                    'total_working_days' => $total_working_days,
                );

                $this->cbseexam_exam_model->add($examdata);

                foreach ($exam_student_id as $key => $value) {

                    $savedata = array(

                        'id' => $value,
                        'total_present_days' => $total_present_days[$value],
                    );

                    $this->cbseexam_exam_model->addexamstudent($savedata);
                }
            }
            $array = array('status' => '1', 'error' => array(), 'message' => 'Save Attandance');
        }

        echo json_encode($array);
    }

    public function check_teacher_remark($field, $marks)
    {
        $total_working_days = $this->input->post('total_working_days');
        if ($marks == "") {
            $this->form_validation->set_message('check_teacher_remark', $this->lang->line('student_attandance_required'));
            return false;
        } elseif ($marks > $total_working_days) {
            $this->form_validation->set_message('check_teacher_remark', $this->lang->line('student_attandance_cant_be_greater_than_total_attendance_days'));
            return false;
        }
        return true;
    }

    public function addteacherremark()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_teacher_remark', 'can_edit')) {
            access_denied();
        }

        $exam_student_id = $this->input->post('exam_student_id');
        $teacher_remark = $this->input->post('teacher_remark');

        foreach ($exam_student_id as $key => $value) {

            $savedata = array(
                'id' => $value,
                'staff_id' => $this->customlib->getStaffID(),
                'remark' => $teacher_remark[$value],
            );
            $this->cbseexam_exam_model->addexamstudent($savedata);
        }

        $array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('success_message'));
        echo json_encode($array);
    }

    public function get_exam()
    {
        $data['term_list'] = $this->cbseexam_term_model->get();
        $data['classlist'] = $this->class_model->get();
        $data['assessment_result'] = $this->cbseexam_assessment_model->get();
        $data['grade_result'] = $this->cbseexam_grade_model->getgradelist();
        $exam_id = $this->input->post('exam_id');
        $data['result'] = $this->cbseexam_exam_model->get_exambyId($exam_id);
        $class_section_list = $this->cbseexam_exam_model->get_classsectionbyId($exam_id);
        $data['class_id'] = $class_section_list[0]['class_id'];
        $data['class_section_list'] = json_encode($class_section_list);
        $data['delete_string'] = $this->input->post('delete_string');
        $data['sch_setting'] = $this->sch_setting_detail;

        $page = $this->load->view('cbseexam/exam/_edit_exam', $data, true);
        $array = array('status' => '1', 'error' => '', 'page' => $page);
        echo json_encode($array);
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('exam_term_id', $this->lang->line('term'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_name', $this->lang->line('exam_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('grade_id', $this->lang->line('exam_grade'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

            $msg['exam_term_id'] = form_error('exam_term_id');
            $msg['class_id'] = form_error('class_id');
            $msg['exam_name'] = form_error('exam_name');
            $msg['grade_id'] = form_error('grade_id');

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {


            if (isset($_POST['is_publish'])) {
                $is_publish = 1;
            } else {
                $is_publish = 0;
            }

            if (isset($_POST['is_active'])) {
                $is_active = 1;
            } else {
                $is_active = 0;
            }

            $data = array(
                'id' => $this->input->post('exam_id'),
                'cbse_term_id' => $this->input->post('exam_term_id'),
                'cbse_exam_assessment_id' => $this->input->post('assessment_id'),
                'cbse_exam_grade_id' => $this->input->post('grade_id'),
                'name' => $this->input->post('exam_name'),
                'description' => $this->input->post('exam_description'),
                'is_active' => $is_active,
                'is_publish' => $is_publish,
                'created_by' => $this->customlib->getStaffID(),
                'session_id' => $this->current_session,
            );


            $this->cbseexam_exam_model->add($data);
            if (!empty($_POST['section'])) {

                $this->cbseexam_exam_model->removeclasssection($this->input->post('exam_id'));

                foreach ($_POST['section'] as $key => $value) {
                    $exam_class_section = array(
                        'cbse_exam_id' => $this->input->post('exam_id'),
                        'class_section_id' => $value,
                    );

                    $this->cbseexam_exam_model->add_exam_class_section($exam_class_section);
                }
            }

            //================
            if (isset($_POST['is_publish'])) {
                $exam = $this->cbseexam_exam_model->get_exambyId($this->input->post('exam_id'));
                $exam_students = $this->cbseexam_exam_model->get_examstudents($this->input->post('exam_id'));

                $student_exams = array('exam' => $exam, 'exam_result' => $exam_students);



                $this->cbse_mail_sms->mailsms('cbse_exam_result', $student_exams);
            }
            //=================

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }

        echo json_encode($array);
    }

    public function deleteexam()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam', 'can_delete')) {
            access_denied();
        }

        $exam_id = $this->input->post('exam_id');
        $this->cbseexam_exam_model->remove_exam($exam_id);
        $array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('delete_message'));
        echo json_encode($array);
    }

    public function generate_rank()
    {
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/generate_rank');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('cbseexam/exam/generate_rank');
        $this->load->view('layout/footer');
    }


    public function examtimetable()
    {
        $data = [];
        $data['exams'] = $this->cbseexam_exam_model->getExamTimetable();
        $this->load->view('layout/header');
        $this->load->view('cbseexam/exam/examtimetable', $data);
        $this->load->view('layout/footer');
    }
}
