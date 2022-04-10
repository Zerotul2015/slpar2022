<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 03.06.18
 * Time: 19:52
 */

namespace App\Controllers\Api;


use App\Classes\ActiveRecord\Tables\DeliveryMethods;
use App\Classes\ActiveRecord\Tables\Orders;
use App\Classes\ActiveRecord\Tables\PaymentMethods;
use App\Classes\ActiveRecord\Tables\Products;
use App\Classes\ActiveRecord\Tables\ProductsCategory;
use App\Classes\ActiveRecord\Tables\ProductsStockStatus;
use App\Classes\ActiveRecord\Tables\ProductsWishlist;
use App\Classes\ActiveRecord\Tables\SettingsProductPage;
use App\Classes\ActiveRecord\Tables\Users;
use App\Classes\Mail;
use App\Model\Admin\Certificates\CertificatesModel;
use App\Model\Admin\Orders\OrdersMainModel;
use App\Model\Catalog\CatalogCompareModel;
use App\Model\Catalog\CatalogModel;
use App\Model\Cart\CartModel;
use App\Model\Orders\OrdersModel;
use App\Model\Product\ProductsModel;
use App\Model\Search\SearchModel;
use App\Model\Visitors\Shop\ShopModel;
use App\Model\Visitors\Shop\Wishlist\WishlistModel;
use Exception;
use JsonException;
use Model\Notification\NotificationSucceededTest;
use Model\Notification\NotificationWaitingForCaptureTest;
use YandexCheckout\Common\Exceptions\ApiException;
use YandexCheckout\Common\Exceptions\BadApiRequestException;
use YandexCheckout\Common\Exceptions\ForbiddenException;
use YandexCheckout\Common\Exceptions\InternalServerError;
use YandexCheckout\Common\Exceptions\NotFoundException;
use YandexCheckout\Common\Exceptions\ResponseProcessingException;
use YandexCheckout\Common\Exceptions\TooManyRequestsException;
use YandexCheckout\Common\Exceptions\UnauthorizedException;
use YandexCheckout\Model\Notification\NotificationSucceeded;
use YandexCheckout\Model\Notification\NotificationWaitingForCapture;
use YandexCheckout\Model\NotificationEventType;

class Json // старый метод, вместо не го используется GetData.
{
    public $postData = [];
    public $limit = 20;
    private $where = [];


    public function __construct()
    {
        $this->postData = json_decode(file_get_contents("php://input"), true);
        if (isset($this->postData['limit']) && is_int($this->postData['limit'])) {
            $this->limit = $this->postData['limit'];
        }
        if (isset($this->postData['where']) && is_array($this->postData['where'])) {
            $this->where = $this->postData['where'];
        }
    }

    /**
     *Возвращает массив филтров для указаной категории
     * Дополнительно если передан параметр urlString, вернет urlData перевднный в массив запрос из urlString
     *
     */
    public function getFiltersForCategory()
    {
        $returnArray = ['result' => false, 'filters' => []];
        $this->postData = json_decode(file_get_contents("php://input"), true);
        if (isset($this->postData['category'])) {
            $urlData = [];
            if (isset($this->postData['urlString'])) {
                $getQueryString = parse_url($this->postData['urlString'], PHP_URL_QUERY);
                parse_str($getQueryString, $urlData);
            }


            $filtersAll = CatalogModel::getFilters($this->postData['category'], $urlData);
            $returnArray = [
                'result' => true,
                'filters' => $filtersAll['filters'],
                'manufacturers' => $filtersAll['manufacturers'],
                'urlData' => $urlData
            ];

        }
        $this->returnAnswer($returnArray);
    }

    private function returnAnswer($returnArray)
    {
        header('Content-Type: application/json');
        echo json_encode($returnArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    }

    public function getProductsWithFilters()
    {
        $returnArray = ['result' => false, 'productsData' => []];
        $this->postData = json_decode(file_get_contents("php://input"), true);
        if (isset($this->postData['category'])) {
            $categoryID = $this->postData['category'];
            $filtersQuery = isset($this->postData['filters']) ? $this->postData['filters'] : [];
            $queryArray = isset($this->postData['queryArray']) ? $this->postData['queryArray'] : [];

            if (!empty($queryArray)) {
                $getQueryString = parse_url($queryArray, PHP_URL_QUERY);
                parse_str($getQueryString, $queryArray);
            }
            $queryArray = array_merge($queryArray, $filtersQuery);

            $sortSettings = isset($this->postData['sortSettings']) ? $this->postData['sortSettings'] : null;

            if ($productsData = CatalogModel::getProductsRecursiveWithFilter($categoryID, $queryArray, $sortSettings)) {
                $returnArray['result'] = true;
                $returnArray['productsData'] = $productsData;

                if (isset($this->postData['sortSettings'])) {
                    switch ($this->postData['sortSettings']) {
                        case 'priceDown':
                            $queryArray['sortBy'] = 'price';
                            $queryArray['sortOrder'] = 'DESC';
                            break;
                        case 'priceUp':
                            $queryArray['sortBy'] = 'price';
                            $queryArray['sortOrder'] = 'ASC';
                            break;
                        case 'name':
                            $queryArray['sortBy'] = 'name';
                            $queryArray['sortOrder'] = 'ASC';
                            break;
                        default:
                            $queryArray['sortBy'] = 'priority';
                            $queryArray['sortOrder'] = 'DESC';
                            break;
                    }
                }
                $returnArray['uriString'] = http_build_query($queryArray);
            }
        }
        $this->returnAnswer($returnArray);
    }

    public function getProductOffer()
    {
        $returnArray = ['result' => false, 'offer' => ''];
        if (isset($this->postData['id'])) {
            if ($settingsProductPage = SettingsProductPage::findOne(1)) {
                if (isset($settingsProductPage->offers[$this->postData['id']])) {
                    $returnArray['offer'] = $settingsProductPage->offers[$this->postData['id']];
                    $returnArray['result'] = true;
                }
            }
            $this->returnAnswer($returnArray);
        }
    }

    public function getCategoryForProduct()
    {
        $returnArray = ['result' => false, 'category' => ['name' => 'не указан']];
        if (isset($this->postData['id'])) {
            if ($returnData = ProductsCategory::findOne($this->postData['id'])) {
                $returnArray = ['result' => true, 'category' => $returnData];
            }
            $this->returnAnswer($returnArray);
        }
    }

    /**
     *Возращает товары с указаным(и) id
     */
    public function getProducts():void{
        $returnArray = ['result' => false, 'values' => false];
        if (isset($this->postData['id']) && !empty($this->postData['id'])) {
            if ($returnData = CatalogModel::getProductsPrepared($this->postData['id'])) {
                $returnArray = ['result' => true, 'values' => $returnData];
            }
            $this->returnAnswer($returnArray);
        }
    }
    //////////Методы для сравнения товара
    public
    function compareAdd()
    {
        $returnArray['result'] = false;
        if (isset($this->postData['product']) && isset($this->postData['category'])) {
            $returnArray['compare'] = CatalogCompareModel::addCompare($this->postData['category'], $this->postData['product']);
            $returnArray['result'] = true;
        }
        $this->returnAnswer($returnArray);
    }

    public
    function compareRemove(): void
    {
        $returnArray['result'] = false;
        if (isset($this->postData['product'], $this->postData['category'])) {
            $returnArray['compare'] = CatalogCompareModel::removeCompare($this->postData['category'], $this->postData['product']);
            $returnArray['result'] = true;
        }
        $this->returnAnswer($returnArray);
    }

    public
    function compareClean(): void
    {
        $returnArray['result'] = CatalogCompareModel::cleanCompare();
        $this->returnAnswer($returnArray);
    }

    public
    function compareGet(): void
    {
        $returnArray['result'] = true;
        if(isset($this->postData['prepared']) && $this->postData['prepared']){
            $returnArray['compare'] = CatalogCompareModel::getComparePrepared();
        }else{
            $returnArray['compare'] = CatalogCompareModel::getCompare();
        }

        $this->returnAnswer($returnArray);
    }

    //////////Конец Методы для сравнения товара

    public
    function checkRecaptcha(): void
    {

        $answer = ['success' => false];
        $data = json_decode(file_get_contents("php://input"), true, 512);
        if (isset($data['recaptchaResponse'])) {
            $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptcha_secret = '6LfVUJUUAAAAALp1BrsZRo46Nm8MaBBd1ELyxssv';
            $recaptcha_response = $data['recaptchaResponse'];
            $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
            $answer = json_decode($recaptcha);
        }
        static::setHeaderJson();
        $this->returnAnswer($answer);
    }

    private
    static function setHeaderJson(): void
    {
        header('Content-Type: application/json');
        //TODO раскоментировать когда буду публиковать финальную версию
        //header('Access-Control-Allow-Origin: https://' . $_SERVER['SERVER_NAME']);
    }

    /**
     * Отправляем сообщение запроса обратного звонка администратору
     */
    public
    function backCallSend(): void
    {
        if (isset($this->postData['phone'])) {
            $message = '';
            if (isset($this->postData['from'])) {
                $fromPage = $this->postData['from'];
                $message .= "<p>Запрос звонка был сделан со страницы: $fromPage</p>";
            }
            $message .= '<p>Телефон:<br>' . htmlspecialchars($this->postData['phone']) . '</p>';
            if (isset($this->postData['time']) && !empty($this->postData['time'])) {
                $message .= '<p>Указаное время для звонка:<br>' . htmlspecialchars($this->postData['time']) . '</p>';
            }
            $mailClass = new Mail();
            $mailClass->setSubject('Обратный звонок - Камин42');
            $mailClass->setMessage($message);
            $this->returnAnswer(['result' => true]);
            session_write_close();
            fastcgi_finish_request();
            $result = $mailClass->send();
        } else {
            $this->returnAnswer(['result' => true]);
        }
    }

    /**
     * Отправляем сообщение с формы обратной связи администратору
     * @throws JsonException
     */
    public
    function feedbackSend(): void
    {
        $answer = ['result' => false];
        $data = json_decode(file_get_contents("php://input"), true, 512);
        if (isset($data['message']) && $data['message']) {
            $message = '';
            if (isset($data['from'])) {
                $fromPage = htmlspecialchars($data['from']);
                $message .= "<p>Сообщение было отправлено со страницы: '$fromPage'</p>";
            }
            if (isset($data['name']) && $data['name'] && is_string($data['name'])) {
                $message .= '<p>Имя: ' . htmlspecialchars($data['name']) . '</p>';
            }
            if (isset($data['contact']) && $data['contact'] && is_string($data['contact'])) {
                $message .= '<p>Контактные данные: ' . htmlspecialchars($data['contact']) . '</p>';
            }
            $message .= '<p>Текст сообщения:<br>' . htmlspecialchars($data['message']) . '</p>';
            $mailClass = new Mail();
            $mailClass->setSubject('Форма обратной связи - Камин42');
            $mailClass->setMessage($message);
            $answer['result'] = 1;
            static::setHeaderJson();
            $this->returnAnswer($answer);
            session_write_close();
            fastcgi_finish_request();
            $mailClass->send();

        }


    }

    //пока нигде не используется

    /**
     * Проверяем что логин не занят.
     * В качестве параметра post зарос с ключами login и id(не обязателен)
     * @throws JsonException
     */
    public
    function checkAvailableLogin()
    {
        $answer = ['result' => false];
        $data = json_decode(file_get_contents("php://input"), true, 512);
        if (isset($data['login'])) {
            $login = $data['login'];
            if (isset($data['id'])) {
                $id = $data['id'];
            } else {
                $id = false;
            };
            //TODO испрваить Users на нужный класс
            $found = Users::find()->where(['login' => $login])->one();


            if ($found && $found->id == $id) {
                $answer['result'] = true;
            } elseif ($found == false) {
                $answer['result'] = true;
            }
            static::setHeaderJson();
            $this->returnAnswer($answer);
        }
    }

    /**
     * Возвращает массив включеных способов оплаты
     */
    public
    function getPaymentsMethods(): void
    {
        $result = false;
        if ($payments = PaymentMethods::find()->where(['enable' => 1])->select(['id', 'name', 'description'])->indexBy()->all()) {
            $result = true;
        }
        $returnArray = ['result' => $result, 'payments' => $payments];
        $this->returnAnswer($returnArray);
    }

    /**
     * Возвращает массив включеных способов доставки
     */
    public
    function getDeliveryMethods(): void
    {
        $result = false;
        if ($delivery = DeliveryMethods::find()->where(['enable' => 1])->indexBy()->all()) {
            $result = true;
        }
        $returnArray = ['result' => $result, 'delivery' => $delivery];
        $this->returnAnswer($returnArray);
    }

    /**
     * Возвращает массив статусов наличия товара упорядоченнхы по сроку
     */
    public function getStockStatus(): void
    {
        $result = false;
        if ($stockStatus = ProductsStockStatus::find()->sort('DESC')->orderBy('delivery_time')->all()) {
            $result = true;
        }
        $returnArray = ['result' => $result, 'stockStatus' => $stockStatus];
        $this->returnAnswer($returnArray);
    }

    /**
     * Возвращает упрощенный варинат корзины. У товара указан только id и quantity
     * @throws JsonException
     */
    public
    function cartGet(): void
    {
        $data = json_decode(file_get_contents("php://input"), true, 512);
        $returnArray = ['result' => false, 'cart' => []];
        if ($cart = CartModel::getCart()) {
            $returnArray = ['result' => true, 'cart' => $cart];
        }
        $this->returnAnswer($returnArray);
    }

    /**
     * Возвращает корзину, товар подготовлен к выводу в шаблон.
     * @throws JsonException
     */
    public
    function cartPreparedGet(): void
    {
        $data = json_decode(file_get_contents("php://input"), true, 512);
        $returnArray = ['result' => false, 'cart' => []];
        if ($cart = CartModel::getPreparedCart()) {
            $returnArray = ['result' => true, 'cart' => $cart];
        }
        $this->returnAnswer($returnArray);
    }

    /**
     * Возвращает сервисы связанные с товаром в корзине
     */
    public
    function cartGetServices(): void
    {
        if (isset($this->postData['needAll']) && $this->postData['needAll']) {
            $services = CartModel::getService(true);
        } else {
            $services = CartModel::getService();
        }
        if ($services) {
            $returnArray = ['result' => true, 'services' => $services];
        } else {
            $returnArray = ['result' => true, 'services' => []];
        }

        $this->returnAnswer($returnArray);
    }

    /**
     * Добавляем товар к в корзину
     */
    public
    function cartAdd(): void
    {
        $data = json_decode(file_get_contents("php://input"), true, 512);
        $returnArray = ['result' => false, 'cart' => []];
        if (isset($data['id'])) {
            $id = $data['id'];
            $optionSelected = $data['optionSelected'] ?? null;
            $quantity = $data['quantity'] ?? 1;
            $returnArray = CartModel::addProduct($id, $quantity, $optionSelected);
        }
        $this->returnAnswer($returnArray);
    }

    /**
     * Добавляем подарочный сертификат в корзину
     */
    public function addCertificate()
    {
        $answer = ['result' => false];
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['certificate'])) {
            $certificate = $data['certificate'];
            $resultAdd = \App\Model\Cart\CartModel::addCertificateToCart($certificate);
            if ($resultAdd) {
                $answer['result'] = true;
            } else {
                $answer['result'] = false;
            }
        }
        $this->returnAnswer($answer);
    }

    /**
     * Убираем подарочный сертификат из корзину
     */
    public function cartRemoveCertificate()
    {
        $answer = ['result' => false];
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'])) {
            $certificateKey = $data['id'];
            $resultAdd = \App\Model\Cart\CartModel::deleteCertificationInCart($certificateKey);
            if ($resultAdd) {
                $answer['result'] = true;
            } else {
                $answer['result'] = false;
            }
        }
        $this->returnAnswer($answer);
    }

    /**
     * Меняем количество у указаного товара на переданное значение
     */
    public
    function cartChangeQuantity()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $returnArray = ['result' => false, 'cart' => []];
        if (isset($data['id'])) {
            $id = $data['id'];
            $optionSelected = $data['optionSelected'] ?? null;
            $quantity = $data['quantity'] ?? 1;
            $returnArray = CartModel::changeQuantity($id, $quantity, $optionSelected);
        }
        $this->returnAnswer($returnArray);
    }

    /**
     * Удаляем товар с переданным id из корзины
     */
    public
    function cartRemoveProduct()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $returnArray = ['result' => false, 'cart' => []];
        if (isset($data['id'])) {
            $id = $data['id'];
            $returnArray = CartModel::removeProduct($id);
        }
        $this->returnAnswer($returnArray);
    }

    /**
     * Принимаем новый заказ
     * @throws Exception
     */
    public
    function createOrder()
    {
        $result = false;
        $data = json_decode(file_get_contents("php://input"), true);
        if ($order = OrdersModel::newOrder($data)) {
            $result = true;
        }
        $this->returnAnswer(['result' => $result, 'order' => $order]);
    }

    /**
     * Принимаем новый заказ
     * @throws Exception
     */
    public
    function createQuickOrder()
    {
        $result = false;
        $data = json_decode(file_get_contents("php://input"), true);
        if ($order = OrdersModel::newOrder($data, true)) {
            $result = true;
        }
        $this->returnAnswer(['result' => $result, 'order' => $order]);
    }

    /**
     * Очищаем корзину
     */
    public
    function cartClear()
    {
        CartModel::clearCart();
        $this->returnAnswer(true);
    }

    /**
     * Возвращает количество товаров в избранном
     */
    public
    function wishlistWidget()
    {
        $returnData = ['count' => 0, 'products' => []];
        if (isset($_COOKIE['wishlist_token'])) {
            if ($returnData = ProductsWishlist::find()->where(['token' => $_COOKIE['wishlist_token']])->one()) {
                if ($returnData->products) {
                    $returnData = ['count' => count($returnData->products), 'products' => $returnData->products];
                } else {
                    $returnData = ['count' => 0, 'products' => []];
                }
            }
        }

        static::setHeaderJson();
        $this->returnAnswer($returnData);
    }

    /**
     * Добавляем товар в избраное
     *
     */
    public
    function addWishlist()
    {
        $returnData = ['count' => 0];
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'])) {
            $count = WishlistModel::addProduct($data['id']);
            $returnData = ['count' => $count];
        }
        header('Content-Type: application/json');
        $this->returnAnswer($returnData);
    }

    public
    function productDetail()
    {
        $returnData = false;
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'])) {
            $product = Products::findOne($data['id']);
            if ($product) {
                $product = ShopModel::prepareProductsForTemplate($product);
                $specificationsProduct = ShopModel::getSpecificationsForProducts($product);
                $returnData['specifications'] = $specificationsProduct;
            }
        }

        static::setHeaderJson();
        $this->returnAnswer($returnData);
    }

    /**
     * Активируем сертификат(с проверкой пин-кода и на то что он был оплачен).
     * Через post принимает id и pin
     * Возвращает остаток сертификата или false если не прошли проверку
     */
    public
    function applyPromocode()
    {
        $result = false;
        $promocode = false;
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['promocode']) && $data['promocode']) {
            $promocodeText = $data['promocode'];
            if ($promocode = CartModel::applyPromocode($promocodeText)) {
                $result = true;
            }

        }

        static::setHeaderJson();
        $this->returnAnswer(['result' => $result, 'promocode' => $promocode]);
    }

    /**
     * Активируем сертификат(с проверкой пин-кода и на то что он был оплачен).
     * Через post принимает id и pin
     * Возвращает остаток сертификата или false если не прошли проверку
     */
    public
    function applyCertificate()
    {
        $result = false;
        $certificate = false;
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id']) && isset($data['pin'])) {
            $certID = $data['id'];
            $certPIN = $data['pin'];
            if ($certID && $certPIN) {
                $certificate = CartModel::applyCertificate($certID, $certPIN);
            }
        }

        static::setHeaderJson();
        $this->returnAnswer(['result' => $result, 'certificate' => $certificate]);
    }

    public
    function recoveryCertificate()
    {
        fastcgi_finish_request(); //TODO переделать на сервер очередей
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['mail'])) {
            $mail = $data['mail'];
            if ($mail && filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                CertificatesModel::recoveryCertificate($mail);
            }
        }
    }

    public function searchRequest()
    {
        $returnArray = ['result' => false, 'searchingResults' => []];
        if (isset($this->postData['queryString']) && !empty($this->postData['queryString'])) {
            if (isset($this->postData['limit']) && !empty($this->postData['limit'])) {
                $limit = $this->postData['limit'];
            } else {
                $limit = 10;
            }
            $returnArray['result'] = true;
            $returnArray['searchingResults'] = SearchModel::searchData($this->postData['queryString'], $limit);
        }
        $this->returnAnswer($returnArray);
    }

    public function sendNewVerifyCodeCustomer()
    {
        $returnArray = ['result' => false];
        $orderID = $this->postData['orderID'] ?? null;
        $customerMail = $this->postData['mail'] ?? null;
        if (isset($this->postData['orderID']) || isset($this->postData['mail'])) {
            $returnArray['result'] = OrdersModel::generateVerifyCode($orderID, $customerMail);
        }
        $this->returnAnswer($returnArray);
    }

    public function getOrdersPreview()
    {
        $returnArray = ['result' => false, 'returnData' => false];
        $orderID = $this->postData['orderID'] ?? null;
        $customerMail = $this->postData['mail'] ?? null;
        $pin = $this->postData['pin'] ?? null;
        $token = $this->postData['token'] ?? null;
        if ((($orderID || $customerMail) && $pin) || $token) {
            $returnArray['result'] = true;
            $returnArray['returnData'] = OrdersModel::getOrdersPreview($orderID, $customerMail, $pin, $token);
        }
        $this->returnAnswer($returnArray);
    }

    /**
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ForbiddenException
     * @throws InternalServerError
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    public function getUrlPaymentYandexKassa(): void
    {
        $returnArray = ['result' => false, 'returnData' => false];
        if ($orderId = $this->postData['orderId'] ?? null) {
            $returnArray['returnData'] = OrdersModel::preparePaymentYandexKassa($orderId);
            $returnArray['result'] = $returnArray['returnData']['error'] === 0;
        }
        $this->returnAnswer($returnArray);
    }

    public function notificationsFromYandexKassa(): void
    {
        //TODO обрабатка ответов от яндекса

        try {
            $notification = ($this->postData['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
                ? new NotificationSucceeded($this->postData)
                : new NotificationWaitingForCapture($this->postData);
            $payment = $notification->getObject();
            if ($payment->status === 'succeeded') {
                if($order = Orders::find()->where(['yandex_payment_id' => $payment->id])->one()){
                    $oldStatus = $order->status_id;
                    $order->status_id = 3;
                    $order = OrdersMainModel::changeStatus($order, $oldStatus);
                    $order->save();
                }
            }
        } catch (Exception $e) {
            // Обработка ошибок при неверных данных
        }
    }
}