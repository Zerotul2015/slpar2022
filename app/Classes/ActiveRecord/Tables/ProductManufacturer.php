<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed name regular read/write property
 * @property mixed image regular read/write property
 * @property mixed description regular read/write property
 * @property mixed folder regular read/write property
 * @property mixed url regular read/write property
 */

class ProductManufacturer extends Main
{
    static string $tableName = 'product_manufacturer'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\ProductManufacturer'; 

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
            'type' => 'varchar(128)',
            'null' => 'NO',
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
        'description' => [
            'field' => 'description',
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
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = []; 

}