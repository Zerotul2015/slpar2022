<?php

namespace App\Model\Admin\Wholesale;

use App\Classes\ActiveRecord\Tables\Discount;
use App\Classes\ActiveRecord\Tables\WholesaleCustomer;
use JetBrains\PhpStorm\ArrayShape;

class WholesaleCustomerModel implements \App\Model\Interfaces\DefaultMethodTableClass
{

    /**
     * @param $val
     * @return array
     */

    #[ArrayShape(['result' => "bool", 'error' => "array", 'returnData' => "\App\Classes\ActiveRecord\Main|\App\Classes\ActiveRecord\Tables\WholesaleCustomer", 'id' => "mixed"])]
    public static function Save($val): array
    {
        $returnResult = ['result' => false];
        $itemOld = false;
        if (isset($val['id']) && $val['id']) {
            $itemOld = WholesaleCustomer::findOne($val['id']);
        } else {
            $val['id'] = '';
        }
        if ($itemOld) {
            //обработчики старых значений на случай необходимости
        } else {
            //обработчики новых значений на случай необходимости
        }

        //проверка значений
        $errors = self::checkValues($val);
        if (empty($errors)) {
            $itemSave = WholesaleCustomer::create($val);
            if ($resultSave = $itemSave->save()) {
                $returnResult['id'] = $itemSave->id;
                $returnResult['result'] = true;
                $returnResult['returnData'] = $itemSave;
            }
        } else {
            $returnResult['error'] = $errors;
        }


        return $returnResult;
    }

    /**
     * Возвращает массив с текстом ошибок если они есть или пустой если нет.
     * @param $newItem
     * @return array
     */
    private static function checkValues($newItem): array
    {
        $errors = [];

        if (!isset($newItem['details']['nameCompany'])) {
            $errors['nameCompany'] = 'Не указано название организации.';
        }if (!isset($newItem['details']['addressCompany'])) {
            $errors['addressCompany'] = 'Не указан адрес.';
        }
        return $errors;
    }

    /**
     * @param $id
     * @return false[]
     */
    #[ArrayShape(['result' => "bool"])]
    public static function Delete($id): array
    {
        $result = false;
        if ($item = WholesaleCustomer::findOne($id)) {
            $result = $item->del();
        }
        return ['result' => $result];
    }
}