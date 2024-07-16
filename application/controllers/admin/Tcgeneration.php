<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Tcgeneration extends Admin_Controller
{

    public function __construct()
    {
       
        parent::__construct();
        // $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->library('customlib');
        $this->load->library('media_storage');
        $this->load->model('tcgeneration_model');
        
    }

    public function index()
    {
        // if (!$this->rbac->hasPrivilege('generate_certificate', 'can_view')) {
        //     access_denied();
        // }
        // $this->session->set_userdata('top_menu', 'Certificate');
        // $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

        $certificateList         = $this->tcgeneration_model->getstudentcertificate();
        $data['certificateList'] = $certificateList;
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/tcgeneration/generatecertificate', $data);
        $this->load->view('layout/footer', $data);
    }

    public function search()
    {
        // $this->session->set_userdata('top_menu', 'Certificate');
        // $this->session->set_userdata('sub_menu', 'admin/generatecertificate');

        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $certificateList         = $this->tcgeneration_model->getstudentcertificate();
        $data['certificateList'] = $certificateList;
        $button                  = $this->input->post('search');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/tcgeneration/generatecertificate', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $class       = $this->input->post('class_id');
            $section     = $this->input->post('section_id');
            $search      = $this->input->post('search');
            $certificate = $this->input->post('certificate_id');
            if (isset($search)) {
                $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
                $this->form_validation->set_rules('certificate_id', $this->lang->line('certificate'), 'trim|required|xss_clean');
                if ($this->form_validation->run() == false) {

                } else {
                    $data['searchby']          = "filter";
                    $data['class_id']          = $this->input->post('class_id');
                    $data['section_id']        = $this->input->post('section_id');
                    $certificate               = $this->input->post('certificate_id');
                    $certificateResult         = $this->tcgeneration_model->getcertificatebyid($certificate);
                    $data['certificateResult'] = $certificateResult;
                    $resultlist                = $this->student_model->searchByClassSection($class, $section);
                    $data['resultlist']        = $resultlist;
                    $title                     = $this->classsection_model->getDetailbyClassSection($data['class_id'], $data['section_id']);
                    // $data['title']             = $this->lang->line('std_dtl_for') . ' ' . $title['class'] . "(" . $title['section'] . ")";
                }
            }
            $data['sch_setting'] = $this->sch_setting_detail;
            $this->load->view('layout/header', $data);
            $this->load->view('admin/tcgeneration/generatecertificate', $data);
            $this->load->view('layout/footer', $data);
        }
    }



    public function createtc()
    {

        // if (!$this->rbac->hasPrivilege('student_id_card', 'can_view')) {
        //     access_denied();
        // }

        // $this->session->set_userdata('top_menu', 'Certificate');
        // $this->session->set_userdata('sub_menu', 'admin/studentidcard');
        
        $this->data['idcardlist'] = $this->tcgeneration_model->idcardlist();
        $this->data['classlist'] = $this->tcgeneration_model->getsubjects();

        
        $this->load->view('layout/header');
        $this->load->view('admin/tcgeneration/createidcard', $this->data);
        $this->load->view('layout/footer');
    }


    public function handle_upload($var)
    {
        $image_validate = $this->config->item('image_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->image_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->image_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = @getimagesize($_FILES[$var]['tmp_name'])) {

                if (!in_array($files['mime'], $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed'));
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                    return false;
                }

                if ($file_size > $result->image_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($result->image_size / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed_or_extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }



    public function create()
    {

        // if (!$this->rbac->hasPrivilege('student_id_card', 'can_add')) {
        //     access_denied();
        // }

        // $data['title'] = 'Student ID Card';

        $this->form_validation->set_rules('tc_name', $this->lang->line('tc_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_head_tittle', $this->lang->line('tc_head_tittle'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('school_name', $this->lang->line('school_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_description', $this->lang->line('tc_description'), 'trim|required|xss_clean');
        
        $this->form_validation->set_rules('tc_body', $this->lang->line('tc_body'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_address', $this->lang->line('tc_address'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_footer', $this->lang->line('tc_footer'), 'trim|required|xss_clean');

        $this->form_validation->set_rules('tc_conduct', $this->lang->line('tc_conduct'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_mother_tongue', $this->lang->line('mother_tongue'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('firstlang_id', $this->lang->line('student_tc_firstlangg'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('secondlang_id', $this->lang->line('student_tc_secondlangg'), 'trim|required|xss_clean');

        $this->form_validation->set_rules('logo_img', $this->lang->line('logo'), 'callback_handle_upload');
        

        $this->form_validation->set_rules('tc_date_left', $this->lang->line('student_tc_actually'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_nationality', $this->lang->line('student_tc_nationality'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_second_year_course', $this->lang->line('student_tc_qualified'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_eligible_university_course', $this->lang->line('student_tc_declared'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_receipt_scholarship', $this->lang->line('student_tc_scholarship'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_receipt_concession', $this->lang->line('student_tc_specified'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_punishment_during_period', $this->lang->line('student_tc_candidate'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_optional_lang', $this->lang->line('tc_optional_lang'), 'trim|required|xss_clean');


        if ($this->form_validation->run() == false) {
            $this->data['idcardlist'] = $this->tcgeneration_model->idcardlist();
            $this->data['classlist'] = $this->tcgeneration_model->getsubjects();
            

            $this->load->view('layout/header');
            $this->load->view('admin/tcgeneration/createidcard', $this->data);
            $this->load->view('layout/footer');
        } else {
            
            $studentname     = 0;
            $admissiondate   = 0;
            $parentsname     = 0;
            $dob             = 0;
            $mothertongue    = 0;
            $datetc          = 0;
            $caste           = 0;

            
            if ($this->input->post('is_active_student_name') == 1) {
                $studentname = $this->input->post('is_active_student_name');
            }
            if ($this->input->post('is_active_admission_date') == 1) {
                $admissiondate = $this->input->post('is_active_admission_date');
            }
            if ($this->input->post('is_active_parents_name') == 1) {
                $parentsname = $this->input->post('is_active_parents_name');
            }
            if ($this->input->post('is_active_dob') == 1) {
                $dob = $this->input->post('is_active_dob');
            }
            
            if ($this->input->post('is_active_mother_tongue') == 1) {
                $mothertongue = $this->input->post('is_active_mother_tongue');
            }
            if ($this->input->post('is_active_date_tc') == 1) {
                $datetc = $this->input->post('is_active_date_tc');
            }
            if ($this->input->post('is_active_caste') == 1) {
                $caste = $this->input->post('is_active_caste');
            }
            // $first_sub = $this->tcgeneration_model->getsubjectname($this->input->post('firstlang_id'));
            // $second_sub = $this->tcgeneration_model->getsubjectname($this->input->post('secondlang_id'));
            
            $data = array(
                'tc_name'                => $this->input->post('tc_name'),
                'school_name'            => $this->input->post('school_name'),
                'tc_head_tittle'         => $this->input->post('tc_head_tittle'),
                'tc_description'         => $this->input->post('tc_description'),
                'tc_address'             => $this->input->post('tc_address'),
                'tc_body'                => $this->input->post('tc_body'),

                'tc_date_left'           => $this->input->post('tc_date_left'),
                'tc_nationality'         => $this->input->post('tc_nationality'),
                'tc_second_year_course'  => $this->input->post('tc_second_year_course'),
                'tc_eligible_university_course'  => $this->input->post('tc_eligible_university_course'),
                'tc_receipt_scholarship' => $this->input->post('tc_receipt_scholarship'),
                'tc_receipt_concession'  => $this->input->post('tc_receipt_concession'),
                'tc_punishment_during_period'  => $this->input->post('tc_punishment_during_period'),
                'tc_optional_lang'       => $this->input->post('tc_optional_lang'),


                'tc_first_lang'          => $this->input->post('firstlang_id'),
                'tc_second_lang'         => $this->input->post('secondlang_id'),


                'tc_footer'              => $this->input->post('tc_footer'),
                'tc_conduct'             => $this->input->post('tc_conduct'),
                'tc_mother_tongue'       => $this->input->post('tc_mother_tongue'),
                'enable_student_name'    => $studentname,
                'enable_admission_date'  => $admissiondate,
                'enable_parents_name'    => $parentsname,
                'enable_mother_tongue'   => $mothertongue,
                'enable_date_tc'         => $datetc,
                'enable_caste'           => $caste,
                'enable_dob'             => $dob,
                'status'                 => 1,
            );

            if (!empty($_FILES['logo_img']['name'])) {
                
                // $logo_img_name = $this->media_storage->applicationfileupload("logo_img", "./uploads/tcgeneration/logo/");

                $logo_img_name = $this->media_storage->fileupload("logo_img", "./uploads/tcgeneration/logo/");
            } else {
                $logo_img_name = '';
            }
            $data['logo'] = $logo_img_name;

            $insert_id = $this->tcgeneration_model->addtcgenerate($data);

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/tcgeneration/create');
        }
    }

    

    public function edit($id)
    {
        // if (!$this->rbac->hasPrivilege('student_id_card', 'can_edit')) {
        //     access_denied();
        // }

        $data['title']            = 'Edit ID Card';
        $data['id']               = $id;
        $editidcard               = $this->tcgeneration_model->get($id);
        $this->data['editidcard'] = $editidcard;


        // $this->form_validation->set_rules('school_name', $this->lang->line('school_name'), 'trim|required|xss_clean');
        // $this->form_validation->set_rules('address', $this->lang->line('address_phone_email'), 'trim|required|xss_clean');
        // $this->form_validation->set_rules('title', $this->lang->line('id_card_title'), 'trim|required|xss_clean');
        // $this->form_validation->set_rules('background_image', $this->lang->line('background_image'), 'callback_handle_upload[background_image]');
        // $this->form_validation->set_rules('logo_img', $this->lang->line('logo'), 'callback_handle_upload[logo_img]');
        // $this->form_validation->set_rules('sign_image', $this->lang->line('signature'), 'callback_handle_upload[sign_image]');
        


        $this->form_validation->set_rules('tc_name', $this->lang->line('tc_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_head_tittle', $this->lang->line('tc_head_tittle'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('school_name', $this->lang->line('school_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_description', $this->lang->line('tc_description'), 'trim|required|xss_clean');
        
        $this->form_validation->set_rules('tc_body', $this->lang->line('tc_body'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_address', $this->lang->line('tc_address'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_footer', $this->lang->line('tc_footer'), 'trim|required|xss_clean');

        $this->form_validation->set_rules('tc_conduct', $this->lang->line('tc_conduct'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_mother_tongue', $this->lang->line('mother_tongue'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('firstlang_id', $this->lang->line('student_tc_firstlangg'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('secondlang_id', $this->lang->line('student_tc_secondlangg'), 'trim|required|xss_clean');

        $this->form_validation->set_rules('logo_img', $this->lang->line('logo'), 'callback_handle_upload');
        

        $this->form_validation->set_rules('tc_date_left', $this->lang->line('student_tc_actually'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_nationality', $this->lang->line('student_tc_nationality'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_second_year_course', $this->lang->line('student_tc_qualified'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_eligible_university_course', $this->lang->line('student_tc_declared'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_receipt_scholarship', $this->lang->line('student_tc_scholarship'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_receipt_concession', $this->lang->line('student_tc_specified'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_punishment_during_period', $this->lang->line('student_tc_candidate'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('tc_optional_lang', $this->lang->line('tc_optional_lang'), 'trim|required|xss_clean');

        
        
        
        if ($this->form_validation->run() == false) {
            $this->data['idcardlist'] = $this->tcgeneration_model->idcardlist();
            $this->data['classlist'] = $this->tcgeneration_model->getsubjects();
            $this->load->view('layout/header');
            $this->load->view('admin/tcgeneration/studentidcardedit', $this->data);
            $this->load->view('layout/footer');
        } else {
            $studentname     = 0;
            $admissiondate   = 0;
            $parentsname     = 0;
            $dob             = 0;
            $mothertongue    = 0;
            $datetc          = 0;
            $caste           = 0;





            // $admission_no    = 0;
            // $studentname     = 0;
            // $class           = 0;
            // $fathername      = 0;
            // $mothername      = 0;
            // $address         = 0;
            // $phone           = 0;
            // $dob             = 0;
            // $bloodgroup      = 0;
            // $vertical_card   = 0;
            // $student_barcode = 0;






            if ($this->input->post('is_active_student_name') == 1) {
                $studentname = $this->input->post('is_active_student_name');
            }
            if ($this->input->post('is_active_admission_date') == 1) {
                $admissiondate = $this->input->post('is_active_admission_date');
            }
            if ($this->input->post('is_active_parents_name') == 1) {
                $parentsname = $this->input->post('is_active_parents_name');
            }
            if ($this->input->post('is_active_dob') == 1) {
                $dob = $this->input->post('is_active_dob');
            }
            
            if ($this->input->post('is_active_mother_tongue') == 1) {
                $mothertongue = $this->input->post('is_active_mother_tongue');
            }
            if ($this->input->post('is_active_date_tc') == 1) {
                $datetc = $this->input->post('is_active_date_tc');
            }
            if ($this->input->post('is_active_caste') == 1) {
                $caste = $this->input->post('is_active_caste');
            }
            
            // $first_sub = $this->tcgeneration_model->getsubjectname($this->input->post('firstlang_id'));
            // $second_sub = $this->tcgeneration_model->getsubjectname($this->input->post('secondlang_id'));
            


            $data = array(
                'id'                     => $this->input->post('id'),
                'tc_name'                => $this->input->post('tc_name'),
                'school_name'            => $this->input->post('school_name'),
                'tc_head_tittle'         => $this->input->post('tc_head_tittle'),
                'tc_description'         => $this->input->post('tc_description'),
                'tc_address'             => $this->input->post('tc_address'),
                'tc_body'                => $this->input->post('tc_body'),

                'tc_date_left'           => $this->input->post('tc_date_left'),
                'tc_nationality'         => $this->input->post('tc_nationality'),
                'tc_second_year_course'  => $this->input->post('tc_second_year_course'),
                'tc_eligible_university_course'  => $this->input->post('tc_eligible_university_course'),
                'tc_receipt_scholarship' => $this->input->post('tc_receipt_scholarship'),
                'tc_receipt_concession'  => $this->input->post('tc_receipt_concession'),
                'tc_punishment_during_period'  => $this->input->post('tc_punishment_during_period'),
                'tc_optional_lang'       => $this->input->post('tc_optional_lang'),


                'tc_first_lang'          => $this->input->post('firstlang_id'),
                'tc_second_lang'         => $this->input->post('secondlang_id'),


                'tc_footer'              => $this->input->post('tc_footer'),
                'tc_conduct'             => $this->input->post('tc_conduct'),
                'tc_mother_tongue'       => $this->input->post('tc_mother_tongue'),
                'enable_student_name'    => $studentname,
                'enable_admission_date'  => $admissiondate,
                'enable_parents_name'    => $parentsname,
                'enable_mother_tongue'   => $mothertongue,
                'enable_date_tc'         => $datetc,
                'enable_caste'           => $caste,
                'enable_dob'             => $dob,
                'status'                 => 1,
            );







            // $data = array(
            //     'id'                     => $this->input->post('id'),
            //     'title'                  => $this->input->post('title'),
            //     'school_name'            => $this->input->post('school_name'),
            //     'school_address'         => $this->input->post('address'),
            //     'header_color'           => $this->input->post('header_color'),
            //     'enable_admission_no'    => $admission_no,
            //     'enable_student_name'    => $studentname,
            //     'enable_class'           => $class,
            //     'enable_fathers_name'    => $fathername,
            //     'enable_mothers_name'    => $mothername,
            //     'enable_address'         => $address,
            //     'enable_phone'           => $phone,
            //     'enable_dob'             => $dob,
            //     'enable_blood_group'     => $bloodgroup,
            //     'enable_vertical_card'   => $vertical_card,
            //     'enable_student_barcode' => $student_barcode,
            //     'status'                 => 1,
            // );

            // $removebackground_image = $this->input->post('removebackground_image');
            $removelogo_image       = $this->input->post('removelogo_image');
            // $removesign_image       = $this->input->post('removesign_image');

            
            if ($removelogo_image != '') {
                $data['logo'] = '';
            }

            // if ($removesign_image != '') {
            //     $data['sign_image'] = '';
            // }

            

            // if (isset($_FILES["logo_img"]) && $_FILES['logo_img']['name'] != '' && (!empty($_FILES['logo_img']['name']))) {
            //     $logo_img     = $this->media_storage->fileupload("logo_img", "./uploads/student_id_card/logo/");
            //     $data['logo'] = $logo_img;
            // }

            // if (isset($_FILES["logo_img"]) && $_FILES['logo_img']['name'] != '' && (!empty($_FILES['logo_img']['name']))) {
            //     $this->media_storage->filedelete($editidcard[0]->logo, "uploads/student_id_card/logo");
            // }
            
            if (isset($_FILES["logo_img"]) && $_FILES['logo_img']['name'] != '' && (!empty($_FILES['logo_img']['name']))) {
                $this->media_storage->filedelete($editidcard[0]->logo, "uploads/tcgeneration/logo");
            }


            if (isset($_FILES["logo_img"]) && $_FILES['logo_img']['name'] != '' && (!empty($_FILES['logo_img']['name']))) {
                $logo_img     = $this->media_storage->fileupload("logo_img", "./uploads/tcgeneration/logo/");
                $data['logo'] = $logo_img;
            }


            

            
            // $this->Student_id_card_model->addidcard($data);
            $insert_id = $this->tcgeneration_model->addtcgenerate($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/tcgeneration/createtc');
        }
    }

    public function delete($id)
    {
        $data['title'] = 'Certificate List';
        $row           = $this->tcgeneration_model->get($id);
        

        if ($row[0]->logo != '') {
            $this->media_storage->filedelete($row[0]->logo, "uploads/tcgeneration/logo/");
        }

        $this->tcgeneration_model->remove($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/tcgeneration/createtc');
    }

    public function view()
    {
        $id             = $this->input->post('certificateid');
        $output         = '';
        $data['idcard'] = $this->tcgeneration_model->idcardbyid($id);
        $certificate         = $this->tcgeneration_model->getcertificatebyid($id);
        $data['certificate'] = $certificate;
        $this->load->view('admin/tcgeneration/studentidcardpreview', $data);
    }

    
    public function generate($student, $class, $certificate)
    {
        $certificateResult         = $this->tcgeneration_model->getcertificatebyid($certificate);
        $data['certificateResult'] = $certificateResult;
        $resultlist                = $this->student_model->searchByClassStudent($class, $student);
        $data['resultlist']        = $resultlist;

        $this->load->view('admin/tcgeneration/transfercertificate', $data);
    }

    public function generatemultiple()
    {

        $studentid           = $this->input->post('data');
        $student_array       = json_decode($studentid);
        $certificate_id      = $this->input->post('certificate_id');
        $class               = $this->input->post('class_id');
        $data                = array();
        $results             = array();
        $std_arr             = array();
        
        $data['sch_setting'] = $this->setting_model->get();
        $certificate         = $this->tcgeneration_model->getcertificatebyid($certificate_id);
        $data['certificate'] = $certificate;

        $data['firstlang']  = $this->tcgeneration_model->getsubjectname($certificate[0]->tc_first_lang);
        $data['secondlang'] = $this->tcgeneration_model->getsubjectname($certificate[0]->tc_second_lang);
        
        foreach ($student_array as $key => $value) {
            $std_arr[] = $value->student_id;
        }
        $data['students'] = $this->student_model->getStudentsByArray($std_arr);
        foreach ($data['students'] as $key => $value) {
            $data['students'][$key]->name = $this->customlib->getFullName($value->firstname, $value->middlename, $value->lastname, $this->sch_setting_detail->middlename, $this->sch_setting_detail->lastname);
        }

        $data['sch_setting'] = $this->sch_setting_detail;
        $certificates        = $this->load->view('admin/tcgeneration/printcertificate', $data, true);
        echo $certificates;

    }

}



