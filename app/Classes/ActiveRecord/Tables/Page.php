<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed title regular read/write property
 * @property mixed category_id regular read/write property
 * @property mixed categoryId regular read/write property
 * @property mixed style regular read/write property
 * @property mixed images regular read/write property
 * @property mixed folder regular read/write property
 * @property mixed seo regular read/write property
 * @property mixed description regular read/write property
 * @property mixed content regular read/write property
 * @property mixed url regular read/write property
 * @property mixed integrated regular read/write property
 * @property mixed url_integrated regular read/write property
 * @property mixed urlIntegrated regular read/write property
 * @property mixed date regular read/write property
 */

class Page extends Main
{
    static string $tableName = 'page'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\Page'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'title' => [
            'field' => 'title',
            'type' => 'varchar(255)',
            'null' => 'NO',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'category_id' => [
            'field' => 'category_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'style' => [
            'field' => 'style',
            'type' => 'varchar(32)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'images' => [
            'field' => 'images',
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
        ],
        'seo' => [
            'field' => 'seo',
            'type' => 'text',
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
        'content' => [
            'field' => 'content',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'url' => [
            'field' => 'url',
            'type' => 'varchar(255)',
            'null' => 'NO',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'integrated' => [
            'field' => 'integrated',
            'type' => 'tinyint(1)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'url_integrated' => [
            'field' => 'url_integrated',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'date' => [
            'field' => 'date',
            'type' => 'timestamp',
            'null' => 'YES',
            'key' => '',
            'default' => 'CURRENT_TIMESTAMP',
            'extra' => 'DEFAULT_GENERATED'
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = []; 

}