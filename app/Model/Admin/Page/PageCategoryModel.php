<?php


namespace App\Model\Admin\Page;


use App\Classes\ActiveRecord\Tables\PageCategory;
use App\Model\Admin\MainModel;
use App\Model\Interfaces\DefaultMethodTableClass;

class PageCategoryModel implements DefaultMethodTableClass
{

    /**
     * @param $val
     * @return array
     */
    public static function Save($val): array
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null];
        $itemOld = false;
        if (!isset($val['name_short'], $val['name_full']) || (empty($val['name_short']) && empty($val['name_full']))) {
            $returnResult['error'] = 'Поля название обязательно для заполнения';
        } else {
            //заполняем из другого если одно из названий пусто
            $val['name_short'] = empty($val['name_short']) ? $val['name_full'] : $val['name_short'];
            $val['name_full'] = empty($val['name_full']) ? $val['name_short'] : $val['name_full'];

            if (isset($val['id']) && $val['id']) {
                $itemOld = PageCategory::findOne($val['id']);
            } else {
                $val['id'] = '';
            }

            $val['folder'] = $itemOld ? isset($itemOld->folder) : uniqid('', true) . 'fldr' . time();;
            $val['url'] = MainModel::urlGeneration($val['name_short'], PageCategory::find(), $val['id']);

            $item = PageCategory::create()->set($val);

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
        if ($item = PageCategory::findOne($id)) {
            $result = $item->del();
        }
        return ['result' => $result];
    }
}