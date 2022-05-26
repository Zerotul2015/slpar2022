<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 30.04.18
 * Time: 14:18
 */

use App\Controllers\Images\ImageCache;

require '../../vendor/autoload.php';
\App\Classes\PrepareApp::startup();

echo '<p>---------------------------------------------</p>';
echo 'Очищаем кэш изображений:<br>';
$imageCache = new ImageCache("", "", "");
$settings = ImageCache::$settings;

$fileSystem = new \App\Model\FilesystemModel();
foreach ($settings['path'] as $path) {
    foreach ($settings['templates'] as $pathTemplate => $with) {
        if ($fileSystem->exists(ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate)) {
            $fileSystem->remove(ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate);
        }
    }
    if ($fileSystem->exists(ROOT_DIRECTORY_PUBLIC . $path . '/index.html')) {
        echo 'Путь ' . ROOT_DIRECTORY_PUBLIC . $path . '/index.html уже существует<br>';
    } else {
        $fileSystem->dumpFile(ROOT_DIRECTORY_PUBLIC . $path . '/index.html', 'access denied');
        echo 'Файл ' . ROOT_DIRECTORY_PUBLIC . $path . '/index.html' . ' создан<br>';
    }
    foreach ($settings['templates'] as $pathTemplate => $with) {
        if ($fileSystem->exists(ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate)) {
            echo 'Путь ' . ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate . ' уже существует<br>';
        } else {
            $fileSystem->mkdir(ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate, 0755);
            echo 'Путь ' . ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate . ' создан<br>';
        }

        if ($fileSystem->exists(ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate . '/index.html')) {
            echo 'Путь ' . ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate . '/index.html уже существует<br>';
        } else {
            $fileSystem->dumpFile(ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate . '/index.html', 'access denied');
            echo 'Файл ' . ROOT_DIRECTORY_PUBLIC . $path . $pathTemplate . '/index.html' . ' создан<br>';
        }

    }
}

//очизаем кэш изображений товаров
$pathImageProducts = ROOT_DIRECTORY_PUBLIC . '/images/products/';

$resultScanDir = scandir($pathImageProducts);

foreach ($resultScanDir as $dir) {
    if ($dir == '.' || $dir == '..') {
        continue;
    } else {
        $pathCurrentProduct = $pathImageProducts . $dir . '/';
    }
    if (is_dir($pathCurrentProduct)) {
        $resultScanDirProduct = scandir($pathCurrentProduct);
        if (count($resultScanDirProduct) == 2) {
            $fileSystem->remove($pathCurrentProduct);
        }
        foreach ($resultScanDirProduct as $dirTemplate) {
            if ($dirTemplate == '.' || $dirTemplate == '..') {
                continue;
            } else {
                $pathDirTemplate = $pathCurrentProduct . $dirTemplate;
                if (is_dir($pathDirTemplate)) {
                    $fileSystem->remove($pathDirTemplate);
                }
            }
        }

    }
}
