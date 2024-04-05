<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}




class Conference extends Admin_Controller
{

    private $conference_setting = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->library('zoom_mail_sms');
        $this->load->model(array('conference_model', 'conferencehistory_model', 'zoomsetting_model'));
        $this->conference_setting = $this->zoomsetting_model->get();
        $this->sch_setting_detail = $this->setting_model->getSetting();
        // if ($this->router->fetch_method() != "index") {
        //     $this->auth->addonchk('sszlc', site_url('admin/conference'));
        // }
    }

    public function index()
    {

        if (!$this->rbac->hasPrivilege('setting', 'can_view')) {
            access_denied();
        }
        $this->load->config('zoom-config');

        $this->session->set_userdata('top_menu', 'conference');
        $this->session->set_userdata('sub_menu', 'conference/zoom_api_setting');
        $data            = array();
        $data['title']   = 'Zoom Setting';
        $data['version'] = $this->config->item('version');

        $setting = $this->zoomsetting_model->get();
        if (empty($setting)) {
            $setting                    = new stdClass();
            $setting->zoom_api_key      = "";
            $setting->zoom_api_secret   = "";
            $setting->use_teacher_api   = 1;
            $setting->use_zoom_app      = 1; 
            $setting->use_zoom_app_user = 1;
        }

        $data['setting'] = $setting;

        $this->form_validation->set_rules('zoom_api_key', $this->lang->line('zoom_api_key'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('zoom_api_secret', $this->lang->line('zoom_api_secret'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('use_teacher_api', $this->lang->line('teacher_api_credential'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('use_zoom_app', $this->lang->line('use_zoom_client_for_staff'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('use_zoom_app_user', $this->lang->line('use_zoom_client_for_student'), 'required|trim|xss_clean');

        if ($this->form_validation->run() === false) {
            $data['title'] = 'Email Config List';
            $this->load->view('layout/header', $data);
            $this->load->view('admin/conference/index', $data);
            $this->load->view('layout/footer', $data);
        } else {

            $data_insert = array(
                'zoom_api_key'    => $this->input->post('zoom_api_key'),
                'zoom_api_secret' => $this->input->post('zoom_api_secret'),
                'use_teacher_api' => $this->input->post('use_teacher_api'),
                'use_zoom_app'    => $this->input->post('use_zoom_app'),
                'use_zoom_app_user'    => $this->input->post('use_zoom_app_user'),
            );

            $this->zoomsetting_model->add($data_insert);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/conference');
        }
    }

    public function timetable()
    {
        if (!$this->rbac->hasPrivilege('live_classes', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'conference');
        $this->session->set_userdata('sub_menu', 'conference/live_class');
        $data                       = array();
        $data['conference_setting'] = $this->conference_setting;
        $role                       = json_decode($this->customlib->getStaffRole());
        $class                      = $this->class_model->get();
        $data['classlist']          = $class;
        $data['role']               = $role;
        $staff_id                   = $this->customlib->getStaffID();
        $data['logged_staff_id']    = $staff_id;

        if ($role->id == 2) {
            $stafflist         = $this->staff_model->getEmployee(2);
            $data['stafflist'] = $stafflist;
            $data['timetable'] = array();
            $days              = $this->customlib->getDaysname();

            $data['conferences'] = $this->conference_model->getByStaff($this->customlib->getStaffID());
            $userdata            = $this->customlib->getUserData();
            $role_id             = $userdata["role_id"];
            $condition           = "";
            foreach ($days as $day_key => $day_value) {
                $timetableid = "";
                $concate     = "no";
                if (isset($role_id) && ($userdata["role_id"] == 2) && ($userdata["class_teacher"] == "yes")) {
                    $myclasssubjects = $this->subjecttimetable_model->getByStaffClassTeacherandDay($this->customlib->getStaffID(), $day_key);

                    if (!empty($myclasssubjects[0]->timetable_id)) {

                        $timetableid = $myclasssubjects[0]->timetable_id;
                        $concate     = "yes";
                    }
                }

                $mysubjects = $this->subjecttimetable_model->getByTeacherSubjectandDay($this->customlib->getStaffID(), $day_key);
                if (!empty($mysubjects[0]->timetable_id)) {
                    if ($concate == 'yes') {
                        $timetableid = $timetableid . "," . $mysubjects[0]->timetable_id;
                    } else {
                        $timetableid = $mysubjects[0]->timetable_id;
                    }
                }

                if ($timetableid == '') {

                    $condition = " and subject_timetable.id in(0) ";
                } else {

                    $condition = " and subject_timetable.id in(" . $timetableid . ") ";
                }
                $data['timetable'][$day_value] = $this->subjecttimetable_model->getSyllabussubject($staff_id, $day_key, $condition);
            }
        } else {

            $data['conferences'] = $this->conference_model->getByStaff();
        }

        $this->load->view('layout/header');
        if ($role->id == 2) {

            $this->load->view('admin/conference/timetable', $data);
        } else {
            $roles         = $this->role_model->get();
            $data['roles'] = $roles;
            $this->load->view('admin/conference/stafftimetable', $data);
        }
        $this->load->view('layout/footer');
    }

    public function add()
    {

        $response = array();
        $this->form_validation->set_rules('title', $this->lang->line('class_title'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('class_date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('section_id[]', $this->lang->line('section'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('host_video', $this->lang->line('host_video'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('client_video', $this->lang->line('client_video'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('duration', $this->lang->line('class_duration_minutes'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title'        => form_error('title'),
                'date'         => form_error('date'),
                'class_id'     => form_error('class_id'),
                'section_id'   => form_error('section_id[]'),
                'host_video'   => form_error('host_video'),
                'client_video' => form_error('client_video'),
                'password'     => form_error('password'),
                'duration'     => form_error('duration'),
            );
            $response = array('status' => 0, 'error' => $data);
        } else {

            $role     = json_decode($this->customlib->getStaffRole());
            $api_type = 'global';
            $staff    = $this->staff_model->get($this->customlib->getStaffID());

            if ($role->id != 2) {
                $params = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
            } else {
                $zoomsetting = $this->zoomsetting_model->get();
                if ($zoomsetting->use_teacher_api) {
                    if ($staff['zoom_api_key'] != "" && $staff['zoom_api_secret'] != "") {
                        $api_type = "self";
                    }

                    $params = array(
                        'zoom_api_key'    => $staff['zoom_api_key'],
                        'zoom_api_secret' => $staff['zoom_api_secret'],
                    );
                } else {
                    $params = array(
                        'zoom_api_key'    => "",
                        'zoom_api_secret' => "",
                    );
                }

            }

            $this->load->library('zoom_api', $params);

            $insert_array = array(
                'staff_id'     => $this->customlib->getStaffID(),
                'title'        => $this->input->post('title'),
                'date'         => date('Y-m-d H:i:s', $this->customlib->dateTimeformat($this->input->post('date'))),
                'duration'     => $this->input->post('duration'),
                'password'     => $this->input->post('password'),
                'created_id'   => $this->customlib->getStaffID(),
                'api_type'     => $api_type,
                'host_video'   => $this->input->post('host_video'),
                'client_video' => $this->input->post('client_video'),
                'description'  => $this->input->post('description'),
                'timezone'     => $this->customlib->getTimeZone(),
            );
            $response = $this->zoom_api->createAMeeting($insert_array);

            if ($response) {
                if (isset($response->id)) {
                    $insert_array['return_response'] = json_encode($response);
                    $this->conference_model->add($insert_array, $this->input->post('section_id[]'));

                    $sender_details = array('class_section_id' => $this->input->post('section_id[]'), 'title' => $this->input->post('title'), 'date' => $this->input->post('date'), 'duration' => $this->input->post('duration'));
                    $this->zoom_mail_sms->mailsms('online_classes', $sender_details);

                    $response = array('status' => 1, 'message' => $this->lang->line('success_message'));
                } else {
                    $response = array('status' => 0, 'error' => array($response->message));
                }
            } else {
                $response = array('status' => 0, 'error' => array('Something went wrong.'));
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function delete($id, $zoom_id,$force_delete = NULL)
    {

        $result = $this->conference_model->get($id);

        if (empty($result)) {
            $this->session->set_flashdata('msg', '<div class="alert alert-error text-left">Something went wrong.</div>');
            redirect($_SERVER['HTTP_REFERER'], 'refresh');
        }

        if(!is_null($force_delete)){
            $data['title'] = 'Delete Conference';
                    $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
                    $this->conference_model->remove($id);
        }else{
    
            if ($result->api_type == 'global') {

                $params = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
            } else {
                $staff = $this->staff_model->get($this->customlib->getStaffID());
                if ($staff['zoom_api_key'] == "" && $staff['zoom_api_secret'] == "") {
                    $this->session->set_flashdata('msg', '<div class="alert alert-error text-left">You have created by your own account, API Credential not exists.</div>');
                    redirect($_SERVER['HTTP_REFERER'], 'refresh');
                }
                $params = array(
                    'zoom_api_key'    => $staff['zoom_api_key'],
                    'zoom_api_secret' => $staff['zoom_api_secret'],
                );
            }
                $this->load->library('zoom_api', $params);

                $response = $this->zoom_api->deleteMeeting($zoom_id);

                if (!empty($response)) {
                    $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $response->message . ' if you want to force delete from your application  <a href="'.site_url('admin/conference/delete/'.$id.'/'.$zoom_id.'/force_delete').'"> Click Here </a></div>');
                } else {
                    $data['title'] = 'Delete Conference';
                    $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('delete_message') . '</div>');
                    $this->conference_model->remove($id);
                }
        }

        redirect($_SERVER['HTTP_REFERER'], 'refresh');
    }

    public function addcredential()
    {
        $response = array();
        if ($this->input->post('button') == "save") {
            $this->form_validation->set_rules('zoom_api_key', $this->lang->line('zoom_api_key'), 'required|trim|xss_clean');
            $this->form_validation->set_rules('zoom_api_secret', $this->lang->line('zoom_api_secret'), 'required|trim|xss_clean');

            if ($this->form_validation->run() == false) {
                $data = array(
                    'zoom_api_key'    => form_error('zoom_api_key'),
                    'zoom_api_secret' => form_error('zoom_api_secret'),
                );
                $response = array('status' => 0, 'error' => $data);
            } else {
                $insert_array = array(
                    'id'              => $this->customlib->getStaffID(),
                    'zoom_api_key'    => $this->input->post('zoom_api_key'),
                    'zoom_api_secret' => $this->input->post('zoom_api_secret'),
                );
                $insert_id = $this->staff_model->update($insert_array);
                $response  = array('status' => 1, 'message' => $this->lang->line('success_message'));
            }
        } else {
            $insert_array = array(
                'id'              => $this->customlib->getStaffID(),
                'zoom_api_key'    => null,
                'zoom_api_secret' => null,
            );
            $insert_id = $this->staff_model->update($insert_array);
            $response  = array('status' => 1, 'message' => $this->lang->line('update_message'));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function addByOther()
    {

        $response = array();

        $this->form_validation->set_rules('title', $this->lang->line('class_title'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('class_date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('section_id[]', $this->lang->line('section'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('staff_id', $this->lang->line('staff'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('role_id', $this->lang->line('role'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('host_video', $this->lang->line('host_video'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('client_video', $this->lang->line('client_video'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('duration', $this->lang->line('class_duration_minutes'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title'        => form_error('title'),
                'date'         => form_error('date'),
                'class_id'     => form_error('class_id'),
                'section_id'   => form_error('section_id[]'),
                'staff_id'     => form_error('staff_id'),
                'role_id'      => form_error('role_id'),
                'host_video'   => form_error('host_video'),
                'client_video' => form_error('client_video'),
                'password'     => form_error('password'),
                'duration'     => form_error('duration'),
            );
            $response = array('status' => 0, 'error' => $data);
        } else {

            $api_type = 'global';

            $params = array(
                'zoom_api_key'    => "",
                'zoom_api_secret' => "",
            );
            $this->load->library('zoom_api', $params);

            $insert_array = array(
                'staff_id'     => $this->input->post('staff_id'),
                'title'        => $this->input->post('title'),
                'date'         => date('Y-m-d H:i:s', $this->customlib->dateTimeformat($this->input->post('date'))),

                'duration'     => $this->input->post('duration'),
                'password'     => $this->input->post('password'),
                'created_id'   => $this->customlib->getStaffID(),
                'api_type'     => $api_type,
                'host_video'   => $this->input->post('host_video'),
                'client_video' => $this->input->post('client_video'),
                'description'  => $this->input->post('description'),
                'timezone'     => $this->customlib->getTimeZone(),
            );
            $response = $this->zoom_api->createAMeeting($insert_array);

            if ($response) {
                if (isset($response->id)) {
                    $insert_array['return_response'] = json_encode($response);
                    $this->conference_model->add($insert_array, $this->input->post('section_id[]'));
                    $sender_details = array('class_section_id' => $this->input->post('section_id[]'), 'title' => $this->input->post('title'), 'date' => $this->input->post('date'), 'duration' => $this->input->post('duration'));
                    $this->zoom_mail_sms->mailsms('online_classes', $sender_details);
                    $response = array('status' => 1, 'message' => $this->lang->line('success_message'));
                } else {
                    $response = array('status' => 0, 'error' => array($response->message));
                }
            } else {
                $response = array('status' => 0, 'error' => array('Something went wrong.'));
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function addByClassTeacher()
    {

        $response = array();
        $this->form_validation->set_rules('title', $this->lang->line('class_title'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('class_date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('section_id[]', $this->lang->line('section'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('staff_id', $this->lang->line('staff'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('host_video', $this->lang->line('host_video'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('client_video', $this->lang->line('client_video'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('duration', $this->lang->line('class_duration_minutes'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title'        => form_error('title'),
                'date'         => form_error('date'),
                'class_id'     => form_error('class_id'),
                'section_id'   => form_error('section_id[]'),
                'staff_id'     => form_error('staff_id'),
                'host_video'   => form_error('host_video'),
                'client_video' => form_error('client_video'),
                'password'     => form_error('password'),
                'duration'     => form_error('duration'),
            );
            $response = array('status' => 0, 'error' => $data);
        } else {

            $api_type = 'global';

            $params = array(
                'zoom_api_key'    => "",
                'zoom_api_secret' => "",
            );
            $this->load->library('zoom_api', $params);

            $insert_array = array(
                'staff_id'     => $this->input->post('staff_id'),
                'title'        => $this->input->post('title'),
                'date'         => date('Y-m-d H:i:s', $this->customlib->dateTimeformat($this->input->post('date'))),

                'duration'     => $this->input->post('duration'),
                'password'     => $this->input->post('password'),
                'created_id'   => $this->customlib->getStaffID(),
                'api_type'     => $api_type,
                'host_video'   => $this->input->post('host_video'),
                'client_video' => $this->input->post('client_video'),
                'description'  => $this->input->post('description'),
                'timezone'     => $this->customlib->getTimeZone(),
            );
            $response = $this->zoom_api->createAMeeting($insert_array);

            if ($response) {
                if (isset($response->id)) {
                    $insert_array['return_response'] = json_encode($response);

                    $this->conference_model->add($insert_array, $this->input->post('section_id[]'));
                    //==============

                    $sender_details = array('class_section_id' => $this->input->post('section_id[]'), 'title' => $this->input->post('title'), 'date' => $this->input->post('date'), 'duration' => $this->input->post('duration'));
                    $this->zoom_mail_sms->mailsms('online_classes', $sender_details);

                    //================

                    $response = array('status' => 1, 'message' => $this->lang->line('success_message'));
                } else {
                    $response = array('status' => 0, 'error' => array($response->message));
                }
            } else {
                $response = array('status' => 0, 'error' => array('Something went wrong.'));
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function meeting()
    {
        if (!$this->rbac->hasPrivilege('live_meeting', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'conference');
        $this->session->set_userdata('sub_menu', 'conference/live_meeting');
        $data                       = array();
        $role                       = json_decode($this->customlib->getStaffRole());
        $data['role']               = $role;
        $data['logged_staff_id']    = $this->customlib->getStaffID();
        $data['conference_setting'] = $this->conference_setting;

        if ($role->id == 7) {
            $data['conferences'] = $this->conference_model->getStaffMeeting();
        } else {
            $data['conferences'] = $this->conference_model->getStaffMeeting($data['logged_staff_id']);
        }

        $data['staffList'] = $this->staff_model->get();
        $this->load->view('layout/header');
        $this->load->view('admin/conference/meeting', $data);
        $this->load->view('layout/footer');
    }

    public function addMeeting()
    {

        $response = array();

        $this->form_validation->set_rules('title', $this->lang->line('meeting') . ' ' . $this->lang->line('title'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('meeting') . ' ' . $this->lang->line('date'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('duration', $this->lang->line('meeting_duration_minutes'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('host_video', $this->lang->line('host_video'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('staff[]', $this->lang->line('staff'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('client_video', $this->lang->line('client_video'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title'        => form_error('title'),
                'date'         => form_error('date'),
                'staff[]'      => form_error('staff[]'),
                'host_video'   => form_error('host_video'),
                'client_video' => form_error('client_video'),
                'password'     => form_error('password'),
                'duration'     => form_error('duration'),
            );
            $response = array('status' => 0, 'error' => $data);
        } else {
            $staff       = $this->staff_model->get($this->customlib->getStaffID());
            $zoomsetting = $this->zoomsetting_model->get();
            $api_type    = 'global';

            if ($zoomsetting->use_teacher_api) {
                if ($staff['zoom_api_key'] != "" && $staff['zoom_api_secret'] != "") {
                    $api_type = "self";
                }
                $params = array(
                    'zoom_api_key'    => $staff['zoom_api_key'],
                    'zoom_api_secret' => $staff['zoom_api_secret'],
                );
            } else {
                $params = array(
                    'zoom_api_key'    => "",
                    'zoom_api_secret' => "",
                );
            }

            $this->load->library('zoom_api', $params);
            $insert_array = array(
                'title'        => $this->input->post('title'),
                'date'         => date('Y-m-d H:i:s', $this->customlib->dateTimeformat($this->input->post('date'))),
                'duration'     => $this->input->post('duration'),
                'password'     => $this->input->post('password'),
                'created_id'   => $this->customlib->getStaffID(),
                'api_type'     => $api_type,
                'host_video'   => $this->input->post('host_video'),
                'client_video' => $this->input->post('client_video'),
                'description'  => $this->input->post('description'),
                'purpose'      => 'meeting',
                'timezone'     => $this->customlib->getTimeZone(),
            );
            $response = $this->zoom_api->createAMeeting($insert_array);
            $staff    = $this->input->post('staff[]');
            if ($response) {
                if (isset($response->id)) {
                    $insert_array['return_response'] = json_encode($response);
                    $this->conference_model->addmeeting($insert_array, $staff);

                    $staff_mail_sms_list = $this->conference_model->getAllStaffByArray($staff);
                    if (!empty($staff_mail_sms_list)) {
                        $sender_details = array();
                        foreach ($staff_mail_sms_list as $staff_mail_sms_list_key => $staff_mail_sms_list_value) {
                            $sender_details[] = array(
                                'title'       => $this->input->post('title'),
                                'date'        => $this->input->post('date'),
                                'duration'    => $this->input->post('duration'),
                                'employee_id' => $staff_mail_sms_list_value->employee_id,
                                'department'  => $staff_mail_sms_list_value->department,
                                'designation' => $staff_mail_sms_list_value->designation,
                                'name'        => ($staff_mail_sms_list_value->surname == "") ? $staff_mail_sms_list_value->name : $staff_mail_sms_list_value->name . " " . $staff_mail_sms_list_value->surname,
                                'contact_no'  => $staff_mail_sms_list_value->contact_no,
                                'email'       => $staff_mail_sms_list_value->email,
                            );
                        }

                        $this->zoom_mail_sms->mailsms('online_meeting', $sender_details);
                    }

                    $response = array('status' => 1, 'message' => $this->lang->line('success_message'));
                } else {
                    $response = array('status' => 0, 'error' => array($response->message));
                }
            } else {
                $response = array('status' => 0, 'error' => array('Something went wrong.'));
            }
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function getcredential()
    {
        $response                    = array();
        $staff                       = $this->staff_model->get($this->customlib->getStaffID());
        $response['zoom_api_key']    = $staff['zoom_api_key'];
        $response['zoom_api_secret'] = $staff['zoom_api_secret'];

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function chgstatus()
    {
        $response = array();

        $this->form_validation->set_rules('conference_id', $this->lang->line('zoom_api_key'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('chg_status', $this->lang->line('zoom_api_secret'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'conference_id' => form_error('conference_id'),
                'chg_status'    => form_error('chg_status'),
            );
            $response = array('status' => 0, 'error' => $data);
        } else {
            $insert_array = array(
                'status' => $this->input->post('chg_status'),
            );
            $insert_id = $this->conference_model->update($this->input->post('conference_id'), $insert_array);
            $response  = array('status' => 1, 'message' => $this->lang->line('update_message'));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($response));
    }

    public function meeting_report()
    {
        if (!$this->rbac->hasPrivilege('live_meeting_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'conference');
        $this->session->set_userdata('sub_menu', 'conference/meeting_report');
        $data                    = array();
        $staff_id                = $this->customlib->getStaffID();
        $data['logged_staff_id'] = $staff_id;

        $data['meetingList'] = $this->conferencehistory_model->getmeeting();
        $this->load->view('layout/header');
        $this->load->view('admin/conference/meeting_report', $data);
        $this->load->view('layout/footer');
    }

    public function class_report()
    {
        if (!$this->rbac->hasPrivilege('live_classes_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'conference');
        $this->session->set_userdata('sub_menu', 'conference/class_report');
        $data['title'] = 'Class Report';

        $class = $this->class_model->get();

        $data['classlist']       = $class;
        $staff_id                = $this->customlib->getStaffID();
        $data['logged_staff_id'] = $staff_id;
        $this->form_validation->set_rules('class_id', $this->lang->line('class'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', $this->lang->line('section'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

        } else {
            $class_id              = $this->input->post('class_id');
            $section_id            = $this->input->post('section_id');
            $data['class_id']      = $class_id;
            $data['section_id']    = $section_id;
            $data['liveclassList'] = $this->conferencehistory_model->getclass($class_id, $section_id);
        }
        $this->load->view('layout/header', $data);
        $this->load->view('admin/conference/class_report', $data);
        $this->load->view('layout/footer', $data);
    }

    public function add_history()
    {

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'id' => form_error('id'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $conference_id = $this->input->post('id');
            $live          = $this->conference_model->get($conference_id);
            $staff_id      = $this->customlib->getStaffID();
            if ($live->created_id != $staff_id) {
                $data_insert = array(
                    'conference_id' => $conference_id,
                    'staff_id'      => $staff_id,
                );
                $this->conferencehistory_model->updatehistory($data_insert, 'staff');
            }

            $array = array('status' => 1, 'error' => '');
            echo json_encode($array);
        }
    } 

    public function getViewerList()
    {
        $recordid     = $this->input->post('recordid');
        $type         = $this->input->post('type');
        $data['type'] = 'staff';
        if (isset($type)) {
            $data['type']         = $type;
            $class_id             = $this->input->post('class_id');
            $section_id           = $this->input->post('section_id');
            $data['viewerDetail'] = $this->conferencehistory_model->getLiveStudent($recordid, $class_id, $section_id);
        } else {

            $data['viewerDetail'] = $this->conferencehistory_model->getMeetingStaff($recordid);
        }
        $data['sch_setting'] = $this->sch_setting_detail;
   
        $data['page'] = $this->load->view('admin/conference/_partialviewerlist', $data, true);

        echo json_encode($data);
    }
 
    public function join($type, $id)
    {
        
        $zoom_api_key    = "";
        $zoom_api_secret = "";
        $live = $this->conference_model->get($id);
     
         if ($type == "class") {

            $leaveUrl = "admin/conference/timetable";
            $class_section=$this->conference_model->getClassSectionByConferenceID($id);
            foreach ($class_section as $key => $value) {
                 $sender_details = array(
                'class_section_id' => $value->cls_section_id,
                 'title' => $live->title,
                  'date' => $live->date,
                  'duration' => $live->duration
              );

            $this->zoom_mail_sms->mailsms('zoom_online_classes_start', $sender_details);
            }
           
        } elseif ($type == "meeting") {
            $stafflist=$this->conference_model->getStaffbyConferenceId($id);
            $staff_mail_sms_list = $this->conference_model->getAllStaffByArray($stafflist);
                    if (!empty($staff_mail_sms_list)) {
                        $sender_details = array();
                        foreach ($staff_mail_sms_list as $staff_mail_sms_list_key => $staff_mail_sms_list_value) {
                            $sender_details[] = array(
                                'title'       => $live->title,
                                'date'        => $live->date,
                                'duration'    => $live->duration,
                                'employee_id' => $staff_mail_sms_list_value->employee_id,
                                'department'  => $staff_mail_sms_list_value->department,
                                'designation' => $staff_mail_sms_list_value->designation,
                                'name'        => ($staff_mail_sms_list_value->surname == "") ? $staff_mail_sms_list_value->name : $staff_mail_sms_list_value->name . " " . $staff_mail_sms_list_value->surname,
                                'contact_no'  => $staff_mail_sms_list_value->contact_no,
                                'email'       => $staff_mail_sms_list_value->email,
                            );
                        }

                        $this->zoom_mail_sms->mailsms('zoom_online_meeting_start', $sender_details);
                    }
                  
            $leaveUrl = "admin/conference/meeting";
           
        } 
        if ($live->api_type == "global") {
            $zoomsetting = $this->zoomsetting_model->get();
            if (!empty($zoomsetting)) {

                $zoom_api_key    = $zoomsetting->zoom_api_key;
                $zoom_api_secret = $zoomsetting->zoom_api_secret;
            }
        } else {
            $staff           = $this->staff_model->get($live->created_id);
            $zoom_api_key    = $staff['zoom_api_key'];
            $zoom_api_secret = $staff['zoom_api_secret'];

        }

        $meetingID                = json_decode($live->return_response)->id;
        $data['zoom_api_key']     = $zoom_api_key;
        $data['zoom_api_secret']  = $zoom_api_secret;
        $data['meetingID']        = $meetingID;
        $data['meeting_password'] = $live->password;
        $data['leaveUrl']         = $leaveUrl;
        $data['title']            = $live->title;
        if ($type == "meeting") {
            $data['host'] = ($live->create_by_surname == "") ? $live->create_by_name : $live->create_by_name . " " . $live->create_by_surname;
            $staff_id     = $this->customlib->getStaffID();
            if ($live->created_id != $staff_id) {
                $data_insert = array(
                    'conference_id' => $id,
                    'staff_id'      => $staff_id,
                );
                $this->conferencehistory_model->updatehistory($data_insert, 'staff');
            }

        } elseif ($type == "class") {

            $data['host'] = ($live->create_for_surname == "") ? $live->create_for_name : $live->create_for_name . " " . $live->create_for_surname;
        }
        $data['name'] = $this->customlib->getAdminSessionUserName();
       
        $this->load->view('admin/conference/join', $data);
    }

    public function getlivestatus()
    {

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'id' => form_error('id'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $conference_id = $this->input->post('id');
            $live          = $this->conference_model->get($conference_id);
            //========
            $data['conference_setting'] = $this->conference_setting;
            if ($live->api_type == "global") {
                $zoomsetting = $this->zoomsetting_model->get();
                if (!empty($zoomsetting)) {

                    $zoom_api_key    = $zoomsetting->zoom_api_key;
                    $zoom_api_secret = $zoomsetting->zoom_api_secret;
                }
            } else {
                $staff           = $this->staff_model->get($live->created_id);
                $zoom_api_key    = $staff['zoom_api_key'];
                $zoom_api_secret = $staff['zoom_api_secret'];

            }
            $params = array(
                'zoom_api_key'    => $zoom_api_key,
                'zoom_api_secret' => $zoom_api_secret,
            );
            $this->load->library('zoom_api', $params);
            $meetingID               = json_decode($live->return_response)->id;
            $api_Response            = $this->zoom_api->getMeeting($meetingID);
            $data['api_Response']    = $api_Response;
            $staff_id                = $this->customlib->getStaffID();
            $data['logged_staff_id'] = $staff_id;
            $data['live']            = $live;
            $data['live_url']        = json_decode($live->return_response);
            $data['page']            = $this->load->view('admin/conference/_livestatus', $data, true);
            $array                   = array('status' => '1', 'page' => $data['page']);
            echo json_encode($data);
           

        }
    }

}
