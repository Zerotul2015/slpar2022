<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed smtp regular read/write property
 * @property mixed email_login regular read/write property
 * @property mixed emailLogin regular read/write property
 * @property mixed email_pass regular read/write property
 * @property mixed emailPass regular read/write property
 * @property mixed email_to_send regular read/write property
 * @property mixed emailToSend regular read/write property
 * @property mixed email_to_receive regular read/write property
 * @property mixed emailToReceive regular read/write property
 */

class SettingsNotifications extends Main
{
    static string $tableName = 'settings_notifications'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\SettingsNotifications'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'smtp' => [
            'field' => 'smtp',
            'type' => 'varchar(64)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'email_login' => [
            'field' => 'email_login',
            'type' => 'varchar(64)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'email_pass' => [
            'field' => 'email_pass',
            'type' => 'varchar(64)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'email_to_send' => [
            'field' => 'email_to_send',
            'type' => 'varchar(64)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'email_to_receive' => [
            'field' => 'email_to_receive',
            'type' => 'varchar(64)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = []; 

}