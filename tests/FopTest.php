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

	public function testWatermark()
	{
		$watermark = new Qiniu_Watermark();
		$url = $this->url;
		$imgUrl = 'http://www.b1.qiniudn.com/images/logo-2.png';
		$dissolve = 50;
		$gravity = 'SouthEast';
		$dx = 20;
		$dy = 20;
		$ops = array();

        if(!empty($this->imgUrl)){
            $ops[] = 'image/'.$this->urlsafe_base64_encode($this->imgUrl);
        }
        if(!empty($this->dissolve)){
            $ops[] = 'dissolve/'.$this->dissolve;
        }
        if(!empty($this->gravity)){
            $ops[] = 'gravity/'.$this->gravity;
        }
        if(!empty($this->dx)){
            $ops[] = 'dx/'.$this->dx;
        }
        if(!empty($this->dy)){
            $ops[] .= 'dy/'.$this->dy;
        }
        switch(count($ops)){
            case 0 :
                $expectedUrl = $url;
                break;
            
            case 1 :
                $expectedUrl = $url.'?watermark/1/'.$ops[0];
                break;
            
            default :
                $expectedUrl = $url.'?watermark/1/'.implode('/', $ops);
        }
		$this->assertEquals($watermark->MakeRequest($url), $expectedUrl);
	}

	private function urlsafe_base64_encode($imgUrl)
    {
        $base64Url = base64_encode($imgUrl);
        if(strpos($base64Url,'+') !== false && strpos($base64Url,'/') !== false){    
            $base64Url = str_replace(array('+', '/'), array('-', '_'), $base64Url).'=';
        }
        return $base64Url;
    }
}
