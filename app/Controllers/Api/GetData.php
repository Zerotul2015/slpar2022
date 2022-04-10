<?php


namespace App\Controllers\Api;


use App\Classes\ActiveRecord\Tables\BathStyle;
use App\Classes\ActiveRecord\Tables\Customer;
use App\Classes\ActiveRecord\Tables\CustomerCompany;
use App\Classes\ActiveRecord\Tables\GalleryCategory;
use App\Classes\ActiveRecord\Tables\Menu;
use App\Classes\ActiveRecord\Tables\Orders;
use App\Classes\ActiveRecord\Tables\OrdersStatus;
use App\Classes\ActiveRecord\Tables\Page;
use App\Classes\ActiveRecord\Tables\PageCategory;
use App\Classes\ActiveRecord\Tables\Product;
use App\Classes\ActiveRecord\Tables\ProductCategory;
use App\Classes\ActiveRecord\Tables\ProductManufacturer;
use App\Classes\ActiveRecord\Tables\ProductStockStatus;
use App\Classes\ActiveRecord\Tables\ProductUnit;
use App\Classes\ActiveRecord\Tables\Settings;
use App\Classes\ActiveRecord\Tables\SettingsBanner;
use App\Classes\ActiveRecord\Tables\SettingsLayouts;
use App\Classes\ActiveRecord\Tables\SettingsNotifications;
use App\Classes\ActiveRecord\Tables\Users;
use App\Model\PublicTemplateModel;


/**
 * Класс для получения данных из базы
 * Class GetData
 * @package App\Controllers\Admin\Api
 */
class GetData extends Main
{

    //Поиск по сайту (товары, категории, страницы)
    public function searchSite(): void
    {

        $productsCategory = ProductCategory::find();
        $products = Product::find();
        $pages = Page::find();

        $postData = $this->postData;
        $searchString = $postData['searchString'];
        $postData['condition'] = 'like';
        //products

        $postData['andWhere'] = [
            [
                'where' => 'name',
                'searchString' => $searchString,
                'condition' => 'like',
                'group' => '0',
                'groupCondition' => 'OR'],
            [
                'where' => 'article',
                'searchString' => $searchString,
                'condition' => 'like',
                'group' => '0',
                'groupCondition' => 'OR',
            ],
        ];
        $this->postData = $postData;
        $productsFound = $this->prepareReturnData($products)['returnData'];
        //фильтруем отключенные товары
        if(is_array($productsFound)){
            foreach ($productsFound as $k => $itemProduct){
                if(!$itemProduct->enable){
                    unset($productsFound[$k]);
                }
            }
        }
        //productsCategory
        $postData['andWhere'] = [
            [
                'where' => 'name',
                'searchString' => $searchString,
                'condition' => 'like',
                'group' => '0',
                'groupCondition' => 'OR']
        ];
        $this->postData = $postData;
        $productsCategoryFound = $this->prepareReturnData($productsCategory)['returnData'];
        //page
        $postData['andWhere'] = [
            [
                'where' => 'title',
                'searchString' => $searchString,
                'condition' => 'like',
                'group' => '0',
                'groupCondition' => 'OR']
        ];
        $this->postData = $postData;
        $pagesFound = $this->prepareReturnData($pages)['returnData'];

        $returnData = ['result' => true, 'returnData' => [
            'productsCategory' => $productsCategoryFound, 'products' => $productsFound, 'pages' => $pagesFound]
        ];
        $this->returnAnswer($returnData);
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
                //если нужно сгруппировать данные по указаному полю
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

    public function productCategory(): void
    {
        $object = ProductCategory::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function productManufacturer(): void
    {
        $object = ProductManufacturer::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function productUnit(): void
    {
        $object = ProductUnit::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function productStockStatus(): void
    {
        $object = ProductStockStatus::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function product(): void
    {
        $object = Product::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function bathStyle(): void
    {
        $object = BathStyle::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function galleryCategory(): void
    {
        $object = GalleryCategory::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function banners(): void
    {
        $object = SettingsBanner::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function page(): void
    {
        $object = Page::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function pageCategory(): void
    {
        $object = PageCategory::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function layouts(): void
    {
        $object = SettingsLayouts::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function templateSettings(): void
    {

        $this->returnAnswer(PublicTemplateModel::templateSettings());
    }

}