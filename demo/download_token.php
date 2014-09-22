<?php
require_once('../qiniu/utils.php');
require_once('../qiniu/auth_digest.php');

function dnToken($url, $expires = 3600)
{

    $e = 'e=' . (time() + $expires);

    $qry = parse_url($url, PHP_URL_QUERY);
    if ($qry) {
        $url .= '&' . $e;
    } else {
        $url .= '?' . $e;
    }

    $sign = Qiniu_Sign(null, $url);

    return $url . '&token=' . $sign;
}

$url = 'http://sslayer.qiniudn.com/dive-into-golang.pptx';
echo dnToken($url);

echo "\n";
$url = 'http://sslayer.qiniudn.com/dive-into-golang.pptx?odconv/pdf';
echo dnToken($url);
