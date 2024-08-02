<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Template extends MY_Addon_CBSEController
{

    public function __construct()
    {
        parent::__construct();
      
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_template', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'cbse_exam');
        $this->session->set_userdata('sub_menu', 'cbse_exam/template');
        $data['title'] = 'Category List';
        $category_result = $this->category_model->get();
        $data['categorylist'] = $category_result;
        $subjectgroupList = $this->subjectgroup_model->getByID();
        $data['subjectgroupList'] = $subjectgroupList;
        $data['result'] = $this->cbseexam_template_model->gettemplatelist();
        $class = $this->class_model->get();
        $data['classlist'] = $class;
        $data['marksheet'] = $this->cbseexam_result_model->marksheet_type();
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/template/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_template', 'can_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('template'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('left_logo', $this->lang->line('left_logo'), 'callback_handle_upload[left_logo]');
        $this->form_validation->set_rules('right_logo', $this->lang->line('right_logo'), 'callback_handle_upload[right_logo]');
        $this->form_validation->set_rules('background_img', $this->lang->line('sign_image'), 'callback_handle_upload[background_img]');
        $this->form_validation->set_rules('left_sign', $this->lang->line('left_sign'), 'callback_handle_upload[left_sign]');
        $this->form_validation->set_rules('middle_sign', $this->lang->line('middle_sign'), 'callback_handle_upload[middle_sign]');
        $this->form_validation->set_rules('right_sign', $this->lang->line('right_sign'), 'callback_handle_upload[right_sign]');

        if ($this->form_validation->run() == false) {

            $msg['name'] = form_error('name');
            $msg['class_id'] = form_error('class_id');
            $msg['left_logo'] = form_error('left_logo');
            $msg['right_logo'] = form_error('right_logo');
            $msg['background_img'] = form_error('background_img');
            $msg['left_sign'] = form_error('left_sign');
            $msg['middle_sign'] = form_error('middle_sign');
            $msg['right_sign'] = form_error('right_sign');

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');

        } else {

            if (isset($_POST['is_name'])) {
                $is_name = 1;
            } else {
                $is_name = 0;
            }
            if (isset($_POST['is_father_name'])) {
                $is_father_name = 1;
            } else {
                $is_father_name = 0;
            }
            if (isset($_POST['is_mother_name'])) {
                $is_mother_name = 1;
            } else {
                $is_mother_name = 0;
            }
            if (isset($_POST['is_admission_no'])) {
                $is_admission_no = 1;
            } else {
                $is_admission_no = 0;
            }
            if (isset($_POST['exam_session'])) {
                $exam_session = 1;
            } else {
                $exam_session = 0;
            }
            if (isset($_POST['is_roll_no'])) {
                $is_roll_no = 1;
            } else {
                $is_roll_no = 0;
            }
            if (isset($_POST['is_photo'])) {
                $is_photo = 1;
            } else {
                $is_photo = 0;
            }
            if (isset($_POST['is_division'])) {
                $is_division = 1;
            } else {
                $is_division = 0;
            }
            if (isset($_POST['is_class'])) {
                $is_class = 1;
            } else {
                $is_class = 0;
            }
            if (isset($_POST['is_section'])) {
                $is_section = 1;
            } else {
                $is_section = 0;
            }
            if (isset($_POST['is_dob'])) {
                $is_dob = 1;
            } else {
                $is_dob = 0;
            }
            if (isset($_POST['remark'])) {
                $is_remark = 1;
            } else {
                $is_remark = 0;
            }

            $data = array(

                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'created_by' => $this->customlib->getStaffID(),
                'school_name' => $this->input->post('school_name'),
                'exam_center' => $this->input->post('exam_center'),
                'date' => $this->customlib->dateFormatToYYYYMMDD($this->input->post('date')),
                'is_name' => $is_name,
                'is_father_name' => $is_father_name,
                'is_mother_name' => $is_mother_name,
                'is_admission_no' => $is_admission_no,
                'is_roll_no' => $is_roll_no,
                'is_photo' => $is_photo,
                'is_class' => $is_class,
                'is_division' => $is_division,
                'is_section' => $is_section,
                'is_dob' => $is_dob,
                'is_remark' => $is_remark,
                'session_id'=>$this->setting_model->getCurrentSession(),
                'content' => $this->input->post('content'),
                'content_footer' => $this->input->post('content_footer'),
                'exam_session' => $exam_session,
                'orientation' => $this->input->post('orientation'),
            );

            if (isset($_FILES["header_image"]) && !empty($_FILES["header_image"]['name'])) {
                $time = md5($_FILES["header_image"]['name'] . microtime());
                $fileInfo = pathinfo($_FILES["header_image"]["name"]);
                $img_name = $time . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["header_image"]["tmp_name"], "./uploads/cbseexam/template/header_image/" . $img_name);
                $data['header_image'] = $img_name;
            }
            if (isset($_FILES["left_sign"]) && !empty($_FILES["left_sign"]['name'])) {
                $time = md5($_FILES["left_sign"]['name'] . microtime());
                $fileInfo = pathinfo($_FILES["left_sign"]["name"]);
                $img_name = $time . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["left_sign"]["tmp_name"], "./uploads/cbseexam/template/left_sign/" . $img_name);
                $data['left_sign'] = $img_name;
            }
            if (isset($_FILES["middle_sign"]) && !empty($_FILES["middle_sign"]['name'])) {
                $time = md5($_FILES["middle_sign"]['name'] . microtime());
                $fileInfo = pathinfo($_FILES["middle_sign"]["name"]);
                $img_name = $time . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["middle_sign"]["tmp_name"], "./uploads/cbseexam/template/middle_sign/" . $img_name);
                $data['middle_sign'] = $img_name;
            }
            if (isset($_FILES["right_sign"]) && !empty($_FILES["right_sign"]['name'])) {
                $time = md5($_FILES["right_sign"]['name'] . microtime());
                $fileInfo = pathinfo($_FILES["right_sign"]["name"]);
                $img_name = $time . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["right_sign"]["tmp_name"], "./uploads/cbseexam/template/right_sign/" . $img_name);
                $data['right_sign'] = $img_name;
            }
            if (isset($_FILES["background_img"]) && !empty($_FILES["background_img"]['name'])) {
                $time = md5($_FILES["background_img"]['name'] . microtime());
                $fileInfo = pathinfo($_FILES["background_img"]["name"]);
                $img_name = $time . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["background_img"]["tmp_name"], "./uploads/cbseexam/template/background_img/" . $img_name);
                $data['background_img'] = $img_name;
            }

            if (!empty($_POST['section'])) {
                $template_id = $this->cbseexam_template_model->add($data);
                foreach ($_POST['section'] as $key => $value) {
                    $template_class_section = array(
                        'cbse_template_id' => $template_id,
                        'class_section_id' => $value
                    );
                    $this->cbseexam_template_model->add_class_section($template_class_section);
                }
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $msg['section'] = $this->lang->line('please_select_atleast_one_section');
                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }
        }
        echo json_encode($array);
    }

    public function edit()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_template', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('name', $this->lang->line('template'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('left_logo', $this->lang->line('left_logo'), 'callback_handle_upload[left_logo]');
        $this->form_validation->set_rules('right_logo', $this->lang->line('right_logo'), 'callback_handle_upload[right_logo]');
        $this->form_validation->set_rules('background_img', $this->lang->line('background_image'), 'callback_handle_upload[background_img]');
        $this->form_validation->set_rules('left_sign', $this->lang->line('left_sign'), 'callback_handle_upload[left_sign]');
        $this->form_validation->set_rules('middle_sign', $this->lang->line('middle_sign'), 'callback_handle_upload[middle_sign]');
        $this->form_validation->set_rules('right_sign', $this->lang->line('right_sign'), 'callback_handle_upload[right_sign]');

        if ($this->form_validation->run() == false) {
            $msg['name'] = form_error('name');
            $msg['class_id'] = form_error('class_id');
            $msg['left_logo'] = form_error('left_logo');
            $msg['right_logo'] = form_error('right_logo');
            $msg['background_img'] = form_error('background_img');
            $msg['left_sign'] = form_error('left_sign');
            $msg['middle_sign'] = form_error('middle_sign');
            $msg['right_sign'] = form_error('right_sign');

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            if (isset($_POST['stu_name'])) {
                $is_name = 1;
            } else {
                $is_name = 0;
            }
            if (isset($_POST['father_name'])) {
                $is_father_name = 1;
            } else {
                $is_father_name = 0;
            }
            if (isset($_POST['mother_name'])) {
                $is_mother_name = 1;
            } else {
                $is_mother_name = 0;
            }
            if (isset($_POST['admission_no'])) {
                $is_admission_no = 1;
            } else {
                $is_admission_no = 0;
            }
            if (isset($_POST['examsession'])) {
                $exam_session = 1;
            } else {
                $exam_session = 0;
            }
            if (isset($_POST['roll_no'])) {
                $is_roll_no = 1;
            } else {
                $is_roll_no = 0;
            }
            if (isset($_POST['division'])) {
                $is_division = 1;
            } else {
                $is_division = 0;
            }
            if (isset($_POST['photo'])) {
                $is_photo = 1;
            } else {
                $is_photo = 0;
            }
            if (isset($_POST['class'])) {
                $is_class = 1;
            } else {
                $is_class = 0;
            }
            if (isset($_POST['is_section'])) {
                $is_section = 1;
            } else {
                $is_section = 0;
            }
            if (isset($_POST['date_of_birth'])) {
                $is_dob = 1;
            } else {
                $is_dob = 0;
            }
            if (isset($_POST['is_remark'])) {
                $is_remark = 1;
            } else {
                $is_remark = 0;
            }

            $data = array(
                'id' => $this->input->post('templateid'),
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'created_by' => $this->customlib->getStaffID(),
                'school_name' => $this->input->post('school_name'),
                'exam_center' => $this->input->post('exam_center'),
                'date' => $this->customlib->dateFormatToYYYYMMDD($this->input->post('date')),
                'is_name' => $is_name,
                'is_father_name' => $is_father_name,
                'is_division' => $is_division,
                'is_mother_name' => $is_mother_name,
                'is_admission_no' => $is_admission_no,
                'is_roll_no' => $is_roll_no,
                'is_photo' => $is_photo,
                'is_class' => $is_class,
                'is_section' => $is_section,
                'is_dob' => $is_dob,
                'is_remark' => $is_remark,
                'content' => $this->input->post('content'),
                'content_footer' => $this->input->post('content_footer'),
                'exam_session' => $exam_session,
                'orientation' => $this->input->post('orientation'),
            );

            $result = $this->cbseexam_template_model->get($this->input->post('templateid'));
            
            if (isset($_FILES["header_image"]) && !empty($_FILES["header_image"]['name'])) {
                $time = md5($_FILES["header_image"]['name'] . microtime());
                $fileInfo = pathinfo($_FILES["header_image"]["name"]);
                $img_name = $time . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["header_image"]["tmp_name"], "./uploads/cbseexam/template/header_image/" . $img_name);
                $data['header_image'] = $img_name;
            } else {
                $data['header_image'] = $result['header_image'];
            }
            
            
            if (isset($_FILES["left_logo"]) && !empty($_FILES["left_logo"]['name'])) {
                $time = md5($_FILES["left_logo"]['name'] . microtime());
                $fileInfo = pathinfo($_FILES["left_logo"]["name"]);
                $img_name = $time . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["left_logo"]["tmp_name"], "./uploads/cbseexam/template/left_logo/" . $img_name);
                $data['left_logo'] = $img_name;
            } else {
                $data['left_logo'] = $result['left_logo'];
            }
            if (isset($_FILES["right_logo"]) && !empty($_FILES["right_logo"]['name'])) {
                $time = md5($_FILES["right_logo"]['name'] . microtime());
                $fileInfo = pathinfo($_FILES["right_logo"]["name"]);
                $img_name = $time . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["right_logo"]["tmp_name"], "./uploads/cbseexam/template/right_logo/" . $img_name);
                $data['right_logo'] = $img_name;
            } else {
                $data['right_logo'] = $result['right_logo'];
            }
            if (isset($_FILES["left_sign"]) && !empty($_FILES["left_sign"]['name'])) {
                $time = md5($_FILES["left_sign"]['name'] . microtime());
                $fileInfo = pathinfo($_FILES["left_sign"]["name"]);
                $img_name = $time . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["left_sign"]["tmp_name"], "./uploads/cbseexam/template/left_sign/" . $img_name);
                $data['left_sign'] = $img_name;
            } else {
                $data['left_sign'] = $result['left_sign'];
            }
            if (isset($_FILES["middle_sign"]) && !empty($_FILES["middle_sign"]['name'])) {
                $time = md5($_FILES["middle_sign"]['name'] . microtime());
                $fileInfo = pathinfo($_FILES["middle_sign"]["name"]);
                $img_name = $time . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["middle_sign"]["tmp_name"], "./uploads/cbseexam/template/middle_sign/" . $img_name);
                $data['middle_sign'] = $img_name;
            } else {
                $data['middle_sign'] = $result['middle_sign'];
            }
            if (isset($_FILES["right_sign"]) && !empty($_FILES["right_sign"]['name'])) {
                $time = md5($_FILES["right_sign"]['name'] . microtime());
                $fileInfo = pathinfo($_FILES["right_sign"]["name"]);
                $img_name = $time . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["right_sign"]["tmp_name"], "./uploads/cbseexam/template/right_sign/" . $img_name);
                $data['right_sign'] = $img_name;
            } else {
                $data['right_sign'] = $result['right_sign'];
            }
            if (isset($_FILES["background_img"]) && !empty($_FILES["background_img"]['name'])) {
                $time = md5($_FILES["background_img"]['name'] . microtime());
                $fileInfo = pathinfo($_FILES["background_img"]["name"]);
                $img_name = $time . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["background_img"]["tmp_name"], "./uploads/cbseexam/template/background_img/" . $img_name);
                $data['background_img'] = $img_name;
            } else {
                $data['background_img'] = $result['background_img'];
            }
 
            if (!empty($_POST['section'])) {
                $this->cbseexam_template_model->add($data);
                $this->cbseexam_template_model->deleteclasssectionbytemplateid($this->input->post('templateid'));

                foreach ($_POST['section'] as $key => $value) {

                    $template_class_section = array(
                        'cbse_template_id' => $this->input->post('templateid'),
                        'class_section_id' => $value
                    );
                    $this->cbseexam_template_model->add_class_section($template_class_section);
                }

                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $msg['section'] = $this->lang->line('please_select_atleast_one_section');
                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }
        }
        echo json_encode($array);

    }

    public function getdata()
    {
        $template_id = $this->input->post('template_id');
        $result = $this->cbseexam_template_model->get($template_id);
        $data['classlist'] = $this->class_model->get();
        $data['result'] = $result;
        $data['sections'] = $this->cbseexam_template_model->getclasssection($template_id);
        $data['selected_class_id'] = $data['sections'][0]['class_id'];
        $data['selected_section_id'] = json_encode($data['sections']);

        $page = $this->load->view('cbseexam/template/edit', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function viewtemplate()
    {
        $template_id = $this->input->post('template_id');
        $result = $this->cbseexam_template_model->get($template_id);
      
        $data = [];
        $data['template'] = $result;
        $page = $this->load->view('cbseexam/template/viewtemplate', $data, true);
        echo json_encode(array('status' => 1, 'page' => $page));
    }

    public function get_ClassSectionByTermId($termid)
    {
        $result = $this->cbseexam_term_model->gettermbyid($termid);
        $data['class_id'] = $result[0]['class_id'];
        $data['sections'] = $this->section_model->getClassBySection($result[0]['class_id']);
        echo json_encode($data);
    }

    public function remove()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_template', 'can_delete')) {
            access_denied();
        }

        $templateid = $this->input->post('templateid');
        $this->cbseexam_template_model->remove($templateid);
        $array = array('status' => '1', 'error' => '', 'message' => $this->lang->line('delete_message'));
        echo json_encode($array);
    }

    public function get_examdata()
    {
        $marksheet_type = $_POST['marksheet_type'];
        $template_id = $_POST['template_id'];

        $result = $this->cbseexam_template_model->all_term($template_id);
        $data['templatedata'] = $this->cbseexam_template_model->get_templatedata($template_id);

      
        if ($marksheet_type == 'all_term') {
            $data['result'] = $result;
            $data['examdata'] = $this->load->view('cbseexam/template/_all_term', $data, true);
        } elseif ($marksheet_type == 'term_wise') {
            $data['result'] = $result;
            $data['examdata'] = $this->load->view('cbseexam/template/_term_wise', $data, true);
        } elseif ($marksheet_type == 'without_term') {
            $data['result'] = $result;
            $data['examdata'] = $this->load->view('cbseexam/template/_without_term', $data, true);
        } elseif ($marksheet_type == 'exam_wise') {
            $data['result'] = $result;
            $data['examdata'] = $this->load->view('cbseexam/template/_exam_wise', $data, true);
        }
        echo json_encode($data);
    }

    public function linkexams()
    {
        if (!$this->rbac->hasPrivilege('cbse_exam_link_exam', 'can_view')) {
            access_denied();
        }

        $this->form_validation->set_rules('marksheet', $this->lang->line('marksheet_type'), 'trim|required|xss_clean');
        if ($this->input->post('marksheet') == 'term_wise') {

            $this->form_validation->set_rules('teacher_remark', $this->lang->line('teacher_remark'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('grading', $this->lang->line('grading'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('terms[]', $this->lang->line('term'), 'trim|required|xss_clean');

            $selected_exams = [];
            $term_exam_weightage = "";
            if ($this->input->post('terms')) {
                foreach ($_POST['terms'] as $term_key => $term_value) {

                    if (!empty($_POST['exam'][$term_value])) {
                        $term_exam_weightage = 0;
                        foreach ($_POST['exam'][$term_value] as $exam_key => $exam_value) {
                            $selected_exams[] = $exam_value;

                            if ($_POST['weightage'][$term_value][$exam_value] != "") {

                                $term_exam_weightage = $term_exam_weightage + $_POST['weightage'][$term_value][$exam_value];
                            }
                        }
                    }
                }
            }

            $teacher_remark = $this->input->post('teacher_remark');
            if (!empty($selected_exams) && isset($teacher_remark)) {

                $selectable = implode("|", $selected_exams);
                $this->form_validation->set_rules('teacher_remark', $this->lang->line('teacher_remark'), "callback_check_teacher_remark[" . $selectable . "]");

            }

            $exam_grading = $this->input->post('grading');
            if (!empty($selected_exams) && isset($exam_grading)) {

                $selectable = implode("|", $selected_exams);
                $this->form_validation->set_rules('grading', $this->lang->line('exam_grading'), "callback_check_exam_grading[" . $selectable . "]");

            }

            if ($term_exam_weightage != 100 || $term_exam_weightage != 0) {

                $this->form_validation->set_rules('max_weightage', $this->lang->line('weightage'), 'callback_check_weightage[' . $term_exam_weightage . ']');

            }

        } elseif ($this->input->post('marksheet') == 'all_term') {

            $this->form_validation->set_rules('teacher_remark', $this->lang->line('teacher_remark'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('grading', $this->lang->line('grading'), 'trim|required|xss_clean');
            if (!$this->input->post('terms')) {
                $this->form_validation->set_rules('terms[]', $this->lang->line('term'), 'trim|required|xss_clean');
            }

            $selected_exams = [];
            $term_exam_weightage = "";

            if ($this->input->post('terms')) {
                $term_exam_weightage = 0;
                foreach ($_POST['terms'] as $term_key => $term_value) {
                    $term_exam_weightage += (int) $this->input->post('term_weightage')[$term_value];
                    if (!empty($_POST['exam'][$term_value])) {
                        foreach ($_POST['exam'][$term_value] as $exam_key => $exam_value) {
                            $selected_exams[] = $exam_value;

                        }
                    }
                }
            }

            if ($term_exam_weightage != 100) {
                $this->form_validation->set_rules('max_weightage', $this->lang->line('weightage'), 'callback_check_weightage[' . $term_exam_weightage . ']');
            }

            $teacher_remark = $this->input->post('teacher_remark');
            if (!empty($selected_exams) && isset($teacher_remark)) {
                $selectable = implode("|", $selected_exams);
                $this->form_validation->set_rules('teacher_remark', $this->lang->line('teacher_remark'), "callback_check_teacher_remark[" . $selectable . "]");
            }

            $exam_grading = $this->input->post('grading');
            if (!empty($selected_exams) && isset($exam_grading)) {
                $selectable = implode("|", $selected_exams);
                $this->form_validation->set_rules('grading', $this->lang->line('exam_grading'), "callback_check_exam_grading[" . $selectable . "]");
            }

        } elseif ($_POST['marksheet'] == 'without_term') {

            $this->form_validation->set_rules('teacher_remark', $this->lang->line('teacher_remark'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('grading', $this->lang->line('grading'), 'trim|required|xss_clean');
            $selected_exams = [];
            $term_exam_weightage = 0;
            if (!empty($_POST['exam'])) {
                if (count($_POST['exam']) <= 1) {
                    $this->form_validation->set_rules('multiple_exams', $this->lang->line('exams'), 'callback_check_exams[' . count($_POST['exam']) . ']');
                }
                foreach ($_POST['exam'] as $exam_key => $exam_value) {
                    $selected_exams[] = $exam_value;
                    if ($_POST['weightage'][$exam_value] != "") {
                        $term_exam_weightage += $_POST['weightage'][$exam_value];
                    }
                }
            }

            if ($term_exam_weightage != 100) {
                $this->form_validation->set_rules('max_weightage', $this->lang->line('weightage'), 'callback_check_weightage[' . $term_exam_weightage . ']');
            }

            $teacher_remark = $this->input->post('teacher_remark');
            if (!empty($selected_exams) && isset($teacher_remark)) {
                $selectable = implode("|", $selected_exams);
                $this->form_validation->set_rules('teacher_remark', $this->lang->line('teacher_remark'), "callback_check_teacher_remark[" . $selectable . "]");
            }

            $exam_grading = $this->input->post('grading');
            if (!empty($selected_exams) && isset($exam_grading)) {
                $selectable = implode("|", $selected_exams);
                $this->form_validation->set_rules('grading', $this->lang->line('exam_grading'), "callback_check_exam_grading[" . $selectable . "]");
            }
        }

        if ($this->form_validation->run() == false) {
            $msg['marksheet'] = form_error('marksheet');
            $msg['max_weightage'] = form_error('max_weightage');
            $msg['teacher_remark'] = form_error('teacher_remark');
            $msg['multiple_exams'] = form_error('multiple_exams');
            $msg['terms'] = form_error('terms[]');
            $msg['grading'] = form_error('grading');
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $templatedata = array(
                'marksheet_type' => $_POST['marksheet'],
                'id' => $_POST['template_id'],
            );
            if (isset($_POST['grading'])) {
                $templatedata['gradeexam_id'] = $_POST['grading'];
            }
            if (isset($_POST['teacher_remark'])) {
                $templatedata['remarkexam_id'] = $_POST['teacher_remark'];
            }
            if (($_POST['marksheet'] == 'exam_wise')) {
                $exam_first = $this->input->post('exam');
                $templatedata['gradeexam_id'] = $exam_first[0];
                $templatedata['remarkexam_id'] = $exam_first[0];
            }
            $template_id = $this->cbseexam_template_model->add($templatedata);

            $this->cbseexam_template_model->delete_template_record($_POST['template_id']);

            if (($_POST['marksheet'] == 'all_term') || ($_POST['marksheet'] == 'term_wise')) {
                $cbse_template_terms = array();
                if (!empty($_POST['terms'])) {

                    foreach ($_POST['terms'] as $key => $value) {
                        $cbse_template_terms = array(
                            'cbse_template_id' => $_POST['template_id'],
                            'cbse_term_id' => $value,
                        );

                        if (isset($_POST['term_weightage'])) {
                            $cbse_template_terms['weightage'] = $_POST['term_weightage'][$value];
                        }

                        $cbse_template_term_id = $this->cbseexam_template_model->cbse_template_terms($cbse_template_terms);

                        if (!empty($_POST['exam'][$value])) {
                            foreach ($_POST['exam'][$value] as $exam_key => $exam_value) {

                                $cbse_template_term_exam = array(
                                    'cbse_template_term_id' => $cbse_template_term_id,
                                    'cbse_exam_id' => $exam_value,
                                    'cbse_template_id' => $_POST['template_id'],
                                );

                                if (isset($_POST['weightage'])) {
                                    $cbse_template_term_exam['weightage'] = $_POST['weightage'][$value][$exam_value];
                                }

                                $this->cbseexam_template_model->cbse_template_term_exams($cbse_template_term_exam);
                                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
                            }
                        } else {
                            $msg['section'] = $this->lang->line('please_select_exam');
                            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
                        }
                    }
                } else {
                    $msg['section'] = $this->lang->line('please_select_term');
                    $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
                }

            } elseif (($_POST['marksheet'] == 'exam_wise') || ($_POST['marksheet'] == 'without_term')) {

                if (!empty($_POST['exam'])) {
                    foreach ($_POST['exam'] as $exam_key => $exam_value) {

                        $cbse_template_term_exam['cbse_exam_id'] = $exam_value;
                        $cbse_template_term_exam['cbse_template_id'] = $_POST['template_id'];

                        if (isset($_POST['weightage'])) {
                            $cbse_template_term_exam['weightage'] = $_POST['weightage'][$exam_value];
                        }
                        $this->cbseexam_template_model->cbse_template_term_exams($cbse_template_term_exam);
                        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
                        # code...
                    }
                } else {
                    $msg['section'] = $this->lang->line('please_select_exam');
                    $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
                }
            }

        }

        echo json_encode($array);
    }

    public function get()
    {
        $class_section_id = $_POST['class_section_id'];
 
        $template_list = $this->cbseexam_template_model->getTemplateListbyclasssectionid($class_section_id);
        echo json_encode($template_list);
    }

    public function check_exams($field, $exams)
    {
        if ($exams >= 2) {
            // user exists
            return true;
        } else {
            $this->form_validation->set_message('check_exams', $this->lang->line('select_multiple_exams'));
            return false;
        }
    }

    public function check_exam_grading($field, $exams)
    {
        $exams = explode("|", $exams);
        if (in_array($field, $exams)) {
            // user exists
            return true;
        } else {
            $this->form_validation->set_message('check_exam_grading', $this->lang->line('grading_should_be_choose_from_selected_exam'));
            return false;
        }
        return true;
    }

    public function check_teacher_remark($field, $exams)
    {
        $exams = explode("|", $exams);
        if (in_array($field, $exams)) {
            // user exists
            return true;
        } else {
            $this->form_validation->set_message('check_teacher_remark', $this->lang->line('teacher_remark_should_be_choose_from_selected_exam'));
            return false;
        }
        return true;
    }

    public function check_weightage($field, $weightage)
    {
        if ($weightage == 100) {
            // user exists
            return true;
        } else {
            if (strlen($weightage) != 0) {
                if ($weightage == 0) {
                    $this->form_validation->set_message('check_weightage', $this->lang->line('select_term_exam_weightage_should_not_be_zero_or_empty'));
                    return false;
                } elseif ($weightage > 100 || $weightage < 100) {
                    $this->form_validation->set_message('check_weightage', $this->lang->line('select_term_exam_weightage_should_not_be_greater_or_less'));
                    return false;
                }
            }
        }
        return true;
    }

    public function handle_upload($str, $var)
    {
        $result = $this->filetype_model->get();
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->image_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->image_mime)));
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

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
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed') . " " . $this->lang->line('or') . " " . $this->lang->line('extension_not_allowed'));
                return false;
            }

            return true;
        }
        return true;
    }
    
    public function templatewiserank($template_id)
    {
        $data = array();
        $data['sch_setting'] = $this->setting_model->getSetting();
        $class_section_id = $this->input->post('class_section_id');
        $data['template'] = $this->cbseexam_template_model->get($template_id);
        $data['studentList'] = $this->cbseexam_result_model->getTemplateStudents($template_id);
        $data['cbse_template_id'] = $template_id;
        $this->load->view('layout/header', $data);
        $this->load->view('cbseexam/template/templatewiserank', $data);
        $this->load->view('layout/footer', $data);
    }

}