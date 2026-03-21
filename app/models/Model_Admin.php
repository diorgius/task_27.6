<?php

namespace App\models;
use App\core\Model;
use App\data\DB;

class Model_Admin extends Model
{

    public function getUsers()
    {
        DB::dbconnect();
        $users = DB::getAll('users');

        if ($users) {
            return $users;
        } else {
            return false;
        }
    }

    public function createUser(array $data)
    {
        $login = htmlspecialchars(trim($data['login']));
        $password = password_hash(htmlspecialchars(trim($data['password'])), PASSWORD_DEFAULT);
        $email = htmlspecialchars(trim($data['email']));
        $role = htmlspecialchars(trim($data['role']));

        // надо делать проверки на соответствие введенных данных как при регистрации 

        $credentials = [
            'login' => $login,
            'password' => $password,
            'email' => $email,
            'role' => $role
        ];

        DB::dbconnect();
        $user = DB::create('users', $credentials);

        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    public function editUser(int $id)
    {
        DB::dbconnect();
        $user = DB::getByProp('users', 'id', $id);

        if ($user) {
            return $user;
        } else {
            return false;
        }
    }

    public function updateUser(array $data)
    {
        $id = $data['id'];
        $login = htmlspecialchars(trim($data['login']));
        $password = password_hash(htmlspecialchars(trim($data['password'])), PASSWORD_DEFAULT);
        $email = htmlspecialchars(trim($data['email']));
        $role = htmlspecialchars(trim($data['role']));

        // надо делать проверки на соответствие введенных данных как при регистрации 

        $credentials = [
            'id' => $id,
            'login' => $login,
            'password' => $password,
            'email' => $email,
            'role' => $role
        ];

        DB::dbconnect();
        $user = DB::update('users', $credentials);

        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteUser(int $id): void
    {
        DB::dbconnect();
        DB::delete('users', $id);
    }
}