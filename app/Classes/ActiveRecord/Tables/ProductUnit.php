<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed code regular read/write property
 * @property mixed name regular read/write property
 * @property mixed symbol_national regular read/write property
 * @property mixed symbolNational regular read/write property
 * @property mixed symbol_international regular read/write property
 * @property mixed symbolInternational regular read/write property
 */

class ProductUnit extends Main
{
    static string $tableName = 'product_unit'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\ProductUnit'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'code' => [
            'field' => 'code',
            'type' => 'varchar(4)',
            'null' => 'YES',
            'key' => 'UNI',
            'default' => null,
            'extra' => ''
        ],
        'name' => [
            'field' => 'name',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'symbol_national' => [
            'field' => 'symbol_national',
            'type' => 'varchar(64)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'symbol_international' => [
            'field' => 'symbol_international',
            'type' => 'varchar(64)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = ['code' => '']; 

}