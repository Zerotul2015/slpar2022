<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 09.03.19
 * Time: 16:13
 */

namespace App\Model\Admin\Images;


class ImagesModel
{

    /**
     * lossless оптимизация изображений.
     * Поддерживает jpeg, png, gif
     * @param $fullPathForImage
     */
    public static function optimizeImage($fullPathForImage)
    {
        $imageType = exif_imagetype($fullPathForImage);
        switch ($imageType) {
            case 2://IMAGETYPE_JPEG
                exec('jpegoptim ' . $fullPathForImage . ' --strip-all --all-progressive > /dev/null 2>&1');
                break;
            case 3://IMAGETYPE_PNG
                exec('optipng ' . $fullPathForImage . ' > /dev/null 2>&1');
                break;
            default:

                break;
        }
    }
}