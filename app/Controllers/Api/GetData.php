<?php


namespace App\Controllers\Api;


use App\Classes\ActiveRecord\Tables\BathStyle;
use App\Classes\ActiveRecord\Tables\Customer;
use App\Classes\ActiveRecord\Tables\CustomerCompany;
use App\Classes\ActiveRecord\Tables\DeliveryMethods;
use App\Classes\ActiveRecord\Tables\Discount;
use App\Classes\ActiveRecord\Tables\GalleryCategory;
use App\Classes\ActiveRecord\Tables\Menu;
use App\Classes\ActiveRecord\Tables\Orders;
use App\Classes\ActiveRecord\Tables\OrdersStatus;
use App\Classes\ActiveRecord\Tables\Page;
use App\Classes\ActiveRecord\Tables\PageCategory;
use App\Classes\ActiveRecord\Tables\PaymentMethods;
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
use App\Model\BathStyle\BathStyleModel;
use App\Model\PublicTemplateModel;
use App\Model\Shop\Product\ProductModel;
use JetBrains\PhpStorm\ArrayShape;


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
        if (is_array($productsFound)) {
            foreach ($productsFound as $k => $itemProduct) {
                if (!$itemProduct->enable) {
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
        $return = $this->prepareReturnData($object);
        if (!empty($return['returnData'])) {
            $return['returnData'] = ProductModel::prepareProductsForTemplate($return['returnData']);
        }
        $this->returnAnswer($return);
    }

    public function productRelated(): void
    {
        $return = ['result' => false, 'returnData' => null];
        $object = Product::find();
        $productBase = $this->prepareReturnData($object)['returnData'];
        if (!empty($productBase)) {
            $return['returnData'] = ProductModel::getRelatedProducts($productBase[0]->id);
            $return['result'] = true;
        }
        $this->returnAnswer($return);
    }

    /**
     * Если передать getProductsData = true и bathStyleId, тогда возвращает массив товаров и категорий для стиля
     * Иначе рабатет как стандартный метод получения данных через prepareReturnData()
     * @return void
     */
    public function bathStyle(): void
    {
        if (isset($this->postData['getProductsData'], $this->postData['bathStyleId']) && $this->postData['getProductsData'] === true) {
            $returnData = [
                'result' => true,
                'returnData' => BathStyleModel::getProductsData($this->postData['bathStyleId'])];
        } else {
            $object = BathStyle::find();
            $returnData = $this->prepareReturnData($object);
        }
        $this->returnAnswer($returnData);
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

    public function templateData(): void
    {
        $section = $this->postData['section'] ?? '';
        $sectionKey = $this->postData['sectionKey'] ?? '';
        $simple = $this->postData['simple'] ?? false;
        $simple = !!$simple;
        $this->returnAnswer(PublicTemplateModel::templateSettings($section, $sectionKey, $simple));
    }

    public function discount(): void
    {
        $object = Discount::find()->where(['enable' => 1]);
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function deliveryMethods(): void
    {
        $object = DeliveryMethods::find()->where(['enable' => 1]);
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function paymentMethods(): void
    {
        $object = PaymentMethods::find()->where(['enable' => 1]);
        $this->returnAnswer($this->prepareReturnData($object));
    }


}