<?php


namespace App\Model\Admin\Product;


use App\Classes\ActiveRecord\Tables\BathStyle;
use App\Classes\ActiveRecord\Tables\Product;
use App\Classes\ActiveRecord\Tables\ProductCategory;
use App\Model\Admin\MainModel;
use App\Model\Interfaces\DefaultMethodTableClass;

class ProductModel implements DefaultMethodTableClass
{
    /**
     * @param $val
     * @return array
     */
    public static function Save($val): array
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null];
        if (!isset($val['name']) || empty($val['name'])) {
            $returnResult['error'] = 'Поля "Название" обязательно для заполнения';
        } else {
            if (isset($val['id']) && $val['id'] && $itemOld = Product::findOne($val['id'])) {
                $imagesOld = $itemOld->images ?? [];
                $folder = $itemOld->folder ?: uniqid('', true) . 'fldr' . time();
            } else {
                $val['id'] = '';
                $imagesOld = '';
                $folder = uniqid('', true) . 'fldr' . time();
            }
            $val['folder'] = $folder;

            $val['images'] = $val['images'] ?? [];
            $val['image_main'] = $val['image_main'] ?? false;

            $preparedImages = MainModel::prepareImagesBeforeSave($val['images'], $val['image_main'], $imagesOld, 'products/' . $val['folder']);
            $val['images'] = $preparedImages['images'];
            $val['image_main'] = $preparedImages['image_main'];
            $val['url'] = MainModel::urlGeneration($val['name'], Product::find(), $val['id']);

            $val['category_id'] = (isset($val['category_id']) && ProductCategory::findOne($val['category_id'])) ? $val['category_id'] : null;
            $item = Product::create()->set($val);
            $resultSave = $item->save();
            if (!$item->id) {
                $item->id = $resultSave;
            }
            $returnResult['id'] = $item->id;
            if ($resultSave) {
                static::updateRelatedData($item);
                $returnResult['result'] = true;
            }
            $returnResult['returnData'] = $item;
        }
        return $returnResult;
    }

    public static function updateRelatedData(Product $product)
    {
        //обновляем ссылку в банных стилях
        $bathStyleId = $product->bath_style_id;
        if (!empty($bathStyleId)) {
            foreach ($bathStyleId as $styleId) {
                $bathStyle = BathStyle::findOne($styleId);
                $productsInStyle = $bathStyle->products_id;
                $productsInStyle[$product->id] = $product->id;
                $bathStyle->products_id = $productsInStyle;
                $bathStyle->save();
            }
        }
    }

    /**
     * @param $id
     * @return false[]
     */
    public static function Delete($id): array
    {
        $result = false;
        if ($item = Product::findOne($id)) {
            if ($result = $item->del()) {
                if (!empty($item->image)) {
                    MainModel::prepareImageBeforeSave('', $item->image, 'products/' . $item->folder);
                }
            }
        }
        return ['result' => $result];
    }
}