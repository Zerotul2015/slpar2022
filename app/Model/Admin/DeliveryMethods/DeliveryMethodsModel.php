<?php

namespace App\Model\Admin\DeliveryMethods;

use App\Classes\ActiveRecord\Tables\DeliveryMethods;
use JetBrains\PhpStorm\ArrayShape;

class DeliveryMethodsModel
{
    /**
     * @param $val
     * @return array
     */

    #[ArrayShape(['result' => "bool", 'error' => "array", 'returnData' => "\App\Classes\ActiveRecord\Main|\App\Classes\ActiveRecord\Tables\DeliveryMethods", 'id' => "mixed"])]
    public static function Save($val): array
    {
        $returnResult = ['result' => false];
        $itemOld = false;
        if (isset($val['id']) && $val['id']) {
            $itemOld = DeliveryMethods::findOne($val['id']);
        } else {
            $val['id'] = '';
        }
        if ($itemOld) {
            //обработчики старых значений на случай необходимости
            $val['protected'] = $itemOld->protected ?: 0;
        } else {
            //обработчики новых значений на случай необходимости
        }

        //проверка значений
        $errors = self::checkValues($val);
        if (empty($errors)) {
            $itemSave = DeliveryMethods::create($val);
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
        if ($item = DeliveryMethods::findOne($id)) {
            if (!$item->protected) {
                $result = $item->del();
            }
        }
        return ['result' => $result];
    }
}