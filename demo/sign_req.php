<?php
require_once('../qiniu/auth_digest.php');
require('../qiniu/http.php');

$url = 'https://10fd05306325.a.passageway.io/chgm/aXRpc2F0ZXN0OmdvZ29waGVyLmpwZw==/mime/YXBwbGljYXRpb24vdGVzdA==';
$u = array('path' => $url);
$req = new Qiniu_Request($u, '');
$mac = Qiniu_RequireMac(null);

echo $mac->SignRequest($req, true);
