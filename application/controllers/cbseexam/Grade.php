<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Grade extends MY_Addon_CBSEController
{

    public function __construct()
    {
        parent::__construct();
    
    }

    public function gradelist()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_grade', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/grade');
        $data['result'] = $this->cbseexam_grade_model->getgradelist();
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/grade/gradelist', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_grade', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('name', $this->lang->line('grade_title'), 'trim|required|xss_clean');
        $rows = $this->input->post('row');
        if (!isset($rows)) {
            $this->form_validation->set_rules('rows', $this->lang->line('grade'), 'trim|required|xss_clean');
        } elseif (isset($rows) && !empty($rows)) {
            foreach ($this->input->post('row') as $row_key => $row_value) {

                if ($this->input->post('range_name_' . $row_value) == "") {
                    $this->form_validation->set_rules('range_name', $this->lang->line('grade'), 'trim|required|xss_clean');
                }

                if ($this->input->post('maximum_percentage_' . $row_value) == "") {
                    $this->form_validation->set_rules('maximum_percentage', $this->lang->line('maximum_percentage'), 'trim|required|xss_clean');
                }

                if ($this->input->post('minimum_percentage_' . $row_value) == "") {
                    $this->form_validation->set_rules('minimum_percentage', $this->lang->line('minimum_percentage'), 'trim|required|xss_clean');
                }
            }
        }

        if ($this->form_validation->run() == false) {
            $msg['name']               = form_error('name');
            $msg['rows']               = form_error('rows');
            $msg['range_name']         = form_error('range_name');
            $msg['maximum_percentage'] = form_error('maximum_percentage');
            $msg['minimum_percentage'] = form_error('minimum_percentage');
            $array                     = array('status' => 0, 'error' => $msg);
        } else {
            $cbse_exam_grades_range = [];
            $cbse_exam_grades_range_update = [];
            $insert_grade           = [
                'name'        => $this->input->post('name'),
                'description' => $this->input->post('description'),
            ];
            $delete_grade_range = [];
            if ($this->input->post('action') == "update") {
                $insert_grade['id']=$this->input->post('record_id');
                $prev_ids           = $this->input->post('prev_ids');
                $update_id          = $this->input->post('update_id');
                $delete_grade_range = array_diff($prev_ids, $update_id);
            }

            $update_ids = $this->input->post('update_id');

            foreach ($_POST['row'] as $row_key => $row_value) {
                if ($update_ids[$row_key] > 0) {
                    $update_grade_range = array(
                        'id' => $update_ids[$row_key],
                        'cbse_exam_grade_id' =>  $insert_grade['id'],
                        'name'               => $this->input->post('range_name_' . $row_value),
                        'minimum_percentage' => $this->input->post('minimum_percentage_' . $row_value),
                        'maximum_percentage' => $this->input->post('maximum_percentage_' . $row_value),
                        'description'        => $this->input->post('type_description_' . $row_value),
                        'created_by'         => $this->customlib->getStaffID(),
                    );
                    $cbse_exam_grades_range_update[] = $update_grade_range;
                } else {
                    $insert_grade_range = array(
                        'cbse_exam_grade_id' => '',
                        'name'               => $this->input->post('range_name_' . $row_value),
                        'minimum_percentage' => $this->input->post('minimum_percentage_' . $row_value),
                        'maximum_percentage' => $this->input->post('maximum_percentage_' . $row_value),
                        'description'        => $this->input->post('type_description_' . $row_value),
                        'created_by'         => $this->customlib->getStaffID(),
                    );
                    $cbse_exam_grades_range[] = $insert_grade_range;
                }
            }

            $this->cbseexam_grade_model->add_graderange($insert_grade, $cbse_exam_grades_range,$cbse_exam_grades_range_update,$delete_grade_range);
            $array = array('status' => 1, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function get_editdetails()
    {
        $id     = $this->input->post('id');
        $result = $this->cbseexam_grade_model->get_editdetails($id);
        echo json_encode($result);
    }

    public function add_graderange()
    {
        $data           = array();
        $id             = $this->input->post('id');
        $result         = $this->cbseexam_grade_model->get_graderangebyId($id);
        $data['result'] = $result;
        $data['delete_string'] = $this->input->post('delete_string');
        echo json_encode($this->load->view("cbseexam/grade/_add_graderange", $data, true));
    }

    public function gradeform()
    {
        $data              = array();
        $data['action']    = $this->input->post('action');
        $data['record_id'] = $this->input->post('record_id');
        $total_rows        = 2;
        if ($data['record_id'] > 0) {
            $get_old_data         = $this->cbseexam_grade_model->getWithRange($data['record_id']);
            $total_rows           = count($get_old_data['list']) + 1;
            $data['get_old_data'] = $get_old_data;
        }

        $page = $this->load->view("cbseexam/grade/partial/_gradeform", $data, true);
        echo json_encode(['status' => 1, 'page' => $page, 'total_rows' => $total_rows]);
    }

    public function remove()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_grade', 'can_delete')) {
            access_denied();
        }
        $id = $this->input->post('id');
        $this->cbseexam_grade_model->remove($id);
        $array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('delete_message'));
        echo json_encode($array);
    }
}
