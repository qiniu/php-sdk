<?php
namespace Qiniu\Processing;

use Qiniu\Config;
use Qiniu\Http\Client;
use Qiniu\Http\Error;
use Qiniu\Processing\Operation;

/**
 * 持久化处理类,该类用于主动触发异步持久化操作.
 *
 * @link http://developer.qiniu.com/docs/v6/api/reference/fop/pfop/pfop.html
 */
final class PersistentFop
{
    /**
     * @var 账号管理密钥对，Auth对象
     */
    private $auth;

    /**
     * @var 操作资源所在空间
     */
    private $bucket;

    /**
     * @var 多媒体处理队列，详见 https://portal.qiniu.com/mps/pipeline
     */
    private $pipeline;

    /**
     * @var 持久化处理结果通知URL
     */
    private $notify_url;

    public function __construct($auth, $bucket, $pipeline = null, $notify_url = null, $force = false)
    {
        $this->auth = $auth;
        $this->bucket = $bucket;
        $this->pipeline = $pipeline;
        $this->notify_url = $notify_url;
        $this->force = $force;
    }

    /**
     * 列取空间的文件列表
     *
     * @param $key     待处理的源文件
     * @param $fops    处理详细操作，规格详见 http://developer.qiniu.com/docs/v6/api/reference/fop/
     * 
     * @return array[] 返回持久化处理的persistentId，类似{"persistentId": 5476bedf7823de4068253bae}
     * 
     * @link  http://developer.qiniu.com/docs/v6/api/reference/rs/list.html
     */
    public function execute($key, array $fops)
    {
        $ops = implode(';', $fops);
        $params = array('bucket' => $this->bucket, 'key' => $key, 'fops' => $ops);
        if (!empty($this->pipeline)) {
            $params['pipeline'] = $this->pipeline;
        }
        if (!empty($this->notify_url)) {
            $params['notifyURL'] = $this->notify_url;
        }
        if ($this->force) {
            $params['force'] = 1;
        }
        $data = http_build_query($params);
        $url = Config::API_HOST . '/pfop/';
        $headers = $this->auth->authorization($url, $data, 'application/x-www-form-urlencoded');
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $response = Client::post($url, $data, $headers);
        if (!$response->ok()) {
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
        if (!$response->ok()) {
            return array(null, new Error($url, $response));
        }
        return array($response->json(), null);
    }

    public function __call($method, $args)
    {
        $key = $args[0];
        $cmd = $method;
        $mod = null;
        if (count($args)>1) {
            $mod = $args[1];
        }

        $options = array();
        if (count($args)>2) {
            $options = $args[2];
        }

        $target_bucket = null;
        if (count($args)>3) {
            $target_bucket = $args[3];
        }

        $target_key = null;
        if (count($args)>4) {
            $target_key = $args[4];
        }

        $pfop = Operation::buildOp($cmd, $mod, $options);
        if ($target_bucket != null) {
            $pfop = Operation::saveas($pfop, $target_bucket, $target_key);
        }

        $ops = array();
        array_push($ops, $pfop);
        return $this->execute($key, $ops);
    }

    public function mkzip(
        $dummy_key,
        $urls_and_alias,
        $to_bucket = null,
        $to_key = null,
        $mode = 2
    ) {
        $base = 'mkzip/' . $mode;
        $op = array($base);
        foreach ($urls_and_alias as $key => $value) {
            if (is_int($key)) {
                array_push($op, 'url/' . \Qiniu\base64_urlSafeEncode($value));
            } else {
                array_push($op, 'url/' . \Qiniu\base64_urlSafeEncode($key));
                array_push($op, 'alias/' . \Qiniu\base64_urlSafeEncode($key));
            }
        }
        $fop = implode('/', $op);
        if ($to_bucket != null) {
            $op = Operation::saveas($fop, $to_bucket, $to_key);
        }
        $ops =array($op);
        return $this->execute($dummy_key, $ops);
    }
}
