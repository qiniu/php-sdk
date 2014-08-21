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

class Qiniu_Watermark {

    public $imgUrl;
    public $dissolve;
    public $gravity;
    public $dx;
    public $dy;

    public function MakeRequest($url)
    {
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
                return $url;
                break;
            
            case 1 :
                return $url.'?watermark/1/'.$ops[0];
                break;
            
            default :
                return $url.'?watermark/1/'.implode('/', $ops);
        }
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