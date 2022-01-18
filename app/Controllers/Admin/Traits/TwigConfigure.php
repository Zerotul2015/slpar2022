<?php


namespace App\Controllers\Admin\Traits;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

trait TwigConfigure
{
    /**
     * @param null|array|string $pathTemplates //массив или строка c путями к файлам шаблона
     */
    public function twigConfig($pathTemplates = null)
    {
        $loaderPathArray = [
            ROOT_DIRECTORY . '/resources/views/admin/',
            ROOT_DIRECTORY . '/resources/views/admin/layouts',
        ];
        if($pathTemplates){
            if(is_array($pathTemplates)){
                foreach ($pathTemplates as $pathItem){
                    if(!empty($pathItem)) {
                        $loaderPathArray[] = ROOT_DIRECTORY . $pathItem;
                    }
                }
            }elseif(is_string($pathTemplates)){
                $loaderPathArray[] = ROOT_DIRECTORY . $pathTemplates;
            }
        }
        $loader = new FilesystemLoader($loaderPathArray);
        $this->twig = new Environment($loader, array(
            'cache' => ROOT_DIRECTORY . '/storage/cache/templates',
            'debug' => true,
        ));

    }
}