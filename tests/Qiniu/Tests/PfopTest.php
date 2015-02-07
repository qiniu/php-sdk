<?php
namespace Qiniu\Tests;

use Qiniu\Processing\Operation;
use Qiniu\Processing\PersistentFop;

class PfopTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute1()
    {
        global $testAuth;
        $pfop = new PersistentFop($testAuth, 'testres', 'sdktest', true);
        $op = Operation::saveas('avthumb/m3u8/segtime/10/vcodec/libx264/s/320x240', 'phpsdk', 'pfoptest');
        $ops = array();
        array_push($ops, $op);
        list($id, $error) = $pfop->execute('sintel_trailer.mp4', $ops);
        $this->assertNull($error);
        list($status, $error) = PersistentFop::status($id);
        $this->assertNotNull($status);
        $this->assertNull($error);
    }

    public function testAvthumb()
    {
        global $testAuth;
        $pfop = new PersistentFop($testAuth, 'testres', 'sdktest', true);
        $options = array(
            'segtime' => 10,
            'vcodec' => 'libx264',
            's' => '320x240'
        );
        list($id, $error) = $pfop->avthumb('sintel_trailer.mp4', 'm3u8', $options, 'phpsdk', 'avthumtest');
        $this->assertNull($error);
        list($status, $error) = PersistentFop::status($id);
        $this->assertNotNull($status);
        $this->assertNull($error);
    }

    public function testExecute2()
    {
        global $testAuth;
        $pfop = new PersistentFop($testAuth, 'testres', 'sdktest', true);
        $url_src1 =  'http://testres.qiniudn.com/gogopher.jpg';
        $url_en1 = \Qiniu\base64_urlSafeEncode($url_src1);
        $url_alias_en1 = \Qiniu\base64_urlSafeEncode('g.jpg');
        $url_en2 = $url_en1;
        $fop = "mkzip/2/url/$url_en1/alias/$url_alias_en1/url/$url_en2";
        $op = Operation::saveas($fop, 'phpsdk', 'mkziptest');
        $ops = array();
        array_push($ops, $op);
        list($id, $error) = $pfop->execute('sintel_trailer.mp4', $ops);
        $this->assertNull($error);
        list($status, $error) = PersistentFop::status($id);
        $this->assertNotNull($status);
        $this->assertNull($error);
    }

    public function testMkzip()
    {
        global $testAuth;
        $pfop = new PersistentFop($testAuth, 'testres', 'sdktest', true);
        $urls = array(
            'http://testres.qiniudn.com/gogopher.jpg' => 'g.jpg',
            'http://testres.qiniudn.com/gogopher.jpg'
        );
        list($id, $error) = $pfop->mkzip('sintel_trailer.mp4', $urls, 'phpsdk', 'mkziptest2.zip');
        $this->assertNull($error);
        list($status, $error) = PersistentFop::status($id);
        $this->assertNotNull($status);
        $this->assertNull($error);
    }
}
