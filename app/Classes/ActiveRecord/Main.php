<?php
/**
 * Created by PhpStorm.
 * Visitors: zerotul
 * Date: 11.10.17
 * Time: 11:46
 */

namespace App\Classes\ActiveRecord;

use App\Classes\ActiveRecord;
use JetBrains\PhpStorm\ArrayShape;

/**
 * TODO проверить все методы на соовместимость с новыми where и andWhere
 * Class Main
 * @package App\Classes\ActiveRecord
 */
abstract class Main implements \JsonSerializable
{
    static string $tableName; //
    static array $columnName = []; //
    static string $primaryKey = ''; //
    static array $uniqueKey = []; //
    private array $valueColumn = [];
    private array $findWhereGroup = [];
    private static array $conditionAllowed = ['=' => true, '<>' => true, 'LIKE' => true, 'NOT LIKE' => true];
    private static array $groupConditionAllowed = ['AND' => true, 'OR' => true];
    private array $settingsGet = [];
    private string $indexBy = '';

    public function __construct()
    {
        /*// Формируем имя таблица из названия класса

        static::$tableName = FactoryObject::tableNameFromClassName(get_called_class());

        // Получаем детали таблицы
        $db = new ActiveRecord();
        static::$columnName = $db->getNameColumn($this->tableName);
        static::$primaryKey = $db->getTablePrimaryKey($this->tableName);
        if ($uniqueKey = $db->getTableUniqueKey($this->tableName)) {
            static::$uniqueKey = $uniqueKey;
        }
        */
    }

    /**
     * Заполняет объекты указаными данными и сохраняет в базу
     * @param array $arrayObjects (пример [[0]=>['id'=>15, 'name'=>'Peoples']])
     * @return array id добавленый эллементов или результат добавления (пример [11, false, 12, 13, true)
     */
    public static function saveArrayObjects(array $arrayObjects)
    {
        $returnResult = [];
        if (!empty($arrayObjects)) {
            foreach ($arrayObjects as $currentObject) {
                $emptyObject = static::find();
                foreach ($currentObject as $nameValue => $value) {
                    $emptyObject->{$nameValue} = $value;
                }
                $returnResult[] = $emptyObject->save();
            }
        }
        return $returnResult;
    }

    /**
     * Синоним создания пустого объекта для дальнейшего поиска в базе
     * @return $this
     */
    public static function find()
    {
        return FactoryObject::createObject(get_called_class());
    }

    /**
     * @return bool|string
     */
    public function save()
    {
        $db = new ActiveRecord();
        foreach ($this->valueColumn as $key => $value) {
            //приводим все массивы перед записью в базу к JSON строке
            if (is_array($value) || is_object($value)) {
                $this->valueColumn[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            // Присваиваем пустым ключам значения по умолчанию, если не указано занчение по умолчанию,тогда удаляем его
            if (empty($this->valueColumn[$key]) && $this->valueColumn[$key] !== 0) {
                if (is_null(static::$columnName[$key]['default'])) {
                    $this->valueColumn[$key] = null;
                } elseif (empty(static::$columnName[$key]['default']) == false) {
                    $this->valueColumn[$key] = static::$columnName[$key]['default'];
                }
            }
        }
        //проверка на существование в таблице записи с таким ключем
        //TODO написать проверку уникальных полей
        $autoIncrement = (static::$columnName[static::$primaryKey]['extra'] == 'auto_increment');
        if ($autoIncrement) {
            if (isset($this->valueColumn[static::$primaryKey])) {
                if (empty($this->valueColumn[static::$primaryKey])) {
                    unset($this->valueColumn[static::$primaryKey]);
                    $result = $db->set(static::$tableName, $this->valueColumn);
                } else {
                    $existDataTable = is_object(static::findOne($this->valueColumn[static::$primaryKey]));
                    if ($existDataTable) {
                        $result = $db->set(static::$tableName, $this->valueColumn, static::$primaryKey . '=' . $this->valueColumn[static::$primaryKey]);
                    } else {
                        $result = $db->set(static::$tableName, $this->valueColumn);
                    }

                }
            } else {
                $result = $db->set(static::$tableName, $this->valueColumn);
            }
        } elseif (isset($this->valueColumn[static::$primaryKey]) && !empty($this->valueColumn[static::$primaryKey])) {
            $result = $db->set(static::$tableName, $this->valueColumn);
        } else {
            $result = false;
        }
        if ((!isset($this->valueColumn[static::$primaryKey]) || !$this->valueColumn[static::$primaryKey]) && $result) {
            $this->valueColumn[static::$primaryKey] = $result;
        }
        $this->JSON_Decode();
        return $result;
    }

    /**
     * Получаем один объект с указаным primaryKey
     * @param $id string
     * @return $this || false
     */
    public static function findOne($id = '')
    {
        return FactoryObject::createObject(get_called_class())->where([static::$primaryKey => $id])->one();
    }

    /**
     * Получаем первый экземпляр класса
     * @return $this || false
     */
    public function one()
    {
        $one = FactoryObject::getOne($this->prepareDataForFind());
        if ($one) {
            $one->JSON_Decode();
        }
        return $one;
    }

    /**
     * Подготавливаем массив параметров для зароса получения объектов
     * @return array
     */
    private function prepareDataForFind()
    {
        $orderBy = (isset($this->settingsGet['orderBy'])) ? $this->settingsGet['orderBy'] : '';
        $sort = (isset($this->settingsGet['sort'])) ? $this->settingsGet['sort'] : 'DESC';
        $limit = (isset($this->settingsGet['limit'])) ? $this->settingsGet['limit'] : [];
        $select = (isset($this->settingsGet['select'])) ? $this->settingsGet['select'] : [];

        return [
            'tableName' => static::$tableName,
            'where' => $this->findWhereGroup,
            'orderBy' => $orderBy,
            'sort' => $sort,
            'limit' => $limit,
            'select' => $select,
            'className' => static::$className
        ];
    }

    /**
     * Привдоим все значения являющиеся JSON к виду массива
     *
     */
    public function JSON_Decode()
    {
        foreach ($this->valueColumn as $keyCol => $valueCurrent) {
            if (static::isJSON($valueCurrent)) {
                $this->valueColumn[$keyCol] = json_decode($valueCurrent, true);
            }
        }
        return $this;
    }

    /**
     * Проверка на то что это JSON строка
     * @param $string
     * @return bool
     */
    public static function isJSON($string)
    {
        if (is_string($string)) {
            json_decode($string);
            $resultReturn = (json_last_error() === JSON_ERROR_NONE);
        } else {
            $resultReturn = false;
        }
        return $resultReturn;
    }


    /**
     * Используется для проверки на корректность в $this->where() и $this->andWhere()
     * @param $condition
     * @return string
     */
    private function checkCondition($condition): string
    {
        $condition = strtoupper($condition);
        //'=', '<>', 'LIKE', 'NOT LIKE'
        return isset(static::$conditionAllowed[$condition]) ? $condition : '=';
    }

    /**
     * Используется для проверки на корректность в $this->where и $this->andWhere
     * @param $condition
     * @return string
     */
    private function checkGroupCondition($condition): string
    {
        $condition = strtoupper($condition);
        //'OR', 'AND'
        return isset(static::$groupConditionAllowed[$condition]) ? $condition : 'AND';
    }

    private static function ifLikeVal($val, $condition)
    {
        if ($condition === 'LIKE' || $condition === 'NOT LIKE') {
            $val = '%' . $val . '%';
        }
        return $val;
    }

    /**
     * New!!! Устанавливает условия для поиска в базе
     *
     * @param string|array $params
     * @param string|array $val
     * @param string $condition
     * @param int $groupKey
     * @param string $groupCondition
     * @return $this
     */
    public function where($params = '', $val = null, string $condition = '=', int $groupKey = 0, string $groupCondition = 'AND'): Main
    {
        $condition = $this->checkCondition($condition);
        $groupCondition = $this->checkGroupCondition($groupCondition);
        if (!isset($this->findWhereGroup[$groupKey]['condition']) || !$this->findWhereGroup[$groupKey]['condition']) {
            $this->findWhereGroup[$groupKey]['condition'] = $groupCondition;
        }

        if (is_array($params) && !empty($params)) {
            foreach ($params as $column => $colVal) {
                if (!empty($colVal)) {
                    if (is_array($colVal) && !empty($colVal)) {
                        foreach ($colVal as $colValItem) {
                            $this->andWhere($column, $colValItem, $condition, $groupKey, $groupCondition);
                        }
                    } else {
                        $this->andWhere($column, $colVal, $condition, $groupKey, $groupCondition);
                    }
                }
            }
        } elseif ($params && $val !== null) {
            if (isset(static::$columnName[$params])) {
                if (is_array($val)) {
                    foreach ($val as $valItem) {
                        $this->andWhere($params, $valItem, $condition, $groupKey, $groupCondition);
                    }
                } else {
                    $this->findWhereGroup[$groupKey]['where'][$params][0] = ['val' => static::ifLikeVal($val, $condition), 'condition' => $condition];
                }
            }
        } elseif (!empty($params)) {
            $this->findWhereGroup[$groupKey]['where'][static::$primaryKey][0] = ['val' => static::ifLikeVal($params, $condition), 'condition' => $condition];
        }
        return $this;
    }

    /**
     * New!!! Добавляет условие условия для поиска в базе
     *
     * @param $params array|string
     * @param null $val string|array
     * @param string $condition
     * @param int $groupKey
     * @param string $groupCondition
     * @return $this
     */
    public function andWhere($params = '', $val = null, string $condition = '=', int $groupKey = 0, string $groupCondition = 'AND'): Main
    {
        $condition = $this->checkCondition($condition);
        $groupCondition = $this->checkGroupCondition($groupCondition);
        if (!isset($this->findWhereGroup[$groupKey]['condition']) || !$this->findWhereGroup[$groupKey]['condition']) {
            $this->findWhereGroup[$groupKey]['condition'] = $groupCondition;
        }

        if (is_array($params) && !empty($params)) {
            foreach ($params as $column => $colVal) {
                if (!empty($colVal)) {
                    if (is_array($colVal) && !empty($colVal)) {
                        foreach ($colVal as $colValItem) {
                            $this->andWhere($column, $colValItem, $condition, $groupKey, $groupCondition);
                        }
                    } else {
                        $this->andWhere($column, $colVal, $condition, $groupKey, $groupCondition);
                    }
                }
            }
        } elseif ($params && $val !== null) {
            if (isset(static::$columnName[$params])) {
                if (is_array($val)) {
                    foreach ($val as $valItem) {
                        $this->andWhere($params, $valItem, $condition, $groupKey, $groupCondition);
                    }
                } else {
                    $this->findWhereGroup[$groupKey]['where'][$params][] = ['val' => static::ifLikeVal($val, $condition), 'condition' => $condition];
                }
            }
        } elseif (!empty($params)) {
            $this->findWhereGroup[$groupKey]['where'][static::$primaryKey][] = ['val' => static::ifLikeVal($params, $condition), 'condition' => $condition];
        }
        return $this;
    }

    public static function delByPrimaryKey($primaryKey)
    {
        if ($toDel = FactoryObject::createObject(get_called_class())->where([static::$primaryKey => $primaryKey])->one()) {
            $db = new ActiveRecord();
            $result = $db->delete(static::$tableName, [static::$primaryKey => $toDel->valueColumn[static::$primaryKey]]);
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Создание пустого объекта для дальнейшего поиска в базе
     * @param $val array массив значений для нового объекта в фомате ['name' => 'value',..., 'nameN' => 'valueN']
     * @return $this
     */
    public static function create(array $val = []): Main
    {
        $emptyObject = FactoryObject::createObject(get_called_class());
        if (empty($val) == false) {
            $emptyObject->set($val);
        }
        return $emptyObject;
    }

    /**
     * Присваиваем все значения принятого массива соответствующим свойствам объекта
     * @param array $value (пример ['name' => 'Олег', 'phone' => '9231451571'])
     * @return $this
     */
    public function set(array $value): Main
    {
        foreach ($value as $keyVal => $val) {
            if (isset(static::$columnName[$keyVal])) {
                $this->valueColumn[$keyVal] = $val;
            }
            if (isset(static::$columnName[$keyVal]['type']) && static::$columnName[$keyVal]['type'] == 'tinyint(1)') {
                if ($val) {
                    $this->valueColumn[$keyVal] = 1;
                } else {
                    $this->valueColumn[$keyVal] = 0;
                }
            }
        }
        return $this;
    }

    /**
     * Получаем count последних объектов из базы в виде массива
     * @param int $count int
     * @return $this[]
     */
    public static function findLast(int $count = 1)
    {
        $emptyObjectForFind = static::find();
        $emptyObjectForFind
            ->orderBy($emptyObjectForFind::$primaryKey)
            ->limit([$count]);
        return $emptyObjectForFind->all();
    }

    /**
     * Сколько и с какого элмента выводим
     * @param array $limit
     * @return $this
     */
    public function limit(array $limit): Main
    {
        if (isset($limit[1]) && isset($limit[0])) {
            $this->settingsGet['limit'] = [$limit[0], $limit[1]];
        } elseif (isset($limit[0])) {
            $this->settingsGet['limit'] = [$limit[0]];
        } else {
            $this->settingsGet['limit'] = [];
        }
        return $this;
    }

    /**
     * Указываем по какому поля сортировать результат
     * @param string $column
     * @return $this
     */
    public function orderBy(string $column): Main
    {
        $resultCheckColumnExist = isset(static::$columnName[$column]);
        if ($resultCheckColumnExist) {
            $this->settingsGet['orderBy'] = $column;
        }
        return $this;
    }

    /**
     * Получаем массив всех экземпляров класса
     * @return $this[]
     */
    public function all()
    {
        if (empty($this->indexBy)) {
            $result = FactoryObject::getAll($this->prepareDataForFind());
        } else {
            $result = FactoryObject::getAllIndexBy($this->prepareDataForFind(), $this->indexBy);
        }
        if ($result) {
            foreach ($result as $k => $obj) {
                $result[$k] = $obj->JSON_Decode();
            }
        }
        return $result;

    }

    /**
     * Получаем все объекты из базы в виде массива
     * @return $this[]
     */
    public static function findAll()
    {
        return FactoryObject::createObject(get_called_class())->all();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->valueColumn;
    }

    public function __get($k)
    {
        if (!isset($this->valueColumn[$k])) {
            $k = static::camelToUnderscore($k);
        }
        if (isset($this->valueColumn[$k])) {
            $v = $this->valueColumn[$k];
            // приводим время из секунд в формат timestamp
            if (static::$columnName[$k]['type'] == 'timestamp') {
                $v = strtotime($v);
            }
        } else {
            $v = null;
        }
        return $v;
    }

    public function __set($k, $v)
    {

        if (!isset(static::$columnName[$k])) {
            $k = static::camelToUnderscore($k);
        }
        if (isset(static::$columnName[$k])) {
            // приводим время из секунд в формат timestamp
            if (static::$columnName[$k]['type'] == 'timestamp') {
                if (is_numeric($v)) {
                    $v = date('Y-m-d H:i:s', $v);
                } else {
                    $v = date('Y-m-d H:i:s', strtotime($v));
                }
            }
            if (static::$columnName[$k]['type'] == 'tinyint(1)') {
                if ($v) {
                    $v = 1;
                } else {
                    $v = 0;
                }
            }

            $this->valueColumn[$k] = $v;
        }
    }

    public function __isset($k)
    {
        return isset($this->valueColumn[$k]);
    }

    private static function camelToUnderscore($string, $us = "_"): string
    {
        return strtolower(preg_replace(
            '/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', $us, $string));
    }

    public function __toString(){
        $string = '';
        foreach ($this->valueColumn as $k => $v) {
            $string .= "$k: $v;" . PHP_EOL;
        }
        return $string;
    }
    /**
     * Возвращает свойства объекта в виде ассоциативного массива
     * @return array
     */
    public function toArray(): array
    {
        $newArray = [];
        foreach ($this->valueColumn as $k => $v) {
            $newArray[$k] = $v;
        }
        return $newArray;
    }

    /**
     * Удаляет объект из базы
     * @return bool
     */
    public function del(): bool
    {
        $db = new ActiveRecord();
        $existObj = $this->findOne($this->valueColumn[static::$primaryKey]);
        if ($existObj) {
            $result = $db->delete(static::$tableName, [static::$primaryKey => $this->valueColumn[static::$primaryKey]]);
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Возвращает все найденные в базе объекты сгруппированные по указанному полю
     * Ко всем объектам применен метод JSON_Decode()
     * @param string $columnName
     * @return array [$this, $this, ...]
     */
    public function findGroupBy(string $columnName): array
    { //TODO протестировать, проверить на разных объектах с разными условиями
        $returnArray = [];

        if (array_key_exists($columnName, static::$columnName)) {
            if (empty($this->indexBy)) {
                $arrayObjects = FactoryObject::getAll($this->prepareDataForFind());
            } else {
                $arrayObjects = FactoryObject::getAllIndexBy($this->prepareDataForFind(), $this->indexBy);
            }
            if ($arrayObjects) {
                foreach ($arrayObjects as $k => $obj) {
                    $arrayObjects[$k] = $obj->JSON_Decode();
                }

                foreach ($arrayObjects as $arrayIndex => $objectItem) {
                    $groupKey = $objectItem->$columnName ? $objectItem->$columnName : 0;
                    if (empty($this->indexBy)) {
                        $returnArray[$groupKey][] = $objectItem;
                    } else {
                        $returnArray[$groupKey][$arrayIndex] = $objectItem;
                    }
                }
            }
        }
        return $returnArray;
    }

    /**
     * Задает ключ для получения всех объектов в виде ассоциативного массива с нужным ключем
     * (по умолчанию primaryKey)
     * @param string $columnName
     * @return $this
     */
    public function indexBy(string $columnName = ''): Main
    {
        if (!$columnName) {
            $this->indexBy = static::$primaryKey;
        } elseif (array_key_exists($columnName, static::$columnName)) {
            $this->indexBy = $columnName;
        }
        return $this;
    }

    /**
     * Указываем порядок сортировки объектов
     * @param string $sort 'ASC'|'DESC'
     * @return $this
     */
    public function sort(string $sort = 'DESC')
    {
        $checkArray = ['ASC', 'DESC'];
        $sort = strtoupper($sort);
        if (in_array($sort, $checkArray)) {
            $this->settingsGet['sort'] = $sort;
        }
        return $this;
    }

    /**
     * Указываем поле для выборки
     * @param string|array $column (['id','name',...])
     * @return $this
     */
    public function select($column): Main
    {
        if (!empty($column)) {
            if (is_array($column)) {
                foreach ($column as $colName) {
                    if (isset(static::$columnName[$colName])) {
                        $this->settingsGet['select'][$colName] = $colName;
                    }
                }
            } else {
                $this->settingsGet['select'][$column] = $column;
            }
        }
        return $this;
    }

    /**
     * Ищем все объекты с указаными настройками в базы, и приводим каждый объект к массиву
     * @return array
     */
    public function findAllToArray()
    {
        if (empty($this->indexBy)) {
            $found = FactoryObject::getAll($this->prepareDataForFind());
        } else {
            $found = FactoryObject::getAllIndexBy($this->prepareDataForFind(), $this->indexBy);
        }

        $arrayObjects = [];
        if ($found) {
            foreach ($found as $k => $currentItem) {
                if (empty($this->indexBy)) {
                    $arrayObjects[] = $currentItem->JSON_Decode()->toArray();
                } else {
                    $arrayObjects[$k] = $currentItem->JSON_Decode()->toArray();
                }
            }
        }
        return $arrayObjects;
    }

    /**
     * Получаем данные для нужной страницы и количество страниц
     * @param int $page нужная страница
     * @param int $perPage элементов на страницу
     * @return array число возможных страниц и объекты для текущей страницы
     */
    #[ArrayShape(['pageCount' => "float", 'objects' => "array"])] public function pagination(int $page = 1, int $perPage = 20): array
    {
        $count = $this->count();

        $pageCount = ceil($count / $perPage);

        $start = $page * $perPage - $perPage;
        if ($start > $count - 1) : $start = $count - 1; endif;
        $this->limit([$start, $perPage]);
        $objects = FactoryObject::getAll($this->prepareDataForFind());

        $arrayObjects = [];
        if ($objects) {
            foreach ($objects as $k => $obj) {
                $arrayObjects[$k] = $obj->JSON_Decode();
            }
        }
        return ['pageCount' => $pageCount, 'objects' => $arrayObjects];
    }

    /**
     * Возвращает число объектов соответствующих условиям отбора
     * @return int
     */
    public function count(): int
    {
        $db = new ActiveRecord();
        $valGet = FactoryObject::prepareGet($this->prepareDataForFind());
        return $db->count(static::$tableName, $valGet['where'], $valGet['limit']);
    }

}