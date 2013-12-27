<?php

require_once("http.php");


function Qiniu_CDN_Refresh($self, $urls, $dirs)
{
    global $QINIU_CDN_REFRESH_HOST;
    $url = $QINIU_CDN_REFRESH_HOST . '/cdn/refresh';
    $params = 'urls=' . implode('&urls=', $urls);
    $params .= '&dirs=' . implode('&dirs=', $dirs)

    return Qiniu_Client_CallWithForm($self, $url, $params);
}
