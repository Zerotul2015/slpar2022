<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed image_header regular read/write property
 * @property mixed imageHeader regular read/write property
 * @property mixed slider regular read/write property
 * @property mixed seo regular read/write property
 * @property mixed layout regular read/write property
 * @property mixed folder regular read/write property
 */

class SettingsIndexPage extends Main
{
    static string $tableName = 'settings_index_page'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\SettingsIndexPage'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'image_header' => [
            'field' => 'image_header',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'slider' => [
            'field' => 'slider',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
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
        'layout' => [
            'field' => 'layout',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'folder' => [
            'field' => 'folder',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = []; 

}