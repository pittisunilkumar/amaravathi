<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Term extends MY_Addon_CBSEController
{

    public function __construct()
    {
        parent::__construct();
      
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_term', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/term');
        $data['categorylist']     = $this->category_model->get();
        $data['subjectgroupList'] = $this->subjectgroup_model->getByID(); 
        $data['classlist'] = $this->class_model->get();
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/term/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        $id = $this->input->post('id');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('term_code', $this->lang->line('code'), 'trim|required|xss_clean|callback_check_duplicate_code');

        if ($this->form_validation->run() == false) {
            $msg = [
                'name'      => form_error('name'),
                'term_code' => form_error('term_code'),
            ];

            $array = array('status' => 'fail', 'error' => $msg);

        } else {

            $data = array(
                'id'          => $id,
                'name'        => $this->input->post('name'),
                'term_code'   => $this->input->post('term_code'),
                'description' => $this->input->post('description'),
                'created_by'  => $this->customlib->getStaffID(),
            );

            if (!empty($id)) {

                $this->cbseexam_term_model->add($data);

            } else {

                $term_id = $this->cbseexam_term_model->add($data);

            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

        }
        echo json_encode($array);

    }

    public function check_duplicate_code()
    {
        $term_code = $this->input->post('term_code');
        $id        = $this->input->post('id');
        if($term_code != ""){
            
                if (isset($id) && $id == "") {
                    $id = 0;
                }

                if ($this->cbseexam_term_model->check_check_duplicate_code($term_code, $id)) {
                    $this->form_validation->set_message('check_duplicate_code', $this->lang->line('term_code_already_exists'));
                    return false;
                } else {
                    return true;
                }
        }

        return true;

    }

    public function getdata()
    {
        $id     = $this->input->post('id');
        $result = $this->cbseexam_term_model->get($id);     
        echo json_encode(array('status' => 1, 'result' => $result));
    }

    public function get_ClassSectionByTermId($termid)
    {
        $result           = $this->cbseexam_term_model->get($termid);
        $data['class_id'] = $result->class_id;
        $data['sections'] = $this->section_model->getClassBySection($result->class_id);
        echo json_encode($data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_term', 'can_delete')) {
            access_denied();
        }       
        $this->cbseexam_term_model->remove($id);           
        redirect('cbseexam/term');
    }

    public function gettermlist()
    {
        $m = $this->cbseexam_term_model->gettermlist();
        $m = json_decode($m);
        $dt_data = array();
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $editbtn   = '';
                $deletebtn = '';
                $documents = '';

                if ($this->rbac->hasPrivilege('cbse_exam_term', 'can_edit')) {                   
                    
                    $editbtn = "<button  class='btn btn-default btn-xs edit_term'  data-toggle='tooltip' data-recordid=" . $value->id . "  title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></button>"; 
                    
                }

                if ($this->rbac->hasPrivilege('cbse_exam_term', 'can_delete')) {
                   
                    $deletebtn = "<a onclick='return confirm("."\"". $this->lang->line('delete_confirm') ."\"". ")' href='" . base_url() . "cbseexam/term/delete/" . $value->id . "' class='btn btn-default btn-xs' title='" . $this->lang->line('delete') . "' data-toggle='tooltip'><i class='fa fa-trash'></i></a>";
                }

                $row   = array();
                $row[] = $value->name;
                $row[] = $value->term_code;
                $row[] = $value->description;
                $row[]     = $editbtn . ' ' . $deletebtn;
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }

}
