<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed name regular read/write property
 * @property mixed phone regular read/write property
 * @property mixed mail regular read/write property
 * @property mixed pass regular read/write property
 * @property mixed status regular read/write property
 * @property mixed note_hidden regular read/write property
 * @property mixed noteHidden regular read/write property
 * @property mixed deleted regular read/write property
 * @property mixed token regular read/write property
 * @property mixed token_auth regular read/write property
 * @property mixed tokenAuth regular read/write property
 * @property mixed verify_code regular read/write property
 * @property mixed verifyCode regular read/write property
 * @property mixed is_wholesale regular read/write property
 * @property mixed isWholesale regular read/write property
 */

class Customer extends Main
{
    static string $tableName = 'customer'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\Customer'; 

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
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'phone' => [
            'field' => 'phone',
            'type' => 'varchar(32)',
            'null' => 'YES',
            'key' => 'UNI',
            'default' => null,
            'extra' => ''
        ],
        'mail' => [
            'field' => 'mail',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => 'UNI',
            'default' => null,
            'extra' => ''
        ],
        'pass' => [
            'field' => 'pass',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'status' => [
            'field' => 'status',
            'type' => 'varchar(32)',
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
        ],
        'token' => [
            'field' => 'token',
            'type' => 'varchar(128)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'token_auth' => [
            'field' => 'token_auth',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => 'UNI',
            'default' => null,
            'extra' => ''
        ],
        'verify_code' => [
            'field' => 'verify_code',
            'type' => 'varchar(12)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'is_wholesale' => [
            'field' => 'is_wholesale',
            'type' => 'tinyint(1)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = ['phone' => '','mail' => '','token_auth' => '']; 

}