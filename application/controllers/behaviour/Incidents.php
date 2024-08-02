<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Incidents extends MY_Addon_BRController {

    function __construct() {
        parent::__construct();
    }

    /*
    This function is used load incident list
    */
    public function index() {
        
        if (!$this->rbac->hasPrivilege('behaviour_records_incident', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'behaviour');
        $this->session->set_userdata('sub_menu', 'behaviour/incidents');
        $this->load->view('layout/header');
        $this->load->view('behaviour/incidents/incidentlist');
        $this->load->view('layout/footer');
    }

    /*
    This function is used to insert incident
    */
    public function create()
    {
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('point', $this->lang->line('point'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {

            $msg = array(
                'title'              => form_error('title'),
                'point'              => form_error('point'),
                'description'         => form_error('description'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');

        } else {
            $negative_incident =   $this->input->post('negative_incident');
            if($negative_incident == 1){
                $point = '-'.$this->input->post("point");
            }else{
                $point = $this->input->post("point");
            }
            $data = array(
                'title'         => $this->input->post("title"),
                'point'         => $point,
                'description'   => $this->input->post("description"),
            );

            $this->studentbehaviour_model->add($data);

            $msg   = $this->lang->line('success_message');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }

        echo json_encode($array);
    }

    /*
    This function is used to show incident record on datatable
    */
    public function dtincident()
    {
        $incidentlist            = $this->studentbehaviour_model->incident();
        $incidentlist            = json_decode($incidentlist);
        $dt_data=array();
        $editbtn = '';
        $deletebtn = '';
        if (!empty($incidentlist->data)) {
            foreach ($incidentlist->data as $key => $value) {
                if ($this->rbac->hasPrivilege('behaviour_records_incident', 'can_edit')) {
                    $editbtn = "<a  class='btn btn-default btn-xs editincidentmodel'  data-toggle='tooltip'   data-method_call='edit' data-original-title='" . $this->lang->line('edit') . "' data-record_id=".$value->id." ><i class='fa fa-pencil'></i></a>";
                }
                if ($this->rbac->hasPrivilege('behaviour_records_incident', 'can_delete')) {
                    $deletebtn = "<a href='#' data-record_id=".$value->id." class='btn btn-default btn-xs deletebtn'  data-toggle='tooltip'  title='" . $this->lang->line('delete') . "' data-original-title='" . $this->lang->line('delete') . "'><i class='fa fa-remove'></i></a>";
                }
                
                $row   = array();
                $row[] = $value->title;
                $row[] = $value->point;
                $row[] = $value->description;
                $row[] = $editbtn.''.$deletebtn;
                $dt_data[] = $row;  
            }

        }
        $json_data = array(
            "draw"            => intval($incidentlist->draw),
            "recordsTotal"    => intval($incidentlist->recordsTotal),
            "recordsFiltered" => intval($incidentlist->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data); 
    }

    /*
    This function is used to delete incident record 
    */
    public function delete() {
        $id = $this->input->post('incidentid');
        $this->studentbehaviour_model->delete($id);
        echo json_encode(array('status' => 1, 'message' => $this->lang->line('delete_message')));
    }

    /*
    This function is used to get single incident record 
    */
    public function get() {
        $incidentid = $this->input->post('incidentid');
        $incidentlist   = $this->studentbehaviour_model->get($incidentid);        
        $pointcheck = explode("-", $incidentlist['point']);    
       
        if(isset($pointcheck[1])){
            $negative_incident = 1;
            $point = $pointcheck[1];            
        }else{
            $negative_incident = "";
            $point = $pointcheck[0];            
        }
        
        $incidentlist['point'] = $point;
        $incidentlist['negative_incident'] = $negative_incident; 
        $data['incidentlist'] = $incidentlist;
        $page = $this->load->view('behaviour/incidents/_incidentedit', $data, true);
        echo json_encode(array('page' => $page, 'status' => 1)); 
    }

    /*
    This function is used to edit incident record 
    */
    public function edit()
    {
        $this->form_validation->set_rules('title', $this->lang->line('title'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('point', $this->lang->line('point'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('description', $this->lang->line('description'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {

            $msg = array(
                'title'              => form_error('title'),
                'point'              => form_error('point'),
                'description'         => form_error('description'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');

        } else {
            $negative_incident =   $this->input->post('negative_incident');
            if($negative_incident == 1){
                $point = '-'.$this->input->post("point");
            }else{
                $point = $this->input->post("point");
            }
            
            $data = array(
                'id'            => $this->input->post("incident_id"),
                'title'         => $this->input->post("title"),
                'point'         => $point,
                'description'   => $this->input->post("description"),
            );

            $this->studentbehaviour_model->add($data);

            $msg   = $this->lang->line('update_message');
            $array = array('status' => 'success', 'error' => '', 'message' => $msg);
        }

        echo json_encode($array);
    }
}