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
        if ($scheme != null) 
        {
            $this->scheme = $scheme;
        }
    }

    public function getUpHostByToken($uptoken)
    {
        list($ak, $bucket) = $this->unmarshalUpToken($uptoken);
        $upHosts = $this->getUpHosts($ak, $bucket);
        return $upHosts[0];
    }

    public function getBackupUpHostByToken($uptoken)
    {
        list($ak, $bucket) = $this->unmarshalUpToken($uptoken);
        $upHosts = $this->getUpHosts($ak, $bucket);
        return $upHosts[1];
    }

    public function getUpHosts($ak, $bucket)
    {
        $bucketHosts = $this->getBucketHosts($ak, $bucket);
        $upHosts = $bucketHosts['upHosts'];
        return $upHosts;
    }

    public function getBucketHostsByUpToken($uptoken) 
    {
        list($ak, $bucket) = $this->unmarshalUpToken($uptoken);
        return $this->getBucketHosts($ak, $bucket);
    }

    private function unmarshalUpToken($uptoken)
    {
        $token = split(':', $uptoken);
        if (count($token) !== 3)
        {
            throw new \Exception("Invalid Uptoken", 1);
        }

        $ak = $token[0];
        $policy = base64_urlSafeDecode($token[2]);
        $policy = json_decode($policy, true);

        list($bucket, $_) = split(':', $policy['scope']);
         
        return array($ak, $bucket);
    }

    public function getBucketHosts($ak, $bucket)
    {
        $key = $ak . $bucket;

        $exist = false;
        if (count($this->hostCache) > 0) 
        {
            $exist = array_key_exists($key, $this->hostCache) && $this->hostCache[$key]['deadline'] > time();
        }

        if ($exist) 
        {
            return $this->hostCache[$key];
        }

        list($hosts, $_) = $this->bucketHosts($ak, $bucket);

        var_dump($hosts);
        $schemeHosts = $hosts[$this->scheme];
        $bucketHosts = array('upHosts' => $schemeHosts['up'], 'ioHost' => $schemeHosts['io'], 'deadline' => time() + $hosts['ttl']);

        $this->hostCache[$key] = $bucketHosts;
        return $bucketHosts;
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
        $path = '/v1/query' . "?ak=$ak&bucket=$bucket";
        $ret = Client::Get(Config::UC_HOST . $path);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        $r = ($ret->body === null) ? array() : $ret->json();
        return array($r, null);
    }
}
