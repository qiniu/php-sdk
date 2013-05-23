<?php
class Client 
{
	/**
	 * HTTP Methods
	 */
	const HTTP_METHOD_GET    = 'GET';
	const HTTP_METHOD_POST   = 'POST';
	const HTTP_METHOD_PUT    = 'PUT';
	const HTTP_METHOD_DELETE = 'DELETE';
	const HTTP_METHOD_HEAD   = 'HEAD';

	/**
	 * HTTP Form content types
	 */
	const HTTP_FORM_CONTENT_TYPE_APPLICATION = 0;
	const HTTP_FORM_CONTENT_TYPE_MULTIPART = 1;
	
	public $httpMethodPost = self::HTTP_METHOD_POST;
	public $httpFormContentTypeApplication = self::HTTP_FORM_CONTENT_TYPE_APPLICATION;
	public $httpFormContentTypeMultipart = self::HTTP_FORM_CONTENT_TYPE_MULTIPART;
	
	public $header;
	public $host;
	
	
	public function __construct($host)
	{	
		$this->host = $host;
	}
	
	public function setHeader($key, $val)
	{
		$this->header[$key] = $val;
	}

	public function roundTripper($method, $path, $body)
	{
		return $this->request($this->host . $path, $body, $method, $this->header);
	}
	
	public function callWith($path, $body, $contentType = "", $contentLength = "")
	{
		if ($contentType !== "") {
			$this->setHeader("Content-Type", $contentType);
		}	
		
		if (intval($contentLength)) {
			$this->setHeader("Content-Length", $contentLength);
		}
		
		return $this->roundTripper($this->httpMethodPost, $path, $body);
	}
	
	public function call($path)
	{
		return $this->callWith($path, "");
	}
	
	public function callWithForm($path, $ops) 
	{
		return $this->callWith($path, $ops);
	}
	
	public function callWithMultiPart($path, $fields = '', $files = '')
	{
		list($contentType, $body) = $this->encodeMultiPartFormdata($fields, $files);
		return $this->callWith($path, $body, $contentType, strlen($body));
	}
	
	public function encodeMultiPartFormdata($fields, $files)
	{
		$eol = "\r\n";
		$data = array();
		if ($fields == '') {
			$fields = array();
		}
		if ($files == '') {
			$files = array();
		}
		
		$mimeBoundary = md5(time());
		foreach ($fields as $name => $val){
			array_push($data, '--' . $mimeBoundary);
			array_push($data, "Content-Disposition: form-data; name=$name");
			array_push($data, '');
			array_push($data, $val);
		}
		
		foreach ($files as $file) {
			array_push($data, '--' . $mimeBoundary);
			list($name, $fileName, $fileCxt) = $file;			
			array_push($data, "Content-Disposition: form-data; name=$name; filename=$fileName");
			array_push($data, 'Content-Type: application/octet-stream');
			array_push($data, '');
			array_push($data, $fileCxt);
		}
		
		array_push($data, '--' . $mimeBoundary);
		array_push($data, '');
	
		$body = implode($eol, $data);
		$contentType = 'multipart/form-data; boundary=' . $mimeBoundary;
		return array($contentType, $body);
	}
	
   /**
     * Execute a request safely (with curl)
     *
     * @param string $url URL
     * @param mixed  $parameters Array of parameters
     * @param string $httpMethod HTTP Method
     * @param array  $httpHeader HTTP Headers
     * @param int    $formContentType HTTP form content type to use
     * @return array
     */
    private function request($url, $parameters = '', $httpMethod = self::HTTP_METHOD_GET, $httpHeader = null, $formContentType = self::HTTP_FORM_CONTENT_TYPE_APPLICATION, $curlExtraOptions = null)
    {
        $curlOptions = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CUSTOMREQUEST  => $httpMethod
        );
        if (!empty($curlExtraOptions)) {
            foreach ($curlExtraOptions as $k => $v)
                $curlOptions[$k] = $v;
        }

        switch($httpMethod)
        {
            case self::HTTP_METHOD_POST:
                $curlOptions[CURLOPT_POST] = true;
            case self::HTTP_METHOD_PUT:
                if (!isset($curlOptions[CURLOPT_UPLOAD])) {
                    if (self::HTTP_FORM_CONTENT_TYPE_APPLICATION === $formContentType) {
                        if (is_array($parameters))
                            $parameters = http_build_query($parameters);
                    }
                    $curlOptions[CURLOPT_POSTFIELDS] = $parameters;
                }
                break;
            case self::HTTP_METHOD_HEAD:
                $curlOptions[CURLOPT_NOBODY] = true;
            case self::HTTP_METHOD_DELETE:
            case self::HTTP_METHOD_GET:
                $url .= '?' . http_build_query($parameters, null, '&');
                break;
            default:
                break;
        }

        if (is_array($httpHeader))
        {
            $header = array();
            foreach($httpHeader as $key => $parsedUrlValue) {
                $header[] = "$key: $parsedUrlValue";
            }
            $curlOptions[CURLOPT_HTTPHEADER] = $header;
        }

        $curlOptions[CURLOPT_URL] = $url;

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        $ret = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        if ($contentType === "application/json") {
            $jsonDecode = json_decode($ret, true);
        } else {
            $jsonDecode = null;
        }
        $resp = (null === $jsonDecode) ? $ret : $jsonDecode;

        if (floor($code / 100) != 2) {
        	$errMsg = @$resp['error'];
        	return array(null, $errMsg);
        }
 
        return array($resp, null);
    }
	
}


function SiginJson($key, $secret, $data)
{
	$encodedData = URLSafeBase64Encode(json_encode($data));
	$checksum = hash_hmac('sha1', $encodedData, $secret, true);
	$encodedChecksum = URLSafeBase64Encode($checksum);

	return "$key:$encodedChecksum:$encodedData";
}
