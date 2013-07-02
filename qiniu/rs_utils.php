<?php

require_once("rs.php");
require_once("io.php");

function Qiniu_RS_Put($self, $bucket, $key, $body, $putExtra) // => ($data, $err)
{
	$putPolicy = new Qiniu_RS_PutPolicy("$bucket:$key");
	$upToken = $putPolicy->Token($self->Mac);
	return Qiniu_Put($upToken, $key, $body, $putExtra);
}

function Qiniu_RS_PutFile($self, $bucket, $key, $localFile, $putExtra) // => ($data, $err)
{
	$putPolicy = new Qiniu_RS_PutPolicy("$bucket:$key");
	$upToken = $putPolicy->Token($self->Mac);
	return Qiniu_PutFile($upToken, $key, $localFile, $putExtra);
}

