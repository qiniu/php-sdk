<?php
namespace Qiniu\Tests;

use Qiniu\Processing\Operation;
use Qiniu\Processing\PersistentFop;

class PfopTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute1()
    {
        global $testAuth;
        $bucket = 'testres';
        $key = 'sintel_trailer.mp4';
        $pfop = new PersistentFop($testAuth, $bucket);
        
        $fops = 'avthumb/m3u8/segtime/10/vcodec/libx264/s/320x240';
        list($id, $error) = $pfop->execute($key, $fops);
        $this->assertNull($error);
        list($status, $error) = PersistentFop::status($id);
        $this->assertNotNull($status);
        $this->assertNull($error);
    }


    public function testExecute2()
    {
        global $testAuth;
        $bucket = 'testres';
        $key = 'sintel_trailer.mp4';
        $fops = array(
                'avthumb/m3u8/segtime/10/vcodec/libx264/s/320x240',
                'vframe/jpg/offset/7/w/480/h/360',
            );
        $pfop = new PersistentFop($testAuth, $bucket);

        list($id, $error) = $pfop->execute($key, $fops);
        $this->assertNull($error);
        list($status, $error) = PersistentFop::status($id);
        $this->assertNotNull($status);
        $this->assertNull($error);
    }
}
