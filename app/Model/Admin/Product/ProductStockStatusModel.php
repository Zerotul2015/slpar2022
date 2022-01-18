<?php


namespace App\Model\Admin\Product;


use App\Classes\ActiveRecord\Tables\ProductStockStatus;
use App\Model\Interfaces\DefaultMethodTableClass;

class ProductStockStatusModel implements DefaultMethodTableClass
{

    /**
     * @param $val
     * @return array
     */
    public static function Save($val): array
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null];
        if (!isset($val['name'], $val['description'], $val['delivery_time'])
            || empty($val['name']) || empty($val['description'])) {
            $returnResult['error'] = 'Все обязательны для заполнения';
        } else {
            $val['delivery_time'] = $val['delivery_time'] ?: 0;
            $item = ProductStockStatus::create()->set($val);
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
        if ($item = ProductStockStatus::findOne($id)) {
            $result = $item->del();
        }
        return ['result' => $result];
    }
}