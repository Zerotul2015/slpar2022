<?php


namespace App\Controllers\Admin\Api;


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
use App\Classes\ActiveRecord\Tables\PromoCode;
use App\Classes\ActiveRecord\Tables\Settings;
use App\Classes\ActiveRecord\Tables\SettingsBanner;
use App\Classes\ActiveRecord\Tables\SettingsLayouts;
use App\Classes\ActiveRecord\Tables\SettingsNotifications;
use App\Classes\ActiveRecord\Tables\Users;
use App\Classes\ActiveRecord\Tables\WholesaleCustomer;
use App\Classes\ActiveRecord\Tables\WholesaleLevel;


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
        $object->select(['name','phone','mail','status','note_hidden','is_wholesale']);
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function wholesaleLevel(): void
    {
        $object = WholesaleLevel::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function wholesaleCustomer(): void
    {
        $object = WholesaleCustomer::find();
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

    public function deliveryMethods(): void
    {
        $object = DeliveryMethods::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }
    public function paymentMethods(): void
    {
        $object = PaymentMethods::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }

    public function orders(): void
    {
        $object = Orders::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }
    public function ordersStatus(): void
    {
        $object = OrdersStatus::find();
        $this->returnAnswer($this->prepareReturnData($object));
    }



}