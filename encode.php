<?php
require_once('qiniu/utils.php');
require_once('qiniu/auth_digest.php');


function makeSaveasUrl($url, $bucket, $key)
{

    $entry = Qiniu_Encode("$bucket:$key");
    $url .= '|saveas/' . $entry;

    $urlPased = parse_url($url);

    $mac = Qiniu_RequireMac();
    $data = $urlPased['host'] . $urlPased['path'] . '?' . $urlPased['query'];
    $sign = $mac->Sign($data);

    return $url + '/sign/' . $sign;
}

$url = 'http://auditlog.qiniudn.com/1.jpg?imageMogr2/auto-orient/rotate/45';
$bucket = 'auditlog';
$key = 'rotate.jpg';

echo makeSaveasUrl($url, $bucket, $key);
