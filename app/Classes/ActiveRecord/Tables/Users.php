<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed login regular read/write property
 * @property mixed pass regular read/write property
 * @property mixed token regular read/write property
 * @property mixed access_level regular read/write property
 * @property mixed accessLevel regular read/write property
 */

class Users extends Main
{
    static string $tableName = 'users'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\Users'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'login' => [
            'field' => 'login',
            'type' => 'varchar(64)',
            'null' => 'NO',
            'key' => 'UNI',
            'default' => null,
            'extra' => ''
        ],
        'pass' => [
            'field' => 'pass',
            'type' => 'varchar(255)',
            'null' => 'NO',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'token' => [
            'field' => 'token',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'access_level' => [
            'field' => 'access_level',
            'type' => 'varchar(32)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = ['login' => '']; 

}