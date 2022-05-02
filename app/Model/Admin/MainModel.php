<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 23.12.17
 * Time: 16:57
 */

namespace App\Model\Admin;

use App\Classes\ActiveRecord\Main;
use App\Controllers\Images\ImageCache;
use App\Model\FilesystemModel;

class MainModel
{
    public static function translit($string)
    {
        $replace = array(
            "А" => "A",
            "а" => "a",
            "Б" => "B",
            "б" => "b",
            "В" => "V",
            "в" => "v",
            "Г" => "G",
            "г" => "g",
            "Д" => "D",
            "д" => "d",
            "Е" => "E",
            "е" => "e",
            "Ё" => "E",
            "ё" => "e",
            "Ж" => "Zh",
            "ж" => "zh",
            "З" => "Z",
            "з" => "z",
            "И" => "I",
            "и" => "i",
            "Й" => "I",
            "й" => "i",
            "К" => "K",
            "к" => "k",
            "Л" => "L",
            "л" => "l",
            "М" => "M",
            "м" => "m",
            "Н" => "N",
            "н" => "n",
            "О" => "O",
            "о" => "o",
            "П" => "P",
            "п" => "p",
            "Р" => "R",
            "р" => "r",
            "С" => "S",
            "с" => "s",
            "Т" => "T",
            "т" => "t",
            "У" => "U",
            "у" => "u",
            "Ф" => "F",
            "ф" => "f",
            "Х" => "Kh",
            "х" => "kh",
            "Ц" => "C",
            "ц" => "c",
            "Ч" => "Ch",
            "ч" => "ch",
            "Ш" => "Sh",
            "ш" => "sh",
            "Щ" => "Shch",
            "щ" => "shch",
            "Ы" => "Y",
            "ы" => "y",
            "Э" => "E",
            "э" => "e",
            "Ю" => "Iu",
            "ю" => "iu",
            "Я" => "Ia",
            "я" => "ia",
            "ъ" => "",
            "ь" => "",
            " " => "-",
            "'" => "",
            "“" => "",
            "`" => "",
            "\"" => "",
            "{" => "",
            "}" => "",
            "[" => "",
            "]" => "",
            "\\" => "",
            "/" => "",
            "|" => "",
            "№" => "",
            "«" => "",
            "»" => "",
            "," => "_",
            "#" => "",
            "!" => "_",
            "°" => ""
        );
        return iconv("UTF-8", "UTF-8//IGNORE", strtr($string, $replace));
    }

    /**
     * Переводим строку $stringForUrl с кирилицы в url
     * Если передан пустой экземпляр класса, тогда будет произведена проверка униакльность url по свойству url у дргуих объектов
     * этого класса из базы
     * @param string $stringForUrl
     * @param Main|false $emptyObjectForCheck
     * @param int|false $id идентификатор объекта в базе данных
     * @return string||false
     */
    public static function urlGeneration(string $stringForUrl, $emptyObjectForCheck = false, $id = false)
    {
        if (empty($stringForUrl)) {
            return false;
        } else {
            $checkExistUrl = false;
            $translit = static::translitUrl($stringForUrl);
            // если передан объект проверяем что он реализует интерфейс ActiveRecord и тогда проверем url на дублирвание
            if (is_object($emptyObjectForCheck)) {
                if (strripos(get_parent_class($emptyObjectForCheck), 'activerecord')) {
                    $checkExistUrl = $emptyObjectForCheck::find()->where(['url' => $translit])->one();
                }
            }
            if ($checkExistUrl) {
                if (($id && ($checkExistUrl->{$checkExistUrl::$primaryKey} != $id)) || !$id) {
                    $translit = $translit . '-' . uniqid('', true);
                }
            }
            $stringReturn = strtolower($translit);
            if (strlen($stringReturn) > 255) {
                $stringReturn = substr($stringReturn, 255);
            }

            return $stringReturn;
        }
    }

    public static function translitUrl($string)
    {
        $replace = array(
            "А" => "A",
            "а" => "a",
            "Б" => "B",
            "б" => "b",
            "В" => "V",
            "в" => "v",
            "Г" => "G",
            "г" => "g",
            "Д" => "D",
            "д" => "d",
            "Е" => "E",
            "е" => "e",
            "Ё" => "E",
            "ё" => "e",
            "Ж" => "Zh",
            "ж" => "zh",
            "З" => "Z",
            "з" => "z",
            "И" => "I",
            "и" => "i",
            "Й" => "I",
            "й" => "i",
            "К" => "K",
            "к" => "k",
            "Л" => "L",
            "л" => "l",
            "М" => "M",
            "м" => "m",
            "Н" => "N",
            "н" => "n",
            "О" => "O",
            "о" => "o",
            "П" => "P",
            "п" => "p",
            "Р" => "R",
            "р" => "r",
            "С" => "S",
            "с" => "s",
            "Т" => "T",
            "т" => "t",
            "У" => "U",
            "у" => "u",
            "Ф" => "F",
            "ф" => "f",
            "Х" => "Kh",
            "х" => "kh",
            "Ц" => "C",
            "ц" => "c",
            "Ч" => "Ch",
            "ч" => "ch",
            "Ш" => "Sh",
            "ш" => "sh",
            "Щ" => "Shch",
            "щ" => "shch",
            "Ы" => "Y",
            "ы" => "y",
            "Э" => "E",
            "э" => "e",
            "Ю" => "Iu",
            "ю" => "iu",
            "Я" => "Ia",
            "я" => "ia",
            "ъ" => "",
            "Ъ" => "",
            "ь" => "",
            "Ь" => "",
            " " => "-",
            "'" => "",
            "`" => "",
            "“" => "",
            "\"" => "",
            "{" => "",
            "}" => "",
            "[" => "",
            "]" => "",
            "\\" => "",
            "/" => "",
            "|" => "",
            "№" => "",
            "«" => "",
            "." => "",
            "»" => "",
            "," => "_",
            "!" => "_",
            "°" => ""
        );
        $replacedString = strtr($string, $replace);

        $patternPreg = [
            '/-{2}/', // -
            '/_+-}/', // -
            '/-_+}/', // -
            '/_+}/' // _
        ];
        $replacePreg = [
            '-',
            '-',
            '-',
            '_'
        ];
        return preg_replace($patternPreg, $replacePreg, $replacedString);
    }

    /**
     * Склонение существительных после числительных
     * Например для (7, 'день', 'дня', 'дней') вернет дней
     * @param $count
     * @param $form1 string пример день
     * @param $form2 string пример дня
     * @param $form3 string пример дней
     * @return string
     */
    public static function declination($count, string $form1, string $form2, string $form3): string
    {
        $count = abs($count) % 100;
        $lcount = $count % 10;
        if ($count >= 11 && $count <= 19) return ($form3);
        if ($lcount >= 2 && $lcount <= 4) return ($form2);
        if ($lcount == 1) return ($form1);
        return $form3;
    }



    /**
     * Удаляем изображения созданые ImageCache
     * @param $fullPathOriginalImage string абсолютный путь к изображению
     */
    public static function removeImageInCache(string $fullPathOriginalImage)
    {
        $fileSystemModel = new FilesystemModel();
        $directory = ImageCache::$settings['templates'];
        $nameFile = basename($fullPathOriginalImage);
        $onlyMainPath = preg_replace('/' . $nameFile . '/', '', $fullPathOriginalImage);
        foreach ($directory as $dir => $other) {
            $path = $onlyMainPath . $dir . '/' . $nameFile;
            if ($fileSystemModel->exists($path)) {
                $fileSystemModel->remove($path);
            }
        }
    }

    /**
     * Актуальный метод
     * Обработка массива изображений перед записью в базу
     *
     * Принимает массив из имен изображений
     * Если указан $productID проверят изображения на изменение
     * Возвращает array images с именами новых изображений
     * @param array $images
     * @param string|false $imageMain
     * @param array|null|false $oldImages массив из старых изображений
     * @param string $folder имя папки для хранения изображений без слеша в начале и конце пути. Пример: 'product/celiva'
     * @return array ['images', 'image_main]
     */
    public static function prepareImagesBeforeSave(array $images, $imageMain = false, $oldImages = [], string $folder = 'other'): array
    {
        //собираем все изображение товара из базы в один массив
        $imagesBase = [];
        if ($oldImages) {
            if (empty($oldImages['images']) == false) {
                foreach ($oldImages['images'] as $ImageBaseTemp) {
                    $imagesBase[$ImageBaseTemp] = $ImageBaseTemp;
                }
            }
        }

        $fileSystemModel = new FilesystemModel();
        if (empty($images)) {
            $imageMain = false;
        } else {
            $indexImageExist = false;
            foreach ($images as $key => $imageItem) {
                if ($imageMain) {
                    $isIndex = $imageItem === $imageMain;
                    $indexImageExist = $indexImageExist ? $indexImageExist : $isIndex;
                } else {
                    $isIndex = true;
                    $indexImageExist = true;
                }

                $isTempUploader = preg_match('/\/upload\/temp/', $imageItem);
                if (!$isTempUploader) {
                    $imageItem = basename(FilesystemModel::getAbsolutePath($imageItem));
                }
                //изображение использовалось в этом товаре
                if (isset($imagesBase[$imageItem])) {
                    //убираем его из массива, чтобы не удалить
                    unset($imagesBase[$imageItem]);
                    $nameFile = $imageItem;
                    $images[$key] = $nameFile;
                } //изображение новое и находится в каталоге /upload/temp
                elseif ($isTempUploader) {
                    $oldPath = FilesystemModel::getAbsolutePath($imageItem);
                    $nameFile = basename($oldPath);
                    $newPath = FilesystemModel::getAbsolutePath('/images/' . $folder . '/' . $nameFile);
                    $pathToFolder = FilesystemModel::getAbsolutePath('/images/' . $folder);
                    if (!$fileSystemModel->exists($pathToFolder)) {
                        $fileSystemModel->mkdir($pathToFolder, 0755);
                    }
                    $fileSystemModel->rename($oldPath, $newPath);
                    $images[$key] = $nameFile;
                }
                if ($isIndex) {
                    $imageMain = $images[$key];
                }
            }
            if (!$indexImageExist) {
                $imageMain = $images[0];
            }
        }

        // удаляем не нужные изображения
        if (empty($imagesBase) === false) {
            foreach ($imagesBase as $imageDel) {
                if ($imageDel) {
                    $path = FilesystemModel::getAbsolutePath('/images/' . $folder . '/' . $imageDel);
                    if ($fileSystemModel->exists($path)) {
                        $fileSystemModel->remove($path);
                        MainModel::removeImageInCache($path);
                    }
                }
            }
        }
        return ['images' => $images, 'image_main' => $imageMain];
    }

    /**
     * Актуальный метод
     * Проверяет изображение на изменение
     *
     * Если оно было изменено удаляет строе вместе с кешем, а новое переносит в указаную папку
     * @param string $image // новое изображение
     * @param string|false|null $imageOld // старое изображение
     * @param string $folder имя папки для хранения изображений без слеша в начале и конце пути. Пример: 'product/celiva'
     * @return string
     */
    public static function prepareImageBeforeSave(string $image, $imageOld = '', string $folder = 'other'): string
    {
        $imageChange = true;
        $fileSystemModel = new FilesystemModel();
        $imageReturn = '';
        if (!empty($image)) {
            $pathAbsoluteImages = FilesystemModel::getAbsolutePath($image); // полный путь до изображения
            $isTempUploader = preg_match('/\/upload\/temp/', $image);
            if ($isTempUploader) {//изображение новое т.к. находится в каталоге /upload/temp
                $nameFile = basename($pathAbsoluteImages);
                $newPath = FilesystemModel::getAbsolutePath('/images/' . $folder . '/' . $nameFile);
                $pathToFolder = FilesystemModel::getAbsolutePath('/images/' . $folder);
                if (!$fileSystemModel->exists($pathToFolder)) {
                    $fileSystemModel->mkdir($pathToFolder, 0755);
                }
                $fileSystemModel->rename($pathAbsoluteImages, $newPath);
                $imageReturn = $nameFile;
            } else {//если оно не в папке для временых файлов, значит оно уже используется
                $imageReturn = basename($pathAbsoluteImages);
                $imageChange = false;
            }
        }
        // если изображение изменилось, удаляем старое и очищаем кэш
        if ($imageChange && $imageOld) {
            $path = FilesystemModel::getAbsolutePath('/images/' . $folder . '/' . $imageOld);
            if ($fileSystemModel->exists($path) && is_file($path)) {
                $fileSystemModel->remove($path);
                MainModel::removeImageInCache($path);
            }
        }
        return $imageReturn;
    }

    /**
     * Формирует объект для ответа на запрос изменений данных в базе а также выводит его.
     * @param $result
     */
    public static function printResultChangeForAjax($result)
    {
        if (is_numeric($result)) {
            $returnResult = ['result' => $result, 'id' => $result];
        } elseif (is_array($result)) {
            $returnResult = $result;
        } else {
            $returnResult = ['result' => $result];
        }
        header('Content-Type: application/json');
        echo json_encode($returnResult);
    }

    /**
     * Возвращает id переданного массива или объекта
     * Если id не найден возвращает null
     * @param $val
     * @return int|null
     */
    public static function returnID($val): ?int
    {
        $returnID = null;
        if (is_object($val)) {
            $returnID = $val->id ?? null;
        } else {
            $returnID = $val['id'] ?? null;
        }
        return $returnID;
    }


    /**
     * @param $val array
     * @param $className string|Main
     * @return mixed
     */
    public static function save(array $val, string|Main $className): mixed
    {
        if (isset($val['id']) && $val['id'] && $newObject = $className::findOne($val['id'])) {
            $newObject->set($val);
        } else {
            $newObject = $className::create()->set($val);
        }
        return $newObject->save();
    }

    /**
     * @param $id
     * @param $className string|Main
     * @return bool
     */
    public static function delete($id, $className): bool
    {
        $result = false;
        if ($objectInBase = $className::findOne($id)) {
            $result = $objectInBase->del();
        }
        return $result;
    }
}
