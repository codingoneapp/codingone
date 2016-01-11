<?php
class Compress{
	public $devpath;
	public $propath;
	public $needcomp = false;
	function __construct($needcomp=false){
		$this->devpath = YYUC_FRAME_PATH.'view/'.Conf::$view_folder.'/@style/';
		$this->propath = YYUC_FRAME_PATH.YYUC_PUB.'/';
		$this->needcomp = $needcomp;
	}
	/**
	 * 压缩JS
	 */	
	public function cpcss(){
		$this->tocpcss('css');
	}
	public function tocpcss($path){
		if(!is_dir($this->devpath.$path)){
			if(strpos(strtolower($path), '.css')==strlen($path)-4){
				if(file_exists($this->propath.$path)&&filemtime($this->devpath.$path)<filemtime($this->propath.$path)){
					return;
				}
				File::creat_dir_with_filepath($this->propath.$path);
				//如果传入的是JS文件则压缩
				$buffer = file_get_contents($this->devpath.$path);
				if($this->needcomp){
					$buffer = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $buffer);
					$arr = array("\r\n", "\r", "\n", "\t", "  ", "    ", "    ") ;
					$buffer = str_replace($arr, "", $buffer);
				}				
				if(empty(Conf::$remote_path)){
					Conf::$remote_path = '/';
				}else if(strlen(Conf::$remote_path)-1!=strrpos(Conf::$remote_path, '/')){
					Conf::$remote_path = Conf::$remote_path.'/';
				}
				$buffer = str_replace("img@", Conf::$remote_path."media/images/", $buffer);
				$buffer = str_replace("vid@", Conf::$remote_path."media/videos/", $buffer);
				$buffer = str_replace("sou@", Conf::$remote_path."media/sounds/", $buffer);
				$buffer = str_replace("ani@", Conf::$remote_path."media/animations/", $buffer);				
				file_put_contents($this->propath.$path, $buffer);
			}else{
				@copy($this->devpath.$path, $this->propath.$path);
			}
			return; 
		}
		//如果传入的参数是目录
		$handle = File::scandir($this->devpath.$path);
		foreach ($handle as $file){
			if($file!='.'&&$file!='..'){
				$dir = $path . '/' . $file; //当前文件$dir为文件目录+文件
				$this->tocpcss($dir);
			} 
		}
		return ; 
	}
	/**
	 * 压缩JS
	 */	
	public function cpjs(){
		$this->tocpjs('js');
	}
	public function tocpjs($path){
		if(!is_dir($this->devpath.$path)){
			if(strpos(strtolower($path), '.js')==strlen($path)-3){
				if(file_exists($this->propath.$path)&&filemtime($this->devpath.$path)<filemtime($this->propath.$path)){
					return;
				}
				File::creat_dir_with_filepath($this->propath.$path);
				$buffer = file_get_contents($this->devpath.$path);
				//如果传入的是JS文件则压缩
				if(!$this->needcomp){
					file_put_contents($this->propath.$path, $buffer);
				}else{
					$packer = new JavaScriptPacker($buffer, 'Normal', true, false);					
					file_put_contents($this->propath.$path, $packer->pack());
				}
			}else{
				@copy($this->devpath.$path, $this->propath.$path);
			}		
			return; 
		}
		//如果传入的参数是目录
		$handle = File::scandir($this->devpath.$path);
		foreach ($handle as $file){
			if($file!='.'&&$file!='..'){
				$dir = $path . '/' . $file; //当前文件$dir为文件目录+文件
				$this->tocpjs($dir);
			} 
		}
		return ; 
	}
}
?>