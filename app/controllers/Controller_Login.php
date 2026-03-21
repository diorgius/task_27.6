<?php

namespace App\controllers;
use App\core\Controller;
use App\models\Model_Login;

require_once CORE . 'logger.php';

class Controller_Login extends Controller
{
    protected $model;

    public function index()
    {
        $this->view->generate('view_login.php', 'view_template.php');
    }

    // public function signupvk($data) // это если передавать данные через адресную строку
    // {
    //     if ($data) {
    //         $credentials = $data;

    public function signupvk()
    {
        if (isset($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
            ) {
                $data = 'Login: CSRF токен не валидный';
                logging('warning', $data);
                $this->view->generate('view_login.php', 'view_template.php', $data);
            } else {
                $credentials = $_POST;
                $this->model = new Model_Login();
                $user = $this->model->loginvk($credentials);

                if ($user) {
                    $_SESSION['auth'] = true;
                    $_SESSION['userId'] = $user['id'];
                    $_SESSION['login'] = $user['login'];
                    $_SESSION['role'] = $user['role'];
                    header('location: /');
                    exit();
                } else {
                    $data = "Error VK ID login: no such user vkid - {$credentials['vkid']}";
                    logging('info', $data);
                    unset($data);
                    $data[] = 'Вы не зарегистрированы';
                    $this->view->generate('view_registration.php', 'view_template.php', $data);
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
                $data = 'Login: CSRF токен не валидный';
                logging('warning', $data);
                $this->view->generate('view_login.php', 'view_template.php', $data);
            } else {
                $credentials = $_POST;
                if (isset($_POST['remember'])) {
                    $remember = $_POST['remember'];
                }
                $this->model = new Model_Login();
                $user = $this->model->login($credentials);

                if ($user) {
                    $_SESSION['auth'] = true;
                    $_SESSION['userId'] = $user['id'];
                    $_SESSION['login'] = $user['login'];
                    $_SESSION['role'] = $user['role'];
                    if($remember) $this->model->setcookie($user['id']);
                    header('location: /');
                    exit();
                } else {
                    $data = "Wrong login or password: login - {$credentials['login']}, password - {$credentials['password']}";
                    logging('info', $data);
                    $data = 'Неверное имя пользователя или пароль';
                    $this->view->generate('view_login.php', 'view_template.php', $data);
                }
            }
        }
    }

    public function signout()
    {
        session_start();
        unset($_SESSION['auth']);
        unset($_SESSION['userId']);
        unset($_SESSION['login']);
        unset($_SESSION['csrf_token']);
        setcookie("id", '', time() - 60 * 60 * 24, "/", "", false, true);
        setcookie("hash", '', time() - 60 * 60 * 24, "/", "", false, true);
        session_destroy();
        header('location: /');
        exit();
    }
}