<?php
define("CKFiledir", dirname(__FILE__) . '/cookie.dat');

class FCurl
{
    public $return;
    protected $ch;
    public $refer = "";
    public $theagent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0";
    const CKFile = CKFiledir;

    public function __construct()
    {
        $this->ch = curl_init();
        $this->stdcurl();
    }

    protected function stdcurl()
    {
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_REFERER, $this->refer);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->theagent);
    }

    public function get(
        $url = null,
        $hasbody = false,
        $hheader = 1,
        $CKFile = self::CKFile
    )
    {
        if ($hasbody == true) {
            $gbody = false;
        } else {
            $gbody = true;
        }

        curl_setopt($this->ch, CURLOPT_POST, 0);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HEADER, $hheader);
        curl_setopt($this->ch, CURLOPT_NOBODY, $gbody);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $CKFile);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $CKFile);
        /*curl_setopt($this->ch, CURLINFO_HEADER_OUT, true);
        var_dump(curl_getinfo($this->ch));*/

        $rs = curl_exec($this->ch);


        $this->return = $rs;
    }

    public function post(
        $url = null,
        $pfield = null,
        $hasbody = false,
        $hheader = 1,
        $CKFile = self::CKFile
    )
    {
        if ($hasbody == true) {
            $gbody = false;
        } else {
            $gbody = true;
        }

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HEADER, $hheader);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $CKFile);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $CKFile);
        curl_setopt($this->ch, CURLOPT_NOBODY, $gbody);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $pfield);

        $rs = curl_exec($this->ch);

        $this->return = $rs;
    }

    public function clearck()
    {
        file_put_contents(dirname(__FILE__) . "/cookie.dat", "");
    }

    public function resetopt()
    {
        $this->stdcurl();
    }

}