<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed sid regular read/write property
 * @property mixed products regular read/write property
 * @property mixed promo_code_used regular read/write property
 * @property mixed promoCodeUsed regular read/write property
 * @property mixed date_last_edit regular read/write property
 * @property mixed dateLastEdit regular read/write property
 */

class Cart extends Main
{
    static string $tableName = 'cart'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\Cart'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'sid' => [
            'field' => 'sid',
            'type' => 'varchar(255)',
            'null' => 'NO',
            'key' => 'UNI',
            'default' => null,
            'extra' => ''
        ],
        'products' => [
            'field' => 'products',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'promo_code_used' => [
            'field' => 'promo_code_used',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'date_last_edit' => [
            'field' => 'date_last_edit',
            'type' => 'timestamp',
            'null' => 'YES',
            'key' => '',
            'default' => 'CURRENT_TIMESTAMP',
            'extra' => 'DEFAULT_GENERATED'
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = ['sid' => '']; 

}