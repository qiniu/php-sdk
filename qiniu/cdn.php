<?php
require_once("http.php");

function Qiniu_CDN_Refresh($self, $urls, $dirs = array())
{
    global $QINIU_CDN_REFRESH_HOST;
    $url = $QINIU_CDN_REFRESH_HOST . '/refresh/';
    $params = 'urls=' . implode('&urls=', $urls);

    if (!empty($dirs)) {
        $params .= '&dirs=' . implode('&dirs=', $dirs);
    }

    return Qiniu_Client_CallWithForm($self, $url, $params);
}
