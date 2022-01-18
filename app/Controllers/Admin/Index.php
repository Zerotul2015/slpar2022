<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 11.12.17
 * Time: 23:05
 */

namespace App\Controllers\Admin;


use App\Controllers\Admin\Traits\TwigConfigure;
use Twig\Environment;

class Index
{
    use  TwigConfigure;

    public bool $auth = false;
    public Environment $twig;

    public function Main()
    {
        $this->twigConfig();
        $template = $this->twig->load('index.twig');
        echo $template->render();
    }

}