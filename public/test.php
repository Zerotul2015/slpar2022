<?php

use App\Model\Admin\Images\ImagesModel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

use Dotenv\Dotenv;

$start = microtime(true);
$memory = memory_get_usage();
require '../vendor/autoload.php';
define('ROOT_DIRECTORY', $_SERVER['DOCUMENT_ROOT'] . '/..');
define('ROOT_DIRECTORY_PUBLIC', $_SERVER['DOCUMENT_ROOT']);
$dotenv = Dotenv::createImmutable(ROOT_DIRECTORY);
$dotenv->load();
$error = '';

var_dump(filter_var('kostyalinksl.com', FILTER_VALIDATE_EMAIL));

echo '<br>===============================================================';
echo '<br>Время выполнения скрипта: ' . (microtime(true) - $start) . ' sec.';
echo '<br>Использовано памяти: ' . (memory_get_usage() - $memory) . ' байт';