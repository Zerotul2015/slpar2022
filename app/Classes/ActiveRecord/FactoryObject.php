<?php
/**
 * Created by PhpStorm.
 * Public: zerotul
 * Date: 21.10.17
 * Time: 15:20
 */

namespace App\Classes\ActiveRecord;


use App\Classes\ActiveRecord;

class FactoryObject
{
    /**
     * Создаем объект нужного класса
     * @param string $className
     * @return \App\Classes\ActiveRecord\Main
     */
    public static function createObject(string $className = 'stdClass'): Main
    {
        return new $className;
    }

    /**
     * Получаем из базы все объекты нужного класса
     * @param $val array
     * @return array | false
     */
    public static function getAll(array $val)
    {
        $db = new ActiveRecord();
        $valGet = static::prepareGet($val);
        $result = $db->getObject($valGet['tableName'], $valGet['select'], $valGet['where'], $valGet['orderBy'], $valGet['sort'], $valGet['limit'], $valGet['className']);
        return $result ?: false;
    }

    /**
     * Получаем массив всех объекты нужного класса, в качестве ключей которго используются primaryKey
     * @param $val array
     * @param $indexBy string
     * @return array | false
     */
    public static function getAllIndexBy(array $val, string $indexBy = '')
    {
        $returnResult = false;
        $result = static::getAll($val);
        if ($result) {
            if ($indexBy) {
                foreach ($result as $res) {
                    $returnResult[$res->{$indexBy}] = $res;
                }
            } else {
                foreach ($result as $res) {
                    $returnResult[$res::$primaryKey] = $res;
                }
            }
        }
        /** @var array|false $returnResult */
        return $returnResult;
    }

    /**
     * Получаем из базы один объект
     * @param $val array
     * @return \App\Classes\ActiveRecord\Main | false
     */
    public static function getOne(array $val)
    {
        $val['limit'] = [0, 1];
        $result = static::getAll($val);
        return (isset($result[0])) ? $result[0] : false;
    }


    /**
     * Подготавливаем запрос для полученения объектов из базы
     * @param $val array
     * @return array
     */
    public static function prepareGet(array $val): array
    {
        $arrayNeedVal = [
            'tableName' => '',
            'select' => [],
            'where' => '',
            'orderBy' => '',
            'sort' => '',
            'limit' => [],
            'className' => 'stdClass',
        ];
        return array_replace_recursive($arrayNeedVal, $val);
    }

    /**
     * Пока ни где не используется. Оставил на всякий случай.
     * Преобразовываем имя талицы к названию класса
     * @param $name (название таблицы)
     * @return string (имя класса)
     */
    public static function classNameFromTableName($name): string
    {
        $classNamePrepare = explode('_', $name);
        $className = '';
        foreach ($classNamePrepare as $classNameWord) {
            $className = $className . ucfirst($classNameWord);
        }
        return '\App\Classes\ActiveRecord\Tables\\' . $className;
    }
}