<?php

/**
 * Created by PhpStorm.
 * User: Tu TV
 * Date: 22/11/2015
 * Time: 5:46 AM
 */
class FriesMail
{
    public $subject;
    public $html;
    public $text;

    /**
     * @var array
     */
    public $to;

    /**
     * @var string
     */
    public $from;

    public $fromName;

    public function __construct($subject, $html)
    {
        $this->subject = $subject;
        $this->html = $html;
        $this->text = strip_tags($this->html);
    }

    /**
     * Set email to
     *
     * @param $mail
     *
     * @return $this
     */
    public function addTo($mail)
    {
        $this->to[] = $mail;

        return $this;
    }

    /**
     * Set email from
     *
     * @param $from
     *
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Set from name
     *
     * @param $fromName
     *
     * @return $this
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;

        return $this;
    }

    /**
     * Get array to
     *
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Get from
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Get from name
     *
     * @return mixed
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * Get subject
     *
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Get html
     *
     * @return mixed
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Get text
     *
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    public function sendMail()
    {
        $url = 'https://api.sendgrid.com/';
        $user = 'tutv95';
        $pass = 'dktd2015';

        $friesMail = $this;

        $json_string = array(

            'to' => $friesMail->getTo(),
            'category' => 'confirmed_account'
        );

        $params = array(
            'api_user' => $user,
            'api_key' => $pass,
            'x-smtpapi' => json_encode($json_string),
            'to' => 'fries.uet@gmail.com',
            'subject' => $friesMail->getSubject(),
            'html' => $friesMail->getHtml(),
            'text' => $friesMail->getText(),
            'from' => $friesMail->getFrom(),
            'fromname' => $friesMail->getFromName(),
        );

        $request = $url . 'api/mail.send.json';

        // Generate curl request
        $session = curl_init($request);
        curl_setopt($session, CURLOPT_POST, true);
        curl_setopt($session, CURLOPT_POSTFIELDS, $params);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_SSLVERSION, 6);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($session);
        curl_close($session);

        $re = json_decode($response);

//        dd($re);

        if ($re->message == 'success') {
            return true;
        }

        return false;
    }
}