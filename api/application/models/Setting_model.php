<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Setting_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null)
    {
        $this->db->select('sch_settings.id,sch_settings.lang_id,sch_settings.class_teacher,sch_settings.is_rtl,sch_settings.cron_secret_key, sch_settings.timezone,sch_settings.attendence_type,sch_settings.superadmin_restriction,
          sch_settings.name,sch_settings.email,sch_settings.phone,languages.language,languages.short_code,
          sch_settings.address,sch_settings.dise_code,sch_settings.date_format,sch_settings.currency,sch_settings.currency_symbol,sch_settings.start_month,sch_settings.session_id,sch_settings.start_week,sch_settings.lastname,sch_settings.middlename,sch_settings.fee_due_days,sch_settings.image,sch_settings.theme,sessions.session,sch_settings.student_login,sch_settings.parent_login,currencies.symbol as currency_symbol,currencies.base_price,currencies.short_name,currencies.short_name as currency_short_name,'
        );
        $this->db->from('sch_settings');
        $this->db->join('sessions', 'sessions.id = sch_settings.session_id');
        $this->db->join('languages', 'languages.id = sch_settings.lang_id');
        $this->db->join('currencies', 'currencies.id = sch_settings.currency');
        if ($id != null) {
            $this->db->where('sch_settings.id', $id);
        } else {
            $this->db->order_by('sch_settings.id');
        }
        $query = $this->db->get();

        if ($id != null) {
            return $query->row_array();
        } else {
            $session_array                = $this->session->has_userdata('session_array');
            $result                       = $query->result_array();
            $result[0]['current_session'] = array(
                'session_id' => $result[0]['session_id'],
                'session'    => $result[0]['session'],
            );

            if ($session_array) {
                $session_array           = $this->session->userdata('session_array');
                $result[0]['session_id'] = $session_array['session_id'];
                $result[0]['session']    = $session_array['session'];
            }

            return $result;
        }
    }

    public function getSchoolDetail($id = null)
    {
        $this->db->select('sch_settings.id,sch_settings.lang_id,sch_settings.is_rtl,sch_settings.timezone,
          sch_settings.name,sch_settings.email,sch_settings.phone,languages.language,sch_settings.attendence_type,
          sch_settings.address,sch_settings.dise_code,sch_settings.date_format,sch_settings.currency,sch_settings.currency_symbol,sch_settings.start_month,sch_settings.session_id,sch_settings.image,sch_settings.theme,sessions.session,student_profile_edit,sch_settings.is_student_feature_lock,sch_settings.lock_grace_period'
        );
        $this->db->from('sch_settings');
        $this->db->join('sessions', 'sessions.id = sch_settings.session_id');
        $this->db->join('languages', 'languages.id = sch_settings.lang_id');
        $this->db->order_by('sch_settings.id');
        $query = $this->db->get();
        return $query->row();
    }

    public function getSetting()
    {
        $this->db->select('sch_settings.id,sch_settings.attendence_type,sch_settings.lang_id,sch_settings.is_rtl,sch_settings.fee_due_days,sch_settings.class_teacher,sch_settings.cron_secret_key,sch_settings.timezone,
          sch_settings.name,sch_settings.email,sch_settings.biometric,sch_settings.biometric_device,sch_settings.phone,sch_settings.adm_prefix,sch_settings.adm_start_from,languages.language,sch_settings.adm_no_digit,sch_settings.adm_update_status,sch_settings.adm_auto_insert,sch_settings.staffid_prefix,sch_settings.staffid_start_from,sch_settings.staffid_auto_insert,sch_settings.staffid_no_digit,sch_settings.staffid_update_status,
          sch_settings.address,sch_settings.dise_code,sch_settings.date_format,sch_settings.currency,sch_settings.currency_place,sch_settings.currency_symbol,sch_settings.start_month,sch_settings.session_id,sch_settings.image,sch_settings.theme,sessions.session,online_admission,sch_settings.is_duplicate_fees_invoice,sch_settings.is_student_house,sch_settings.is_blood_group,sch_settings.roll_no,sch_settings.middlename,sch_settings.lastname,sch_settings.category,sch_settings.cast,sch_settings.religion,sch_settings.mobile_no,sch_settings.student_email,sch_settings.admission_date,sch_settings.student_photo,sch_settings.student_height,sch_settings.student_weight,sch_settings.measurement_date,sch_settings.father_name,sch_settings.father_phone,sch_settings.father_occupation,sch_settings.father_pic,sch_settings.mother_name,sch_settings.mother_phone,sch_settings.mother_occupation,sch_settings.mother_pic,sch_settings.guardian_relation,sch_settings.guardian_email,sch_settings.guardian_pic,sch_settings.guardian_address,sch_settings.current_address,sch_settings.permanent_address,sch_settings.route_list,sch_settings.hostel_id,sch_settings.bank_account_no,sch_settings.national_identification_no,sch_settings.local_identification_no,sch_settings.rte,sch_settings.previous_school_details,sch_settings.student_note,sch_settings.upload_documents,sch_settings.staff_designation,sch_settings.staff_department,sch_settings.staff_last_name,sch_settings.staff_father_name,sch_settings.staff_mother_name,sch_settings.staff_date_of_joining,sch_settings.staff_phone,sch_settings.staff_emergency_contact,sch_settings.staff_marital_status,sch_settings.staff_photo,sch_settings.staff_current_address,sch_settings.staff_permanent_address,sch_settings.staff_qualification,sch_settings.staff_work_experience,sch_settings.staff_note,sch_settings.staff_epf_no,sch_settings.staff_basic_salary,sch_settings.staff_contract_type,sch_settings.staff_work_shift,sch_settings.staff_work_location,sch_settings.staff_leaves,sch_settings.staff_account_details,sch_settings.staff_social_media,sch_settings.staff_upload_documents,sch_settings.admin_logo,sch_settings.admin_small_logo,sch_settings.mobile_api_url,sch_settings.app_primary_color_code,sch_settings.app_secondary_color_code,sch_settings.app_logo,languages.short_code as `language_code`,sch_settings.student_profile_edit,sch_settings.currency_format,student_panel_login,parent_panel_login,sch_settings.maintenance_mode,sch_settings.student_timeline,sch_settings.is_offline_fee_payment'); 
        $this->db->from('sch_settings');
        $this->db->join('sessions', 'sessions.id = sch_settings.session_id');
        $this->db->join('languages', 'languages.id = sch_settings.lang_id');
        $this->db->order_by('sch_settings.id');
        $query = $this->db->get();
        return $query->row();
    }

    public function getSchoolDisplay()
    {
        $this->db->select('
          sch_settings.name,sch_settings.dise_code,sch_settings.email,sch_settings.phone,sch_settings.address,sch_settings.start_month,sch_settings.image,sessions.session');
        $this->db->from('sch_settings');
        $this->db->join('sessions', 'sessions.id = sch_settings.session_id');
        $this->db->join('languages', 'languages.id = sch_settings.lang_id');
        $this->db->order_by('sch_settings.id');
        $query = $this->db->get();
        return $query->row();
    }

    public function getCurrentSession()
    {
        $session_result = $this->get();
        return $session_result[0]['session_id'];
    }

    public function getCurrentSessionName()
    {
        $session_result = $this->get();
        return $session_result[0]['session'];
    }

    public function getCurrentSchoolName()
    {
        $session_result = $this->get();
        return $session_result[0]['name'];
    }

    public function getStartMonth()
    {
        $session_result = $this->get();
        return $session_result[0]['start_month'];
    }

    public function getCurrentSessiondata()
    {
        $session_result = $this->get();
        return $session_result[0];
    }

    public function getCurrency()
    {
        $session_result = $this->get();
        return $session_result[0]['currency'];
    }

    public function getCurrencySymbol()
    {
        $session_result = $this->get();
        return $session_result[0]['currency_symbol'];
    }

    public function getDateYmd()
    {
        return date('Y-m-d');
    }

    public function getDateDmy()
    {
        return date('d-m-Y');
    }

    public function add_cronsecretkey($data, $id)
    {
        $this->db->where("id", $id)->update("sch_settings", $data);
    }

    public function student_fields()
    {
        $fields = explode(',', 'blood_group,student_house,roll_no,category,religion,cast,mobile_no,student_email,admission_date,lastname,student_photo,student_height,student_weight,measurement_date,father_name,father_phone,father_occupation,father_pic,mother_name,mother_phone,mother_occupation,mother_pic,guardian_relation,guardian_email,guardian_pic,guardian_address,current_address,permanent_address,route_list,hostel_id,bank_account_no,national_identification_no,local_identification_no,rte,previous_school,guardian_name,guardian_phone,guardian_occupation,bank_name,ifsc_code');
        $this->db->select('is_blood_group as `blood_group`,is_student_house as `student_house`,roll_no,category,religion,cast,mobile_no,student_email,admission_date,lastname,student_photo,student_height,student_weight,measurement_date,father_name,father_phone,father_occupation,father_pic,mother_name,mother_phone,mother_occupation,mother_pic,guardian_relation,guardian_email,guardian_pic,guardian_address,current_address,permanent_address,route_list,hostel_id,bank_account_no,national_identification_no,local_identification_no,rte,previous_school_details as previous_school,guardian_name,guardian_phone,guardian_occupation,bank_name,ifsc_code');
        $this->db->from('sch_settings');
        $this->db->order_by('sch_settings.id');
        $query      = $this->db->get();
        $result     = $query->row();
        $new_object = array();
        foreach ($fields as $key => $value) {
            if ($result->{$value}) {
                $new_object[$value] = 1;
            }
        }
        return $new_object;
    }

    public function getAdminsmalllogo()
    {
        $query = $this->db->select('admin_small_logo')->get('sch_settings');
        $logo  = $query->row_array();
        echo $logo['admin_small_logo'];
    }

    public function getTemplate($type)
    {
        $this->db->select()->from('notification_setting');
        $this->db->where('notification_setting.type', $type);
        $query = $this->db->get();
        return $query->row();
    }
    
    function get_currency_list(){

        $CI = &get_instance();
        $CI->db->select('currencies.*,IFNULL(sch_settings.currency, 0) as `currency_id`')->from('currencies');
        $CI->db->join('sch_settings','currencies.id=sch_settings.currency','left');
        $CI->db->where('currencies.is_active',1);
        $CI->db->order_by('id');   
        $query = $CI->db->get();
        return $query->result();

    }   
    
}
