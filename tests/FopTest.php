<?php

require_once("bootstrap.php");

class FopTest extends PHPUnit_Framework_TestCase
{
	public $url = 'http://phpsdk.qiniudn.com/f22.jpeg';

	public function testImageView()
	{
		$imageView = new Qiniu_ImageView();
		$imageView->Mode = 1;
		$imageView->Width = 80;
		$imageView->Format = 'jpg';

		$url = $this->url;

		$expectedUrl = $url . '?imageView/1/w/80/format/jpg';
		$this->assertEquals($imageView->MakeRequest($url), $expectedUrl);
	}

	public function testExif()
	{
		$exif = new Qiniu_Exif();
		$url = $this->url;
		$expectedUrl = $url . '?exif';
		$this->assertEquals($exif->MakeRequest($url), $expectedUrl);
	}

	public function testImageInfo()
	{
		$imageView = new Qiniu_ImageInfo();
		$url = $this->url;
		$expectedUrl = $url . '?imageInfo';

		$this->assertEquals($imageView->MakeRequest($url), $expectedUrl);
	}
}
