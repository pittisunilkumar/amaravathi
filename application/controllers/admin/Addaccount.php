<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Addaccount extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('addaccount_model');
        $this->load->model('accountcategory_model');
    }

    public function index()
    {
        // if (!$this->rbac->hasPrivilege('fees_type', 'can_view')) {
        //     access_denied();
        // }
        // $this->session->set_userdata('top_menu', 'Fees Collection');
        // $this->session->set_userdata('sub_menu', 'feetype/index');
        
        
        $this->form_validation->set_rules('name', $this->lang->line('accountname'), 'required');
        $this->form_validation->set_rules('code', $this->lang->line('accountcode'), 'required');
        $this->form_validation->set_rules('accountcategory_id', $this->lang->line('accountcategory'), 'required');
        $this->form_validation->set_rules('section_id', $this->lang->line('accounttype'), 'required');
        $this->form_validation->set_rules('gender', $this->lang->line('accountrole'), 'required');
        $this->form_validation->set_rules('sections[]', $this->lang->line('paymentmodes'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {

        } else {
            $section_array = $this->input->post('sections');
            $data = array(
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'account_category'        => $this->input->post('accountcategory_id'),
                'account_type'        => $this->input->post('section_id'),
                'account_role'        => $this->input->post('gender'),
                'description' => $this->input->post('description'),
                'cash' => in_array('cash', $section_array) ? 1 : 0,
                'cheque' => in_array('cheque', $section_array) ? 1 : 0,
                'dd' => in_array('dd', $section_array) ? 1 : 0,
                'bank_transfer' => in_array('bank_transfer', $section_array) ? 1 : 0,
                'upi' => in_array('upi', $section_array) ? 1 : 0,
                'card' => in_array('card', $section_array) ? 1 : 0,
            );

            $status=$this->addaccount_model->add($data);
            if($status==false){
                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">' . $this->lang->line('danger_message') . '</div>');
            }else{
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            }
            redirect('admin/addaccount/index');
        }
        $feegroup_result     = $this->addaccount_model->getaddedaccounts();
        $data['feetypeList'] = $feegroup_result;
        
        $feegroup             = $this->accountcategory_model->get();
        $data['feegroupList'] = $feegroup;

        $genderList                    = $this->customlib->getaccountrole();
        $data['genderList']            = $genderList;


        $this->load->view('layout/header', $data);
        $this->load->view('admin/addaccount/feetypeList', $data);
        $this->load->view('layout/footer', $data);

    }

    public function delete($id)
    {
        // if (!$this->rbac->hasPrivilege('fees_type', 'can_delete')) {
        //     access_denied();
        // }

        $this->addaccount_model->remove($id);
        redirect('admin/addaccount/index');
    }

    public function edit($id)
    {
        // if (!$this->rbac->hasPrivilege('fees_type', 'can_edit')) {
        //     access_denied();
        // }
        // $this->session->set_userdata('top_menu', 'Fees Collection');
        // $this->session->set_userdata('sub_menu', 'feetype/index');
        $data['id']          = $id;

        $feegroup_result     = $this->addaccount_model->getaddedaccounts();
        $data['feetypeList'] = $feegroup_result;
        
        $feegroup             = $this->accountcategory_model->get();
        $data['feegroupList'] = $feegroup;

        $genderList                    = $this->customlib->getaccountrole();
        $data['genderList']            = $genderList;

        $feetype             = $this->addaccount_model->getaddedaccount($id);
        $data['feetype']     = $feetype;
        // $feegroup_result     = $this->addaccount_model->get();
        // $data['feetypeList'] = $feegroup_result;

        $this->form_validation->set_rules(
            'name', $this->lang->line('accountname'), array(
                'required',
                array('check_exists', array($this->addaccount_model, 'check_exists')),
            )
        );

        $this->form_validation->set_rules(
            'code', $this->lang->line('accountcode'), array(
                'required',
                array('check_exists', array($this->addaccount_model, 'check_exists_code')),
            )
        );

        // $this->form_validation->set_rules('name', $this->lang->line('accountname'), 'required');
        // $this->form_validation->set_rules('code', $this->lang->line('accountcode'), 'required');
        $this->form_validation->set_rules('accountcategory_id', $this->lang->line('accountcategory'), 'required');
        $this->form_validation->set_rules('section_id', $this->lang->line('accounttype'), 'required');
        $this->form_validation->set_rules('gender', $this->lang->line('accountrole'), 'required');
        $this->form_validation->set_rules('sections[]', $this->lang->line('paymentmodes'), 'trim|required|xss_clean');

        
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/addaccount/feetypeEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            
            $section_array = $this->input->post('sections');
            $data = array(
                'id'          => $id,
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'account_category'        => $this->input->post('accountcategory_id'),
                'account_type'        => $this->input->post('section_id'),
                'account_role'        => $this->input->post('gender'),
                'description' => $this->input->post('description'),
                'cash' => in_array('cash', $section_array) ? 1 : 0,
                'cheque' => in_array('cheque', $section_array) ? 1 : 0,
                'dd' => in_array('dd', $section_array) ? 1 : 0,
                'bank_transfer' => in_array('bank_transfer', $section_array) ? 1 : 0,
                'upi' => in_array('upi', $section_array) ? 1 : 0,
                'card' => in_array('card', $section_array) ? 1 : 0,
            );

            $this->addaccount_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/addaccount/index');
        }
    }



    public function getaccounttype()
    {
        $class_id = $this->input->get('accountcategory_id');
        $data     = $this->addaccount_model->getaccounttypee($class_id);
        echo json_encode($data);
    }



    public function getaccounts()
    {
        $class_id = strtolower($this->input->get('accountcategory_id'));
        $data     = $this->addaccount_model->getaddedaccountsfee($class_id);
        echo json_encode($data);
    }

}




