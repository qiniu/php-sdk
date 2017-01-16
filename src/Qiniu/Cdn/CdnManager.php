<?php

namespace Qiniu\Cdn;

use Qiniu\Auth;
use Qiniu\Http\Error;
use Qiniu\Http\Client;

final class CdnManager
{

    private $auth;
    private $server;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        $this->server = 'http://fusion.qiniuapi.com';
    }

    public function refreshUrls($urls)
    {
        return $this->refreshUrlsAndDirs($urls, null);
    }

    public function refreshDirs($dirs)
    {
        return $this->refreshUrlsAndDirs(null, $dirs);
    }

    /**
     * @param array $urls 待刷新的文件链接数组
     *
     * @return array 刷新的请求回复和错误，参考 examples/cdn_manager.php 代码
     * @link http://developer.qiniu.com/article/fusion/api/refresh.html
     */
    public function refreshUrlsAndDirs($urls, $dirs)
    {
        $req = array();
        if (!empty($urls)) {
            $req['urls'] = $urls;
        }
        if (!empty($dirs)) {
            $req['dirs'] = $dirs;
        }

        $url = $this->server . '/v2/tune/refresh';
        $body = json_encode($req);
        return $this->post($url, $body);
    }

    /**
     * @param array $urls 待预取的文件链接数组
     *
     * @return array 预取的请求回复和错误，参考 examples/cdn_manager.php 代码
     *
     * @link http://developer.qiniu.com/article/fusion/api/refresh.html
     */
    public function prefetchUrls($urls)
    {
        $req = array(
            'urls' => $urls,
        );

        $url = $this->server . '/v2/tune/prefetch';
        $body = json_encode($req);
        return $this->post($url, $body);
    }

    /**
     * @param array $domains      待获取带宽数据的域名数组
     * @param string $startDate   开始的日期，格式类似 2017-01-01
     * @param string $endDate     结束的日期，格式类似 2017-01-01
     * @param string $granularity 获取数据的时间间隔，可以是 5min, hour 或者 day
     *
     * @return array 带宽数据和错误信息，参考 examples/cdn_manager.php 代码
     *
     * @link http://developer.qiniu.com/article/fusion/api/traffic-bandwidth.html
     */
    public function getBandwidthData($domains, $startDate, $endDate, $granularity)
    {
        $req = array();
        $req['domains'] = implode(';', $domains);
        $req['startDate'] = $startDate;
        $req['endDate'] = $endDate;
        $req['granularity'] = $granularity;

        $url = $this->server . '/v2/tune/bandwidth';
        $body = json_encode($req);
        return $this->post($url, $body);
    }

    /**
     * @param array  $domains     待获取流量数据的域名数组
     * @param string $startDate   开始的日期，格式类似 2017-01-01
     * @param string $endDate     结束的日期，格式类似 2017-01-01
     * @param string $granularity 获取数据的时间间隔，可以是 5min, hour 或者 day
     *
     * @return array 流量数据和错误信息，参考 examples/cdn_manager.php 代码
     *
     * @link http://developer.qiniu.com/article/fusion/api/traffic-bandwidth.html
     */
    public function getFluxData($domains, $startDate, $endDate, $granularity)
    {
        $req = array();
        $req['domains'] = implode(';', $domains);
        $req['startDate'] = $startDate;
        $req['endDate'] = $endDate;
        $req['granularity'] = $granularity;

        $url = $this->server . '/v2/tune/flux';
        $body = json_encode($req);
        return $this->post($url, $body);
    }

    /**
     * @param array  $domains 待获取日志下载链接的域名数组
     * @param string $logDate 获取指定日期的日志下载链接，格式类似 2017-01-01
     *
     * @return array 日志下载链接数据和错误信息，参考 examples/cdn_manager.php 代码
     *
     * @link http://developer.qiniu.com/article/fusion/api/log.html
     */
    public function getCdnLogList($domains, $logDate)
    {
        $req = array();
        $req['domains'] = implode(';', $domains);
        $req['day'] = $logDate;

        $url = $this->server . '/v2/tune/log/list';
        $body = json_encode($req);
        return $this->post($url, $body);
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

    /**
     * 构建时间戳防盗链鉴权的访问外链
     *
     * @param string $host             带访问协议的域名
     * @param string $fileName         原始文件名，不需要urlencode
     * @param string $queryStringArray 查询参数命名数组，不需要urlencode
     * @param string $encryptKey       时间戳防盗链密钥
     * @param string $deadline         链接有效期时间戳（以秒为单位）
     *
     * @return string 带鉴权信息的资源外链，参考 examples/cdn_manager.php 代码
     */
    public static function createTimestampAntiLeechUrl($host, $fileName, $queryStringArray, $encryptKey, $deadline)
    {
        $encodedFileName= str_replace("+", "%20", urlencode($fileName));
        if (!empty($queryStringArray)) {
            $queryStrings = array();
            foreach ($queryStringArray as $key => $value) {
                array_push($queryStrings, urlencode($key) . '=' . urlencode($value));
            }
            $queryString = implode('&', $queryStrings);
            $urlToSign = $host . '/' . $encodedFileName . '?' . $queryString;
        } else {
            $urlToSign = $host . '/' . $encodedFileName;
        }

        $path = '/' . $encodedFileName;
        $expireHex = dechex($deadline);

        $strToSign = $encryptKey . $path . $expireHex;
        $signStr = md5($strToSign);

        if (!empty($queryString)) {
            $signedUrl = $urlToSign . '&sign=' . $signStr . '&t=' . $expireHex;
        } else {
            $signedUrl = $urlToSign . '?sign=' . $signStr . '&t=' . $expireHex;
        }

        return $signedUrl;
    }
}
