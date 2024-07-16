<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Customlib {

    // api ver: 3.5

    public $CI;

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->model('Setting_model');
    }
    
    public function getSchoolCurrencyPrice()
    {
       
            $student = $this->CI->session->userdata('student_currency');
            return $student['currency_base_price'];

       
    }

    public function getCurrencySymbol()
    {
           
            $student = $this->CI->session->userdata('student_currency');
            
            return $student['currency_symbol'];

       
    }
    
    
    public function getMonthList($month = 0) {
        $months = array(
            0 => '',
            1 => 'january',
            2 => 'february',
            3 => 'march',
            4 => 'april',
            5 => 'may',
            6 => 'june',
            7 => 'july',
            8 => 'august',
            9 => 'september',
            10 => 'october',
            11 => 'november',
            12 => 'decmber');

        return $months[$month];
    }

    public function getDaysname() {
        $status = array();
        $status['Monday'] = 'Monday';
        $status['Tuesday'] = 'Tuesday';
        $status['Wednesday'] = 'Wednesday';
        $status['Thursday'] = 'Thursday';
        $status['Friday'] = 'Friday';
        $status['Saturday'] = 'Saturday';
        $status['Sunday'] = 'Sunday';
        return $status;
    }

    public function getSchoolName() {
        $admin = $this->CI->Setting_model->getSetting();
        return $admin->name;
    }

    public function getGender() {
        $gender = array();
        $gender['Male'] = 'male';
        $gender['Female'] = 'female';
        return $gender;
    } 

     public function getFullName($firstname, $middlename, $lastname, $is_middlename,$is_lastname)
    {
        $name="";
        if ($is_middlename) {
            $name= ($middlename == "") ? $firstname : $firstname . " " . $middlename;
        } else {
            $name= $firstname;
        }

       if ($is_lastname) {
            $name= ($lastname == "") ? $name : $name . " " . $lastname;
        } 

        return $name;
    }

    public function getCourseThumbnailPath($course_thumbnail){
       $web_path= trim(base_url(), "api/");
       return $web_path."/uploads/course/course_thumbnail/".$course_thumbnail;

    }
    
    public function uniqueFileName($prefix = "", $name = "")
    {
        if (!empty($_FILES)) {
            $newFileName = uniqid($prefix, true) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION));
            return $newFileName;
        }
        return false;
    }
    
    public function getCurrencyFormat()
    {
        $admin = $this->CI->Setting_model->getSetting();

        return $admin->currency_format;             
             
        
    }
    
  
    

}
