<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Exam extends Student_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('cbseexam/cbseexam_exam_model', 'cbseexam/cbseexam_assessment_model', 'cbseexam/cbseexam_grade_model'));

    }

    public function timetable()
    {
		$this->session->set_userdata('top_menu', 'cbse_exam');
		$this->session->set_userdata('sub_menu', 'user/cbse/cbse_exam_timetable');
        $data = [];
        $student_current_class = $this->customlib->getStudentCurrentClsSection();
        $data['exams'] = $this->cbseexam_exam_model->getStudentExamTimetable($student_current_class->student_session_id);
        $this->load->view('layout/student/header', $data);
        $this->load->view('user/cbse/timetable', $data);
        $this->load->view('layout/student/footer', $data);
    }

    public function result()
    {
        $this->session->set_userdata('top_menu', 'cbse_exam');
		$this->session->set_userdata('sub_menu', 'user/cbse/cbse_exam_result');		
		
        $data = [];
        $student_current_class = $this->customlib->getStudentCurrentClsSection();

        $exam_list = $this->cbseexam_exam_model->getStudentExamByStudentSession($student_current_class->student_session_id);

        $student_exams = [];
        if (!empty($exam_list)) {
            foreach ($exam_list as $exam_key => $exam_value) {
              
                $exam_value->{"subjects"} = $this->cbseexam_exam_model->getexamsubjects($exam_value->cbse_exam_id);
                $exam_value->{"grades"} = $this->cbseexam_grade_model->getGraderangebyGradeID($exam_value->cbse_exam_grade_id);
                $exam_value->{"exam_assessments"} = $this->cbseexam_assessment_model->getWithAssessmentTypeByAssessmentID($exam_value->cbse_exam_assessment_id);

                $cbse_exam_result = $this->cbseexam_exam_model->getStudentResultByExamId($exam_value->cbse_exam_id, [$exam_value->student_session_id]);
               
                $students = [];
                $student_rank="";

                if (!empty($cbse_exam_result)) {

                    foreach ($cbse_exam_result as $student_key => $student_value) {
                        $student_rank=$student_value->rank;
                   
                        if (!empty($students)) {

                            if (!array_key_exists($student_value->subject_id, $students['subjects'])) {

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
                                        ],
                                    ],
                                ];

                                $students['subjects'][$student_value->subject_id] = $new_subject;

                            } elseif (!array_key_exists($student_value->cbse_exam_assessment_type_id, $students['subjects'][$student_value->subject_id]['exam_assessments'])) {

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

                                $students['subjects'][$student_value->subject_id]['exam_assessments'][$student_value->cbse_exam_assessment_type_id] = $new_assesment;
                            }

                        } else {
                          
                            $students['subjects'] = [
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

                                        ],

                                    ],
                                ],

                            ];

                        }
                    }
                }
                $exam_value->{"rank"} = $student_rank;
                $exam_value->{"exam_data"} = $students;

            }
        }

        $data['exams'] = $exam_list;

        $this->load->view('layout/student/header', $data);
        $this->load->view('user/cbse/result', $data);
        $this->load->view('layout/student/footer', $data);
    }

}