<?php


namespace App\Model\Admin\Customer;


use App\Model\Interfaces\DefaultMethodTableClass;
use libphonenumber\PhoneNumberUtil;

class CustomerModel implements DefaultMethodTableClass

{
    /**
     * @var string| \App\Classes\ActiveRecord\Main
     */
    private static string $nameTableClass = 'App\Classes\ActiveRecord\Tables\Customer';

    private static array $customerStatusAvailable = [
        'active' => 'Активен',
        'blocked' => 'Заблокирован',
        'confirm_wait' => 'Ожидает подтверждения',
    ];
    private static string $customerStatusDefault = 'active';

    public static function Save($val)
    {
        $returnResult = ['result' => false, 'id' => null, 'returnData' => null, 'error' => null];
        $error = static::checkVal($val);
        if ($error['status'] === true) {
            $returnResult['error'] = $error['text'];
        } else {
            $itemInBase = (isset($val['id']) && $val['id']) ? static::$nameTableClass::findOne($val['id']) : false;
            if (!empty($val['pass'])) {
                $passwordString = $val['pass'];
                $val['pass'] = password_hash($val['pass'], PASSWORD_DEFAULT);
            }
            $val['status'] = static::checkedStatus($val['status'] ?? null);
            $val['phone'] = static::checkedPhone($val['phone'] ?? null);

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

            if (isset($passwordString) && !empty($passwordString)) {
                static::sendPassword($passwordString);
            }
        }

        return $returnResult;
    }

    public static function checkVal($val): array
    {
        $errorText = null;
        $errorStatus = false;
        if (!isset($val['mail']) || !filter_var($val['mail'], FILTER_VALIDATE_EMAIL)) {
            $errorText = $errorText . 'Поле почта обязательно к заполнению. ';
            $errorStatus = true;
        }
        if (isset($val['phone']) && !empty($val['phone']) && !static::checkedPhone($val['phone'])) {
            $errorText = $errorText . 'Введите существующий номер телефона. ';
            $errorStatus = true;
        }
        return ['status' => $errorStatus, 'text' => $errorText];
    }

    /**
     * Проверяет на то что такой статус разрешен, в случае успеха возвращает его, иначе $customerStatusDefault.
     * @param string|null $status
     * @return string
     */
    public static function checkedStatus(string $status = null): string
    {
        return isset(static::$customerStatusAvailable[$status]) ? $status : static::$customerStatusDefault;
    }

    /**
     * Проверяет на то что указан верный номер, в случае успеха возвращает его, иначе пусту строку;
     * @param string|null $phone
     * @return string|null
     */
    public static function checkedPhone(string $phone = null): ?string
    {
        $phoneCheckedString = null;
        if ($phone) {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $phoneProto = $phoneUtil->parse($phone, "RU");
                $phoneCheckedString = $phoneUtil->format($phoneProto, \libphonenumber\PhoneNumberFormat::E164);
            } catch (\libphonenumber\NumberParseException $e) {

            }
        }
        return $phoneCheckedString;
    }

    /**
     * Отправляем новый пароль покупателю
     * @param string $newPass
     * @return void
     */
    public static function sendPassword($newPass): void
    {
        //TODO переписать с использованием очередей
    }

    /**
     * @param $id
     * @return false[]
     */
    public static function Delete($id): array
    {
        $result = false;
        if ($item = static::$nameTableClass::findOne($id)) {
            $item->deleted = true; //т.к. покупатели не удаляются из базы, то просто помечаем что он удален.
            $result = $item->save();
        }
        return ['result' => $result];
    }
}