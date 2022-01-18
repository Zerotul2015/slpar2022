<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 17.08.18
 * Time: 19:12
 */

namespace App\Controllers\Admin\Traits;


use App\Model\Admin\MainModel;

/**
 * Trait BasicMethodsChange
 *  Для класса используещего этот трейт если его модель реализуещая интерфейс DefaultMethodTableClass имеет имя отличное
 *  от правила(примера) PagesBlocksModel обязательно наличие static $nameModel = 'полное_имя_класса_модели_реализуещей_интерфейс_DefaultMethodTableClass';
 * @package App\Controllers\Admin\Traits
 */
trait BasicMethodsChange
{
    /**
     * Сохраняет
     */
    public function Save()
    {
        if (!isset(static::$nameModel)){
            $nameClass = str_replace('\App\Controllers\Admin\\', '',__CLASS__);
            static::$nameModel = '\App\Model\Admin\\' . $nameClass . 'Model';
        }
        $result = false;
        $postData = json_decode(file_get_contents("php://input"), true);
        if (isset($postData['values'])) {
            $val = $postData['values'];
            $result = static::$nameModel::Save($val);
        }
        MainModel::printResultChangeForAjax($result);
    }

    public function Del()
    {
        $result = false;
        $postData = json_decode(file_get_contents("php://input"), true);
        if (isset($postData['id'])) {
            $id = $postData['id'];
            $result = static::$nameModel::Delete($id);
        }
        MainModel::printResultChangeForAjax($result);
    }
}