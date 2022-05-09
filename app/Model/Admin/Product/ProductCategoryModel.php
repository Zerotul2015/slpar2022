<?php


namespace App\Model\Admin\Product;


use App\Classes\ActiveRecord\Tables\ProductCategory;
use App\Classes\ActiveRecord\Tables\WholesaleLevel;
use App\Model\Admin\MainModel;
use App\Model\Interfaces\DefaultMethodTableClass;

class ProductCategoryModel implements DefaultMethodTableClass
{

    /**
     * @param $val
     * @return array
     */
    public static function Save($val): array
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null];
        if (!isset($val['name']) || empty($val['name'])) {
            $returnResult['error'] = 'Поля "Наименование" обязательно для заполнения';
        } else {
            if (isset($val['id']) && $val['id'] && $itemOld = ProductCategory::findOne($val['id'])) {
                $imageOld = $itemOld->image ?? '';
                $folder = $itemOld->folder ? $itemOld->folder : uniqid('', true) . 'fldr' . time();
            } else {
                $val['id'] = '';
                $imageOld = '';
                $folder = uniqid('', true) . 'fldr' . time();
            }
            $image = $val['image'] ?? '';
            $val['folder'] = $folder;
            $val['image'] = MainModel::prepareImageBeforeSave($image, $imageOld, 'categories/' . $folder);
            $val['url'] = MainModel::urlGeneration($val['name'], ProductCategory::find(), $val['id']);
            $val['wholesale_discount_size'] = static::updateWholesaleLevel($val['wholesale_discount_size']);
            $item = ProductCategory::create()->set($val);
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

    public static function updateWholesaleLevel(array|null $wholesaleDiscountSize): array
    {
        $wholesaleDiscountSizeChecked = [];
        if (!empty($wholesaleDiscountSize) && $wholesaleLevel = WholesaleLevel::find()->indexBy()->all()) {
            foreach ($wholesaleDiscountSize as $levelId => $discountSize){
                if(isset($wholesaleLevel[$levelId])){
                    $wholesaleDiscountSizeChecked[$levelId] = $discountSize;
                }
            }
        }
        return $wholesaleDiscountSizeChecked;
    }

    /**
     * @param $id
     * @return false[]
     */
    public static function Delete($id): array
    {
        $result = false;
        if ($item = ProductCategory::findOne($id)) {
            if ($result = $item->del()) {
                if (!empty($item->image)) {
                    MainModel::prepareImageBeforeSave('', $item->image, 'categories/' . $item->folder);
                }
            }
        }
        return ['result' => $result];
    }
}