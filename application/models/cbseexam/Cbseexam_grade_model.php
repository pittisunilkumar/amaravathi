<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cbseexam_grade_model extends MY_Model
{
    /*
    This function is used to add and update cbse exam grade
    */
    public function add($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && !empty($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('cbse_exam_grades', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On cbse exam grades id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
            $insert_id = $data['id'];
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
            $this->db->insert('cbse_exam_grades', $data);
            $insert_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On cbse exam grades id " . $insert_id;
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
                //return $return_value;
            }
        }
        return $insert_id;
    }

    /*
    This function is used to add and update cbse exam grade rang
    */
    public function add_graderange($insert_grade, $cbse_exam_grades_range, $cbse_exam_grades_range_update, $delete_grade_range)
    {     
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($insert_grade['id']) && !empty($insert_grade['id'])) {
            foreach ($cbse_exam_grades_range as $grade_range_key => $grade_range_value) {
                $cbse_exam_grades_range[$grade_range_key]['cbse_exam_grade_id'] = $insert_grade['id'];
            }
            if (!empty($cbse_exam_grades_range)) {
                $this->db->insert_batch('cbse_exam_grades_range', $cbse_exam_grades_range);
            }
            if (!empty($cbse_exam_grades_range_update)) {
                $this->db->update_batch('cbse_exam_grades_range', $cbse_exam_grades_range_update, 'id');
            }
            if (!empty($delete_grade_range)) {
                $this->db->where_in('id', $delete_grade_range);
                $this->db->delete('cbse_exam_grades_range');
            }

            $this->db->where('id', $insert_grade['id']);
            $this->db->update('cbse_exam_grades', $insert_grade);

        } else {
            $this->db->insert('cbse_exam_grades', $insert_grade);
            $insert_id = $this->db->insert_id();
            foreach ($cbse_exam_grades_range as $grade_range_key => $grade_range_value) {
                $cbse_exam_grades_range[$grade_range_key]['cbse_exam_grade_id'] = $insert_id;
            }
            $this->db->insert_batch('cbse_exam_grades_range', $cbse_exam_grades_range);
        }

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

    /*
    This function is used to get cbse exam grade range
    */
    public function getgradelist()
    {
        $list = $this->get();
        if (!empty($list)) {
            foreach ($list as $key => $value) {
                $list[$key]['data'] = $this->db->select('*')->from('cbse_exam_grades_range')->where('cbse_exam_grade_id', $value['id'])->get()->result_array();
            }
        }
        return $list;
    }

    /*
    This function is used to get cbse exam grade
    */
    public function get()
    {
        return $this->db->select('*')->get('cbse_exam_grades')->result_array();
    }

    /*
    This function is used to get cbse exam grade with grade range
    */
    public function getWithRange($id)
    {
        $result         = $this->db->select('*')->where('id', $id)->get('cbse_exam_grades')->row_array();
        $result['list'] = $this->db->select('*')->from('cbse_exam_grades_range')->where('cbse_exam_grade_id', $id)->get()->result_array();
        return $result;
    }

    /*
    This function is used to get cbse exam grade range based on grade id
    */
    public function getGraderangebyGradeID($cbse_exam_grade_id)
    {
        $result = $this->db->select('*')->from('cbse_exam_grades_range')->where('cbse_exam_grade_id', $cbse_exam_grade_id)->get()->result();
        return $result;
    }

    /*
    This function is used to get cbse exam grade range based on id
    */
    public function get_graderangebyId($id)
    {
        $result = $this->db->select('*')->from('cbse_exam_grades_range')->where('id', $id)->get()->row_array();
        return $result;
    }

    public function getExamGrades($cbse_exam_id)
    {
        $result = $this->db->select('cbse_exam_grades_range.*')->from('cbse_exams')
            ->join('cbse_exam_grades', 'cbse_exams.cbse_exam_grade_id = cbse_exam_grades.id')
            ->join('cbse_exam_grades_range', 'cbse_exam_grades.id = cbse_exam_grades_range.cbse_exam_grade_id')
            ->where('cbse_exams.id', $cbse_exam_id)
            ->get()->result();
        return $result;
    }

    /*
    This function is used to delete cbse exam grade range based on range id
    */
    public function remove_graderange($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('cbse_exam_grades_range');
        $message   = DELETE_RECORD_CONSTANT . " On cbse exam grades range id " . $id;
        $action    = "Delete";
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

    /*
    This function is used to delete cbse exam grade based on id
    */
    public function remove($id)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('cbse_exam_grades');
        $message   = DELETE_RECORD_CONSTANT . " On cbse exam grades id " . $id;
        $action    = "Delete";
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

}
