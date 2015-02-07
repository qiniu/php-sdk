<?php
namespace Qiniu\Processing;

use Qiniu\Http\Client;
use Qiniu\Http\Error;

final class Operation
{
    private $auth;
    private $token_expire;
    private $domain;
    public static function buildOp($cmd, $mode = null, array $args = array())
    {
        $op = array($cmd);
        if ($mode !== null) {
            array_push($op, $mode);
        }
        foreach ($args as $key => $value) {
            array_push($op, "$key/$value");
        }
        return implode('/', $op);
    }

    public static function pipeCmd($cmds)
    {
        return implode('|', $cmds);
    }

    public static function saveas($op, $bucket, $key)
    {
        return self::pipeCmd(array($op, 'saveas/' . \Qiniu\entry($bucket, $key)));
    }

    public function __construct($domain, $auth = null, $token_expire = 3600)
    {
        $this->auth = $auth;
        $this->domain = $domain;
        $this->token_expire = $token_expire;
    }

    public function buildUrl($key, $cmd, $mod = null, array $args = array())
    {
        $fop = self::buildOp($cmd, $mod, $args);
        $baseUrl = "http://$this->domain/$key?$fop";
        $url = $baseUrl;
        if ($this->auth != null) {
            $url = $this->auth->privateDownloadUrl($baseUrl, $this->token_expire);
        }
        return $url;
    }

    public function __call($method, $args)
    {
        $key = $args[0];
        $cmd = $method;
        $mode = null;
        if (count($args)>1) {
            $mode = $args[1];
        }

        if (count($args)>2) {
            $options = $args[2];
        }
        $options = array();
        $url = $this->buildUrl($key, $cmd, $mode, $options);
        $r = Client::get($url);
        if (!$r->ok()) {
            return array(null, new Error($url, $r));
        }
        if ($r->json() != null) {
            return array($r->json(), null);
        }
        return array($r->body, null);
    }
}
