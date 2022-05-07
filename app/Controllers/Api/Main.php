<?php


namespace App\Controllers\Api;


use App\Controllers\Authorization;

abstract class Main
{
    public bool $isAuth = false;
    public bool|int $isWholesale = 0;
    public mixed $customerId = null;
    public array $postData = [];
    public string $action = ''; // save, delete, get
    public array $where = [];
    public string $limit = ''; // int count
    public array $values = []; //data
    public array $returnData = ['result'=>false, 'returnData'=> null]; //data

    public function __construct()
    {
        //auth
        $resultAuthCheck = Authorization::isAuth();
        $this->isAuth = $resultAuthCheck['result'];
        $this->customerId = $resultAuthCheck['customerId'];
        $this->isWholesale = $resultAuthCheck['isWholesale'];
        //prepare post data
        $postData = file_get_contents("php://input");
        if (!empty($postData)) {
            $this->postData = json_decode($postData, true);
        }

        if (isset($this->postData['limit']) && is_int($this->postData['limit'])) {
            $this->limit = $this->postData['limit'];
        }
        if (isset($this->postData['where']) && is_array($this->postData['where'])) {
            $this->where = $this->postData['where'];
        }
        if (isset($this->postData['action'])) {
            $this->action = $this->postData['action'];
        }
        if (isset($this->postData['values'])) {
            $this->values = $this->postData['values'];
        }

    }

    public function returnAnswer($returnArray)
    {
        header('Content-Type: application/json');
        echo json_encode($returnArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    }
}