<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed section regular read/write property
 * @property mixed seo regular read/write property
 * @property mixed title regular read/write property
 * @property mixed description regular read/write property
 */

class SettingsSections extends Main
{
    static string $tableName = 'settings_sections'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\SettingsSections'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'section' => [
            'field' => 'section',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'UNI',
            'default' => null,
            'extra' => ''
        ],
        'seo' => [
            'field' => 'seo',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'title' => [
            'field' => 'title',
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
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = ['section' => '']; 

}