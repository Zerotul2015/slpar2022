<?php


namespace App\Model\Admin\Settings;


use App\Classes\ActiveRecord\Tables\Users;
use App\Model\Interfaces\DefaultMethodTableClass;

class SettingsUsersModel implements DefaultMethodTableClass
{
    public static function Save($val)
    {
        $result = false;
        if (isset($val['login'])) {
            if(isset($val['id']) && !empty($val['id']) && $userExist = Users::findOne($val['id'])){
                $user = $userExist;
            }else{
                $user = Users::create();
            }
            if (isset($val['pass']) && !empty($val['pass'])) {
                $val['pass'] = password_hash($val['pass'], PASSWORD_DEFAULT);
                $val['token'] = uniqid($val['login'], true);
            }else{
                if($user->pass){
                    $val['pass'] = $user->pass;
                }
            }
            //проверяем уровень доступа
            $accessLevelApproved = ['manager'=>1, 'admin'=>1];
            if(!isset($accessLevelApproved[$val['access_level']])) {
                $val['access_level'] = 'manager';
            }
            $user->set($val);
            //проверяем не занят ли логин
            $loginFree = true;
            if($userLoginExist = Users::find()->where(['login'=>$val['login']])->one()){
                if(!isset($val['id']) || $val['id'] !== $userLoginExist->id){ //у другого пользователя
                    $loginFree = false;
                }
            }
            if($loginFree){
                $result = $user->save();
            }else{
                $result = ['result'=>false, 'errorText'=>'Логин занят другим пользователем'];
            }
        }
        return $result;
    }

    public static function Delete($id): bool
    {
        $result = false;
        if($banner = Users::findOne($id)) {
            $result = $banner->del();
        }
        return $result;
    }
}