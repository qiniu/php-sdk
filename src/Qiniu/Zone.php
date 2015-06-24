<?php
namespace Qiniu;

final Class Zone 
{
    public static $upHost = 'http://up.qiniu.com';

    public function  __construct($upHost) 
    {  
        $this->upHost = $upHost; 
    } 

    public static function zone0() 
    {
        return new self('http://up.qiniu.com');
    }

    public static function zone1() 
    {
        return new self('http://up-z1.qiniu.com');
    }
}

