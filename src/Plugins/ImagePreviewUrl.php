<?php
/**
 * Created by PhpStorm.
 * User: ZhangWB
 * Date: 2015/4/21
 * Time: 16:42
 */

namespace zgldh\QiniuStorage\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

/**
 * Class PrivateDownloadUrl
 * 查看图像EXIF <br>
 * $disk        = \Storage::disk('qiniu'); <br>
 * $re          = $disk->getDriver()->imagePreviewUrl('foo/bar1.css',$ops); <br>
 * @package zgldh\QiniuStorage\Plugins
 */
class ImagePreviewUrl extends AbstractPlugin {

    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'imagePreviewUrl';
    }

    public function handle($path = null, $ops = null)
    {
        return $this->filesystem->getAdapter()->imagePreviewUrl($path, $ops);
    }
}