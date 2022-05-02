<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed customer_id regular read/write property
 * @property mixed customerId regular read/write property
 * @property mixed customer_company_id regular read/write property
 * @property mixed customerCompanyId regular read/write property
 * @property mixed payment_id regular read/write property
 * @property mixed paymentId regular read/write property
 * @property mixed delivery_id regular read/write property
 * @property mixed deliveryId regular read/write property
 * @property mixed delivery_address regular read/write property
 * @property mixed deliveryAddress regular read/write property
 * @property mixed products regular read/write property
 * @property mixed status_id regular read/write property
 * @property mixed statusId regular read/write property
 * @property mixed price_order regular read/write property
 * @property mixed priceOrder regular read/write property
 * @property mixed price_delivery regular read/write property
 * @property mixed priceDelivery regular read/write property
 * @property mixed promo_code_used regular read/write property
 * @property mixed promoCodeUsed regular read/write property
 * @property mixed discount_used regular read/write property
 * @property mixed discountUsed regular read/write property
 * @property mixed yandex_payment_id regular read/write property
 * @property mixed yandexPaymentId regular read/write property
 * @property mixed token regular read/write property
 * @property mixed last_modified_time regular read/write property
 * @property mixed lastModifiedTime regular read/write property
 * @property mixed comment_hidden regular read/write property
 * @property mixed commentHidden regular read/write property
 * @property mixed date_order regular read/write property
 * @property mixed dateOrder regular read/write property
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
        'customer_id' => [
            'field' => 'customer_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'customer_company_id' => [
            'field' => 'customer_company_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'payment_id' => [
            'field' => 'payment_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'delivery_id' => [
            'field' => 'delivery_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'delivery_address' => [
            'field' => 'delivery_address',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
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
        'status_id' => [
            'field' => 'status_id',
            'type' => 'bigint unsigned',
            'null' => 'YES',
            'key' => 'MUL',
            'default' => null,
            'extra' => ''
        ],
        'price_order' => [
            'field' => 'price_order',
            'type' => 'int',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'price_delivery' => [
            'field' => 'price_delivery',
            'type' => 'int',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'promo_code_used' => [
            'field' => 'promo_code_used',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'discount_used' => [
            'field' => 'discount_used',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'yandex_payment_id' => [
            'field' => 'yandex_payment_id',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => 'UNI',
            'default' => null,
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
        'last_modified_time' => [
            'field' => 'last_modified_time',
            'type' => 'timestamp',
            'null' => 'YES',
            'key' => '',
            'default' => 'CURRENT_TIMESTAMP',
            'extra' => 'DEFAULT_GENERATED on update CURRENT_TIMESTAMP'
        ],
        'comment_hidden' => [
            'field' => 'comment_hidden',
            'type' => 'text',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'date_order' => [
            'field' => 'date_order',
            'type' => 'timestamp',
            'null' => 'YES',
            'key' => '',
            'default' => 'CURRENT_TIMESTAMP',
            'extra' => 'DEFAULT_GENERATED'
        ]
   ]; 

    static string $primaryKey = 'id';
    static array  $uniqueKey = ['yandex_payment_id' => '']; 

}