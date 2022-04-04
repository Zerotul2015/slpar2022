<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed customer regular read/write property
 * @property mixed customer_company regular read/write property
 * @property mixed customerCompany regular read/write property
 * @property mixed payment_method regular read/write property
 * @property mixed paymentMethod regular read/write property
 * @property mixed delivery_method regular read/write property
 * @property mixed deliveryMethod regular read/write property
 * @property mixed products regular read/write property
 * @property mixed status regular read/write property
 * @property mixed priceorder regular read/write property
 * @property mixed pricedelivery regular read/write property
 */

class Orders extends Main
{
    static string $tableName = 'orders'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\Orders'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'customer' => [
            'field' => 'customer',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'customer_company' => [
            'field' => 'customer_company',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'payment_method' => [
            'field' => 'payment_method',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'delivery_method' => [
            'field' => 'delivery_method',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'products' => [
            'field' => 'products',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'status' => [
            'field' => 'status',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'priceOrder' => [
            'field' => 'priceOrder',
            'type' => 'int',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'priceDelivery' => [
            'field' => 'priceDelivery',
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