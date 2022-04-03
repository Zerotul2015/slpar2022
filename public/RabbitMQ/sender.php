<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

////////////
$message = random_int(0, 1000);

////////////
$connection = new AMQPStreamConnection('rabbitmq', '5672', 'rabbitmq', 'rabbitmq');

$channel = $connection->channel();


$channel->queue_declare(
    'parseProducts',#queue name - Имя очереди может содержать до 255 байт UTF-8 символов
    false,#passive - может использоваться для проверки того, инициирован ли обмен, без того, чтобы изменять состояние сервера
    true,#durable - убедимся, что RabbitMQ никогда не потеряет очередь при падении - очередь переживёт перезагрузку брокера
    false,#exclusive - используется только одним соединением, и очередь будет удалена при закрытии соединения
    false #autodelete - очередь удаляется, когда отписывается последний подписчик
);
$msg = new AMQPMessage(
    $message,
    ['delivery_mode' => 2]#создаёт сообщение постоянным, чтобы оно не потерялось при падении или закрытии сервера
);


$channel->basic_publish(
    $msg,                    #message
    '',             #exchange
    'parseProducts'   #routing key
);

$channel->close();
$connection->close();