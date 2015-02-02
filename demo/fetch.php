<?php

require_once('../qiniu/rs.php');
require_once('../qiniu/conf.php');

$client = new Qiniu_MacHttpClient(null);
$ret = Qiniu_RS_Fetch($client, 'http://rwxf.qiniucdn.com/1.jpg', 'rwxf', 'qiniu.jpg');

var_dump($ret);


