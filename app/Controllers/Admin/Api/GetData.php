<?php


namespace App\Controllers\Admin\Api;


use App\Classes\ActiveRecord\Tables\BathStyle;
use App\Classes\ActiveRecord\Tables\Customer;
use App\Classes\ActiveRecord\Tables\CustomerCompany;
use App\Classes\ActiveRecord\Tables\Discount;
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
use App\Classes\ActiveRecord\Tables\PromoCode;
use App\Classes\ActiveRecord\Tables\Settings;
use App\Classes\ActiveRecord\Tables\SettingsBanner;
use App\Classes\ActiveRecord\Tables\SettingsLayouts;
use App\Classes\ActiveRecord\Tables\SettingsNotifications;
use App\Classes\ActiveRecord\Tables\Users;


/**
 * Класс для получения данных из базы
 * Class GetData
 * @package App\Controllers\Admin\Api
 */
class GetData extends Main
{

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


    public function users(): void
    {
        $this->postData['select'] = ['id', 'login', 'access_level'];
        $object = Users::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function settings(): void
    {
        if ($returnData = Settings::findOne(1)) {
            $result = true;
        } else {
            $result = false;
        }

        $this->returnAnswer(['result' => $result, 'returnData' => $returnData]);
    }

    public function settingsNotifications(): void
    {
        if ($returnData = SettingsNotifications::findOne(1)) {
            $result = true;
        } else {
            $result = false;
        }

        $this->returnAnswer(['result' => $result, 'returnData' => $returnData]);
    }

    public function menu(): void
    {
        $object = Menu::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function customerCompany(): void
    {
        $object = CustomerCompany::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function customer(): void
    {
        $object = Customer::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function orders(): void
    {
        $object = Orders::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }
    public function discount(): void
    {
        $object = Discount::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function promoCode(): void
    {
        $object = PromoCode::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function ordersStatus(): void
    {
        $object = OrdersStatus::find();
        $this->returnAnswer($this->prepareReturnData($object));
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

}