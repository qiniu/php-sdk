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
 * $re          = $disk->getDriver()->persistentStatus('foo/bar1.css'); <br>
 * @package zgldh\QiniuStorage\Plugins
 */
class PersistentStatus extends AbstractPlugin {

    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'persistentStatus';
    }

    public function handle($id)
    {
        return $this->filesystem->getAdapter()->persistentStatus($id);
    }
}