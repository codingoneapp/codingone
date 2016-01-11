<?php
/**
 * 用户认证
 * 
 * @author mqq
 *
 */
class Auth{
	
	/**
	 * 管理员声明	
	 * @param mixed $adminlevel 可以不填，管理员级别组(string|array)
	 */
	public static function im_admin($adminlevel=null){
		if($adminlevel==null){
			$adminlevel="YYUC-ADMIN:";
		}else{
			//我也是普通admin
			self::im_user("YYUC-ADMIN:","YYUC-ADMIN:");
		}
		self::im_user($adminlevel,"YYUC-ADMIN:");
	}
	
	/**
	 * 管理员注销声明
	 * @param mixed $adminlevel 可以不填，管理员级别组(string|array)
	 */
	public static function im_notadmin($adminlevel=null){
		if($adminlevel==null){
			$adminlevel="YYUC-ADMIN:";
		}
		self::im_notuser($adminlevel,"YYUC-ADMIN:");
	}
	/**
	 * 用户声明
	 * @param mixed $userlevel 用户级别(string|array)
	 * @param string $userkey 用户群组
	 */
	public static function im_user($userlevel=null,$userkey='YYUC-USER:'){
		if($userlevel==null){
			$userlevel="YYUC-USER:";
		}elseif(is_array($userlevel)){
			foreach ($userlevel as $ul){
				self::im_user($ul,$userkey);
			}
		}else{
			//我也是普通用户
			$_SESSION['YYUC-USER:YYUC-USER:'] = md5('YYUC-USER:YYUC-USER:');
		}
		$_SESSION[$userkey.$userlevel] = md5($userkey.$userlevel);
		self::set_real_login();
	}
	
	/**
	 * 用户注销声明
	 * @param mixed $userlevel 用户级别(string|array)
	 * @param string $userkey 用户群组
	 */
	public static function im_notuser($userlevel=null,$userkey='YYUC-USER:'){
		if($userlevel==null){
			$userlevel="YYUC-USER:";
		}elseif(is_array($userlevel)){
			foreach ($userlevel as $ul){
				self::im_notuser($ul,$userkey);
			}
		}else{
			//不注销普通用户
			//unset($_SESSION['YYUC-USER:YYUC-USER:']);
		}
		unset($_SESSION[$userkey.$userlevel]);
	}
	/**
	 * 设置session的强制认证模式
	 */
	public static function set_real_login(){
		//强制认证模式授权
		$_SESSION['YYUC_REQIP'] = Request::ip();
	}
	/**
	 * 判断用户是否是管理员
	 * @param mixed $adminlevel 管理员级别(string|array)
	 * @return boolean
	 */
	public static function is_admin($adminlevel=null){
		if($adminlevel==null){
			$adminlevel="YYUC-ADMIN:";
		}
		return self::is_user($adminlevel,"YYUC-ADMIN:");
	}
	/**
	 * 判断用户是否登录
	 * @param mixed $userlevel 用户级别(string|array)
	 * @param string $userkey 用户群组
	 * @return boolean
	 */
	public static function is_user($userlevel=null,$userkey='YYUC-USER:'){
		if($_SESSION['YYUC_REQIP'] !=Request::ip()){
			return false;
		}
		
		//YYUC_set_real_login();
		if($userlevel==null){
			$userlevel="YYUC-USER:";
		}		
		if(is_array($userlevel)){
			foreach ($userlevel as $ul){
				if(self::is_user($ul,$userkey)){
					return true;
				}
			}
		}elseif(isset($_SESSION[$userkey.$userlevel]) && $_SESSION[$userkey.$userlevel]==md5($userkey.$userlevel)){
				return true;
		}
		return false;
		
	}
}