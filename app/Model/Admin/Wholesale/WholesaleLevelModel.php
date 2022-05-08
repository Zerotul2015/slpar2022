<?php

namespace App\Model\Admin\Wholesale;

use App\Classes\ActiveRecord\Tables\WholesaleLevel;
use JetBrains\PhpStorm\ArrayShape;

class WholesaleLevelModel
{
    /**
     * @param $val
     * @return array
     */

    #[ArrayShape(['result' => "bool", 'error' => "array", 'returnData' => "\App\Classes\ActiveRecord\Main|\App\Classes\ActiveRecord\Tables\WholesaleLevel", 'id' => "mixed"])]
    public static function Save($val): array
    {
        $returnResult = ['result' => false];
        $itemOld = false;
        if (isset($val['id']) && $val['id']) {
            $itemOld = WholesaleLevel::findOne($val['id']);
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
            $itemSave = WholesaleLevel::create($val);
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

        if (empty($newItem['discount_default'])) {
            $errors['discount_default'] = 'Укажите скидку по умолчанию!';
        }
        if (!isset($newItem['min_sum_orders_to_enter'])) {
            $errors['min_sum_orders_to_enter'] = 'Минимальная сумма заказов для получения категории не заполнена!';
        }
        if (empty($newItem['name'])) {
            $errors['name'] = 'Название не может быть пустым!';
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
        if ($item = WholesaleLevel::findOne($id)) {
            $result = $item->del();
        }
        return ['result' => $result];
    }
}