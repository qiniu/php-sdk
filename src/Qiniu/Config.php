<?php
namespace Qiniu;

final class Config
{
    const SDK_VER = '7.0.3';

    const BLOCK_SIZE = 4194304; //4*1024*1024 分块上传块大小，该参数为接口规格，暂不支持修改

    const IO_HOST  = 'http://iovip.qbox.me';            // 七牛源站Host
    const RS_HOST  = 'http://rs.qbox.me';               // 文件元信息管理操作Host
    const RSF_HOST = 'http://rsf.qbox.me';              // 列举操作Host
    const API_HOST = 'http://api.qiniu.com';            // 数据处理操作Host

    public static $upHost;                              // 上传Host
    public static $upHostBackup;                        // 上传备用Host

    public function __construct()                       // 构造函数，默认为zone0
    {
        self::setZone(Zone::zone0());
    }
    
    public static function setZone(Zone $z)
    {
        self::$upHost = $z->upHost;
        self::$upHostBackup = $z->upHostBackup;
    }
}
