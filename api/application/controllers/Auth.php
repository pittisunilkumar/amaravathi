<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
    }

    public function login()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->auth_model->check_auth_client();
            if ($check_auth_client == true) {
                $params   = json_decode(file_get_contents('php://input'), true);
                $username = $params['username'];
                $password = $params['password'];
                $app_key  = $params['deviceToken'];
                $response = $this->auth_model->login($username, $password, $app_key);
                json_output(200, $response);
            }
        }
    }

}
