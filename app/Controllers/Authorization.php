<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 11.12.17
 * Time: 23:25
 */

namespace App\Controllers;


use App\Classes\ActiveRecord\Tables\Customer;
use App\Classes\ActiveRecord\Tables\Users;
use JetBrains\PhpStorm\ArrayShape;

class Authorization
{
    public function login()
    {
        if (isset($_POST['login']) && isset($_POST['password'])) {
            $login = $_POST['login'];
            $pass = $_POST['password'];
            if ($this->isAuth()) {
                header('Location: /');
            } else {
                $this->auth($login, $pass);
                header('Location: /');
            }
        } else {
            header('Location: /');
        }
    }

    /**
     * Авторизация
     * @param $login
     * @param $pass
     * @return array
     */

    #[ArrayShape(['result' => "bool", 'customerId' => "mixed", 'isWholesale' => "false|int|mixed"])]
    public static function auth($login, $pass):array
    {
        $result = false;
        $customerId = null;
        $isWholesale = 0;
        if ($customer = Customer::find()->where(['mail' => $login])->one()) {
            if (password_verify($pass, $customer->pass)) {
                $token =  uniqid($customer->id, true);
                $customer->token_auth = $token;
                if ($customer->save()) {
                    $customerId = $customer->id;
                    $isWholesale = $customer->is_wholesale?? 0;
                    setcookie('login', $login, time() + (3600 * 24), '/');
                    setcookie('login', $login, time() + (3600 * 24), '/dealer');
                    setcookie('tokenAuth', $token, time() + (3600 * 24), '/');
                    setcookie('tokenAuth', $token, time() + (3600 * 24), '/dealer');
                    $result =  true;
                }

            }
        }
        return ['result'=>$result, 'customerId'=>$customerId, 'isWholesale'=>$isWholesale];
    }

    /**
     * Проверка на авторизвацию
     * @return array
     */
    #[ArrayShape(['result' => "bool", 'customerId' => "mixed", 'isWholesale' => "false|int|mixed"])]
    public static function isAuth():array
    {
        $result = false;
        $customerId = null;
        $isWholesale = 0;
        if (isset($_COOKIE['tokenAuth']) && isset($_COOKIE['login'])) {
            $findCustomerAuth = Customer::find()->where(['mail' => $_COOKIE['login'], 'token_auth' => $_COOKIE['tokenAuth']])->one();
            if ($findCustomerAuth) {
                $customerId = $findCustomerAuth->id;
                $isWholesale = $findCustomerAuth->is_wholesale?? 0;
                $result = true;
            }
        }
        return ['result'=>$result, 'customerId'=>$customerId, 'isWholesale'=>$isWholesale];
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
        setcookie('login', '0',time() - 3600, '/');
        setcookie('login', '0',time() - 3600, '/dealer');
        setcookie('tokenAuth', '0',time() - 3600, '/');
        setcookie('tokenAuth', '0',time() - 3600, '/dealer');
    }
}