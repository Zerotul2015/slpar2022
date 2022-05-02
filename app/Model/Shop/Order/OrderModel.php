<?php

namespace App\Model\Shop\Order;

use App\Classes\ActiveRecord\Tables\Customer;
use App\Classes\ActiveRecord\Tables\DeliveryMethods;
use App\Classes\ActiveRecord\Tables\Orders;
use App\Classes\ActiveRecord\Tables\PaymentMethods;
use App\Model\MainModel;
use App\Model\NotificationMailModel;
use App\Model\Shop\Cart\CartModel;

class OrderModel
{
    public static function newOrder($valOrder)
    {
        $resultSaveOrder = false;
        $productsForMetrica = [];
        $cart = CartModel::getCart();

        //входящие данные проверены
        if (static::checkFormDataNewOrder($valOrder)) {
            $cartProducts = $cart->products;
            $cartCertificates = $cart->certificates;

            if ($cartProducts || $cartCertificates) {
                //получаем или создаем покупателя
                $customer = static::getOrCreateCustomer($valOrder['customer']);

                $newOrder = Orders::create();
                $newOrder->products = $cartProducts;
                $newOrder->promo_code_used = $cart->promo_code_used;
                $newOrder->delivery_address = $valOrder['address'];
                $newOrder->customer_id = $customer->id;
                $newOrder->delivery_id = $valOrder['delivery'];
                $newOrder->payment_id = $valOrder['payment'];
                $newOrder->token = MainModel::generateToken();
                $newOrder->status_id = 1; // новый заказ
                $resultSaveOrder = $newOrder->save();
                //если не удалось сохранить заказ, убираем покупателя, чтобы не засорять базу
                if ($resultSaveOrder === false) {
                    $customer->del();
                }
                if ($resultSaveOrder) {
                    //записывае в историю сертификата что он был использован в это заказе и обновляем баланс
                    if ($cart->certificate_used) {
                        $giftCertificate = GiftCertificatePurchased::findOne($cart->certificate_used);
                        $sum = CartModel::getCartSumAndCount()['sum'];
                        $delivery = DeliveryMethods::findOne($valOrder['delivery']);
                        if ($sum < $delivery->sum_for_free) {
                            $sum += $delivery->price;
                        }
                        $usageHistory = $giftCertificate->usage_history;
                        if ($giftCertificate->balance > $sum) {
                            $usageHistory[$resultSaveOrder] = $sum;
                        } else {
                            $usageHistory[$resultSaveOrder] = $giftCertificate->balance;
                        }
                        $giftCertificate->usage_history = $usageHistory;
                        $giftCertificate->balance -= $usageHistory[$resultSaveOrder];
                        $giftCertificate->save();
                    }
                    //очищаем корзину если это обычный заказ(не быстрый)
                    if (!$quickOrder) {
                        $productsForMetrica = static::getDataForMetrica($cart->products);
                        CartModel::clearCart();
                    }
                    NotificationMailModel::notificationOrder(1, $newOrder);
                }
            }
        }

        return [
            'id' => $resultSaveOrder,
            'metricaData' => $productsForMetrica
        ];
    }
    /**
     * Проверяем массив данных переданных со страницы оформления заказ
     * @param $valOrder
     * @param bool quickOrder
     * @return bool
     */
    private static function checkFormDataNewOrder($valOrder, $quickOrder = false): bool
    {
        //проверка запроолнености данных покупателя
        if (isset($valOrder['customer']) && is_array($valOrder['customer'])) {
            $customer = $valOrder['customer'];
            $fieldsEmpty = false;
            foreach ($customer as $field) {
                if (empty(trim($field))) {
                    $fieldsEmpty = true;
                }
            }
            $needArrayKeyCustomer = ['name' => '', 'mail' => '', 'phone' => ''];
            $resultDiff = (array_diff_key($needArrayKeyCustomer, $customer));
            if ($quickOrder) {
                $notMail = !isset($valOrder['customer']['mail']) || empty($valOrder['customer']['mail']);
                $notPhone = !isset($valOrder['customer']['phone']) && empty($valOrder['customer']['phone']);
                if ($notMail && $notPhone) {
                    return false;
                }
            } elseif (!$fieldsEmpty && empty(array_diff_key($needArrayKeyCustomer, $customer))) {
                if (!filter_var($customer['mail'], FILTER_VALIDATE_EMAIL)) {
                    $customer = array_filter($customer, function ($v) {
                        return trim($v) == $v;
                    });
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
        //прерываем проверку если это быстрый заказ
        if ($quickOrder) {
            return true;
        }


        // проверка метода доставки
        if (isset($valOrder['delivery']) && $valOrder['delivery']) {
            if (!$delivery = DeliveryMethods::findOne($valOrder['delivery'])) {
                return false;
            }
        } else {
            return false;
        }

        // проверка метода оплаты
        if (isset($valOrder['payment']) && $valOrder['payment']) {
            if (!$payment = PaymentMethods::findOne($valOrder['payment'])) {
                return false;
            }
        } else {
            return false;
        }

        //для любого способы доставки кроме самовывоз нужен адрес доставки
        return !($valOrder['delivery'] !== 1 && !trim($valOrder['address']));
    }

    /**
     * Возвращает покупателя из базы если есть такой(проверяем по mail или phone), иначе создаем нового с переданными данными
     * @param $customer
     * @return Customer|bool
     */
    private static function getOrCreateCustomer($customer): Customer|bool
    {
        $existCustomer = false;
        if (isset($customer['mail']) && !empty($customer['mail'])) {
            $existCustomer = Customer::find()->where(['mail' => $customer['mail']])->one();
        }
        if (!$existCustomer && isset($customer['phone']) && !empty($customer['phone'])) {
            $existCustomer = Customer::find()->where(['phone' => $customer['phone']])->one();
        }
        if ($existCustomer) {
            $customerForOrder = $existCustomer;
        } else {
            $customerForOrder = Customer::create($customer);
            $customerForOrder->save();
        }
        return $customerForOrder;
    }
}