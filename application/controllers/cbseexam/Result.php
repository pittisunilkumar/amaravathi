<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Result extends MY_Addon_CBSEController
{

    public function __construct()
    {
        parent::__construct();
       
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function t()
    {
        $data = array();

        $html = $this->test_multi(7,[88]);

        print_r($html['pg']);
        exit();

        $data['template'] = $this->cbseexam_template_model->get('46');
        $this->load->library('m_pdf');
        $mpdf = $this->m_pdf->load('P');

        $stylesheet = file_get_contents(base_url() . 'backend/cbse_pdf_style.css'); // external css
        if ($data['template']['background_img'] != "") {

            $mpdf->SetDefaultBodyCSS('background', "url('" . base_url("/uploads/cbseexam/template/background_img/" . $data['template']['background_img']) . "')");
            $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
        }
        $mpdf->WriteHTML($stylesheet, 1); // Writing style to pdf
        $mpdf->SetWatermarkText($this->sch_setting_detail->name, .2);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->showWatermarkText = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->baseScript = 1;
        $mpdf->autoLangToFont = true;
        $mpdf->WriteHTML($html['pg'], \Mpdf\HTMLParserMode::HTML_BODY);
        $response = true;

        $content = $mpdf->Output(random_string() . '.pdf', 'I');
        return $content;

    }

    public function marksheet()
    {
       
        if (!$this->rbac->hasPrivilege('cbse_exam_print_marksheet', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/marksheet');
        $data = array();
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $data['marksheet'] = $this->cbseexam_result_model->marksheet_type();
        $data['title'] = 'Add Batch';
        $data['title_list'] = 'Recent Batch';
        $session = $this->session_model->get();
        $data['sessionlist'] = $session;
        $this->form_validation->set_rules('class_section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('template', $this->lang->line('template'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == true) {


            $class_section_id = $this->input->post('class_section_id');
            $template = $this->input->post('template');
            $data['studentList'] = $this->cbseexam_result_model->searchTemplateStudents($class_section_id, $template);
        }

        $data['sch_setting'] = $this->sch_setting_detail;
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/result/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function test_multi($cbse_template_id, $students) //for multiple terms
    {
        $data['template'] = $this->cbseexam_template_model->get($cbse_template_id);
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['current_setting']= $this->customlib->getCurrentSession();
        $cbse_template_subject_term_exam = $this->cbseexam_template_model->getTemplateTermExamWithAssessment($cbse_template_id);
        //======================
        $list_observation = $this->cbse_observation_term_model->getObservationByTemplate($cbse_template_id);
        $list_observation_new = [];
        foreach ($list_observation as $ls_observation_key => $ls_observation_value) {

            if (!array_key_exists($ls_observation_value->cbse_exam_observation_id, $list_observation_new)) {
                $list_observation_new[$ls_observation_value->cbse_exam_observation_id] = [
                    'cbse_exam_observation_id' => $ls_observation_value->cbse_exam_observation_id,
                    'cbse_observation_name' => $ls_observation_value->cbse_observation_name,
                    'cbse_observation_parameters' => $ls_observation_value->cbse_observation_parameters,
                    'cbse_terms' => [
                        [
                            'cbse_term_id' => $ls_observation_value->cbse_term_id,
                            'term_name' => $ls_observation_value->term_name
                        ]
                    ]

                ];
            } else {
                $list_observation_new[$ls_observation_value->cbse_exam_observation_id]['cbse_terms'][] = [
                    'cbse_term_id' => $ls_observation_value->cbse_term_id,
                    'term_name' => $ls_observation_value->term_name
                ];
            }
        }

        $data['list_observation'] = $list_observation_new;

        //====================
        $cbse_observation_parameter = $this->cbseexam_observation_parameter_model->getTermObservationParams($cbse_template_id);

        $data['cbse_observation_parameter'] = $cbse_observation_parameter;
        $cbse_term_parameter = $this->cbseexam_observation_parameter_model->getStudentTermObservation($cbse_template_id, $students);

        $student_observations = [];
        foreach ($cbse_term_parameter as $term_param_key => $term_param_value) {
            if (!array_key_exists($term_param_value->student_session_id, $student_observations)) {
                $student_observations[$term_param_value->student_session_id] = [
                    'terms' => [
                        $term_param_value->cbse_term_id => [
                            'observations' => [
                                $term_param_value->cbse_exam_observation_id => [
                                    $term_param_value->cbse_observation_parameter_id => [

                                        'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                                        'maximum_marks' => $term_param_value->maximum_marks,
                                        'student_session_id' => $term_param_value->student_session_id,
                                        'obtain_marks' => $term_param_value->obtain_marks,
                                        'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                                    ]
                                ]

                            ]
                        ]

                    ]

                ];
            } else {

                if (!array_key_exists($term_param_value->cbse_term_id, $student_observations[$term_param_value->student_session_id]['terms'])) {

                    $new_param_terms = [
                        $term_param_value->cbse_exam_observation_id => [
                            $term_param_value->cbse_observation_parameter_id => [

                                'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                                'maximum_marks' => $term_param_value->maximum_marks,
                                'student_session_id' => $term_param_value->student_session_id,
                                'obtain_marks' => $term_param_value->obtain_marks,
                                'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                            ]
                        ]

                    ];

                    $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'] = $new_param_terms;


                } elseif (!array_key_exists($term_param_value->cbse_exam_observation_id, $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'])) {

                    $new_observation = [
                        $term_param_value->cbse_observation_parameter_id => [

                            'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                            'maximum_marks' => $term_param_value->maximum_marks,
                            'student_session_id' => $term_param_value->student_session_id,
                            'obtain_marks' => $term_param_value->obtain_marks,
                            'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                        ]
                    ];

                    $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'][$term_param_value->cbse_exam_observation_id] = $new_observation;

                } elseif (!array_key_exists($term_param_value->cbse_observation_parameter_id, $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'][$term_param_value->cbse_exam_observation_id])) {

                    $new_observation_params = [

                        'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                        'maximum_marks' => $term_param_value->maximum_marks,
                        'student_session_id' => $term_param_value->student_session_id,
                        'obtain_marks' => $term_param_value->obtain_marks,
                        'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                    ];

                    $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'][$term_param_value->cbse_exam_observation_id][$term_param_value->cbse_observation_parameter_id] = $new_observation_params;

                }

            }
        }

        $data['student_observations'] = $student_observations;

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
                  
                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;
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
                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;
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
                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;
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
                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;
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
                    'rank' => $student_value->rank,
                    'subject_rank' => [
                        $student_value->subject_id=>$student_value->subject_rank
                    ],
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
      
        $result_page = $this->load->view('cbseexam/result/_printpdf_multi', $data, true);

        return array('pg' => $result_page);
    }

    public function test_multi1() //for multiple terms
    {
        $data['template'] = $this->cbseexam_template_model->get('9');
        $data['sch_setting'] = $this->sch_setting_detail;
        //======================
        $list_observation = $this->cbse_observation_term_model->getObservationByTemplate('9');

        $list_observation_new = [];

        foreach ($list_observation as $ls_observation_key => $ls_observation_value) {

            if (!array_key_exists($ls_observation_value->cbse_exam_observation_id, $list_observation_new)) {
                $list_observation_new[$ls_observation_value->cbse_exam_observation_id] = [
                    'cbse_exam_observation_id' => $ls_observation_value->cbse_exam_observation_id,
                    'cbse_observation_name' => $ls_observation_value->cbse_observation_name,
                    'cbse_observation_parameters' => $ls_observation_value->cbse_observation_parameters,
                    'cbse_terms' => [
                        [
                            'cbse_term_id' => $ls_observation_value->cbse_term_id,
                            'term_name' => $ls_observation_value->term_name
                        ]
                    ]

                ];
            } else {
                $list_observation_new[$ls_observation_value->cbse_exam_observation_id]['cbse_terms'][] = [
                    'cbse_term_id' => $ls_observation_value->cbse_term_id,
                    'term_name' => $ls_observation_value->term_name
                ];
            }
        }

        $data['list_observation'] = $list_observation_new;

        //====================
        $cbse_observation_parameter = $this->cbseexam_observation_parameter_model->getTermObservationParams('9');

        $data['cbse_observation_parameter'] = $cbse_observation_parameter;
        $cbse_term_parameter = $this->cbseexam_observation_parameter_model->getStudentTermObservation('9');


        $student_observations = [];
        foreach ($cbse_term_parameter as $term_param_key => $term_param_value) {
            if (!array_key_exists($term_param_value->student_session_id, $student_observations)) {
                $student_observations[$term_param_value->student_session_id] = [
                    'terms' => [
                        $term_param_value->cbse_term_id => [
                            'observations' => [
                                $term_param_value->cbse_exam_observation_id => [
                                    $term_param_value->cbse_observation_parameter_id => [

                                        'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                                        'maximum_marks' => $term_param_value->maximum_marks,
                                        'student_session_id' => $term_param_value->student_session_id,
                                        'obtain_marks' => $term_param_value->obtain_marks,
                                        'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name


                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
            } else {

                if (!array_key_exists($term_param_value->cbse_term_id, $student_observations[$term_param_value->student_session_id]['terms'])) {

                    $new_param_terms = [
                        $term_param_value->cbse_exam_observation_id => [
                            $term_param_value->cbse_observation_parameter_id => [

                                'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                                'maximum_marks' => $term_param_value->maximum_marks,
                                'student_session_id' => $term_param_value->student_session_id,
                                'obtain_marks' => $term_param_value->obtain_marks,
                                'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                            ]
                        ]
                    ];

                    $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'] = $new_param_terms;

                } elseif (!array_key_exists($term_param_value->cbse_exam_observation_id, $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'])) {

                    $new_observation = [
                        $term_param_value->cbse_observation_parameter_id => [

                            'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                            'maximum_marks' => $term_param_value->maximum_marks,
                            'student_session_id' => $term_param_value->student_session_id,
                            'obtain_marks' => $term_param_value->obtain_marks,
                            'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                        ]
                    ];

                    $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'][$term_param_value->cbse_exam_observation_id] = $new_observation;

                } elseif (!array_key_exists($term_param_value->cbse_observation_parameter_id, $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'][$term_param_value->cbse_exam_observation_id])) {

                    $new_observation_params = [

                        'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                        'maximum_marks' => $term_param_value->maximum_marks,
                        'student_session_id' => $term_param_value->student_session_id,
                        'obtain_marks' => $term_param_value->obtain_marks,
                        'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                    ];

                    $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'][$term_param_value->cbse_exam_observation_id][$term_param_value->cbse_observation_parameter_id] = $new_observation_params;

                }
            }
        }

        $data['student_observations'] = $student_observations;

        //==================================       
        $cbse_exam_result = $this->cbseexam_exam_model->getStudentExamResultTermwiseByTemplateId('9');

        $template_subjects = $this->cbseexam_exam_model->getTemplateAssessment('9');
        $subject_array = [];
        $exam_term_exam_assessment = [];
        $gradeexam_id = "";
        $remarkexam_id = "";

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {
            $subject_array[$cbse_exam_result_value->subject_id] = $cbse_exam_result_value->subject_name;
        }

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {

            if ((!array_key_exists($cbse_exam_result_value->cbse_term_id, $exam_term_exam_assessment))) {

                $assessment_array = $this->getExamAssesmentByTerm($template_subjects, $cbse_exam_result_value->cbse_term_id);

                $new_terms = [

                    'cbse_term_id' => $cbse_exam_result_value->cbse_term_id,
                    'cbse_term_name' => $cbse_exam_result_value->cbse_term_name,
                    'cbse_term_code' => $cbse_exam_result_value->cbse_term_code,
                    'cbse_term_weight' => $cbse_exam_result_value->weightage,
                    'term_total_assessments' => $assessment_array,
                    'exams' => []

                ];
                $exam_term_exam_assessment[$cbse_exam_result_value->cbse_term_id] = $new_terms;
            }
        }

        foreach ($template_subjects as $sub_key => $sub_value) {
            if (!array_key_exists($sub_value->cbse_exam_id, $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'])) {
                $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'][$sub_value->cbse_exam_id] = [
                    'cbse_exam_id' => $sub_value->cbse_exam_id,
                    'exam_name' => $sub_value->exam_name,
                    'exam_assessments' => [
                        [

                            'cbse_exam_assessment_id' => $sub_value->cbse_exam_assessment_id,
                            'cbse_exam_assessment_type_id' => $sub_value->cbse_exam_assessment_type_id,
                            'name' => $sub_value->name,
                            'code' => $sub_value->code,

                        ]
                    ]

                ];
            } else {
                $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'][$sub_value->cbse_exam_id]['exam_assessments'][] = [

                    'cbse_exam_assessment_id' => $sub_value->cbse_exam_assessment_id,
                    'cbse_exam_assessment_type_id' => $sub_value->cbse_exam_assessment_type_id,
                    'name' => $sub_value->name,
                    'code' => $sub_value->code,

                ];
            }
        }

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
                                'note' => $student_value->note
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
                        'note' => $student_value->note
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

        $this->load->view('cbseexam/result/_printpdf_multi', $data);

    }

    public function test_multi_exam($cbse_template_id, $students) //multiple exam in single term
    {
        $data['template'] = $this->cbseexam_template_model->get($cbse_template_id);
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['current_setting']= $this->customlib->getCurrentSession();

        $list_observation = $this->cbse_observation_term_model->getObservationByTemplate($cbse_template_id);
        $cbse_template_subject_term_exam = $this->cbseexam_template_model->getTemplateTermExamWithAssessment($cbse_template_id);

        $list_observation_new = [];

        foreach ($list_observation as $ls_observation_key => $ls_observation_value) {

            if (!array_key_exists($ls_observation_value->cbse_exam_observation_id, $list_observation_new)) {
                $list_observation_new[$ls_observation_value->cbse_exam_observation_id] = [
                    'cbse_exam_observation_id' => $ls_observation_value->cbse_exam_observation_id,
                    'cbse_observation_name' => $ls_observation_value->cbse_observation_name,
                    'cbse_observation_parameters' => $ls_observation_value->cbse_observation_parameters,
                    'cbse_terms' => [
                        [
                            'cbse_term_id' => $ls_observation_value->cbse_term_id,
                            'term_name' => $ls_observation_value->term_name
                        ]
                    ]
                ];
            } else {
                $list_observation_new[$ls_observation_value->cbse_exam_observation_id]['cbse_terms'][] = [
                    'cbse_term_id' => $ls_observation_value->cbse_term_id,
                    'term_name' => $ls_observation_value->term_name
                ];
            }
        }

        $data['list_observation'] = ($list_observation_new);

        $cbse_observation_parameter = $this->cbseexam_observation_parameter_model->getTermObservationParams($cbse_template_id);

        $data['cbse_observation_parameter'] = $cbse_observation_parameter;
        $cbse_term_parameter = $this->cbseexam_observation_parameter_model->getStudentTermObservation($cbse_template_id, $students);

        $student_observations = [];
        foreach ($cbse_term_parameter as $term_param_key => $term_param_value) {
            if (!array_key_exists($term_param_value->student_session_id, $student_observations)) {
                $student_observations[$term_param_value->student_session_id] = [
                    'terms' => [
                        $term_param_value->cbse_term_id => [
                            'observations' => [
                                $term_param_value->cbse_exam_observation_id => [
                                    $term_param_value->cbse_observation_parameter_id => [

                                        'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                                        'maximum_marks' => $term_param_value->maximum_marks,
                                        'student_session_id' => $term_param_value->student_session_id,
                                        'obtain_marks' => $term_param_value->obtain_marks,
                                        'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
            } else {

                if (!array_key_exists($term_param_value->cbse_term_id, $student_observations[$term_param_value->student_session_id]['terms'])) {

                    $new_param_terms = [
                        $term_param_value->cbse_exam_observation_id => [
                            $term_param_value->cbse_observation_parameter_id => [

                                'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                                'maximum_marks' => $term_param_value->maximum_marks,
                                'student_session_id' => $term_param_value->student_session_id,
                                'obtain_marks' => $term_param_value->obtain_marks,
                                'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                            ]
                        ]
                    ];

                    $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'] = $new_param_terms;


                } elseif (!array_key_exists($term_param_value->cbse_exam_observation_id, $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'])) {

                    $new_observation = [
                        $term_param_value->cbse_observation_parameter_id => [

                            'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                            'maximum_marks' => $term_param_value->maximum_marks,
                            'student_session_id' => $term_param_value->student_session_id,
                            'obtain_marks' => $term_param_value->obtain_marks,
                            'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                        ]
                    ];

                    $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'][$term_param_value->cbse_exam_observation_id] = $new_observation;

                } elseif (!array_key_exists($term_param_value->cbse_observation_parameter_id, $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'][$term_param_value->cbse_exam_observation_id])) {

                    $new_observation_params = [

                        'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                        'maximum_marks' => $term_param_value->maximum_marks,
                        'student_session_id' => $term_param_value->student_session_id,
                        'obtain_marks' => $term_param_value->obtain_marks,
                        'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                    ];

                    $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'][$term_param_value->cbse_exam_observation_id][$term_param_value->cbse_observation_parameter_id] = $new_observation_params;

                }
            }
        }

        $data['student_observations'] = $student_observations;

        //=========================

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
                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;

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
                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;
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

                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;
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
                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;
                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;
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
                    'rank' => $student_value->rank,
                      'subject_rank' => [
                        $student_value->subject_id=>$student_value->subject_rank
                    ],
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
    
    
        $result_page = $this->load->view('cbseexam/result/_printpdf_multi_exam', $data, true);
        return array('pg' => $result_page);
    }

    public function test_multi_exam1() //multiple exam in single term wise
    {
        $data['template'] = $this->cbseexam_template_model->get('9');
        $data['sch_setting'] = $this->sch_setting_detail;
        $list_observation = $this->cbse_observation_term_model->getObservationByTemplate('9');
        $list_observation_new = [];

        foreach ($list_observation as $ls_observation_key => $ls_observation_value) {

            if (!array_key_exists($ls_observation_value->cbse_exam_observation_id, $list_observation_new)) {
                $list_observation_new[$ls_observation_value->cbse_exam_observation_id] = [
                    'cbse_exam_observation_id' => $ls_observation_value->cbse_exam_observation_id,
                    'cbse_observation_name' => $ls_observation_value->cbse_observation_name,
                    'cbse_observation_parameters' => $ls_observation_value->cbse_observation_parameters,
                    'cbse_terms' => [
                        [
                            'cbse_term_id' => $ls_observation_value->cbse_term_id,
                            'term_name' => $ls_observation_value->term_name
                        ]
                    ]

                ];
            } else {
                $list_observation_new[$ls_observation_value->cbse_exam_observation_id]['cbse_terms'][] = [
                    'cbse_term_id' => $ls_observation_value->cbse_term_id,
                    'term_name' => $ls_observation_value->term_name
                ];
            }
        }

        $data['list_observation'] = ($list_observation_new);

        $cbse_observation_parameter = $this->cbseexam_observation_parameter_model->getTermObservationParams('9');

        $data['cbse_observation_parameter'] = $cbse_observation_parameter;
        $cbse_term_parameter = $this->cbseexam_observation_parameter_model->getStudentTermObservation('9');

        $student_observations = [];
        foreach ($cbse_term_parameter as $term_param_key => $term_param_value) {
            if (!array_key_exists($term_param_value->student_session_id, $student_observations)) {
                $student_observations[$term_param_value->student_session_id] = [
                    'terms' => [
                        $term_param_value->cbse_term_id => [
                            'observations' => [
                                $term_param_value->cbse_exam_observation_id => [
                                    $term_param_value->cbse_observation_parameter_id => [

                                        'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                                        'maximum_marks' => $term_param_value->maximum_marks,
                                        'student_session_id' => $term_param_value->student_session_id,
                                        'obtain_marks' => $term_param_value->obtain_marks,
                                        'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
            } else {

                if (!array_key_exists($term_param_value->cbse_term_id, $student_observations[$term_param_value->student_session_id]['terms'])) {

                    $new_param_terms = [
                        $term_param_value->cbse_exam_observation_id => [
                            $term_param_value->cbse_observation_parameter_id => [

                                'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                                'maximum_marks' => $term_param_value->maximum_marks,
                                'student_session_id' => $term_param_value->student_session_id,
                                'obtain_marks' => $term_param_value->obtain_marks,
                                'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                            ]
                        ]
                    ];

                    $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'] = $new_param_terms;


                } elseif (!array_key_exists($term_param_value->cbse_exam_observation_id, $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'])) {

                    $new_observation = [
                        $term_param_value->cbse_observation_parameter_id => [

                            'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                            'maximum_marks' => $term_param_value->maximum_marks,
                            'student_session_id' => $term_param_value->student_session_id,
                            'obtain_marks' => $term_param_value->obtain_marks,
                            'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                        ]
                    ];

                    $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'][$term_param_value->cbse_exam_observation_id] = $new_observation;

                } elseif (!array_key_exists($term_param_value->cbse_observation_parameter_id, $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'][$term_param_value->cbse_exam_observation_id])) {

                    $new_observation_params = [

                        'cbse_observation_parameter_id' => $term_param_value->cbse_observation_parameter_id,
                        'maximum_marks' => $term_param_value->maximum_marks,
                        'student_session_id' => $term_param_value->student_session_id,
                        'obtain_marks' => $term_param_value->obtain_marks,
                        'cbse_observation_parameter_name' => $term_param_value->cbse_observation_parameter_name

                    ];

                    $student_observations[$term_param_value->student_session_id]['terms'][$term_param_value->cbse_term_id]['observations'][$term_param_value->cbse_exam_observation_id][$term_param_value->cbse_observation_parameter_id] = $new_observation_params;

                }
            }
        }

        $data['student_observations'] = $student_observations;
        //=========================

        $cbse_exam_result = $this->cbseexam_exam_model->getStudentExamResultTermwiseByTemplateId('9');
        $template_subjects = $this->cbseexam_exam_model->getTemplateAssessment('9');
        $subject_array = [];
        $exam_term_exam_assessment = [];
        $gradeexam_id = "";
        $remarkexam_id = "";

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {
            $subject_array[$cbse_exam_result_value->subject_id] = $cbse_exam_result_value->subject_name;
        }

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {
            if ((!array_key_exists($cbse_exam_result_value->cbse_term_id, $exam_term_exam_assessment))) {
                $assessment_array = $this->getExamAssesmentByTerm($template_subjects, $cbse_exam_result_value->cbse_term_id);
                $new_terms = [
                    'cbse_term_id' => $cbse_exam_result_value->cbse_term_id,
                    'cbse_term_name' => $cbse_exam_result_value->cbse_term_name,
                    'cbse_term_code' => $cbse_exam_result_value->cbse_term_code,
                    'term_total_assessments' => $assessment_array,
                    'exams' => []

                ];
                $exam_term_exam_assessment[$cbse_exam_result_value->cbse_term_id] = $new_terms;
            }

        }

        foreach ($template_subjects as $sub_key => $sub_value) {
            if (!array_key_exists($sub_value->cbse_exam_id, $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'])) {
                $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'][$sub_value->cbse_exam_id] = [
                    'cbse_exam_id' => $sub_value->cbse_exam_id,
                    'exam_name' => $sub_value->exam_name,
                    'weightage' => $sub_value->weightage,
                    'exam_assessments' => [
                        [

                            'cbse_exam_assessment_id' => $sub_value->cbse_exam_assessment_id,
                            'cbse_exam_assessment_type_id' => $sub_value->cbse_exam_assessment_type_id,
                            'name' => $sub_value->name,
                            'code' => $sub_value->code,

                        ]
                    ]
                ];
            } else {
                $exam_term_exam_assessment[$sub_value->cbse_term_id]['exams'][$sub_value->cbse_exam_id]['exam_assessments'][] = [

                    'cbse_exam_assessment_id' => $sub_value->cbse_exam_assessment_id,
                    'cbse_exam_assessment_type_id' => $sub_value->cbse_exam_assessment_type_id,
                    'name' => $sub_value->name,
                    'code' => $sub_value->code,

                ];
            }
        }

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
                                'note' => $student_value->note
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
                        'note' => $student_value->note
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
        $this->load->view('cbseexam/result/_printpdf_multi_exam', $data);
    }

    public function multi_exam_without_term($cbse_template_id, $students) //multiple exam without term wise
    {
        $data['template'] = $this->cbseexam_template_model->get($cbse_template_id);
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['current_setting']= $this->customlib->getCurrentSession();
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
                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;

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

                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;

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
                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;

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
                    'rank' => $student_value->rank,
                    'subject_rank' => [
                        $student_value->subject_id=>$student_value->subject_rank
                    ],
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
        $data['exam_grades'] = $this->cbseexam_grade_model->getExamGrades($gradeexam_id);
        $result_page = $this->load->view('cbseexam/result/_multi_exam_without_term', $data, true);
        return array('pg' => $result_page);
    }

    public function multi_exam_without_term1() //multiple exam without term wise
    {
        $data['template'] = $this->cbseexam_template_model->get('9');
        $data['sch_setting'] = $this->sch_setting_detail;
        $cbse_exam_result = $this->cbseexam_exam_model->getStudentExamResultByTemplateId('9');
        $template_subjects = $this->cbseexam_exam_model->getTemplateAssessmentWithoutTerm('9');

        $subject_array = [];
        $exam_term_exam_assessment = [];
        $gradeexam_id = "";
        $remarkexam_id = "";

        foreach ($cbse_exam_result as $cbse_exam_result_key => $cbse_exam_result_value) {
            $subject_array[$cbse_exam_result_value->subject_id] = $cbse_exam_result_value->subject_name;
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
                                'note' => $student_value->note
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
                        'note' => $student_value->note
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

        $this->load->view('cbseexam/result/_multi_exam_without_term', $data);
    }

    public function test($cbse_template_id, $cbse_exam_id, $students)
    {
        $data['template'] = $this->cbseexam_template_model->get($cbse_template_id);
        $data['current_setting']= $this->customlib->getCurrentSession();
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['exam'] = $this->cbseexam_exam_model->getExamWithGrade($cbse_exam_id);
        $cbse_exam_result = $this->cbseexam_exam_model->getStudentExamResultByExamId($cbse_template_id,$cbse_exam_id, $students);
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
                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;


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
                    $students[$student_value->student_id]['subject_rank'][ $student_value->subject_id] = $student_value->subject_rank;
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
                    'rank' => $student_value->rank,                    
                    'subject_rank' => [
                        $student_value->subject_id=>$student_value->subject_rank
                    ],
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
        $result_page = $this->load->view('cbseexam/result/_printpdf', $data, true);
        return array('pg' => $result_page);
    }

    public function test1()
    {
        $data['template'] = $this->cbseexam_template_model->get('9');
        $data['sch_setting'] = $this->sch_setting_detail;
        $data['exam'] = $this->cbseexam_exam_model->getExamWithGrade('17');
        $cbse_exam_result = $this->cbseexam_exam_model->getStudentExamResultByExamId('17', []);
        $data['cbse_exam_result'] = $cbse_exam_result;
        $students = [];

        foreach ($cbse_exam_result as $student_key => $student_value) {

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
                                'note' => $student_value->note
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
                        'note' => $student_value->note
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
        $this->load->view('cbseexam/result/_printpdf', $data);
    }

    function multiKeyExists(array $arr, $key)
    {
        // is in base array?
        if (array_key_exists($key, $arr)) {
            return true;
        }

        // check arrays contained in this array
        foreach ($arr as $element) {
            if (is_array($element)) {
                if (multiKeyExists($element, $key)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function printmarksheet()
    {

        $this->form_validation->set_rules('student_session_id[]', $this->lang->line('exam'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('marksheet_template', $this->lang->line('template'), 'required|trim|xss_clean');

        $data = array();
        $data['sch_setting'] = $this->setting_model->getSetting();


        if ($this->form_validation->run() == false) {
            $msg = array(
                'student' => form_error('student_session_id'),
            );
            $array = array('status' => 0, 'error' => $msg);
            echo json_encode($array);
        } else {

            $type = $this->input->post('type');
            $cbse_template_id = $this->input->post('marksheet_template');
            $data['template'] = $this->cbseexam_template_model->get($cbse_template_id);
            $students = $this->input->post('student_session_id');

            $template = $this->cbseexam_template_model->get($cbse_template_id);
        
            if ($template['marksheet_type'] == "term_wise") {

                $return_page = $this->test_multi_exam($cbse_template_id, $students);
            } elseif ($template['marksheet_type'] == "all_term") {

                $return_page = $this->test_multi($cbse_template_id, $students);
            } elseif ($template['marksheet_type'] == "without_term") {
                $return_page = $this->multi_exam_without_term($cbse_template_id, $students);

            } elseif ($template['marksheet_type'] == "exam_wise") {

                $cbse_temp_term_exam = $this->cbseexam_exam_model->getTemplateSingleExam($cbse_template_id);

          
               
                $return_page = $this->test($cbse_template_id, $cbse_temp_term_exam->cbse_exam_id, $students);

            }

            $this->load->library('m_pdf');

            $mpdf = $this->m_pdf->load($data['template']['orientation']);
            $stylesheet = file_get_contents(base_url() . 'backend/cbse_pdf_style.css'); // external css
            if ($data['template']['background_img'] != "") {

                $mpdf->SetDefaultBodyCSS('background', "url('" . base_url("/uploads/cbseexam/template/background_img/" . $data['template']['background_img']) . "')");
                $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
            }
            $mpdf->WriteHTML($stylesheet, 1); // Writing style to pdf
            $mpdf->SetWatermarkText($this->sch_setting_detail->name, .2);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->showWatermarkText = true;
            $mpdf->autoScriptToLang = true;
            $mpdf->baseScript = 1;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($return_page['pg'], \Mpdf\HTMLParserMode::HTML_BODY);
            $response = true;

            if ($type == "email") {
                $this->load->library('mailsmsconf');
                $content = $mpdf->Output(random_string() . '.pdf', 'S');
                $this->load->library('mailer');
                $student=$this->input->post('student_session_id');

                $student_data = $this->student_model->getByStudentSession($student[0]);

                $exam_roll_no = $student_data['roll_no'];
                $student_name = $this->customlib->getFullName($student_data['firstname'], $student_data['middlename'], $student_data['lastname'], $data['sch_setting']->middlename, $data['sch_setting']->lastname);
                $sender_details = array('email' => $student_data['email'], 'student_name' => $student_name, 'class' => $student_data['class'], 'section' => $student_data['section'], 'admission_no' => $student_data['admission_no'], 'roll_no' => $student_data['roll_no'], 'admit_card_roll_no' => $exam_roll_no, 'dob' => $student_data['dob'], 'guardian_name' => $student_data['guardian_name'], 'guardian_relation' => $student_data['guardian_relation'], 'guardian_phone' => $student_data['guardian_phone'], 'father_name' => $student_data['father_name'], 'father_phone' => $student_data['father_phone'], 'mother_name' => $student_data['mother_name'], 'gender' => $student_data['gender'], 'guardian_email' => $student_data['guardian_email'], 'exam' => "");

                $this->cbse_mail_sms->mailSmsMarksheet('cbse_email_pdf_exam_marksheet', $sender_details, '', '', $content);

            } elseif ($type == "download") {

                $content = $mpdf->Output(random_string() . '.pdf', 'I');
                return $content;
            }
        }
    }

    public function demo()
    {
        $data['result'] = $this->cbseexam_result_model->getresult(2, 1);
        $data['observation'] = $this->cbseexam_result_model->observation_result(2);
        $data['attendance'] = $this->cbseexam_result_model->get_attendance(2);
        $data['student_details'] = $this->cbseexam_result_model->get_student_detail(2);
        $data['sch_setting'] = $this->sch_setting_detail;
        $student_exam_page = $this->load->view('cbseexam/result/_printmarksheet', $data);
    }

    public function examtermwise()
    {
        $class_section_id = $this->input->post('section_id');
        $marksheet_type = $this->input->post('marksheet_type');
        $marksheetlist = $this->cbseexam_result_model->getmarksheettypebyid($marksheet_type);

        if (!empty($marksheetlist)) {
            if ($marksheetlist['short_code'] == 'term_wise') {
                $data['term_wise_list'] = $this->cbseexam_result_model->termwise($marksheet_type);
                $data['type'] = 'term_wise';
                $array = array('status' => '1', 'error' => '', 'data' => $data['term_wise_list'], 'type' => $data['type']);
            } elseif ($marksheetlist['short_code'] == 'exam_wise') {
                $data['exam_wise_list'] = $this->cbseexam_result_model->examwise($marksheet_type);
                $data['type'] = 'exam_wise';
                $array = array('status' => '1', 'error' => '', 'data' => $data['exam_wise_list'], 'type' => $data['type']);
            } else {
                $array = array('status' => '0', 'error' => '');
            }
            echo json_encode($array);
        } else {
            $array = array('status' => '0', 'error' => '');
            echo json_encode($array);
        }
    }

    public function termwiseresult()
    {
        $data['result'] = $this->cbseexam_result_model->gettermwiseresult(2);
        $data['observation'] = $this->cbseexam_result_model->observation_result(2);
        $data['attendance'] = $this->cbseexam_result_model->get_attendance(2);
        $data['student_details'] = $this->cbseexam_result_model->get_student_detail(2);
        $data['sch_setting'] = $this->sch_setting_detail;
        $student_exam_page = $this->load->view('cbseexam/result/_printtermwisemarksheet', $data);
    }

    public function examwiseresult()
    {
        $data['result'] = $this->cbseexam_result_model->getexamwiseresult(2);
        $data['observation'] = $this->cbseexam_result_model->observation_result(2);
        $data['attendance'] = $this->cbseexam_result_model->get_attendance(2);
        $data['student_details'] = $this->cbseexam_result_model->get_student_detail(2);
        $data['sch_setting'] = $this->sch_setting_detail;
        $student_exam_page = $this->load->view('cbseexam/result/_printexamwisemarksheet', $data);
    }

    public function getExamAssesmentByTerm($array, $find_cbse_term_id)
    {

        $return_array = [];
        foreach ($array as $_arrry_key => $_arrry_value) {
            if ($_arrry_value->cbse_term_id == $find_cbse_term_id) {

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

}