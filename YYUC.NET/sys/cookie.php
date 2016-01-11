<?php
class Cookie{	

	/**
	 * Cookie设置 默认过期时间是一个月,范围是整个domain
	 * 
	 * @param string $k Cookie 键
	 * @param string $v Cookie 值 传入null则为清空Cookie
	 * @param boolean $httponly 是否是禁止客户端操作的
	 * @param integer $time 过期时间
	 * @param string $domain 适用范围
	 * @param string $path 使用路径
	 * @param boolean $secure 是否是安全(https)的
	 */
	public static function set($k,$v=null,$httponly = false,$time=null,$domain=null,$path='/',$secure=false){
		if($v === null){
			$_COOKIE[$k] = null;
			unset($_COOKIE[$k]);
			setcookie($k,null,time()-100,$path,$domain,$secure,$httponly);
		}else{
			$time = isset($time)?$time:time()+3600*24*30;
			$_COOKIE[$k] = $v;
			setcookie($k,$v,$time,$path,$domain,$secure,$httponly);
		}
	}
	
	/**
	 * 安全取得Cookie内容
	 * @param string $k Cookie 键
	 * @return string cookie内容
	 */
	public static function get($k,$def=null){
		return isset($_COOKIE[$k]) ? $_COOKIE[$k] : $def;
	}
	
	/**
	 * 判断是否含有指定的Cookie内容
	 * @param string $k Cookie 键
	 * @return boolean
	 */
	public static function has($k){
		return isset($_COOKIE[$k]) ? true : false;
	}
	
	/**
	 * 删除指定的Cookie内容
	 * @param string $k Cookie 键
	 */
	public static function remove($k){
		return self::set($k);
	}
	
	/**
	 * 清空Cookie
	 */
	public static function clear_all(){
		foreach ($_COOKIE as $k=>$v){			
			self::set($k);
		}
	}
	/**
	 * 清空全局Cookie 一般不可调用 否则会造成系统错误
	 */
	public static function clear(){
		foreach ($_COOKIE as $k=>$v){
			if(strpos($k, 'SYS_')!==0){
				self::set($k);
			}
			
		}
	}
}