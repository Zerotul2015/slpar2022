<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed active regular read/write property
 * @property mixed time_start regular read/write property
 * @property mixed timeStart regular read/write property
 * @property mixed time_end regular read/write property
 * @property mixed timeEnd regular read/write property
 * @property mixed value regular read/write property
 * @property mixed unit regular read/write property
 * @property mixed name regular read/write property
 * @property mixed composition regular read/write property
 */

class Discounts extends Main
{
    static string $tableName = 'discounts'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\Discounts'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'active' => [
            'field' => 'active',
            'type' => 'tinyint(1)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'time_start' => [
            'field' => 'time_start',
            'type' => 'int',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'time_end' => [
            'field' => 'time_end',
            'type' => 'int',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'value' => [
            'field' => 'value',
            'type' => 'int',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'unit' => [
            'field' => 'unit',
            'type' => 'varchar(6)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'name' => [
            'field' => 'name',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'composition' => [
            'field' => 'composition',
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