<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 02.05.18
 * Time: 18:48
 */

namespace App\Model\Admin\Settings;

use App\Classes\ActiveRecord\Tables\SettingsBanner;
use App\Classes\ActiveRecord\Tables\SettingsIndexPage;
use App\Model\Admin\MainModel;
use App\Model\FilesystemModel;
use App\Model\Interfaces\DefaultMethodTableClass;

class SettingsBannerModel implements DefaultMethodTableClass
{
    public static function Save($val): array
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null];
        $oldImage = '';
        $slider = false;
        if (!isset($val['id'])) {
            $val['id'] = '';
        } elseif ($val['id'] && $slider = SettingsBanner::findOne($val['id'])) {
            $oldImage = $slider->image;
        }
        //сохраняем изображения категории
        if (isset($val['image']) || !($val['image'] = '')) {
            $val['image'] = MainModel::prepareImageBeforeSave($val['image'], $oldImage,  '/images/banner/');
        }

        if ($slider) {
            $slider->set($val);
        } else {
            $slider = SettingsBanner::create($val);
        }

        if($returnResult['result'] = boolval($slider->save())){
            $returnResult['id'] = $slider->id;
            $returnResult['returnData'] = $slider;
        }
        return $returnResult;
    }

    public static function Delete($id): bool
    {
        $result = false;
        $banner = SettingsBanner::findOne($id);
        //удаляем изображения
        if ($banner->del()) {
            $fileSystemModel = new FilesystemModel();
            if ($banner->image) {
                $pathImage = FilesystemModel::getAbsolutePath('/images/banner/' . $banner->image);
                if ($fileSystemModel->exists($pathImage)) {
                    $fileSystemModel->remove($pathImage);
                    MainModel::removeImageInCache($pathImage);
                }
            }
            $result = true;

            //удаляем упоминание об удаленом банере из всех мест где он использовался

                SettingsLayoutsModel::removeDeletedElement(4, $id);

            $indexPage = SettingsIndexPage::findOne(1);
            $indexPageSlider = $indexPage->slider;
            if (isset($indexPageSlider['images']) && !empty($indexPageSlider['images'])) {
                foreach ($indexPageSlider['images'] as $key => $bannerId) {
                    if ($bannerId == $id) {
                        unset($indexPageSlider['images'][$key]);
                    }
                }
                $newImageArray =[];
                foreach ($indexPageSlider['images'] as $bannerId){
                    $newImageArray[] =$bannerId;
                }
                $indexPageSlider['images'] = $newImageArray;
                $indexPage->slider = $indexPageSlider;
                $indexPage->save();
            }
        }
        return $result;
    }
}