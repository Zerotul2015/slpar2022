<?php

namespace App\Model\Customer;

use App\Classes\ActiveRecord\Tables\Customer;
use App\Classes\ActiveRecord\Tables\WholesaleCustomer;
use App\Controllers\Authorization;
use App\Model\Notification\NotificationMailModel;
use JetBrains\PhpStorm\ArrayShape;

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


    public static function registerRequest($requestDataForm): array
    {
        $returnData = ['result' => false, 'returnData' => '', 'error' => ''];
        if (isset($requestDataForm['name'], $requestDataForm['phone'], $requestDataForm['mail'], $requestDataForm['company'])) {
            $sendResult = NotificationMailModel::notificationRegisterRequest($requestDataForm);
            $returnData['result'] = $sendResult['result'];
        }
        return $returnData;
    }
}