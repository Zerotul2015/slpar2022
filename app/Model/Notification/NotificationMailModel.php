<?php


namespace App\Model\Notification;


use App\Classes\ActiveRecord\Tables\Customer;
use App\Classes\ActiveRecord\Tables\Orders;
use App\Classes\ActiveRecord\Tables\OrdersStatus;
use App\Classes\Mail;
use Exception;

class NotificationMailModel
{


    /**
     * Отправляет уведомление о заказах на $recipientMail, если не указывать то уведомление администратору
     * @param $status
     * @param Orders $order
     * @param string $recipientMail
     * @return bool|int
     * @throws Exception
     */
    public static function notificationOrder($status, Orders $order, $recipientMail = '')
    {
        $result = false;
        $statusInBase = OrdersStatus::findOne($status);
        // подстановка значений в письма
        $id = $order->id;
        $placeholder = ['[id]', '[link.details]', '[link.pay]', '[name]'];

        $customer = Customers::findOne($order->customer_id);
        if ($customer->token) {
            $tokenDetails = $customer->token;
        } else {
            $tokenDetails = MainModel::generateToken();
            $customer->token = $tokenDetails;
            $customer->save();
        }
        $linkDetails = 'https://' . $_SERVER['HTTP_HOST'] . '/orders/preview/' . $tokenDetails;
        $linkDetailsAdmin = 'https://' . $_SERVER['HTTP_HOST'] . '/admin/orders/details/' . $order->id;
        $linkPay = 'https://' . $_SERVER['HTTP_HOST'] . '/orders/pay/' .$order->id . '?token=' . $order->token;
        if (!$recipientMail) {
            $recipientMail = $customer->mail;
        }
        $name = $customer ? $customer->name : 'имя покупателя не указано';
        $statusInBase->message_mail = str_replace($placeholder, [$id, $linkDetails, $linkPay, $name], $statusInBase->message_mail);

        if ($statusInBase->send_admin) {
            $statusInBase->message_mail_admin = str_replace($placeholder, [$id, $linkDetailsAdmin, $linkPay, $name], $statusInBase->message_mail_admin);
        }

        if ($statusInBase) {
            $mail = new Mail();
            switch ($status) {
                case 1: //новый
                    //для покупателя
                    if ($recipientMail) {
                        $mail->setRecipient($recipientMail);
                        $mail->setSubject('Ваш заказ №' . $order->id . ' получен');
                        $mail->setMessage($statusInBase->message_mail);
                        $result = $mail->send();
                    }
                    //для админа
                    if ($statusInBase->send_admin) {
                        $mailAdmin = new Mail();
                        $mailAdmin->setSubject('Получен новый заказ №' . $order->id);
                        $mailAdmin->setMessage($statusInBase->message_mail_admin);
                        $result = $mailAdmin->send();
                    }
                    break;
                case
                2: //ожидает оплаты
                    //для покупателя
                    if ($recipientMail) {
                        $mail->setRecipient($recipientMail);
                        $mail->setSubject('Ваш заказ №' . $order->id . ' ' . $statusInBase->name);
                        $mail->setMessage($statusInBase->message_mail);
                        $result = $mail->send();
                    }
                    //для админа
                    if ($statusInBase->send_admin) {
                        $mailAdmin = new Mail();
                        $mailAdmin->setSubject('Заказ №' . $order->id . ' ' . $statusInBase->name);
                        $mailAdmin->setMessage($statusInBase->message_mail_admin);
                        $result = $mailAdmin->send();
                    }
                    break;
                case 3: //оплачен
                    //для покупателя
                    if ($recipientMail) {
                        $mail->setRecipient($recipientMail);
                        $mail->setSubject('Ваш заказ №' . $order->id . ' ' . $statusInBase->name);
                        $mail->setMessage($statusInBase->message_mail);
                        $result = $mail->send();
                    }
                    //для админа
                    if ($statusInBase->send_admin) {
                        $mailAdmin = new Mail();
                        $mailAdmin->setSubject('Поступила оплата заказа №' . $order->id);
                        $mailAdmin->setMessage($statusInBase->message_mail_admin);
                        $result = $mailAdmin->send();
                    }
                    break;
                case 4: //отправлен
                    //для покупателя
                    if ($recipientMail) {
                        $mail->setRecipient($recipientMail);
                        $mail->setSubject('Ваш заказ №' . $order->id . ' ' . $statusInBase->name);
                        $mail->setMessage($statusInBase->message_mail);
                        $result = $mail->send();
                    }
                    //для админа
                    if ($statusInBase->send_admin) {
                        $mailAdmin = new Mail();
                        $mailAdmin->setSubject('Заказ №' . $order->id . ' ' . $statusInBase->name);
                        $mailAdmin->setMessage($statusInBase->message_mail_admin);
                        $result = $mailAdmin->send();
                    }
                    break;
                default:
                    break;
            }
        }
        return $result;
    }

    /**
     * Отправляет сертификат на указаную почту, если передатб $customMessage то
     * оно будет использовано вместо стандартного письма.
     * @param $certificate
     * @param $mailTo
     * @param string $customMessage
     * @return int
     */
    public static function sendCertificate($certificate, $mailTo, $customMessage = ''): int
    {
        $mail = new Mail();
        $mail->setSubject('Подарочный сертификат интернет-магазина ИзЛесуВестимо');

        //состовляем сообщение если не передано готовое
        if (empty($customMessage)) {
            $customMessage = "<p style='text-align: center'><img style='display:block; width:100%' src='https://$_SERVER[SERVER_NAME]/images/certificates/large/$certificate->image' alt='подарочный сертификат'></p>";
            $customMessage .= "<p>Подарочный сертификат на <b>$certificate->amount ₽</b>";
            if ($certificate->delivery) {
                $customMessage .= " для $certificate->recipien от $certificate->sender</p>";
            } else {
                if ($certificate->recipient) {
                    $customMessage .= " для $certificate->recipient";
                }
                if ($certificate->sender) {
                    $customMessage .= " от $certificate->sender";
                }
                $customMessage .= '.</p>';
            }
            if (!empty($certificate->message_for_recipient)) {
                $customMessage .= "<p>Сообщение от отправителя:<br>$certificate->message_for_recipient</p>";
            }
            $customMessage .= "<p>
                                Номер сертификата: $certificate->number
                                <br>
                                Пин-код: $certificate->pin
                            </p>";
        }

        $mail->setRecipient($mailTo);
        $mail->setMessage($customMessage);
        return $mail->send();
    }

    /**
     * @param $certificates
     * @param $recipient
     * @return bool|int
     */
    public static function sendCertificateRecovery($certificates, $recipient){
        $mail = new Mail();
        $mail->setSubject('Подарочные сертификаты "Каминно-Печной Дискаунтер КАМИН42"');
        $mail->setRecipient($recipient);
        $message = '';
        if($certificates) {
            $message = 'Вы запросили восстановление подарочных сертифкатов.<br>Ниже приведен список сертификатов зарегистрированных на ваш e-mail:<br>';
            foreach ($certificates as $certificate) {
                $message .= '<p>Сертификат №' . $certificate->number . '  Пин-код:' . $certificate->pin . '</p>';
            }
        }
        else{
            $message = 'Вы запросили восстановление подарочных сертификатов.<br>К сожалению мы не нашли сертификатов в которых вы указаны как получатель.<br>Возможно у вас есть другая почта и сертификат был оформлен на нее.';
        }
        $mail->setMessage($message);
        return $mail->send();
    }
}