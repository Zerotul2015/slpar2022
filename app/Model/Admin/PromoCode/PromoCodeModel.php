<?php


namespace App\Model\Admin\PromoCode;


use App\Classes\ActiveRecord\Tables\PromoCode;
use App\Model\Interfaces\DefaultMethodTableClass;
use JetBrains\PhpStorm\ArrayShape;

class PromoCodeModel implements DefaultMethodTableClass
{

    /**
     * @param $id
     * @return false[]
     */

    #[ArrayShape(['result' => "bool"])]
    public static function Delete($id): array
    {
        $result = false;
        if ($item = PromoCode::findOne($id)) {
            $result = $item->del();
        }
        return ['result' => $result];
    }

    #[ArrayShape(['result' => "bool", 'error' => "array", 'returnData' => "\App\Classes\ActiveRecord\Main|\App\Classes\ActiveRecord\Tables\PromoCode", 'id' => "mixed"])]
    public static function Save($val): array
    {
        $returnResult = ['result' => false];
        $itemOld = false;
        if (isset($val['id']) && $val['id']) {
            $itemOld = PromoCode::findOne($val['id']);
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
            $itemSave = PromoCode::create($val);
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
     * Проверка на активность
     * @param PromoCode $promoCode
     * @return bool
     */
    public static function activityCheck(PromoCode $promoCode): bool
    {
        $active = true;
        $now = time();

        if ($promoCode->date_start) {
            $start = strtotime($promoCode->date_start);
            if ($start > $now) {
                $active = false;
            }
        }
        if ($promoCode->date_end) {
            $end = strtotime($promoCode->date_end);
            if ($end <= $now) {
                $active = false;
            }
        }

        return $active;
    }
}