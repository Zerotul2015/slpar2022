<?php

namespace App\Controllers\Amqp;

use App\Classes\ActiveRecord\Tables\SupplierTeremonlineCatalogCategory;
use App\Classes\ActiveRecord\Tables\SupplierTeremonlineCatalogProduct;
use App\Model\Parser\Terem\TeremParserModel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Receive
{

    public function Main()
    {
        ////////////////
        $callback = function ($msg) {
            $limit = json_decode($msg->body, true);
            if (isset($limit[0], $limit[1])) {
                echo "START $limit[0] END $limit[1]";
                $products = SupplierTeremonlineCatalogProduct::find()->sort('ASC')->limit($limit)->all();
                TeremParserModel::parseProductsPages($products);
                #отправляем подтверждение, что обработчик завершил работу
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            }
        };
        //////////////

        $connection = new AMQPStreamConnection('rabbitmq', '5672', 'rabbitmq', 'rabbitmq');
        $channel = $connection->channel();

        $channel->queue_declare(
            'parseProducts',#queue name - Имя очереди может содержать до 255 байт UTF-8 символов
            false,#passive - может использоваться для проверки того, инициирован ли обмен, без того, чтобы изменять состояние сервера
            true,#durable - убедимся, что RabbitMQ никогда не потеряет очередь при падении - очередь переживёт перезагрузку брокера
            false,#exclusive - используется только одним соединением, и очередь будет удалена при закрытии соединения
            false #autodelete - очередь удаляется, когда отписывается последний подписчик
        );

        $channel->basic_qos(
            null,   #размер предварительной выборки - размер окна предварительно выборки в октетах, null означает “без определённого ограничения”
            1,      #количество предварительных выборок - окна предварительных выборок в рамках целого сообщения
            null    #глобальный - global=null означает, что настройки QoS должны применяться для получателей, global=true означает, что настройки QoS должны применяться к каналу
        );

        $channel->basic_consume('parseProducts', '', false, false, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function DownloadImages()
    {

        $callback = function ($msg) {
            $limit = json_decode($msg->body, true);
            if (isset($limit[0], $limit[1])) {
                echo "START $limit[0] END $limit[1]";
                $products = SupplierTeremonlineCatalogProduct::find()->sort('ASC')->limit($limit)->all();
                TeremParserModel::downloadImagesProducts($products);
                #отправляем подтверждение, что обработчик завершил работу
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            }
        };

        $connection = new AMQPStreamConnection('rabbitmq', '5672', 'rabbitmq', 'rabbitmq');
        $channel = $connection->channel();

        $channel->exchange_declare('downloadImages', 'fanout', false, true, false);

        list($queue_name, ,) = $channel->queue_declare("", false, true, false, false);

        $channel->queue_bind($queue_name, 'downloadImages');

        $channel->basic_consume($queue_name, '', false, false, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function matchingProducts()
    {
        $callback = function ($msg) {
            $limit = json_decode($msg->body, true);
            if (isset($limit[0], $limit[1])) {
                echo "START $limit[0] END $limit[1]";
                $products = SupplierTeremonlineCatalogProduct::find()->sort('ASC')->limit($limit)->all();
                TeremParserModel::matchingProducts($products);
                #отправляем подтверждение, что обработчик завершил работу
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            }
        };

        $connection = new AMQPStreamConnection('rabbitmq', '5672', 'rabbitmq', 'rabbitmq');
        $channel = $connection->channel();

        $channel->exchange_declare('downloadImages', 'fanout', false, true, false);

        list($queue_name, ,) = $channel->queue_declare("", false, true, false, false);

        $channel->queue_bind($queue_name, 'downloadImages');

        $channel->basic_consume($queue_name, '', false, false, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}