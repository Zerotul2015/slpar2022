<?php


namespace App\Model\Notification;


use App\Classes\ActiveRecord\Tables\Customer;
use App\Classes\ActiveRecord\Tables\Orders;
use App\Classes\ActiveRecord\Tables\OrdersStatus;
use App\Classes\Mail;
use App\Model\MainModel;
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
    public static function notificationOrder($status, Orders $order, string $recipientMail = ''): bool|int
    {
        $result = false;
        $statusInBase = OrdersStatus::findOne($status);
        // подстановка значений в письма
        $id = $order->id;
        $placeholder = ['[id]', '[link.details]', '[link.pay]', '[name]'];

        $customer = Customer::findOne($order->customer_id);
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
}