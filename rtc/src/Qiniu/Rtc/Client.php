<?php

namespace Qiniu\Rtc;

class Client
{
    private $_mac;

    public function __construct($mac)
    {
        $this->_mac=$mac;
    }
}
