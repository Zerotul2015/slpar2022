<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 07.08.18
 * Time: 21:58
 */

namespace App\Classes;


use App\Controllers\Images\ImageCache;


class Routing
{
    public $urlArray = '';
    public $arguments = '';
    private string $className = '';
    private $methodName = '';

    // Default ['images' => 'ImageCache']; Формат [stringKey => nameMethod],
    // Сначало првоеряется наличие локального метода с таким названием,
    // если нет такого, то будет вызван как создание класса с таким имененм
    private array $rootClassCustom; //
    protected bool $CustomController = false;

    /**
     * Routing constructor.
     * @param string $classNameDefault
     * @param string $methodNameDefault
     * @param array $rootClassAvailable
     * @param string $rootClassDefault
     * @param array $rootClassCustom
     * @throws MyException
     */
    public function __construct(
        string $classNameDefault = 'Index',
        string $methodNameDefault = 'Main',
        array $rootClassAvailable = [
            'Admin',
            'Amqp',
            'Api',
            'Cron',
            'Parser',
            'Services',
            'Visitors',
        ],
        string $rootClassDefault = '',
        array $rootClassCustom = ['images' => 'ImageCache']
    )
    {
        $this->classNameDefault = $classNameDefault;
        $this->methodNameDefault = $methodNameDefault;
        $this->rootClassAvailable = $rootClassAvailable;
        $this->rootClassDefault = $rootClassDefault;
        $this->rootClassCustom = $rootClassCustom;


        $urlString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $urlArrayTemp = explode('/', $urlString);

        $urlArrayUcFirst = []; // Массив знчений для полного имени класса с заглавных букв
        $this->urlArray = [];// Массив знчений для полного имени класса все символы строчные
        foreach ($urlArrayTemp as $k => $v) {
            if ($v) {
                $vNew = mb_convert_case($v, MB_CASE_TITLE);
                // приводим к верблюжью написанию
                $vForClassName = preg_replace_callback('/([^-]*)-([^-]+)/', static function ($matches) {
                    return ucfirst($matches[1]) . ucfirst($matches[2]);
                }, $vNew);
                $this->urlArray[] = $v;
                $urlArrayUcFirst[] = $vForClassName;
            }
        }

        //Проверка на кастомный контроллеров
        if (isset($urlArrayUcFirst[0], $this->rootClassCustom[strtolower($this->urlArray[0])])) {
            $this->CustomController = true;
        } else {
            $urlArrayUcFirstOriginal = $urlArrayUcFirst;// нужно для проверки существаования метода
            $prefixClassName = '\App\Controllers\\';
            $nameClassForCheck = '\App\Controllers\\' . $this->classNameDefault;

            $countParameters = count($urlArrayUcFirst);
            if ($countParameters !== 0) {
                do {
                    $nameClassForCheck = $prefixClassName . implode('\\', $urlArrayUcFirst);
                    $keyForMethod = $countParameters;
                    $keyForArgument = $countParameters + 1;
                    if (isset($urlArrayUcFirstOriginal[$keyForMethod])) {
                        $methodNameForCheck = $urlArrayUcFirstOriginal[$keyForMethod];
                    }

                    //если класс существует
                    if (class_exists($nameClassForCheck)) {
                        $this->className = $nameClassForCheck;
                        //проверяем на наличие метода
                        if (isset($methodNameForCheck)) {
                            if (method_exists($nameClassForCheck, $methodNameForCheck)) {
                                $this->methodName = $methodNameForCheck;
                                //если есть аргумент в uri
                                if (isset($this->urlArray[$keyForArgument])) {
                                    $this->arguments = $this->urlArray[$keyForArgument];
                                }
                            } // проверка на наличие метода по умолчанию
                            elseif (method_exists($nameClassForCheck, $this->methodNameDefault)) {
                                $this->methodName = $this->methodNameDefault;
                                $this->arguments = $this->urlArray[$keyForArgument - 1];
                            } else {
                                var_dump($this);
                                throw new MyException('404');
                            }
                        } else {
                            $this->methodName = $this->methodNameDefault;
                        }
                        break;
                    } elseif (class_exists($nameClassForCheck . '\\' . $this->classNameDefault)) {
                        //если контролер по умолчанию
                        $this->className = $nameClassForCheck . '\\' . $this->classNameDefault;
                        if (isset($methodNameForCheck) && method_exists($nameClassForCheck, $methodNameForCheck)) {
                            //если есть аргумент в uri
                            if (isset($this->urlArray[$keyForArgument])) {
                                $this->arguments = $this->urlArray[$keyForArgument];
                            }
                            $this->methodName = $methodNameForCheck;
                        } // проверка на наличие метода по умолчанию
                        elseif (method_exists($this->className, $this->methodNameDefault)) {
                            $this->methodName = $this->methodNameDefault;
                            if (isset($this->urlArray[$keyForArgument - 1])) {
                                $this->arguments = $this->urlArray[$keyForArgument - 1];
                            }
                        } else {
                            throw new MyException('404');
                        }

                        break;
                    } else {
                        array_pop($urlArrayUcFirst);
                    }
                    $countParameters--;
                } while ($countParameters >= 0);
            } else {
                $this->className = $nameClassForCheck;
                $this->methodName = $this->methodNameDefault;
            }
        }
    }

    /**
     * @throws MyException
     */
    public function execute(): void
    {
        if ($this->CustomController) {
            if (method_exists($this, $nameLocalMethod = $this->rootClassCustom[strtolower($this->urlArray[0])])) {
                $this->{$nameLocalMethod}();
            } else {
                throw new MyException('404');
            }
        } elseif (class_exists($this->className)) {


            $createClass = new $this->className();
            if ($this->arguments) {
                $createClass->{$this->methodName}($this->arguments);
            } else {
                $createClass->{$this->methodName}();
            }
        } else {
            throw new MyException('404');
        }
    }


    /**
     *  Метод для вызова контролера ImageCache
     * @throws MyException
     */
    protected function ImageCache(): void
    {
        $this->urlArray;
        $checkUrl4 = isset($this->urlArray[4]) && !empty($this->urlArray[4]);
        $checkUrl3 = isset($this->urlArray[3]) && !empty($this->urlArray[3]);
        if ($checkUrl4) {
            $pathImage = '/' . $this->urlArray[0] . '/' . $this->urlArray[1] . '/' . $this->urlArray[2] . '/';
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
        } elseif ($checkUrl3) {
            $pathImage = strtolower($this->urlArray[1]);
            $template = strtolower($this->urlArray[2]);
            $imageFileName = strtolower($this->urlArray[3]);
            $imageCacheController = new ImageCache($pathImage, $template, $imageFileName);
            //проверка на существование такого изображения
            if ($imageCacheController) {
                $response = $imageCacheController->response();
                echo $response;
            } else {
                throw new MyException('404');
            }
        }

    }
}