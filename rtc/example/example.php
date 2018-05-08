<?php

require join(DIRECTORY_SEPARATOR, array(dirname(dirname(__FILE__)), 'src', 'Rtc_v3.php'));
require_once("../../autoload.php");

$ak = 'gwd_gV4gPKZZsmEOvAuNU1AcumicmuHooTfu64q5';
$sk = 'xxxx';

$mac = new QiniuRtc\Mac($ak, $sk);
$client = new QiniuRtc\AppClient($mac);
$hub = 'lfxlive';
$title = 'lfxl';
try {
    //创建app
    $resp = $client->createApp($hub, $title, $maxUsers);
    print_r($resp);exit;
    // 获取app状态
    $resp = $client->getApp('dgbg9ym9p');
    print_r($resp);exit;
    //修改app状态
    $mergePublishRtmp = null;
    $mergePublishRtmp['enable'] = true;
    $resp = $client->UpdateApp('dgbgljsxp', $hub, $title, $maxUsers, $mergePublishRtmp);
    print_r($resp);exit;
    //删除app
    $resp = $client->deleteApp('dgbgljsxp');
    print_r($resp);exit;
    //获取房间连麦的成员
    $resp=$client->getappUserNum("dgbfvvzid", 'lfxl');
    print_r($resp);exit;
    //剔除房间的连麦成员
    $resp=$client->kickingPlayer("dgbfvvzid", 'lfx', "qiniu-f6e07b78-4dc8-45fb-a701-a9e158abb8e6");
    print_r($resp);exit;
    // 列举房间
    $resp=$client->listRooms("dgbfvvzid", 'lfx', null, null);
    print_r($resp);exit;
    //鉴权的有效时间: 1个小时.
    $resp = $client->appToken("dgbfvvzid", "lfxl", '1111', (time()+3600), 'user');
    print_r($resp);exit;
} catch (\Exception $e) {
    echo "Error:", $e, "\n";
}
