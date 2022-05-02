<?php

namespace App\Model\Shop\Order;

use App\Classes\ActiveRecord\Tables\Customer;
use App\Classes\ActiveRecord\Tables\DeliveryMethods;
use App\Classes\ActiveRecord\Tables\Orders;
use App\Classes\ActiveRecord\Tables\PaymentMethods;
use App\Classes\ActiveRecord\Tables\PaymentYandex;
use App\Classes\ActiveRecord\Tables\ProductCategory;
use App\Classes\Mail;
use App\Model\MainModel;
use App\Model\Notification\NotificationMailModel;
use App\Model\Shop\Cart\CartModel;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use YooKassa\Client;

class OrderModel
{
    /**
     * @throws Exception
     */

    #[ArrayShape(['result' => "bool", 'returnData' => "array"])]
    public static function newOrder($valOrder): array
    {
        $returnData = ['result' => false, 'returnData' => []];
        $resultSaveOrder = false;
        $productsForMetrica = [];
        $cart = CartModel::getCart();

        //входящие данные проверены
        if (static::checkFormDataNewOrder($valOrder)) {
            $cartProducts = $cart->products;

            if ($cartProducts) {
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
                    $returnData['result'] = true;
                    $returnData['returnData']['metrika'] = static::getDataForMetrica($cart->products);
                    CartModel::deleteCart();

                    NotificationMailModel::notificationOrder(1, $newOrder);
                }
            }
        }

        return $returnData;
    }

    /**
     * Проверяем массив данных переданных со страницы оформления заказ
     * @param $valOrder
     * @param bool quickOrder
     * @return bool
     */
    private static function checkFormDataNewOrder($valOrder): bool
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
            if (!$fieldsEmpty && empty(array_diff_key($needArrayKeyCustomer, $customer))) {
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

    /**
     * Принимате в качестве параметра массив товаров из корзины
     * Возвращает массив товаров для отпарвки в метрику для фиксации продажи
     * @param $productsInCart
     * @return array
     */
    public static function getDataForMetrica($productsInCart): array
    {
        $categoryArray = [];
        $productsForMetrica = [];
        if (is_array($productsInCart) && !empty($productsInCart)) {
            foreach ($productsInCart as $cartProduct) {
                $categoryArray[$cartProduct['product']->category_id] = $cartProduct['product']->category_id;
            }
            $categories = ProductCategory::find()->where(['id' => $categoryArray])->select(['id', 'name'])->indexBy()->all();
            foreach ($productsInCart as $productInCart) {
                $productOneForMetrica = [];
                $productOneForMetrica['id'] = $productInCart['product']->id;
                $productOneForMetrica['name'] = $productInCart['product']->name;
                if ($productInCart['product']->price_on_request) {
                    $productOneForMetrica['price'] = 0;
                } else {
                    $productOneForMetrica['price'] = $productInCart['product']->price;
                }
                $productOneForMetrica['brand'] = $productInCart['product']->manufacturer_id;
                $productOneForMetrica['category'] = $categories[$productInCart['product']->category_id]->name;
                $productOneForMetrica['quantity'] = $productInCart['count'];
                $productsForMetrica[] = $productOneForMetrica;
            }
        }
        return $productsForMetrica;
    }

    /**
     * @param null $orderID
     * @param null $customerMail
     * @return bool|int
     * @throws Exception
     */
    public static function generateVerifyCode($orderID = null, $customerMail = null): bool|int
    {
        $result = false;
        if ($orderID) {
            if ($order = Orders::find()->where(['id' => $orderID])->select(['id', 'customer_id'])->one()) {
                $customer = Customer::findOne($order->customer_id);
            }
        } elseif ($customerMail) {
            $customer = Customer::find()->where(['mail' => $customerMail])->one();
        }
        if (isset($customer)) {
            $code = random_int(100000, 999999);
            $token = MainModel::generateToken();
            $customer->verify_code = $code;
            $customer->token = $token;
            if ($customer->save()) {
                $mailClass = new Mail();
                $mailClass->setRecipient($customer->mail);
                $mailClass->setSubject('Новый пин-код для просмотра заказов - Камин42');
                $message = '<p>Получен запрос на доступ к вашим заказам.</p>
                        <p>Ваш новый пин-код для просмотра заказов: ' . $code . '</p>
                        <p>Вставьте данный код в форму на сайте или просто перейдите по ссылке
                        <a href="https://' . $_SERVER['HTTP_HOST'] . '/orders/preview/' . $token . '">просмотр заказов</a></p>';
                $mailClass->setMessage($message);
                $result = $mailClass->send();
            }
        }
        return $result;
    }


    /**
     * @param int|null $orderID
     * @param string|null $customerMail
     * @param int|null $pin
     * @param null $token
     * @return array
     */
    #[ArrayShape(['result' => "bool", 'returnData' => "\App\Classes\ActiveRecord\Main[]|\App\Classes\ActiveRecord\Tables\Orders[]|array"])]
    public static function getOrdersPreview(int $orderID = null, string $customerMail = null, int $pin = null, $token = null): array
    {
        $returnData = ['result' => false, 'returnData' => []];
        $orders = null;
        if ($token) {
            if ($customer = Customer::find()->where(['token' => $token])->one()) {
                $orders = Orders::find()->where(['customer_id' => $customer->id])->all();
            }
        } elseif ($pin) {
            if ($orderID) {
                if (($orderTemp = Orders::find()->where(['id' => $orderID])->one()) && $customer = Customer::find()->where(['id' => $orderTemp->customer_id, 'verify_code' => $pin])->one()) {
                    $orders = [];
                    $orders[] = $orderTemp;
                }
            } elseif ($customerMail) {
                if ($customer = Customer::find()->where(['mail' => $customerMail, 'verify_code' => $pin])->one()) {
                    $orders = Orders::find()->where(['customer_id' => $customer->id])->all();
                }
            }
        }
        if ($orders) {
            foreach ($orders as $order) {
                $order->comment_hidden = '';
            }
        }

        if(!empty($orders)){
            $returnData = ['result' => true, 'returnData' => $orders];
        }
        return $returnData;
    }

    /**
     * Расчет стоимости доставки для указаного заказа
     * @param Orders $order
     * @param int $sumOrder
     * @return int
     */
    public static function getDeliveryPriceForOrder(Orders $order, int $sumOrder = 0): int
    {
        $deliveryPrice = 0;
        if ($order->delivery_id) {
            if (!$sumOrder) {
                $sumOrder = self::calculateSumOrder($order);
            }
            $deliveryDetails = DeliveryMethods::findOne($order->delivery_id);
            if ($deliveryDetails->sum_for_free > $sumOrder) {
                $deliveryPrice = $deliveryDetails->price;
            }
        }
        return $deliveryPrice;
    }

    /**
     * Расчет стоимости заказа БЕЗ учета доставки
     * @param Orders $order
     * @return int
     */
    public static function calculateSumOrder(Orders $order): int
    {
        $sum = 0;
        if ($order->products) {
            $products = $order->products;
            foreach ($products as $product) {
                if (!$product['product']['price_on_request'] && $product['product']['price'] && $product['count']) {
                    $sum += $product['product']['price'] * $product['count'];
                }
            }
        }
        if ($order->delivery_id) {
            $deliveryDetails = DeliveryMethods::findOne($order->delivery_id);
            if ($deliveryDetails->sum_for_free <= $sum) {
                $deliveryPrice = 0;
            } else {
                $deliveryPrice = $deliveryDetails->price;
            }
            $sum += $deliveryPrice;
        }

        return $sum;
    }

    /**
     * @param $orderId
     * @return array
     * @throws \YooKassa\Common\Exceptions\ApiException
     * @throws \YooKassa\Common\Exceptions\BadApiRequestException
     * @throws \YooKassa\Common\Exceptions\ForbiddenException
     * @throws \YooKassa\Common\Exceptions\InternalServerError
     * @throws \YooKassa\Common\Exceptions\NotFoundException
     * @throws \YooKassa\Common\Exceptions\ResponseProcessingException
     * @throws \YooKassa\Common\Exceptions\TooManyRequestsException
     * @throws \YooKassa\Common\Exceptions\UnauthorizedException|\YooKassa\Common\Exceptions\ExtensionNotFoundException
     */
    public static function preparePaymentYandexKassa($orderId): array
    {
        /**
         *  0 - нет ошибок;
         *  1 - нет такого заказа;
         *  2 - заказ уже оплачен;
         *  3 - заказ еще не соглосован;
         *  4 - стоимость заказа равна 0;
         *  5 - у заказа другой способ оплаты;
         *  6 - не удалось подготовить платеж;
         */
        $error = 0;

        $confirmationUrl = '';
        if (($order = Orders::findOne($orderId))) {
            if ((int)$order->payment_id !== 4) {
                $error = 5;
            }
            if ($order->status_id > 2) {
                $error = 2;// заказ уже оплачен;
            }
            if ($order->status_id < 2) {
                $error = 3;// заказ еще не соглосован;
            }
            $receiptAndSum = self::receiptYandex($order);
            if ($receiptAndSum['sum'] === 0) {
                $error = 4;// стоимость заказа равна 0;
            }

            //TODO дописать проверку на использование подарочного сертификата


            if ($error === 0) {
                if ($customer = Customer::findOne($order->customer_id)) {
                    $returnUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/orders/preview/' . $customer->token;
                } else {
                    $returnUrl = 'https://' . $_SERVER['HTTP_HOST'];
                }
                $settingYandexKassa = PaymentYandex::findOne(1);
                $client = new Client();
                $client->setAuth($settingYandexKassa->shop_id, $settingYandexKassa->secret_key);
                $payment = $client->createPayment(
                    [
                        'amount' => [
                            'value' => $receiptAndSum['sum'],
                            'currency' => 'RUB',
                        ],
                        'confirmation' => [
                            'type' => 'redirect',
                            'return_url' => $returnUrl,
                        ],
                        'receipt' => $receiptAndSum['receipt'],
                        'capture' => true,
                        'description' => 'Заказ №1' . $orderId,
                    ],
                    uniqid('', true)
                );
                if ($payment) {
                    $confirmationUrl = $payment->confirmation['confirmation_url'];
                    $order->yandex_payment_id = $payment->id;
                    $order->save();
                } else {
                    $error = 6;
                }
            }
        } else {
            $error = 1;
        }
        return ['confirmation_url' => $confirmationUrl, 'error' => $error];
    }

    /**
     * Формирует массив данных для передачи в яндекс.кассу и формирования чека онлайн.кассы
     * @param Orders $order
     * @return array['receipt', 'sum']
     */
    public static function receiptYandex(Orders $order): array
    {
        if ($order->customer_id && $customer = Customer::findOne($order->customer_id)) {
            $receipt = [
                'customer' => [
                    'full_name' => $customer->name,
                    'email' => $customer->mail,
                    'phone' => $customer->phone
                ]
            ];
        } else {
            $receipt = [
                'customer' => [
                    'full_name' => 'Покупатель',
                    'email' => 'kostyalinks@gmail.com',
                    'phone' => ''
                ]
            ];
        }

        //скидка по промокоду
        $promocodeDiscountBalance = 0; // чтобы не ругался анализатор
        $promocodeUsed = $order->promocode_used;
        if (isset($promocodeUsed['amount']) && $promocodeUsed['unit'] !== 'percent') {
            $promocodeDiscountBalance = (int)$promocodeUsed['amount'];
        }

        $sum = 0;
        $items = [];
        $sumMinusPromocodeDiscount = 0;
        if ($order->products) {
            $products = $order->products;
            foreach ($products as $product) {
                if (!$product['product']['price_on_request'] && $product['product']['price'] && $product['count']) {
                    //
                    $priceNew = $product['product']['price'];

                    //если скидка по промокоду в процентах
                    if (isset($promocodeUsed['amount']) && $promocodeUsed['unit'] === 'percent') {
                        $priceNew = $priceNew - ($priceNew * ($promocodeUsed['amount'] / 100));
                    }
                    //
                    $sum += $priceNew * $product['count'];
                    $items[] = [
                        "description" => $product['product']['name'],
                        "quantity" => $product['count'] * 1.00,
                        "amount" => array(
                            "value" => $priceNew * 1.00,
                            "currency" => "RUB"
                        ),
                        "vat_code" => "4",
                        "payment_mode" => "full_prepayment",
                        "payment_subject" => "commodity"
                    ];
                }
            }
            //если скидак по промокоду в сумме, а не процентах
            if (isset($promocodeUsed['amount']) && $promocodeUsed['unit'] !== 'percent') {
                //вычисляем процентное соотношение цены товара к общей стоимости всех товаров;
                $percentToSum = [];
                foreach ($items as $keyItem => $item) {
                    $percentToSum[$keyItem] = $item['amount']['value'] / ($sum / 100); // процент от общей суммы заказа
                }
                foreach ($percentToSum as $keyPercent => $percentToSumItem) {
                    $items[$keyPercent]['amount']['value'] = $items[$keyPercent]['amount']['value'] - $promocodeDiscountBalance / 100 * $percentToSumItem; // процент от общей суммы заказа
                    $items[$keyPercent]['amount']['value'] = floor($items[$keyPercent]['amount']['value']);
                    if ($items[$keyPercent]['amount']['value'] < 0) {
                        $items[$keyPercent]['amount']['value'] = 0;
                    }
                    $sumMinusPromocodeDiscount += $items[$keyPercent]['amount']['value'];
                }
                $sum = $sumMinusPromocodeDiscount;
            }
        }

        //Стоимость доставки
        if ($order->price_delivery) {
            $deliveryDetails = DeliveryMethods::findOne($order->delivery_id);
            $deliveryName = $deliveryDetails->name;
            $deliveryPrice = $order->price_delivery * 1.00;
            $items[] = [
                "description" => $deliveryName,
                "quantity" => 1.00,
                "amount" => array(
                    "value" => $deliveryPrice,
                    "currency" => "RUB"
                ),
                "vat_code" => "4",
                "payment_mode" => "full_prepayment",
                "payment_subject" => "service"
            ];
        }
        $receipt['items'] = $items;

        return ['receipt' => $receipt, 'sum' => $sum];
    }

    /**
     * Расчет стоимости заказа С учетом доставки
     * @param Orders $order
     * @return int
     */
    public static function calculateSumOrderWithDelivery(Orders $order): int
    {
        $sum = self::calculateSumOrder($order);
        $sum += $order->price_delivery ?: 0;
        return $sum;
    }
}