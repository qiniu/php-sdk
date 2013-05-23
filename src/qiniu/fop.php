<?php
require_once('rpc.php');

class ImageView
{
	public $mode = 1; // 1或2
	public $width; // width 默认为0，表示不限定宽度
	public $height;
	public $quality; // 图片质量, 1-100
	public $format; // 输出格式, jpg, gif, png, tif 等图片格式
	
	public function makeRequest($url) 
	{
		$url .= '?imageView/' . $this->mode;
		
		if ($this->width > 0) {
			$url .= '/w/' . $this->width;
		}
		
		if ($this->height > 0) {
			$url .= '/h/' . $this->height;
		}
		
		if ($this->quality > 0) {
			$url .= '/q/' . $this->quality;
		}
		
		if ($this->format != '') {
			$url .= '/format/' . $this->format;
		} 
		
		return $url;
	}
}


class ImageMogr
{
	public $autoOrient = false; 
	public $thumbnail; 
	public $gravity;
	public $crop; 
	public $quality;
	public $rotate;
	public $format;
	
	/*
	 * 图像处理接口，生成图像处理的参数
	*   "thumbnail": <ImageSizeGeometry>, 缩略图尺寸
	*   "gravity": <GravityType>, =NorthWest, North, NorthEast, West, Center, East, SouthWest, South, SouthEast
	*   "crop": <ImageSizeAndOffsetGeometry>, 裁剪尺寸
	*   "quality": <ImageQuality>,
	*   "rotate": <RotateDegree>, 旋转角度, 单位为度
	*   "format": <DestinationImageFormat>, =jpg, gif, png, tif, etc.
	*   "auto_orient": <TrueOrFalse> 根据原图EXIF信息自动旋正
	*/
	public function makeRequest($url)
	{
		$url .= '?imageMogr';
		if (isset($this->thumbnail) && !empty($this->thumbnail)) {
			$url .= '/thumbnail/' . $this->thumbnail;	
		}
		
		if (isset($this->gravity) && !empty($this->gravity)) {
			$url .= '/gravity/' . $this->gravity;
		}
		
		if (isset($this->crop) && !empty($this->crop)) {
			$url .= '/crop/' . $this->crop;
		}
		
		if (isset($this->quality) && !empty($this->quality)) {
			$url .= '/quality/' . $this->quality;
		}
		
		if (isset($this->rotate) && !empty($this->rotate)) {
			$url .= '/rotate/' . $this->rotate;
		}
		
		if (isset($this->format) && !empty($this->format)) {
			$url .= '/format/' . $this->format;
		}
		
		if (isset($this->autoOrient) && $this->autoOrient === true) {
			$url .= '/auto-orient';
		}
		
		return $url;
	}	
}


class ImageExif
{
	public function makeRequest($url)
	{
		return $url . '?exif';
	}
	
	public function call($url)
	{
		$rpc = new Client($this->makeRequest($url));
		return $rpc->call("");
	}
}


class ImageInfo
{
	public function makeRequest($url)
	{
		return $url . '?imageInfo';
	}
	
	public function call($url)
	{
		$client = new Client($this->makeRequest($url));
		return $client->call("");
	}
}