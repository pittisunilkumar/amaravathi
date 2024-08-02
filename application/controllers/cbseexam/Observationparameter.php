<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Observationparameter extends MY_Addon_CBSEController
{

    public function __construct()
    {
        parent::__construct();
    
        $this->current_session    = $this->setting_model->getCurrentSession();
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {      
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/observation_parameter');
        $data['title'] = 'Observationparameter List';
        $section_result      = $this->cbseexam_observation_parameter_model->get();
        $data['parameterlist'] = $section_result;
        $this->form_validation->set_rules('parameter', $this->lang->line('parameter'), 'trim|required|xss_clean');  

         $this->form_validation->set_rules(
            'parameter', $this->lang->line('parameter'), array(
                'required',
                array('parameter_exists', array($this->cbseexam_observation_parameter_model, 'parameter_exists')),
            )
        );

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('cbseexam/observationparameter/list', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'name' => $this->input->post('parameter'),
            );
            $this->cbseexam_observation_parameter_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('cbseexam/observationparameter');
        }
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_observation_parameter', 'can_delete')) {
            access_denied();
        }
       
        $this->cbseexam_observation_parameter_model->remove($id);           
        redirect('cbseexam/observationparameter');
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_observation_parameter', 'can_edit')) {
            access_denied();
        }
       
        $section_result      = $this->cbseexam_observation_parameter_model->get();
        $data['parameterlist'] = $section_result;
        $data['title']       = 'Edit Section';
        $data['id']          = $id;
        $parameter             = $this->cbseexam_observation_parameter_model->get($id);
        $data['parameter']     = $parameter;

        $this->form_validation->set_rules(
            'parameter', $this->lang->line('parameter'), array(
                'required',
                array('parameter_exists', array($this->cbseexam_observation_parameter_model, 'parameter_exists')),
            )
        );

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('cbseexam/observationparameter/edit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'      => $id,
                'name' => $this->input->post('parameter'),
            );
            $this->cbseexam_observation_parameter_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('cbseexam/observationparameter');
        }
    }

}
