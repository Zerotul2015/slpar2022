<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 13.03.18
 * Time: 22:19
 */

namespace App\Controllers\Images;

use App\Classes\MyException;
use App\Model\FilesystemModel;
use Intervention\Image\ImageManagerStatic as Image;

/**
 * Пример адреса для imagecache site.ru/images/products/small/simple.jpg
 * Где products это static::$settings['path]['products'], a small это this->settings['templates]['small']
 * Class ImageCache
 * @package App\Controllers\Images
 */
class ImageCache
{

    /**
     * Элемент template отвечает за шаблоны размеров изображений
     *     watermark boolean отвечает за наложение водяного знака, если не указан считаем что false
     *     Его сруктура: имя_шаблона => ширина или имя_шаблона => [ширина, высота, canvas, ]
     *     Примеры small=>120 или small=>['width' => 120, 'height' => 150, fit=> true]
     *     Допустимые значения для width, height Положительные числа или '' или null
     *     fit boolean Комбинируйте обрезку и изменение размера, чтобы форматировать изображение в интеллектуальном режиме. Метод найдет оптимальное соотношение сторон вашей ширины и высоты на текущем изображении автоматически, вырезает его и изменяет его размер до заданного размера
     *     При fit true обязателбно указывать следующие элементы: width, position
     *          position string Влияет нс какой точки изображения изменения размера будет происходить, допустимые значения top-left, top, top-right, left, center (по умолчанию), right, bottom-left, bottom, bottom-right
     * Элемент path отвечает за путь по которму будут храниться изображения
     * @var array
     */
    public static
        $settings = [
        'templates' => [
            'small' => [
                'width' => 120,
                'height' => 120,
                'fit' => true
            ],
            'medium' => 240,
            'large' => 480,
            'logo_default' => 200,
            'thumb' => [
                'width' => 250,
                'height' => 250,
                'fit' => true
            ],
            'thumb_medium' => [
                'width' => 350,
                'height' => 350,
                'fit' => true
            ],
            'thumb_big' => [
                'width' => 500,
                'height' => 500,
                'fit' => true
            ],
            'thumb_manufacturer' => [
                'height' => 125,
            ],
            'header_background' => [
                'height' => 600,
                'fit' => false
            ],
            'header_background_mobile' => [
                'height' => 400,
                'fit' => false
            ],
            'header_background_tablet' => [
                'height' => 550,
                'fit' => false
            ],
            '50'=>[
                'width'=>50,
                'fit' => false
            ],
            '100'=>[
                'width'=>100,
                'fit' => false
            ],
            '250'=>[
                'width'=>250,
                'fit' => false
            ],
            '500'=>[
                'width'=>500,
                'fit' => false
            ],
            '800'=>[
                'width'=>800,
                'fit' => false
            ],
            '1280'=>[
                'width'=>1280,
                'fit' => false
            ],
            '1920'=>[
                'width'=>1920,
                'fit' => false
            ],

        ],
        'path' => [
            //'manufacturers' => '/images/manufacturers/',
        ]
    ];
    public $request = [
        'path' => '',
        'templates' => '',
        'originalImage' => '',
        'requestImage' => '',
        'requestImageExist' => '',
        'watermark' => '/images/watermark5.png'
    ];

    public function __construct($path, $template, $imageName)

    {
        $returnVal = false;
        $fileSystem = new FilesystemModel();

        //получаем полнуый путь до watermark
        $watermark = FilesystemModel::getAbsolutePath($this->request['watermark']);
        if (!$fileSystem->exists($watermark)) {
            $watermark = false;
        }
        //конец получения полного путь до watermark

        if (isset(static::$settings['path'][$path]) && isset(static::$settings['templates'][$template])) {
            $pathOriginalImage = FilesystemModel::getAbsolutePath(static::$settings['path'][$path] . $imageName);
            $fileOriginalExist = $fileSystem->exists($pathOriginalImage);
            $isFile = is_file($pathOriginalImage);
            if ($fileOriginalExist && $isFile) {
                if ($template == 'original') {
                    $pathRequestImage = $pathOriginalImage;
                    $requestImageExist = true;
                } else {
                    if (!$fileSystem->exists(FilesystemModel::getAbsolutePath(static::$settings['path'][$path] . $template))) {
                        $fileSystem->mkdir(FilesystemModel::getAbsolutePath(static::$settings['path'][$path] . $template), 0755);
                    }
                    $pathRequestImage = FilesystemModel::getAbsolutePath(static::$settings['path'][$path] . $template . '/' . $imageName);
                    $requestImageExist = $fileSystem->exists($pathRequestImage);
                }
                $this->request = [
                    'path' => $path,
                    'templates' => $template,
                    'originalImage' => $pathOriginalImage,
                    'requestImage' => $pathRequestImage,
                    'requestImageExist' => $requestImageExist
                ];
            } else {
                $returnVal = false;
            }
        } else {
            //если это 4 уровневое url например для изображений товара
            $pathOriginalImage = FilesystemModel::getAbsolutePath($path . $imageName);
            $fileOriginalExist = $fileSystem->exists($pathOriginalImage);
            $isFile = is_file($pathOriginalImage);
            if ($fileOriginalExist && $isFile) {
                if ($template == 'original') {
                    $pathRequestImage = $pathOriginalImage;
                    $requestImageExist = true;
                } else {
                    if (!$fileSystem->exists(FilesystemModel::getAbsolutePath($path . $template))) {
                        $fileSystem->mkdir(FilesystemModel::getAbsolutePath($path . $template), 0755);
                    }
                    $pathRequestImage = FilesystemModel::getAbsolutePath($path . $template . '/' . $imageName);
                    $requestImageExist = $fileSystem->exists($pathRequestImage);
                }
                $this->request = [
                    'path' => $path,
                    'templates' => $template,
                    'originalImage' => $pathOriginalImage,
                    'requestImage' => $pathRequestImage,
                    'requestImageExist' => $requestImageExist
                ];
            } else {
                $returnVal = false;
            }
        }
        $this->request['watermark'] = $watermark;
        return $returnVal;
    }


    /**
     * @return string
     * @throws MyException
     */
    public function response()
    {
        Image::configure(array('driver' => 'imagick'));
        if ($this->request['originalImage']) {
            $originalImage = Image::make($this->request['originalImage']);
            //если нет изображения нужного фомата создаем его
            if (!$this->request['requestImageExist']) {
                if (is_array(static::$settings['templates'][$this->request['templates']])) {
                    //если указано автоматическое интелектуальное изменение размера
                    if (isset(static::$settings['templates'][$this->request['templates']]['fit']) && static::$settings['templates'][$this->request['templates']]['fit']) {
                        $width = static::$settings['templates'][$this->request['templates']]['width'];
                        //если указана высота и она больше 0
                        if (isset(static::$settings['templates'][$this->request['templates']]['height']) && static::$settings['templates'][$this->request['templates']]['height']) {
                            $height = static::$settings['templates'][$this->request['templates']]['height'];
                            $originalImage->fit($width, $height, function ($constraint) {
                                $constraint->upsize();
                            });
                        } //если указана только ширина то будет создано квадратное изображение
                        else {
                            $originalImage->fit($width);
                        }
                    } //если не указано интелектуальное изменение размера
                    else {
                        //определяем какие размеры указаны
                        if (isset(static::$settings['templates'][$this->request['templates']]['width']) && static::$settings['templates'][$this->request['templates']]['width']) {
                            $width = static::$settings['templates'][$this->request['templates']]['width'];
                        } else {
                            $width = null;
                        }
                        if (isset(static::$settings['templates'][$this->request['templates']]['height']) && static::$settings['templates'][$this->request['templates']]['height']) {
                            $height = static::$settings['templates'][$this->request['templates']]['height'];
                        } else {
                            $height = null;
                        }
                        // если указаны ширина и высота то меняем изображение до фиксированого размера
                        if ($width && $height) {
                            $originalImage->resize($width, $height);
                        } //иначе с сохранение соотношения сторон
                        else {
                            $originalImage->resize($width, $height, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        }
                    }
                } elseif (static::$settings['templates'][$this->request['templates']]) {
                    $width = static::$settings['templates'][$this->request['templates']];
                    $originalImage->widen($width, function ($constraint) {
                        $constraint->upsize();
                    });
                }


                if (isset(static::$settings['templates'][$this->request['templates']]['watermark'])) {
                    $watermarkNeed = static::$settings['templates'][$this->request['templates']]['watermark'];
                    if ($watermarkNeed) {
                        //узнаем размер изображения
                        $widthWatermark = $originalImage->width() / 3;
                        $imageWatermark = Image::make($this->request['watermark']);
                        $imageWatermark->resize($widthWatermark, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });


                        // в нижнем правом углу
                        //$originalImage->insert($imageWatermark, 'bottom-right', 10, 10);

                        // заливаем все изображение
                        $originalImage->fill($imageWatermark);

                        //заливаем все изображение
                        //$originalImage->fill($imageWatermark);
                    }
                }

                $originalImage->save($this->request['requestImage']);
            }
            //header('Content-Type: ' . $originalImage->mime());
            echo $originalImage->response();
        }else{
            throw new MyException('404');
        }

    }
}