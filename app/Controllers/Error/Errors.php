<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 02.01.2017
 * Time: 14:53
 */

namespace App\Controllers\Error;


use App\Classes\Factory\TemplateFactory;
use App\Classes\MyException;
use App\Model\PublicMainModel;
use App\Model\PublicTemplateModel;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Errors
{
    private array $loaderPathTemplate = [
        ROOT_DIRECTORY . '/resources/views/public/layouts',
        ROOT_DIRECTORY . '/resources/views/public/',
    ];

    /**
     * @throws MyException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function E404()
    {
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        $varRender = [
            'template' => PublicTemplateModel::prepareTemplateValue('404'),
            'current_url' => 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]
        ];
        $templateFile = '404.twig';
        echo TemplateFactory::render($this->loaderPathTemplate, $templateFile, $varRender, 0);
    }

    /**
     * @throws MyException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function E403()
    {
        header('HTTP/1.1 403 Forbidden');
        header("Status: 403 Forbidden");
        $varRender = [
            'template' => PublicTemplateModel::prepareTemplateValue('403'),
            'current_url' => 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]
        ];
        $templateFile = '403.twig';
        echo TemplateFactory::render($this->loaderPathTemplate, $templateFile, $varRender, 0);
    }
}