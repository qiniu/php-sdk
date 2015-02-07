<?php
namespace Qiniu\Tests;

use Qiniu\Processing\Operation;
use Qiniu\Processing\PersistentFop;

class FopTest extends \PHPUnit_Framework_TestCase
{
    public function testExifPub()
    {
        $fop = new Operation('testres.qiniudn.com');
        list($exif, $error) = $fop->exif('gogopher.jpg');
        $this->assertNull($error);
        $this->assertNotNull($exif);
    }

    public function testExifPrivate()
    {
        global $testAuth;
        $fop = new Operation('private-res.qiniudn.com', $testAuth);
        list($exif, $error) = $fop->exif('noexif.jpg');
        $this->assertNotNull($error);
        $this->assertNull($exif);
    }
}
