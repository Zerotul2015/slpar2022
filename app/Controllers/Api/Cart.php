<?php

namespace App\Controllers\Api;

use App\Model\Shop\Cart\CartModel;

class Cart extends Main
{

    public function getCart()
    {
        $this->returnData['result'] = true;
        $this->returnData['returnData'] = CartModel::getCart();
        $this->returnAnswer($this->returnData);
    }

    public function addProduct()
    {
        $productId = $this->postData['productId'] ?? null;
        $count = isset($this->postData['count']) && $this->postData['count'] ? $this->postData['count'] : 1;
        if ($productId) {
            $this->returnData = CartModel::addProduct($productId, $count);
        }
        $this->returnAnswer($this->returnData);
    }

    public function changeCount()
    {
        $productId = $this->postData['productId'] ?? null;
        $countNew = isset($this->postData['count']) && $this->postData['count'] ? $this->postData['count'] : 1;
        if ($productId) {
            $this->returnData = CartModel::changeCount($productId, $countNew);
        }
        $this->returnAnswer($this->returnData);
    }

    public function removeProduct()
    {
        $productId = $this->postData['productId'] ?? null;
        if ($productId) {
            $this->returnData = CartModel::removeProduct($productId);
        }
        $this->returnAnswer($this->returnData);
    }

    public function applyPromoCode()
    {
        $promoCodeText = $this->postData['promoCode'] ?? null;
        if ($promoCodeText && is_string($promoCodeText)) {
            $this->returnData = CartModel::applyPromoCode($promoCodeText);
        }
        $this->returnAnswer($this->returnData);
    }

    public function delCart()
    {
        $this->returnData['result'] = CartModel::deleteCart();
        $this->returnAnswer($this->returnData);
    }
}