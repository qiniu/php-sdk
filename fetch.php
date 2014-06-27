<?php

require_once('qiniu/rs.php');
require_once('qiniu/conf.php');

$client = new Qiniu_MacHttpClient(null);
$ret = Qiniu_RS_Fetch($client, 'http://img0.bdstatic.com/img/image/shouye/mxjxx-11768363916.jpg', 'alibaba', 'wf.jpg');

var_dump($ret);

