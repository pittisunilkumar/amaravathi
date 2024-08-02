<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cbseexam_template_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /*
    This function is used to add and edit cbse exam term
    */
	public function add($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('cbse_template', $data);
            $message = UPDATE_RECORD_CONSTANT . " On cbse terms id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
            //======================Code End==============================
            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
        } else {
            $this->db->insert('cbse_template', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On cbse terms id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
            //======================Code End==============================
            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
            return $record_id;
        }
    }

    public function add_class_section($data){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->insert('cbse_template_class_sections',$data);
        
        $insert_id = $this->db->insert_id();
        $message = INSERT_RECORD_CONSTANT . " On cbse template class sections id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }  
    }

    public function deleteclasssectionbytemplateid($template_id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('cbse_template_id',$template_id);
        $this->db->delete('cbse_template_class_sections');        
        $message = DELETE_RECORD_CONSTANT . " On cbse template class sections id " . $template_id;
        $action = "Delete";
        $record_id = $template_id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $template_id;
        }
    }

    public function delete_class_section($id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('cbse_term_id',$id);
        $this->db->delete('cbse_term_class_sections');
        
        $message = DELETE_RECORD_CONSTANT . " On cbse term class sections id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $id;
        }
    }

    public function gettemplatelist(){
    	return $this->db->select('cbse_template.*,CONCAT(classes.class, ": ", GROUP_CONCAT(sections.section ORDER BY sections.section ASC SEPARATOR ",")) AS class_sections')->from('cbse_template')->join('cbse_template_class_sections','cbse_template_class_sections.cbse_template_id=cbse_template.id')->join('class_sections','class_sections.id=cbse_template_class_sections.class_section_id')->join('classes','classes.id=class_sections.class_id')->join('sections','sections.id=class_sections.section_id')->group_by('cbse_template.id')->where('session_id',$this->current_session)->get()->result_array();
    }

    public function gettermbyid($id){
    	return $this->db->select('cbse_terms.*,class_sections.class_id,class_sections.section_id,class_sections.id as class_section_id,sections.section ')->from('cbse_term_class_sections')->join('cbse_terms','cbse_terms.id=cbse_term_class_sections.cbse_term_id')->join('class_sections','class_sections.id=cbse_term_class_sections.class_section_id')->join('sections','sections.id=class_sections.section_id')->where('cbse_term_class_sections.cbse_term_id',$id)->get()->result_array();
    }

    public function getClassSectionByTermId($term_id){
       $cbse_term_class_section= $this->db->select('class_section_id')->from('cbse_term_class_sections')->where('cbse_term_id',$term_id)->get()->result_array();
       foreach ($cbse_term_class_section as $key => $value) {
           $cbse_term_class_sections[] = $value['class_section_id'];
       }
       return $cbse_term_class_sections;
    }

    public function remove($id){
    	
    	$this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->deleteclasssectionbytemplateid($id);

        $this->db->where('id',$id);
        $this->db->delete('cbse_template');
        $message = DELETE_RECORD_CONSTANT . " On cbse template id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }

    }

    public function all_term($template_id){
       $result= $this->db->select('cbse_terms.*')->from('cbse_exam_class_sections')->join('cbse_template_class_sections','cbse_template_class_sections.class_section_id=cbse_exam_class_sections.class_section_id')->join('cbse_exams','cbse_exams.id=cbse_exam_class_sections.cbse_exam_id')->join('cbse_terms','cbse_terms.id=cbse_exams.cbse_term_id')->where('cbse_exams.session_id',$this->current_session)->where('cbse_template_class_sections.cbse_template_id',$template_id)->where('cbse_exams.session_id',$this->current_session)->group_by('cbse_terms.id')->get()->result_array();
        $return=array();
        foreach ($result as $key => $value) {
            $exam=$this->db->select('cbse_exams.*')->from('cbse_exams')->where('cbse_term_id',$value['id'])->where('cbse_exams.session_id',$this->current_session)->get()->result_array();
            $return[$value['id']]['name']=$value['name'];
            $return[$value['id']]['exam']=$exam;
        }
        return $return;
    }

    public function getTermByTemplateId($template_id){
            $result=$this->db->select('cbse_template_terms.*,cbse_terms.name,cbse_terms.term_code')->from('cbse_template_terms')->join('cbse_terms','cbse_terms.id=cbse_template_terms.cbse_term_id')->where('cbse_template_id',$template_id)->get()->result();

        return $result;
    }

    public function getTemplateTermExamWithAssessment($cbse_template_id){
        $return_array=[
            'subjects'=>[],
            'terms'=>[]
        ];
           $sql   = "SELECT cbse_template.*,cbse_template_terms.cbse_term_id,cbse_template_term_exams.cbse_exam_id,cbse_template_terms.weightage,subjects.id as subject_id,subjects.name as subject_name,subjects.code as subject_code,cbse_terms.name as term_name ,cbse_terms.term_code as term_code  FROM `cbse_template` INNER join cbse_template_terms on cbse_template_terms.cbse_template_id=cbse_template.id INNER join cbse_terms on  cbse_terms.id=cbse_template_terms.cbse_term_id INNER JOIN cbse_template_term_exams on cbse_template_term_exams.cbse_template_term_id=cbse_template_terms.id INNER join cbse_exam_timetable on cbse_exam_timetable.cbse_exam_id =cbse_template_term_exams.cbse_exam_id INNER join subjects on subjects.id=cbse_exam_timetable.subject_id WHERE cbse_template.id=".$this->db->escape($cbse_template_id)." GROUP BY cbse_exam_timetable.subject_id" ;
        
           $query = $this->db->query($sql);
           $result= $query->result();           
   
           if(!empty($result)){

            $getTermByTemplate=$this->getTermByTemplateId($cbse_template_id);
          
               $subjects=[];
               $terms=[];
               foreach ($result as $result_key => $result_value) {
                
                 $subjects[$result_value->subject_id]= $result_value->subject_name." (".$result_value->subject_code.")";
       
               }

               if(!empty($getTermByTemplate)){
                foreach ($getTermByTemplate as $tm_key => $tm_value) {

                    if(!array_key_exists($tm_value->cbse_term_id,$terms)){
                       
                        $terms[$tm_value->cbse_term_id]=[
                                'cbse_term_id'=>$tm_value->cbse_term_id,
                                'cbse_term_name'=>$tm_value->name,
                                'cbse_term_code'=>$tm_value->term_code,
                                'cbse_term_weight'=>$tm_value->weightage,
                                'term_total_assessments'=>[],
                                'exams'=>[],
                                ];        
                      }
                }                
               }

               $return_array['subjects']=$subjects;  
               $return_array['terms']=$terms;  
           }

            if(!empty($return_array)){
                foreach ($return_array['terms'] as $term_array_key => $term_array_value) {
                    $term_exam_array=[];
                    $term_assessment_array=[];
                    $term_exams=$this->getTermExamsWithAssessmentAndAssmentTypes($cbse_template_id,$term_array_key);
                    foreach ($term_exams as $term_exam_key => $term_exam_value) {
                        $term_assessment_array[]=  [
                            'cbse_exam_assessment_id'=>$term_exam_value->cbse_exam_assessment_id,
                            'cbse_exam_assessment_type_id'=>$term_exam_value->cbse_exam_assessment_type_id,
                            'name'=>$term_exam_value->cbse_exam_assessment_type_name,
                            'code'=>$term_exam_value->cbse_exam_assessment_type_code,
                            'maximum_marks'=>$term_exam_value->maximum_marks,
                            'pass_percentage'=>$term_exam_value->pass_percentage
                        ];

                           if(array_key_exists($term_exam_value->cbse_exam_id , $term_exam_array)){
                            $term_exam_array[$term_exam_value->cbse_exam_id]['exam_assessments'][]=[
                                'cbse_exam_assessment_id'=>$term_exam_value->cbse_exam_assessment_id,
                                'cbse_exam_assessment_type_id'=>$term_exam_value->cbse_exam_assessment_type_id,
                                'name'=>$term_exam_value->cbse_exam_assessment_type_name,
                                'code'=>$term_exam_value->cbse_exam_assessment_type_code,
                                'maximum_marks'=>$term_exam_value->maximum_marks,
                                'pass_percentage'=>$term_exam_value->pass_percentage
                            ];
                           }else{                          
                            $term_exam_array[$term_exam_value->cbse_exam_id]=[
                              'cbse_exam_id'=>  $term_exam_value->cbse_exam_id,
                              'exam_name'=>  $term_exam_value->exam_name,
                              'total_working_days'=>  $term_exam_value->total_working_days,
                              'exam_weightage'=>  $term_exam_value->exam_weightage,
                              'exam_assessments'=>[[
                                'cbse_exam_assessment_id'=>$term_exam_value->cbse_exam_assessment_id,
                                'cbse_exam_assessment_type_id'=>$term_exam_value->cbse_exam_assessment_type_id,
                                'name'=>$term_exam_value->cbse_exam_assessment_type_name,
                                'code'=>$term_exam_value->cbse_exam_assessment_type_code,
                                'maximum_marks'=>$term_exam_value->maximum_marks,
                                'pass_percentage'=>$term_exam_value->pass_percentage
                              ]],

                            ];
                           }                       
                    }

                    $return_array['terms'][$term_array_key]['exams']=$term_exam_array;
                    $return_array['terms'][$term_array_key]['term_total_assessments']=$term_assessment_array;
                }
            }

           return $return_array;
       }
    

    public function getTermExamsWithAssessmentAndAssmentTypes($cbse_template_id,$cbse_term_id){

        $sql   = "SELECT cbse_template_terms.*,cbse_template_term_exams.cbse_exam_id,cbse_template_term_exams.weightage as exam_weightage,cbse_exams.name as exam_name, cbse_exams.exam_code, cbse_exams.cbse_exam_assessment_id,cbse_exams.total_working_days,cbse_exam_assessments.name as cbse_exam_assessment_name,cbse_exam_assessment_types.name as cbse_exam_assessment_type_name, cbse_exam_assessment_types.code as cbse_exam_assessment_type_code, cbse_exam_assessment_types.id as cbse_exam_assessment_type_id,maximum_marks,pass_percentage  FROM `cbse_template_terms` INNER join cbse_template_term_exams on cbse_template_term_exams.cbse_template_term_id=cbse_template_terms.id INNER join cbse_exams on cbse_exams.id =cbse_template_term_exams.cbse_exam_id INNER join cbse_exam_assessments on cbse_exam_assessments.id=cbse_exams.cbse_exam_assessment_id INNER join cbse_exam_assessment_types on cbse_exam_assessment_types.cbse_exam_assessment_id=cbse_exam_assessments.id  WHERE cbse_template_terms.cbse_template_id=".$this->db->escape($cbse_template_id)." and cbse_template_terms.cbse_term_id =".$this->db->escape($cbse_term_id)." and cbse_exams.is_publish=1 order by cbse_exams.id desc, cbse_exam_assessment_types.id asc";               
        $query = $this->db->query($sql);
        $result= $query->result();
        return $result;

       }
       
    public function get_templatedata($template_id){
        $tempalateresult=$this->db->select('*')->from('cbse_template')->where('id',$template_id)->get()->row_array();
        $termdata=$this->db->select('*')->from('cbse_template_terms')->where('cbse_template_id',$template_id)->get()->result_array(); 
        foreach ($termdata as $tkey => $tvalue) {
            $exam=$this->db->select('cbse_template_term_exams.*')->from('cbse_template_term_exams')->where('cbse_template_term_id',$tvalue['id'])->get()->result_array();
            $tempalateresult['term_details'][$tvalue['cbse_term_id']]=$tvalue;
            foreach ($exam as $ekey => $evalue) {
                $tempalateresult['term_exam'][$tvalue['cbse_term_id']][$evalue['cbse_exam_id']]=$evalue;
            }
        }
        $exam_without_term=$this->db->select('*')->from('cbse_template_term_exams')->where('cbse_template_id',$template_id)->get()->result_array();
        
        foreach ($exam_without_term as $ewkey => $ewvalue) {
           $tempalateresult['exam_without_term'][$ewvalue['cbse_exam_id']]=$ewvalue['cbse_exam_id'];
           $tempalateresult['exam_without_termweigtage'][$ewvalue['cbse_exam_id']]=$ewvalue['weightage'];
        }
        return $tempalateresult;

    }    

    public function getTemplateTermsOrExam($template_id){
        $template=$this->db->select('*')->from('cbse_template')->where('id',$template_id)->get()->row();

    if($template->marksheet_type == "exam_wise" || $template->marksheet_type == "without_term"){
         $template->{"exams"}=$this->db->select('cbse_template_term_exams.*,cbse_exams.name,cbse_exams.exam_code')
         ->from('cbse_template_term_exams')
          ->join('cbse_exams','cbse_template_term_exams.cbse_exam_id=cbse_exams.id')
         ->where('cbse_template_id',$template->id)
         ->get()
         ->result();
    }elseif ($template->marksheet_type =="all_term" || $template->marksheet_type == "term_wise") {
       $template->{"terms"}=$this->db->select('cbse_template_terms.*,cbse_terms.name,cbse_terms.term_code')
       ->from('cbse_template_terms')
       ->join('cbse_terms','cbse_template_terms.cbse_term_id=cbse_terms.id')
       ->where('cbse_template_id',$template->id)
       ->get()
       ->result();
    }
         return $template;

  }
  
    public function getTemplateWithTermWithExams($template_id){
   
        $terms=$this->db->select('cbse_template_terms.*,cbse_terms.name,cbse_terms.term_code')->from('cbse_template_terms')->join('cbse_terms','cbse_terms.id=cbse_template_terms.cbse_term_id')->where('cbse_template_id',$template_id)->get()->result();

        if(!empty($terms)){
            foreach ($terms as $term_key => $term_value) {
                $term_value->{"exams"}=$this->db->select('cbse_template_term_exams.*,cbse_exams.name,cbse_exams.exam_code')
                ->from('cbse_template_term_exams')
                ->join('cbse_exams','cbse_template_term_exams.cbse_exam_id=cbse_exams.id')
                ->where('cbse_template_term_id',$term_value->id)
                ->get()
                ->result();
            }
        }

        return $terms;
    }

    public function delete_template_record($template_id){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
  
        $this->db->where('`cbse_template_term_exams`.`cbse_template_id`',$template_id);
        $this->db->delete('cbse_template_term_exams');        
        
        $this->db->where('`cbse_template_terms`.`cbse_template_id`',$template_id);
        $this->db->delete('cbse_template_terms');
        
        $message = DELETE_RECORD_CONSTANT . " On cbse template term exams and cbse template terms where cbse template id " . $template_id;
        $action = "Delete";
        $record_id = $template_id;
        $this->log($message, $template_id, $action);
        
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }       
    }

    public function cbse_template_terms($cbse_template_terms){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
            $this->db->insert('cbse_template_terms',$cbse_template_terms);
            $insert_id = $this->db->insert_id();
        
            $message   = INSERT_RECORD_CONSTANT . " On cbse template terms id " . $insert_id;
            $action    = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
    }

    public function cbse_template_term_exams($cbse_template_term_exams){
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
            $this->db->insert('cbse_template_term_exams',$cbse_template_term_exams);
            $insert_id = $this->db->insert_id();
        
            $message   = INSERT_RECORD_CONSTANT . " On cbse template term exams id " . $insert_id;
            $action    = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
    }

    public function getTemplateListbyclasssectionid($class_section_id){
       return $this->db->select('cbse_template.*')->from('cbse_template')
       ->join('cbse_template_class_sections','cbse_template.id=`cbse_template_class_sections`.`cbse_template_id`')
       ->where('marksheet_type is NOT NULL')->where('gradeexam_id is NOT NULL')->where('remarkexam_id is NOT NULL')
       ->where('`cbse_template_class_sections`.`class_section_id`',$class_section_id)
       ->where('`cbse_template`.`session_id`',$this->current_session)
       ->get()
       ->result_array();
    }

    /*
    This function is used to get cbse exam template id
    */
    public function get($template_id){
       $this->db->select('cbse_template.*');
       $this->db->from('cbse_template');
       $this->db->where('cbse_template.id', $template_id);
       $result = $this->db->get();
       return $result->row_array();
    }

    public function getclasssection($template_id){
       $this->db->select('cbse_template_class_sections.*,class_sections.class_id,class_sections.section_id');
       $this->db->from('cbse_template_class_sections');
       $this->db->join('class_sections','class_sections.id=cbse_template_class_sections.class_section_id');
       $this->db->where('cbse_template_class_sections.cbse_template_id', $template_id);
       $result = $this->db->get();
       return $result->result_array();
    }

    public function getStudentExamsResult($cbse_template_id){
        $this->db->select('cbse_template_class_sections.*,class_sections.class_id,class_sections.section_id');
       $this->db->from('cbse_template_term_exams');
       $this->db->join('class_sections','class_sections.id=cbse_template_class_sections.class_section_id');
       $this->db->where('cbse_template_class_sections.cbse_template_id', $template_id);
       $result = $this->db->get();
       return $result->result_array();
    }
}