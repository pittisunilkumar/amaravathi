<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Addresult extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Customlib');
        $this->load->library('media_storage');
        $this->load->model('resultsubjectgroup_model');
        $this->load->model('addresult_model');
        $this->load->model('publicresultsubjectgroup_model');
        $this->load->model('student_model');

        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->current_session = $this->setting_model->getCurrentSession();
    }


    public function index()
    {
        if (!$this->rbac->hasPrivilege('adding_internal_results', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generateidcard');

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;

        $data['resulttypelist'] = $this->resultsubjectgroup_model->resulttype();

        // $idcardlist              = $this->Generateidcard_model->getstudentidcard();
        // $data['idcardlist']      = $idcardlist;

        $progresslist            = $this->customlib->resultaddingstatus();
        $data['progresslist']    = $progresslist;

        $button                  = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/addresult', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class   = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $search  = $this->input->post('search');

            $admistatus   = $this->input->post('progress_id');
            if($admistatus=="withadmissionno"){
                $vall=1;
            }
            if($admistatus=="noadmissionno"){
                $vall=0;
            }
            // $id_card = $this->input->post('id_card');
            if (isset($search)) {
                $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('progress_id', $this->lang->line('admi_status'), 'trim|required|xss_clean');

                //$this->form_validation->set_rules('id_card', $this->lang->line('id_card_template'), 'trim|required|xss_clean');
                if ($this->form_validation->run() == false) {

                } else {
                    $data['searchby']     = "filter";
                    $data['class_id']     = $this->input->post('class_id');
                    $data['section_id']   = $this->input->post('section_id');
                    // $id_card              = $this->input->post('id_card');
                    // $idcardResult         = $this->Generateidcard_model->getidcardbyid($id_card);
                    // $data['idcardResult'] = $idcardResult;
                    $resultlist           = $this->addresult_model->admissionnostatusgetDatatableByClassSection($class, $section,$vall);
                    $data['resultlist']   = $resultlist;
                     
                }
            }

            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/addresult', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    public function search()
    {
        if (!$this->rbac->hasPrivilege('adding_internal_results', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Certificate');
        $this->session->set_userdata('sub_menu', 'admin/generateidcard');

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $data['sch_setting']     = $this->sch_setting_detail;

        $data['resulttypelist'] = $this->resultsubjectgroup_model->resulttype();

        $progresslist            = $this->customlib->checkadminstatus();
        $data['progresslist']    = $progresslist;

        $button                  = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/addresult', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class   = $this->input->post('class_id');
            $section = $this->input->post('section_id');
            $search  = $this->input->post('search');
            $admistatus   = $this->input->post('progress_id');

            $result_id   = $this->input->post('result_id');
            
            
            // $id_card = $this->input->post('id_card');
            if (isset($search)) {
                $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('progress_id', $this->lang->line('admi_status'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('result_id', $this->lang->line('internal_result_type'), 'trim|required|xss_clean');

                //$this->form_validation->set_rules('id_card', $this->lang->line('id_card_template'), 'trim|required|xss_clean');
                if ($this->form_validation->run() == false) {

                } else {
                    $data['searchby']     = "filter";
                    $data['class_id']     = $this->input->post('class_id');
                    $data['section_id']   = $this->input->post('section_id');
                    $data['result_id']    = $this->input->post('result_id');
                    // $id_card              = $this->input->post('id_card');
                    // $idcardResult         = $this->Generateidcard_model->getidcardbyid($id_card);
                    // $data['idcardResult'] = $idcardResult;
                    $data['subjects'] = $this->addresult_model->subjectsgroup($this->input->post('result_id'));

                    if($admistatus=="withadmissionno"){
                        $vall=1;
                    }
                    if($admistatus=="noadmissionno"){
                        $vall=0;
                    }
                    $data['status']=$vall;
                    $resultlist           = $this->addresult_model->admissionnostatusgetDatatableByClassSection($class, $section,$vall,$result_id);
                    $data['resultlist']   = $resultlist;
                     
                }
            }

            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/addresult', $data);
            $this->load->view('layout/footer', $data);
        }
    }









    public function addmore_point()
    {

        $data                     = array();
        $id                       = $this->input->post('id');
        $pickup_pointdata=['id'=>"",
                            'destination_distance'=>"",
                            'pickup_point_id'=>"",
                            'pickup_time'=>"",
                            'fees'=>0
                          ];


        if(isset($id)){
             $pickup_pointdata         = $this->addresult_model->getpickup_pointbyid($id);
        }


        $listpickup_point         = $this->addresult_model->dropdownpickup_point();
        $data['listpickup_point'] = $listpickup_point;

        $data['subjectidd']       = $this->input->post('subjectidd');
        
        $data['resid']            = $this->input->post('resulttype_id');

        $data['result']           = $pickup_pointdata;
        $data['delete_string']    = $this->input->post('delete_string');
        $data['subjectsData']     = $this->input->post('subjectsData');

        echo json_encode($this->load->view("admin/results/_addresultsubject", $data, true));

    }


    public function create()
    {
        if (!$this->rbac->hasPrivilege('adding_internal_results', 'can_view')) {
            access_denied();
        }

        $stid= $this->input->post('student_id');
        $resulttype_id= $this->input->post('resulttype_id');
        $validate  = 1;
        $pickup_array=array();

        // $msg['student_id']="<p>" . $this->input->post('student_id') . "</p>";
        
        if (!empty($_POST['pickup_point_id'])) {

            foreach ($_POST['pickup_point_id'] as $pickup_pointkey => $pickup_pointvalue) {
                if ($pickup_pointvalue == '') {
                    $validate            = 0;
                    $msg['pickup_point_id'] = "<p>" . $this->lang->line('the_pickup_point_field_is_required') . "</p>";
                          break;
                }


            }
        } else {
            $validate            = 0;
            $msg['pickup_point'] = "<p>" . $this->lang->line('the_pickup_point_field_is_required') . "</p>";
        }

        

        if (!empty($_POST['actualmarks'])) {
            foreach ($_POST['actualmarks'] as $maks => $maksval) {
                if ($maksval == '') {
                    $validate            = 0;
                    $msg['actualmarks'] = "<p>" . $this->lang->line('_marks_required') . "</p>";
                    break;
                }else{
                   
                  $expr = '/^[0-9]*(\.[0-9]{0,2})?$/';
                if (!preg_match($expr, $maksval)) {
                   $validate            = 0;
                   $msg['actualmarks'] = "<p>" . $this->lang->line('_marks_valid') . "</p>";
                   break;
                }


                }
            }
        } else {
            $validate            = 0;
            $msg['actualmarks'] = "<p>" . $this->lang->line('_marks_required') . "</p>";
        }



        
        // if ($this->form_validation->run() == false) {
        //     $array           = array('status' => 'fail', 'error' => $msg, 'message' => '');
        // } else
        
        if ($validate == 0) {

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {




            if (!empty($_POST['pickup_point_id'])) {
                $sn = 1;
                foreach ($_POST['pickup_point_id'] as $pickup_pointkey => $pickup_pointvalue) {
                    // $time = $this->input->post('time')[$pickup_pointkey];

                    $marksdata = $this->addresult_model->getmarks($resulttype_id,$this->input->post('pickup_point_id')[$pickup_pointkey]);

                    $data = array(
                        
                        'stid'   => $stid,
                        'resulgroup_id'      => $resulttype_id,
                        'subjectid' => $this->input->post('pickup_point_id')[$pickup_pointkey],
                        'markstableid' =>$marksdata['id'],
                        'actualmarks' => $this->input->post('actualmarks')[$pickup_pointkey],
                        'session_id' => $this->current_session,
                        
                    );
                    
                    $sn++;
                    $insert_id = $this->addresult_model->add($data);

                }
            }


            $data1=array(
                'stid' => $stid,
                'resultype_id' => $resulttype_id,
                'session_id' => $this->current_session,
                'assign_status' => 0,
            );

            $in = $this->addresult_model->addresult($data1);

            //$array = array('status' => 'success', 'error' => '', 'message' => $resulttype_id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function get_assigndetails()
    {
        $id             = $this->input->post('id');
        $data['result'] = $this->addresult_model->getPickupPointByRouteID($id);
        echo json_encode($data['result']);
    }


    public function get_pickupdropdownlist()
    {
        $vehroute_id           = $this->input->post('vehroute_id');
        $vehicle_route_pickups = $this->addresult_model->getPickupPointsByvehrouteId($vehroute_id);
        echo json_encode($vehicle_route_pickups);
    }

    public function resulteditdata()
    {

        $data                     = array();
        
        $resid                   = $this->input->post('resulttypeid');
        $data['resid']           = $resid;
        $subjectsData            = $this->input->post('subjectsData');

        $stid                    = $this->input->post('stid');

        $academicid=$this->current_session;

        $marks   = $this->addresult_model->getmarkforsubject($stid,$resid,$subjectsData['subject_id'],$academicid);

        $data['marks']=$marks;
        
        $data['subjectsData']     = $subjectsData;

        echo json_encode($this->load->view("admin/results/_editresultsubject", $data, true));

    }

    
    public function viewpionts()
    {

        $data                     = array();
        $stid       = $this->input->post('subjectidd');
        $resulttype            = $this->input->post('resulttype_id');
        // $data['subjectsData']     = $this->input->post('subjectsData');

        $academicid=$this->current_session;
        $data['academicid']=$academicid;

        $data['admissionno']=$this->student_model->getAdmissionNumber($stid);
        
        $data['resultname']=$this->publicresultsubjectgroup_model->getresultname($resulttype);
        $data['studentdata']=$this->publicresultsubjectgroup_model->gtstudentdata($stid);

        $resultdata = $this->publicresultsubjectgroup_model->getstudentresultsview($stid,$resulttype,$academicid);
        $data['resultdata']=$resultdata;

        echo json_encode($this->load->view("admin/results/_showresultsubject", $data, true));

    }



    public function update()
    {
        if (!$this->rbac->hasPrivilege('adding_internal_results', 'can_view')) {
            access_denied();
        }

        $stid= $this->input->post('student_iddd');
        $resulttype_id= $this->input->post('resulttype_iddd');
        $validate  = 1;
        $pickup_array=array();

        // $msg['student_id']="<p>" . $this->input->post('student_id') . "</p>";
        
        if (!empty($_POST['pickup_point_id'])) {

            foreach ($_POST['pickup_point_id'] as $pickup_pointkey => $pickup_pointvalue) {
                if ($pickup_pointvalue == '') {
                    $validate            = 0;
                    $msg['pickup_point_id'] = "<p>" . $this->lang->line('the_pickup_point_field_is_required') . "</p>";
                          break;
                }


            }
        } else {
            $validate            = 0;
            $msg['pickup_point'] = "<p>" . $this->lang->line('the_pickup_point_field_is_required') . "</p>";
        }

        

        if (!empty($_POST['actualmarks'])) {
            foreach ($_POST['actualmarks'] as $maks => $maksval) {
                if ($maksval == '') {
                    $validate            = 0;
                    $msg['actualmarks'] = "<p>" . $this->lang->line('_marks_required') . "</p>";
                    break;
                }else{
                   
                  $expr = '/^[0-9]*(\.[0-9]{0,2})?$/';
                if (!preg_match($expr, $maksval)) {
                   $validate            = 0;
                   $msg['actualmarks'] = "<p>" . $this->lang->line('_marks_valid') . "</p>";
                   break;
                }


                }
            }
        } else {
            $validate            = 0;
            $msg['actualmarks'] = "<p>" . $this->lang->line('_marks_required') . "</p>";
        }

        // if ($this->form_validation->run() == false) {
        //     $array           = array('status' => 'fail', 'error' => $msg, 'message' => '');
        // } else
        
        if ($validate == 0) {

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {


            if (!empty($_POST['pickup_point_id'])) {
                $sn = 1;
                foreach ($_POST['pickup_point_id'] as $pickup_pointkey => $pickup_pointvalue) {
                    // $time = $this->input->post('time')[$pickup_pointkey];

                    $marksdata = $this->addresult_model->getmarks($resulttype_id,$this->input->post('pickup_point_id')[$pickup_pointkey]);

                    $data = array(
                        
                        'stid'   => $stid,
                        'resulgroup_id'      => $resulttype_id,
                        'subjectid' => $this->input->post('pickup_point_id')[$pickup_pointkey],
                        'markstableid' =>$marksdata['id'],
                        'actualmarks' => $this->input->post('actualmarks')[$pickup_pointkey],
                        'session_id' => $this->current_session,
                        
                    );
                    
                    $sn++;
                    $insert_id = $this->addresult_model->resultupdate($stid,$resulttype_id,$this->input->post('pickup_point_id')[$pickup_pointkey],$data);

                }
            }


            //$array = array('status' => 'success', 'error' => '', 'message' => $resulttype_id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
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
        
        $subjectlist= $this->addresult_model->subjectsgroupp($id,$this->current_session);
        $data['subjectlist']=$subjectlist;

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
            $data['class_id']    = $this->input->post('class_id');
            $data['section_id']  = $this->input->post('section_id');

            $resultlist         = $this->addresult_model->searchstudentresultassign($data['class_id'], $data['section_id'], $data['category_id'], $data['gender'],$id,$this->current_session);
            $data['resultlist'] = $resultlist;

        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/results/resultassign', $data);
        $this->load->view('layout/footer', $data);
    }


    public function addresultgroup()
    {
        $this->form_validation->set_rules('fee_session_groups', $this->lang->line('fee_group'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'fee_session_groups' => form_error('fee_session_groups'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $student_session_id     = $this->input->post('student_session_id');
            $fee_session_groups     = $this->input->post('fee_session_groups');
            $student_sesssion_array = isset($student_session_id) ? $student_session_id : array();
            $student_ids            = $this->input->post('student_ids');
            $delete_student         = array_diff($student_ids, $student_sesssion_array);

            if (!empty($student_sesssion_array)) {
                foreach ($student_sesssion_array as $key => $value) {
                    $update_array=array(
                        'assign_status'=>1,
                    );
                    $this->addresult_model->updatingresult($value,$fee_session_groups,$update_array,$this->current_session);
                }
            }
            if (!empty($delete_student)) {
                foreach($delete_student as $key => $value){
                    $update_array=array(
                        'assign_status'=>0,
                    );
                    $this->addresult_model->updatingresult($value,$fee_session_groups,$update_array,$this->current_session);
                }
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

}





