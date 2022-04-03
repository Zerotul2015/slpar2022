<?php

namespace App\Controllers\Shop;

use App\Controllers\Shop\Traits\TwigConfigure;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Index
{
    use  TwigConfigure;
    public Environment $twig;

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function Main()
    {
        $this->twigConfig();
        $template = $this->twig->load('index.twig');

        echo $template->render();
    }
}