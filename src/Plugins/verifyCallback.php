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
 * Class verifyCallback
 * 验证回调是否正确 <br>
 * $disk        = \Storage::disk('qiniu'); <br>
 * $re          = $disk->getDriver()->verifyCallback('application/x-www-form-urlencoded', $request->header('Authorization'), 'callback url', $request->getContent()); <br>
 * @package zgldh\QiniuStorage\Plugins
 */
class verifyCallback extends AbstractPlugin
{

    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'verifyCallback';
    }

    public function handle($contentType, $originAuthorization, $url, $body)
    {
        return $this->filesystem->getAdapter()->verifyCallback($contentType, $originAuthorization, $url, $body);
    }
}
