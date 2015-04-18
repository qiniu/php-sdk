<?php
namespace Qiniu;

final class Config
{
    const SDK_VER = '7.0.2';

    const IO_HOST  = 'http://iovip.qbox.me';            // 七牛源站Host
    const RS_HOST  = 'http://rs.qbox.me';               // 文件元信息管理操作Host
    const RSF_HOST = 'http://rsf.qbox.me';              // 列举操作Host
    const API_HOST = 'http://api.qiniu.com';            // 数据处理操作Host

    const UPAUTO_HOST = 'http://up.qiniu.com';          // 默认上传Host
    const UPDX_HOST = 'http://updx.qiniu.com';          // 电信上传Host
    const UPLT_HOST = 'http://uplt.qiniu.com';          // 联通上传Host
    const UPYD_HOST = 'http://upyd.qiniu.com';          // 移动上传Host
    const UPBACKUP_HOST = 'http://upload.qiniu.com';    // 备用上传Host

    const BLOCK_SIZE = 4194304; //4*1024*1024 分块上传块大小，该参数为接口规格，暂不支持修改

    public static $defaultHost = self::UPAUTO_HOST;     // 设置为默认上传Host
}
