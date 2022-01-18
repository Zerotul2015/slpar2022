<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 15.03.18
 * Time: 23:53
 */

namespace App\Controllers\Admin;

use App\Model\Admin\UploaderModel;


/**
 * Конструктор класа загружает файлы в зависимости установленых ключей $_FILES
 * Возвращает url относительно корня сайта для загруженого файла
 * Class Uploader
 * @package App\Controllers\Admin
 */
class Uploader
{

    public function __construct()
    {
        if (!Authorization::isAuth()) {
            header('HTTP/1.1 403 Forbidden');
            header("Status: 403 Forbidden");
            die;
//            if (isset($_FILES[$key])) {
//                $filesystemModel = new FilesystemModel();
//                // несколько файлов
//                if (isset($_FILES[$key]['name']) && is_array($_FILES[$key]['name'])) {
//                    echo json_encode($filesystemModel->UploaderAllTemp($_FILES[$key]));
//                } // один файл
//                else {
//                    echo $filesystemModel->UploaderTemp($_FILES[$key]);
//                }
//            }
        }
    }

    public function Main()
    {
        $answer['result'] = true;
        $answer['url'] = UploaderModel::upload();
        header('Content-Type: application/json');
        echo json_encode($answer, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}