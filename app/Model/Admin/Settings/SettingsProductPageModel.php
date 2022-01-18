<?php


namespace App\Model\Admin\Settings;


use App\Classes\ActiveRecord\Tables\SettingsProductPage;
use App\Model\Interfaces\DefaultMethodTableClass;

class SettingsProductPageModel implements DefaultMethodTableClass
{
    public static function Save($val)
    {
        $resultCheck = false;

        if ($sectionSetting = SettingsProductPage::findOne(1)) {
            $resultCheck = $sectionSetting->set($val)->save();
        }
        return $resultCheck;
    }

    public static function Delete($id)
    {
        return false;
    }
}