<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Externalbulkimport extends Admin_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->load->library('media_storage');
        $this->config->load('app-config');
        $this->load->library('mailsmsconf');
        $this->load->library('encoding_lib');

        $this->load->model('examtype_model');

        $this->load->model('addpublicresult_model');

        $this->current_session = $this->setting_model->getCurrentSession();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        
    }


    public function import()
    {
        if (!$this->rbac->hasPrivilege('external_bulk_import', 'can_view')) {
            access_denied();
        }
        $data['title']      = $this->lang->line('import_student');
        $data['title_list'] = $this->lang->line('recently_added_student');

        $userdata           = $this->customlib->getUserData();

        $category                   = $this->addpublicresult_model->get();
        $data['categorylist']       = $category;

        $session                    = $this->examtype_model->sessions();
        $data['sessions']           = $session;

        $fields = array('hall_no','subject_code','subject_code','subject_code','subject_code');


        $data["fields"]       = $fields;

        $this->form_validation->set_rules('file', $this->lang->line('select_csv_file'), 'callback_handle_csv_upload');
        $this->form_validation->set_rules('academic_id', $this->lang->line('academic_year'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('exam_id', $this->lang->line('resut_type'), 'trim|required|xss_clean');


        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/results/externalresultbulkimport', $data);
            $this->load->view('layout/footer', $data);
        } else {

            $session = $this->setting_model->getCurrentSession();
            $examtype_id = $this->input->post('exam_id');
            $sessionid = $this->input->post('academic_id');

            $subjectsdata = $this->addpublicresult_model->subjectsgroupp($examtype_id,$sessionid);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if ($ext == 'csv') {
                $file = $_FILES['file']['tmp_name'];
                $this->load->library('CSVReader');
                $result = $this->csvreader->parse_file($file);

                if (!empty($result)) {
                    $rowcount = 0;
                    for ($i = 1; $i <= count($result); $i++) {
                        $student_data[$i] = array();
                        $n = 0;
                        
                        $hallticket_no = $result[$i]["hall_no"];

                        $stidd = $this->addpublicresult_model->getstudentid($hallticket_no);
                        
                        foreach ($subjectsdata as $subject_code) {

                            $marksid = $this->addpublicresult_model->getmarksidd($examtype_id, $subject_code['subid'], $sessionid);
                            
                            if(!empty($result[$i][$subject_code['subject_code']])){
                                $dataa = array(
                                    "stid" => $stidd,
                                    "resulgroup_id" => $examtype_id,
                                    "subjectid" => $subject_code['subid'],
                                    "actualmarks" => $result[$i][$subject_code['subject_code']], // Use subject code to access marks
                                    "markstableid" => $marksid,
                                    "session_id" => $sessionid
                                );

                                $insert_id = $this->addpublicresult_model->add($dataa);
                                
                                $data1=array(
                                    'stid' => $stidd,
                                    'resultype_id' => $examtype_id,
                                    'session_id' => $this->current_session,
                                    'assign_status' => 0,
                                );
                    
                                $in = $this->addpublicresult_model->addresult($data1);
                            }
                        }
                        //$this->examtype_model->updateresultt($dataa);
                        }
                    } else {
                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
                    }
                } else {
                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
                }
            }
            
            redirect('admin/results/externalbulkimport/import');
        }
    }


    public function handle_csv_upload()
    {
        $error = "";
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('csv');
            $mimes       = array('text/csv',
                'text/plain',
                'application/csv',
                'text/comma-separated-values',
                'application/excel',
                'application/vnd.ms-excel',
                'application/vnd.msexcel',
                'text/anytext',
                'application/octet-stream',
                'application/txt');
            $temp      = explode(".", $_FILES["file"]["name"]);
            $extension = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if (!in_array($_FILES['file']['type'], $mimes)) {
                $error .= "Error opening the file<br />";
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('file_type_not_allowed'));
                return false;
            }
            if (!in_array($extension, $allowedExts)) {
                $error .= "Error opening the file<br />";
                $this->form_validation->set_message('handle_csv_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($error == "") {
                return true;
            }
        } else {
            $this->form_validation->set_message('handle_csv_upload', $this->lang->line('please_select_file'));
            return false;
        }
    }


    public function exportformat()
    {
        $this->load->helper('download');
        $filepath = "./backend/import/import_student_admission_no.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_student_admission_no.csv';

        force_download($name, $data);
    }


}



?>