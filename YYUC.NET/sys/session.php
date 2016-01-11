<?php
class Session{	
	/**
	 * Session设置
	 * @param string $k Session键
	 * @param string $v Session值 传入null则为清空Session
	 */
	public static function set($k,$v=null){
		if($v === null){
			$_SESSION[md5(Conf::$session_prefix).$k] = null;
			unset($_SESSION[md5(Conf::$session_prefix).$k]);
		}else{
			$_SESSION[md5(Conf::$session_prefix).$k] = $v;
		}
	}
	
	/**
	 * 安全取得Session内容
	 * @param string $k Session键
	 * @return string Session内容
	 */
	public static function get($k,$def=null){
		return isset($_SESSION[md5(Conf::$session_prefix).$k]) ? $_SESSION[md5(Conf::$session_prefix).$k] : $def;
	}
	
	
	/**
	 * 判断Session是否含有指定内容
	 * @param string $k Session参数
	 * @return boolean
	 */
	public static function has($k){
		if(isset($_SESSION[md5(Conf::$session_prefix).$k])){
			return true;
		}
		return false;
	}
	
	/**
	 * 删除指定Session内容
	 * @param string $k Session键
	 */
	public static function remove($k){
		return self::set(md5(Conf::$session_prefix).$k);
	}
	
	/**
	 * 一次性Session显示信息存入
	 * @param string $k Session键
	 * @param string $v Session值 传入null则为清空Session
	 */
	public static function once($k,$v=null){
		if($v === null){
			$_SESSION[md5(Conf::$session_prefix).'YYUC_ONCE_'.$k] = null;
			unset($_SESSION[md5(Conf::$session_prefix).'YYUC_ONCE_'.$k]);
		}else{
			$_SESSION[md5(Conf::$session_prefix).'YYUC_ONCE_'.$k] = $v;
		}
	}
	
	/**
	 * 取得Session一次性显示内容
	 * @param string $k Session参数
	 * @return string Session内容
	 */
	public static function flush($k){
		if(isset($_SESSION[md5(Conf::$session_prefix).'YYUC_ONCE_'.$k])){
			$msg = $_SESSION[md5(Conf::$session_prefix).'YYUC_ONCE_'.$k];
			self::once($k);
			return $msg;
		}		
		return null;
	}
	
	/**
	 * 判断Session是否含有一次性显示内容
	 * @param string $k Session参数
	 * @return boolean
	 */
	public static function hold($k){
		if(isset($_SESSION[md5(Conf::$session_prefix).'YYUC_ONCE_'.$k])){
			return true;
		}
		return false;
	}
	
	/**
	 * 清空Session
	 */
	public static function clear(){
		foreach ($_SESSION as $k=>$v){
			if(strpos($k, md5(Conf::$session_prefix))===0){
				$_SESSION[$k] = null;
				unset($_SESSION[$k]);
			}			
		}
	}
	
	/**
	 * 清空全局Session，一般不可调用 否则会造成系统错误
	 */
	public static function clear_all(){
		foreach ($_SESSION as $k=>$v){
			$_SESSION[$k] = null;
			unset($_SESSION[$k]);
		}
	}
}