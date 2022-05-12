<?php

namespace App\Controllers\Api;

use App\Classes\ActiveRecord\Tables\ProductStockStatus;
use App\Classes\ActiveRecord\Tables\WholesaleLevel;
use App\Model\Customer\CustomerModel;
use App\Model\Shop\Compare\CompareModel;

class Customer extends Main
{


    public function getDealer()
    {
        if ($this->isAuth) {
            $this->returnData = CustomerModel::getProfile();
            $this->returnAnswer($this->returnData);
        }
    }

    public function getWholesaleLevels()
    {
        if ($this->isAuth) {
            $object = WholesaleLevel::find();
            $this->returnAnswer($this->prepareReturnData($object));
        }


    }

    public function registerRequestWholesale()
    {
        $formData = [
            'name' => $this->postData['name'] ?? null,
            'phone' => $this->postData['phone'] ?? null,
            'mail' => $this->postData['mail'] ?? null,
            'company' => $this->postData['company'] ?? null,
            'comment' => $this->postData['comment'] ?? ''
        ];

        $this->returnData = CustomerModel::registerRequestWholesale($formData);
        $this->returnAnswer($this->returnData);
    }

    public function registerCustomer()
    {
        $formData = [
            'name' => $this->postData['name'] ?? null,
            'phone' => $this->postData['phone'] ?? null,
            'mail' => $this->postData['mail'] ?? null,
            'pass' => $this->postData['pass'] ?? null,
        ];

        $this->returnData = CustomerModel::registerCustomer($formData);
        $this->returnAnswer($this->returnData);
    }

    public function recoveryPassword()
    {
        $mail = $this->postData['mail'] ?? null;
        $this->returnData = CustomerModel::recoveryPassword($mail);
        $this->returnAnswer($this->returnData);
    }

    public function checkAlreadyRegistered()
    {
        $mail = $this->postData['mail'] ?? null;
        $phone = $this->postData['phone'] ?? null;

        $this->returnData = CustomerModel::checkAlreadyRegistered($mail, $phone);
        $this->returnAnswer($this->returnData);
    }


    public function makeOrder()
    {

    }

    public function checkAuth()
    {
        if ($this->isAuth) {
            $this->returnData['returnData'] = [
                'isAuth' => true,
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
            $this->returnData['result'] = $resultAuth['result'];
            $this->returnData['returnData'] = [
                'isAuth' => $resultAuth['result'],
                'customerId' => $resultAuth['customerId'],
                'isWholesale' => $resultAuth['isWholesale']
            ];
        }
        $this->returnAnswer($this->returnData);
    }

    public function logout()
    {
        \App\Controllers\Authorization::exit();
        $this->returnData['result'] = true;
        $this->returnAnswer($this->returnData);
    }

}