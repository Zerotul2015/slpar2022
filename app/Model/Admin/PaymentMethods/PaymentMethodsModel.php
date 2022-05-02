<?php

namespace App\Model\Admin\PaymentMethods;

use App\Classes\ActiveRecord\Tables\PaymentMethods;
use JetBrains\PhpStorm\ArrayShape;

class PaymentMethodsModel
{
    /**
     * @param $val
     * @return array
     */

    #[ArrayShape(['result' => "bool", 'error' => "array", 'returnData' => "\App\Classes\ActiveRecord\Main|\App\Classes\ActiveRecord\Tables\PaymentMethods", 'id' => "mixed"])]
    public static function Save($val): array
    {
        $returnResult = ['result' => false];
        $itemOld = false;
        if (isset($val['id']) && $val['id']) {
            $itemOld = PaymentMethods::findOne($val['id']);
        } else {
            $val['id'] = '';
        }
        if ($itemOld) {
            //обработчики старых значений на случай необходимости
            $val['protected'] = $itemOld->protected ?: 0;
            $val['protected_name'] = $itemOld->protected_name ?: '';
        } else {
            //обработчики новых значений на случай необходимости
        }

        //проверка значений
        $errors = self::checkValues($val);
        if (empty($errors)) {
            $itemSave = PaymentMethods::create($val);
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
        if ($item = PaymentMethods::findOne($id)) {
            if (!$item->protected) {
                $result = $item->del();
            }
        }
        return ['result' => $result];
    }
}