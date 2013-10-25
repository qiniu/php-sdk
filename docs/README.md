---
title: PHP SDK 
---


此 SDK 适用于 PHP 5.1.0 及其以上版本。基于 [七牛云存储官方API](http://docs.qiniu.com) 构建。使用此 SDK 构建您的网络应用程序，能让您以非常便捷地方式将数据安全地存储到七牛云存储上。无论您的网络应用是一个网站程序，还是包括从云端（服务端程序）到终端（手持设备应用）的架构的服务或应用，通过七牛云存储及其 SDK，都能让您应用程序的终端用户高速上传和下载，同时也让您的服务端更加轻盈。

SDK源码地址：<https://github.com/qiniu/php-sdk/tags>


- [应用接入](#install)
	- [获取Access Key 和 Secret Key](#acc-appkey)
- [资源管理接口](#rs-api)
	- [1 查看单个文件属性信息](#rs-stat)
	- [2 复制单个文件](#rs-copy)
	- [3 移动单个文件](#rs-move)
	- [4 删除单个文件](#rs-delete)
- [上传下载接口](#get-and-put-api)
	- [1 文件上传](#upload)
		- [1.1 上传流程](#io-put-flow)
		- [1.2 上传策略](#io-put-policy)
	- [2 文件下载](#io-download)
		- [2.1 公有资源下载](#public-download)
		- [2.2 私有资源下载](#private-download)
- [数据处理接口](#fop-api)
	- [1 图像](#fop-image)
		- [1.1 查看图像属性](#fop-image-info)
		- [1.2 查看图片EXIF信息](#fop-exif)
		- [1.3 生成图片预览](#fop-image-view)
- [贡献代码](#contribution)
- [许可证](#license)



<a name=install></a>
## 应用接入

<a name="acc-appkey"></a>

### 1. 获取Access Key 和 Secret Key

要接入七牛云存储，您需要拥有一对有效的 Access Key 和 Secret Key 用来进行签名认证。可以通过如下步骤获得：

1. [开通七牛开发者帐号](https://portal.qiniu.com/signup)
2. [登录七牛开发者自助平台，查看 Access Key 和 Secret Key](https://portal.qiniu.com/setting/key) 。

<a name=rs-api></a>
## 资源管理接口

<a name="rs-stat"></a>
### 1.查看单个文件属性信息

示例代码如下：

	require_once("qiniu/rs.php");

	$bucket = "phpsdk";
	$key = "pic.jpg";
	$accessKey = '<YOUR_APP_ACCESS_KEY>';
	$secretKey = '<YOUR_APP_SECRET_KEY>';
	
	Qiniu_SetKeys($accessKey, $secretKey);
	$client = new Qiniu_MacHttpClient(null);

	list($ret, $err) = Qiniu_RS_Stat($client, $bucket, $key);
	echo "Qiniu_RS_Stat result: \n";
	if ($err !== null) {
		var_dump($err);
	} else {
		var_dump($ret);
	}
	
<a name="rs-copy"></a>
### 2. 复制单个文件

示例代码如下：

	require_once("qiniu/rs.php");

	$bucket = "phpsdk";
	$key = "pic.jpg";
	$key1 = "file_name1";
	$accessKey = '<YOUR_APP_ACCESS_KEY>';
	$secretKey = '<YOUR_APP_SECRET_KEY>';
	
	Qiniu_SetKeys($accessKey, $secretKey);
	$client = new Qiniu_MacHttpClient(null);
	
	$err = Qiniu_RS_Copy($client, $bucket, $key, $bucket, $key1);
	echo "====> Qiniu_RS_Copy result: \n";
	if ($err !== null) {
		var_dump($err);
	} else {
		echo "Success!";
	}

<a name=rs-move></a>
### 3. 移动单个文件

示例代码如下：

	require_once("qiniu/rs.php");

	$bucket = "phpsdk";
	$key = "pic.jpg";
	$key1 = "file_name1";
	$accessKey = '<YOUR_APP_ACCESS_KEY>';
	$secretKey = '<YOUR_APP_SECRET_KEY>';
	
	Qiniu_SetKeys($accessKey, $secretKey);
	$client = new Qiniu_MacHttpClient(null);
	
	$err = Qiniu_RS_Move($client, $bucket, $key, $bucket, $key1);
	echo "====> Qiniu_RS_Move result: \n";
	if ($err !== null) {
		var_dump($err);
	} else {
		echo "Success!";
	}
	
<a name=rs-delete></a>
### 4. 删除单个文件

示例代码如下：

	require_once("qiniu/rs.php");
	
	$bucket = "phpsdk";
	$key1 = "file_name1";
	$accessKey = '<YOUR_APP_ACCESS_KEY>';
	$secretKey = '<YOUR_APP_SECRET_KEY>';
	
	Qiniu_SetKeys($accessKey, $secretKey);
	$client = new Qiniu_MacHttpClient(null);
	
	$err = Qiniu_RS_Delete($client, $bucket, $key1);
	echo "====> Qiniu_RS_Delete result: \n";
	if ($err !== null) {
		var_dump($err);
	} else {
		echo "Success!";
	}

<a name="get-and-put-api"></a>
## 上传下载接口
	
<a name=upload></a>
### 1. 文件上传

为了尽可能地改善终端用户的上传体验，七牛云存储首创了客户端直传功能。一般云存储的上传流程是：

    客户端（终端用户） => 业务服务器 => 云存储服务

这样多了一次上传的流程，和本地存储相比，会相对慢一些。但七牛引入了客户端直传，将整个上传过程调整为：

    客户端（终端用户） => 七牛 => 业务服务器

客户端（终端用户）直接上传到七牛的服务器，通过DNS智能解析，七牛会选择到离终端用户最近的ISP服务商节点，速度会比本地存储快很多。文件上传成功以后，七牛的服务器使用回调功能，只需要将非常少的数据（比如Key）传给应用服务器，应用服务器进行保存即可。

<a name="io-put-flow"></a>
#### 1.1上传流程

在七牛云存储中，整个上传流程大体分为这样几步：

1. 业务服务器颁发 [uptoken（上传授权凭证）](http://docs.qiniu.com/api/put.html#uploadToken)给客户端（终端用户）
2. 客户端凭借 [uptoken](http://docs.qiniu.com/api/put.html#uploadToken) 上传文件到七牛
3. 在七牛获得完整数据后，发起一个 HTTP 请求回调到业务服务器
4. 业务服务器保存相关信息，并返回一些信息给七牛
5. 七牛原封不动地将这些信息转发给客户端（终端用户）

需要注意的是，回调到业务服务器的过程是可选的，它取决于业务服务器颁发的 [uptoken](http://docs.qiniu.com/api/put.html#uploadToken)。如果没有回调，七牛会返回一些标准的信息（比如文件的 hash）给客户端。如果上传发生在业务服务器，以上流程可以自然简化为：

1. 业务服务器生成 uptoken（不设置回调，自己回调到自己这里没有意义）
2. 凭借 [uptoken](http://docs.qiniu.com/api/put.html#uploadToken) 上传文件到七牛
3. 善后工作，比如保存相关的一些信息

服务端生成 [uptoken](http://docs.qiniu.com/api/put.html#uploadToken) 代码如下：


	require_once("qiniu/rs.php");
	
	$bucket = 'phpsdk';
	$accessKey = '<YOUR_APP_ACCESS_KEY>';
	$secretKey = '<YOUR_APP_SECRET_KEY>';
	
	Qiniu_SetKeys($accessKey, $secretKey);
	$putPolicy = new Qiniu_RS_PutPolicy($bucket);
	$upToken = $putPolicy->Token(null);
	
上传文件到七牛（通常是客户端完成，但也可以发生在服务端）：


上传字符串

	require_once("qiniu/io.php");
	require_once("qiniu/rs.php");
	
	$bucket = "phpsdk";
	$key1 = "file_name1";
	$accessKey = '<YOUR_APP_ACCESS_KEY>';
	$secretKey = '<YOUR_APP_SECRET_KEY>';
	
	Qiniu_SetKeys($accessKey, $secretKey);
	$putPolicy = new Qiniu_RS_PutPolicy($bucket);
	$upToken = $putPolicy->Token(null);
	list($ret, $err) = Qiniu_Put($upToken, $key1, "Qiniu Storage!", null);
	echo "====> Qiniu_Put result: \n";
	if ($err !== null) {
		var_dump($err);
	} else {
		var_dump($ret);
	}

上传本地文件

	require_once("qiniu/io.php");
	require_once("qiniu/rs.php");
	
	$bucket = "phpsdk";
	$key1 = "file_name1";
	$accessKey = '<YOUR_APP_ACCESS_KEY>';
	$secretKey = '<YOUR_APP_SECRET_KEY>';
	
	Qiniu_SetKeys($accessKey, $secretKey);
	$putPolicy = new Qiniu_RS_PutPolicy($bucket);
	$upToken = $putPolicy->Token(null);
	$putExtra = new Qiniu_PutExtra();
	$putExtra->Crc32 = 1;
	list($ret, $err) = Qiniu_PutFile($upToken, $key1, __file__, $putExtra);
	echo "====> Qiniu_PutFile result: \n";
	if ($err !== null) {
		var_dump($err);
	} else {
		var_dump($ret);
	}


<a name="io-put-policy"></a>
### 1.2 上传策略

[uptoken](http://docs.qiniu.com/api/put.html#uploadToken) 实际上是用 AccessKey/SecretKey 进行数字签名的上传策略(`Qiniu_RS_PutPolicy`)，它控制则整个上传流程的行为。让我们快速过一遍你都能够决策啥：

	class Qiniu_RS_PutPolicy
	{
		public $Scope;				// 必选项。可以是 bucketName 或者 bucketName:key
		public $CallbackUrl;		// 可选
		public $CallbackBody;		// 可选
		public $ReturnUrl;			// 可选， 更贴切的名字是 redirectUrl。
		public $ReturnBody;			// 可选
		public $AsyncOps;			// 可选
		public $EndUser;			// 可选
		public $Expires;			// 可选。默认是 3600 秒
		public $PersistentOps;		// 可选。
		public $PersistentNotifyUrl;	// 如果设置了PersistentOps，必须同时设置此项。
	}

* `scope` 限定客户端的权限。如果 `scope` 是 bucket，则客户端只能新增文件到指定的 bucket，不能修改文件。如果 `scope` 为 bucket:key，则客户端可以修改指定的文件。**注意： key必须采用utf8编码，如使用非utf8编码访问七牛云存储将反馈错误**
* `callbackUrl` 设定业务服务器的回调地址，这样业务服务器才能感知到上传行为的发生。
* `callbackBody` 设定业务服务器的回调信息。文件上传成功后，七牛向业务服务器的callbackUrl发送的POST请求携带的数据。支持 [魔法变量](http://docs.qiniu.com/api/put.html#MagicVariables) 和 [自定义变量](http://docs.qiniu.com/api/put.html#xVariables)。
* `returnUrl` 设置用于浏览器端文件上传成功后，浏览器执行301跳转的URL，一般为 HTML Form 上传时使用。文件上传成功后浏览器会自动跳转到 `returnUrl?upload_ret=returnBody`。
* `returnBody` 可调整返回给客户端的数据包，支持 [魔法变量](http://docs.qiniu.com/api/put.html#MagicVariables) 和 [自定义变量](http://docs.qiniu.com/api/put.html#xVariables)。`returnBody` 只在没有 `callbackUrl` 时有效（否则直接返回 `callbackUrl` 返回的结果）。不同情形下默认返回的 `returnBody` 并不相同。在一般情况下返回的是文件内容的 `hash`，也就是下载该文件时的 `etag`；但指定 `returnUrl` 时默认的 `returnBody` 会带上更多的信息。
* `asyncOps` 可指定上传完成后，需要自动执行哪些数据处理。这是因为有些数据处理操作（比如音视频转码）比较慢，如果不进行预转可能第一次访问的时候效果不理想，预转可以很大程度改善这一点。  
* `persistentOps` 可指定音视频文件上传完成后，需要进行的转码持久化操作。asyncOps的处理结果保存在缓存当中，有可能失效。而persistentOps的处理结果以文件形式保存在bucket中，体验更佳。[数据处理(持久化)](http://docs.qiniu.com/api/persistent-ops.html)  
* `persistentNotifyUrl` 音视频转码持久化完成后，七牛的服务器会向用户发送处理结果通知。这里指定的url就是用于接收通知的接口。设置了`persistentOps`,则需要同时设置此字段。

关于上传策略更完整的说明，请参考 [uptoken](http://docs.qiniu.com/api/put.html#uploadToken)。


<a name=io-download></a>
### 2. 文件下载
七牛云存储上的资源下载分为 公有资源下载 和 私有资源下载 。

私有（private）是 Bucket（空间）的一个属性，一个私有 Bucket 中的资源为私有资源，私有资源不可匿名下载。

新创建的空间（Bucket）缺省为私有，也可以将某个 Bucket 设为公有，公有 Bucket 中的资源为公有资源，公有资源可以匿名下载。

<a name=public-download></a>
#### 2.1 公有资源下载
如果在给bucket绑定了域名的话，可以通过以下地址访问。

	[GET] http://<domain>/<key>
	
示例代码：

	$key = 'pic.jpg';
	$domain = 'phpsdk.qiniudn.com';
	//$baseUrl 就是您要访问资源的地址
	$baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key);

其中\<domain\>是bucket所对应的域名。七牛云存储为每一个bucket提供一个默认域名。默认域名可以到[七牛云存储开发者平台](https://portal.qiniu.com/)中，空间设置的域名设置一节查询。用户也可以将自有的域名绑定到bucket上，通过自有域名访问七牛云存储。

**注意： key必须采用utf8编码，如使用非utf8编码访问七牛云存储将反馈错误**

<a name=private-download></a>
#### 2.2 私有资源下载
私有资源必须通过临时下载授权凭证(downloadToken)下载，如下：

	[GET] http://<domain>/<key>?e=<deadline>&token=<downloadToken>

注意，尖括号不是必需，代表替换项。  
私有下载链接可以使用 SDK 提供的如下方法生成：

	require_once("qiniu/rs.php");

	$key = 'pic.jpg';
	$domain = 'phpsdk.qiniudn.com';
	$accessKey = '<YOUR_APP_ACCESS_KEY>';
	$secretKey = '<YOUR_APP_SECRET_KEY>';
	
	Qiniu_SetKeys($accessKey, $secretKey);	
	$baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key);
	$getPolicy = new Qiniu_RS_GetPolicy();
	$privateUrl = $getPolicy->MakeRequest($baseUrl, null);
	echo "====> getPolicy result: \n";
	echo $privateUrl . "\n";


<a name=fop-api></a>
## 数据处理接口
七牛支持在云端对图像, 视频, 音频等富媒体进行个性化处理

<a name=fop-image></a>
### 1. 图像
<a name=fop-image-info></a>
#### 1.1 查看图像属性

	require_once("qiniu/rs.php");
	require_once("qiniu/fop.php");

	$key = 'pic.jpg';
	$domain = 'phpsdk.qiniudn.com';
	$accessKey = '<YOUR_APP_ACCESS_KEY>';
	$secretKey = '<YOUR_APP_SECRET_KEY>';
	
	Qiniu_SetKeys($accessKey, $secretKey);	
	//生成baseUrl
	$baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key);

	//生成fopUrl
 	$imgInfo = new Qiniu_ImageInfo;
 	$imgInfoUrl = $imgInfo->MakeRequest($baseUrl);
 	
 	//对fopUrl 进行签名，生成privateUrl。 公有bucket 此步可以省去。
 	$getPolicy = new Qiniu_RS_GetPolicy();
 	$imgInfoPrivateUrl = $getPolicy->MakeRequest($imgInfoUrl, null);
	echo "====> imageInfo privateUrl: \n";
	echo $imgInfoPrivateUrl . "\n";

将`$imgInfoPrivateUrl`粘贴到浏览器地址栏中就可以查看该图像的信息了。

<a name=fop-exif></a>
#### 1.2 查看图片EXIF信息


	require_once("qiniu/rs.php");
	require_once("qiniu/fop.php");

	$key = 'pic.jpg';
	$domain = 'phpsdk.qiniudn.com';
	$accessKey = '<YOUR_APP_ACCESS_KEY>';
	$secretKey = '<YOUR_APP_SECRET_KEY>';
	
	Qiniu_SetKeys($accessKey, $secretKey);	
	//生成baseUrl
	$baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key);
	
	//生成fopUrl
	$imgExif = new Qiniu_Exif;
 	$imgExifUrl = $imgExif->MakeRequest($baseUrl);
 	
 	//对fopUrl 进行签名，生成privateUrl。 公有bucket 此步可以省去。
 	$getPolicy = new Qiniu_RS_GetPolicy();
 	$imgExifPrivateUrl = $getPolicy->MakeRequest($imgExifUrl, null);
	echo "====> imageView privateUrl: \n";
	echo $imgExifPrivateUrl . "\n";
	
<a name=fop-image-view></a>
#### 1.3 生成图片预览

	require_once("qiniu/rs.php");
	require_once("qiniu/fop.php");

	$key = 'pic.jpg';
	$domain = 'phpsdk.qiniudn.com';
	$accessKey = '<YOUR_APP_ACCESS_KEY>';
	$secretKey = '<YOUR_APP_SECRET_KEY>';
	
	Qiniu_SetKeys($accessKey, $secretKey);	
	//生成baseUrl
	$baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key);
	
	//生成fopUrl
 	$imgView = new Qiniu_ImageView;
 	$imgView->Mode = 1;
 	$imgView->Width = 60;
 	$imgView->Height = 120;
 	$imgViewUrl = $imgView->MakeRequest($baseUrl);
 	
 	//对fopUrl 进行签名，生成privateUrl。 公有bucket 此步可以省去。
 	$getPolicy = new Qiniu_RS_GetPolicy();
 	$imgViewPrivateUrl = $getPolicy->MakeRequest($imgViewUrl, null);
	echo "====> imageView privateUrl: \n";
	echo $imgViewPrivateUrl . "\n";
	
	
<a name=contribution></a>
## 贡献代码

1. Fork
2. 创建您的特性分支 (`git checkout -b my-new-feature`)
3. 提交您的改动 (`git commit -am 'Added some feature'`)
4. 将您的修改记录提交到远程 `git` 仓库 (`git push origin my-new-feature`)
5. 然后到 github 网站的该 `git` 远程仓库的 `my-new-feature` 分支下发起 Pull Request


<a name=license></a>
## 许可证

Copyright (c) 2013 qiniu.com

基于 MIT 协议发布:

* [www.opensource.org/licenses/MIT](http://www.opensource.org/licenses/MIT)

