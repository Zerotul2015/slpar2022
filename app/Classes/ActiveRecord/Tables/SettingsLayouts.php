<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed layout_for regular read/write property
 * @property mixed layoutFor regular read/write property
 * @property mixed is_default regular read/write property
 * @property mixed isDefault regular read/write property
 * @property mixed for_id regular read/write property
 * @property mixed forId regular read/write property
 * @property mixed blocks regular read/write property
 */

class SettingsLayouts extends Main
{
    static string $tableName = 'settings_layouts'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\SettingsLayouts'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'layout_for' => [
            'field' => 'layout_for',
            'type' => 'varchar(32)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'is_default' => [
            'field' => 'is_default',
            'type' => 'tinyint(1)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'for_id' => [
            'field' => 'for_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'blocks' => [
            'field' => 'blocks',
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