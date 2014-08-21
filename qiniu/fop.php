<?php

require_once("auth_digest.php");

// --------------------------------------------------------------------------------
// class Qiniu_ImageView

class Qiniu_ImageView {
	public $Mode;
    public $Width;
    public $Height;
    public $Quality;
    public $Format;

    public function MakeRequest($url)
    {
    	$ops = array($this->Mode);

    	if (!empty($this->Width)) {
    		$ops[] = 'w/' . $this->Width;
    	}
    	if (!empty($this->Height)) {
    		$ops[] = 'h/' . $this->Height;
    	}
    	if (!empty($this->Quality)) {
    		$ops[] = 'q/' . $this->Quality;
    	}
    	if (!empty($this->Format)) {
    		$ops[] = 'format/' . $this->Format;
    	}

    	return $url . "?imageView/" . implode('/', $ops);
    }
}

// --------------------------------------------------------------------------------
// class Qiniu_Exif

class Qiniu_Exif {

	public function MakeRequest($url)
	{
		return $url . "?exif";
	}

}

// --------------------------------------------------------------------------------
// class Qiniu_ImageInfo

class Qiniu_ImageInfo {

	public function MakeRequest($url)
	{
		return $url . "?imageInfo";
	}

}

// --------------------------------------------------------------------------------
// class Qiniu_watermark

class Qiniu_watermark {

    public $imgUrl;
    public $dissolve;
    public $gravity;
    public $dx;
    public $dy;

    public function MakeRequest($url)
    {
        return $url . "?watermark/1".'/image/'.$this->urlsafe_base64_encode($this->imgUrl).'/dissolve/'.$this->dissolve.'/gravity/'.$this->gravity.'/dx/'.$this->dx.'/dy/'.$this->dy;
    }

    private function urlsafe_base64_encode($imgUrl)
    {
        $base64Url = base64_encode($imgUrl);
        $base64Url = str_replace(array('+', '/'), array('-', '_'), $base64Url).'=';
        return $base64Url;
    }

}