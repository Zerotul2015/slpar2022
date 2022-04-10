<?php


namespace App\Controllers\Api;


abstract class Main
{
    public bool $auth = false;
    public array $postData = [];
    public string $action = ''; // save, delete, get
    public array $where = [];
    public string $limit = ''; // int count
    public array $values = []; //data

    public function __construct()
    {
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