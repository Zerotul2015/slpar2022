<?php


namespace App\Controllers\Api;


use App\Controllers\Authorization;
use JetBrains\PhpStorm\ArrayShape;

abstract class Main
{
    public bool $isAuth = false;
    public bool|int $isWholesale = 0;
    public mixed $customerId = null;
    public array $postData = [];
    public string $action = ''; // save, delete, get
    public array $where = [];
    public string $limit = ''; // int count
    public array $values = []; //data
    public array $returnData = ['result'=>false, 'returnData'=> null]; //data

    public function __construct()
    {
        //auth
        $resultAuthCheck = Authorization::isAuth();
        $this->isAuth = $resultAuthCheck['result'];
        $this->customerId = $resultAuthCheck['customerId'];
        $this->isWholesale = $resultAuthCheck['isWholesale'];
        //prepare post data
        $postData = file_get_contents("php://input");
        if (!empty($postData)) {
            $this->postData = json_decode($postData, true);
        }

        if (isset($this->postData['limit']) && is_int($this->postData['limit'])) {
            $this->limit = $this->postData['limit'];
        }
        if (isset($this->postData['where']) && is_array($this->postData['where'])) {
            $this->where = $this->postData['where'];
        }
        if (isset($this->postData['action'])) {
            $this->action = $this->postData['action'];
        }
        if (isset($this->postData['values'])) {
            $this->values = $this->postData['values'];
        }

    }

    /**
     * Получает данные для запроса из $this->postData;
     *  indexBy - Имя колонки по для индексации значений;
     *  where - Имя колонки в которой будет поиск совпадения строки searchString
     *      searchString - Строка для поиска в колонке указанной в where
     *      condition - Знак сравнения со строкой searchString, допустимые значение(регистр не важен): '=','<>','like','not like'
     *      group - ключ группы условий
     *      groupCondition - Условие группы 'OR' или 'AND'
     *  andWhere - Массив с дополнительными условиями, каждый элемент это массив с ключами where, searchString, и не обязательные condition, group, groupCondition
     *  count - Если указан, вернется количество записей удовлетворяющих заданным условиям
     *  select - массив со списком нужных колонок или строка с именем колонки
     *  pagination - для постраничного вывода, массив с ключами perPage(к-во записей на страницу) и page(нужная страница)
     *  limit - срез записей с указанными отступами массив с двумя ключами, 0(начальный элемент), 1(конечный)
     * @param \App\Classes\ActiveRecord\Main $object
     * @return array
     */
    #[ArrayShape(['result' => "bool", 'returnData' => "mixed"])]
    public function prepareReturnData(\App\Classes\ActiveRecord\Main $object): array
    {
        $result = false;
        $returnData = null;
        $postData = $this->postData;
        //индексирование
        if (isset($postData['indexBy'])) {
            $object->indexBy($postData['indexBy']);
        }
        //отбор по значению колонки
        if (isset($postData['where'], $postData['searchString'])) {
            $condition = $postData['condition'] ?? '=';
            $group = $postData['group'] ?? 0;
            $groupCondition = $postData['groupCondition'] ?? 'AND';
            $object->where($postData['where'], $postData['searchString'], $condition, $group, $groupCondition);
        }
        // массив значений для отбора по значению колонки
        if (isset($postData['andWhere']) && is_array($postData['andWhere']) && !empty($postData['andWhere'])) {
            foreach ($postData['andWhere'] as $andWhere) {
                $condition = $andWhere['condition'] ?? '=';
                $group = $andWhere['group'] ?? 0;
                $groupCondition = $andWhere['groupCondition'] ?? 'AND';
                $object->andWhere($andWhere['where'], $andWhere['searchString'], $condition, $group, $groupCondition);
            }
        }
        //если запросили количество подходящих записей в базе, а не сами данные
        if (isset($postData['count'])) {
            $returnData = $object->count();
            $result = true;
        } else {// иначе все остальное
            //выбираем только нужные поля, если требуется.
            if (isset($postData['select'])) {
                $object->select($postData['select']);
            }
            //для постраничного вывода
            if (isset($postData['pagination'])) {
                $pagination = $postData['pagination'];
                if (isset($pagination['page'], $pagination['perPage'])) {
                    if ($returnData = $object->pagination($pagination['page'], $pagination['perPage'])) {
                        $result = true;
                    }
                }
            } else {//иначе только данные
                // лимит
                if (isset($postData['limit'])) {
                    $object->limit($postData['limit']);
                }
                //если нужно сгруппировать данные по указанному полю
                if (isset($postData['groupBy']) && !empty($postData['groupBy'])) {
                    $returnData = $object->findGroupBy($postData['groupBy']);
                } else {//без группировки
                    $returnData = $object->all();
                }

                $result = true;
            }
        }
        return ['result' => $result, 'returnData' => $returnData];
    }

    public function returnAnswer($returnArray)
    {
        header('Content-Type: application/json');
        echo json_encode($returnArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    }
}