<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 11.12.17
 * Time: 23:25
 */

namespace App\Controllers\Admin;


use App\Classes\ActiveRecord\Tables\Users;

class Authorization
{
    public function login()
    {
        if (isset($_POST['login']) && isset($_POST['password'])) {
            $login = $_POST['login'];
            $pass = $_POST['password'];


            if ($this->isAuth()) {
                header('Location: /admin');
            } else {
                $this->auth($login, $pass);
                header('Location: /admin');
            }
        } else {
            header('Location: /admin');
        }

    }

    /**
     * Авторизация
     * @param $login
     * @param $pass
     * @return bool
     */
    public static function auth($login, $pass):bool
    {
        $result = false;
        if ($user = Users::find()->where(['login' => $login])->one()) {
            if (password_verify($pass, $user->pass)) {
                $token =  uniqid($user->login, true);
                $user->token = $token;
                if ($user->save()) {
                    setcookie('login', $login, time() + (3600 * 24), '/filemanager');
                    setcookie('login', $login, time() + (3600 * 24), '/admin');
                    setcookie('tokenAuth', $token, time() + (3600 * 24), '/filemanager');
                    setcookie('tokenAuth', $token, time() + (3600 * 24), '/admin');
                    $result =  true;
                }

            }
        }
        return $result;
    }

    /**
     * Проверка на авторизвацию
     * @return bool
     */
    public static function isAuth():bool
    {
        $result = false;
        if (isset($_COOKIE['tokenAuth']) && isset($_COOKIE['login'])) {
            $checkAuth = Users::find()->where(['login' => $_COOKIE['login'], 'token' => $_COOKIE['tokenAuth']])->one();
            if ($checkAuth) {
                $result = true;
            }
        }
        return $result;
    }

    public function logout()
    {
        self::exit();
        header('Location: /');
    }

    public static function exit()
    {
        unset($_COOKIE["login"]);
        unset($_COOKIE["tokenAuth"]);
        setcookie('login', '0',time() - 3600, '/admin');
        setcookie('login', '0',time() - 3600, '/filemanager');
        setcookie('tokenAuth', '0',time() - 3600, '/admin');
        setcookie('tokenAuth', '0',time() - 3600, '/filemanager');
    }
}