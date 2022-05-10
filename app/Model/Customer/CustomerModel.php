<?php

namespace App\Model\Customer;

use App\Classes\ActiveRecord\Tables\Customer;
use App\Classes\ActiveRecord\Tables\WholesaleCustomer;
use App\Classes\PhoneParse;
use App\Controllers\Authorization;
use App\Model\Notification\NotificationMailModel;
use JetBrains\PhpStorm\ArrayShape;
use libphonenumber\ValidationResult;

class CustomerModel
{
    #[ArrayShape(['result' => "false", 'returnData' => "array|string", 'error' => "string"])]
    public static function getProfile(): array
    {
        $returnData = ['result' => false, 'returnData' => '', 'error' => ''];
        $checkData = Authorization::isAuth();
        if ($checkData['result'] === true) {
            $customer = Customer::find()
                ->where(['id' => $checkData['customerId']])
                ->select(['id', 'name', 'phone', 'mail', 'status', 'is_wholesale'])
                ->one();
            $wholesaleDetails = [];
            if ($customer->is_wholesale) {
                $wholesale = WholesaleCustomer::find()->where(['customer_id' => $customer->id])->one();
                $wholesaleDetails = $wholesale->details;
                $wholesaleDetails['levelId'] = $wholesale->wholesale_level_id;
            }
            $returnData['returnData'] = [
                'customer' => $customer,
                'wholesale' => $wholesaleDetails
            ];
        } else {
            $returnData['error']['access'] = 'Доступ запрещен.';
        }
        return $returnData;
    }

    #[ArrayShape(['result' => "false", 'returnData' => "array|string", 'error' => "string"])]
    public static function changeProfile($customerNewData, $wholesaleNewData): array
    {
        $returnData = ['result' => false, 'returnData' => '', 'error' => ''];
        $checkData = Authorization::isAuth();
        if ($checkData['result'] === true) {
            $customer = Customer::findOne($checkData['customerId']);
            //хешируем новый пароль если был передан, иначе оставляем старый
            if (isset($customerNewData['pass']) && !empty($customerNewData['pass'])) {
                $customerNewData['pass'] = self::changePass($customerNewData['pass']);
            }
            $customer->set($customerNewData);
            if (!$customer->save()) {
                $returnData['error']['customer'] = 'Не удалось сохранить данные покупателя.';
            }
            $wholesale = WholesaleCustomer::find()->where(['customer_id' => $customer->id])->one();
            $wholesale->details = $wholesaleNewData;
            if (!$wholesale->save()) {
                $returnData['error']['wholesale'] = 'Не удалось сохранить данные дилера.';
            }
            $returnData['result'] = !isset($returnData['error']['wholesale'], $returnData['error']['customer']);
        } else {
            $returnData['error']['access'] = 'Доступ запрещен.';
        }
        return $returnData;
    }

    private static function changePass($passString): string
    {
        return password_hash($passString, PASSWORD_DEFAULT);
    }


    #[ArrayShape(['result' => "false|mixed", 'returnData' => "string", 'error' => "string"])]
    public static function registerRequestWholesale($requestDataForm): array
    {
        $returnData = ['result' => false, 'returnData' => '', 'error' => ''];
        if (isset($requestDataForm['name'], $requestDataForm['phone'], $requestDataForm['mail'], $requestDataForm['company'])) {
            $sendResult = NotificationMailModel::notificationRegisterRequest($requestDataForm);
            $returnData['result'] = $sendResult['result'];
        }
        return $returnData;
    }

    public static function registerCustomer($requestDataForm): array
    {
        $returnData = ['result' => false, 'returnData' => '', 'error' => ''];
        $errors = [];

        if (!empty($requestDataForm['phone'])) {
            $resultParse = PhoneParse::parsePhoneString($requestDataForm['phone']);
            if (empty($resultParse['error']) && !empty($resultParse['phone'])) {
                $requestDataForm['phone'] = $resultParse['phone'];
            } else {
                $errors['phone'] = $resultParse['error'];
            }
        } else {
            $errors['phone'] = 'Введите номер телефона.';
        }
        if (empty($requestDataForm['mail']) || filter_var($requestDataForm['mail'], FILTER_VALIDATE_EMAIL) === false) {
            $errors['mail'] = 'Введите почту.';
        }
        if (empty($requestDataForm['name'])) {
            $errors['mail'] = 'Введите почту.';
        }

        if (empty($errors)) {

        }

        return $returnData;
    }


    /**
     * Проверка на то что нет покупателя с таким телефоном, почтой
     * @param string|null $mail
     * @param string|null $phone
     * @return array
     */
    #[ArrayShape(['result' => "bool", 'returnData' => "false[]", 'error' => "array|null"])]
    public static function checkAlreadyRegistered(string $mail = null, string $phone = null): array
    {
        $returnData = ['result' => false, 'returnData' => ['mailUsed' => false, 'phoneUsed' => false], 'error' => null];
        $errors = [];
        if ($mail) {
            if (filter_var($mail, FILTER_VALIDATE_EMAIL) !== false) {
                $customerAlreadyExit = Customer::find()->where(['mail' => $mail])->one();
                $returnData['returnData']['mailUsed'] = (bool)$customerAlreadyExit;
            } else {
                $errors['mail'] = 'Почтовый ящик введен с ошибкой.';
            }
        }
        if ($phone) {
            $resultParsePhone = PhoneParse::parsePhoneString($phone);
            if($resultParsePhone['result'] === true){
                $phone =  $resultParsePhone['phone'];
                $customerAlreadyExit = Customer::find()->where(['phone' => $phone])->one();
                $returnData['returnData']['phoneUsed'] = (bool)$customerAlreadyExit;
            }else{
                $errors['phone'] = 'Телефон указан с ошибкой.';
            }
        }
        if (empty($errors)) {
            $returnData['result'] = true;
        } else {
            $returnData['error'] = $errors;
        }
        return $returnData;
    }
}