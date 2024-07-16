<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Hallticketimport extends Admin_Controller
{

    public $sch_setting_detail = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->config->load('app-config');
        $this->load->library('mailsmsconf');
        $this->load->library('encoding_lib');
        $this->load->model('student_model');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        
    }

    public function import()
    {
        if (!$this->rbac->hasPrivilege('hallticketbulkimport', 'can_view')) {
            access_denied();
        }
        $data['title']      = $this->lang->line('import_student');
        $data['title_list'] = $this->lang->line('recently_added_student');

        $userdata           = $this->customlib->getUserData();


        $fields = array('hall_no','admi_no','session');



        $data["fields"]       = $fields;
        $this->form_validation->set_rules('file', $this->lang->line('select_csv_file'), 'callback_handle_csv_upload');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/hallticket/hallticketbulkimport', $data);
            $this->load->view('layout/footer', $data);
        } else {

            $session = $this->setting_model->getCurrentSession();
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'csv') {
                    $file = $_FILES['file']['tmp_name'];
                    $this->load->library('CSVReader');
                    $result = $this->csvreader->parse_file($file);

                    if (!empty($result)) {
                        $rowcount = 0;
                        $fail = '';
                        for ($i = 1; $i <= count($result); $i++) {

                            
                            $student_data[$i] = array();
                            $n                = 0;

                            foreach ($result[$i] as $key => $value) {
                                $student_data[$i][$fields[$n]] = $this->encoding_lib->toUTF8($result[$i][$key]);
                                $n++;
                            }

                            $hall_no                           = $student_data[$i]["hall_no"];
                            $admi_no                           = $student_data[$i]["admi_no"];
                            $session                           = $student_data[$i]["session"];

                            $admi_no_id=$this->student_model->getadmi_no_id_for_hallticket($admi_no);
                            if (!$admi_no_id) {
                                // Admission number not found, cannot proceed with import
                                $fail = $fail . ',' . $admi_no;
                                continue;
                            }
                            $check_exsist_id=$this->student_model->check_admino_id($admi_no_id);

                            if(!$check_exsist_id){
                                if($hall_no){
                                    $data = array(
                                        "admi_no_id" => $admi_no_id,
                                        "std_hallticket" => $hall_no,
                                        "hallticket_status" => 1
                                    );

                                    // $this->student_model->hallticket_no_add($data);
                                    $success = $this->student_model->hallticket_no_add($data);
                                    if ($success) {
                                        $rowcount++;
                                    } else {
                                        $fail=$fail . ',' . $i;
                                    }
                                }
                            }
                            
                        }
                        $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('total') . ' ' . count($result) . $this->lang->line('records_found_in_csv_file_total') . $rowcount . ' ' . $this->lang->line('records_imported_successfully') . $fail .'</div>');

                    } else {
                        $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('no_record_found') . '</div>');
                    }

                } else {

                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">' . $this->lang->line('please_upload_csv_file_only') . '</div>');
                }
            }

            redirect('admin/hallticket/hallticketimport/import');

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
        $filepath = "./backend/import/import_student_hallticket_no.csv";
        $data     = file_get_contents($filepath);
        $name     = 'import_student_hallticket_no.csv';

        force_download($name, $data);
    }


}


?>