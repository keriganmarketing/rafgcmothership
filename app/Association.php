<?php
namespace App;

use PHRETS\Configuration;
use PHRETS\Session;


abstract class Association {
    protected $url;
    protected $rets;
    protected $password;
    protected $username;
    protected $retsClass;
    protected $retsResource;
    protected $localResource;

    public function connect()
    {
        $config = new Configuration();
        $config->setLoginUrl($this->url)
            ->setUsername($this->username)
            ->setPassword($this->password)
            ->setRetsVersion('1.7.2')
            ->setOption("compression_enabled", true)
            ->setOption("offset_support", true);
        $this->rets = new Session($config);
        $this->rets->Login();
        return $this;
    }
}