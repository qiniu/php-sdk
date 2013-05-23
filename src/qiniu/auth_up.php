<?php

class UPClient extends Client
{
	public $token;
	public function __construct($token)
	{
		$this->token = $token;
	}
	
	public function setAuth($url, &$httpHeader, &$parameters) 
	{
		$httpHeader['Authorization'] = "UpToken " . $this->token;
	}
}