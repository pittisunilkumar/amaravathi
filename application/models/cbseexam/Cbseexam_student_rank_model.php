<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cbseexam_student_rank_model extends MY_Model
{

    public function add_rank($student_details, $template_id,$subject_rank)
    {          
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        if ($template_id != "") {
         
            $this->db->where_in('cbse_template_id', $template_id);
            $this->db->delete('cbse_student_template_rank');

            $this->db->where_in('cbse_template_id', $template_id);
            $this->db->delete('cbse_exam_student_subject_rank');

        }

        $subject_array=[];
        
        if(!empty($subject_rank)){
            foreach ($subject_rank as $subject_key => $subject_value) {

           if(!empty($subject_value)){
               foreach ($subject_value as $s_key => $s_value) {
                   $s_value['subject_id']=$subject_key;
                   $subject_array[]=$s_value;
                }
           }
              
            }
            if(!empty($subject_array)){
                $this->db->insert_batch('cbse_exam_student_subject_rank', $subject_array);
            }
        }
      
        $this->db->insert_batch('cbse_student_template_rank', $student_details);

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
        return true;
    }

    public function add_exam_rank($student_details,$exam_id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        if ($exam_id != "") {
         
            $this->db->where_in('cbse_exam_id', $exam_id);
            $this->db->delete('cbse_student_exam_ranks');

        }
      
        $this->db->insert_batch('cbse_student_exam_ranks', $student_details);

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
        return true;
    }


}