<?php
namespace Qiniu\Storage;

use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Http\Client;
use Qiniu\Http\Error;

final class BucketManager
{
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function buckets()
    {
        return $this->rsget('/buckets');
    }

    public function listFiles($bucket, $prefix = null, $marker = null, $limit = 1000, $delimiter = null)
    {
        $query = array('bucket' => $bucket);
        if (!empty($prefix)) {
            $query['prefix'] = $prefix;
        }
        if (!empty($marker)) {
            $query['marker'] = $marker;
        }
        if (!empty($limit)) {
            $query['limit'] = $limit;
        }
        if (!empty($delimiter)) {
            $query['delimiter'] = $delimiter;
        }
        $url = Config::RSF_HOST . '/list?' . http_build_query($query);
        list($ret, $error) = $this->get($url);
        if ($ret == null) {
            return array(null, null, $error);
        }
        $marker = isset($ret['marker']) ? $ret['marker'] : null;
        return array($ret['items'], $marker, null);
    }

    public function stat($bucket, $key)
    {
        $path = '/stat/' . \Qiniu\entry($bucket, $key);
        return $this->rsGet($path);
    }

    public function delete($bucket, $key)
    {
        $path = '/delete/' . \Qiniu\entry($bucket, $key);
        list($_, $error) = $this->rsPost($path);
        return $error;
    }

    public function rename($bucket, $oldname, $newname)
    {
        return $this->move($bucket, $oldname, $bucket, $newname);
    }

    public function copy($from_bucket, $from_key, $to_bucket, $to_key)
    {
        $from = \Qiniu\entry($from_bucket, $from_key);
        $to = \Qiniu\entry($to_bucket, $to_key);
        $path = '/copy/' . $from . '/' . $to;
        list($_, $error) = $this->rsPost($path);
        return $error;
    }

    public function move($from_bucket, $from_key, $to_bucket, $to_key)
    {
        $from = \Qiniu\entry($from_bucket, $from_key);
        $to = \Qiniu\entry($to_bucket, $to_key);
        $path = '/move/' . $from . '/' . $to;
        list($_, $error) = $this->rsPost($path);
        return $error;
    }

    public function changeMime($bucket, $key, $mime)
    {
        $resource = \Qiniu\entry($bucket, $key);
        $encode_mime = \Qiniu\base64_urlSafeEncode($mime);
        $path = '/chgm/' . $resource . '/mime/' .$encode_mime;
        list($_, $error) = $this->rsPost($path);
        return $error;
    }

    public function fetch($url, $bucket, $key)
    {
        $resource = \Qiniu\base64_urlSafeEncode($url);
        $to = \Qiniu\entry($bucket, $key);
        $path = '/fetch/' . $resource . '/to/' . $to;
        list($_, $error) = $this->ioPost($path);
        return $error;
    }

    public function prefetch($bucket, $key)
    {
        $resource = \Qiniu\entry($bucket, $key);
        $path = '/prefetch/' . $resource;
        list($_, $error) = $this->ioPost($path);
        return $error;
    }

    public function batch($operations)
    {
        $params = 'op=' . implode('&op=', $operations);
        return $this->rsPost('/batch', $params);
    }

    private function rsPost($path, $body = null)
    {
        $url = Config::RS_HOST . $path;
        return $this->post($url, $body);
    }

    private function rsGet($path)
    {
        $url = Config::RS_HOST . $path;
        return $this->get($url);
    }

    private function ioPost($path, $body = null)
    {
        $url = Config::IO_HOST . $path;
        return $this->post($url, $body);
    }

    private function get($url)
    {
        $headers = $this->auth->authorization($url);
        $ret = Client::get($url, $headers);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        return array($ret->json(), null);
    }

    private function post($url, $body)
    {
        $headers = $this->auth->authorization($url, $body, 'application/x-www-form-urlencoded');
        $ret = Client::post($url, $body, $headers);
        if (!$ret->ok()) {
            return array(null, new Error($url, $ret));
        }
        $r = $ret->body == null ? array() : $ret->json();
        return array($r, null);
    }

    public static function buildBatchCopy($source_bucket, $key_pairs, $target_bucket)
    {
        return self::twoKeyBatch('copy', $source_bucket, $key_pairs, $target_bucket);
    }


    public static function buildBatchRename($bucket, $key_pairs)
    {
        return self::buildBatchMove($bucket, $key_pairs, $bucket);
    }


    public static function buildBatchMove($source_bucket, $key_pairs, $target_bucket)
    {
        return self::twoKeyBatch('move', $source_bucket, $key_pairs, $target_bucket);
    }


    public static function buildBatchDelete($bucket, $keys)
    {
        return self::oneKeyBatch('delete', $bucket, $keys);
    }


    public static function buildBatchStat($bucket, $keys)
    {
        return self::oneKeyBatch('stat', $bucket, $keys);
    }

    private static function oneKeyBatch($operation, $bucket, $keys)
    {
        $data = array();
        foreach ($keys as $key) {
            array_push($data, $operation . '/' . \Qiniu\entry($bucket, $key));
        }
        return $data;
    }

    private static function twoKeyBatch($operation, $source_bucket, $key_pairs, $target_bucket)
    {
        if ($target_bucket == null) {
            $target_bucket = $source_bucket;
        }
        $data = array();
        foreach ($key_pairs as $from_key => $to_key) {
            $from = \Qiniu\entry($source_bucket, $from_key);
            $to = \Qiniu\entry($target_bucket, $to_key);
            array_push($data, $operation . '/' . $from . '/' . $to);
        }
        return $data;
    }
}
