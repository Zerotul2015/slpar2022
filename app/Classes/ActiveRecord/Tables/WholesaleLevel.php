<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed name regular read/write property
 * @property mixed discount_default regular read/write property
 * @property mixed discountDefault regular read/write property
 * @property mixed message_enter regular read/write property
 * @property mixed messageEnter regular read/write property
 * @property mixed message_exit regular read/write property
 * @property mixed messageExit regular read/write property
 * @property mixed min_sum_orders_to_enter regular read/write property
 * @property mixed minSumOrdersToEnter regular read/write property
 */

class WholesaleLevel extends Main
{
    static string $tableName = 'wholesale_level'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\WholesaleLevel'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'name' => [
            'field' => 'name',
            'type' => 'varchar(128)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'discount_default' => [
            'field' => 'discount_default',
            'type' => 'int',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'message_enter' => [
            'field' => 'message_enter',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'message_exit' => [
            'field' => 'message_exit',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'min_sum_orders_to_enter' => [
            'field' => 'min_sum_orders_to_enter',
            'type' => 'int',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = []; 

}