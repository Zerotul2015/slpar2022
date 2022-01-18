<?php
/**
 * Created by PhpStorm.
 * User: kosty
 * Date: 24.09.2016
 * Time: 15:05
 */

namespace App\Controllers\Admin;


abstract class Main
{
    protected $data = [];
    public $auth = false;
    public $twig = '';

    function __construct()
    {
        if (!$this->auth = Authorization::isAuth()) {
            if ($_SERVER['REQUEST_URI'] != '/admin/') {
                header('location: /admin/');
            }
        }
        $loader = new \Twig\Loader\FilesystemLoader(ROOT_DIRECTORY . '/resources/views/admin');
        $this->twig = new \Twig\Environment($loader, array(
            'cache' => ROOT_DIRECTORY . '/storage/cache/templates',
            'debug' => true,
        ));
    }

    public function __set($k, $v)
    {
        $this->data[$k] = $v;
    }

    public function __get($name)
    {
        return $this->data[$name];
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

}