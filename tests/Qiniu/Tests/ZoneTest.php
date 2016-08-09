<?php
namespace Qiniu\Tests;

use Qiniu;
use Qiniu\Zone;

class ZoneTest extends \PHPUnit_Framework_TestCase
{
    protected $zone;
    protected $zoneHttps;
    protected $ak;

    protected $bucketName;
    protected $bucketNameBC;


    protected function setUp()
    {
        global $bucketName;
        $this->bucketName = $bucketName;

        global $bucketNameBC;
        $this->bucketNameBC = $bucketNameBC;

        global $accessKey;
        $this->ak = $accessKey;

        $this->zone = new Zone();
        $this->zoneHttps = new Zone('https');
    }

    public function testUpHosts()
    {

        list($upHosts, $err) = $this->zone->getUpHosts($this->ak, $this->bucketName);
        $this->assertNull($err);
        $this->assertEquals('http://up.qiniu.com', $upHosts[0]);
        $this->assertEquals('http://upload.qiniu.com', $upHosts[1]);

        list($upHosts, $err) = $this->zone->getUpHosts($this->ak, $this->bucketNameBC);
        $this->assertNull($err);
        $this->assertEquals('http://up-z1.qiniu.com', $upHosts[0]);
        $this->assertEquals('http://upload-z1.qiniu.com', $upHosts[1]);

        list($upHosts, $err) = $this->zoneHttps->getUpHosts($this->ak, $this->bucketName);
        $this->assertNull($err);
        $this->assertEquals('https://up.qbox.me', $upHosts[0]);

        list($upHosts, $err) = $this->zoneHttps->getUpHosts($this->ak, $this->bucketNameBC);
        $this->assertNull($err);
        $this->assertEquals('https://up-z1.qbox.me', $upHosts[0]);
    }

    public function testUpHostByToken()
    {
        $uptoken_bc = 'QWYn5TFQsLLU1pL5MFEmX3s5DmHdUThav9WyOWOm:bl77a3xPdTyBNYFGVRy
        oIQNyp_s=:eyJzY29wZSI6InBocHNkay1iYyIsImRlYWRsaW5lIjoxNDcwNzI1MzE1LCJ1cEhvc
        3RzIjpbImh0dHA6XC9cL3VwLXoxLnFpbml1LmNvbSIsImh0dHA6XC9cL3VwbG9hZC16MS5xaW5p
        dS5jb20iLCItSCB1cC16MS5xaW5pdS5jb20gaHR0cDpcL1wvMTA2LjM4LjIyNy4yNyJdfQ==';

        $upHost = $this->zone->getUpHostByToken($uptoken_bc);
        $this->assertEquals('http://up-z1.qiniu.com', $upHost);

        $upHostBackup = $this->zone->getBackupUpHostByToken($uptoken_bc);
        $this->assertEquals('http://upload-z1.qiniu.com', $upHostBackup);


        $uptoken_bc_https = 'QWYn5TFQsLLU1pL5MFEmX3s5DmHdUThav9WyOWOm:7I47O-vFcN5TKO
        6D7cobHPVkyIA=:eyJzY29wZSI6InBocHNkay1iYyIsImRlYWRsaW5lIjoxNDcwNzIyNzQ1LCJ1c
        Ehvc3RzIjpbImh0dHBzOlwvXC91cC16MS5xYm94Lm1lIl19';
        $upHost = $this->zoneHttps->getUpHostByToken($uptoken_bc_https);
        $this->assertEquals('https://up-z1.qbox.me', $upHost);

        $upHostBackup = $this->zoneHttps->getBackupUpHostByToken($uptoken_bc_https);
        $this->assertEquals('https://up-z1.qbox.me', $upHostBackup);
    }

    public function testIoHosts()
    {

        $ioHost = $this->zone->getIoHost($this->ak, $this->bucketName);
        $this->assertEquals('http://iovip.qbox.me', $ioHost);

        $ioHost = $this->zone->getIoHost($this->ak, $this->bucketNameBC);
        $this->assertEquals('http://iovip-z1.qbox.me', $ioHost);

        $ioHost = $this->zoneHttps->getIoHost($this->ak, $this->bucketName);
        $this->assertEquals('https://iovip.qbox.me', $ioHost);

        $ioHost = $this->zoneHttps->getIoHost($this->ak, $this->bucketNameBC);
        $this->assertEquals('https://iovip-z1.qbox.me', $ioHost);
    }
}
