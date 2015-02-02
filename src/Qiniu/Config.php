<?php
namespace Qiniu;

final class Config
{
    const SDK_VER = '7.0.0';

    const IO_HOST  = 'http://iovip.qbox.me';
    const RS_HOST  = 'http://rs.qbox.me';
    const RSF_HOST = 'http://rsf.qbox.me';
    const API_HOST = 'http://api.qiniu.com';

    const UPAUTO_HOST = 'http://up.qiniu.com';
    const UPDX_HOST = 'http://updx.qiniu.com';
    const UPLT_HOST = 'http://uplt.qiniu.com';
    const UPYD_HOST = 'http://upyd.qiniu.com';
    const UPBACKUP_HOST = 'http://upload.qiniu.com';

    const BLOCK_SIZE = 4194304; # 4*1024*1024

    public static $defaultHost = self::UPAUTO_HOST;
}
