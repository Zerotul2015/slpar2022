<?php

namespace App\Classes;

use JetBrains\PhpStorm\ArrayShape;

class PhoneParse
{

    /**
     * Проверяет номер на корректность.
     * Возвращает телефонный номер приведенный к стандарту. Или текст ошибки.
     * @param $phoneString
     * @return array
     */
    #[ArrayShape(['result' => "false", 'phone' => "string", 'error' => "null|string"])]
    public static function parsePhoneString($phoneString): array
    {
        $returnData = ['result' => false, 'phone' => '', 'error' => null];
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $swissNumberProto = $phoneUtil->parse($phoneString);
            if ($isValid = $phoneUtil->isValidNumber($swissNumberProto)) {
                $returnData['result'] = true;
                $returnData['phone'] = $phoneUtil->format($swissNumberProto, \libphonenumber\PhoneNumberFormat::E164);
            }else{
                $returnData['error'] ='Номер телефона указан не верно!';
            }
        } catch (\libphonenumber\NumberParseException $e) {
            $returnData['error'] = $e;
        }
        return $returnData;
    }
}