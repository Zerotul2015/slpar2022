<?php

namespace App\Controllers\Api;

use App\Model\Shop\Order\OrderModel;
use Exception;

class Orders extends Main
{

    /**
     * @throws Exception
     */
    public function restoreAccess()
    {
        $orderId = $this->postData['orderId'] ?? null;
        $customerMail = $this->postData['customerMail'] ?? null;
        $this->returnData['result'] = OrderModel::generateVerifyCode($orderId, $customerMail);
        $this->returnAnswer($this->returnData);
    }

    public function getOrders()
    {
        $orderId = $this->postData['orderId'] ?? null;
        $customerMail = $this->postData['customerMail'] ?? null;
        $pin = $this->postData['pin'] ?? null;
        $token = $this->postData['token'] ?? null;
        $this->returnData = OrderModel::getOrdersPreview($orderId, $customerMail, $pin, $token);
        $this->returnAnswer($this->returnData);
    }

    /**
     * @throws Exception
     */
    public function makingOrder()
    {
        $ordersDetails = $this->postData['ordersDetails'] ?? null;
        $this->returnData = OrderModel::newOrder($ordersDetails);
        $this->returnAnswer($this->returnData);
    }

}