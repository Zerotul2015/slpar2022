<?php
use Amp\Http\Client\Request;
use App\Classes\ActiveRecord\Tables\SupplierTeremonlineCatalogCategory;
use App\Classes\ActiveRecord\Tables\SupplierTeremonlineCatalogManufacturer;
use DiDom\Document;
use Dotenv\Dotenv;
use function Amp\call;





$start = microtime(true);
$memory = memory_get_usage();
require '../vendor/autoload.php';
define('ROOT_DIRECTORY', $_SERVER['DOCUMENT_ROOT'] . '/..');
define('ROOT_DIRECTORY_PUBLIC', $_SERVER['DOCUMENT_ROOT']);
$dotenv = Dotenv::createImmutable(ROOT_DIRECTORY);
$dotenv->load();
$error = '';
$page = file_get_contents('https://www.teremonline.ru/catalog/otoplenie/konvektory/');
$document = new Document($page);

$name = null;
if ($document->has('.sc-te-hdr')) {//страница товара
    $name = trim($document->first('.sc-te-hdr')->text());
} elseif ($document->has('.sar-p')) { //карточка товара на странице категории
    var_dump($document->first('.sar-p'));
    $name = $document->first('.sar-p')->getAttribute('content');
}
var_dump($name);













echo '<br>===============================================================';
echo '<br>Время выполнения скрипта: ' . (microtime(true) - $start) . ' sec.';
echo '<br>Использовано памяти: ' . (memory_get_usage() - $memory) . ' байт';