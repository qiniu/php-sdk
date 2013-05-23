<?php
require_once('../src/qiniu/fop.php');

class FopTest extends PHPUnit_Framework_TestCase
{
	public $url;
	public $notExistUrl;
	public $imageExif;
	public $imageView;
	public $imageInfo;
	public $imageMogr;
	
	
	public function setUp()
	{
		$this->url = "http://phpsdk.qiniudn.com/pic_key2";
		$this->notExistUrl = "http://phpsdk.qiniudn.com/pic_key3";
		$this->imageExif = new ImageExif();	
		$this->imageView = new ImageView();	
		$this->imageInfo = new ImageInfo();
		$this->imageMogr = new ImageMogr();
	}
	
	public function testImageExif()
	{
		list($ret, $err) = $this->imageExif->call($this->url);
		$this->assertNull($err);
		$this->assertFalse($ret === null);
		
		list($ret, $err) = $this->imageExif->call($this->notExistUrl);
		$this->assertNull($ret);
		$this->assertFalse($err === null);
	}
	
	public function testImageView()
	{
		$this->imageView->mode = 2;
		$this->imageView->format = 'gif';
		$assertUrl = $this->imageView->makeRequest($this->url);
		$this->assertEquals($assertUrl, $this->url . '?imageView/2/format/gif');
		
		$this->imageView->width = 30;
		$this->imageView->height = 90;
		$this->imageView->quality = 80;
		$assertUrl= $this->imageView->makeRequest($this->url);
		$this->assertEquals($assertUrl, $this->url . '?imageView/2/w/30/h/90/q/80/format/gif');
	}
	
	public function testImageInfo()
	{
		list($ret, $err) = $this->imageInfo->call($this->url);
		$this->assertNull($err);
		$this->assertFalse($ret === null);
		
		list($ret, $err) = $this->imageInfo->call($this->notExistUrl);
		$this->assertFalse($err === null);
	}
	
	public function testImageMogr()
	{
		$this->imageMogr->quality = 70;
		$this->imageMogr->format = 'png';
		$this->imageMogr->gravity = 'Center';
		$assertUrl = $this->imageMogr->makeRequest($this->url);

		$this->assertEquals($assertUrl, $this->url . '?imageMogr/gravity/Center/quality/70/format/png');
	}	
	
}










