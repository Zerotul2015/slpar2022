<?php


namespace App\Model\Admin\Product;


use App\Classes\ActiveRecord\Tables\ProductManufacturer;
use App\Model\Admin\MainModel;
use App\Model\Interfaces\DefaultMethodTableClass;
use JetBrains\PhpStorm\ArrayShape;

class ProductManufacturerModel implements DefaultMethodTableClass
{

    /**
     * @param $val
     * @return array
     */
    #[ArrayShape(['result' => "bool", 'id' => "mixed|null", 'returnData' => "\App\Classes\ActiveRecord\Main|\App\Classes\ActiveRecord\Tables\ProductManufacturer|null", 'error' => "string"])]
    public static function Save($val): array
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null];
        if (empty($val['name'])) {
            $returnResult['error'] = 'Поля "Наименование" обязательно для заполнения';
        } else {
            if (isset($val['id']) && $val['id'] && $itemOld = ProductManufacturer::findOne($val['id'])) {
                $imageOld = $itemOld->image ?? '';
                $folder = $itemOld->folder;
            } else {
                $val['id'] = '';
                $imageOld = '';
                $folder = uniqid('', true) . 'fldr' . time();
            }
            $image = $val['image'] ?? '';
            $val['folder'] = $folder;
            $val['image'] = MainModel::prepareImageBeforeSave($image, $imageOld, 'manufacturers/' . $folder);
            $val['url'] = MainModel::urlGeneration($val['name'], ProductManufacturer::find(), $val['id']);
            $item = ProductManufacturer::create()->set($val);
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
    #[ArrayShape(['result' => "bool"])] public static function Delete($id): array
    {
        $result = false;
        if ($item = ProductManufacturer::findOne($id)) {
            if ($result = $item->del()) {
                if (!empty($item->image)) {
                    MainModel::prepareImageBeforeSave('', $item->image, 'manufacturers/' . $item->folder);
                }
            }
        }
        return ['result' => $result];
    }
}