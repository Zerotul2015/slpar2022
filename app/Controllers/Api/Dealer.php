<?php

namespace App\Controllers\Api;

use App\Model\Dealer\DealerModel;
use App\Model\Shop\Compare\CompareModel;

class Dealer extends Main
{


    public function getDealer()
    {
        if ($this->isAuth) {
            $this->returnData = DealerModel::getProfile();
            $this->returnAnswer($this->returnData);
        }
    }

    public function registerRequest()
    {
        $formData = [
            'name' => $this->postData['name'] ?? null,
            'phone' => $this->postData['phone'] ?? null,
            'mail' => $this->postData['mail'] ?? null,
            'company' => $this->postData['company'] ?? null,
            'comment' => $this->postData['comment'] ?? ''
        ];

        $this->returnData = DealerModel::registerRequest($formData);
        $this->returnAnswer($this->returnData);
    }

    public function makeOrder(){

    }

    public function checkAuth()
    {
        if ($this->isAuth) {
            $this->returnData['returnData'] = [
                'isAuth'=>true,
                'customerId' => $this->customerId,
                'isWholesale' => $this->isWholesale
            ];
        }
        $this->returnData['result'] = $this->isAuth;
        $this->returnAnswer($this->returnData);
    }

    public function auth()
    {
        $login = $this->postData['login'] ?? null;
        $pass = $this->postData['pass'] ?? null;
        if ($login && $pass) {
            $resultAuth = \App\Controllers\Authorization::auth($login, $pass);
            $this->returnData['result'] =  $resultAuth['result'];
            $this->returnData['returnData'] = [
                'isAuth'=> $resultAuth['result'],
                'customerId' => $resultAuth['customerId'],
                'isWholesale' => $resultAuth['isWholesale']
            ];
        }
        $this->returnAnswer($this->returnData);
    }

    public function logout(){
        \App\Controllers\Authorization::exit();
        $this->returnData['result']=true;
        $this->returnAnswer($this->returnData);
    }

}