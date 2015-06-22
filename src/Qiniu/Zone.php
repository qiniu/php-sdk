<?php
namespace Qiniu;

final Class Zone 
{
    const ZONE0 = 0,                          // Zone 常量， 现在有两个zone可以设置，zone0 & zone1
          ZONE1 = 1;

    public $upHost;

    public function  __construct($upHost) {  
        $this->upHost = $upHost; 
    } 

    public static function zone0() {
        return self::zone(self::ZONE0);
    }

    public static function zone1() {
        return self::zone(self::ZONE1);
    }

    public static function zone($z) {
        switch ($z) {
            case self::ZONE0:
                return new self('http://up.qiniu.com');
                break;
            case self::ZONE1:
                return new self('http://up-z1.qiniu.com');
                break;
        }
        return;
    }
}

