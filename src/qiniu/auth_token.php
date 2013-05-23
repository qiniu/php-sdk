<?php
require_once('auth_sign.php');
require_once('config.php');
require_once('rpc.php');

class PutPolicy
{
	public $scope;
	public $expires = 3600;
	public $callbackUrl;
	public $callbackBodyType;
	public $customer;
	public $asyncOps;
	public $escape;            # 非 0 表示 Callback 的 Params 支持转义符
	public $detectMime;
	
	public function __construct($scope)
	{
		$this->scope = $scope;
	}
	
	public function token() 
	{
		$deadline = time() + $this->expires;
		$params = array("scope" => $this->scope, "deadline" => $deadline);

		if (!empty($this->callbackUrl)) {
			$params['callbackUrl'] = $this->callbackUrl;
		}
		
		if (!empty($this->callbackBodyType)) {
			$params['callbackBodyType'] = $this->callbackBodyType;
		}
		
		if (!empty($this->customer)) {
			$params['customer'] = $this->customer;
		}
		
		if (!empty($this->asyncOps)) {
			$params['asyncOps'] = $this->asyncOps;
		}
		
		if (!empty($this->escape)) {
			$params['escape'] = $this->escape;
		}
		
		if (!empty($this->detectMime)) {
			$params['detectMime'] = $this->detectMime;
		}
		
		return SiginJson(ACCESS_KEY, SECRET_KEY, $params);
	}
}


class GetPolicy
{
	public $scope;
	public $expires = 3600;
	
	public function __construct($scope)
	{
		$this->scope = $scope;
	}
	
	public function token() 
	{
		$deadline = time() + $this->expires;
		$params = array('S' => $this->scope, 'E' => $deadline);

		return SiginJson(ACCESS_KEY, SECRET_KEY, $params);
	}
}