<?php
class Image{
	/**
	 * 图片加水印（适用于png/jpg/gif格式）
	 *
	 * @author fly
	 *
	 * @param $srcImg    原图片
	 * @param $waterImg  水印图片
	 * @param $savepath  保存路径
	 * @param $savename  保存名字
	 * @param $positon   水印位置
	 *                   1:顶部居左, 2:顶部居右, 3:居中, 4:底部局左, 5:底部居右
	 * @param $alpha     透明度 -- 0:完全透明, 100:完全不透明
	 *
	 * @return 成功 -- 加水印后的新图片地址
	 *      失败 -- -1:原文件不存在, -2:水印图片不存在, -3:原文件图像对象建立失败
	 *              -4:水印文件图像对象建立失败 -5:加水印后的新图片保存失败
	 */
	public static function water_mark($srcImg, $waterImg, $positon=5, $alpha=30 , $savepath=null, $savename=null){
		$temp = pathinfo($srcImg);
		$name = $temp[basename];
		$path = $temp[dirname];
		$exte = $temp[extension];
		$savename = $savename ? $savename : $name;
		$savepath = $savepath ? $savepath : $path;
		$savefile = $savepath .'/'. $savename;
		$srcinfo = @getimagesize($srcImg);
		if (!$srcinfo) {
			return -1;  //原文件不存在
		}
		$waterinfo = @getimagesize($waterImg);
		if (!$waterinfo) {
			return -2;  //水印图片不存在
		}
		$srcImgObj = self::create_from_ext($srcImg);
		if (!$srcImgObj) {
			return -3;  //原文件图像对象建立失败
		}
		$waterImgObj = self::create_from_ext($waterImg);
		if (!$waterImgObj) {
			return -4;  //水印文件图像对象建立失败
		}
		switch ($positon) {
			//1顶部居左
			case 1: $x=$y=0; break;
			//2顶部居右
			case 2: $x = $srcinfo[0]-$waterinfo[0]; $y = 0; break;
			//3居中
			case 3: $x = ($srcinfo[0]-$waterinfo[0])/2; $y = ($srcinfo[1]-$waterinfo[1])/2; break;
			//4底部居左
			case 4: $x = 0; $y = $srcinfo[1]-$waterinfo[1]; break;
			//5底部居右
			case 5: $x = $srcinfo[0]-$waterinfo[0]; $y = $srcinfo[1]-$waterinfo[1]; break;
			default: $x=$y=0;
		}
		//创建一个和图片2一样大小的真彩色画布（ps：只有这样才能保证后面copy图片1的时候不会失真）
		$image_3 = imageCreatetruecolor($srcinfo[0],$srcinfo[1]);
		//为真彩色画布创建白色背景，再设置为透明
		$color = imagecolorallocate($image_3, 255, 255, 255);
		imagefill($image_3, 0, 0, $color);
		imageColorTransparent($image_3, $color);
		//首先将图片2画布采样copy到真彩色画布中，不会失真
		imagecopyresampled($image_3,$srcImgObj,0,0,0,0,$srcinfo[0],$srcinfo[1],$srcinfo[0],$srcinfo[1]);		
		//将画布保存到指定的gif文件		
		
		if($waterinfo[2] !==3 ){
			imagecopymerge($image_3, $waterImgObj, $x, $y, 0, 0, $waterinfo[0], $waterinfo[1], $alpha);
		}else{
			imagecopy($image_3, $waterImgObj, $x, $y, 0, 0, $waterinfo[0], $waterinfo[1]);
		}		
		switch ($srcinfo[2]) {
			case 3: imagepng($image_3, $savefile,0); break;
			case 1: imagegif($image_3, $savefile); break;
			case 2: imagejpeg($image_3, $savefile,100); break;
			
			default: return -5;  //保存失败
		}
		return $savefile;
	}
	
	/**
	 * 从路径中建立图像资源
	 * 
	 * @param resource $imgfile
	 */
	public static function create_from_ext($imgfile)
	{
		$info = getimagesize($imgfile);
		$im = null;
		switch ($info[2]) {
			case 1: $im=imagecreatefromgif($imgfile); break;
			case 2: $im=imagecreatefromjpeg($imgfile); break;
			case 3: $im=imagecreatefrompng($imgfile); break;
		}
		return $im;
	}
	
	
	/**
	 * 
	 * 直接改变图片大小
	 * @param $image
	 * @param $dw
	 * @param $dh
	 * @return boolean
	 */
	public static function change_size($image,$dw=450,$dh=450,$npath=null){
		if(!file_exists($image)){
			return false;
		}
		if(!$npath){
			$npath = $image;
		}
		$img= self::create_from_ext($image);
		//创建一个和图片2一样大小的真彩色画布（ps：只有这样才能保证后面copy图片1的时候不会失真）
		$nimg = imageCreatetruecolor($dw,$dh);
		//为真彩色画布创建白色背景，再设置为透明
		$color = imagecolorallocate($nimg, 255, 255, 255);
		imagefill($nimg, 0, 0, $color);
		imageColorTransparent($nimg, $color);
		//首先将图片2画布采样copy到真彩色画布中，不会失真
		
		#如果是执行调整尺寸操作则
		$width = imagesx($img);
		$height = imagesy($img);
		imagecopyresampled($nimg,$img,0,0,0,0,$dw,$dh,$width,$height);#重采样拷贝部分图像并调整大小
		imagejpeg ($nimg,$npath,100);          #以jpeg格式将图像输出到浏览器或文件
		#取得文件的类型,根据不同的类型建立不同的对象
		return true;		
	}
	
	/**
	 *
	 * @param string $image 图片路径
	 * @param integer $dw 调整时最大宽度;缩略图时的绝对宽度
	 * @param integer $dh 调整时最大高度;缩略图时的绝对高度
	 * @param integer $type 1,调整尺寸; 2,生成缩略图
	 * @return boolean
	 */
	public static function optimize($image,$dw=450,$dh=450,$type=1){
		if(!file_exists($image)){
			return false;
		}
		#如果需要生成缩略图,则将原图拷贝一下重新给$image赋值
		if($type !=1 ){
			copy($image,str_replace(".","_x.",$image));
			$image=str_replace(".","_x.",$image);
		}
		#取得文件的类型,根据不同的类型建立不同的对象
		$img= self::create_from_ext($image);
		//imagesavealpha($img, true);
		#如果对象没有创建成功,则说明非图片文件
		if(empty($img)){
			#如果是生成缩略图的时候出错,则需要删掉已经复制的文件
			if($type!=1){
			unlink($image);
		}
		return false;
		}
		#如果是执行调整尺寸操作则
		$w = $width = imagesx($img);
		$h = $height = imagesy($img);
		if($type==1){			
			if($width>$dw){
				$par=$dw/$width;
				$width=$dw;
				$height=$height*$par;
				if($height>$dh){
					$par=$dh/$height;
					$height=$dh;
					$width=$width*$par;
				}
			}elseif($height>$dh){
				$par=$dh/$height;
				$height=$dh;
				$width=$width*$par;
				if($width>$dw){
					$par=$dw/$width;
					$width=$dw;
					$height=$height*$par;
				}
			}
			$nimg = imagecreatetruecolor($width,$height);
			$alpha = imagecolorallocatealpha($img, 0, 0, 0, 127);
			imagefill($nimg, 0, 0, $alpha);
			imagecopyresampled($nimg,$img,0,0,0,0,$width,$height,$w,$h);
			imagesavealpha($nimg, true);
			imagepng($nimg,$image);
			return true;
			#如果是执行生成缩略图操作则
		}else{
			$nimg = imagecreatetruecolor($dw,$dh);
			if($h/$w>$dh/$dw){ #高比较大
				$width=$dw;
				$height=$h*$dw/$w;
				$intnh=$height-$dh;
				imagecopyresampled($nimg, $img, 0, -$intnh/1.8, 0, 0, $dw, $height, $w, $h);
			}else{     #宽比较大
				$height=$dh;
				$width=$w*$dh/$h;
				$intnw=$width-$dw;
				imagecopyresampled($nimg, $img, -$intnw/1.8, 0, 0, 0, $width, $dh, $w, $h);
			}
			imagejpeg ($nimg,$image);
			return true;
		}
	}
	
	public static function samlltojpg($image,$dw=450,$dh=450,$isjpeg=true){
		if(!file_exists($image)){
			return false;
		}
		#取得文件的类型,根据不同的类型建立不同的对象
		$img= self::create_from_ext($image);
		if(empty($img)){
			return false;
		}
		#如果是执行调整尺寸操作则
		$w = $width = imagesx($img);
		$h = $height = imagesy($img);
		$needsmall = false;
		if($w>$dw){
			$dh = intval(floatval($dw)/floatval($w)*$h);
			$needsmall = true;
		}
		if($h>$dh){
			$dw = intval(floatval($dh)/floatval($h)*$w);
			$needsmall = true;
		}
		if($needsmall){
			$nimg = imagecreatetruecolor($dw,$dh);
			$alpha = imagecolorallocatealpha($nimg, 0, 0, 0, 127);
			imagefill($nimg, 0, 0, $alpha);
			if($h/$w>$dh/$dw){ #高比较大
				$width=$dw;
				$height=$h*$dw/$w;
				$intnh=$height-$dh;
				imagecopyresampled($nimg, $img, 0, -$intnh/1.8, 0, 0, $dw, $height, $w, $h);
			}else{     #宽比较大
				$height=$dh;
				$width=$w*$dh/$h;
				$intnw=$width-$dw;
				imagecopyresampled($nimg, $img, -$intnw/1.8, 0, 0, 0, $width, $dh, $w, $h);
			}
			if($isjpeg){
				imagejpeg ($nimg,$image);
			}else{
				imagesavealpha($nimg, true);
				imagepng($nimg,$image);
			}
			
		}else{
			if($isjpeg){
				imagejpeg ($img,$image);
			}else{
				imagesavealpha($img, true);
				imagepng($img,$image);
			}
		}
		
		return true;
	}
	
	public static function samlltopng($image,$dw=450,$dh=450){
		return self::samlltojpg($image,$dw,$dh,false);
	}
	
	public static function set_radius($path,$radius,$npath=null){
		if($npath==null){
			$npath = $path;
		}
		$rounder = new RoundedCorner($path, $radius);
		$iimg = $rounder->round_it();
		imagepng($iimg,$npath);		
	}
}


class RoundedCorner {
	private $_r;
	private $_g;
	private $_b;
	private $_image_path;
	private $_radius;
	private $_oldimg;
	private $_image_width;
	private $_image_height;

	function __construct($image_path, $radius, $r = 255, $g = 0, $b = 0) {
		$this->_oldimg = Image::create_from_ext($image_path);
		$this->_image_path = $image_path;
		$this->_image_width = imagesx($this->_oldimg);
		$this->_image_height = imagesy($this->_oldimg);
		if($radius==null){
			$radius = intval($this->_image_height/8);
		}
		$this->_radius = $radius;
		$this->_r = (int)$r;
		$this->_g = (int)$g;
		$this->_b = (int)$b;
	}

	private function _get_lt_rounder_corner() {
		$radius = $this->_radius;
		$img = imagecreatetruecolor($radius, $radius);
		$bgcolor = imagecolorallocate($img, $this->_r, $this->_g, $this->_b);
		$fgcolor = imagecolorallocate($img, 0, 0, 0);
		imagefill($img, 0, 0, $bgcolor);
		imagefilledarc($img, $radius, $radius, $radius*2, $radius*2, 180, 270, $fgcolor, IMG_ARC_PIE);
		imagecolortransparent($img, $fgcolor);
		return $img;
	}


	public function round_it() {
		// load the source image
		$src_image = $this->_oldimg;
		if ($src_image === false) {
			die();
		}
		$image_width = $this->_image_width;
		$image_height = $this->_image_width;

		// create a new image, with src_width, src_height, and fill it with transparent color
		$image = imagecreatetruecolor($image_width, $image_height);
		$trans_color = imagecolorallocate($image, $this->_r, $this->_g, $this->_b);
		imagefill($image, 0, 0, $trans_color);

		// then overwirte the source image to the new created image
		imagecopymerge($image, $src_image, 0, 0, 0, 0, $image_width, $image_height, 100);

		// then just copy all the rounded corner images to the 4 corners
		$radius = $this->_radius;
		// lt
		$lt_corner = $this->_get_lt_rounder_corner();
		imagecopymerge($image, $lt_corner, 0, 0, 0, 0, $radius, $radius, 100);
		// lb
		$lb_corner = imagerotate($lt_corner, 90, $trans_color);
		imagecopymerge($image, $lb_corner, 0, $image_height - $radius, 0, 0, $radius, $radius, 100);
		// rb
		$rb_corner = imagerotate($lt_corner, 180, $trans_color);
		imagecopymerge($image, $rb_corner, $image_width - $radius, $image_height - $radius, 0, 0, $radius, $radius, 100);
		// rt
		$rt_corner = imagerotate($lt_corner, 270, $trans_color);
		imagecopymerge($image, $rt_corner, $image_width - $radius, 0, 0, 0, $radius, $radius, 100);

		// set the transparency
		imagecolortransparent($image, $trans_color);
		// display it
		imagedestroy($src_image);
		imagedestroy($lt_corner);
		imagedestroy($lb_corner);
		imagedestroy($rb_corner);
		imagedestroy($rt_corner);		
		return $image;
	}
}


		
