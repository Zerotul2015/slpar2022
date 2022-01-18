<?php

$start = microtime(true);
$memory = memory_get_usage();
require '../vendor/autoload.php';
require '../vendor/autoload.php';


use Dotenv\Dotenv;


define('ROOT_DIRECTORY', $_SERVER['DOCUMENT_ROOT'] . '/..');
define('ROOT_DIRECTORY_PUBLIC', $_SERVER['DOCUMENT_ROOT']);
$dotenv = Dotenv::createImmutable(ROOT_DIRECTORY);
$dotenv->load();
use App\Classes\ActiveRecord\Tables\SupplierTeremonlineCatalogProduct;
use App\Model\Parser\Terem\TeremParserModel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Receive
{

    public array $workers = ['parser', 'images'];

    public array $workerTypes = [
        'parser' => ['productPage', 'productCard', 'category', 'brand'],
        'images' => ['download', 'compress']
    ];

    public function Main()
    {
        $connection = new AMQPStreamConnection('rabbitmq', '5672', 'rabbitmq', 'rabbitmq');
        $channel = $connection->channel();

        $channel->exchange_declare('longOperation', 'topic', false, true, false);

        list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

        $workers = $this->workers;
        $workerTypes = $this->workerTypes;

        foreach ($workers as $worker) {
            if (isset($workerTypes[$worker])) {
                foreach ($workerTypes[$worker] as $workerType) {
                    $channel->queue_bind($queue_name, 'longOperation', $worker . '.' . $workerType);
                }
            }
        }


        $callback = function ($msg) {
            $limit = json_decode($msg->body, true);
            if (isset($limit[0], $limit[1])) {
                $products = SupplierTeremonlineCatalogProduct::find()->sort('ASC')->limit($limit)->all();
                TeremParserModel::parseProductsPages($products);
            }
        };

        $channel->basic_consume($queue_name, '', false, false, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}