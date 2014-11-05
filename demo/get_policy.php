<?php
require_once('../qiniu/rs.php');
require_once('../qiniu/auth_digest.php');

$gpy = new Qiniu_RS_GetPolicy();

$url = 'http://sslayer.qiniudn.com/dive-into-golang.pptx';
echo $gpy->MakeRequest($url, null);

echo "\n";
$url = 'http://sslayer.qiniudn.com/dive-into-golang.pptx?odconv/pdf';
echo $gpy->MakeRequest($url, null);

echo "\n";
$url = 'http://sslayer.qiniug.com/2.m3u8';
echo $gpy->MakeRequest($url, null);
