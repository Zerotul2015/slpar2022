<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed category_name regular read/write property
 * @property mixed categoryName regular read/write property
 * @property mixed url regular read/write property
 * @property mixed folder regular read/write property
 * @property mixed category_description regular read/write property
 * @property mixed categoryDescription regular read/write property
 * @property mixed seo regular read/write property
 * @property mixed connection_products regular read/write property
 * @property mixed connectionProducts regular read/write property
 * @property mixed connection_category_products regular read/write property
 * @property mixed connectionCategoryProducts regular read/write property
 * @property mixed connection_services regular read/write property
 * @property mixed connectionServices regular read/write property
 */

class GalleryCategory extends Main
{
    static string $tableName = 'gallery_category'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\GalleryCategory'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'category_name' => [
            'field' => 'category_name',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'url' => [
            'field' => 'url',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => 'UNI',
            'default' => null,
            'extra' => ''
        ],
        'folder' => [
            'field' => 'folder',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => 'UNI',
            'default' => null,
            'extra' => ''
        ],
        'category_description' => [
            'field' => 'category_description',
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
        'connection_products' => [
            'field' => 'connection_products',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'connection_category_products' => [
            'field' => 'connection_category_products',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'connection_services' => [
            'field' => 'connection_services',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = ['url' => '','folder' => '']; 

}