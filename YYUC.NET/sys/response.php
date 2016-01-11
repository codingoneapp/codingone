<?php
class Response{
	
	/**
	 * 修改请求输出的mime类型
	 * 
	 * @param Mime $mime 要输出mime类型
	 */
	public static function mime($mime){
		header('Content-type: '.$mime);
	}
	
	/**
	 * 文本输出 ,输出后退出此次请求
	 * @param string $str 要输出的字串
	 * @param Mime $mime 要输出mime类型
	 */
	public static function write($str, $mime = null){
		$str = strval($str);
		if($mime === null){
			$mime = Mime::$text;
		}
		
		if(is_array($mime)){
			$mime = $mime[0];
		}
		
		header('Content-type: '.$mime);
		echo trim($str);
		exit();
	}
	/**
	 * 文本输出 ,输出后不退出此次请求
	 * @param string $str 要输出的字串
	 * @param Mime $mime 要输出mime类型
	 */
	public static function text($str, $mime = null){
		Page::ignore_view();
		$str = strval($str);
		if($mime === null){
			$mime = Mime::$text;
		}
	
		if(is_array($mime)){
			$mime = $mime[0];
		}
	
		header('Content-type: '.$mime);
		echo trim($str);
	}
	/**
	 * json输出 ,输出后退出此次请求
	 * @param array $arr 要输出的数组
	 */
	public static function json($arr){
		if(!empty($arr)){
			self::write(json_encode($arr), Mime::$json);
		}elseif(is_array($arr)){
			self::write("[]", Mime::$json);
		}else{
			self::write("{}", Mime::$json);
		}		
	}
	
	/**
	 * 客户端直接执行JS语句!!!此方法只在加载了视图文件的情况下可用<br/>
	 * 所添加的JS代码会在页面前期初始化工作完成之后执行
	 * @param string $jscode 要执行的JS脚本
	 */
	public static function exejs($jscode){
		Page::$js_arr[] = $jscode;
	}
	
	
	/**
	 * 准备为客户端进行文件下载<br/>
 	 * @param string $filename 客户端下载的文件名
	 * @param mixed $content 客户端下载的文件内容，如不传入则把这次请求的实际相应内容作为文件内容
	 * @param Mime $mime 要输出mime类型默认为：APPLICATION
 	*/
	public static function download($filename,$content = null, $mime = null){
		header( "Cache-Control: public" );
		header( "Pragma: public" );		
		header( "Content-Disposition: attachment; filename=".str_replace('+', '%20', urlencode($filename))."" ) ;
		if($content != null){
			self::write($content,$mime);
		}elseif($mime != null){
			if(is_array($mime)){
				$mime = $mime[0];
			}
			header('Content-type: '.$mime);
		}else{
			header("Content-type: APPLICATION/OCTET-STREAM") ;
		}
	}
}