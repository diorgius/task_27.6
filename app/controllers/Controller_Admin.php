<?php

namespace App\controllers;

use App\core\Controller;
use App\data\DB;
use App\models\Model_Admin;

class Controller_Admin extends Controller
{

    protected $model;

    public function index()
    {
        if (isset($_SESSION['auth']) && $_SESSION['role'] === 'admin') {
            $this->view->generate('view_admin.php', 'view_template.php');
        } else {
            $this->view->generate('view_404.php', 'view_template.php');
        }
    }

    public function getUsers()
    {
        $this->model = new Model_Admin();
        $users = $this->model->getUsers();
        if ($users) {
            $data = $users;
        // } else {
        //     $data[] = 'Нет данных';
        }
        $this->view->generate('view_admin.php', 'view_template.php', $data);
    }

    public function addUser()
    {
        $this->view->generate('view_adduser.php', 'view_template.php');
    }

    public function createUser()
    {
        if (isset($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model = new Model_Admin();
            $user = $this->model->createUser($_POST);
            if ($user) {
                header('location: /admin/getusers');
            // } else {
            //     $data[] = 'Что-то пошло не так';
            }
        }
    }

    public function editUser(array $data)
    {
        $id = $data[0];
        $this->model = new Model_Admin();
        $user = $this->model->editUser($id);
        if ($user) {
            $data = $user;
            $this->view->generate('view_edituser.php', 'view_template.php', $data);
        // } else {
        //     $data[] = 'Что-то пошло не так';
        }
    }

    public function updateUser()
    {
        if (isset($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model = new Model_Admin();
            $user = $this->model->updateUser($_POST);
            if ($user) {
                header('location: /admin/getusers');
            // } else {
            //     $data[] = 'Что-то пошло не так';
            }
        }
    }

    public function deleteUser(array $data)
    {
        $id = $data[0];
        $this->model = new Model_Admin();
        $users = $this->model->deleteUser($id);
        header('location: /admin/getusers');
    }
}