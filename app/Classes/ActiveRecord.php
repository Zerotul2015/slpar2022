<?php
/**
 * Created by PhpStorm.
 * Public: zerotul
 * Date: 17.04.16
 * Time: 15:07
 */

namespace App\Classes;


use App\Classes\ActiveRecord\FactoryObject;

class ActiveRecord

{
    private \PDO $dbh;
    private string $className = 'stdClass';

    function __construct()
    {
        $this->dbh = new \PDO("mysql:dbname=$_ENV[DB_DATABASE];host=$_ENV[DB_HOST]", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
        $this->dbh->exec("SET NAMES utf8");
    }

    public function setClassName($className)
    {
        $this->className = $className;
    }

    public
    function execute(
        $sql,
        $params = []
    )
    {
        $sth = $this->dbh->prepare($sql);
        return $sth->execute($params);
    }

    /**
     * @param array $val
     * @return array ['sql_text' => [[0] =>'name'], 'sql_val' => [[':name'] => 'name_text'], 'sql_pdo' => [[0] =>'name=:name']]
     */
    public static function prepareSql($val): array
    {
        // $val = ['name' = 'name_text']
        $name_val = [];
        $name_val2 = [];
        $name_val3 = [];
        $sql_val = [];
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                if ($k == 'save') {
                    continue;
                }
                // массив значени для одного ключа
                if (is_array($v)) {
                    $kNew = 1;
                    $name_val[] = $k;
                    foreach ($v as $vKey => $vVal) {
                        $v[$vKey] = ($vVal !== null) ? trim($vVal) : null;
                        $name_val2[] = $k . '=:' . $k . $kNew;
                        $name_val3[] = ':' . $k . $kNew;
                        $sql_val[':' . $k . $kNew] = $vVal;
                        $kNew++;
                    }
                } else {
                    if ($v !== null) {
                        $v = trim($v);
                    }
                    $name_val[] = $k;
                    $name_val2[] = $k . '=:' . $k;
                    $name_val3[] = ':' . $k;
                    $sql_val[':' . $k] = $v;
                }
            }
        }
        return ['name' => $name_val, ':name=>value' => $sql_val, 'name=:name' => $name_val2, ':name' => $name_val3];
    }

    public function count($table, $where, $limit)
    {
        // LIMIT
        $sqlLimit = isset($limit[0]) ? "LIMIT $limit[0]" : '';
        $sqlLimit = isset($limit[1]) ? $sqlLimit . ", $limit[1]" : $sqlLimit;
        $wherePrepared = $this->prepareWhere($where);
        $sql = "SELECT COUNT(*) as count FROM $table $wherePrepared[where] $sqlLimit";
        $sth = $this->dbh->prepare($sql);
        //$sth->debugDumpParams();
        $sth->execute($wherePrepared['params']);
        return $sth->fetch()['count'];
    }


    /**
     * Возвращает подготовленую строку с условиями WHERE... для подстановки в sql запрос
     * и массив параметров для подстановки PDO
     * @param $where
     * @return array
     */
    public function prepareWhere($where): array
    {
        $params = [];
        $sqlWhere = '';
        if (!empty($where)) {
            $countGroup = count($where);
            foreach ($where as $groupKey => $groupWhere) {
                $sqlWhereGroup = '';
                foreach ($groupWhere['where'] as $columnName => $arrayColumnData) {
                    $sqlColumnGroup = '';
                    foreach ($arrayColumnData as $keyData => $columnData) {
                        $keyForParam = ':' . $groupKey . $columnName . $keyData;

                        $params[$keyForParam] = $columnData['val'];
                        $sqlColumnGroup = "$sqlColumnGroup $columnName $columnData[condition] $keyForParam OR";
                    }
                    $sqlColumnGroup = preg_replace('/(OR$)|(\s+$)/', '', $sqlColumnGroup);
                    $sqlWhereGroup = "$sqlWhereGroup $groupWhere[condition] ($sqlColumnGroup)";
                }
                $sqlWhere = "$sqlWhere $sqlWhereGroup";
                $sqlWhere = preg_replace('/(\s+$)|(\s+AND\s+$)|(\s+OR\s+$)|(^\s+AND\s+)|(^\s+OR\s+)/', ' ', $sqlWhere);
                if ($countGroup > 1) {
                    $sqlWhereGroup = "($sqlWhereGroup)";
                }

            }
            //$sqlWhere = preg_replace('/(\s+$)|(\s+AND\s+$)|(\s+OR\s+$)|(^\s+AND\s+)|(^\s+OR\s+)/', ' ', $sqlWhere);
            $sqlWhere = 'WHERE ' . trim($sqlWhere);
        }
        return ['params' => $params, 'where' => $sqlWhere];
    }

    public function getObject($table, $select, $where, $orderBy, $sort, $limit, $className = 'stdClass')
    {
        $select = $select ? implode(', ', $select) : '*';
        //упрорядочивание
        $sort = $sort === 'DESC' ? $sort : 'ASC';
        //сортировка по колонке
        $orderBy = $orderBy ? 'ORDER BY ' . $orderBy : 'ORDER BY  id';
        // LIMIT
        $sqlLimit = isset($limit[0]) ? "LIMIT $limit[0]" : '';
        $sqlLimit = isset($limit[1]) ? $sqlLimit . ", $limit[1]" : $sqlLimit;
        $wherePrepared = $this->prepareWhere($where);
        $sql = "SELECT $select FROM $table $wherePrepared[where] $orderBy $sort $sqlLimit";
        return $this->query_obj($sql, $wherePrepared['params'], $className);
    }


    public function query_obj($sql, $params = [], $className = 'stdClass')
    {
        $sth = $this->dbh->prepare($sql);
        $sth->execute($params);
        $res = $sth->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className);
        //$sth->debugDumpParams();
        return $res;
    }

    function query_array($sql, $params = []): array
    {
        $sth = $this->dbh->prepare($sql);
        $sth->execute($params);
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
        return $res;
    }

    /**
     * @param $table
     * @param array $val ([key => value, key => value])
     * @param string $id (name = value)
     * @return bool|string (last inserted ID)
     */
    public function set($table, $val, $id = '')
    {
        $val = self::prepareSql($val); //[name, :name=>value, name=:name, :name]
        // если редкатирование существуеющей записи
        if ($id) {
            $sql = 'UPDATE `' . $table . '` SET ' . implode(', ', $val['name=:name']) . ' WHERE ' . $id;
            // echo $sql . PHP_EOL;
            $sth = $this->dbh->prepare($sql);
            $result = $sth->execute($val[':name=>value']);
            // $sth->debugDumpParams();
            return $result;
        } // Добавляем новую запись в базу и возварщаем ее ID
        else {
            $sql = 'INSERT INTO `' . $table . '` (' . implode(', ', $val['name']) . ') VALUES(' . implode(', ',
                    $val[':name']) . ')';
            //echo $sql . PHP_EOL;
            $sth = $this->dbh->prepare($sql);
            $sth->execute($val[':name=>value']);
            //$sth->debugDumpParams();
            return $this->lastInsertId();
        }
    }

    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * @param string $table
     * @param array $column (example: id=>15, name => 'NoName')
     * @return bool
     */
    public function delete($table, array $column)
    {
        $val = self::prepareSql($column); //[name, :name=>value, name=:name, :name]
        $whereNew = implode(' AND ', $val['name=:name']);
        $where = preg_replace('/(\s+$)|(\s+AND\s+$)/', ' ', $whereNew);
        $sql = 'DELETE FROM `' . $table . '` WHERE ' . $where;
        $sth = $this->dbh->prepare($sql);
        $result = $sth->execute($val[':name=>value']);
        //$sth->debugDumpParams();
        return $result;
    }

    /**
     * @param string $table
     * @return array
     */
    public function getNameColumn($table)
    {
        $tableDetails = $this->query_array('SHOW COLUMNS FROM ' . $table);
        foreach ($tableDetails as $k => $detail) {
            $detail = array_change_key_case($detail, CASE_LOWER);
            $columnName[strtolower($detail['field'])] = $detail;
        }
        return $columnName;
    }

    /**
     * @param string $table
     * @return string
     */
    public function getTablePrimaryKey($table)
    {
        $tableDetails = $this->query_array('SHOW COLUMNS FROM ' . $table);
        $PrimaryKey = null;
        foreach ($tableDetails as $k => $detail) {
            $detail = array_change_key_case($detail, CASE_LOWER);
            if ($detail['key'] == 'PRI') {
                $PrimaryKey = $detail['field'];
            }
        }
        return $PrimaryKey;
    }

    /**
     * @param string $table
     * @return array || null
     */
    public function getTableUniqueKey($table)
    {
        $tableDetails = $this->query_array('SHOW COLUMNS FROM ' . $table);
        $uniqueKey = null;
        foreach ($tableDetails as $k => $detail) {
            $detail = array_change_key_case($detail, CASE_LOWER);
            if ($detail['key'] == 'UNI') {
                $uniqueKey[] = $detail['field'];
            }
        }
        return $uniqueKey;
    }

    /**
     * Получаем список всех таблиц в базе
     *
     */
    public function getTableList()
    {
        $sql = "SHOW TABLES";
        return $this->query_array($sql, []);
    }


}
