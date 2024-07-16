<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Accounttype extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('accounttype_model');
    }

    public function index()
    {
        // if (!$this->rbac->hasPrivilege('fees_type', 'can_view')) {
        //     access_denied();
        // }
        // $this->session->set_userdata('top_menu', 'Fees Collection');
        // $this->session->set_userdata('sub_menu', 'feetype/index');

        $this->form_validation->set_rules(
            'code', $this->lang->line('fees_code'), array(
                'required',
                array('check_exists', array($this->accounttype_model, 'check_exists')),
            )
        );
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        if ($this->form_validation->run() == false) {

        } else {
            $data = array(
                'type'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'description' => $this->input->post('description'),
            );

            $status=$this->accounttype_model->add($data);
            if($status==false){
                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">' . $this->lang->line('danger_message') . '</div>');
            }else{
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            }
            redirect('admin/accounttype/index');
        }
        $feegroup_result     = $this->accounttype_model->get();
        $data['feetypeList'] = $feegroup_result;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/accounttype/feetypeList', $data);
        $this->load->view('layout/footer', $data);

    }

    public function delete($id)
    {
        // if (!$this->rbac->hasPrivilege('fees_type', 'can_delete')) {
        //     access_denied();
        // }

        $this->accounttype_model->remove($id);
        redirect('admin/accounttype/index');
    }

    public function edit($id)
    {
        // if (!$this->rbac->hasPrivilege('fees_type', 'can_edit')) {
        //     access_denied();
        // }
        // $this->session->set_userdata('top_menu', 'Fees Collection');
        // $this->session->set_userdata('sub_menu', 'feetype/index');
        $data['id']          = $id;
        $feetype             = $this->accounttype_model->get($id);
        $data['feetype']     = $feetype;
        $feegroup_result     = $this->accounttype_model->get();
        $data['feetypeList'] = $feegroup_result;
        $this->form_validation->set_rules(
            'name', $this->lang->line('name'), array(
                'required',
                array('check_exists', array($this->accounttype_model, 'check_exists')),
            )
        );
        $this->form_validation->set_rules('code', $this->lang->line('fees_code'), 'required');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/accounttype/feetypeEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'          => $id,
                'type'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'description' => $this->input->post('description'),
            );
            $this->accounttype_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/accounttype/index');
        }
    }

}




