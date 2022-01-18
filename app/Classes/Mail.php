<?php
/**
 * Created by PhpStorm.
 * User: zerotul
 * Date: 22.10.18
 * Time: 22:28
 */

namespace App\Classes;


use App\Classes\ActiveRecord\Tables\SettingsNotifications;

class Mail
{
    protected $smtp;
    protected $login;
    protected $pass;
    protected $sender;
    protected $recipientDefault;
    protected $recipient = '';
    protected $transport;
    protected $mailer;
    protected $subject;
    protected $messageHTML;
    protected $message;
    protected $contentType = 'text/html';

    public function __construct()
    {
        $settings = SettingsNotifications::findOne(1);
        $this->smtp = $settings->smtp;
        $this->login = $settings->email_login;
        $this->pass = $settings->email_pass;
        $this->sender = $settings->email_to_send;
        $this->recipientDefault = $settings->email_to_receive;

        $this->transport = (new \Swift_SmtpTransport($this->smtp, 465, 'ssl'))
            ->setUsername($this->login)
            ->setPassword($this->pass);

        $this->mailer = new \Swift_Mailer($this->transport);
    }

    /**
     * Содержимое сообщения
     * @param string $messageHTML
     * @return $this
     */
    public function setMessage(string $messageHTML)
    {
        $this->messageHTML = $messageHTML;
        return $this;
    }

    /**
     * Указываем почту получателя
     * @param string $recipientMail
     * @return $this
     */
    public function setRecipient(string $recipientMail)
    {
        if(filter_var($recipientMail, FILTER_VALIDATE_EMAIL)) {
            $this->recipient = $recipientMail;
        }
        return $this;
    }

    /**
     * Указываем тему сообщения
     * @param string $subject
     * @return $this
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Устанавливаем формат сообщения
     * 0 - text, 1 - html
     * @param int $contentType
     * @return $this
     */
    public function setContentType(int $contentType)
    {
        if ($contentType == 0) {
            $this->contentType = 'text/plain';
        }
        if ($contentType == 1) {
            $this->contentType = 'text/html';
        }
        return $this;
    }

    private function createMessage()
    {
        $this->recipient = empty($this->recipient) ? $this->recipientDefault : $this->recipient;
        $this->message = (new \Swift_Message($this->subject))
            ->setFrom($this->sender)
            ->setTo($this->recipient)
            ->setBody($this->messageHTML, 'text/html');
    }

    /** Отправляем сообщение
     * @return int
     */
    public function send()
    {
        $this->createMessage();
        try {
            return (bool)$this->mailer->send($this->message);
        }catch(\Exception $e){
            return false;
            // Get error here
            $e->getCode();
            $e->getMessage();
            exit;
        }
    }
}