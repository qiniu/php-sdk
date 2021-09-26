<?php

namespace Qiniu\Storage;

use MyCLabs\Enum\Enum;

/**
 * 分片上传版本枚举类
 *
 * @link https://github.com/myclabs/php-enum
 */
final class SplitUploadVersion extends Enum
{
    const V1 = 'v1';
    const V2 = 'v2';
}