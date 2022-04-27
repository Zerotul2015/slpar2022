<?php


namespace App\Model\Admin\Page;


use App\Classes\ActiveRecord\Tables\PageCategory;
use App\Classes\ActiveRecord\Tables\Page;
use App\Model\Admin\MainModel;
use App\Model\Interfaces\DefaultMethodTableClass;

class PageModel implements DefaultMethodTableClass
{

    /**
     * @param $val
     * @return array
     */
    public static function Save($val): array
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null];
        $pageOld = false;
        if (isset($val['id']) && $val['id']) {
            $pageOld = Page::findOne($val['id']);
        } else {
            $val['id'] = '';
        }
        $images = $val['images'] ?? [];
        if ($pageOld) {
            $val['integrated'] = $pageOld->integrated;
            $val['url'] = $pageOld->url;
            $imagesOld = $pageOld->images ?? [];
            $folder = $pageOld->folder;
        } else {
            $val['url'] = MainModel::urlGeneration($val['title'], Page::find(), $val['id']);
            $imagesOld = [];
            $folder = uniqid('', true) . 'fldr' . time();
        }
        $val['folder'] = $folder;
        $val['images'] = MainModel::prepareImagesBeforeSave($images, false, $imagesOld, 'pages/' . $folder)['images'];



        //проверка на существование категории
        if(isset($val['category_id']) && $val['category_id']){
            if(!$category = PageCategory::findOne($val['category_id'])){
                $val['category_id'] = null;
            }
        }

        $page = Page::create()->set($val);

        $resultSave = $page->save();
        if (!$page->id) {
            $page->id = $resultSave;
        }
        $returnResult['id'] = $page->id;
        if ($resultSave) {
            $returnResult['result'] = true;
        }
        $returnResult['returnData'] = $page;

        return $returnResult;
    }

    /**
     * @param $id
     * @return false[]
     */
    public static function Delete($id): array
    {
        $result = false;
        if ($page = Page::findOne($id)) {
            if (!$page->integrated && $result = $page->del()) {
                if (!empty($page->images)) {
                    $images = $page->images;
                    foreach ($images as $image) {
                        MainModel::prepareImageBeforeSave('', $image, 'pages/' . $page->folder);
                    }
                }
            }
        }
        return ['result' => $result];
    }
}