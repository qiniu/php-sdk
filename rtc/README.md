# Rtc Streaming Cloud Server-Side Library For PHP

## Features

- App
    - [x] 创建房间: App->createApp()
    - [x] 查看房间: App->getApp()
    - [x] 删除房间: App->deleteApp()
    - [x] 生成房间token: App->AppToken()



## Contents

- [Installation](#installation)
- [Usage](#usage)
    - [Configuration](#configuration)
    - [App](#app)
        - [Create a app](#create-a-app)
        - [Get a app](#get-a-app)
        - [Delete a app](#delete-a-app)
        - [Generate a app token](#generate-a-app-token)


## Usage

### App

#### Create a app

```php
$ak = "gwd_gV4gPKZZsmEOvAuNU1AcumicmuHooTfu64q5";
$sk = "xxxx";
$mac = new Qiniu\Rtc\Mac($ak, $sk);
$client = new Qiniu\Rtc\AppClient($mac);
$resp=$client->createApp("901","testApp");
print_r($resp);
```

#### Get a app

```php
$ak = "gwd_gV4gPKZZsmEOvAuNU1AcumicmuHooTfu64q5";
$sk = "xxxx";
$mac = new Qiniu\Rtc\Mac($ak, $sk);
$client = new Qiniu\Rtc\AppClient($mac);
$resp=$client->getApp("deq02uhb6");
print_r($resp);
```

#### Delete a app

```php
$ak = "gwd_gV4gPKZZsmEOvAuNU1AcumicmuHooTfu64q5";
$sk = "xxxx";
$mac = new Qiniu\Rtc\Mac($ak, $sk);
$client = new Qiniu\Rtc\AppClient($mac);
$resp=$client->deleteApp("deq02uhb6");
print_r($resp);
```

#### Generate a app token

```php
$ak = "gwd_gV4gPKZZsmEOvAuNU1AcumicmuHooTfu64q5";
$sk = "xxxx";
$mac = new Qiniu\Rtc\Mac($ak, $sk);
$client = new Qiniu\Rtc\AppClient($mac);
$resp=$client->appToken("deq02uhb6", "lfx", '1111', (time()+3600), 'user');
print_r($resp);
```