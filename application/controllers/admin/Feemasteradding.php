<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feemasteradding extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setting_model');
        $this->load->model('feegroupadding_model');
        $this->load->model('feetypeadding_model');
        $this->load->model('feesessiongroupadding_model');
        $this->load->model('feegrouptypeadding_model');
        $this->load->model('class_model');
        $this->load->model('category_model');
        $this->load->model('studentfeemasteradding_model');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {

        // $this->session->set_userdata('top_menu', 'Fees Collection');
        // $this->session->set_userdata('sub_menu', 'admin/feemaster');
		
        $data['title']        = $this->lang->line('fees_master_list');
        $feegroup             = $this->feegroupadding_model->get();
        $data['feegroupList'] = $feegroup;
        $feetype              = $this->feetypeadding_model->get();
        $data['feetypeList']  = $feetype;
        $feegroup_result       = $this->feesessiongroupadding_model->getFeesByGroup(null,0);
        $data['feemasterList'] = $feegroup_result;

        $this->form_validation->set_rules('feetype_id', $this->lang->line('fee_type'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|numeric');

        $this->form_validation->set_rules(
            'fee_groups_id', $this->lang->line('fee_group'), array(
                'required',
                array('check_exists', array($this->feesessiongroupadding_model, 'valid_check_exists')),
            )
        );

        if (isset($_POST['account_type']) && $_POST['account_type'] == 'fix') {
            $this->form_validation->set_rules('fine_amount', $this->lang->line('fix_amount'), 'required|numeric');
            $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'trim|required|xss_clean');

        } elseif (isset($_POST['account_type']) && ($_POST['account_type'] == 'percentage')) {
            $this->form_validation->set_rules('fine_percentage', $this->lang->line('percentage'), 'required|numeric');
            $this->form_validation->set_rules('fine_amount', $this->lang->line('fix_amount'), 'required|numeric');
            $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'trim|required|xss_clean');
        }

        if ($this->form_validation->run() == false) {

        } else {
            
            if($this->input->post('fine_amount')){
                $fine_amount    =   convertCurrencyFormatToBaseAmount($this->input->post('fine_amount'));
            }else{
                $fine_amount    = '';
            }
            
            $insert_array = array(
                'fee_groups_id'   => $this->input->post('fee_groups_id'),
                'feetype_id'      => $this->input->post('feetype_id'),
                'amount'          => convertCurrencyFormatToBaseAmount($this->input->post('amount')),
                'due_date'        => $this->customlib->dateFormatToYYYYMMDD($this->input->post('due_date')),
                'session_id'      => $this->setting_model->getCurrentSession(),
                'fine_type'       => $this->input->post('account_type'),
                'fine_percentage' => $this->input->post('fine_percentage'),
                'fine_amount'     => $fine_amount,
            );

            $feegroup_result = $this->feesessiongroupadding_model->add($insert_array);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/feemasteradding/index');
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/feemasteradding/feemasterList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        // if (!$this->rbac->hasPrivilege('fees_master', 'can_delete')) {
        //     access_denied();
        // }
        $data['title'] = $this->lang->line('fees_master_list');
        $this->feegrouptypeadding_model->remove($id);
        redirect('admin/feemasteradding/index');
    }

    public function deletegrp($id)
    {
        $data['title'] = $this->lang->line('fees_master_list');
        $this->feesessiongroupadding_model->remove($id);
        redirect('admin/feemasteradding');
    }

    public function edit($id)
    {
        // if (!$this->rbac->hasPrivilege('fees_master', 'can_edit')) {
        //     access_denied();
        // }
        $this->session->set_userdata('top_menu', 'Fees Collection');
        $this->session->set_userdata('sub_menu', 'admin/feemaster');
        $data['id']            = $id;
        $feegroup_type         = $this->feegrouptypeadding_model->get($id);
        $data['feegroup_type'] = $feegroup_type;
        $feegroup              = $this->feegroupadding_model->get();
        $data['feegroupList']  = $feegroup;
        $feetype               = $this->feetypeadding_model->get();
        $data['feetypeList']   = $feetype;
        $feegroup_result       = $this->feesessiongroupadding_model->getFeesByGroup(null,0);
        $data['feemasterList'] = $feegroup_result;
        $this->form_validation->set_rules('feetype_id', $this->lang->line('fee_type'), 'required');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'required|numeric');
        $this->form_validation->set_rules(
            'fee_groups_id', $this->lang->line('fee_group'), array(
                'required',
                array('check_exists', array($this->feesessiongroupadding_model, 'valid_check_exists')),
            )
        );

        if (isset($_POST['account_type']) && $_POST['account_type'] == 'fix') {
            $this->form_validation->set_rules('fine_amount', $this->lang->line('fix_amount'), 'required|numeric');
            $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'trim|required|xss_clean');
        } elseif (isset($_POST['account_type']) && ($_POST['account_type'] == 'percentage')) {
            $this->form_validation->set_rules('fine_percentage', $this->lang->line('percentage'), 'required|numeric');
            $this->form_validation->set_rules('fine_amount', $this->lang->line('fix_amount'), 'required|numeric');
            $this->form_validation->set_rules('due_date', $this->lang->line('due_date'), 'trim|required|xss_clean');
        }
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/feemasteradding/feemasterEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            
            if($this->input->post('fine_amount')){
                $fine_amount    =   convertCurrencyFormatToBaseAmount($this->input->post('fine_amount'));
            }else{
                $fine_amount    = '';
            }
            
            $insert_array = array(
                'id'              => $this->input->post('id'),
                'feetype_id'      => $this->input->post('feetype_id'),
                'due_date'        => $this->customlib->dateFormatToYYYYMMDD($this->input->post('due_date')),
                'amount'          => convertCurrencyFormatToBaseAmount($this->input->post('amount')),
                'fine_type'       => $this->input->post('account_type'),
                'fine_percentage' => $this->input->post('fine_percentage'),
                'fine_amount'     => $fine_amount,
            );

            $feegroup_result = $this->feegrouptypeadding_model->add($insert_array);

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/feemasteradding/index');
        }
    }

    public function assign($id)
    {
        // if (!$this->rbac->hasPrivilege('fees_group_assign', 'can_view')) {
        //     access_denied();
        // }
        // $this->session->set_userdata('top_menu', 'Fees Collection');
        // $this->session->set_userdata('sub_menu', 'admin/feemaster');
        $data['id']              = $id;
        $data['title']           = $this->lang->line('student_fees');
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $feegroup_result         = $this->feesessiongroupadding_model->getFeesByGroup($id);
        $data['feegroupList']    = $feegroup_result;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;
        $genderList            = $this->customlib->getGender();
        $data['genderList']    = $genderList;
        $RTEstatusList         = $this->customlib->getRteStatus();
        $data['RTEstatusList'] = $RTEstatusList;

        $category             = $this->category_model->get();
        $data['categorylist'] = $category;

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $data['category_id'] = $this->input->post('category_id');
            $data['gender']      = $this->input->post('gender');
            $data['rte_status']  = $this->input->post('rte');
            $data['class_id']    = $this->input->post('class_id');
            $data['section_id']  = $this->input->post('section_id');

            $resultlist         = $this->studentfeemasteradding_model->searchAssignFeeByClassSection($data['class_id'], $data['section_id'], $id, $data['category_id'], $data['gender'], $data['rte_status']);
            $data['resultlist'] = $resultlist;
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/feemasteradding/assign', $data);
        $this->load->view('layout/footer', $data);
    }


    public function addfeegroup()
    {
        $this->form_validation->set_rules('fee_session_groups', $this->lang->line('fee_group'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('amount[]', $this->lang->line('amount'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'fee_session_groups' => form_error('fee_session_groups'),
                'amount[]' => form_error('fee_session_groups'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $student_session_id     = $this->input->post('student_session_id');
            $feegrouptype_id        = $this->input->post('fee_group_type_id');
            $amount = $this->input->post('amount');
            $fee_session_groups     = $this->input->post('fee_session_groups');
            $student_sesssion_array = isset($student_session_id) ? $student_session_id : array();
            $student_ids            = $this->input->post('student_ids');
            $delete_student         = array_diff($student_ids, $student_sesssion_array);

            $preserve_record = array();
            if (!empty($student_sesssion_array)) {
                foreach ($student_sesssion_array as $key => $value) {
                    $insert_array = array(
                        'student_session_id'   => $value,
                        'fee_session_group_id' => $fee_session_groups,
                    );
                    $inserted_id = $this->studentfeemasteradding_model->add($insert_array);

                    for ($i = 0; $i < count($feegrouptype_id); $i++){
                        $insert_array1 = array(
                            'student_session_id'=> $value,
                            'fee_groups_feetype_id'=> $feegrouptype_id[$i],
                            'amount' => $amount[$i],
                        );
                        $inserted_id1 = $this->studentfeemasteradding_model->addd($insert_array1);

                    }
                    // foreach ($feegrouptype_id as $key2 => $value2) {
                    //     $insert_array1 = array(
                    //         'student_session_id'=> $value,
                    //         'fee_groups_feetype_id'=> $value2,
                    //     );
                    //     $inserted_id1 = $this->studentfeemasteradding_model->addd($insert_array1);

                    // }

                    $preserve_record[] = $inserted_id;
                }
            }
            if (!empty($delete_student)) {
                $this->studentfeemasteradding_model->delete($fee_session_groups, $delete_student);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }

    }

}
