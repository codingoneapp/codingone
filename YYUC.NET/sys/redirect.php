<?php

class Redirect{
	
	/**
	 * 跳转的时候是否强制不走缓存
	 * @var string
	 */
	public static $renew = false;
	
	/**
	 * 跳转到指定url
	 * 运行后直接退出此次请求
	 * @param string $url 客户端跳转的URL绝对路径或者控制器路径<br>根目录控制器请开头补“/”
	 * @param mixed $data 带着数据跳转
	 */
	public static function to($url,$data = null,$status='HTTP/1.1 301'){
		$url = yyuc::url($url);
		if(self::$renew){
			$url = $url.'@YYUC_RENEW';
		}
		header($status);
		header("Location:$url");
		//echo '<meta http-equiv="Refresh" content="0"; url="'.$url.'" />';
		if($data !==null){
			Session::once('YYUC_REDIRECT_DATA',$data);
		}
		echo 'This page has moved to <a href="'.$url.'">'.$url.'</a>';
		die();
	}
	
	/**
	 * iframe嵌套指定url
	 * 运行后直接退出此次请求
	 * @param string $url 客户端跳转的URL
	 */
	public static function iframe($url,$title='',$description='',$keywords=''){
		$url = yyuc::url($url);
		$jquery = trim(Conf::$jquery_path)==''?(Page::asset('@system/js/').'jquery.js'):Conf::$jquery_path;
		$appdenstr = '<script type="text/javascript" src="'.$jquery.'"></script><script type="text/javascript">function fitif(){jQuery("#mainoem").width(jQuery(window).width());jQuery("#mainoem").height(jQuery(window).height());jQuery("#mainoem").attr("src","'.$url.'");}jQuery(fitif);jQuery(window).resize(fitif);</script>';
		echo '<html><head><title>'.htmlentities($title).'</title>'.$appdenstr.'<meta name="keywords" content="'.htmlentities($keywords).'"/><meta name="description" content="'.htmlentities($description).'"/></head><body style="margin: 0;padding: 0;overflow: hidden;"><iframe style="border: none;width: 100%;height: 100%;" src="about:blank"  frameborder="no" border="0"  id="mainoem"></iframe></body></html>';
		die();
	}
	/**
	 * 等待一段时间后跳转到指定url
	 * 运行后仍然正常显示页面
	 * @param string $url 客户端跳转的URL绝对路径或者控制器路径<br>根目录控制器请开头补“/”
	 * @param integer $time 延时时间
	 * @param mixed $data 带着数据跳转
	 */
	public static function delay_to($url,$time = 5,$data = null){
		$url = yyuc::url($url);
		if($time == 0){
			//纯JS跳转
			Response::write("<script>location.href='".$url."';</script>",Mime::$htm);
			die();
			
		}else{
			$tzpam = '';
			if(is_array($data)){
				foreach ($data as $k=>$v){
					$tzpam .= "<input type='hidden' name='".$k."' value='".htmlentities($v)."'/>";
				}				
			}else{
				$tzpam = "<input type='hidden' name='yyuc_pam' value='1'/>";
			}
			Response::write("<form action='".$url."' method='get' id='yyucform'>".$tzpam."</form><script>document.getElementById('yyucform').submit();</script>");
			die();
		}		
	}
	
	/**
	 * 取得跳转到此页时的附带数据信息
	 */
	public static function ignore_cache(){
		self::$renew = true;
	}
	
	/**
	 * 取得跳转到此页时的附带数据信息
	 */
	public static function last_data(){
		return Session::flush('YYUC_REDIRECT_DATA');
	}
	
	/**
	 * 跳转到404页面
	 * 运行后直接退出此次请求
	 */
	public static function to_404(){
		if(is_callable('rescue_404')){
			$ruri = $_SERVER['REAL_REQUEST_URI'];
			$ruril = strlen($ruri);
			if(strrpos($ruri, '/index') == $ruril-6){
				$ruri = substr($ruri, 0,$ruril-6);
			}
			rescue_404($ruri);
		}
		header('HTTP/1.1 404 Not Found');
		header("status: 404 Not Found");
		include YYUC_FRAME_PATH.'view/'.Conf::$view_folder.'/404'.Conf::$view_suffix;
		exit();
	}
	
	/**
	 * 跳转到错误页面
	 * 运行后直接退出此次请求
	 * @param string $err_msg 错误信息
	 */
	public static function to_500($err_msg){
		header('HTTP/1.1 500 Internal Server Error');
		header("status: 500 Internal Server Error");
		include YYUC_FRAME_PATH.'view/'.Conf::$view_folder.'/500'.Conf::$view_suffix;
		exit();
	}
	
	/**
	 * 返回请求前的页面
	 * 如果为浏览器直接输入 则报出错误<br>
	 * 因为此方法基于前台JS的goto方法和REFERER实现的
	 */
	public static function back($data = null){
		if(isset($_SERVER['HTTP_REFERER'])){
			self::to($_SERVER['HTTP_REFERER'],$data);
		}else{
			die('错误的请求来源');
		}
		exit();
	}
	
	/**
	 * 返回请求的前一不同页面，一般用于保存后跳转
	 * 如果为浏览器直接输入 则报出错误<br>
	 */
	public static function prev($data = null){
		if(isset($_SESSION['YYUC_HTTP_REFERER_OLD'])){
			self::to($_SESSION['YYUC_HTTP_REFERER_OLD'],$data);
		}else{
			self::back();
		}
		exit();
	}
	
	/**
	 * 返回请求的前一不同页面，如果不存在则到指定页面
	 * @param string $url 客户端跳转的URL绝对路径或者控制器路径<br>根目录控制器请开头补“/”
	 */
	public static function prev_or_to($url,$data = null){
		if(isset($_SESSION['YYUC_HTTP_REFERER_OLD'])){
			self::to($_SESSION['YYUC_HTTP_REFERER_OLD'],$data);
		}else{
			self::to($url,$data);
		}
		exit();
	}
	
	/**
	 * 301重定向
	 * @param string $url 客户端跳转的URL绝对路径或者控制器路径<br>根目录控制器请开头补“/”
	 */
	public static function to_301($url){
		self::to($url,null,'HTTP/1.1 301 Moved Permanently');
		echo 'This page has moved to <a href="'.$url.'">'.$url.'</a>';
		exit();
	}
		
	/**
	 *直接跳转到网站首页
	 */
	public static function index($data = null){
		self::to(Conf::$http_path,$data);
		exit();
	}
	
	/**
	 *刷新当前请求的页面，通常用于过滤掉POST请求的F5刷新
	 */
	public static function refresh(){
		self::to(Conf::$http_path.$_SERVER["REQUEST_URI"]);
		exit();
	}
}
?>