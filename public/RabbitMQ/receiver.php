<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;

////////////////////////
$callbackSimple = function ($msg) {
    echo 'Текст сообщения: ' . $msg;
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']); # отправляем подтверждение, что обработчик завершил работу
};
////////////////////////

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


$channel->basic_consume(
    'parseProducts',                    #очередь
    '',                             #тег получателя - Идентификатор получателя, валидный в пределах текущего канала. Просто строка
    false,                          #не локальный - TRUE: сервер не будет отправлять сообщения соединениям, которые сам опубликовал
    false,                           #без подтверждения - отправлять соответствующее подтверждение обработчику, как только задача будет выполнена
    false,                          #эксклюзивная - к очереди можно получить доступ только в рамках текущего соединения
    false,                          #не ждать - TRUE: сервер не будет отвечать методу. Клиент не должен ждать ответа
    $callbackSimple                         #функция обратного вызова - метод, который будет принимать сообщение
);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();