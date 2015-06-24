<?php
namespace Qiniu;

final class Zone
{
    public static $upHost = 'http://up.qiniu.com';
    public static $upBackupHost = 'http://upload.qiniu.com';

    public function __construct()
    {
    }

    public function setUpHost($host)
    {
        self::$upHost = $host;
    }

    public function setUpBackupHost($host)
    {
        self::$upBackupHost = $host;
    }

    public static function zone0()
    {
        return new self;
    }

    public static function zone1()
    {
        $z1 = new self;
        $z1->setUpHost('http://up-z1.qiniu.com');
        $z1->setUpBackupHost('http://upload-z1.qiniu.com');
        return $z1;
    }
}
