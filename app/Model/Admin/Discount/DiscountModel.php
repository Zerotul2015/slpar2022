<?php


namespace App\Model\Admin\Discount;


use App\Classes\ActiveRecord\Tables\Discount;
use App\Model\Interfaces\DefaultMethodTableClass;
use JetBrains\PhpStorm\ArrayShape;

class DiscountModel implements DefaultMethodTableClass
{

    /**
     * @param $val
     * @return array
     */

    #[ArrayShape(['result' => "bool", 'error' => "array", 'returnData' => "\App\Classes\ActiveRecord\Main|\App\Classes\ActiveRecord\Tables\Discount", 'id' => "mixed"])]
    public static function Save($val): array
    {
        $returnResult = ['result' => false];
        $itemOld = false;
        if (isset($val['id']) && $val['id']) {
            $itemOld = Discount::findOne($val['id']);
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
            $itemSave = Discount::create($val);
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

        $availableValues = [
            'type' =>
                ['count' => 'count', 'sum'=>'sum'],
            'unit' =>
                ['percent' => 'percent', 'rub' => 'rub']
        ];
        $availableUnit = ['unit' => 'count'];
        if (!isset($newItem['type'], $availableValues['type'][$newItem['type']])) {
            $errors[] = 'Выберите тип скидки!';
        }

        if (!isset($newItem['unit'])) {
            $errors[] = 'Выберите ед. измерения для скидки!';
        } else {
            if (!isset($availableValues['unit'][$newItem['unit']])) {
                $errors[] = 'Скидка может быть только в % или рублях!';
            }
        }

        if (!isset($newItem['amount'])) {
            $errors[] = 'Размер скидки не указан!';
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
        if ($item = Discount::findOne($id)) {
            $result = $item->del();
        }
        return ['result' => $result];
    }
}