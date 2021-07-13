<?php
namespace Qiniu\Http;

final class Request
{
    public $url;
    public $headers;
    public $body;
    public $method;

    public $options = array(
        'timeout' => 60
    );

    public function __construct($method, $url, array $headers = array(), $body = null, $options = array())
    {
        $this->method = strtoupper($method);
        $this->url = $url;
        $this->headers = $headers;
        $this->body = $body;
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }
}
