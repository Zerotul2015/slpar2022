<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed customer_id regular read/write property
 * @property mixed customerId regular read/write property
 * @property mixed wholesale_level_id regular read/write property
 * @property mixed wholesaleLevelId regular read/write property
 * @property mixed details regular read/write property
 */

class WholesaleCustomer extends Main
{
    static string $tableName = 'wholesale_customer'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\WholesaleCustomer'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'customer_id' => [
            'field' => 'customer_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'wholesale_level_id' => [
            'field' => 'wholesale_level_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'details' => [
            'field' => 'details',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = []; 

}