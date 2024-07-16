<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Addon_MBController extends Admin_Controller
{

    public function __construct()
    {

        parent::__construct();
        $this->load->config('multibranch-config');
        $this->load->model("multibranch/multibranch_model");
     
        if ( $this->router->fetch_class() != "branch" ) {
          $this->auth->addonchk('ssmb', site_url('admin/multibranch/branch')); 
        }elseif ( $this->router->fetch_class() == "branch"  && $this->router->fetch_method() != "index") {
           $this->auth->addonchk('ssmb', site_url('admin/multibranch/branch'));
        }

    }

}
