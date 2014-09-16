<?php

require_once("../qiniu/auth_digest.php");
require("../qiniu/http.php");

$url = 'https://10fd05306325.a.passageway.io';
$u = array('path' => $url);
$req = new Qiniu_Request($u, 'name=123.txt&hash=FkU6TEApsSV89zqQ4_Lr9IWCsdP2&size=7&key=201409030000019228_doc_1410847521821&catalog_type=P');


$mac = Qiniu_RequireMac(null);

echo $mac->SignRequest($req, true);
