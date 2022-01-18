<?php


namespace App\Model\Admin;


use App\Controllers\Admin\Authorization;
use App\Model\FilesystemModel;

class UploaderModel
{
    /**
     * Uploader constructor.
     * Загружает файлы во временую папку
     * Возвращает строку URL если картинка одна или закодирвоаный в JSON массив URL
     * @param string $key
     */
    public function __construct($key = 'temp')
    {
        static::upload($key = 'temp');
    }

    public static function upload($key = 'temp')
    {
        $return = '';
        if (isset($_FILES[$key]) && Authorization::isAuth()) {
            $filesystemModel = new FilesystemModel();
            // несколько файлов
            if (isset($_FILES[$key]['name']) && is_array($_FILES[$key]['name'])) {
                $return =  $filesystemModel->UploaderAllTemp($_FILES[$key]);
            } // один файл
            else {
                $return = $filesystemModel->UploaderTemp($_FILES[$key]);
            }
        }
        return $return;
    }
}