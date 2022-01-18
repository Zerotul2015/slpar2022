<?php

namespace App\Controllers\Parser;

use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\HttpException;
use Amp\Http\Client\Request;
use App\Classes\ActiveRecord\Tables\SupplierTeremonlineCatalogCategory;
use App\Classes\ActiveRecord\Tables\SupplierTeremonlineCatalogManufacturer;
use App\Classes\ActiveRecord\Tables\SupplierTeremonlineCatalogProduct;
use App\Model\Parser\Terem\TeremParserModel;
use DiDom\Document;
use DiDom\Exceptions\InvalidSelectorException;
use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

use function Amp\call;
use function Amp\Promise\all;
use function Amp\Promise\wait;
use function Amp\delay;

class TeremParser
{

    public function RunParserBrands()
    {
        $brandsUrls = TeremParserModel::parseBrands();
    }

    /**
     * @throws InvalidSelectorException
     */
    public function RunParserCategories()
    {
        $brandsUrls = TeremParserModel::parseCategories();
    }


    /**
     * Обходим первую страницу основных подкатегорий и собираем ссылки на все страницы этих категорий.
     */
    public function parseCategoriesLinksToPages()
    {
        TeremParserModel::parseCategoriesLinksToPages();
    }


    /**
     * Отправляет в очередь на парсинг карточки товаров со страниц категорий
     * @return array errors если были
     * @throws Exception
     */
    public function parseProductsCardsOnCategoriesPages()
    {
        $categories = SupplierTeremonlineCatalogCategory::find()->indexBy()->findGroupBy('parent_id');
        $links = [];
        foreach ($categories[0] as $catMain) {
            if (isset($categories[$catMain->id])) {
                foreach ($categories[$catMain->id] as $cateSub) {
                    if (!empty($cateSub->links_to_pages)) {
                        $links = array_merge($links, $cateSub->links_to_pages);
                    }
                }
            }
        }
        $linksChunks = array_chunk($links, 10);

        $connection = new AMQPStreamConnection('rabbitmq', '5672', 'rabbitmq', 'rabbitmq');
        $channel = $connection->channel();

        $channel->queue_declare(
            'parseProductsOnCategoryPage',#queue name - Имя очереди может содержать до 255 байт UTF-8 символов
            false,#passive - может использоваться для проверки того, инициирован ли обмен, без того, чтобы изменять состояние сервера
            true,#durable - убедимся, что RabbitMQ никогда не потеряет очередь при падении - очередь переживёт перезагрузку брокера
            false,#exclusive - используется только одним соединением, и очередь будет удалена при закрытии соединения
            false #autodelete - очередь удаляется, когда отписывается последний подписчик
        );


        foreach ($linksChunks as $chunkLinks) {
            $msg = new AMQPMessage(
                json_encode($chunkLinks),
                ['delivery_mode' => 2]#создаёт сообщение постоянным, чтобы оно не потерялось при падении или закрытии сервера
            );
            $channel->basic_publish($msg, '', 'parseProductsOnCategoryPage');
        }


        $channel->close();
        $connection->close();
    }

    /**
     * Парсим карточки товаров со страниц категорий
     */
    public function WorkerParseProductsCardsOnCategoriesPages()
    {
        ////////////////////
        $callback = function ($msg) {
            $links = json_decode($msg->body, true);
            var_dump($links);
            if (!empty($links)) {
                TeremParserModel::parseProductsCardsOnCategoryPages($links);
            }
            #отправляем подтверждение, что обработчик завершил работу
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };
        ////////////////////


        $connection = new AMQPStreamConnection('rabbitmq', '5672', 'rabbitmq', 'rabbitmq');
        $channel = $connection->channel();

        $channel->queue_declare(
            'parseProductsOnCategoryPage',#queue name - Имя очереди может содержать до 255 байт UTF-8 символов
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

        $channel->basic_consume('parseProductsOnCategoryPage', '', false, false, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }


    //Получает задание на парсинг страниц товара из очереди и выполняет.
    public function WorkerParseProducts()
    {
        ////////////////
        $callback = function ($msg) {
            $limit = json_decode($msg->body, true);
            if (isset($limit[0], $limit[1])) {
                $products = SupplierTeremonlineCatalogProduct::find()->limit($limit)->all();
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

    //Отправляет задание на парсинг страниц товаров в очередь.
    public function ParseProducts()
    {
        $connection = new AMQPStreamConnection('rabbitmq', '5672', 'rabbitmq', 'rabbitmq');
        $channel = $connection->channel();

        $channel->queue_declare(
            'parseProducts',#queue name - Имя очереди может содержать до 255 байт UTF-8 символов
            false,#passive - может использоваться для проверки того, инициирован ли обмен, без того, чтобы изменять состояние сервера
            true,#durable - убедимся, что RabbitMQ никогда не потеряет очередь при падении - очередь переживёт перезагрузку брокера
            false,#exclusive - используется только одним соединением, и очередь будет удалена при закрытии соединения
            false #autodelete - очередь удаляется, когда отписывается последний подписчик
        );

        $page = 1;
        $perPage = 50;
        $productsCount = SupplierTeremonlineCatalogProduct::find()->count();
        $pageCount = ceil($productsCount / $perPage);
        do {
            $start = $page * $perPage - $perPage;
            $start = ($start > $productsCount - 1) ? ($productsCount - 1) : $start;
            $limitQuery = [$start, $perPage];
            $msg = new AMQPMessage(
                json_encode($limitQuery),
                ['delivery_mode' => 2]#создаёт сообщение постоянным, чтобы оно не потерялось при падении или закрытии сервера
            );
            $channel->basic_publish($msg, '', 'parseProducts');
            $page++;
        } while ($page <= $pageCount);
        $channel->close();
        $connection->close();
    }

    //Получает задания на загрузку изображений для товаров из очереди и загружает их.
    public function WorkerDownloadImagesProducts()
    {
        $callback = function ($msg) {
            $limit = json_decode($msg->body, true);
            if (isset($limit[0], $limit[1])) {
                $products = SupplierTeremonlineCatalogProduct::find()->limit($limit)->all();
                TeremParserModel::downloadImagesProducts($products);
                #отправляем подтверждение, что обработчик завершил работу
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            }
        };

        $connection = new AMQPStreamConnection('rabbitmq', '5672', 'rabbitmq', 'rabbitmq');
        $channel = $connection->channel();

        $channel->queue_declare(
            'downloadImages',#queue name - Имя очереди может содержать до 255 байт UTF-8 символов
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

        $channel->basic_consume('downloadImages', '', false, false, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    //Отправляет задание на загрузку изображений для товаров в очередь.
    public function DownloadImagesProducts()
    {
        $connection = new AMQPStreamConnection('rabbitmq', '5672', 'rabbitmq', 'rabbitmq');
        $channel = $connection->channel();

        $channel->queue_declare(
            'downloadImages',#queue name - Имя очереди может содержать до 255 байт UTF-8 символов
            false,#passive - может использоваться для проверки того, инициирован ли обмен, без того, чтобы изменять состояние сервера
            true,#durable - убедимся, что RabbitMQ никогда не потеряет очередь при падении - очередь переживёт перезагрузку брокера
            false,#exclusive - используется только одним соединением, и очередь будет удалена при закрытии соединения
            false #autodelete - очередь удаляется, когда отписывается последний подписчик
        );

        $page = 1;
        $perPage = 50;
        $productsCount = SupplierTeremonlineCatalogProduct::find()->count();
        $pageCount = ceil($productsCount / $perPage);
        do {
            $start = $page * $perPage - $perPage;
            $start = ($start > $productsCount - 1) ? ($productsCount - 1) : $start;
            $limitQuery = [$start, $perPage];
            $msg = new AMQPMessage(
                json_encode($limitQuery),
                ['delivery_mode' => 2]#создаёт сообщение постоянным, чтобы оно не потерялось при падении или закрытии сервера
            );
            $channel->basic_publish($msg, '', 'downloadImages');
            $page++;
        } while ($page <= $pageCount);
        $channel->close();
        $connection->close();
    }


    /**
     * Перенос категорий в наши категории
     * @return void
     */
    public function ComparisonCategories()
    {
        $categories = SupplierTeremonlineCatalogCategory::find()->findGroupBy('parent_id');
        //основные категории
        if (isset($categories[0]) && !empty($categories[0])) {
            TeremParserModel::matchingCategories($categories[0]);
            foreach ($categories[0] as $categoryMain) {
                //подкатегории 1-го уровня
                if (isset($categories[$categoryMain->id])) {
                    TeremParserModel::matchingCategories($categories[$categoryMain->id]);
                    foreach ($categories[$categoryMain->id] as $categorySub) {
                        //подкатегории 2-го уровня
                        if (isset($categories[$categorySub->id])) {
                            TeremParserModel::matchingCategories($categories[$categorySub->id]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Перенос производителей в наши.
     * @return void
     */
    public function ComparisonBrands()
    {
        $brands = SupplierTeremonlineCatalogManufacturer::findAll();
        TeremParserModel::matchingBrands($brands);
    }


    /**
     * Ставит задание на сопоставление товаров в очередь.
     * @return void
     * @throws Exception
     */
    public function ComparisonProducts()
    {
        $connection = new AMQPStreamConnection('rabbitmq', '5672', 'rabbitmq', 'rabbitmq');
        $channel = $connection->channel();

        $channel->queue_declare(
            'comparisonProducts',#queue name - Имя очереди может содержать до 255 байт UTF-8 символов
            false,#passive - может использоваться для проверки того, инициирован ли обмен, без того, чтобы изменять состояние сервера
            true,#durable - убедимся, что RabbitMQ никогда не потеряет очередь при падении - очередь переживёт перезагрузку брокера
            false,#exclusive - используется только одним соединением, и очередь будет удалена при закрытии соединения
            false #autodelete - очередь удаляется, когда отписывается последний подписчик
        );

        $page = 1;
        $perPage = 100;
        $productsCount = SupplierTeremonlineCatalogProduct::find()->count();
        $pageCount = ceil($productsCount / $perPage);
        do {
            $start = $page * $perPage - $perPage;
            $start = ($start > $productsCount - 1) ? ($productsCount - 1) : $start;
            $limitQuery = [$start, $perPage];
            $msg = new AMQPMessage(
                json_encode($limitQuery),
                ['delivery_mode' => 2]#создаёт сообщение постоянным, чтобы оно не потерялось при падении или закрытии сервера
            );
            $channel->basic_publish($msg, '', 'comparisonProducts');
            $page++;
        } while ($page <= $pageCount);
        $channel->close();
        $connection->close();
    }

    // Запускает слушателя заданий сопоставления товаров
    public function WorkerComparisonProducts()
    {
        $callback = function ($msg) {
            $limit = json_decode($msg->body, true);
            if (isset($limit[0], $limit[1])) {
                $products = SupplierTeremonlineCatalogProduct::find()->limit($limit)->all();
                TeremParserModel::matchingProducts($products);
                #отправляем подтверждение, что обработчик завершил работу
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            }
        };

        $connection = new AMQPStreamConnection('rabbitmq', '5672', 'rabbitmq', 'rabbitmq');
        $channel = $connection->channel();

        $channel->queue_declare(
            'comparisonProducts',#queue name - Имя очереди может содержать до 255 байт UTF-8 символов
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

        $channel->basic_consume('comparisonProducts', '', false, false, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

}