<?php


namespace App\Model\Admin\Settings;


use App\Classes\ActiveRecord\Tables\SettingsNotifications;
use App\Model\Interfaces\DefaultMethodTableClass;

class SettingsNotificationsModel implements DefaultMethodTableClass
{
    public static function Save($val): array
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null];
        if ($setting = SettingsNotifications::findOne(1)) {
            if ($returnResult['result'] = boolval($setting->set($val)->save())) {
                $returnResult['id'] =$setting->id;
                $returnResult['returnData'] = $setting;
            }
        }
        return $returnResult;
    }

    public static function Delete($id): bool
    {
        return false;
    }
}