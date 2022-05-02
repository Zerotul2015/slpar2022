<?php


namespace App\Model\Admin\Orders;


use App\Classes\ActiveRecord\Tables\OrdersStatus;
use JetBrains\PhpStorm\ArrayShape;

class OrdersStatusModel implements \App\Model\Interfaces\DefaultMethodTableClass
{

    /**
     * @param $val
     * @return array
     */

    #[ArrayShape(['result' => "bool", 'error' => "array", 'returnData' => "\App\Classes\ActiveRecord\Main|\App\Classes\ActiveRecord\Tables\OrdersStatus", 'id' => "mixed"])]
    public static function Save($val): array
    {
        $returnResult = ['result' => false];
        $itemOld = false;
        if (isset($val['id']) && $val['id']) {
            $itemOld = OrdersStatus::findOne($val['id']);
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
            $itemSave = OrdersStatus::create($val);
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

    public static function Delete($id)
    {
        // TODO: Implement Delete() method.
    }
}