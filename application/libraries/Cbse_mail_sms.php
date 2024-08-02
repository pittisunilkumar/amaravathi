<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cbse_mail_sms
{

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('mailer');
        $this->CI->load->library('customlib');
        $this->sch_setting = $this->CI->setting_model->getSetting();
        $this->config_mailsms = $this->CI->config->item('mailsms');
        $this->_is_mail_config = $this->CI->emailconfig_model->getActiveEmail();
    }

    public function mailsms($send_for, $sender_details)
    {

        $chk_mail_sms = $this->CI->customlib->sendMailSMS($send_for);

        if (!empty($chk_mail_sms)) {

            if ($send_for == "cbse_exam_result") {

                $this->sendCbseResult($chk_mail_sms, $sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject'], $chk_mail_sms['template_id']);
            }
        }
    }



    public function sendCbseResult($chk_mail_sms, $exam_result, $template, $subject, $template_id)
    {


        if ($chk_mail_sms['mail'] || $chk_mail_sms['sms'] || $chk_mail_sms['notification']) {
            $sms_detail = $this->CI->smsconfig_model->getActiveSMS();
            if (!empty($exam_result['exam_result'])) {
                foreach ($exam_result['exam_result'] as $res_key => $res_value) {

                    $detail = array(
                        'student_name' => $this->CI->customlib->getFullName($res_value['firstname'], $res_value['middlename'], $res_value['lastname'], $this->sch_setting->middlename, $this->sch_setting->lastname),
                        'roll_no' => $res_value['roll_no'],
                        'email' => $res_value['email'],
                        'exam' => $exam_result['exam']['name'],
                        'guardian_phone' => $res_value['guardian_phone'],
                        'mobileno' => $res_value['mobileno'],
                        'guardian_email' => $res_value['guardian_email'],
                        'app_key' => $res_value['app_key'],
                        'parent_app_key' => $res_value['parent_app_key'],
                    );

                    if ($chk_mail_sms['mail'] && ($detail['guardian_email'] != "" || $detail['email'] != "")) {


                        $this->sentCBSEExamResultMail($detail, $template, $subject, $chk_mail_sms);
                    }

                    if ($chk_mail_sms['sms'] && $detail['guardian_phone'] != ""  && !empty($sms_detail)) {

                        $this->sentCBSEExamResultSMS($detail, $template, $template_id, $chk_mail_sms);
                    }



                    if ($chk_mail_sms['notification'] && ($detail['parent_app_key'] != "" || $detail['app_key'] != "")) {
                        $this->sentCBSEExamResultNotification($detail, $template, $subject, $chk_mail_sms);
                    }
                }
            }
        }
    }




    public function sentCBSEExamResultMail($detail, $template, $subject, $chk_mail_sms)
    {

        $msg = $this->getCBSEStudentResultContent($detail, $template);

        if ($chk_mail_sms['student_recipient']) {

            $send_to = $detail['email'];
            if (!empty($this->_is_mail_config) && $send_to != "") {
                $this->CI->mailer->send_mail($send_to, $subject, $msg);
            }
        } elseif ($chk_mail_sms['guardian_recipient']) {
            $send_to = $detail['guardian_email'];
            if (!empty($this->_is_mail_config) && $send_to != "") {
                $this->CI->mailer->send_mail($send_to, $subject, $msg);
            }
        }
    }


    public function getCBSEStudentResultContent($student_result_detail, $template)
    {
        foreach ($student_result_detail as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }


    public function sentCBSEExamResultSMS($detail, $template, $template_id, $chk_mail_sms)
    {

        $sms_detail = $this->CI->smsconfig_model->getActiveSMS();
        $msg        = $this->getCBSEExamResultContent($detail, $template, $sms_detail->type);

        if ($chk_mail_sms['student_recipient']) {
            $send_to = $detail['mobileno'];
            $this->sendMarksheetSMS($sms_detail, $msg, $send_to);
        } elseif ($chk_mail_sms['guardian_recipient']) {
            $send_to = $detail['guardian_phone'];
            $this->sendMarksheetSMS($sms_detail, $msg, $send_to);
        }
    }


    function sendMarksheetSMS($sms_detail, $msg, $send_to)
    {



        if (!empty($sms_detail)) {
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
                    return true;
                }
            } else if ($sms_detail->type == 'twilio') {

                $params = array(
                    'mode'        => 'sandbox',
                    'account_sid' => $sms_detail->api_id,
                    'auth_token'  => $sms_detail->password,
                    'api_version' => '2010-04-01',
                    'number'      => $sms_detail->contact,
                );

                $this->CI->load->library('twilio', $params);

                $from     = $sms_detail->contact;
                $to       = $send_to;
                $message  = $msg;
                $response = $this->CI->twilio->sms($from, $to, $message);

                if ($response->IsError) {
                    return true;
                } else {
                    return true;
                }
            } else if ($sms_detail->type == 'msg_nineone') {
                $params = array(
                    'authkey'    => $sms_detail->authkey,
                    'senderid'   => $sms_detail->senderid,
                    'templateid' => $template_id,
                );
                $this->CI->load->library('msgnineone', $params);
                $this->CI->msgnineone->sendSMS($send_to, $msg);
            } else if ($sms_detail->type == 'smscountry') {
                $params = array(
                    'username'  => $sms_detail->username,
                    'sernderid' => $sms_detail->senderid,
                    'password'  => $sms_detail->password,
                    'authkey'   => $sms_detail->authkey,
                    'api_id'    => $sms_detail->api_id,
                );
                $this->CI->load->library('smscountry', $params);
                $this->CI->smscountry->sendSMS($send_to, $msg);
            } else if ($sms_detail->type == 'text_local') {
                $to     = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'hash'     => $sms_detail->password,
                );
                $this->CI->load->library('textlocalsms', $params);
                $this->CI->textlocalsms->sendSms(array($to), $msg, $sms_detail->senderid);
            } else if ($sms_detail->type == 'bulk_sms') {
                $to     = $send_to;
                $params = array(
                    'username' => $sms_detail->username,
                    'password' => $sms_detail->password,
                );
                $this->CI->load->library('bulk_sms_lib', $params);
                $this->CI->bulk_sms_lib->sendSms(array($to), $msg);
            } else if ($sms_detail->type == 'mobireach') {
                $to     = $send_to;
                $params = array(
                    'authkey'  => $sms_detail->authkey,
                    'senderid' => $sms_detail->senderid,
                    'routeid'  => $sms_detail->api_id,

                );
                $this->CI->load->library('mobireach_lib', $params);
                $this->CI->mobireach_lib->sendSms(array($to), $msg);
            } else if ($sms_detail->type == 'nexmo') {
                $to     = $send_to;
                $params = array(
                    'from'       => $sms_detail->senderid,
                    'api_key'    => $sms_detail->api_id,
                    'api_secret' => $sms_detail->authkey,

                );
                $this->CI->load->library('nexmo_lib', $params);
                $this->CI->nexmo_lib->sendSms($to, $msg);
            } else if ($sms_detail->type == 'africastalking') {
                $to     = $send_to;
                $params = array(
                    'from'         => $sms_detail->senderid,
                    'api_key'      => $sms_detail->api_id,
                    'api_username' => $sms_detail->username,

                );
                $this->CI->load->library('africastalking_lib', $params);
                $this->CI->africastalking_lib->sendSms($to, $msg);
            } else if ($sms_detail->type == 'custom') {
                $this->CI->load->library('customsms');
                $from    = $sms_detail->contact;
                $to      = $send_to;
                $message = $msg;
                $this->CI->customsms->sendSMS($to, $message);
            } else {
            }
        }
        return true;
    }



    public function sentCBSEExamResultNotification($detail, $template, $subject, $chk_mail_sms)
    {

        $msg        = $this->getCBSEExamResultContent($detail, $template);
        $push_array = array(
            'title' => $subject,
            'body'  => $msg,
        );

        if ($chk_mail_sms['student_recipient']) {
            if ($detail['app_key'] != "") {
                $this->CI->pushnotification->send($detail['app_key'], $push_array, "mail_sms");
            }
        } elseif ($chk_mail_sms['guardian_recipient']) {
            if ($detail['parent_app_key'] != "") {
                $this->CI->pushnotification->send($detail['parent_app_key'], $push_array, "mail_sms");
            }
        }
    }


    public function getCBSEExamResultContent($student_result_detail, $template, $sms_detail_type = null)
    {

        foreach ($student_result_detail as $key => $value) {

            if ($sms_detail_type == 'msg_nineone') {

                if (strlen($value) > 30) {
                    $value = substr($value, 0, 29);
                }
            }
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }


    public function mailSmsMarksheet($send_for, $sender_details, $date = null, $exam_schedule_array = null, $file = null)
    {
        $chk_mail_sms = $this->CI->customlib->sendMailSMS($send_for);

        if (!empty($chk_mail_sms)) {

            if ($send_for == "cbse_email_pdf_exam_marksheet") {
                if ($chk_mail_sms['mail'] && $chk_mail_sms['template'] != "") {
                    $this->cbsesendpdfExamMarksheet($chk_mail_sms, $sender_details, $chk_mail_sms['template'], $chk_mail_sms['subject'], $file);
                }
            }
        }
    }


    public function cbsesendpdfExamMarksheet($chk_mail_sms, $sender_details, $template, $subject, $file)
    {

        if (!empty($this->_is_mail_config)) {



            $file_name = $sender_details['student_name'] . '_' . $sender_details['admission_no'];
            $msg = $this->getpdfCbseExamMarksheetContent($sender_details, $template);
            foreach ($sender_details as $key => $value) {
                $subject = str_replace('{{' . $key . '}}', $value, $subject);
            }


            if ($chk_mail_sms['student_recipient']) {

                $send_to = $sender_details['email'];
                if (!empty($this->_is_mail_config) && $send_to != "") {
                    $this->CI->mailer->send_mail_marksheet($send_to, $subject, $msg, $file, $file_name);
                }
            } elseif ($chk_mail_sms['guardian_recipient']) {
                $send_to = $sender_details['guardian_email'];
                if (!empty($this->_is_mail_config) && $send_to != "") {
                    $this->CI->mailer->send_mail_marksheet($send_to, $subject, $msg, $file, $file_name);
                }
            }
        }
    }


    public function getpdfCbseExamMarksheetContent($student_detail, $template)
    {
        foreach ($student_detail as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        return $template;
    }
}
