<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 11.12.17
 * Time: 21:42
 */

namespace App\Classes;


use App\Controllers\Error\Errors;
use App\Controllers\Images\ImageCache;

/**
 * Class UrlManager
 * @package App\Classes
 */
class UrlManager
{
    const CONTROLLER_DEFAULT = 'Visitors';
    const CONTROLLER_ACTION_DEFAULT = 'Index';
    private $urlString = '';
    private $urlArray = '';
    private $controllersAvailable = [];
    private $subControllersAvailable = [];
    private $controllerClassName = '';
    private $controllerAction = '';
    private $ctrlOneAction = [
        '\App\Controllers\Visitors\Pages',
        '\App\Controllers\Visitors\Shop\Categories\Index',
    ];

    public function __construct()
    {
        //Получаем список доступных контроллеров
        $pathControllers = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR;
        $dirList = scandir($pathControllers);
        foreach ($dirList as $dir) {
            if (is_dir($pathControllers . $dir) && $dir != '.' && $dir != '..') {
                $this->controllersAvailable[] = $dir;
                $pathSub = $pathControllers . $dir . DIRECTORY_SEPARATOR;
                $dirSubList = scandir($pathSub);
                // получаем список вложенных контролеров
                foreach ($dirSubList as $dirSub) {
                    if (is_dir($pathSub . $dirSub) && $dirSub != '.' && $dirSub != '..') {
                        $this->subControllersAvailable[] = $dir . '\\' . $dirSub;
                    }
                }
            }
        }
    }


    public
    function getController()
    {
        try {
            $this->urlString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $this->urlArray = explode('/', $this->urlString);

            $ctrlDir = ucwords(strtolower($this->urlArray[1]), '-');
            $ctrlDir = preg_replace('/-/', '', $ctrlDir);
            // Если такой контроллер не существует считаем что это Visitors
            if (!in_array($ctrlDir, $this->controllersAvailable)) {
                $ctrlDir = self::CONTROLLER_DEFAULT;
            }

            switch ($ctrlDir) {
                case self::CONTROLLER_DEFAULT:
                    // проверка на вложенный контроллер
                    $modifierKey = 0;
                    $subCtrlDir = preg_replace('/-/', '', ucwords(strtolower($this->urlArray[1]), '-'));
                    $subCtrlDir = $ctrlDir . '\\' . $subCtrlDir;
                    if (in_array($subCtrlDir, $this->subControllersAvailable)) {
                        $modifierKey = 1;
                        $ctrlDir = $subCtrlDir;
                    }
                    // конец проверки на вложенный контроллер

                    //если это контролер по умолчанию(имя класса контроллера содержится в первом параметре url)
                    if (!isset($this->urlArray[1 + $modifierKey]) || empty($this->urlArray[1 + $modifierKey]) || $this->urlArray[1 + $modifierKey] == '') {
                        $ctrl = self::CONTROLLER_ACTION_DEFAULT;
                    } else {
                        $ctrl = ucwords(strtolower($this->urlArray[1 + $modifierKey]), '-');
                        $ctrl = preg_replace('/-/', '', $ctrl);
                    }
                    $this->controllerClassName = '\App\Controllers\\' . $ctrlDir . '\\' . $ctrl;
                    $argument = '';
                    //если это контроллер с одним дейстивем
                    if (in_array($this->controllerClassName, $this->ctrlOneAction)) {
                        $this->controllerAction = self::CONTROLLER_ACTION_DEFAULT;
                        if (isset($this->urlArray[2 + $modifierKey])) {
                            $argument = $this->urlArray[2 + $modifierKey];
                        }
                    } else {
                        if (!isset($this->urlArray[2 + $modifierKey]) || empty($this->urlArray[2 + $modifierKey]) || $this->urlArray[2 + $modifierKey] == '') {
                            $this->controllerAction = self::CONTROLLER_ACTION_DEFAULT;
                        } else {
                            $this->controllerAction = ucwords(strtolower($this->urlArray[2 + $modifierKey]), '-');
                            $this->controllerAction = preg_replace('/-/', '', $this->controllerAction);
                        }
                        if (isset($this->urlArray[3 + $modifierKey])) {
                            $argument = strtolower($this->urlArray[3 + $modifierKey]);
                        }
                    }
                    if (class_exists($this->controllerClassName)) {
                        $controller = new $this->controllerClassName;
                        if (method_exists($this->controllerClassName, $this->controllerAction)) {
                            $controller->{$this->controllerAction}($argument);
                        } else {
                            throw new MyException('404');
                        }
                    } else {
                        throw new MyException('404');
                    }
                    break;
                case 'Images':
                    $checkUrl = (isset($this->urlArray[2]) && !empty($this->urlArray[2])) && (isset($this->urlArray[3]) && !empty($this->urlArray[3])) && (isset($this->urlArray[4]) && !empty($this->urlArray[4]));
                    if ($checkUrl) {
                        $pathImage = strtolower($this->urlArray[2]);
                        $template = strtolower($this->urlArray[3]);
                        $imageFileName = strtolower($this->urlArray[4]);
                        $imageCacheController = new ImageCache($pathImage, $template, $imageFileName);
                        //проверка на существование такого изображения
                        if ($imageCacheController) {
                            $response = $imageCacheController->response();
                            echo $response;
                        } else {
                            throw new MyException('404');
                        }
                    }
                    break;
                default:
                    // проверка на вложенный контроллер
                    $modifierKey = 0;
                    if (isset($this->urlArray[2])) {
                        $subCtrlDir = preg_replace('/-/', '', ucwords(strtolower($this->urlArray[2]), '-'));
                        $subCtrlDir = $ctrlDir . '\\' . $subCtrlDir;
                        if (in_array($subCtrlDir, $this->subControllersAvailable)) {
                            $modifierKey = 1;
                            $ctrlDir = $subCtrlDir;
                        }
                    }
                    // конец проверки на вложенный контроллер
                    if (!isset($this->urlArray[2 + $modifierKey]) || empty($this->urlArray[2 + $modifierKey]) || $this->urlArray[2 + $modifierKey] == '') {
                        $ctrl = self::CONTROLLER_ACTION_DEFAULT;
                    } else {
                        $ctrl = ucwords(strtolower($this->urlArray[2 + $modifierKey]), '-');
                        $ctrl = preg_replace('/-/', '', $ctrl);
                    }
                    $this->controllerClassName = '\App\Controllers\\' . $ctrlDir . '\\' . $ctrl;
                    if (!isset($this->urlArray[3 + $modifierKey]) || empty($this->urlArray[3 + $modifierKey])) {
                        $this->controllerAction = self::CONTROLLER_ACTION_DEFAULT;
                    } else {
                        $this->controllerAction = ucfirst(strtolower($this->urlArray[3 + $modifierKey]));
                    }
                    $argument = '';
                    if (in_array($this->controllerClassName, $this->ctrlOneAction)) {
                        $this->controllerAction = self::CONTROLLER_ACTION_DEFAULT;
                        if (isset($this->urlArray[3 + $modifierKey])) {
                            $argument = $this->urlArray[3 + $modifierKey];
                        }
                    } else {
                        if (!isset($this->urlArray[3 + $modifierKey]) || empty($this->urlArray[3 + $modifierKey]) || $this->urlArray[3 + $modifierKey] == '') {
                            $this->controllerAction = self::CONTROLLER_ACTION_DEFAULT;
                        } else {
                            $this->controllerAction = ucwords(strtolower($this->urlArray[3 + $modifierKey]), '-');
                            $this->controllerAction = preg_replace('/-/', '', $this->controllerAction);
                        }
                        if (isset($this->urlArray[4 + $modifierKey])) {
                            $argument = strtolower($this->urlArray[4 + $modifierKey]);
                        }
                    }
                    if (class_exists($this->controllerClassName)) {
                        $controller = new $this->controllerClassName;
                        if (method_exists($this->controllerClassName, $this->controllerAction)) {
                            $controller->{$this->controllerAction}($argument);
                        } else {
                            throw new MyException('404');
                        }
                    } else {
                        throw new MyException('404');
                    }
                    break;
            }
        } catch (MyException $e) {
            switch ($e->getMessage()) {
                case '404':
                    $classError = new Errors();
                    $classError->E404();
                    break;
                default:
                    $classError = new Errors();
                    $classError->E404();
                    break;
            }
        }
    }
}
