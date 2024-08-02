<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Behaviour_mail_sms {

    public function __construct() {
        $this->CI = &get_instance();
        $this->CI->load->library('mailer');
        $this->config_mailsms = $this->CI->config->item('mailsms');
    }

    public function mailsms($send_for, $sender_details) {

        $chk_mail_sms = $this->CI->customlib->sendMailSMS($send_for);
       
        if (!empty($chk_mail_sms)) {
 
            if ($send_for == "behaviour_incident_assigned") {                
             
                $this->sendBehaviourIncidentAssigned($chk_mail_sms, $sender_details, $chk_mail_sms['template'],$chk_mail_sms['template_id'], $chk_mail_sms['subject']);
            }  
        }
    }

    public function sendBehaviourIncidentAssigned($chk_mail_sms, $student_details, $template, $template_id, $subject) {
      
        $student_guardian_sms_list = array();
        $student_sms_list = array();
        $student_email_list = array();
        $student_guardian_email_list = array();
        $student_notification_list = array();
        $student_guardian_notification_list = array();
        $sms_detail = $this->CI->smsconfig_model->getActiveSMS();

        if ($chk_mail_sms['mail'] or $chk_mail_sms['sms'] or $chk_mail_sms['notification']) {             

            if (!empty($student_details)) {                 

                    if ($student_details['parent_app_key'] != "") {
                        $student_guardian_notification_list[] = array(                        
                            'app_key' => $student_details['parent_app_key'],                            
                            'incident_title' => $student_details['incident_title'], 
                            'incident_point' => $student_details['incident_point'],
                            'mobileno' => $student_details['mobileno'],
                            'email' => $student_details['email'] ,
                            'guardian_name' => $student_details['guardian_name'],
                            'guardian_phone' => $student_details['guardian_phone'], 
                            'guardian_email' => $student_details['guardian_email'],                          
                            'class' => $student_details['class'],
                            'section' => $student_details['section'],
                            'admission_no' => $student_details['admission_no'],
                            'student_name' => $student_details['student_name'],
                        );
                    }

                    if ($student_details['app_key'] != "") {
                        $student_notification_list[] = array(
                            'app_key' => $student_details['app_key'],
                            'incident_title' => $student_details['incident_title'], 
                            'incident_point' => $student_details['incident_point'],
                            'mobileno' => $student_details['mobileno'],
                            'email' => $student_details['email'] ,
                            'guardian_name' => $student_details['guardian_name'],
                            'guardian_phone' => $student_details['guardian_phone'], 
                            'guardian_email' => $student_details['guardian_email'],                          
                            'class' => $student_details['class'],
                            'section' => $student_details['section'],
                            'admission_no' => $student_details['admission_no'],
                            'student_name' => $student_details['student_name'],
                        );
                    }

                    if ($student_details['email'] != "") {
                        $student_email_list[$student_details['email']] = array(
                            'incident_title' => $student_details['incident_title'], 
                            'incident_point' => $student_details['incident_point'],
                            'mobileno' => $student_details['mobileno'],
                            'email' => $student_details['email'] ,
                            'guardian_name' => $student_details['guardian_name'],
                            'guardian_phone' => $student_details['guardian_phone'], 
                            'guardian_email' => $student_details['guardian_email'],                          
                            'class' => $student_details['class'],
                            'section' => $student_details['section'],
                            'admission_no' => $student_details['admission_no'],
                            'student_name' => $student_details['student_name'],
                        );
                    }
                    
                    if ($student_details['guardian_email'] != "") {
                        $student_guardian_email_list[$student_details['guardian_email']] = array(
                            'incident_title' => $student_details['incident_title'], 
                            'incident_point' => $student_details['incident_point'],
                            'mobileno' => $student_details['mobileno'],
                            'email' => $student_details['email'] ,
                            'guardian_name' => $student_details['guardian_name'],
                            'guardian_phone' => $student_details['guardian_phone'], 
                            'guardian_email' => $student_details['guardian_email'],                          
                            'class' => $student_details['class'],
                            'section' => $student_details['section'], 
                            'admission_no' => $student_details['admission_no'],
                            'student_name' => $student_details['student_name'],
                        );
                    }

                    if ($student_details['mobileno'] != "") {
                        $student_sms_list[$student_details['mobileno']] = array(
                            'incident_title' => $student_details['incident_title'], 
                            'incident_point' => $student_details['incident_point'],
                            'mobileno' => $student_details['mobileno'],
                            'email' => $student_details['email'] ,
                            'guardian_name' => $student_details['guardian_name'],
                            'guardian_phone' => $student_details['guardian_phone'], 
                            'guardian_email' => $student_details['guardian_email'],                          
                            'class' => $student_details['class'],
                            'section' => $student_details['section'],
                            'admission_no' => $student_details['admission_no'],
                            'student_name' => $student_details['student_name'],
                        );
                    }

                    if ($student_details['guardian_phone'] != "") {
                        $student_guardian_sms_list[$student_details['guardian_phone']] = array(
                            'incident_title' => $student_details['incident_title'], 
                            'incident_point' => $student_details['incident_point'],
                            'mobileno' => $student_details['mobileno'],
                            'email' => $student_details['email'] ,
                            'guardian_name' => $student_details['guardian_name'],
                            'guardian_phone' => $student_details['guardian_phone'], 
                            'guardian_email' => $student_details['guardian_email'],                          
                            'class' => $student_details['class'],
                            'section' => $student_details['section'],                             
                            'admission_no' => $student_details['admission_no'],
                            'student_name' => $student_details['student_name'],
                        );
                    }                
                
                if (!empty($student_guardian_notification_list)) {
                    if($chk_mail_sms['notification']){
                        $this->sentBehaviourIncidentAssignedNotification($student_guardian_notification_list, $template, $subject);
                    }
                }
                if (!empty($student_notification_list)) {
                    if($chk_mail_sms['notification']){
                        $this->sentBehaviourIncidentAssignedNotification($student_notification_list, $template, $subject);
                    }                    
                }
                if ($student_email_list) {
                    if($chk_mail_sms['mail']){
                        $this->sentBehaviourIncidentAssignedMail($student_email_list, $template, $subject);
                    }
                }
                if ($student_guardian_email_list) {
                    if($chk_mail_sms['mail']){
                        $this->sentBehaviourIncidentAssignedMail($student_guardian_email_list, $template, $subject);
                    }
                }             
                if ($student_sms_list) {                   
                    if($chk_mail_sms['sms'] && !empty($sms_detail) ){                       
                        $this->sentBehaviourIncidentAssignedSMS($student_sms_list, $template,$template_id);
                    }
                }
                if ($student_guardian_sms_list) {                    
                    if($chk_mail_sms['sms'] && !empty($sms_detail)){
                        $this->sentBehaviourIncidentAssignedSMS($student_guardian_sms_list, $template,$template_id);
                    }
                }              
            }
        }
    }
    
    public function sentBehaviourIncidentAssignedNotification($detail, $template, $subject) {
        $this->CI->load->library('pushnotification');
        foreach ($detail as $student_key => $student_value) {
            $msg = $this->getBehaviourIncidentAssignedContent($detail[$student_key], $template);

            $push_array = array(
                'title' => $subject,
                'body' => $msg,
            );

            if ($student_value['app_key'] != "") {
                $this->CI->pushnotification->send($student_value['app_key'], $push_array, "mail_sms");
            }
        }
    }
    
    public function sentBehaviourIncidentAssignedMail($detail, $template, $subject) {

        if (!empty($this->CI->mail_config)) {
            foreach ($detail as $student_key => $student_value) {
                $send_to = $student_key;
                if ($send_to != "") {
                    $msg = $this->getBehaviourIncidentAssignedContent($detail[$student_key], $template);

                    $subject = $subject;
                    $this->CI->mailer->send_mail($send_to, $subject, $msg);
                }
            }
        }
    }    

    public function sentBehaviourIncidentAssignedSMS($detail, $template, $template_id) {

        $sms_detail = $this->CI->smsconfig_model->getActiveSMS();
        if (!empty($sms_detail)) {

            foreach ($detail as $student_key => $student_value) {
                $send_to = $student_key;
                if ($send_to != "") {
                    $msg = $this->getBehaviourIncidentAssignedContent($detail[$student_key], $template,$sms_detail->type);
                  
                    $subject = "Online Class";
                    if ($sms_detail->type == 'clickatell') {
                        $params = array(
                            'apiToken' => $sms_detail->api_id,
                        );
                        $this->CI->load->library('clickatell', $params);

                        try {
                            $result = $this->CI->clickatell->sendMessage(['to' => [$send_to], 'content' => $msg]);
                            foreach ($result['messages'] as $message) {
                                
                            }
                            return true;
                        } catch (Exception $e) {
                            return false;
                        }
                    } else if ($sms_detail->type == 'twilio') {

                        $params = array(
                            'mode' => 'sandbox',
                            'account_sid' => $sms_detail->api_id,
                            'auth_token' => $sms_detail->password,
                            'api_version' => '2010-04-01',
                            'number' => $sms_detail->contact,
                        );

                        $this->CI->load->library('twilio', $params);

                        $from = $sms_detail->contact;
                        $to = $send_to;
                        $message = $msg;
                        $response = $this->CI->twilio->sms($from, $to, $message);

                        if ($response->IsError) {
                            return false;
                        } else {
                            return true;
                        }
                    } else if ($sms_detail->type == 'msg_nineone') {

                        $params = array(
                            'authkey' => $sms_detail->authkey,
                            'senderid' => $sms_detail->senderid,
                            'templateid' => $template_id,
                        );
                        $this->CI->load->library('msgnineone', $params);
                        $this->CI->msgnineone->sendSMS($send_to, $msg);
                    } else if ($sms_detail->type == 'smscountry') {
                        $params = array(
                            'username' => $sms_detail->username,
                            'sernderid' => $sms_detail->senderid,
                            'password' => $sms_detail->password,
                            'authkey'   => $sms_detail->authkey,
                            'api_id'    => $sms_detail->api_id,
                        );
                        $this->CI->load->library('smscountry', $params);
                        $this->CI->smscountry->sendSMS($send_to, $msg);
                    } else if ($sms_detail->type == 'text_local') {
                        $params = array(
                            'username' => $sms_detail->username,
                            'hash' => $sms_detail->password,
                        );
                        $this->CI->load->library('textlocalsms', $params);
                        $this->CI->textlocalsms->sendSms(array($send_to), $msg, $sms_detail->senderid);
                    } else if ($sms_detail->type == 'custom') {
                        $this->CI->load->library('customsms');
                        $from = $sms_detail->contact;
                        $to = $send_to;
                        $message = $msg;
                        $this->CI->customsms->sendSMS($to, $message);
                    } else {
                        
                    }
                }
            }
        }
    }

    public function getBehaviourIncidentAssignedContent($student_detail, $template, $sms_detail_type=null) {

        foreach ($student_detail as $key => $value) {
            if ($sms_detail_type == 'msg_nineone') {

                if (strlen($value) > 30) {
                    $value = substr($value, 0, 29);
                }
            }
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    } 

}
