<?php


namespace App\Controllers\Api;


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
            'isWholesale'=> 0, //0 - обычный покупатель, 1 - оптовик ...
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
                        if($resultAuth = \App\Controllers\Authorization::auth($this->postData['login'], $this->postData['password'])){
                            $this->returnData = [
                                'isAuth' => true,
                                'tokenAuth' => $_COOKIE['tokenAuth'] ?? null,
                                'isWholesale'=> $resultAuth,
                                'result'=>true,
                            ];
                        }
                    }
                    break;
                case 'checkAuth':
                    if(\App\Controllers\Authorization::isAuth()){
                        $this->returnData = [
                            'isAuth' => true,
                            'tokenAuth' => $_COOKIE['tokenAuth'],
                            'accessLevel'=> 1,
                            'result'=>true,
                        ];
                    }
                    break;
                case 'logout':
                    \App\Controllers\Authorization::exit();
                    break;
            }
        }
        static::returnAnswer($this->returnData);
    }

    public function Logout(){
        \App\Controllers\Authorization::exit();
    }

    public function returnAnswer($returnArray)
    {
        header('Content-Type: application/json');
        echo json_encode($returnArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    }
}