<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class MY_Addon_BRController extends Admin_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->load->config('behaviour-report-config');
        $this->load->library('behaviour_mail_sms');
        $this->load->model(array("studentbehaviour_model","studentincidents_model","studentincidents_model","studentincidents_model"));
        /**
        if ($this->uri->segment(1) == "behaviour" && ($this->router->fetch_class() != "setting" xor $this->router->fetch_method() != "index")) {

            $this->auth->addonchk('ssbr', site_url('behaviour/setting/index'));

        }elseif ($this->uri->segment(1) != "behaviour") {

             redirect('admin/unauthorized');
        }
        */

    }

}
