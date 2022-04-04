<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed secret_key regular read/write property
 * @property mixed secretKey regular read/write property
 * @property mixed shop_id regular read/write property
 * @property mixed shopId regular read/write property
 */

class PaymentYandex extends Main
{
    static string $tableName = 'payment_yandex'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\PaymentYandex'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'secret_key' => [
            'field' => 'secret_key',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'shop_id' => [
            'field' => 'shop_id',
            'type' => 'varchar(32)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = []; 

}