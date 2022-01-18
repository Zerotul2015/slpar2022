<?php


namespace App\Controllers\Admin\Traits;


use App\Controllers\Admin\Authorization;
use Twig\Environment;
use Twig\Lexer;
use Twig\Loader\FilesystemLoader;

trait ConstructVue
{
    public bool $auth = false;
    public Environment $twig;

    public function __construct()
    {
        if (!$this->auth = Authorization::isAuth()) {
            if ($_SERVER['REQUEST_URI'] != '/admin/') {
                header('location: /admin/');
            }
        }

    }
}