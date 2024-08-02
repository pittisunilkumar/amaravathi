<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Subjects extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('resultsubjects_model');
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('results_subject_branch', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'category/index');
        $data['title']        = $this->lang->line('category_list');
        $category_result      = $this->resultsubjects_model->get();
        $data['categorylist'] = $category_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/results/subjects', $data);
        $this->load->view('layout/footer', $data);
    }

    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('results_subject_branch', 'can_view')) {
            access_denied();
        }
        $data['title']    = $this->lang->line('category_list');
        $category         = $this->resultsubjects_model->get($id);
        $data['category'] = $category;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/results/resulttypeshow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('results_subject_branch', 'can_delete')) {
            access_denied();
        }
        $data['title'] = $this->lang->line('category_list');
        $this->resultsubjects_model->remove($id);
        $this->session->set_flashdata('msgdelete', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/results/subjects/index');
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('results_subject_branch', 'can_add')) {
            access_denied();
        }
        $data['title']        = $this->lang->line('add_category');
        $category_result      = $this->resultsubjects_model->get();
        $data['categorylist'] = $category_result;
        $this->form_validation->set_rules('category', $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subjectcode', $this->lang->line('subjectcode'), 'trim|required|xss_clean|callback_check_subjectcode');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/subjects', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'examtype' => $this->input->post('category'),
                'subject_code' =>strtoupper($this->input->post('subjectcode'))
            );
            $this->resultsubjects_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/results/subjects/index');
        }
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('results_subject_branch', 'can_edit')) {
            access_denied();
        }
        $data['title']        = $this->lang->line('edit_category');
        $category_result      = $this->resultsubjects_model->get();
        $data['categorylist'] = $category_result;
        $data['id']           = $id;
        $category             = $this->resultsubjects_model->get($id);
        $data['category']     = $category;
        $this->form_validation->set_rules('category', $this->lang->line('category'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('subjectcode', $this->lang->line('subjectcode'), 'trim|required|xss_clean|callback_check_subjectcode');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/subjectsedit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'       => $id,
                'examtype' => $this->input->post('category'),
                'subject_code' =>strtoupper($this->input->post('subjectcode'))
            );
            $this->resultsubjects_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/results/subjects/index');
        }
    }


    public function check_subjectcode($str){
        $this->load->database();

        $subcode = $this->security->xss_clean($str);
        
        // Replace 'your_table_name' with the actual name of your database table
        $query = $this->db->get_where('resultsubjects', array('subject_code' => $subcode));
        
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('check_subjectcode', $this->lang->line('record_already_exist'));
            return false;
        }
        
        return true;
    }

}
