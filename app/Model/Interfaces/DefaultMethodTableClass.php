<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 17.08.18
 * Time: 19:38
 */

namespace App\Model\Interfaces;


interface DefaultMethodTableClass
{
    public static function Save($val);
    public static function Delete($id);

}