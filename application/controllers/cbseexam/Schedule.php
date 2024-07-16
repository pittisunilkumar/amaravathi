<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Schedule extends MY_Addon_CBSEController
{
 
    public function __construct()
    {
        parent::__construct();
   
    } 
 
    public function index()
    {
        if (!$this->rbac->hasPrivilege('student_categories', 'can_view')) {
            access_denied();
        }  
        
        $this->session->set_userdata('top_menu', 'Student Information');
        $this->session->set_userdata('sub_menu', 'category/index');
        $data['result']=array();
        $data['exam_result']=$this->cbseexam_exam_model->getexams();
        $data['assessment_result']=$this->cbseexam_schedule_model->get();
        $data['grade_result']=$this->cbseexam_grade_model->getgradelist();
        $class             = $this->class_model->get();
        $data['classlist'] = $class;  
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/schedule/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        $assessment_id=$this->input->post('assessment_id');
          
        if (empty($assessment_id)) {
            $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean|is_unique[cbse_exam_assessments.name]');
        }else{
            $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        }      
   
        $validate = 1;
        $duplicate = 0;
        if (!empty($_POST['type_name'])) {
            foreach ($_POST['type_name'] as $type_namekey => $type_namevalue) {
                if ($type_namevalue == '') {
                    $validate = 0;
                    $msg['type_name'] ="<p>".$this->lang->line('type_name_is_required')."</p>";       
                }

                if (isset($new[$type_namevalue])){
                    $duplicate++;
                    $new[$type_namevalue]++;
                }else{
                    $duplicate = 0;
                    $new[$type_namevalue]=0;
                }
            }
        } else {
            $validate = 0;
            $msg['type_name'] ="<p>".$this->lang->line('type_name_is_required')."</p>"; 
        }       

        if (!empty($_POST['maximum_marks'])) {
            foreach ($_POST['maximum_marks'] as $maximum_markskey => $maximum_marksvalue) {
                if ($maximum_marksvalue == '') {
                    $validate = 0;
                    $msg['maximum_marks'] ="<p>".$this->lang->line('maximum_marks_is_required')."</p>"; 
                }
            }
        } else {
            $validate = 0;
            $msg['maximum_marks'] ="<p>".$this->lang->line('maximum_marks_is_required')."</p>"; 
        }

        if (!empty($_POST['pass_percentage'])) {
            foreach ($_POST['pass_percentage'] as $pass_percentagekey => $pass_percentagevalue) {
                if ($pass_percentagevalue == '') {
                    $validate = 0;
                    $msg['pass_percentage'] ="<p>".$this->lang->line('pass_percentage_is_required')."</p>";
                }
            }
        } else {
            $validate = 0;
            $msg['pass_percentage'] ="<p>".$this->lang->line('pass_percentage_is_required')."</p>";
        }

        if($duplicate>0){
            $validate = 0;
            $msg['duplicate_type_name'] ="<p>".$this->lang->line('duplicate_name_found')."</p>";
        }        
        if ($this->form_validation->run() == FALSE) {
            $msg['name'] = form_error('name');
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } elseif ($validate == 0) {             
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
             if (!empty($_POST['delete_ides'])) {
            foreach ($_POST['delete_ides'] as $delete_ideskey => $delete_idesvalue) {
                $this->cbseexam_schedule_model->remove_assessment_type($delete_idesvalue);
            }}
             if (!empty($_POST['type_name'])) {
               
                $sn=1;
                $insert=array('name'=>$this->input->post('name'),'description'=>$this->input->post('description'));
                if(!empty($assessment_id)){
                    $insert['id']=$assessment_id;
                }
                $insert_id=$this->cbseexam_schedule_model->add($insert);
            foreach ($_POST['type_name'] as $type_namekey => $type_namevalue) {

               $data=array(
                'id'=>$this->input->post('assessment_type_id')[$type_namekey],
                'cbse_exam_assessment_id'=>$insert_id,
                'name'=>$this->input->post('type_name')[$type_namekey],
                'code'=>$this->input->post('code')[$type_namekey],
                'maximum_marks'=>$this->input->post('maximum_marks')[$type_namekey],
                'pass_percentage'=>$this->input->post('pass_percentage')[$type_namekey],
                'description'=>$this->input->post('type_description')[$type_namekey],
                'created_by'=>$this->customlib->getStaffID());
              
               $this->cbseexam_schedule_model->add_type($data);
              
            }
        }            
         
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }    

    public function get_editdetails(){
        $id=$this->input->post('id');
        $result=$this->cbseexam_schedule_model->get_editdetails($id);
        echo json_encode($result);

    }

    public function remove($id){
        $this->cbseexam_schedule_model->remove($id);
        redirect('cbseexam/schedule');
    }

    public function add_type(){
        $data=array();
        $id=$this->input->post('id');
        $result=$this->cbseexam_schedule_model->get_assessmentTypebyId($id);
        $data['result']=$result;
        $data['delete_string']=$this->input->post('delete_string');
        echo json_encode($this->load->view("cbseexam/schedule/_add_type", $data, true));
    }

    

}
