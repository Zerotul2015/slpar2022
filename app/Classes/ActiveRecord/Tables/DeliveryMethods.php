<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed name regular read/write property
 * @property mixed description regular read/write property
 * @property mixed price regular read/write property
 * @property mixed sum_for_free regular read/write property
 * @property mixed sumForFree regular read/write property
 * @property mixed enable regular read/write property
 * @property mixed protected regular read/write property
 */

class DeliveryMethods extends Main
{
    static string $tableName = 'delivery_methods'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\DeliveryMethods'; 

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
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'description' => [
            'field' => 'description',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'price' => [
            'field' => 'price',
            'type' => 'int',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'sum_for_free' => [
            'field' => 'sum_for_free',
            'type' => 'int unsigned',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'enable' => [
            'field' => 'enable',
            'type' => 'tinyint(1)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'protected' => [
            'field' => 'protected',
            'type' => 'tinyint(1)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = []; 

}