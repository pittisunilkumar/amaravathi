<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Accountcategorygroup extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->model('accountcategory_model');
        $this->load->model('accounttype_model');
        $this->load->model('accountcategorygroup_model');
    }

    public function index()
    {

        // $this->session->set_userdata('top_menu', 'Fees Collection');
        // $this->session->set_userdata('sub_menu', 'admin/feemaster');
		
        $data['title']        = $this->lang->line('fees_master_list');
        $feegroup             = $this->accountcategory_model->get();
        $data['feegroupList'] = $feegroup;
        $feetype              = $this->accounttype_model->get();
        $data['feetypeList']  = $feetype;
        $feegroup_result       = $this->accountcategorygroup_model->getFeesByGroup();
        $data['feemasterList'] = $feegroup_result;

        $this->form_validation->set_rules('feetype_id', $this->lang->line('fee_type'), 'required');

        $this->form_validation->set_rules(
            'fee_groups_id', $this->lang->line('fee_group'), array(
                'required',
                array('check_exists', array($this->accountcategorygroup_model, 'valid_check_exists')),
            )
        );

        

        if ($this->form_validation->run() == false) {

        } else {
        
            
            $insert_array = array(
                'accountcategory_id'   => $this->input->post('fee_groups_id'),
                'accounttype_id'      => $this->input->post('feetype_id'),
                
            );

            $feegroup_result = $this->accountcategorygroup_model->add($insert_array);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/accountcategorygroup/index');
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/accountcategorygroup/feemasterList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        // if (!$this->rbac->hasPrivilege('fees_master', 'can_delete')) {
        //     access_denied();
        // }
        $data['title'] = $this->lang->line('fees_master_list');
        $this->accountcategorygroup_model->remove($id);
        redirect('admin/accountcategorygroup/index');
    }

    public function deletegrp($id)
    {
        $data['title'] = $this->lang->line('fees_master_list');
        $this->accountcategorygroup_model->removegrp($id);
        redirect('admin/accountcategorygroup');
    }

    public function edit($id)
    {
        // if (!$this->rbac->hasPrivilege('fees_master', 'can_edit')) {
        //     access_denied();
        // }
        // $this->session->set_userdata('top_menu', 'Fees Collection');
        // $this->session->set_userdata('sub_menu', 'admin/feemaster');
        $data['id']            = $id;
        $feegroup_type         = $this->accountcategorygroup_model->get($id);
        $data['feegroup_type'] = $feegroup_type;
        $feegroup              = $this->accountcategory_model->get();
        $data['feegroupList']  = $feegroup;
        $feetype               = $this->accounttype_model->get();
        $data['feetypeList']   = $feetype;
        $feegroup_result       = $this->accountcategorygroup_model->getFeesByGroup();
        $data['feemasterList'] = $feegroup_result;
        
        $this->form_validation->set_rules('feetype_id', $this->lang->line('fee_type'), 'required');
        $this->form_validation->set_rules(
            'fee_groups_id', $this->lang->line('fee_group'), array(
                'required',
                array('check_exists', array($this->accountcategorygroup_model, 'valid_check_exists')),
            )
        );

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/accountcategorygroup/feemasterEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            
            $insert_array = array(
                'id'              => $this->input->post('id'),
                'accounttype_id'      => $this->input->post('feetype_id'),
            );

            $feegroup_result = $this->accountcategorygroup_model->add($insert_array);

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/accountcategorygroup/index');
        }
    }

    // public function assign($id)
    // {
    //     // if (!$this->rbac->hasPrivilege('fees_group_assign', 'can_view')) {
    //     //     access_denied();
    //     // }
    //     // $this->session->set_userdata('top_menu', 'Fees Collection');
    //     // $this->session->set_userdata('sub_menu', 'admin/feemaster');
    //     $data['id']              = $id;
    //     $data['title']           = $this->lang->line('student_fees');
    //     $class                   = $this->class_model->get();
    //     $data['classlist']       = $class;
    //     $feegroup_result         = $this->feesessiongroup_model->getFeesByGroup($id);
    //     $data['feegroupList']    = $feegroup_result;
    //     $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
    //     $data['sch_setting']     = $this->sch_setting_detail;
    //     $genderList            = $this->customlib->getGender();
    //     $data['genderList']    = $genderList;
    //     $RTEstatusList         = $this->customlib->getRteStatus();
    //     $data['RTEstatusList'] = $RTEstatusList;

    //     $category             = $this->category_model->get();
    //     $data['categorylist'] = $category;

    //     if ($this->input->server('REQUEST_METHOD') == 'POST') {

    //         $data['category_id'] = $this->input->post('category_id');
    //         $data['gender']      = $this->input->post('gender');
    //         $data['rte_status']  = $this->input->post('rte');
    //         $data['class_id']    = $this->input->post('class_id');
    //         $data['section_id']  = $this->input->post('section_id');

    //         $resultlist         = $this->studentfeemaster_model->searchAssignFeeByClassSection($data['class_id'], $data['section_id'], $id, $data['category_id'], $data['gender'], $data['rte_status']);
    //         $data['resultlist'] = $resultlist;
    //     }

    //     $this->load->view('layout/header', $data);
    //     $this->load->view('admin/accountcategorygroup/assign', $data);
    //     $this->load->view('layout/footer', $data);
    // }

}
