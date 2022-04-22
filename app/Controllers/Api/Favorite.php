<?php

namespace App\Controllers\Api;

use App\Model\Shop\Favorite\FavoriteModel;

class Favorite extends Main
{

    public function getFavorite()
    {
        $this->returnData['result'] = true;
        $this->returnData['returnData'] = FavoriteModel::getFavorite();
        $this->returnAnswer($this->returnData);
    }

    public function addProduct()
    {
        $productId = $this->postData['productId'] ?: null;
        if ($productId) {
            $this->returnData = FavoriteModel::addProduct($productId);
        }
        $this->returnAnswer($this->returnData);
    }
    public function removeProduct()
    {
        $productId = $this->postData['productId'] ?: null;
        if ($productId) {
            $this->returnData = FavoriteModel::removeProduct($productId);
        }
        $this->returnAnswer($this->returnData);
    }

    public function delFavorite()
    {
        $this->returnData['result'] = FavoriteModel::deleteFavorite();
        $this->returnAnswer($this->returnData);
    }
}