<?php


namespace App\Classes\Factory;


use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Lexer;
use Twig\Loader\FilesystemLoader;

/**
 * Class TwigFactory
 * @package App\Classes
 */
class TemplateFactory
{

    /**
     * * TODO Когда будет время перевести десктоп версию на renderAdaptive
     * @param $loaderPathTemplate array|string
     * @param $templateFile string
     * @param $varRender
     * @param $withVue bool
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public static function render($loaderPathTemplate, string $templateFile, $varRender, $withVue = true): string
    {
        $loader = new FilesystemLoader($loaderPathTemplate);
        $twig = new Environment($loader, array(
            'cache' => ROOT_DIRECTORY . '/storage/cache/templates',
            'debug' => true,
        ));
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        // для устрания конфилта с vue.js
        if ($withVue) {
            $lexer = new Lexer($twig, array(
                'tag_comment' => array('{#', '#}'),
                'tag_block' => array('{%', '%}'),
                'tag_variable' => array('[[', ']]'), // was array('{{', '}}')
                'interpolation' => array('#{', '}'),
            ));
            $twig->setLexer($lexer);
        }

        //$twig->load($templateFile);
        $template = $twig->load($templateFile);
        return $template->render($varRender);
    }


    /**
     * Новый метод добалви когда начал писать моблиьную версию.
     * TODO Когда будет время перевести десктоп версию на этот метод
     * @param $loaderPathTemplate array|string
     * @param $templateFile string
     * @param $varRender
     * @param $withVue bool
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public static function renderAdaptive($loaderPathTemplate, string $templateFile, $varRender, $withVue = true): string
    {
        if (IS_MOBILE) {
           // $pathForTemplate = ROOT_DIRECTORY . '/resources/views/public-mobile/';
            $pathForTemplate = ROOT_DIRECTORY . '/resources/views/public/';
        } else {
            $pathForTemplate = ROOT_DIRECTORY . '/resources/views/public/';
        }

        if (is_array($loaderPathTemplate)) {
            foreach ($loaderPathTemplate as $k => $pathEnd) {
                $loaderPathTemplate[$k] = $pathForTemplate . $pathEnd;
            }
        }else{
            $loaderPathTemplate = $pathForTemplate . $loaderPathTemplate;
        }
        $loader = new FilesystemLoader($loaderPathTemplate);
        $twig = new Environment($loader, array(
            'cache' => ROOT_DIRECTORY . '/storage/cache/templates',
            'debug' => true,
        ));
        // для устрания конфилта с vue.js
        if ($withVue) {
            $lexer = new Lexer($twig, array(
                'tag_comment' => array('{#', '#}'),
                'tag_block' => array('{%', '%}'),
                'tag_variable' => array('[[', ']]'), // was array('{{', '}}')
                'interpolation' => array('#{', '}'),
            ));
            $twig->setLexer($lexer);
        }

        //$twig->load($templateFile);
        $template = $twig->load($templateFile);
        return $template->render($varRender);
    }


}