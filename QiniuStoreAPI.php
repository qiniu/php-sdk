<?php
/*
 * Qiniu objcet store 封装
 * Class QiniuStoreAPI
 * @author  iscod-ning
 * @computer kukuxiu
*/

function classLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . '/src/' . $path . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('classLoader');

require_once  __DIR__ . '/src/Qiniu/functions.php';

use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

class QiniuStoreAPI{

  private $accessKey = 'accessKey'; //AK
  private $secretKey = 'secretKey'; //SK
  private $bucket = 'bucket';//默认空间名
  private $auth;

  /**
   * 参数初始化
   * @param $appKey
   * @param $appSecret
   * @param string $format
  */ 
  public function __construct(){
    //初始化Auth状态：
    $auth = new Auth($this->accessKey, $this->secretKey);
    $this->auth = $auth;
  }

  /**
  *第三方资源抓取
  *@param $url第三方地址
  *@param $bucket云空间
  *@param $filename生成的名称
  *@return bool
  */
  public function fetch($url, $filename, $bucket = ''){
    if (!$bucket) $bucket = $this->bucket;
    $bmgr = new BucketManager($this->auth);

    list($ret, $err) = $bmgr->fetch($url, $bucket, $filename);
    if ($err !== null) {
      return false;
    }else{
      return isset($ret['key']) ? $ret['key'] : '';
    }
  }

  /**
  *@param $filePath 要上传文件的本地路径
  *@param $key 上传到七牛后保存的文件名
  *@param $bucket 上传的空间名称
  *@return bool
  */
  public function Upload($filePath, $key, $bucket = ''){
    if (!$bucket) $bucket = $this->bucket;
    $auth = $this->auth;

    // 生成上传 Token
    $token = $auth->uploadToken($bucket);

    // 初始化 UploadManager 对象并进行文件的上传。
    $uploadMgr = new UploadManager();

    list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);

    if ($err !== null) {
        return false;
    } else {
      return isset($ret['key']) ? $ret['key'] : '';
    }
  }

  /**
  *删除文件
  */
  public function delete($key, $bucket = ''){
    $auth = $this->auth;
    if (!$bucket) $bucket = $this->bucket;
    //初始化BucketManager
    $bucketMgr = new BucketManager($auth);

    $err = $bucketMgr->delete($bucket, $key);

    if ($err !== null) {
        return false;
    } else {
      return true;
        echo "Success!";
    }
  }

}