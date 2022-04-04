<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed banner_name regular read/write property
 * @property mixed bannerName regular read/write property
 * @property mixed image regular read/write property
 * @property mixed href regular read/write property
 * @property mixed description regular read/write property
 */

class SettingsBanner extends Main
{
    static string $tableName = 'settings_banner'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\SettingsBanner'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'banner_name' => [
            'field' => 'banner_name',
            'type' => 'varchar(64)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'image' => [
            'field' => 'image',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'href' => [
            'field' => 'href',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'description' => [
            'field' => 'description',
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