<?php
require_once("http.php");

function Qiniu_CDN_Refresh($self, $urls, $dirs = array())
{
    global $QINIU_CDN_REFRESH_HOST;
    $url = $QINIU_CDN_REFRESH_HOST . '/refresh/';

    $params = 'urls=' . Qiniu_Http_Build_Query($urls, '&urls=');

    if (!empty($dirs)) {
        $params .= '&dirs=' . Qiniu_Http_Build_Query($dirs, '&urls=');
    }

    return Qiniu_Client_CallWithForm($self, $url, $params);
}

function Qiniu_Http_Build_Query($query, $sep)
{
    array_walk($query, function(&$val, $key){
        $val = urlencode($val);
    });
    return implode($query, $sep);
}
