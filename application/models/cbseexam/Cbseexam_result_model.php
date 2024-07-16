<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cbseexam_result_model extends MY_Model {


    /*
    This function is used to get cabse exam marksheet type
    */
    public function marksheet_type()
    {
        return $this->db->select('*')->from('cbse_marksheet_type')->get()->result_array();
    }

    public function searchStudents($class_section_id)
    {
       return  $this->db->select('students.*')
        ->from('cbse_exam_class_sections')
        ->join('cbse_exam_students','cbse_exam_students.id=cbse_exam_class_sections.cbse_exam_id')
        ->join('student_session','student_session.id=cbse_exam_students.student_session_id')
        ->join('students','students.id=student_session.student_id')
        ->where('cbse_exam_class_sections.class_section_id',$class_section_id)
        ->group_by('students.id')
        ->get()
        ->result();
    }

    public function searchTemplateStudents($class_section_id,$cbse_template_id)
    {
        return  $this->db->select('`cbse_template_term_exams`.`id` as `cbse_template_term_exam_id`, `cbse_exam_class_sections`.`class_section_id`, `cbse_exam_students`.`id` as `cbse_exam_student_id`, `cbse_exam_students`.`student_session_id`, `class_sections`.`class_id`, `class_sections`.`section_id`, `students`.*')
        ->from('cbse_template_term_exams')
        ->join('cbse_exam_class_sections','cbse_exam_class_sections.cbse_exam_id=cbse_template_term_exams.cbse_exam_id and cbse_exam_class_sections.class_section_id='.$class_section_id)
        ->join('cbse_exam_students' , 'cbse_exam_students.cbse_exam_id=cbse_template_term_exams.cbse_exam_id')      
        ->join('class_sections', 'class_sections.id=cbse_exam_class_sections.class_section_id')
        ->join('student_session','student_session.id=cbse_exam_students.student_session_id and student_session.class_id=class_sections.class_id and student_session.section_id=class_sections.section_id')
        ->join('students', 'students.id=student_session.student_id')
        ->where('cbse_template_id',$cbse_template_id)
        ->group_by('student_session.id')
        ->get()
        ->result();
    }

    public function searchTemplateStudentsWithTerm($class_section_id,$cbse_template_id)
    {
        return  $this->db->select('`cbse_template_term_exams`.`id` as `cbse_template_term_exam_id`, `cbse_exam_class_sections`.`class_section_id`, `cbse_exam_students`.`id` as `cbse_exam_student_id`, `cbse_exam_students`.`student_session_id`, `class_sections`.`class_id`, `class_sections`.`section_id`, `students`.*,cbse_student_template_rank.rank,cbse_student_template_rank.rank_percentage')
        ->from('cbse_template_term_exams')
        ->join('cbse_template' , 'cbse_template.id=cbse_template_term_exams.cbse_template_id')  
        ->join('cbse_exam_class_sections','cbse_exam_class_sections.cbse_exam_id=cbse_template_term_exams.cbse_exam_id and cbse_exam_class_sections.class_section_id='.$class_section_id)
        ->join('cbse_exam_students' , 'cbse_exam_students.cbse_exam_id=cbse_template_term_exams.cbse_exam_id')      
        ->join('class_sections', 'class_sections.id=cbse_exam_class_sections.class_section_id')
        ->join('student_session','student_session.id=cbse_exam_students.student_session_id and student_session.class_id=class_sections.class_id and student_session.section_id=class_sections.section_id')
        ->join('students', 'students.id=student_session.student_id')
        ->join('cbse_student_template_rank', 'cbse_student_template_rank.cbse_template_id=cbse_template.id and cbse_student_template_rank.student_session_id=student_session.id', 'left')
        ->where('cbse_template_term_exams.cbse_template_id',$cbse_template_id)
        ->group_by('student_session.id')
        ->order_by('`cbse_student_template_rank`.`rank`','asc')
        ->get()
        ->result();
    }
    
    public function getTemplateStudents($cbse_template_id)
    {
        return  $this->db->select('`cbse_template_term_exams`.`id` as `cbse_template_term_exam_id`,  `cbse_exam_students`.`id` as `cbse_exam_student_id`, `cbse_exam_students`.`student_session_id`,  `students`.*,cbse_student_template_rank.rank,cbse_student_template_rank.rank_percentage,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section')
        ->from('cbse_template_term_exams')
        ->join('cbse_template' , 'cbse_template.id=cbse_template_term_exams.cbse_template_id')         
        ->join('cbse_exam_students' , 'cbse_exam_students.cbse_exam_id=cbse_template_term_exams.cbse_exam_id')     
        ->join('student_session','student_session.id=cbse_exam_students.student_session_id')   
        ->join('classes', 'student_session.class_id = classes.id')
        ->join('sections', 'sections.id = student_session.section_id')
         ->join('students', 'students.id=student_session.student_id')
        ->join('cbse_student_template_rank', 'cbse_student_template_rank.cbse_template_id=cbse_template.id and cbse_student_template_rank.student_session_id=student_session.id', 'left')
        ->where('cbse_template_term_exams.cbse_template_id',$cbse_template_id)
        ->group_by('student_session.id')
        ->order_by('`cbse_student_template_rank`.`rank`','asc')
        ->get()
        ->result();
    }

    public function get_grade($mark,$grade_data)
    {
        $finale_array=array();
        foreach ($grade_data as $gkey => $gvalue) {
            $gradedata=range($gvalue['minimum_percentage'], $gvalue['maximum_percentage']);
            foreach ($gradedata as $key => $value) {
                $finale_array[$value]=$gvalue['name'];
            }
        }

        if (array_key_exists($mark,$finale_array)) {
           return $finale_array[$mark];
        }else{
            return false;
        }       
    }

    public function templatedata($cbse_template_id)
    {
       return $this->db->select('*')->from('cbse_template')->where('id',$cbse_template_id)->get()->row_array();
    }

    public function getresult($student_session_id,$cbse_template_id)
    {
        $termsdata=array();
        $template_data=$this->templatedata($cbse_template_id);
        $grade=$this->db->select('*')->from('cbse_exam_grades_range')->where('cbse_exam_grade_id',$template_data['gradeexam_id'])->get()->result_array();
        $subject=$this->db->select('subjects.*')->from('cbse_exam_students')->join('cbse_exam_timetable','cbse_exam_timetable.cbse_exam_id=cbse_exam_students.cbse_exam_id')->join('subjects','subjects.id=cbse_exam_timetable.subject_id')->where('cbse_exam_students.student_session_id',$student_session_id)->group_by('cbse_exam_timetable.subject_id')->get()->result_array();
        $subjects=array();
        foreach($subject as $skey=>$svalue){
            $subjects[$svalue['id']]['name']=$svalue['name'];
        }
        
        if(($template_data['marksheet_type']=='exam_wise') || ($template_data['marksheet_type']=='without_term'))
        {           
            $exam=$this->db->select('cbse_exams.*,cbse_template_term_exams.weightage')->from('cbse_template_term_exams')->join('cbse_exams','cbse_template_term_exams.cbse_exam_id=cbse_exams.id')->where('cbse_template_term_exams.cbse_template_id',$template_data['id'])->get()->result_array();
            $total_assignment=1;

            foreach ($exam as $examkey => $examvalue) {
                $termsdata['exam'][$examvalue['id']]['name']=$examvalue['name'];
                
                $exam_assessments=$this->db->select('cbse_exam_assessment_types.*')->from('cbse_exam_assessments')->join('cbse_exam_assessment_types','cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exam_assessments.id')->where('cbse_exam_assessments.id',$examvalue['cbse_exam_assessment_id'])->get()->result_array();

                $total_assignment+=count($exam_assessments);
            
                foreach ($exam_assessments as $exam_assessmentskey => $exam_assessmentsvalue) {

                    $termsdata['exam'][$examvalue['id']]['exam_assessments'][$exam_assessmentsvalue['id']]=$exam_assessmentsvalue;
                    
                    $assesment_mark=$this->db->select('cbse_student_subject_marks.mark,,cbse_exam_timetable.subject_id')->from('cbse_student_subject_result')
                    ->join('cbse_exam_students','cbse_exam_students.id=cbse_student_subject_result.cbse_exam_student_id')
                    ->join('cbse_student_subject_marks','cbse_student_subject_marks.cbse_student_subject_result_id=cbse_student_subject_result.id')
                    ->join('cbse_exam_timetable','cbse_exam_timetable.id=cbse_student_subject_result.cbse_exam_timetable_id')

                    ->where(array('cbse_student_subject_marks.cbse_exam_assessment_type_id'=>$exam_assessmentsvalue['id'],'cbse_exam_students.cbse_exam_id'=>$examvalue['id'],'cbse_exam_students.student_session_id'=>$student_session_id))->get()->result_array();

                    $assesment_marks=array();
                    foreach ($assesment_mark as $assesment_markkey => $assesment_markvalue) 
                    {
                        $assesment_markvalue['mark']=$assesment_markvalue['mark'];
                        $assesment_markvalue['grade']=$this->get_grade($assesment_markvalue['mark'],$grade);   
                        $assesment_marks[$assesment_markvalue['subject_id']]=$assesment_markvalue;           
                    }

                    $termsdata['exam'][$examvalue['id']]['exam_assessments'][$exam_assessmentsvalue['id']]['marks']=$assesment_marks;
                }   

                $termsdata['exam'][$examvalue['id']]['colspan']=count($exam_assessments);
                $termsdata['exam'][$examvalue['id']]['weightage']=$examvalue['weightage'];

            }
        }else{
        
        $terms=$this->db->select('cbse_template_terms.*,cbse_terms.name')
        ->from('cbse_template_terms')
        ->join('cbse_terms','cbse_terms.id=cbse_template_terms.cbse_term_id')
        ->join('cbse_exams','cbse_exams.cbse_term_id=cbse_terms.id')
        ->join('cbse_exam_students','cbse_exam_students.cbse_exam_id=cbse_exams.id')
        ->where('cbse_exam_students.student_session_id',$student_session_id)
        ->where('cbse_template_terms.cbse_template_id',$cbse_template_id)
        ->group_by('cbse_template_terms.id')
        ->get()
        ->result_array();
        
        $total_td=0;
        foreach($terms as $key=>$value){

            $termsdata[$value['id']]['name']=$value['name'];
            $termsdata[$value['id']]['weightage']=$value['weightage'];
            $exam=$this->db->select('cbse_exams.*,cbse_template_term_exams.weightage')->from('cbse_template_term_exams')->join('cbse_exams','cbse_template_term_exams.cbse_exam_id=cbse_exams.id')->where('cbse_template_term_exams.cbse_template_term_id',$value['id'])->get()->result_array();
            $total_assignment=1;

            foreach ($exam as $examkey => $examvalue) {
            $termsdata[$value['id']]['exam'][$examvalue['id']]['name']=$examvalue['name'];
            $exam_assessments=$this->db->select('cbse_exam_assessment_types.*')->from('cbse_exam_assessments')->join('cbse_exam_assessment_types','cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exam_assessments.id')->where('cbse_exam_assessments.id',$examvalue['cbse_exam_assessment_id'])->get()->result_array();

            $total_assignment+=count($exam_assessments);
            
            foreach ($exam_assessments as $exam_assessmentskey => $exam_assessmentsvalue) {                

            $termsdata[$value['id']]['exam'][$examvalue['id']]['exam_assessments'][$exam_assessmentsvalue['id']]=$exam_assessmentsvalue;
            $assesment_mark=$this->db->select('cbse_student_subject_marks.mark,cbse_exam_timetable.subject_id')->from('cbse_student_subject_result')->join('cbse_exam_students','cbse_exam_students.id=cbse_student_subject_result.cbse_exam_student_id')->join('cbse_student_subject_marks','cbse_student_subject_marks.cbse_student_subject_result_id=cbse_student_subject_result.id')->join('cbse_exam_timetable','cbse_exam_timetable.id=cbse_student_subject_result.cbse_exam_timetable_id')->where(array('cbse_student_subject_marks.cbse_exam_assessment_type_id'=>$exam_assessmentsvalue['id'],'cbse_exam_students.cbse_exam_id'=>$examvalue['id'],'cbse_exam_students.student_session_id'=>$student_session_id))->get()->result_array();

            $assesment_marks=array();
            foreach ($assesment_mark as $assesment_markkey => $assesment_markvalue) {
                $assesment_markvalue['mark']=$assesment_markvalue['mark']; 
                $assesment_markvalue['grade']=$this->get_grade($assesment_markvalue['mark'],$grade);   
                $assesment_marks[$assesment_markvalue['subject_id']]=$assesment_markvalue;  
               
            }

            $termsdata[$value['id']]['exam'][$examvalue['id']]['exam_assessments'][$exam_assessmentsvalue['id']]['marks']=$assesment_marks;
            }

            $termsdata[$value['id']]['exam'][$examvalue['id']]['colspan']=count($exam_assessments);
            $termsdata[$value['id']]['exam'][$examvalue['id']]['weightage']=$examvalue['weightage'];

            }
            
            $termsdata[$value['id']]['colspan']=$total_assignment;
            $total_td+=$total_assignment;
            }
            $returndata['td']=$total_td;
            }
            $returndata['result']=$termsdata;
            $returndata['subjects']=$subjects;
            $returndata['exam_grade']=$grade;
            return $returndata;     

    }

    public function observation_result($student_session_id)
    { 
        $observations=array();
        $observation=  $this->db->select('cbse_observation_student_marks.marks,cbse_observation_subparameter.name as subname,cbse_observation_parameters.name,cbse_observation_subparameter.id,cbse_observation_parameters.id as observation_id')->from('cbse_observation_student_marks')->join('cbse_observation_subparameter','cbse_observation_subparameter.id=cbse_observation_student_marks.cbse_observation_subparameter_id')->join('cbse_observation_parameters','cbse_observation_parameters.id=cbse_observation_subparameter.cbse_observation_parameter_id')->where('cbse_observation_student_marks.student_session_id',$student_session_id)->get()->result_array();
        foreach($observation as $key=>$value)
        {

            $observations[$value['observation_id']]['name']= $value['name']; 
            $observations[$value['observation_id']]['result'][]= $value;  

        }
        return $observations;
    }

    public function get_attendance($student_session_id)
    {
        return $this->db->select('sum(cbse_exam_students.total_present_days) as total_present_days,sum(cbse_exams.total_working_days) as total_working_days')->from('cbse_exams')->join('cbse_exam_students','`cbse_exam_students`.`cbse_exam_id`=cbse_exams.id')->where('cbse_exam_students.student_session_id',$student_session_id)->get()->row_array();
    }

    /*
    This function is used to get cbse exam marksheet type base on id
    */
    public function getmarksheettypebyid($id)
    {
        return $this->db->select('*')->from('cbse_marksheet_type')->where('cbse_marksheet_type.id', $id)->get()->row_array();
    }

    public function termwise($class_section_id)
    {
        $this->db->select('cbse_terms.id, cbse_terms.name as term_name');
        $this->db->from('cbse_exam_class_sections');
        $this->db->join('cbse_exams','cbse_exams.cbse_term_id=cbse_exam_class_sections.cbse_exam_id','left');
        $this->db->join('cbse_terms','cbse_terms.id=cbse_exams.cbse_term_id', 'left');
        $this->db->where('cbse_exams.cbse_term_id!= 0 or cbse_exams.cbse_term_id!= "null"');
        $this->db->where('cbse_exam_class_sections.class_section_id', $class_section_id);
        $this->db->group_by('cbse_exams.cbse_term_id');
        $result = $this->db->get();
        return $result->result_array();
    }

    public function examwise($class_section_id)
    {
        $this->db->select('cbse_exams.id, cbse_exams.name as cbse_exam_name');
        $this->db->from('cbse_exam_class_sections');
        $this->db->join('cbse_exams','cbse_exams.id=cbse_exam_class_sections.cbse_exam_id');
        $this->db->where('cbse_exams.cbse_term_id = 0 or cbse_exams.cbse_term_id = "null"');
        $this->db->where('cbse_exam_class_sections.class_section_id', $class_section_id);
        $result = $this->db->get();
        return $result->result_array();
    }

    public function get_student_detail($student_session_id)
    {
        return  $this->db->select('students.*')->from('students')->join('student_session','student_session.student_id=students.id')->where('student_session.id',$student_session_id)->get()->row_array();
    }

    public function gettermwiseresult($student_session_id)
    {
        $grade=$this->db->select('*')->from('cbse_exam_grades_range')->where('cbse_exam_grade_id',1)->get()->result_array();
    
        $terms=$this->db->select('cbse_terms.id,cbse_terms.name')->from('cbse_exam_students')->join('cbse_exams','cbse_exams.id=cbse_exam_students.cbse_exam_id')->join('cbse_terms','cbse_terms.id=cbse_exams.cbse_term_id')->where('cbse_exam_students.student_session_id',$student_session_id)->where('cbse_terms.id',1)->get()->result_array();
        $subject=$this->db->select('subjects.*')->from('cbse_exam_students')->join('cbse_exam_timetable','cbse_exam_timetable.cbse_exam_id=cbse_exam_students.cbse_exam_id')->join('subjects','subjects.id=cbse_exam_timetable.subject_id')->where('cbse_exam_students.student_session_id',$student_session_id)->group_by('cbse_exam_timetable.subject_id')->get()->result_array();
        $subjects=array();
        foreach($subject as $skey=>$svalue){
            $subjects[$svalue['id']]['name']=$svalue['name'];
        }
        $total_td=0;
        foreach($terms as $key=>$value){

            $termsdata[$value['id']]['name']=$value['name'];
            $exam=$this->db->select('*')->from('cbse_exams')->where('cbse_term_id',$value['id'])->get()->result_array();
            $total_assignment=1;

            foreach ($exam as $examkey => $examvalue) {
            $termsdata[$value['id']]['exam'][$examvalue['id']]['name']=$examvalue['name'];
            $exam_assessments=$this->db->select('cbse_exam_assessment_types.*')->from('cbse_exam_assessments')->join('cbse_exam_assessment_types','cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exam_assessments.id')->where('cbse_exam_assessments.id',$examvalue['cbse_exam_assessment_id'])->get()->result_array();

            $total_assignment+=count($exam_assessments);
            
            foreach ($exam_assessments as $exam_assessmentskey => $exam_assessmentsvalue) {

            $termsdata[$value['id']]['exam'][$examvalue['id']]['exam_assessments'][$exam_assessmentsvalue['id']]=$exam_assessmentsvalue;
            $assesment_mark=$this->db->select('cbse_student_subject_marks.mark,cbse_student_subject_result.subject_id')->from('cbse_student_subject_result')->join('cbse_exam_students','cbse_exam_students.id=cbse_student_subject_result.cbse_exam_student_id')->join('cbse_student_subject_marks','cbse_student_subject_marks.cbse_student_subject_result_id=cbse_student_subject_result.id')->where(array('cbse_student_subject_marks.cbse_exam_assessment_type_id'=>$exam_assessmentsvalue['id'],'cbse_exam_students.cbse_exam_id'=>$examvalue['id'],'cbse_exam_students.student_session_id'=>$student_session_id))->get()->result_array();

            $assesment_marks=array();
            foreach ($assesment_mark as $assesment_markkey => $assesment_markvalue) {
                $assesment_markvalue['mark']=$assesment_markvalue['mark'];
                $assesment_markvalue['grade']=$this->get_grade($assesment_markvalue['mark'],$grade);   
                $assesment_marks[$assesment_markvalue['subject_id']]=$assesment_markvalue;              
               
            }

            $termsdata[$value['id']]['exam'][$examvalue['id']]['exam_assessments'][$exam_assessmentsvalue['id']]['marks']=$assesment_marks;
            }

            $termsdata[$value['id']]['exam'][$examvalue['id']]['colspan']=count($exam_assessments);          

            }
            
            $termsdata[$value['id']]['colspan']=$total_assignment;
            $total_td+=$total_assignment;
        }
            
            $returndata['td']=$total_td;        
            $returndata['result']=$termsdata;
            $returndata['subjects']=$subjects;
            return $returndata;
    }


    public function getexamwiseresult($student_session_id)
    {
        $grade=$this->db->select('*')->from('cbse_exam_grades_range')->where('cbse_exam_grade_id',1)->get()->result_array();  

        $subject=$this->db->select('subjects.*')->from('cbse_exam_students')->join('cbse_exam_timetable','cbse_exam_timetable.cbse_exam_id=cbse_exam_students.cbse_exam_id')->join('subjects','subjects.id=cbse_exam_timetable.subject_id')->where('cbse_exam_students.student_session_id',$student_session_id)->group_by('cbse_exam_timetable.subject_id')->get()->result_array();
        
        $subjects=array();
        
        foreach($subject as $skey=>$svalue){
            $subjects[$svalue['id']]['name']=$svalue['name'];
        }
        
        $total_td=0;
   
            $exam=$this->db->select('*')->from('cbse_exams')->where('id',1)->get()->result_array();
            $total_assignment=1;

            foreach ($exam as $examkey => $examvalue) {
            $termsdata['exam'][$examvalue['id']]['name']=$examvalue['name'];
            $exam_assessments=$this->db->select('cbse_exam_assessment_types.*')->from('cbse_exam_assessments')->join('cbse_exam_assessment_types','cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exam_assessments.id')->where('cbse_exam_assessments.id',$examvalue['cbse_exam_assessment_id'])->get()->result_array();

            $total_assignment+=count($exam_assessments);
            
            foreach ($exam_assessments as $exam_assessmentskey => $exam_assessmentsvalue) {

            $termsdata['exam'][$examvalue['id']]['exam_assessments'][$exam_assessmentsvalue['id']]=$exam_assessmentsvalue;
            $assesment_mark=$this->db->select('cbse_student_subject_marks.mark,cbse_student_subject_result.subject_id')->from('cbse_student_subject_result')->join('cbse_exam_students','cbse_exam_students.id=cbse_student_subject_result.cbse_exam_student_id')->join('cbse_student_subject_marks','cbse_student_subject_marks.cbse_student_subject_result_id=cbse_student_subject_result.id')->where(array('cbse_student_subject_marks.cbse_exam_assessment_type_id'=>$exam_assessmentsvalue['id'],'cbse_exam_students.cbse_exam_id'=>$examvalue['id'],'cbse_exam_students.student_session_id'=>$student_session_id))->get()->result_array();

            $assesment_marks=array();
            foreach ($assesment_mark as $assesment_markkey => $assesment_markvalue)
            {
                $assesment_markvalue['mark']=$assesment_markvalue['mark'];
                $assesment_markvalue['grade']=$this->get_grade($assesment_markvalue['mark'],$grade);   
                $assesment_marks[$assesment_markvalue['subject_id']]=$assesment_markvalue;
               
            }

            $termsdata['exam'][$examvalue['id']]['exam_assessments'][$exam_assessmentsvalue['id']]['marks']=$assesment_marks;
            }

            $termsdata['exam'][$examvalue['id']]['colspan']=count($exam_assessments);          

            }
            
            $termsdata['colspan']=$total_assignment;
            $total_td+=$total_assignment;
            $returndata['td']=$total_td;        
            $returndata['result']=$termsdata;
            $returndata['subjects']=$subjects;
            return $returndata;
    }
 

}