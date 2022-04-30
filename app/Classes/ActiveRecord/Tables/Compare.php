<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed products regular read/write property
 * @property mixed sid regular read/write property
 * @property mixed date_last_edit regular read/write property
 * @property mixed dateLastEdit regular read/write property
 */

class Compare extends Main
{
    static string $tableName = 'compare'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\Compare'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'products' => [
            'field' => 'products',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'sid' => [
            'field' => 'sid',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => 'UNI',
            'default' => null,
            'extra' => ''
        ],
        'date_last_edit' => [
            'field' => 'date_last_edit',
            'type' => 'timestamp',
            'null' => 'YES',
            'key' => '',
            'default' => 'CURRENT_TIMESTAMP',
            'extra' => 'DEFAULT_GENERATED'
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = ['sid' => '']; 

}