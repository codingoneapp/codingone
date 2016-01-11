<?php







/**
 * 页面一次性提醒信息<br/>
 * @param string $msgsign 信息标识
 * @param string $msg 提醒信息
 */
function msg($msgsign,$msg=null){
	if($msg!==null){
		$_SESSION['YYUC_FLUSH_'.$msgsign] = $msg;
	}else if(isset($_SESSION['YYUC_FLUSH_'.$msgsign])){
		$msg = $_SESSION['YYUC_FLUSH_'.$msgsign];
		unset($_SESSION['YYUC_FLUSH_'.$msgsign]);
		return trim($msg);
	}
}


/**
 * 引入特定的控制器文件夹下的扩展类
 * @param string 控制器名称
 */
function import($actionname){
	include_once another($actionname);
}


/**
 * 清空cookie
 */
function clear_cookie(){
	foreach ($_COOKIE as $k=>$v){
		set_cookie($k);
	}
}	







?>