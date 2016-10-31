<?php
namespace Qiniu\Cdn;

use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Http\Client;
use Qiniu\Http\Error;

/**
 * 主要涉及了CDN 日志的列取
 *
 * @link http://developer.qiniu.com/article/fusion/api/log.html 
 */
final class LogManager
{
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    function list_logs($domains, $day)
    {

    	if (empty($day) || empty($domains)) {
            return;
    	}

        $domains = join(';', $domains);
        $body = array('day' => $day, 'domains' => $domains);
        $body = json_encode($body);

    	return $this->post(Config::FUSION_HOST . '/v2/tune/log/list', $body);
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
