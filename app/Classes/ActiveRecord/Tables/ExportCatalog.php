<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed format_file regular read/write property
 * @property mixed formatFile regular read/write property
 * @property mixed token regular read/write property
 * @property mixed url regular read/write property
 */

class ExportCatalog extends Main
{
    static string $tableName = 'export_catalog'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\ExportCatalog'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'format_file' => [
            'field' => 'format_file',
            'type' => 'varchar(16)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'token' => [
            'field' => 'token',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => 'UNI',
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
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = ['token' => '','url' => '']; 

}