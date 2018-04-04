<?php

require "../autoload.php";

$cli = new \Qiniu\Http\Client();

$url = 'https://acc.qbox.me/oauth2/token';
$param = array(
        'grant_type' => 'password',
        'username' => "ts@qiniu.com",
        'password' => urlencode('xxxx'),
);
$param = http_build_query($param);
$headers = array(
    'Content-Type' => 'application/x-www-form-urlencoded'
);

//https://developer.qiniu.com/af/manual/1600/get-account-management-credentials-and-secret-lock
$resp = $cli::post($url, $param, $headers);

$res = json_decode($resp->body, true);

echo '----------------------------token:\n';
var_dump($res);


$res2 = createChildAccount("xxxx@rwifeng.com", 'xxxxxtest', $res['access_token']);

echo '----------------------------create child account:\n';
var_dump($res2);

//https://developer.qiniu.com/af/manual/1534/create-a-account-user-create-child
function createChildAccount($email, $pwd, $aToken) {
    $headers = array(
        'Authorization' => 'Bearer ' . $aToken
    );
    $param = 'email=' . $email . '&password=' . $pwd;

    $cli = new \Qiniu\Http\Client();
    $resp = $cli::post('https://acc.qbox.me/user/create_child', $param, $headers);

    return $resp->body;
}
