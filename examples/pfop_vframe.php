<?php
require_once __DIR__ . '/../autoload.php';

use Qiniu\Auth;
use Qiniu\Processing\PersistentFop;

// 控制台获取密钥：https://portal.qiniu.com/user/key
$accessKey = getenv('QINIU_ACCESS_KEY');
$secretKey = getenv('QINIU_SECRET_KEY');
$auth = new Auth($accessKey, $secretKey);

// 对已经上传到七牛的视频发起异步转码操作
// 视频帧缩略图参考文档：https://developer.qiniu.com/dora/api/1313/video-frame-thumbnails-vframe

// 要转码的文件所在的空间和文件名
$bucket = getenv('QINIU_TEST_BUCKET');
$key = 'qiniu.mp4';

// 用户默认没有私有队列，需要在这里创建然后填写 https://portal.qiniu.com/dora/media-gate/pipeline
$pipeline = 'sdktest';

// 转码完成后通知到你的业务服务器（需要可以公网访问，并能够相应 200 OK）
$notifyUrl = 'http://375dec79.ngrok.com/notify.php';

// 当转码后的文件名与源文件名相同时，是否覆盖源文件
$force = false;

$config = new \Qiniu\Config();
$config->useHTTPS = true;
$pfop = new PersistentFop($auth, $config);

// 视频处理完毕后保存到空间中的名称
$saveasKey = 'qiniu_480x360.jpg';

// 进行视频截帧操作
$fops = "vframe/jpg/offset/1/w/480/h/360/rotate/90|saveas/" .
    \Qiniu\base64_urlSafeEncode("$bucket:$saveasKey");

list($id, $err) = $pfop->execute($bucket, $key, $fops, $pipeline, $notifyUrl, $force);
echo "\n====> pfop avthumb result: \n";
if ($err != null) {
    var_dump($err);
} else {
    echo "PersistentFop Id: $id\n";
}

// 查询转码的进度和状态
list($ret, $err) = $pfop->status($id);
echo "\n====> pfop avthumb status: \n";
if ($err != null) {
    var_dump($err);
} else {
    var_dump($ret);
}
