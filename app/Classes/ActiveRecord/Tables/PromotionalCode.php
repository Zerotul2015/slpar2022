<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed date_start regular read/write property
 * @property mixed dateStart regular read/write property
 * @property mixed date_end regular read/write property
 * @property mixed dateEnd regular read/write property
 * @property mixed discount regular read/write property
 * @property mixed promocode regular read/write property
 * @property mixed auto_apply regular read/write property
 * @property mixed autoApply regular read/write property
 * @property mixed conditions regular read/write property
 */

class PromotionalCode extends Main
{
    static string $tableName = 'promotional_code'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\PromotionalCode'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'date_start' => [
            'field' => 'date_start',
            'type' => 'varchar(32)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'date_end' => [
            'field' => 'date_end',
            'type' => 'varchar(32)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'discount' => [
            'field' => 'discount',
            'type' => 'varchar(128)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'promocode' => [
            'field' => 'promocode',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'auto_apply' => [
            'field' => 'auto_apply',
            'type' => 'tinyint(1)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'conditions' => [
            'field' => 'conditions',
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