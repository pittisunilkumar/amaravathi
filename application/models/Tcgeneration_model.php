<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Tcgeneration_model extends MY_Model
{
    public function getsubjectname($subid){
        $this->db->select('name')->from('subjects');
        $this->db->where('id', $subid);
        $query = $this->db->get();
        $result = $query->row();
        if ($result) {
            return $result->name;
        } else {
            return null; // or any default value you prefer if the subject is not found
        }
    }
    

    // public function getsubjectname($subid){
    //     $this->db->select('name')->from('subjects');
    //     $this->db->where('id', $subid);
    //     $query = $this->db->get();
    //     return $query->row();
    // }

    public function addtcgenerate($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('tc_generation', $data);
            $message = UPDATE_RECORD_CONSTANT . " On  id tc generation id " . $data['id'];
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
            $this->db->insert('tc_generation', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On tc generation id " . $insert_id;
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
            return $insert_id;
        }
    }

    public function idcardlist() {
        $this->db->select('*');
        $this->db->from('tc_generation');
        $query = $this->db->get();
        return $query->result();
    }

    public function idcardbyid($id) {
        $this->db->select('*');
        $this->db->from('tc_generation');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }


    public function get($id) {
        $this->db->select('*');
        $this->db->from('tc_generation');
        $this->db->where('status = 1');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result();
    }



    public function remove($id) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('tc_generation');
        $message = DELETE_RECORD_CONSTANT . " On id tc generation id " . $id;
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

    public function getcertificatebyid($certificate)
    {
        $this->db->select('*');
        $this->db->from('tc_generation');
        $this->db->where('id', $certificate);
        $query = $this->db->get();
        return $query->result();
    }

    public function getstudentcertificate()
    {
        $this->db->select('*');
        $this->db->from('tc_generation');
        $query = $this->db->get();
        return $query->result();
    }


    public function getsubjects(){
        $this->db->select('*');
        $this->db->from('subjects');
        $query = $this->db->get();
        return $query->result_array();
    }


    public function getStudentsByArray($array)
    {
        $i             = 1;
        $custom_fields = $this->customfield_model->get_custom_fields('students');

        $field_var_array = array();
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $custom_fields_key => $custom_fields_value) {
                $tb_counter = "table_custom_" . $i;
                array_push($field_var_array, 'table_custom_' . $i . '.field_value as ' . $custom_fields_value->name);
                $this->db->join('custom_field_values as ' . $tb_counter, 'students.id = ' . $tb_counter . '.belong_table_id AND ' . $tb_counter . '.custom_field_id = ' . $custom_fields_value->id, 'left');
                $i++;
            }
        }

        $field_variable = implode(',', $field_var_array);

        $this->db->select('classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename,students.lastname,students.image,   students.mobileno,students.email,students.state,students.city,students.pincode,students.religion,students.dob ,students.current_address,students.blood_group,students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.cast,students.bank_name, students.ifsc_code,students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.mother_name,students.updated_at,students.father_name,students.rte,students.gender,users.id as `user_tbl_id`,users.username,users.password as `user_tbl_password`,users.is_active as `user_tbl_active`,' . $field_variable)->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->join('users', 'users.user_id = students.id', 'left');
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('users.role', 'student');
        $this->db->where_in('students.id', $array);
        $this->db->order_by('students.id');
        $this->db->group_by('students.id');
        $query = $this->db->get();
        return $query->result();
    }


}