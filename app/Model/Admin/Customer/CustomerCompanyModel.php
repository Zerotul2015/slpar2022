<?php


namespace App\Model\Admin\Customer;


use App\Classes\ActiveRecord\Tables\Customer;
use App\Model\Interfaces\DefaultMethodTableClass;

class CustomerCompanyModel implements DefaultMethodTableClass
{
    /**
     * @var string| \App\Classes\ActiveRecord\Main
     */
    private static string $nameTableClass = 'App\Classes\ActiveRecord\Tables\CustomerCompany';

    public static function Save($val)
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null, 'error' => null];
        $error = static::checkVal($val);
        if($error['status'] === true){
            $returnResult['error'] = $error['text'];
        }else{
            $itemInBase = (isset($val['id']) && $val['id']) ? static::$nameTableClass::findOne($val['id']) : false;

            /** @noinspection DuplicatedCode */
            $itemForSave = $itemInBase ? $itemInBase->set($val) : static::$nameTableClass::create($val);

            $resultSave = $itemForSave->save();
            if (!$itemForSave->id) {
                $itemForSave->id = $resultSave;
            }
            $returnResult['id'] = $itemForSave->id;
            if ($resultSave) {
                $returnResult['result'] = true;
            }
            $returnResult['returnData'] = $itemForSave;
        }
        return $returnResult;
    }

    public static function checkVal($val): array
    {
        $errorText = null;
        $errorStatus = false;
        if (!isset($val['customer_id']) || empty($val['customer_id']) || !Customer::findOne($val['customer_id'])) {
            $errorStatus = true;
            $errorText = $errorText . 'Нельзя добавить контрагента не указав к какому покупателю он привязан';
        }
        if (!isset($val['name']) || empty($val['name'])) {
            $errorStatus = true;
            $errorText = $errorText . 'Заполните Наименование';
        }
        if (!isset($val['inn'])) {
            $errorStatus = true;
            $errorText = $errorText . 'Заполните ИНН';
        }else{
            $lenghtINN = iconv_strlen($val['inn'], 'UTF-8');
            if($lenghtINN != 12 && $lenghtINN !=10){
                $errorStatus = true;
                $errorText = $errorText . ' Длина ИНН должна быть 10 или 12 цифр.';
            }
        }
        if (isset($val['kpp']) && !empty($val['kpp']) && iconv_strlen($val['kpp'], 'UTF-8') != 9) {
            $errorStatus = true;
            $errorText = $errorText . ' КПП должен состоять из 9 цифр, для ИП не указывается.';
        }
        return ['status'=>$errorStatus, 'text'=>$errorText];
    }

    public static function Delete($id)
    {
        $result = false;
        if ($item = static::$nameTableClass::findOne($id)) {
            $item->deleted = true;
            $result = $item->save();
        }
        return ['result' => $result];
    }
}