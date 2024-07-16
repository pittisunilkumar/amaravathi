<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Notification_lib {

    // api ver: 3.0

    public $CI;

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->model('Setting_model');
        $this->CI->load->model('Notificationsetting_model');
    }

  public function sendMailSMS($find)
    {

        $notifications = $this->CI->Notificationsetting_model->get();

        if (!empty($notifications)) {
            foreach ($notifications as $note_key => $note_value) {
                if ($note_value->type == $find) {
                    return array('mail' => $note_value->is_mail, 'sms' => $note_value->is_sms, 'notification' => $note_value->is_notification, 'template' => $note_value->template, 'template_id' => $note_value->template_id, 'subject' => $note_value->subject);
                }
            }
        }
        return false;
    }

}
