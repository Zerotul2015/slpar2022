<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed name regular read/write property
 * @property mixed position regular read/write property
 * @property mixed items regular read/write property
 * @property mixed priority regular read/write property
 * @property mixed enable regular read/write property
 */

class Menu extends Main
{
    static string $tableName = 'menu'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\Menu'; 

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
            'type' => 'varchar(64)',
            'null' => 'NO',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'position' => [
            'field' => 'position',
            'type' => 'varchar(32)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'items' => [
            'field' => 'items',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'priority' => [
            'field' => 'priority',
            'type' => 'int',
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
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = []; 

}