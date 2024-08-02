<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Assessment extends MY_Addon_CBSEController
{

    public function __construct()
    {
        parent::__construct();
     
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_assessment', 'can_view')) {
            access_denied();
        }
        
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/assessment');        
        $data['result']=$this->cbseexam_assessment_model->getassessmentlist();    
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/assessment/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_grade', 'can_add')) {
            access_denied();
        }
          $this->form_validation->set_rules(
            'name', $this->lang->line('assessment'), array(
                'required',
                array('check_exists', array($this->cbseexam_assessment_model, 'check_exists')),
            )
        );

        $rows = $this->input->post('row');
        if (!isset($rows)) {
            $this->form_validation->set_rules('rows', 'rows', 'trim|required|xss_clean',array('required' => 'No assessment type added.'));
        } elseif (isset($rows) && !empty($rows)) {

            foreach ($this->input->post('row') as $row_key => $row_value) {

                if ($this->input->post('type_name_' . $row_value) == "") {
                    $this->form_validation->set_rules('assessment_type', $this->lang->line('assessment_type'), 'trim|required|xss_clean');
                }

                if ($this->input->post('maximum_marks_' . $row_value) == "") {
                    $this->form_validation->set_rules('maximum_marks', $this->lang->line('maximum_marks'), 'trim|required|xss_clean');
                }

                if ($this->input->post('pass_percentage_' . $row_value) == "") {
                    $this->form_validation->set_rules('pass_percentage', $this->lang->line('pass_percentage'), 'trim|required|xss_clean');
                } 

                if ($this->input->post('pass_percentage_' . $row_value) > 100) {
                    $this->form_validation->set_rules('percentage_greater', $this->lang->line('pass_percentage'), 'callback_percentage_greater[' . $this->input->post('pass_percentage_' . $row_value) . ']');
                }
            }
        }

        if ($this->form_validation->run() == false) {
            
            $msg['name']               = form_error('name');
            $msg['rows']               = form_error('rows');
            $msg['assessment_type']         = form_error('assessment_type');
            $msg['maximum_marks'] = form_error('maximum_marks');
            $msg['pass_percentage'] = form_error('pass_percentage');
            $msg['percentage_greater'] = form_error('percentage_greater');
            
            $array                     = array('status' => 0, 'error' => $msg);
        } else {
            $cbse_exam_assessment_type = [];
            $cbse_exam_assessment_type_update = [];
            $insert_assessment           = [
                'name'        => $this->input->post('name'),
                'description' => $this->input->post('description'),
            ];
            $delete_assessment_type = [];
            if ($this->input->post('action') == "update") {
                $insert_assessment['id']=$this->input->post('record_id');
                $prev_ids           = $this->input->post('prev_ids');
                $update_id          = $this->input->post('update_id');
                $delete_assessment_type = array_diff($prev_ids, $update_id);
            }

            $update_ids = $this->input->post('update_id');

            foreach ($_POST['row'] as $row_key => $row_value) {
                if ($update_ids[$row_key] > 0) {
                    $update_grade_range = array(
                        'id' => $update_ids[$row_key],
                        'cbse_exam_assessment_id' =>  $insert_assessment['id'],
                        'name'               => $this->input->post('type_name_' . $row_value),
                        'code'               => $this->input->post('code_' . $row_value),
                        'maximum_marks' => $this->input->post('maximum_marks_' . $row_value),
                        'pass_percentage' => $this->input->post('pass_percentage_' . $row_value),
                        'description'        => $this->input->post('type_description_' . $row_value),
                        'created_by'         => $this->customlib->getStaffID(),
                    );
                    $cbse_exam_assessment_type_update[] = $update_grade_range;
                } else {
                    $insert_assessment_range = array(
                        'cbse_exam_assessment_id' => '',
                        'name'               => $this->input->post('type_name_' . $row_value),
                        'code'               => $this->input->post('code_' . $row_value),
                        'maximum_marks' => $this->input->post('maximum_marks_' . $row_value),
                        'pass_percentage' => $this->input->post('pass_percentage_' . $row_value),
                        'description'        => $this->input->post('type_description_' . $row_value),
                        'created_by'         => $this->customlib->getStaffID(),
                    );
                    $cbse_exam_assessment_type[] = $insert_assessment_range;
                }

            }          

            $this->cbseexam_assessment_model->add_graderange($insert_assessment, $cbse_exam_assessment_type,$cbse_exam_assessment_type_update,$delete_assessment_type);       

            $array = array('status' => 1, 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

function percentage_greater($str, $str2)
{   
   
    if($str2 > 0 && $str2 < 100)
    {
        // return success
        return true;
    }
    else
    {
        // set error message
        $this->form_validation->set_message('percentage_greater', $this->lang->line('percentage_should_be_greater_than_or_less'));

        // return fail
        return FALSE;
    }
}

    public function get_editdetails(){
        $id=$this->input->post('id');
        $result=$this->cbseexam_assessment_model->get_editdetails($id);
        echo json_encode($result);
    } 

    public function assessmentform()
    {
        $data              = array();
        $data['action']    = $this->input->post('action');
        $data['record_id'] = $this->input->post('record_id');
        $total_rows        = 2;
        if ($data['record_id'] > 0) {
            $get_old_data         = $this->cbseexam_assessment_model->getWithAssessmentType($data['record_id']);
            $total_rows           = count($get_old_data['list']) + 1;
            $data['get_old_data'] = $get_old_data;
        }

        $page = $this->load->view("cbseexam/assessment/partial/_assessmentform", $data, true);

        echo json_encode(['status' => 1, 'page' => $page, 'total_rows' => $total_rows]);
    }

    public function add_type(){
        $data=array();
        $id=$this->input->post('id');
        $result=$this->cbseexam_assessment_model->get_assessmentTypebyId($id);
        $data['result']=$result;
        $data['delete_string']=$this->input->post('delete_string');
        echo json_encode($this->load->view("cbseexam/assessment/_add_type", $data, true));
    }

    public function remove()
    {        
        if (!$this->rbac->hasPrivilege('cbse_exam_assessment', 'can_delete')) {
            access_denied();
        }
        
        $id = $this->input->post('id');
        $this->cbseexam_assessment_model->remove($id);
        $array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('delete_message'));
        echo json_encode($array);
    }
}
