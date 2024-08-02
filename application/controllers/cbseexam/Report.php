<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Report extends MY_Addon_CBSEController
{

    public function __construct()
    {
        parent::__construct();

        $this->current_session    = $this->setting_model->getCurrentSession();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'reports/cbse_report');
        $this->session->set_userdata('subsub_menu', '');
        $this->load->view('layout/header');
        $this->load->view('cbseexam/report/index');
        $this->load->view('layout/footer');
    }

    public function examsubject()
    {
        if (!$this->rbac->hasPrivilege('subject_marks_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'reports/cbse_report');
        $this->session->set_userdata('subsub_menu', 'cbse_exam/examsubject');

        $data['exams'] = $this->cbseexam_exam_model->getexamlist();
        $this->form_validation->set_rules('exam_id', $this->lang->line('exam'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {
            $exam_id          = $this->input->post('exam_id');
            $subjects         = $this->cbseexam_exam_model->getexamsubjects($exam_id);
            $exam             = $this->cbseexam_exam_model->getExamWithGrade($exam_id);
            $exam_assessments = $this->cbseexam_assessment_model->getWithAssessmentTypeByAssessmentID($exam->cbse_exam_assessment_id);

            $data['exam']             = $exam;
            $data['subjects']         = $subjects;
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
                            'rank' => $student_value->rank,
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
        }
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/report/examsubject', $data);
        $this->load->view('layout/footer', $data);
    }

    public function templatewise()
    {
        if (!$this->rbac->hasPrivilege('template_marks_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'reports/cbse_report');
        $this->session->set_userdata('subsub_menu', 'cbse_exam/templatewise');
        $data              = array();
        $class             = $this->class_model->get();
        $data['classlist'] = $class;
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/report/templatewise', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getTemplatewiseExams()
    {
        $this->form_validation->set_rules('template_id', $this->lang->line('template_id'), 'required|trim|xss_clean');
        $data = array();
        if ($this->form_validation->run() == false) {
            $msg = array(
                'template_id' => form_error('template_id'),
            );
            $array = array('status' => 0, 'error' => $msg);
            echo json_encode($array);
        } else {
            $template_id = $this->input->post('template_id');
        }
    }

    public function getTermTemplateWise()
    {
        $template_id           = $this->input->post('template_id');
        $data['template_data'] = $this->cbseexam_template_model->getTemplateTermsOrExam($template_id);
        $page                  = $this->load->view('cbseexam/report/_getTermTemplateWise', $data, true);
        echo json_encode(['status' => 1, 'page' => $page]);
    }

    public function getTemplateWiseResult()
    {
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('class_section_id', $this->lang->line('section'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('template_id', $this->lang->line('template'), 'required|trim|xss_clean');
        $data = array();

        if ($this->form_validation->run() == false) {
            $msg = array(
                'class_id' => form_error('class_id'),
                'class_section_id' => form_error('class_section_id'),
                'template_id' => form_error('template_id'),
            );
            $array = array('status' => 0, 'error' => $msg);
            echo json_encode($array);
        } else {
            $template_id = $this->input->post('template_id');
            $class_section_id = $this->input->post('class_section_id');
            $template    = $this->cbseexam_template_model->get($template_id);
           
            if ($template['marksheet_type'] == "without_term") {
                $page = $this->getMultiexam($template_id,$class_section_id);
                echo json_encode(['status' => 1, 'page' => $page['pg']]);
            } elseif ($template['marksheet_type'] == "exam_wise") {

                $tem  = $this->cbseexam_exam_model->getTemplateSingleExam($template_id);
                $page = $this->getSingleExam($tem->cbse_exam_id,$class_section_id);
                echo json_encode(['status' => 1, 'page' => $page['pg']]);
            } elseif ($template['marksheet_type'] == "all_term") {

                $page = $this->getMultiTerm($template_id,$class_section_id);

                echo json_encode(['status' => 1, 'page' => $page['pg']]);
            } elseif ($template['marksheet_type'] == "term_wise") {
                $page = $this->getSinglTerm($template_id,$class_section_id);
                echo json_encode(['status' => 1, 'page' => $page['pg']]);
            }
        }
    }

    public function getSinglTerm($cbse_template_id,$class_section_id) //multiple exam in single term
    {
        $data['terms'] = $this->cbseexam_template_model->getTemplateWithTermWithExams($cbse_template_id);
        $cbse_exam_result = $this->cbseexam_exam_model->getResultTermwiseByTemplateId($cbse_template_id,$class_section_id);
        $template_subjects         = $this->cbseexam_exam_model->getTemplateAssessment($cbse_template_id);
        $subject_array             = [];
        $exam_term_exam_assessment = [];
        $gradeexam_id              = "";
        $remarkexam_id             = "";

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {
            $subject_array[$cbse_exam_result_value->subject_id] = $cbse_exam_result_value->subject_name . " (" . $cbse_exam_result_value->subject_code . ")";
        }

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {
            if ((!array_key_exists($cbse_exam_result_value->cbse_term_id, $exam_term_exam_assessment))) {
                $assessment_array = $this->getExamAssesmentByTerm($template_subjects, $cbse_exam_result_value->cbse_term_id);
                $new_terms = [
                    'cbse_term_id'           => $cbse_exam_result_value->cbse_term_id,
                    'cbse_term_name'         => $cbse_exam_result_value->cbse_term_name,
                    'cbse_term_code'         => $cbse_exam_result_value->cbse_term_code,
                    'term_total_assessments' => $assessment_array,
                    'exams'                  => [],

                ];
                $exam_term_exam_assessment[$cbse_exam_result_value->cbse_term_id] = $new_terms;
            }
        }

        foreach ($template_subjects as $sub_key => $sub_value) {
            if (!array_key_exists($sub_value->cbse_exam_id, $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'])) {
                $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'][$sub_value->cbse_exam_id] = [
                    'cbse_exam_id'     => $sub_value->cbse_exam_id,
                    'exam_name'        => $sub_value->exam_name,
                    'weightage'        => $sub_value->weightage,
                    'exam_assessments' => [[

                        'cbse_exam_assessment_id'      => $sub_value->cbse_exam_assessment_id,
                        'cbse_exam_assessment_type_id' => $sub_value->cbse_exam_assessment_type_id,
                        'name'                         => $sub_value->name,
                        'code'                         => $sub_value->code,
                        'maximum_marks'                => $sub_value->maximum_marks,
                        'pass_percentage'              => $sub_value->pass_percentage,

                    ]],

                ];
            } else {
                $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'][$sub_value->cbse_exam_id]['exam_assessments'][] = [

                    'cbse_exam_assessment_id'      => $sub_value->cbse_exam_assessment_id,
                    'cbse_exam_assessment_type_id' => $sub_value->cbse_exam_assessment_type_id,
                    'name'                         => $sub_value->name,
                    'code'                         => $sub_value->code,
                    'maximum_marks'                => $sub_value->maximum_marks,
                    'pass_percentage'              => $sub_value->pass_percentage,

                ];
            }
        }

        $data['subject_array']             = $subject_array;
        $data['exam_term_exam_assessment'] = $exam_term_exam_assessment;
        $students                          = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {

            $gradeexam_id  = $student_value->gradeexam_id;
            $remarkexam_id = $student_value->remarkexam_id;

            if (array_key_exists($student_value->student_session_id, $students)) {

                if (!array_key_exists($student_value->cbse_term_id, $students[$student_value->student_session_id]['terms'])) {

                    $new_cbse_term_id = [

                        'cbse_term_id'           => $student_value->cbse_term_id,
                        'cbse_term_name'         => $student_value->cbse_term_name,
                        'cbse_term_code'         => $student_value->cbse_term_code,
                        'cbse_term_weight'       => $student_value->cbse_template_terms_weightage,
                        'term_total_assessments' => 1,

                        'exams'                  => [
                            $student_value->id => [
                                'name'               => $student_value->name,
                                'total_assessments'  => 1,
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
                            ],
                        ],
                    ];

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id] = $new_cbse_term_id;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->id, $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'])) {

                    $new_exam = [
                        'name'               => $student_value->name,
                        'total_assessments'  => 1,
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

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id] = $new_exam;
                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->subject_id, $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'])) {

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

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

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

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;
                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['total_assessments'] = count($students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments']);
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
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
                    'rank'               => $student_value->rank,
                    'terms'              => [
                        $student_value->cbse_term_id => [

                            'cbse_term_id'           => $student_value->cbse_term_id,
                            'cbse_term_name'         => $student_value->cbse_term_name,
                            'cbse_term_code'         => $student_value->cbse_term_code,
                            'cbse_term_weight'       => $student_value->cbse_template_terms_weightage,
                            'term_total_assessments' => 1,

                            'exams'                  => [
                                $student_value->id => [
                                    'name'               => $student_value->name,
                                    'total_assessments'  => 1,
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
                                ],
                            ],
                        ],
                    ],
                ];
                if ($student_value->remarkexam_id == $remarkexam_id) {
                    $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                }
            }
        }


        $data['result']        = $students;
        $data['gradeexam_id']  = $gradeexam_id;
        $data['remarkexam_id'] = $remarkexam_id;
        $data['exam_grades'] = $this->cbseexam_grade_model->getExamGrades($gradeexam_id);
        $result_page = $this->load->view('cbseexam/report/_printsingleterm', $data, true);
        return array('pg' => $result_page);
    }

    public function getMultiTerm($cbse_template_id,$class_section_id) //for multiple terms
    {

        $data['terms'] = $this->cbseexam_template_model->getTermByTemplateId($cbse_template_id);


        $cbse_exam_result = $this->cbseexam_exam_model->getResultTermwiseByTemplateIdWithSelectedTerm($cbse_template_id,$class_section_id);


        $template_subjects         = $this->cbseexam_exam_model->getTemplateAssessment($cbse_template_id);
        $subject_array             = [];
        $exam_term_exam_assessment = [];
        $gradeexam_id              = "";
        $remarkexam_id             = "";

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {

            $subject_array[$cbse_exam_result_value->subject_id] = $cbse_exam_result_value->subject_name . " (" . $cbse_exam_result_value->subject_code . ")";
        }

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {

            if ((!array_key_exists($cbse_exam_result_value->cbse_term_id, $exam_term_exam_assessment))) {

                $assessment_array = $this->getExamAssesmentByTerm($template_subjects, $cbse_exam_result_value->cbse_term_id);

                $new_terms = [

                    'cbse_term_id'           => $cbse_exam_result_value->cbse_term_id,
                    'cbse_term_name'         => $cbse_exam_result_value->cbse_term_name,
                    'cbse_term_code'         => $cbse_exam_result_value->cbse_term_code,
                    'cbse_term_weight'       => $cbse_exam_result_value->weightage,
                    'term_total_assessments' => $assessment_array,
                    'exams'                  => [],

                ];
                $exam_term_exam_assessment[$cbse_exam_result_value->cbse_term_id] = $new_terms;
            }
        }

        foreach ($template_subjects as $sub_key => $sub_value) {

            if (!array_key_exists($sub_value->cbse_exam_id, $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'])) {
                $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'][$sub_value->cbse_exam_id] = [
                    'cbse_exam_id'     => $sub_value->cbse_exam_id,
                    'exam_name'        => $sub_value->exam_name,
                    'exam_assessments' => [[
                        'cbse_exam_assessment_id'      => $sub_value->cbse_exam_assessment_id,
                        'cbse_exam_assessment_type_id' => $sub_value->cbse_exam_assessment_type_id,
                        'name'                         => $sub_value->name,
                        'code'                         => $sub_value->code,
                        'maximum_marks'                => $sub_value->maximum_marks,
                        'pass_percentage'              => $sub_value->pass_percentage,

                    ]],

                ];
            } else {
                $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'][$sub_value->cbse_exam_id]['exam_assessments'][] = [

                    'cbse_exam_assessment_id'      => $sub_value->cbse_exam_assessment_id,
                    'cbse_exam_assessment_type_id' => $sub_value->cbse_exam_assessment_type_id,
                    'name'                         => $sub_value->name,
                    'code'                         => $sub_value->code,
                    'maximum_marks'                => $sub_value->maximum_marks,
                    'pass_percentage'              => $sub_value->pass_percentage,
                ];
            }
        }

        $data['subject_array']             = $subject_array;
        $data['exam_term_exam_assessment'] = $exam_term_exam_assessment;
      
        $students                          = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {
            $gradeexam_id  = $student_value->gradeexam_id;
            $remarkexam_id = $student_value->remarkexam_id;

            if (array_key_exists($student_value->student_session_id, $students)) {

                if (!array_key_exists($student_value->cbse_term_id, $students[$student_value->student_session_id]['terms'])) {

                    $new_cbse_term_id = [

                        'cbse_term_id'           => $student_value->cbse_term_id,
                        'cbse_term_name'         => $student_value->cbse_term_name,
                        'cbse_term_code'         => $student_value->cbse_term_code,
                        'cbse_term_weight'       => $student_value->cbse_template_terms_weightage,
                        'term_total_assessments' => 1,
                        'exams'                  => [
                            $student_value->id => [
                                'name'               => $student_value->name,
                                'total_assessments'  => 1,
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
                            ],
                        ],
                    ];

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id] = $new_cbse_term_id;
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->id, $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'])) {

                    $new_exam = [
                        'name'               => $student_value->name,
                        'total_assessments'  => 1,
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

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id] = $new_exam;
                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->subject_id, $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'])) {

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
                                'is_absent'                      => $student_value->is_absent
                            ],
                        ],
                    ];

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

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

                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['term_total_assessments'] += 1;
                    $students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['total_assessments'] = count($students[$student_value->student_session_id]['terms'][$student_value->cbse_term_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments']);
                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
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
                    'rank'               => $student_value->rank,
                    'terms'              => [
                        $student_value->cbse_term_id => [

                            'cbse_term_id'           => $student_value->cbse_term_id,
                            'cbse_term_name'         => $student_value->cbse_term_name,
                            'cbse_term_code'         => $student_value->cbse_term_code,
                            'cbse_term_weight'       => $student_value->cbse_template_terms_weightage,
                            'term_total_assessments' => 1,

                            'exams'                  => [
                                $student_value->id => [
                                    'name'               => $student_value->name,
                                    'total_assessments'  => 1,
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
                                ],
                            ],
                        ],
                    ],
                ];
                if ($student_value->remarkexam_id == $remarkexam_id) {
                    $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                }
            }
        }

        $data['result']        = $students;
        $data['gradeexam_id']  = $gradeexam_id;
        $data['remarkexam_id'] = $remarkexam_id;
        $data['exam_grades']   = $this->cbseexam_grade_model->getExamGrades($gradeexam_id);

        // print_r($students);
        // exit();
        $result_page = $this->load->view('cbseexam/report/_printmultiterm', $data, true);
        return array('pg' => $result_page);
    }

    public function getSingleExam($exam_id,$class_section_id)
    {
        $subjects         = $this->cbseexam_exam_model->getexamsubjects($exam_id);
        $exam             = $this->cbseexam_exam_model->getExamWithGrade($exam_id);
        $exam_assessments = $this->cbseexam_assessment_model->getWithAssessmentTypeByAssessmentID($exam->cbse_exam_assessment_id);
        $data['exam']             = $exam;
        $data['subjects']         = $subjects;
        $data['exam_assessments'] = $exam_assessments;
        $cbse_template_id = $this->input->post('template_id');

        $students         = [];
        $cbse_exam_result = $this->cbseexam_exam_model->getExamResultByExamIdByTemplate($exam_id, $cbse_template_id,$class_section_id);

        if (!empty($cbse_exam_result)) {

            foreach ($cbse_exam_result as $student_key => $student_value) {

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
                        'rank' => $student_value->rank,
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

        $result_page = $this->load->view('cbseexam/report/_printexam', $data, true);

        return array('pg' => $result_page);
    }

    public function getMultiexam($template_id,$class_section_id)
    {
        $template_subjects         = $this->cbseexam_exam_model->getTemplateAssessmentWithoutTerm($template_id);
        $cbse_exam_result          = $this->cbseexam_exam_model->getStudentResultByTemplateId($template_id,$class_section_id);
        $subject_array             = [];
        $exam_term_exam_assessment = [];
        $gradeexam_id              = "";
        $remarkexam_id             = "";
        $data['template']          = $this->cbseexam_template_model->getTemplateTermsOrExam($template_id);

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {

            $subject_array[$cbse_exam_result_value->subject_id] = $cbse_exam_result_value->subject_name . " (" . $cbse_exam_result_value->subject_code . ")";
        }

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {

            if ((!array_key_exists($cbse_exam_result_value->id, $exam_term_exam_assessment))) {

                $assessment_array = $this->getExamAssesment($template_subjects, $cbse_exam_result_value->id);

                $new_terms = [

                    'exam_id'                => $cbse_exam_result_value->id,
                    'exam_name'              => $cbse_exam_result_value->name,
                    'weightage'              => $cbse_exam_result_value->weightage,
                    'exam_total_assessments' => $assessment_array,

                ];
                $exam_term_exam_assessment[$cbse_exam_result_value->id] = $new_terms;
            }
        }

        $data['subject_array']             = $subject_array;
        $data['exam_term_exam_assessment'] = $exam_term_exam_assessment;
        $students                          = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {

            $gradeexam_id  = $student_value->gradeexam_id;
            $remarkexam_id = $student_value->remarkexam_id;

            if (array_key_exists($student_value->student_session_id, $students)) {

                if (!array_key_exists($student_value->id, $students[$student_value->student_session_id]['exams'])) {

                    $new_exam = [
                        'name'               => $student_value->name,
                        'total_assessments'  => 1,
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

                    $students[$student_value->student_session_id]['exams'][$student_value->id] = $new_exam;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->subject_id, $students[$student_value->student_session_id]['exams'][$student_value->id]['subjects'])) {

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

                    $students[$student_value->student_session_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id] = $new_subject;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
                } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students[$student_value->student_session_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'])) {

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

                    $students[$student_value->student_session_id]['exams'][$student_value->id]['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;

                    if ($student_value->remarkexam_id == $remarkexam_id) {
                        $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                    }
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
                    'rank'               => $student_value->rank,
                    'exams'              => [
                        $student_value->id => [
                            'name'               => $student_value->name,
                            'total_assessments'  => 1,
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
                        ],
                    ],
                ];
                if ($student_value->remarkexam_id == $remarkexam_id) {
                    $students[$student_value->student_session_id]['remark'] = $student_value->remark;
                }
            }
        }

        $data['result']        = $students;
        $data['gradeexam_id']  = $gradeexam_id;
        $data['remarkexam_id'] = $remarkexam_id;
        $data['exam_grades']   = $this->cbseexam_grade_model->getExamGrades($gradeexam_id);
        $result_page = $this->load->view('cbseexam/report/_printmultiexam', $data, true);
        return array('pg' => $result_page);
    }

    public function getExamAssesmentByTerm($array, $find_cbse_term_id)
    {
        $return_array = [];
        foreach ($array as $_arrry_key => $_arrry_value) {
            if ($_arrry_value->cbse_term_id == $find_cbse_term_id) {

                $return_array[] = [
                    'assesment_type_id'              => $_arrry_value->cbse_exam_assessment_type_id,
                    'assesment_type_name'            => $_arrry_value->name,
                    'assesment_type_code'            => $_arrry_value->code,
                    'assesment_type_maximum_marks'   => $_arrry_value->maximum_marks,
                    'assesment_type_pass_percentage' => $_arrry_value->pass_percentage,
                ];
            }
        }

        return $return_array;
    }

    public function getExamAssesment($array, $find_cbse_term_id)
    {
        $return_array = [];
        foreach ($array as $_arrry_key => $_arrry_value) {

            if ($_arrry_value->cbse_exam_id == $find_cbse_term_id) {

                $return_array[] = [
                    'assesment_type_id'              => $_arrry_value->cbse_exam_assessment_type_id,
                    'assesment_type_name'            => $_arrry_value->name,
                    'assesment_type_code'            => $_arrry_value->code,
                    'assesment_type_maximum_marks'   => $_arrry_value->maximum_marks,
                    'assesment_type_pass_percentage' => $_arrry_value->pass_percentage,
                ];
            }
        }

        return $return_array;
    }
}
