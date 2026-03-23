<?php

namespace App\models;
use App\core\Model;
use App\data\DB;

class Model_Registration extends Model
{

    public function registrationvk(array $credentials)
    {
        DB::dbconnect();
        // $vkid = htmlspecialchars(trim($credentials[0])); // это если передавать данные через адресную строку
        // $email = htmlspecialchars(trim($credentials[1]));

        $vkid = htmlspecialchars(trim($credentials['vkid']));
        $email = htmlspecialchars(trim($credentials['email']));

        $user = DB::getByProp('users', 'vkid', $vkid);

        if ($user)
            return false; // если пользователь с таким vkid уже есть, то предлагаем ему авторизоваться 

        // если пользователя нет, то проверяем введенный email, вдруг пользователь уже регистрировался через форму,
        // если email есть, то обновляем учетную запись пользователя vkid и меняем роль
        $user = DB::getByProp('users', 'email', $email);

        if ($user) {
            $credentials = [
                'id' => $user['id'],
                'vkid' => $vkid,
                'role' => 'uservk'
            ];
            $user = DB::update('users', $credentials);
            // если нет, то создаем пользователя
        } else {
            $credentials = [
                'vkid' => $vkid,
                'login' => substr($email, 0, strpos($email, '@')),
                'email' => $email,
                'role' => 'uservk'
            ];
            $user = DB::create('users', $credentials);
        }
        // получаем данные пользователя, для авторизации
        $user = DB::getByProp('users', 'id', $user);
        return $user;
    }

    public function registration(array $credentials)
    {
        DB::dbconnect();
        $login = htmlspecialchars(trim($credentials['login']));
        $email = htmlspecialchars(trim($credentials['email']));
        $password = htmlspecialchars(trim($credentials['password']));
        $passwordagain = htmlspecialchars(trim($credentials['passwordagain']));
        // $role = htmlspecialchars(trim($credentials['role']));
        $role = 'user';

        // проверки надо делать на JS (без перезагрузки страницы и не грузить сервер лишними запросами)
        // и тогда можно будет использовать один метод для регистрации через форму и через VK ID 

        $result = [];

        if (!preg_match("/^[a-zA-Z0-9]+$/", $login)) {
            $result[] = "Логин может состоять только из букв английскго алфавита и цифр";
        }

        if ((strlen($login) < 3 || strlen($login) > 30)) {
            $result[] = "Логин должен быть не меньше 3-х символов и не больше 30";
        }

        if ((strlen($password) < 8 || strlen($password) > 20)) {
            $result[] = "Пароль должен быть не меньше 8-ми символов и не больше 20";
        }

        if ($password !== $passwordagain) {
            $result[] = "Пароли не совпадают";
        }

        $user = DB::getByProp('users', 'login', $login);

        if ($user) {
            $result[] = "Такой пользователь уже существует";
        }

        // проверяем по email, если пользователь регистрировался через vkid
        $user = DB::getByProp('users', 'email', $email);

        if ($user) {
            $result[] = "Пользователь с таким email уже существует";
        }

        if (count($result) == 0) {
            $credentials = [
                'login' => $login,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role
            ];
            $user = DB::create('users', $credentials);
            return $user;
        } else {
            return $result;
        }
    }
}