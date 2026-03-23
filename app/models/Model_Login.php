<?php

namespace App\models;
use App\core\Model;
use App\data\DB;

class Model_Login extends Model
{

    public function loginvk(array $credentials)
    {
        DB::dbconnect();
        $vkid = htmlspecialchars(trim($credentials['vkid']));
        $email = htmlspecialchars(trim($credentials['email']));

        // проверяем пользователя
        $user = DB::getByProp('users', 'vkid', $vkid);

        if ($user) {
            return $user;
        } else {
            // если пользователя с vkid нет, то проверяем email, если пользователь регистрировлся через форму
            // и если email есть, то обновляем учетную запись пользователя vkid и меняем роль
            $user = DB::getByProp('users', 'email', $email);
            if ($user) {
                $credentials = [
                    'id' => $user['id'],
                    'vkid' => $vkid,
                    'role' => 'uservk'
                ];
                $user = DB::update('users', $credentials);
                $user = DB::getByProp('users', 'vkid', $vkid);
                return $user;
            } else {
                return false;
            }
        }
    }

    public function login(array $credentials)
    {
        DB::dbconnect();
        $login = htmlspecialchars(trim($credentials['login']));
        $password = htmlspecialchars(trim($credentials['password']));

        $user = DB::getByProp('users', 'login', $login);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                return $user;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function setcookie($id): void
    {
        $cookiehash = hash('gost-crypto', random_int(0, 9999));
        setcookie("id", $id, time() + 60 * 60 * 24, "/", "", false, true);
        setcookie("hash", $cookiehash, time() + 60 * 60 * 24, "/", "", false, true);
        DB::update('users', $values = ['id' => $id, 'cookiehash' => $cookiehash]);
    }
}