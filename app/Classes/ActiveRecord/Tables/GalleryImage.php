<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed title regular read/write property
 * @property mixed gallery_category regular read/write property
 * @property mixed galleryCategory regular read/write property
 * @property mixed image regular read/write property
 * @property mixed connection_products regular read/write property
 * @property mixed connectionProducts regular read/write property
 * @property mixed connection_category_products regular read/write property
 * @property mixed connectionCategoryProducts regular read/write property
 */

class GalleryImage extends Main
{
    static string $tableName = 'gallery_image'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\GalleryImage'; 

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
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'gallery_category' => [
            'field' => 'gallery_category',
            'type' => 'text',
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
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = []; 

}