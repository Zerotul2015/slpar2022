<?php

namespace App\Controllers\Api;

use App\Model\Shop\Compare\CompareModel;

class Compare extends Main
{

    public function getCompare()
    {
        $this->returnData['result'] = true;
        $withCategories = $this->postData['withCategories'] ?? false;
        $this->returnData['returnData'] = CompareModel::getCompare(false, $withCategories);
        $this->returnAnswer($this->returnData);
    }

    public function addProduct()
    {
        $productId = $this->postData['productId'] ?? null;
        if ($productId) {
            $this->returnData = CompareModel::addProduct($productId);
        }
        $this->returnAnswer($this->returnData);
    }
    public function removeProduct()
    {
        $productId = $this->postData['productId'] ?? null;
        if ($productId) {
            $this->returnData = CompareModel::removeProduct($productId);
        }
        $this->returnAnswer($this->returnData);
    }

    public function delCompare()
    {
        $this->returnData['result'] = CompareModel::deleteCompare();
        $this->returnAnswer($this->returnData);
    }
}