<?php


namespace App\Model\Admin\BathStyle;


use App\Classes\ActiveRecord\Tables\BathStyle;
use App\Model\Interfaces\DefaultMethodTableClass;

class BathStyleModel implements DefaultMethodTableClass
{

    /**
     * @param $val
     * @return array
     */
    public static function Save($val): array
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null];
        if (!isset($val['name'])) {
            $returnResult['error'] = 'Поле "Наименование" обязательно для заполнения';
        } else {
            $item = BathStyle::create()->set($val);
            $resultSave = $item->save();
            if (!$item->id) {
                $item->id = $resultSave;
            }
            $returnResult['id'] = $item->id;
            if ($resultSave) {
                $returnResult['result'] = true;
            }
            $returnResult['returnData'] = $item;
        }
        return $returnResult;
    }

    /**
     * @param $id
     * @return false[]
     */
    public static function Delete($id): array
    {
        $result = false;
        if ($item = BathStyle::findOne($id)) {
            $result = $item->del();
        }
        return ['result' => $result];
    }
}