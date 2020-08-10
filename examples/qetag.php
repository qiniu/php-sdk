<?php
require_once __DIR__ . '/../autoload.php';
use Qiniu\Etag;

// 计算文件的 ETag
// 参考文档：https://developer.qiniu.com/kodo/manual/1231/appendix#3

$localFile = "./php-logo.png";
list($etag, $err) = Etag::sum($localFile);
if ($err == null) {
    echo "Etag: $etag";
} else {
    var_dump($err);
}
