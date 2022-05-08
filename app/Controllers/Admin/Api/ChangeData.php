<?php


namespace App\Controllers\Admin\Api;

use App\Classes\ActiveRecord\Tables\Customer;
use App\Classes\ActiveRecord\Tables\CustomerCompany;
use App\Classes\ActiveRecord\Tables\Orders;
use App\Model\Admin\Menu\MenuModel;
use App\Model\Admin\Settings\SettingsBannerModel;
use App\Model\Admin\Settings\SettingsLayoutsModel;
use App\Model\Admin\Settings\SettingsModel;
use App\Model\Admin\Settings\SettingsNotificationsModel;
use App\Model\Admin\Settings\SettingsUsersModel;

class ChangeData extends Main
{

    public function page()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Page\PageModel'));
    }
    public function pageCategory()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Page\PageCategoryModel'));
    }

    public function bathStyle()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\BathStyle\BathStyleModel'));
    }

    public function product()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Product\ProductModel'));
    }

    public function productCategory()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Product\ProductCategoryModel'));
    }

    public function productManufacturer()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Product\ProductManufacturerModel'));
    }

    public function productUnit()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Product\ProductUnitModel'));
    }

    public function productStockStatus()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Product\ProductStockStatusModel'));
    }

    public function banners()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Settings\SettingsBannerModel'));
    }

    public function users()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Settings\SettingsUsersModel'));
    }

    public function layouts()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Settings\SettingsLayoutsModel'));
    }

    public function settings()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Settings\SettingsModel'));
    }

    public function settingsNotifications()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Settings\SettingsNotificationsModel'));
    }

    public function menu()
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Menu\MenuModel'));
    }

    public function customerCompany(): void
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Customer\CustomerCompanyModel'));
    }

    public function customer(): void
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Customer\CustomerModel'));
    }

    public function wholesaleLevel(): void
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Wholesale\WholesaleLevelModel'));
    }
    public function wholesaleCustomer(): void
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Wholesale\WholesaleCustomerModel'));
    }

    public function discount(): void
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Discount\DiscountModel'));
    }
    public function promoCode(): void
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\PromoCode\PromoCodeModel'));
    }

    public function deliveryMethods(): void
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\DeliveryMethods\DeliveryMethodsModel'));
    }
    public function paymentMethods(): void
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\PaymentMethods\PaymentMethodsModel'));
    }

    public function orders(): void
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Orders\OrdersModel'));
    }
    public function ordersStatus(): void
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Orders\OrdersStatusModel'));
    }

}