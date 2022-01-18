<?php


namespace App\Model\Admin\Settings;


use App\Classes\ActiveRecord\Tables\SettingsLayouts;
use App\Classes\ActiveRecord\Tables\SettingsSections;
use App\Model\Interfaces\DefaultMethodTableClass;

class SettingsSectionsModel implements DefaultMethodTableClass
{
    public static function Save($val)
    {
        $resultCheck = false;
        /*
            section
            1: 'Категории товаров',
            2: 'Подборки товаров',
            3: 'Страницы',
            4: 'Услуги',
            5: 'Наши работы'
            6: 'Подарочные сертификаты',
            7: 'Фотогалерея',
        */
        if (isset($val['id']) && $val['id'] && $sectionSetting = SettingsSections::findOne($val['id'])) {
            //проверка section
            $sectionForCheck = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7];
            if (isset($sectionForCheck[$val['section']])) {
                $resultCheck = $sectionSetting->set($val)->save();
            }
        }
        return $resultCheck;
    }

    public static function Delete($id)
    {
        return false;
    }
}