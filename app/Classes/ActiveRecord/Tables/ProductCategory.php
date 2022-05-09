<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed parent_id regular read/write property
 * @property mixed parentId regular read/write property
 * @property mixed priority regular read/write property
 * @property mixed name regular read/write property
 * @property mixed description regular read/write property
 * @property mixed image regular read/write property
 * @property mixed seo regular read/write property
 * @property mixed folder regular read/write property
 * @property mixed url regular read/write property
 * @property mixed is_custom regular read/write property
 * @property mixed isCustom regular read/write property
 * @property mixed custom_link regular read/write property
 * @property mixed customLink regular read/write property
 * @property mixed binding_style regular read/write property
 * @property mixed bindingStyle regular read/write property
 * @property mixed wholesale_discount_size regular read/write property
 * @property mixed wholesaleDiscountSize regular read/write property
 */

class ProductCategory extends Main
{
    static string $tableName = 'product_category'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\ProductCategory'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'parent_id' => [
            'field' => 'parent_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
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
        'name' => [
            'field' => 'name',
            'type' => 'varchar(128)',
            'null' => 'NO',
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
        'image' => [
            'field' => 'image',
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
        'folder' => [
            'field' => 'folder',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
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
        'is_custom' => [
            'field' => 'is_custom',
            'type' => 'tinyint(1)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'custom_link' => [
            'field' => 'custom_link',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'binding_style' => [
            'field' => 'binding_style',
            'type' => 'varchar(16)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'wholesale_discount_size' => [
            'field' => 'wholesale_discount_size',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = ['url' => '']; 

}