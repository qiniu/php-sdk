<?php
/**
 * Created by IntelliJ IDEA.
 * User: wf
 * Date: 2017/4/13
 * Time: PM9:47
 */

require_once("http.php");

// ----------------------------------------------------------
function Qiniu_CDN_Refresh($self, $urls, $dirs = null) // => ($statRet, $error)
{
    global $QINIU_CDN_HOST;
    $uri = "/v2/tune/refresh";

    $params = json_encode(array("urls" => $urls, "dirs" => $dirs));
    return Qiniu_Client_CallWithForm($self, $QINIU_CDN_HOST . $uri, $params, 'application/json');
}

