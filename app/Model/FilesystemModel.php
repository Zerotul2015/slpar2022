<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 09.03.18
 * Time: 23:09
 */

namespace App\Model;

use App\Model\Admin\Images\ImagesModel;
use App\Model\Admin\MainModel;
use Symfony\Component\Filesystem\Filesystem;

class FilesystemModel extends Filesystem
{
    const ROOT_SITE = ROOT_DIRECTORY_PUBLIC;
    const DIR_UPLOAD = ROOT_DIRECTORY_PUBLIC . DIRECTORY_SEPARATOR . 'upload';
    const DIR_TEMP = self::DIR_UPLOAD . DIRECTORY_SEPARATOR . 'temp';
    const DIR_DOCUMENTS = 'documents';
    const DIR_OTHER = 'other';
    const DIR_IMAGES = 'images';

    // разрешеные типы
    private $allowedType = [
        'image' => ['image/gif', 'image/png', 'image/jpeg', 'image/pjpeg', 'image/svg+xml', 'image/bmp', 'image/webp'],
        'video' => ['video/mpeg', 'video/mp4', 'ideo/3gpp2', 'video/3gpp', 'video/x-flv', 'video/x-ms-wmv', 'video/webm', 'video/quicktime', 'video/ogg'],
        'archive' => ['application/zip', 'application/x-rar-compressed', 'application/gzip', 'application/x-tar', 'application/x-7z-compressed'],
        'document' => ['application/pdf', 'video/mp4', 'text/plain', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'application/vnd.oasis.opendocument.graphics', 'application/vnd.oasis.opendocument.presentation', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.oasis.opendocument.text']
    ];

    public function __construct()
    {

    }

    /**
     * Проверяем файл на соответствие разрешенным типам. Возвращает тип файла или false если заперщенный тип файла
     * @param $fileWithFullPath
     * @return false|string
     */
    public function CheckTypeFile($fileWithFullPath)
    {
        $mimeType = mime_content_type($fileWithFullPath);
        foreach ($this->allowedType as $fileType => $typeMime) {
            foreach ($typeMime as $valueMime) {
                if ($mimeType == $valueMime) {
                    return $fileType;
                }
            }
        }
        return false;
    }

    /**
     * На входе имя файла, а на выходе его расширение
     * @param string $filename
     * @return string
     */
    public function GetExtension(string $filename)
    {
        $array = explode(".", $filename);
        return end($array);
    }

    public function generateNewName($nameFile): string
    {
        return md5(microtime() . rand(0, 9999)) . '.' . $this->GetExtension($nameFile);
    }


    /**
     * Загружает файлв из указанной ссылки в DIR_TEMP
     * @param $linkFile
     * @return string возвращает путь относительно корня сайта
     */
    public function downloadToTemp($linkFile): ?string
    {
        $returnRelativePath = null;
        $newName = $this->generateNewName($linkFile);
        $imageUpload = file_get_contents($linkFile);
        $result = file_put_contents( self::DIR_TEMP . DIRECTORY_SEPARATOR . $newName, $imageUpload);
        if ($result){
            $returnRelativePath = '/' . $this->makePathRelative(self::DIR_TEMP, self::ROOT_SITE) . $newName;
            //если это изображение оптимизруем его(lossless)
            if ($this->CheckTypeFile(self::DIR_TEMP . DIRECTORY_SEPARATOR . $newName) === 'image') {
                ImagesModel::optimizeImage(self::DIR_TEMP . DIRECTORY_SEPARATOR . $newName);
            }
        }

        return $returnRelativePath;
    }

    /**
     * Загружаем файл в папку во временую папку temp и возвращаем его url относительно корня сайта
     * @param array $uploadFile
     * @return string
     */
    public function UploaderTemp(array $uploadFile): ?string
    {
        $return = null;
        if ($uploadFile['error'] === 0) {
            if ($typeFile = $this->CheckTypeFile($uploadFile["tmp_name"])) {
                $newName = $this->generateNewName($uploadFile["name"]);
                $extensionFile = substr(strrchr($newName, '.'), 1);
                $extensionFileLower = strtolower($extensionFile);
                $newName = preg_replace('/' . $extensionFile . '$/', $extensionFileLower, $newName);
                move_uploaded_file($uploadFile["tmp_name"], self::DIR_TEMP . DIRECTORY_SEPARATOR . $newName);
                //если это изображение оптимизруем его(lossless)
                if ($this->CheckTypeFile(self::DIR_TEMP . DIRECTORY_SEPARATOR . $newName)) {
                    ImagesModel::optimizeImage(self::DIR_TEMP . DIRECTORY_SEPARATOR . $newName);
                }
                $return = '/' . $this->makePathRelative(self::DIR_TEMP, self::ROOT_SITE) . $newName;
            }
        }
        return $return;
    }

    /**
     * Загружаем файлы во временую папку temp и возвращаем массив их url относительно корня сайта
     * @param array $uploadFile (пример $_FILES['image'])
     * @return array URL
     */
    public function UploaderAllTemp(array $uploadFile): array
    {

        $file_array = array();
        $file_count = count($uploadFile['name']);
        $file_keys = array_keys($uploadFile);

        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_array[$i][$key] = $uploadFile[$key][$i];
            }
        }
        $returnUrlArray = [];
        foreach ($file_array as $fileItem) {
            if ($fileItem['error'] === 0) {
                if ($typeFile = $this->CheckTypeFile($fileItem["tmp_name"])) {
                    $newName = $this->generateNewName($fileItem["name"]);
                    $extensionFile = substr(strrchr($newName, '.'), 1);
                    $extensionFileLower = strtolower($extensionFile);
                    $newName = preg_replace('/' . $extensionFile . '$/', $extensionFileLower, $newName);
                    move_uploaded_file($fileItem["tmp_name"], self::DIR_TEMP . DIRECTORY_SEPARATOR . $newName);
                    $returnUrlArray[] = '/' . $this->makePathRelative(self::DIR_TEMP, self::ROOT_SITE) . $newName;
                }
            }
        }
        return $returnUrlArray;
    }

    /**
     * Загружаем файлы в указаную папку возвращаем массив их url относительно корня сайта
     * @param array $uploadFile (пример $_FILES['image'])
     * @param string $path (например dealers) путь относительно папки upload. Начальный и конечный слешы не нужны
     * @return string URL относительно корня сайта
     */
    public function Uploader(array $uploadFile, $path)
    {
        $pathFull = self::DIR_UPLOAD . DIRECTORY_SEPARATOR . $path;
        $return = null;
        if ($uploadFile['error'] === 0) {
            if ($typeFile = $this->CheckTypeFile($uploadFile["tmp_name"])) {
                $newName = $this->generateNewName($uploadFile["name"]);
                $extensionFile = substr(strrchr($newName, '.'), 1);
                $extensionFileLower = strtolower($extensionFile);
                $newName = preg_replace('/' . $extensionFile . '$/', $extensionFileLower, $newName);
                move_uploaded_file($uploadFile["tmp_name"], $pathFull . DIRECTORY_SEPARATOR . $newName);
                $return = '/' . $this->makePathRelative($pathFull, self::ROOT_SITE) . $newName;
            }
        }
        return $return;
    }

    /**
     * Перемещаем файл в уазаную папку
     * @param $file string (имя файла в паке /upload/temp/ например  '5aa6b8dc9117f.jpeg')
     * @param $toMove (новый путь(без имени файла) относительно папки upload начиная со слеша, пример: '/category/2/')
     * @param $mode (права на новый файл(папку))
     */
    public function moveFileFromTemp(string $file, string $toMove, $mode = 0755)
    {
        $nameFile = basename(self::DIR_TEMP . DIRECTORY_SEPARATOR . $file);
        if (file_exists(self::DIR_UPLOAD . $toMove)) {
            copy(self::DIR_TEMP . DIRECTORY_SEPARATOR . $file, self::DIR_UPLOAD . $toMove . $nameFile);
        } else {
            mkdir(self::DIR_UPLOAD . $toMove, $mode, true);
            copy(self::DIR_TEMP . DIRECTORY_SEPARATOR . $file, self::DIR_UPLOAD . $toMove . $nameFile);
        }
    }


    /**
     * Приводит путь относительно корня сайта в абсолютный для файловой системы
     * @param $pathRelativeSite (пример /upload/image.jpg)
     * @return string (пример /home/user/www/upload/image.jpg)
     */
    static function getAbsolutePath($pathRelativeSite)
    {
        if (preg_match('/^\//', $pathRelativeSite)) {
            return self::ROOT_SITE . $pathRelativeSite;
        } else {
            return self::ROOT_SITE . '/' . $pathRelativeSite;
        }
    }

}