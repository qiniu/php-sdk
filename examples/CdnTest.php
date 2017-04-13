<?php
/**
 * Created by IntelliJ IDEA.
 * User: wf
 * Date: 2017/4/13
 * Time: PM10:16
 */


require_once("../qiniu/cdn_refresh.php");


$client = new Qiniu_MacHttpClient(null);
$urls = array("http://rwxf.qiniudn.com/1.mp4", "http://rwxf.qiniudn.com/1.txt");
$dirs = array("http://rwxf.qiniudn.com/");

list($ret, $err) =  Qiniu_CDN_Refresh($client, $urls, $dirs); // => ($statRet, $error)

var_dump($ret, $err);
