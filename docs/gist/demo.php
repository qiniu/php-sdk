<?php

chdir('../..');
# @gist require_io
require_once('qiniu/io.php');
# @endgist
# @gist require_rs
require_once('qiniu/rs.php');
# @endgist
# @gist require_fop
require_once('qiniu/fop.php');
# @endgist
# @gist require_rsf
require_once('qiniu/rsf.php');
# @endgist
# @gist require_rio
require_once('qiniu/resumable_io.php');
# @endgist
# @gist bucket
$bucket = 'phpsdk';
# @endgist
# @gist key1
$key1 = 'file_name_1';
# @endgist
# @gist key2
$key2 = 'file_name_2';
# @endgist
# @gist file
$file = 'docs/gist/logo.jpg';
# @endgist
# @gist domain
$domain = 'phpsdk.qiniudn.com';
# @endgist

# @gist set_keys
$accessKey = '<YOUR_ACCESS_KEY>';
$secretKey = '<YOUR_SECRET_KEY>';
Qiniu_setKeys($accessKey, $secretKey);
# @endgist
# @gist mac_client
$client = new Qiniu_MacHttpClient(null);
# @endgist

Qiniu_RS_Delete($client, $bucket, $key1);
Qiniu_RS_Delete($client, $bucket, $key2);

//------------------------------------io-----------------------------------------
# @gist putpolicy
$putPolicy = new Qiniu_RS_PutPolicy($bucket);
$upToken = $putPolicy->Token(null);
# @endgist

# @gist put
list($ret, $err) = Qiniu_Put($upToken, $key1, 'Qiniu Storage!', null);
echo "\n\n====> Qiniu_Put result: \n";
if ($err !== null) {
	var_dump($err);
} else {
	var_dump($ret);
}
# @endgist
Qiniu_RS_Delete($client, $bucket, $key1);

# @gist putfile
$putExtra = new Qiniu_PutExtra();
$putExtra->Crc32 = 1;
list($ret, $err) = Qiniu_PutFile($upToken, $key1, $file, $putExtra);
echo "\n\n====> Qiniu_PutFile result: \n";
if ($err !== null) {
	var_dump($err);
} else {
	var_dump($ret);
}
# @endgist
Qiniu_RS_Delete($client, $bucket, $key1);

# @gist getpolicy
$baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key1);
$getPolicy = new Qiniu_RS_GetPolicy();
$privateUrl = $getPolicy->MakeRequest($baseUrl, null);
echo "\n\n====> getPolicy result: \n";
echo $privateUrl . "\n";
# @endgist


//------------------------------------rio-----------------------------------------
//Qiniu_Rio_PutFile($upToken, $key1, $localFile, $putExtra) // => ($putRet, $err)
# @gist rio_putfile
$putExtra = new Qiniu_Rio_PutExtra($bucket);
list($ret, $err) = Qiniu_Rio_PutFile($upToken, $key1, $file, $putExtra);
echo "\n\n====> Qiniu_Rio_PutFile result: \n";
if ($err !== null) {
	var_dump($err);
} else {
	var_dump($ret);
}
# @endgist

//------------------------------------rs-----------------------------------------
# @gist stat
list($ret, $err) = Qiniu_RS_Stat($client, $bucket, $key1);
echo "\n\n====> Qiniu_RS_Stat result: \n";
if ($err !== null) {
	var_dump($err);
} else {
	var_dump($ret);
}
# @endgist

# @gist copy
$err = Qiniu_RS_Copy($client, $bucket, $key1, $bucket, $key2);
echo "\n\n====> Qiniu_RS_Copy result: \n";
if ($err !== null) {
	var_dump($err);
} else {
	echo "Success! \n";
}
# @endgist

# @gist delete
$err = Qiniu_RS_Delete($client, $bucket, $key1);
echo "\n\n====> Qiniu_RS_Delete result: \n";
if ($err !== null) {
	var_dump($err);
} else {
	echo "Success! \n";
}
# @endgist

# @gist move
$err = Qiniu_RS_Move($client, $bucket, $key2, $bucket, $key1);
echo "\n\n====> Qiniu_RS_Move result: \n";
if ($err !== null) {
	var_dump($err);
} else {
	echo "Success! \n";
}
# @endgist

# @gist entrypath1
$e1 = new Qiniu_RS_EntryPath($bucket, $key1);
# @endgist
# @gist entrypath2
$e2 = new Qiniu_RS_EntryPath($bucket, $key2);
# @endgist
# @gist entrypath3
$key3 = $key1 . '3';
$e3 = new Qiniu_RS_EntryPath($bucket, $key3);
# @endgist

# @gist batch_stat
$entries = array($e1, $e2);
list($ret, $err) = Qiniu_RS_BatchStat($client, $entries);
echo "\n\n====> Qiniu_RS_BatchStat result: \n";
if ($err !== null) {
	var_dump($err);
} else {
	var_dump($ret);
}
# @endgist

# @gist batch_copy
$entryPairs = array(new Qiniu_RS_EntryPathPair($e1, $e2), new Qiniu_RS_EntryPathPair($e1, $e3));
list($ret, $err) = Qiniu_RS_BatchCopy($client, $entryPairs);
echo "\n\n====> Qiniu_RS_BatchCopy result: \n";
if ($err !== null) {
	var_dump($err);
} else {
	var_dump($ret);
}
# @endgist

# @gist batch_delete
$entries = array($e1, $e2);
list($ret, $err) = Qiniu_RS_BatchDelete($client, $entries);
echo "\n\n====> Qiniu_RS_BatchDelete result: \n";
if ($err !== null) {
	var_dump($err);
} else {
	var_dump($ret);
}
# @endgist

# @gist batch_move
$entryPairs = array(new Qiniu_RS_EntryPathPair($e3, $e1));
list($ret, $err) = Qiniu_RS_BatchMove($client, $entryPairs);
echo "\n\n====> Qiniu_RS_BatchMove result: \n";
if ($err !== null) {
	var_dump($err);
} else {
	var_dump($ret);
}
# @endgist


//------------------------------------rsf-----------------------------------------

list($iterms, $markerOut, $err) = Qiniu_RSF_ListPrefix($client, $bucket);
echo "\n\n====> Qiniu_RSF result: \n";
if ($err != null) {
	if ($err === Qiniu_RSF_EOF) {
		var_dump($iterms);
	} else {
		var_dump(err);
	}
} else {
	var_dump($iterms);
}


//------------------------------------fop-----------------------------------------
# @gist base_url
//$baseUrl 就是您要访问资源的地址
$baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key1);
# @endgist

# @gist image_info
$getPolicy = new Qiniu_RS_GetPolicy();
$imgInfo = new Qiniu_ImageInfo;

//生成fopUrl
$imgInfoUrl = $imgInfo->MakeRequest($baseUrl);

//对fopUrl 进行签名，生成privateUrl。 公有bucket 此步可以省去。
$imgInfoPrivateUrl = $getPolicy->MakeRequest($imgInfoUrl, null);
echo "\n\n====> imageInfo privateUrl: \n";
echo $imgInfoPrivateUrl . "\n";
# @endgist

# @gist image_exif
$getPolicy = new Qiniu_RS_GetPolicy();
$imgExif = new Qiniu_Exif;

//生成fopUrl
$imgExifUrl = $imgExif->MakeRequest($baseUrl);
//对fopUrl 进行签名，生成privateUrl。 公有bucket 此步可以省去。
$imgExifPrivateUrl = $getPolicy->MakeRequest($imgExifUrl, null);
echo "\n\n====> imageView privateUrl: \n";
echo $imgExifPrivateUrl . "\n";
# @endgist

# @gist image_view
$getPolicy = new Qiniu_RS_GetPolicy();
$imgView = new Qiniu_ImageView;
$imgView->Mode = 1;
$imgView->Width = 60;
$imgView->Height = 30;

//生成fopUrl
$imgViewUrl = $imgView->MakeRequest($baseUrl);

//对fopUrl 进行签名，生成privateUrl。 公有bucket 此步可以省去。
$imgViewPrivateUrl = $getPolicy->MakeRequest($imgViewUrl, null);
echo "\n\n====> imageView privateUrl: \n";
echo $imgViewPrivateUrl . "\n";
# @endgist


