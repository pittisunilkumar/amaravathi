<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cbseexam_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    
    public function getStudentExamByStudentSession($student_session_id){
        return $this->db->select('cbse_exam_students.*,cbse_exams.cbse_exam_assessment_id,cbse_exams.cbse_term_id,cbse_exams.name,cbse_exams.use_exam_roll_no,cbse_exams.is_active,cbse_exams.is_publish,cbse_exams.cbse_term_id,cbse_exams.cbse_exam_grade_id,cbse_exams.total_working_days')
        ->from('cbse_exam_students')
        ->join('student_session','student_session.id=cbse_exam_students.student_session_id')
        ->join('students','students.id=student_session.student_id')        
        ->join('cbse_exams','cbse_exam_students.cbse_exam_id=cbse_exams.id')        
        ->where('cbse_exam_students.student_session_id',$student_session_id)
        ->where('cbse_exams.is_publish','1')
        ->get()->result();
    }

    public function getexamsubjects($exam_id){
        return $this->db->select('cbse_exam_timetable.*,subjects.name as subject_name,subjects.code as subject_code')
        ->from('cbse_exam_timetable')->join('subjects','subjects.id=cbse_exam_timetable.subject_id')
        ->where('cbse_exam_id',$exam_id)->get()->result();
    } 

    
    public function getGraderangebyGradeID($cbse_exam_grade_id)
    {
        $result = $this->db->select('*')->from('cbse_exam_grades_range')->where('cbse_exam_grade_id', $cbse_exam_grade_id)->get()->result();
        return $result;
    }

    public function getWithAssessmentTypeByAssessmentID($cbse_exam_assessment_id)
    {      
        $assement_types= $this->db->select('*')->from('cbse_exam_assessment_types')->where('cbse_exam_assessment_id', $cbse_exam_assessment_id)->order_by('id','asc')->get()->result();
        return $assement_types;
    }

    public function getStudentExamResultByExamId($cbse_exam_id,$students)
    {
        $students = implode(', ', array_map(function($val){return sprintf("'%s'", $val);}, $students));

        $sql   = "SELECT  `cbse_exams`.*,cbse_exam_timetable.id as cbse_exam_timetable_id ,cbse_student_exam_ranks.rank,cbse_terms.name as cbse_term_name,cbse_terms.term_code as cbse_term_code,cbse_exam_timetable.subject_id,cbse_exam_students.id as cbse_exam_student_id,cbse_exam_students.total_present_days,cbse_exam_students.remark,cbse_exam_assessment_types.name as cbse_exam_assessment_type_name,cbse_exam_assessment_types.id as `cbse_exam_assessment_type_id`,cbse_exam_assessment_types.code as cbse_exam_assessment_type_code,cbse_exam_assessment_types.maximum_marks,cbse_student_subject_marks.id as `cbse_student_subject_marks_id`,cbse_student_subject_marks.marks,IFNULL(cbse_student_subject_marks.is_absent,0) as is_absent,cbse_student_subject_marks.note,students.id as `student_id`,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast,  students.dob ,students.current_address, students.previous_school,students.roll_no,
        students.guardian_is,students.parent_id,students.admission_no,
        students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,subjects.name as subject_name,subjects.code as `subject_code`,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,student_session.id as `student_session_id`  FROM `cbse_exams` INNER JOIN cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_students on cbse_exam_students.cbse_exam_id=cbse_exams.id INNER JOIN cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exams.cbse_exam_assessment_id INNER JOIN cbse_terms on cbse_terms.id=cbse_exams.cbse_term_id left join cbse_student_subject_marks on cbse_student_subject_marks.cbse_exam_timetable_id =cbse_exam_timetable.id and cbse_student_subject_marks.cbse_exam_student_id= cbse_exam_students.id and cbse_student_subject_marks.cbse_exam_assessment_type_id=cbse_exam_assessment_types.id INNER JOIN student_session on student_session.id=cbse_exam_students.student_session_id INNER join students on students.id =student_session.student_id INNER JOIN subjects on subjects.id=cbse_exam_timetable.subject_id INNER join classes on student_session.class_id = classes.id INNER join sections on sections.id = student_session.section_id left join cbse_student_exam_ranks on cbse_student_exam_ranks.student_session_id = student_session.id and cbse_student_exam_ranks.cbse_exam_id=".$this->db->escape($cbse_exam_id)." WHERE cbse_exams.`id` = ".$this->db->escape($cbse_exam_id)." and cbse_exams.session_id=". $this->current_session." and student_session.id in (".$students.")";
      
       
        $query = $this->db->query($sql);
        return $query->result();
    }
	
	public function getStudentExamTimetable($student_session_id){
   
       $student_exam= $this->db->select('cbse_exam_students.*,cbse_exams.name,cbse_exams.exam_code')
        ->from('cbse_exam_students')->join('cbse_exams','cbse_exams.id=cbse_exam_students.cbse_exam_id')
        ->where('student_session_id',$student_session_id)
        ->where('cbse_exams.is_active',1)
        ->get()->result();
        if(!empty($student_exam)){
            foreach ($student_exam as $exam_key => $exam_value) {
                
                $student_exam[$exam_key]->{"time_table"}= $this->db->select('cbse_exam_timetable.*,subjects.name as subject_name,subjects.code as subject_code')
                ->from('cbse_exam_timetable')->join('subjects','subjects.id=cbse_exam_timetable.subject_id')
                ->where('cbse_exam_id',$exam_value->cbse_exam_id)
                ->get()
                ->result();
         
            }

        }
    

		return $student_exam;
    } 

    public function getSubjectAssessmentsByExam($subjects){
       
        foreach ($subjects as $subject_key => $subject_value) {
            $subject_assessments=$this->getSubjectAssessments($subject_value->id);
            $subjects[$subject_key]->{'subject_assessments'}=$subject_assessments;
          
      
        }

 return $subjects;

    }
    
    public function getSubjectAssessments($cbse_exam_timetable_id){
        $assement_types= $this->db->select('*')
        ->from('cbse_exam_timetable_assessment_types')
        ->where('cbse_exam_timetable_id', $cbse_exam_timetable_id)
        ->order_by('id','asc')
        ->get()
        ->result();
        return $assement_types;
    }


}
?>