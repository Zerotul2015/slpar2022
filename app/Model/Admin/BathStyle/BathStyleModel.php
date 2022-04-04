<?php


namespace App\Model\Admin\BathStyle;


use App\Classes\ActiveRecord\Tables\BathStyle;
use App\Model\Admin\MainModel;
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
        if (!isset($val['name']) || empty($val['name'])) {
            $returnResult['error'] = 'Поля "Название стиля" обязательно для заполнения';
        } else {
            if (isset($val['id']) && $val['id'] && $itemOld = BathStyle::findOne($val['id'])) {
                $imageOld = $itemOld->image ?? '';
                $folder = $itemOld->folder ? $itemOld->folder : uniqid('', true) . 'fldr' . time();
            }else{
                $val['id'] = '';
                $imageOld = '';
                $folder = uniqid('', true) . 'fldr' . time();
            }
            $image = $val['image'] ?? '';
            $val['folder'] = $folder;
            $val['image'] = MainModel::prepareImageBeforeSave($image, $imageOld, 'bath-style/' . $folder);
            $val['url'] = MainModel::urlGeneration($val['name'], BathStyle::find(), $val['id']);
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
            if ($result = $item->del()) {
                if (!empty($item->image)) {
                    MainModel::prepareImageBeforeSave('', $item->image, 'bath-style/' . $item->folder);
                }
            }
        }
        return ['result' => $result];
    }
}