---
title: PHP SDK | 七牛云存储
---

# PHP SDK 使用指南

此 SDK 适用于 PHP 5.1.0 及其以上版本。基于 [七牛云存储官方API](http://docs.qiniu.com) 构建。使用此 SDK 构建您的网络应用程序，能让您以非常便捷地方式将数据安全地存储到七牛云存储上。无论您的网络应用是一个网站程序，还是包括从云端（服务端程序）到终端（手持设备应用）的架构的服务或应用，通过七牛云存储及其 SDK，都能让您应用程序的终端用户高速上传和下载，同时也让您的服务端更加轻盈。

SDK源码地址：<https://github.com/qiniu/php-sdk/tags>



- [获取Access Key 和 Secret Key](#acc-appkey)
- [资源管理接口](#rs-api)
	- [1 查看单个文件属性信息](#rs-stat)
	- [2 复制单个文件](#rs-copy)
	- [3 移动单个文件](#rs-move)
	- [4 删除单个文件](#rs-delete)
- [上传下载接口](#get-and-put-api)
	- [1 上传授权](#token)
		- [1.1 生成uptoken](#make-uptoken)
	- [2 文件上传](#upload)
		- [2.1 普通上传](#io-upload)
	- [3 文件下载](#io-download)
		- [1 公有资源下载](#public-download)
		- [2 私有资源下载](#private-download)
- [数据处理接口](#fop-api)
	- [1 图像](#fop-image)
		- [1.1 查看图像属性](#fop-image-info)
		- [1.2 查看图片EXIF信息](#fop-exif)
		- [1.3 生成图片预览](#fop-image-view)
- [贡献代码](#contribution)
- [许可证](#license)




## 应用接入

<a name="acc-appkey"></a>

### 1. 获取Access Key 和 Secret Key

要接入七牛云存储，您需要拥有一对有效的 Access Key 和 Secret Key 用来进行签名认证。可以通过如下步骤获得：

1. [开通七牛开发者帐号](https://portal.qiniu.com/signup)
2. [登录七牛开发者自助平台，查看 Access Key 和 Secret Key](https://portal.qiniu.com/setting/key) 。

### 2. 签名认证

首先，到 [https://github.com/qiniu/php-sdk/tags](https://github.com/qiniu/php-sdk/tags) 下载SDK源码。

然后，将SDK压缩包解压放到您的项目中，确保php-sdk/qiniu/目录中存在一个名为 conf.php 的文件，编辑该文件配置您应用程序的密钥信息（Access Key 和 Secret Key）。

$ vim path/to/your_project/lib/php-sdk/qiniu/conf.php

找到如下两行代码并做相应修改：

	$QINIU_ACCESS_KEY	= '<Please apply your access key>';
	$QINIU_SECRET_KEY	= '<Dont send your secret key to anyone>';

在完成 Access Key 和 Secret Key 配置后，您就可以正常使用该 SDK 提供的功能了，这些功能接下来会一一介绍。

<a name=rs-api></a>
## 资源管理接口

<a name="rs-stat"></a>
### 1.查看单个文件属性信息

示例代码如下：

	require_once("rs.php");

	$bucket = "phpsdk";
	$key = "file_name";
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

	require_once("rs.php");

	$bucket = "phpsdk";
	$key = "file_name";
	$key1 = "file_name1";
	$client = new Qiniu_MacHttpClient(null);
	
	$err = Qiniu_RS_Copy($client, $bucket, $key, $bucket, $key1);
	echo "====> Qiniu_RS_Copy result: \n";
	if ($err !== null) {
		var_dump($err);
	} else {
		echo "Success!";
	}
	
<a name="rs-delete"></a>
### 3. 删除单个文件

示例代码如下：

	require_once("rs.php");
	
	$bucket = "phpsdk";
	$key = "file_name";
	$key1 = "file_name1";
	$client = new Qiniu_MacHttpClient(null);
	
	$err = Qiniu_RS_Delete($client, $bucket, $key1);
	echo "====> Qiniu_RS_Delete result: \n";
	if ($err !== null) {
		var_dump($err);
	} else {
		echo "Success!";
	}

<a name="rs-move"></a>
### 4. 移动单个文件

示例代码如下：

	require_once("rs.php");

	$bucket = "phpsdk";
	$key = "file_name";
	$key1 = "file_name1";
	$client = new Qiniu_MacHttpClient(null);
	
	$err = Qiniu_RS_Move($client, $bucket, $key, $bucket, $key1);
	echo "====> Qiniu_RS_Move result: \n";
	if ($err !== null) {
		var_dump($err);
	} else {
		echo "Success!";
	}
	
	
<a name="get-and-put-api"></a>
## 上传下载接口

<a name="token"></a>
###1.上传下载授权

<a name="make-uptoken"></a>
####上传授权uptoken
uptoken是一个字符串，作为http协议Header的一部分（Authorization字段）发送到我们七牛的服务端，表示这个http请求是经过认证的。

示例代码如下：

	$putPolicy = new Qiniu_RS_PutPolicy($bucket);
	$upToken = $putPolicy->Token(null);
	
<a name=upload></a>
###2. 文件上传
**注意**：如果您只是想要上传已存在您电脑本地或者是服务器上的文件到七牛云存储，可以直接使用七牛提供的 [qrsync](/v3/tools/qrsync/) 上传工具。
文件上传有两种方式，一种是以普通方式直传文件，简称普通上传，另一种方式是断点续上传，断点续上传在网络条件很一般的情况下也能有出色的上传速度，而且对大文件的传输非常友好。

<a name=io-upload></a>
#### 2.1 普通上传

上传字符串

	require_once("io.php");
	require_once("rs.php");
	
	$bucket = "phpsdk";
	$key = "file_name";
	
	$putPolicy = new Qiniu_RS_PutPolicy($bucket);
	$upToken = $putPolicy->Token(null);
	$putExtra = new Qiniu_PutExtra();
	list($ret, $err) = Qiniu_Put($upToken, $key, "Qiniu Storage!", null);
	echo "====> Qiniu_Put result: \n";
	if ($err !== null) {
		var_dump($err);
	} else {
		var_dump($ret);
	}

上传本地文件

	require_once("io.php");
	require_once("rs.php");
	
	$bucket = "phpsdk";
	$key = "file_name";
	
	$putPolicy = new Qiniu_RS_PutPolicy($bucket);
	$upToken = $putPolicy->Token(null);
	$putExtra = new Qiniu_PutExtra();
	$putExtra->Crc32 = 1;
	list($ret, $err) = Qiniu_PutFile($upToken, $key, __file__, $putExtra);
	echo "====> Qiniu_PutFile result: \n";
	if ($err !== null) {
		var_dump($err);
	} else {
		var_dump($ret);
	}

<a name=io-download></a>
### 3. 文件下载
七牛云存储上的资源下载分为 公有资源下载 和 私有资源下载 。

私有（private）是 Bucket（空间）的一个属性，一个私有 Bucket 中的资源为私有资源，私有资源不可匿名下载。

新创建的空间（Bucket）缺省为私有，也可以将某个 Bucket 设为公有，公有 Bucket 中的资源为公有资源，公有资源可以匿名下载。

<a name=public-download></a>
#### 3.1 公有资源下载
如果在给bucket绑定了域名的话，可以通过以下地址访问。

	[GET] http://<domain>/<key>

其中<domain>可以到[七牛云存储开发者自助网站](https://portal.qiniu.com/)绑定, 域名可以使用自己一级域名的或者是由七牛提供的二级域名(`<bucket>.qiniudn.com`)。注意，尖括号不是必需，代表替换项。

<a name=private-download></a>
#### 3.2 私有资源下载
私有资源必须通过临时下载授权凭证(downloadToken)下载，如下：

	[GET] http://<domain>/<key>?e=<deadline>&token=<downloadToken>

注意，尖括号不是必需，代表替换项。  
私有下载链接可以使用 SDK 提供的如下方法生成：

	require_once("rs.php");
	require_once("fop.php");

	$key = 'file_name';
	$domain = 'phpsdk.qiniudn.com';
	
	$baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key);
	$getPolicy = new Qiniu_RS_GetPolicy();
	$privateUrl = $getPolicy->MakeRequest($baseUrl);
	echo "====> getPolicy result: \n";
	echo $privateUrl . "\n";


<a name=fop-api></a>
## 数据处理接口
七牛支持在云端对图像, 视频, 音频等富媒体进行个性化处理

<a name=fop-image></a>
### 1. 图像
<a name=fop-image-info></a>
#### 1.1 查看图像属性

	require_once("rs.php");
	require_once("fop.php");

	$key = 'pic.jpg';
	$domain = 'phpsdk.qiniudn.com';
	
	//生成baseUrl
	$baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key);
	$getPolicy = new Qiniu_RS_GetPolicy();
	
	//生成fopUrl
 	$imgInfo = new Qiniu_ImageInfo;
 	$imgInfoUrl = $imgInfo->MakeRequest($baseUrl);
 	
 	//对fopUrl 进行签名，生成privateUrl。 公有bucket 此步可以省去。
 	$imgInfoPrivateUrl = $getPolicy->MakeRequest($imgInfoUrl, null);
	echo "====> imageInfo privateUrl: \n";
	echo $imgInfoPrivateUrl . "\n";

将`$imgInfoPrivateUrl`粘贴到浏览器地址栏中就可以查看该图像的信息了。

<a name=fop-exif></a>
#### 1.2 查看图片EXIF信息


	require_once("rs.php");
	require_once("fop.php");

	$key = 'pic.jpg';
	$domain = 'phpsdk.qiniudn.com';
	
	//生成baseUrl
	$baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key);
	$getPolicy = new Qiniu_RS_GetPolicy();
	
	//生成fopUrl
	$imgExif = new Qiniu_Exif;
 	$imgExifUrl = $imgExif->MakeRequest($baseUrl);
 	
 	//对fopUrl 进行签名，生成privateUrl。 公有bucket 此步可以省去。
 	$imgExifPrivateUrl = $getPolicy->MakeRequest($imgExifUrl, null);
	echo "====> imageView privateUrl: \n";
	echo $imgExifPrivateUrl . "\n";
	
<a name=fop-image-view></a>
#### 1.3 生成图片预览

	require_once("rs.php");
	require_once("fop.php");

	$key = 'pic.jpg';
	$domain = 'phpsdk.qiniudn.com';
	
	//生成baseUrl
	$baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key);
	$getPolicy = new Qiniu_RS_GetPolicy();
	
	//生成fopUrl
 	$imgView = new Qiniu_ImageView;
 	$imgView->Mode = 1;
 	$imgView->Width = 60;
 	$imgView->Height = 30;
 	$imgViewUrl = $imgView->MakeRequest($baseUrl);
 	
 	//对fopUrl 进行签名，生成privateUrl。 公有bucket 此步可以省去。
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

