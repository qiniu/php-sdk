<?php
namespace Qiniu;

use Qiniu\Http\Header;
use Qiniu\Zone;

final class Auth
{
    private $accessKey;
    private $secretKey;

    public function __construct($accessKey, $secretKey)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    public function getAccessKey()
    {
        return $this->accessKey;
    }

    public function sign($data)
    {
        $hmac = hash_hmac('sha1', $data, $this->secretKey, true);
        return $this->accessKey . ':' . \Qiniu\base64_urlSafeEncode($hmac);
    }

    public function signWithData($data)
    {
        $encodedData = \Qiniu\base64_urlSafeEncode($data);
        return $this->sign($encodedData) . ':' . $encodedData;
    }

    public function signRequest($urlString, $body, $contentType = null)
    {
        $url = parse_url($urlString);
        $data = '';
        if (array_key_exists('path', $url)) {
            $data = $url['path'];
        }
        if (array_key_exists('query', $url)) {
            $data .= '?' . $url['query'];
        }
        $data .= "\n";

        if ($body !== null && $contentType === 'application/x-www-form-urlencoded') {
            $data .= $body;
        }
        return $this->sign($data);
    }

    /**
     * @param string $urlString
     * @param string $method
     * @param string $body
     * @param null|Header $headers
     */
    public function signQiniuAuthorization($urlString, $method = "GET", $body = "", $headers = null)
    {
        $url = parse_url($urlString);
        if (!$url) {
            return array(null, new \Exception("parse_url error"));
        }

        // append method, path and query
        if ($method === "") {
            $data = "GET ";
        } else {
            $data = $method . " ";
        }
        if (isset($url["path"])) {
            $data .= $url["path"];
        }
        if (isset($url["query"])) {
            $data .= "?" . $url["query"];
        }

        // append Host
        $data .= "\n";
        $data .= "Host: ";
        if (isset($url["host"])) {
            $data .= $url["host"];
        }
        if (isset($url["port"]) && $url["port"] > 0) {
            $data .= ":" . $url["port"];
        }

        // try append content type
        if ($headers != null && isset($headers["Content-Type"])) {
            // append content type
            $data .= "\n";
            $data .= "Content-Type: " . $headers["Content-Type"];
        }

        // try append xQiniuHeaders
        if ($headers != null) {
            $headerLines = array();
            $keyPrefix = "X-Qiniu-";
            foreach ($headers as $k => $v) {
                if (strlen($k) > strlen($keyPrefix) && strpos($k, $keyPrefix) === 0) {
                    array_push(
                        $headerLines,
                        $k . ": " . $v
                    );
                }
            }
            if (count($headerLines) > 0) {
                $data .= "\n";
                sort($headerLines);
                $data .= implode("\n", $headerLines);
            }
        }

        // append body
        $data .= "\n\n";
        if (strlen($body) > 0
            && isset($headers["Content-Type"])
            && $headers["Content-Type"] != "application/octet-stream"
        ) {
            $data .= $body;
        }

        return array($this->sign(utf8_encode($data)), null);
    }

    public function verifyCallback($contentType, $originAuthorization, $url, $body)
    {
        $authorization = 'QBox ' . $this->signRequest($url, $body, $contentType);
        return $originAuthorization === $authorization;
    }

    public function privateDownloadUrl($baseUrl, $expires = 3600)
    {
        $deadline = time() + $expires;

        $pos = strpos($baseUrl, '?');
        if ($pos !== false) {
            $baseUrl .= '&e=';
        } else {
            $baseUrl .= '?e=';
        }
        $baseUrl .= $deadline;

        $token = $this->sign($baseUrl);
        return "$baseUrl&token=$token";
    }

    public function uploadToken($bucket, $key = null, $expires = 3600, $policy = null, $strictPolicy = true)
    {
        $deadline = time() + $expires;
        $scope = $bucket;
        if ($key !== null) {
            $scope .= ':' . $key;
        }

        $args = self::copyPolicy($args, $policy, $strictPolicy);
        $args['scope'] = $scope;
        $args['deadline'] = $deadline;

        $b = json_encode($args);
        return $this->signWithData($b);
    }

    /**
     *上传策略，参数规格详见
     *http://developer.qiniu.com/docs/v6/api/reference/security/put-policy.html
     */
    private static $policyFields = array(
        'callbackUrl',
        'callbackBody',
        'callbackHost',
        'callbackBodyType',
        'callbackFetchKey',

        'returnUrl',
        'returnBody',

        'endUser',
        'saveKey',
        'forceSaveKey',
        'insertOnly',

        'detectMime',
        'mimeLimit',
        'fsizeMin',
        'fsizeLimit',

        'persistentOps',
        'persistentNotifyUrl',
        'persistentPipeline',

        'deleteAfterDays',
        'fileType',
        'isPrefixalScope',
    );

    private static function copyPolicy(&$policy, $originPolicy, $strictPolicy)
    {
        if ($originPolicy === null) {
            return array();
        }
        foreach ($originPolicy as $key => $value) {
            if (!$strictPolicy || in_array((string)$key, self::$policyFields, true)) {
                $policy[$key] = $value;
            }
        }
        return $policy;
    }

    public function authorization($url, $body = null, $contentType = null)
    {
        $authorization = 'QBox ' . $this->signRequest($url, $body, $contentType);
        return array('Authorization' => $authorization);
    }

    public function authorizationV2($url, $method, $body = null, $contentType = null)
    {
        $headers = null;
        $result = array();
        if ($contentType != null) {
            $headers = new Header(array(
                'Content-Type' => array($contentType),
            ));
            $result['Content-Type'] = $contentType;
        }
        list($sign) = $this->signQiniuAuthorization($url, $method, $body, $headers);
        $result['Authorization'] = 'Qiniu ' . $sign;
        return $result;
    }
}
