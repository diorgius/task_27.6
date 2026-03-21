<?php

namespace App\controllers;
use App\core\Controller;
use App\models\Model_Login;
use App\models\Model_Registration;

require_once CORE . 'logger.php';

class Controller_Registration extends Controller
{
    protected $model;

    public function index()
    {
        $this->view->generate('view_registration.php', 'view_template.php');
    }

    // public function signupvk($data) // это если передавать данные через адресную строку
            // if ($data) {
            // $credentials = $data;

    public function signupvk()
    {
        if (isset($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
            ) {
                $data[] = 'CSRF токен не валидный';
                $error = 'VK ID registration: CSRF токен не валидный';
                logging('warning', $error);
                $this->view->generate('view_registration.php', 'view_template.php', $data);
            } else {
                $credentials = $_POST;
                $this->model = new Model_Registration();
                $user = $this->model->registrationvk($credentials);
                if ($user) {
                    $_SESSION['auth'] = true;
                    $_SESSION['userId'] = $user['id'];
                    $_SESSION['login'] = $user['login'];
                    $_SESSION['role'] = $user['role'];
                    header('location: /');
                    exit();
                } else {
                    $data = "Error VK ID registration: user already registered vkid - {$credentials['vkid']}, email - {$credentials['email']}";
                    logging('info', $data);
                    $data = 'Вы уже зарегистрированы';
                    $this->view->generate('view_login.php', 'view_template.php', $data);

                    // как вариант если пользователь уже зарегистрирован, то авторизуем его
                    // $this->model = new Model_Login();
                    // $user = $this->model->loginvk($credentials);
                    // $_SESSION['auth'] = true;
                    // $_SESSION['userId'] = $user['id'];
                    // $_SESSION['login'] = $user['login'];
                    // $_SESSION['role'] = $user['role'];
                    // header('location: /');
                    // exit();
                }
            }
        }
    }

    public function signup()
    {
        if (isset($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
            ) {
                $data[] = 'CSRF токен не валидный';
                $error = 'Registration: CSRF токен не валидный';
                logging('warning', $error);
                $this->view->generate('view_registration.php', 'view_template.php', $data);
            } else {
                $credentials = $_POST;
                $this->model = new Model_Registration();
                $result = $this->model->registration($credentials);
                if (is_array($result)) {
                    $data = $result;
                    foreach ($data as $error) {
                        $error = 'Registration: ' . $error . ", login - {$credentials['login']}, password - {$credentials['password']}";
                        logging('info', $error);
                    }
                    $this->view->generate('view_registration.php', 'view_template.php', $data);
                } else {
                    $data = 'Успешная регистрация, пожалуйста авторизуйтесь';
                    $this->view->generate('view_login.php', 'view_template.php', $data);
                }
            }
        }
    }
}