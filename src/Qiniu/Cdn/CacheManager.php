<?php
namespace Qiniu\Cdn;

use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Http\Client;
use Qiniu\Http\Error;

/**
 * 主要涉及了CDN 缓存的刷新和预取
 *
 * @link http://developer.qiniu.com/article/fusion/api/refresh.html
 * @link http://developer.qiniu.com/article/fusion/api/prefetch.html
 */
final class CacheManager
{
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function refresh($urls, array $dirs = null)
    {

    	$body = array();
    	if (!empty($urls)) {
    		$body['urls'] = $urls;
    	}
    	if (!empty($dirs)) {
    		$body['dirs'] = $dirs;
    	}

        $body = json_encode($body);

    	return $this->post(Config::FUSION_HOST . '/v2/tune/refresh', $body);
    }

    private function post($url, $body)
    {
        $headers = $this->auth->authorization($url, $body, 'application/json');
        $headers['Content-Type'] = 'application/json';
        $ret = Client::post($url, $body, $headers);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        $r = ($ret->body === null) ? array() : $ret->json();
        return array($r, null);
    }
}
