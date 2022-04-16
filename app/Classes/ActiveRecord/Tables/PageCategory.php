<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed name_short regular read/write property
 * @property mixed nameShort regular read/write property
 * @property mixed name_full regular read/write property
 * @property mixed nameFull regular read/write property
 * @property mixed description regular read/write property
 * @property mixed seo regular read/write property
 * @property mixed parent regular read/write property
 * @property mixed priority regular read/write property
 * @property mixed url regular read/write property
 */

class PageCategory extends Main
{
    static string $tableName = 'page_category'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\PageCategory'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'name_short' => [
            'field' => 'name_short',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => 'без названия',
            'extra' => ''
        ],
        'name_full' => [
            'field' => 'name_full',
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
        'seo' => [
            'field' => 'seo',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'parent' => [
            'field' => 'parent',
            'type' => 'bigint',
            'null' => 'YES',
            'key' => '',
            'default' => '',
            'extra' => ''
        ],
        'priority' => [
            'field' => 'priority',
            'type' => 'int',
            'null' => 'NO',
            'key' => '',
            'default' => '',
            'extra' => ''
        ],
        'url' => [
            'field' => 'url',
            'type' => 'varchar(255)',
            'null' => 'NO',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = []; 

}