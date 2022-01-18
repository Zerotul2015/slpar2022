<?php


namespace App\Controllers\Admin\Api;


use http\Cookie;

class Authorization
{
    public array $returnData = [];
    public array $postData = [];

    public function __construct()
    {
        $this->returnData = [
            'isAuth' => false,
            'tokenAuth' => '',
            'accessLevel'=> 0, //0 - доступ заперещен везде, 1 - полный доступ ...
            'result'=>false,
        ];
        $this->postData = json_decode(file_get_contents("php://input"), true);

    }

    public function Main()
    {
        if (isset($this->postData['action'])) {
            switch ($this->postData['action']) {
                case 'login':
                    if(isset($this->postData['login'], $this->postData['password'])
                        && !empty($this->postData['login']) && !empty($this->postData['password'])){
                        if(\App\Controllers\Admin\Authorization::auth($this->postData['login'], $this->postData['password'])){
                            $this->returnData = [
                                'isAuth' => true,
                                'tokenAuth' => $_COOKIE['tokenAuth'] ?? null,
                                'accessLevel'=> 1,
                                'result'=>true,
                            ];
                        }
                    }
                    break;
                case 'checkAuth':
                    if(\App\Controllers\Admin\Authorization::isAuth()){
                        $this->returnData = [
                            'isAuth' => true,
                            'tokenAuth' => $_COOKIE['tokenAuth'],
                            'accessLevel'=> 1,
                            'result'=>true,
                        ];
                    }
                    break;
                case 'logout':
                    \App\Controllers\Admin\Authorization::exit();
                    break;
            }
        }
        static::returnAnswer($this->returnData);
    }

    public function Logout(){
        \App\Controllers\Admin\Authorization::exit();
    }

    public function returnAnswer($returnArray)
    {
        header('Content-Type: application/json');
        echo json_encode($returnArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    }
}