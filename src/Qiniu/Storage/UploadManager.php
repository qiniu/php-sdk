<?php
namespace Qiniu\Storage;

use Qiniu\Config;
use Qiniu\Http\HttpClient;
use Qiniu\Storage\ResumeUploader;
use Qiniu\Storage\FormUploader;

final class UploadManager
{
    public function __construct()
    {
    }

    public function put(
        $upToken,
        $key,
        $data,
        $params = null,
        $mime = 'application/octet-stream',
        $checkCrc = false
    ) {
        $params = self::trimParams($params);
        return FormUploader::put(
            $upToken,
            $key,
            $data,
            $params,
            $mime,
            $checkCrc
        );
    }

    public function putFile(
        $upToken,
        $key,
        $filePath,
        $params = null,
        $mime = 'application/octet-stream',
        $checkCrc = false
    ) {
        $file = fopen($filePath, 'rb');
        if ($file === false) {
            throw new Exception("file can not open", 1);
        }
        $params = self::trimParams($params);
        $stat = fstat($file);
        $size = $stat['size'];
        if ($size <= Config::BLOCK_SIZE) {
            $data = fread($file, $size);
            fclose($file);
            if ($data === false) {
                throw new Exception("file can not read", 1);
            }
            return FormUploader::put(
                $upToken,
                $key,
                $data,
                $params,
                $mime,
                $checkCrc
            );
        }
        $up = new ResumeUploader(
            $upToken,
            $key,
            $file,
            $size,
            $params,
            $mime,
            $checkCrc
        );
        return $up->upload();
    }

    public static function trimParams($params)
    {
        if ($params == null) {
            return null;
        }
        $ret = array();
        foreach ($params as $k => $v) {
            $pos = strpos($k, 'x:');
            if ($pos === 0 && !empty($v)) {
                $ret[$k] = $v;
            }
        }
        return $ret;
    }
}
