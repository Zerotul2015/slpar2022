<?php 

namespace App\Classes\ActiveRecord\Tables;

use App\Classes\ActiveRecord\Main; 

/**
 * @property mixed id regular read/write property
 * @property mixed title_prefix regular read/write property
 * @property mixed titlePrefix regular read/write property
 * @property mixed title_postfix regular read/write property
 * @property mixed titlePostfix regular read/write property
 * @property mixed title_prefix_product regular read/write property
 * @property mixed titlePrefixProduct regular read/write property
 * @property mixed title_postfix_product regular read/write property
 * @property mixed titlePostfixProduct regular read/write property
 * @property mixed maintenance_mode regular read/write property
 * @property mixed maintenanceMode regular read/write property
 * @property mixed image_header regular read/write property
 * @property mixed imageHeader regular read/write property
 * @property mixed image_logo regular read/write property
 * @property mixed imageLogo regular read/write property
 * @property mixed logo_text regular read/write property
 * @property mixed logoText regular read/write property
 * @property mixed template_footer regular read/write property
 * @property mixed templateFooter regular read/write property
 */

class Settings extends Main
{
    static string $tableName = 'settings'; 

    static string $className = '\App\Classes\ActiveRecord\Tables\Settings'; 

    static array $columnName = [
        'id' => [
            'field' => 'id',
            'type' => 'bigint unsigned',
            'null' => 'NO',
            'key' => 'PRI',
            'default' => null,
            'extra' => 'auto_increment'
        ],
        'title_prefix' => [
            'field' => 'title_prefix',
            'type' => 'varchar(128)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'title_postfix' => [
            'field' => 'title_postfix',
            'type' => 'varchar(128)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'title_prefix_product' => [
            'field' => 'title_prefix_product',
            'type' => 'varchar(128)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'title_postfix_product' => [
            'field' => 'title_postfix_product',
            'type' => 'varchar(128)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'maintenance_mode' => [
            'field' => 'maintenance_mode',
            'type' => 'tinyint(1)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'image_header' => [
            'field' => 'image_header',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'image_logo' => [
            'field' => 'image_logo',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'logo_text' => [
            'field' => 'logo_text',
            'type' => 'varchar(255)',
            'null' => 'YES',
            'key' => '',
            'default' => null,
            'extra' => ''
        ],
        'template_footer' => [
            'field' => 'template_footer',
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