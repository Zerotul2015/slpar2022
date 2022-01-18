<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 02.05.18
 * Time: 18:48
 */

namespace App\Model\Admin\Settings;


use App\Classes\ActiveRecord\Tables\Settings;
use App\Model\Admin\MainModel;

class SettingsModel
{
    public static function save($val): array
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null];
        $settings = static::prepareForSave($val);
        if($returnResult['result'] = boolval($settings->save())){
            $returnResult['id'] = $settings->id;
            $returnResult['returnData'] = $settings;
        }
        return $returnResult;
    }

    /**
     * @param array $val
     * @return Settings
     */
    public static function prepareForSave(array $val): Settings
    {
        $valInBase = Settings::findOne(1);
        $val['image_logo'] = $val['image_logo'] ?? '';
        $val['image_header'] = $val['image_header'] ?? '';
        $oldImages = $valInBase ? [
            'image_logo' => $valInBase->image_logo,
            'image_header' => $valInBase->image_header
        ] : [
            'image_logo' => '',
            'image_header' => ''
        ];
        $val['image_logo'] = MainModel::prepareImageBeforeSave($val['image_logo'], $oldImages['image_logo'], 'template');
        $val['image_header'] = MainModel::prepareImageBeforeSave($val['image_header'], $oldImages['image_header'], 'template');
        return $valInBase->set($val);
    }
}