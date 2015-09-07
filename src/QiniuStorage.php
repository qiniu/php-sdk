<?php namespace zgldh\QiniuStorage;


class QiniuStorage
{
    private $storage = null;
    private static $instance = null;

    public static function disk($name)
    {
        if (self::$instance == null) {
            self::$instance = new self($name);
        }

        return self::$instance;
    }

    private function __construct($name)
    {
        $this->storage = \Storage::disk($name);
    }

    /**
     * 文件是否存在
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        return $this->storage->exists($key);
    }

    /**
     * 获取文件内容
     * @param $key
     * @return string
     */
    public function get($key)
    {
        return $this->storage->get($key);
    }

    /**
     * 上传文件
     * @param $key
     * @param $contents
     * @return bool
     */
    public function put($key, $contents)
    {
        return $this->storage->put($key, $contents);
    }

    /**
     * 附加内容到文件开头
     * @param $key
     * @param $contents
     * @return int
     */
    public function prepend($key, $contents)
    {
        return $this->storage->prepend($key, $contents);
    }

    /**
     * 附加内容到文件结尾
     * @param $key
     * @param $content
     * @return int
     */
    public function append($key, $contents)
    {
        return $this->storage->append($key, $contents);
    }

    /**
     * 删除文件
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        return $this->storage->delete($key);

    }

    /**
     * 复制文件到新的路径
     * @param $key
     * @param $key2
     * @return bool
     */
    public function copy($key, $key2)
    {
        return $this->storage->copy($key, $key2);

    }

    /**
     * 移动文件到新的路径
     * @param $key
     * @param $key2
     * @return bool
     */
    public function move($key, $key2)
    {
        return $this->storage->move($key, $key2);

    }

    public function size($key)
    {
        return $this->storage->size($key);

    }

    public function lastModified($key)
    {
        return $this->storage->lastModified($key);

    }

    public function files($key)
    {
        return $this->storage->files($key);
    }

    public function allFiles($key)
    {
        return $this->storage->files($key);
    }

    public function directories($key)
    {
        return $this->storage->files($key);
    }

    public function allDirectories($key)
    {
        return $this->storage->files($key);
    }

    public function makeDirectory($key)
    {
        return $this->storage->makeDirectory($key);
    }

    public function deleteDirectory($key)
    {
        return $this->storage->deleteDirectory($key);
    }

    /**
     * 获取上传Token
     * @param $key
     * @return bool
     */
    public function uploadToken($key)
    {
        return $this->storage->getDriver()->uploadToken($key);
    }

    /**
     * 获取下载地址
     * @param $key
     * @return mixed
     */
    public function downloadUrl($key, $domainType = 'default')
    {
        return $this->storage->getDriver()->downloadUrl($key, $domainType);
    }

    /**
     * 获取私有bucket下载地址
     * @param $key
     * @return mixed
     */
    public function privateDownloadUrl($key, $domainType = 'default')
    {
        return $this->storage->getDriver()->privateDownloadUrl($key, $domainType);
    }

    /**
     * 获取图片信息
     * @param $key
     * @return mixed
     */
    public function imageInfo($key)
    {
        return $this->storage->getDriver()->imageInfo($key);
    }

    /**
     * 获取图片EXIF信息
     * @param $key
     * @return mixed
     */
    public function imageExif($key)
    {
        return $this->storage->getDriver()->imageExif($key);
    }

    /**
     * 获取图片预览URL
     * @param $key
     * @param $opts
     * @return mixed
     */
    public function imagePreviewUrl($key, $opts)
    {
        return $this->storage->getDriver()->imagePreviewUrl($key, $opts);
    }

    /**
     * 执行持久化数据处理
     * @param $key
     * @param $opts
     * @return mixed
     */
    public function persistentFop($key, $opts)
    {
        return $this->storage->getDriver()->persistentFop($key, $opts);
    }

    /**
     * 查看持久化数据处理的状态
     * @param $id
     * @return mixed
     */
    public function persistentStatus($id)
    {
        return $this->storage->getDriver()->persistentStatus($id);
    }
}