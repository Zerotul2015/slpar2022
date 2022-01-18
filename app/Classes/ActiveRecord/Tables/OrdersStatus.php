<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed name regular read/write property
 * @property mixed send_admin regular read/write property
 * @property mixed sendAdmin regular read/write property
 * @property mixed description regular read/write property
 * @property mixed message_mail regular read/write property
 * @property mixed messageMail regular read/write property
 * @property mixed message_mail_admin regular read/write property
 * @property mixed messageMailAdmin regular read/write property
 */

class OrdersStatus extends Main
{
    static string $tableName = 'orders_status'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\OrdersStatus'; 

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
        'send_admin' => [
            'field' => 'send_admin',
            'type' => 'tinyint(1)',
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
        'message_mail' => [
            'field' => 'message_mail',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'message_mail_admin' => [
            'field' => 'message_mail_admin',
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