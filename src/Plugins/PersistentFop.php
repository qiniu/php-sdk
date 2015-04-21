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
 * $re          = $disk->getDriver()->persistentFop('foo/bar1.css'); <br>
 * @package zgldh\QiniuStorage\Plugins
 */
class PersistentFop extends AbstractPlugin {

    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'persistentFop';
    }

    public function handle($path = null, $fops = null)
    {
        return $this->filesystem->getAdapter()->persistentFop($path, $fops);
    }
}