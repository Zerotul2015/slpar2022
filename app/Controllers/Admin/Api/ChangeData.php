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

    public function orders(): void
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Orders\OrdersModel'));
    }
    public function ordersStatus(): void
    {
        $this->printResultChangeForAjax($this->wrapperApplyChange('App\Model\Admin\Orders\OrdersStatusModel'));
    }


    /**
     * Метод обертка для типового редактирования и удаления обекта
     * @param $nameModel
     * @return false|array
     */
    public function wrapperApplyChange($nameModel)
    {
        $result = false;
        if ($this->action) {
            switch ($this->action) {
                case 'save':
                    if (!empty($this->values)) {
                        $result = $nameModel::Save($this->values);
                    }
                    break;
                case 'delete':
                    if (isset($this->values['id'])) {
                        $id = $this->values['id'];
                        $result = $nameModel::Delete($id);
                    }
                    break;
            }
        }
        return $result;
    }


    /**
     * Формирует объект для ответа на запрос изменений данных в базе а также выводит его.
     * @param $result
     */
    public static function printResultChangeForAjax($result)
    {
        if (is_numeric($result)) {
            $returnResult = ['result' => (bool)$result, 'id' => $result];
        } elseif (is_array($result)) {
            $returnResult = $result;
        } else {
            $returnResult = ['result' => $result];
        }
        header('Content-Type: application/json');
        echo json_encode($returnResult);
    }
}