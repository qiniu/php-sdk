<?php
namespace Qiniu;

use Qiniu\Http\Client;
use Qiniu\Http\Error;

final class Zone
{
    public $ioHost;            // 七牛源站Host
    public $upHost;
    public $upHostBackup;

    public $hostCache; //<scheme>:<ak>:<bucket> ==> array('deadline' => 'xxx', 'upHosts' => array(), 'ioHost' => 'xxx.com')
    public $scheme = 'http';

    public function __construct($scheme = null)
    {
        $this->hostCache = array();
        if ($scheme != null) {
            $this->scheme = $scheme;
        }
    }

    public function getUpHostByToken($uptoken)
    {
        list($ak, $bucket) = $this->unmarshalUpToken($uptoken);
        list($upHosts,) = $this->getUpHosts($ak, $bucket);
        return $upHosts[0];
    }

    public function getBackupUpHostByToken($uptoken)
    {
        list($ak, $bucket) = $this->unmarshalUpToken($uptoken);
        list($upHosts,) = $this->getUpHosts($ak, $bucket);
        return $upHosts[1];
    }

    public function getIoHost($ak, $bucket)
    {
        list($bucketHosts, ) = $this->getBucketHosts($ak, $bucket);
        return $bucketHosts['ioHost'][0];
    }

    public function getUpHosts($ak, $bucket)
    {
        list($bucketHosts, $err) = $this->getBucketHosts($ak, $bucket);
        if ($err !== null) {
            return array(null, $err);
        }

        $upHosts = $bucketHosts['upHosts'];
        return array($upHosts, null);
    }

    private function unmarshalUpToken($uptoken)
    {
        $token = split(':', $uptoken);
        if (count($token) !== 3) {
            throw new \Exception("Invalid Uptoken", 1);
        }

        $ak = $token[0];
        $policy = base64_urlSafeDecode($token[2]);
        $policy = json_decode($policy, true);

        $bucket = $policy['scope'];
        if (strpos($bucket, ':')) {
            $bucket = split(':', $bucket)[0];
        }
         
        return array($ak, $bucket);
    }

    public function getBucketHosts($ak, $bucket)
    {
        $key = $ak . $bucket;

        $exist = false;
        if (count($this->hostCache) > 0) {
            $exist = array_key_exists($key, $this->hostCache) && $this->hostCache[$key]['deadline'] > time();
        }

        if ($exist) {
            return $this->hostCache[$key];
        }

        list($hosts, $err) = $this->bucketHosts($ak, $bucket);
        if ($err !== null) {
            return array(null , $err);
        }

        $schemeHosts = $hosts[$this->scheme];
        $bucketHosts = array('upHosts' => $schemeHosts['up'], 'ioHost' => $schemeHosts['io'], 'deadline' => time() + $hosts['ttl']);

        $this->hostCache[$key] = $bucketHosts;
        return array($bucketHosts, null);
    }


    /*  请求包：
     *   GET /v1/query?ak=<ak>&&bucket=<bucket>
     *  返回包：
     *  
     *  200 OK {
     *    "ttl": <ttl>,              // 有效时间
     *    "http": {
     *      "up": [],
     *      "io": [],                // 当bucket为global时，我们不需要iohost, io缺省
     *    },
     *    "https": {
     *      "up": [],
     *      "io": [],                // 当bucket为global时，我们不需要iohost, io缺省
     *    }
     *  }
     **/
    private function bucketHosts($ak, $bucket)
    {
        $url = Config::UC_HOST . '/v1/query' . "?ak=$ak&bucket=$bucket";
        $ret = Client::Get($url);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        $r = ($ret->body === null) ? array() : $ret->json();
        return array($r, null);
    }
}
