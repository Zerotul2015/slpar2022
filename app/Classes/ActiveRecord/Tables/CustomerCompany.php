<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed customer_id regular read/write property
 * @property mixed customerId regular read/write property
 * @property mixed inn regular read/write property
 * @property mixed kpp regular read/write property
 * @property mixed name regular read/write property
 * @property mixed note regular read/write property
 * @property mixed note_hidden regular read/write property
 * @property mixed noteHidden regular read/write property
 * @property mixed deleted regular read/write property
 */

class CustomerCompany extends Main
{
    static string $tableName = 'customer_company'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\CustomerCompany'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'customer_id' => [
            'field' => 'customer_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'inn' => [
            'field' => 'inn',
            'type' => 'varchar(12)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'kpp' => [
            'field' => 'kpp',
            'type' => 'varchar(9)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'name' => [
            'field' => 'name',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'note' => [
            'field' => 'note',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'note_hidden' => [
            'field' => 'note_hidden',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'deleted' => [
            'field' => 'deleted',
            'type' => 'tinyint(1)',
            'null' => 'YES',
            'key' => '',
            'default' => '',
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = []; 

}