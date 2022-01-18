<?php


namespace App\Model\Admin\Product;


use App\Classes\ActiveRecord\Tables\ProductUnit;
use App\Model\Interfaces\DefaultMethodTableClass;

class ProductUnitModel implements DefaultMethodTableClass
{

    /**
     * @param $val
     * @return array
     */
    public static function Save($val): array
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null];
        if (!isset($val['name'], $val['symbol_national']) || (empty($val['name']) && empty($val['symbol_national']))) {
            $returnResult['error'] = 'Поля "Наименование" и "Обозначение национальное" обязательны для заполнения';
        } else {
            $item = ProductUnit::create()->set($val);
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
        if ($item = ProductUnit::findOne($id)) {
            $result = $item->del();
        }
        return ['result' => $result];
    }
}