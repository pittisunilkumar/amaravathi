<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Accounttranscation extends Admin_Controller
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

        if ($this->form_validation->run() == false) {

        } else {
            $data = array(
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'account_category'        => $this->input->post('accountcategory_id'),
                'account_type'        => $this->input->post('section_id'),
                'account_role'        => $this->input->post('gender'),
                'description' => $this->input->post('description'),
            );

            $status=$this->addaccount_model->add($data);
            if($status==false){
                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">' . $this->lang->line('danger_message') . '</div>');
            }else{
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            }
            redirect('admin/accounttranscation/index');
        }
        $feegroup_result     = $this->addaccount_model->getaddeddebitedaccounts();
        $data['feetypeList'] = $feegroup_result;
        
        $feegroup             = $this->addaccount_model->getaddedcreditedaccounts();
        $data['feegroupList'] = $feegroup;

        $genderList                    = $this->customlib->getaccountrole();
        $data['genderList']            = $genderList;


        $this->load->view('layout/header', $data);
        $this->load->view('admin/accounttranscation/feetypeList', $data);
        $this->load->view('layout/footer', $data);

    }

    public function delete($id)
    {
        // if (!$this->rbac->hasPrivilege('fees_type', 'can_delete')) {
        //     access_denied();
        // }

        $this->addaccount_model->remove($id);
        redirect('admin/accounttranscation/index');
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

        
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/accounttranscation/feetypeEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'          => $id,
                'name'        => $this->input->post('name'),
                'code'        => $this->input->post('code'),
                'account_category'        => $this->input->post('accountcategory_id'),
                'account_type'        => $this->input->post('section_id'),
                'account_role'        => $this->input->post('gender'),
                'description' => $this->input->post('description'),
            );
            $this->addaccount_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/accounttranscation/index');
        }
    }



    public function getaccounttype()
    {
        $class_id = $this->input->get('accountcategory_id');
        $data     = $this->addaccount_model->getaccounttypee($class_id);
        echo json_encode($data);
    }



    public function transaction()
    {
        // if (!$this->rbac->hasPrivilege('fees_type', 'can_view')) {
        //     access_denied();
        // }
        // $this->session->set_userdata('top_menu', 'Fees Collection');
        // $this->session->set_userdata('sub_menu', 'feetype/index');
        
        
        $this->form_validation->set_rules('debitaccount', $this->lang->line('debitaccount'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('debitedamount', $this->lang->line('debitedamount'), 'required');
        $this->form_validation->set_rules('creditaccount', $this->lang->line('creditaccount'), 'required');

        // $this->form_validation->set_rules('description', $this->lang->line('description'), 'required');

        if ($this->form_validation->run() == false) {

        } else {
            $data = array(
                'fromaccountid'        => $this->input->post('debitaccount'),
                'toaccountid'        => $this->input->post('creditaccount'),
                'amount'        => $this->input->post('debitedamount'),
                'date'        => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'note'        => $this->input->post('description'),
            );

            $status=$this->addaccount_model->addtranscation($data);
            $accountname = $this->addaccount_model->getaddedaccount($this->input->post('creditaccount'));
            
            $deposit = array(
                'receiptid'        => $status,
                'accountid'        => $this->input->post('debitaccount'),
                'amount'        => $this->input->post('debitedamount'),
                'date'        => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'type'        => $accountname['name'],
                'description'        => $this->input->post('description'),
                'status'   => 'debit'
            );
            $this->addaccount_model->addingtranscation($deposit);
            $accountname = $this->addaccount_model->getaddedaccount($this->input->post('debitaccount'));

            $credit = array(
                'receiptid'        => $status,
                'accountid'        => $this->input->post('creditaccount'),
                'amount'        => $this->input->post('debitedamount'),
                'date'        => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'type'        => $accountname['name'],
                'description'        => $this->input->post('description'),
                'status'   => 'credit'
            );
            $this->addaccount_model->addingtranscation($credit);


            if($status==false){
                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">' . $this->lang->line('danger_message') . '</div>');
            }else{
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            }
            redirect('admin/accounttranscation/index');
        }

        $feegroup_result     = $this->addaccount_model->getaddeddebitedaccounts();
        $data['feetypeList'] = $feegroup_result;
        
        $feegroup             = $this->addaccount_model->getaddedcreditedaccounts();
        $data['feegroupList'] = $feegroup;

        $genderList                    = $this->customlib->getaccountrole();
        $data['genderList']            = $genderList;


        $this->load->view('layout/header', $data);
        $this->load->view('admin/accounttranscation/feetypeList', $data);
        $this->load->view('layout/footer', $data);

    }




}




