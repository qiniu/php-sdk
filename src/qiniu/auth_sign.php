<?php
require_once('config.php');
require_once('utils.php');

// function SiginJson($key, $secret, $data)
// {
// 	$scope = URLSafeBase64Encode(json_encode($data));
// 	$checksum = hash_hmac('sha1', $scope, $secret, true);
// 	$encoded_checksum = URLSafeBase64Encode($checksum);
	
// 	return "$key:$encoded_checksum:$scope";
// }