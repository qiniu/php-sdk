<?php
namespace Qiniu\Tests;

use Qiniu\Processing\Operation;
use Qiniu\Processing\PersistentFop;

class PfopTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        global $testAuth;
        $pfop = new PersistentFop($testAuth, 'testres', 'sdktest');
        $op = Operation::saveas('avthumb/m3u8/segtime/10/vcodec/libx264/s/320x240', 'phpsdk', 'pfoptest');
        $ops = array();
        array_push($ops, $op);
        list($id, $error) = $pfop->execute('sintel_trailer.mp4', $ops, true);
        $this->assertNull($error);
        list($status, $error) = PersistentFop::status($id);
        $this->assertNotNull($status);
        $this->assertNull($error);
    }
}
