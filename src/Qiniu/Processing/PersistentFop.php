<?php
namespace Qiniu\Processing;

use Qiniu\Config;
use Qiniu\Http\Client;
use Qiniu\Http\Error;

final class PersistentFop
{
    private $auth;
            private $bucket;
        private $pipeline;
        private $notify_url;

    public function __construct($auth, $bucket, $pipeline = null, $notify_url = null)
    {
        $this->auth = $auth;
        $this->bucket = $bucket;
        $this->pipeline = $pipeline;
        $this->notify_url = $notify_url;
    }

    public function execute($key, array $fops, $force = false)
    {
        $ops = implode(';', $fops);
        $params = array('bucket' => $this->bucket, 'key' => $key, 'fops' => $ops);
        if (!empty($this->pipeline)) {
            $params['pipeline'] = $this->pipeline;
        }
        if (!empty($this->notify_url)){
            $params['notifyURL'] = $this->notify_url;
        }
        if ($force) {
            $params['force'] = 1;
        }
        $data = http_build_query($params);
        $url = Config::API_HOST . '/pfop/';
        $headers = $this->auth->authorization($url, $data, 'application/x-www-form-urlencoded');
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $response = Client::post($url, $data, $headers);
        if ($response->statusCode != 200) {
            return array(null, new Error($url, $response));
        }
        $r = $response->json();
        $id = $r['persistentId'];
        return array($id, null);
    }

    public static function status($id)
    {
        $url = Config::API_HOST . "/status/get/prefop?id=$id";
        $response = Client::get($url);
        if ($response->statusCode != 200) {
            return array(null, new Error($url, $response));
        }
        return array($response->json(), null);
    }
}
