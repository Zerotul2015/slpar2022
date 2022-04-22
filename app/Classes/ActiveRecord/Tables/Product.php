<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed name regular read/write property
 * @property mixed article regular read/write property
 * @property mixed specifications regular read/write property
 * @property mixed description regular read/write property
 * @property mixed seo regular read/write property
 * @property mixed images regular read/write property
 * @property mixed image_main regular read/write property
 * @property mixed imageMain regular read/write property
 * @property mixed videos regular read/write property
 * @property mixed attachments regular read/write property
 * @property mixed folder regular read/write property
 * @property mixed category_id regular read/write property
 * @property mixed categoryId regular read/write property
 * @property mixed manufacturer_id regular read/write property
 * @property mixed manufacturerId regular read/write property
 * @property mixed unit_id regular read/write property
 * @property mixed unitId regular read/write property
 * @property mixed dimensions regular read/write property
 * @property mixed stock_status_id regular read/write property
 * @property mixed stockStatusId regular read/write property
 * @property mixed url regular read/write property
 * @property mixed product_options regular read/write property
 * @property mixed productOptions regular read/write property
 * @property mixed price regular read/write property
 * @property mixed price_old regular read/write property
 * @property mixed priceOld regular read/write property
 * @property mixed price_on_request regular read/write property
 * @property mixed priceOnRequest regular read/write property
 * @property mixed priority regular read/write property
 * @property mixed enable regular read/write property
 * @property mixed products_related regular read/write property
 * @property mixed productsRelated regular read/write property
 * @property mixed bath_style_id regular read/write property
 * @property mixed bathStyleId regular read/write property
 */

class Product extends Main
{
    static string $tableName = 'product'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\Product'; 

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
            'null' => 'NO',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'article' => [
            'field' => 'article',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => 'UNI',
            'default' => null,
            'extra' => ''
        ],
        'specifications' => [
            'field' => 'specifications',
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
        'seo' => [
            'field' => 'seo',
            'type' => 'text',
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
        'image_main' => [
            'field' => 'image_main',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'videos' => [
            'field' => 'videos',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'attachments' => [
            'field' => 'attachments',
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
        'category_id' => [
            'field' => 'category_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'manufacturer_id' => [
            'field' => 'manufacturer_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'unit_id' => [
            'field' => 'unit_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'dimensions' => [
            'field' => 'dimensions',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'stock_status_id' => [
            'field' => 'stock_status_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'url' => [
            'field' => 'url',
            'type' => 'varchar(255)',
            'null' => 'NO',
            'key' => 'UNI',
            'default' => null,
            'extra' => ''
        ],
        'product_options' => [
            'field' => 'product_options',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'price' => [
            'field' => 'price',
            'type' => 'decimal(10,2)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'price_old' => [
            'field' => 'price_old',
            'type' => 'decimal(10,2)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'price_on_request' => [
            'field' => 'price_on_request',
            'type' => 'tinyint(1)',
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
            'default' => '',
            'extra' => ''
        ],
        'enable' => [
            'field' => 'enable',
            'type' => 'tinyint(1)',
            'null' => 'YES',
            'key' => '',
            'default' => '1',
            'extra' => ''
        ],
        'products_related' => [
            'field' => 'products_related',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'bath_style_id' => [
            'field' => 'bath_style_id',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = ['article' => '','url' => '']; 

}