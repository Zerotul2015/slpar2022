<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed date_start regular read/write property
 * @property mixed dateStart regular read/write property
 * @property mixed date_end regular read/write property
 * @property mixed dateEnd regular read/write property
 * @property mixed code_text regular read/write property
 * @property mixed codeText regular read/write property
 * @property mixed unit regular read/write property
 * @property mixed amount regular read/write property
 */

class PromoCode extends Main
{
    static string $tableName = 'promo_code'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\PromoCode'; 

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
        'code_text' => [
            'field' => 'code_text',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'unit' => [
            'field' => 'unit',
            'type' => 'varchar(16)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'amount' => [
            'field' => 'amount',
            'type' => 'int',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = []; 

}