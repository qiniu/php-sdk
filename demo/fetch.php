<?php

require_once('../qiniu/rs.php');
require_once('../qiniu/conf.php');

$client = new Qiniu_MacHttpClient(null);
$ret = Qiniu_RS_Fetch($client, 'http://upload.wikimedia.org/wikipedia/commons/b/b0/NewTux.svg', 'rwxf', 'qiniu.svg');


var_dump($ret);


