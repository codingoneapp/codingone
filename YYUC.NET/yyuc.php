<?php
//后退缓存类型
header("Cache-control: private");
//时区
date_default_timezone_set("Asia/Shanghai");
//字符处理编码
mb_internal_encoding("UTF-8");
//默认页面头标识
header('Content-Type: text/html; charset=UTF-8');
header('HTTP/1.1 200 OK');
header("status: 200 OK");
define('YYUC_VERSION', '3.0');
//define('YYUC_VPDDOM', 'OKDES');

function _basename($path) {
	return preg_replace('/^.+[\\\\\\/]/', '', $path);
}
//对于虚拟机的情况下寻找网站根目录
define('YYUC_PUB' ,_basename(dirname(YYUC_LOCAL_PATH)));

//设置错误日志输出
function myErrorHandler($errno, $errstr, $errfile, $errline){
	if(!class_exists('Log')){
		require YYUC_LIB.'sys/log.php';
	}
	
	$err_msg = "信息：{$errno}。内容：{$errstr},发生在文件{$errfile}的{$errline}行";
	switch ($errno) {
		case E_USER_ERROR:
			Log::error($err_msg);
			if(Conf::$is_developing){
				echo $err_msg;
			}
			exit(1);
			break;
		case E_USER_WARNING:
			Log::warn($err_msg);
			break;
		case E_USER_NOTICE:
			Log::info($err_msg);
			break;
		default:
			Log::info($err_msg);
			break;
	}
	return true;
}
set_error_handler('myErrorHandler');
//加载配置文件
require YYUC_FRAME_PATH.'conf.php';
if(Conf::$is_developing){
	ini_set('display_errors', true);
	error_reporting(E_ALL);
}
//----------------------------------------------------------------------------------------
//设置动态加载类方式
function YYUC_autoload($class){
	if(class_exists($class)){
		return;
	}
	$file = YYUC_LIB.'sys/'.strtolower($class).'.php';
	if (is_file($file)){
		require($file);
		return;
	}
	if(conf::$db_port == 'mssql'){
		$file = YYUC_LIB.'sys/db/ms/'.strtolower($class).'.php';
	}else{
		$file = YYUC_LIB.'sys/db/'.strtolower($class).'.php';
	}
	
	if (is_file($file)){
		require($file);
		return;
	}
	$file = YYUC_LIB.'plugin/'.strtolower($class).'.php';
	if (is_file($file)){
		require($file);
		return;
	}
	$file = YYUC_FRAME_PATH.'model/'.strtolower($class).'.php';
	if (is_file($file)){
		require($file);
		return;
	}
	$file = YYUC_LIB.'plugin/@system/'.strtolower($class).'.php';
	if (is_file($file)){
		require($file);
		return;
	}
	$file = YYUC_FRAME_PATH.'plugin/'.strtolower($class).'.php';
	if (is_file($file)){
		require($file);
		return;
	}
	//二维码库
	if(strpos($class, 'QR')===0){
		require(YYUC_LIB.'plugin/QRCode/qrlib.php');
		return;
	}
	//Excel 扩展功能加载
	define('PHPEXCEL_ROOT', YYUC_LIB.'plugin/');
	$file =	YYUC_LIB.'plugin/'.str_replace('_',DIRECTORY_SEPARATOR,$class).'.php';
	if (is_file($file)){
		require($file);
		return;
	}
	
	if($class=='ALIOSS'){
		require(YYUC_LIB.'plugin/AliOss/sdk.class.php');
		return;
	}
}
spl_autoload_register('YYUC_autoload');
//客户端语言
if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
	$lans = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	$_SERVER['HTTP_ACCEPT_LANGUAGE'] = strtolower($lans[0]);
}else{
	$_SERVER['HTTP_ACCEPT_LANGUAGE'] = strtolower(Conf::$default_i18n);
}
//错误显示级别
if(!Conf::$is_developing){
	error_reporting(0);
}else{
	error_reporting(E_ALL);
}

//网络路径
Conf::$http_path = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://'.$_SERVER ['HTTP_HOST'].'/';


//引入统一帮助类
include YYUC_LIB.'sys/helper.php';

//正式请求处理
$_PAGE = new Index();
Index::$me = $_PAGE;

//加载页面验证函数
include YYUC_FRAME_PATH.'/fun/access_validations.php';
//网址解析
$_PAGE->_parseUrl();
//缓存检查
$YYUC_isobcache = false;
//压缩输出
if (Conf::$need_gzip && (isset($_SERVER['HTTP_ACCEPT_ENCODING'])&& preg_match('/gzip/i',$_SERVER['HTTP_ACCEPT_ENCODING']))){
    ob_start('ob_gzhandler');
    $YYUC_isobcache = true;
}
//缓存校验结果如果存在前置校验函数则返回
$cacheres = $_PAGE->check_cache();
if($cacheres === false){
	//如果缓存文件不存在或者缓存已经过期
	//404检查 404检查的同时进行路由规则计算匹配自定义路由
	$_PAGE->_trans404();
}
$yyuc_needsession = true;
if(is_array(Conf::$nosession_path)){
	foreach (Conf::$nosession_path as $apath){
		if(strpos($_PAGE->controller_path, $apath)===0){
			$yyuc_needsession = false;
			break;
		}
	}
}
if($yyuc_needsession){
	//开启Session
	if(Conf::$session_adapter=='cache'){
		//判断session类型
		//----------------------------------------------------------------------------------------
		//session 配置
		function YYUC_session_open($save_path, $session_name){
			return true;
		}
		function YYUC_session_close(){
			return true;
		}
		function YYUC_session_read($k){
			$ttem = Cache::get('YYUC_SESSION/'.$k);
			return $ttem;
		}
		function YYUC_session_write($k, $v){
			Cache::set('YYUC_SESSION/'.$k, $v, Conf::$session_time*60);
		}
		function YYUC_session_destroy($k){
			return Cache::remove('YYUC_SESSION/'.$k);
		}
		function YYUC_session_gc($maxlifetime){
			if(Conf::$cache_adapter != 'memcached'){
				foreach(glob(YYUC_FRAME_PATH.'sys/cache/YYUC_SESSION/*') as $filename){
					if(filemtime($filename) + Conf::$session_time*60 < time() ){
						@unlink($filename);
					}
				}
			}
			return true;
		}
		//----------------------------------------------------------------------------------------
		session_set_save_handler("YYUC_session_open", "YYUC_session_close", "YYUC_session_read", "YYUC_session_write", "YYUC_session_destroy","YYUC_session_gc");
	}
	session_set_cookie_params(Conf::$session_time*60);
	session_start();
	//记录跳转
	if(isset($_SERVER['HTTP_REFERER']) && isset($_GET['back']) && trim($_GET['back'])=='' && !$_POST){
		$_SESSION['YYUC_HTTP_REFERER_OLD'] = $_SERVER['HTTP_REFERER'];
	}
}
if ($_GET['yyuc_sessionid']){
	//记录GET来的sessionid
	session_id($_GET['yyuc_sessionid']);
	session_set_cookie_params(Conf::$session_time*60);
	session_start();
}



if($cacheres === false){
	//是否开启调试记录
	if(Conf::$need_debug_log){
		Debug::benchmark('YYUC_START');
	}
	//加载i18n配置文件数组
	$I18N = YYUC::i18n();
	Page::$i18n = & $I18N;
	
	if(!$_PAGE->is_sys_col){
		//加载用户自定义函数
		include YYUC_FRAME_PATH.'/fun/additions.php';
		//执行全局启动函数
		if(is_callable('yyuc_start')){
			yyuc_start();
		}
		//加载钩子方法
		if(is_callable('access_validations')){
			access_validations($_PAGE->controller_path);
		}
	}
	//进行表单令牌验证
	if(isset($_POST['YYUC_FORM_TOKEN'])){
		if(!@array_key_exists($_POST['YYUC_FORM_TOKEN'], $_SESSION['YYUC_FORM_TOKEN'])){
			//不存在令牌 非正常提交
			Redirect::to_500($I18N['repost_err']);
		}else{
			Page::$tk_ok = true;
			Page::$tk_str = $_SESSION['YYUC_FORM_TOKEN'][$_POST['YYUC_FORM_TOKEN']].'@YYUC@'.$_POST['YYUC_FORM_TOKEN'];
			//删除令牌
			unset($_SESSION['YYUC_FORM_TOKEN'][$_POST['YYUC_FORM_TOKEN']]);
		}
	}
	//加载控制器执行文件
	include $_PAGE->col_path;
	//没有开启常规缓存但是请求的是常规缓存后缀
	if(Page::$cache_type != CACHE_NORMAL&&isset($_SERVER['TRANS_NORMAL_CACHE'])){
		Redirect::to_404();	
	}
	if(!$YYUC_isobcache && Page::$cache_type!==false){
		//开启缓存
		ob_start();
	}
	if(Page::$need_view){
		
		//需要视图 先计算出视图路径和 编译后的文件路径	
		$_PAGE->_transpath();
		
		//编译视图模板
		$_PAGE->_trans();
		
		//加载标签输出主文件
		include YYUC_LIB.'/sys/tag.php';
		//加载编译后的视图文件
		include $_PAGE->com_path;
		//自动执行后台的推送的JS脚本
		if(!empty(Page::$js_arr)){
			echo '<script type="text/javascript">YYUC=window.YYUC?YYUC:{ready:function(fun){window.onload=fun;}};YYUC.ready(function(){'.implode(";", Page::$js_arr).'});</script>';
		}
	}	
	if(Page::$cache_type!==false){
		//写入缓存
		$_PAGE->html .= ob_get_contents();
		$_PAGE->write_cache();
	}
	//事务提交的判断???
}else{
	//此次请求执行之前的过滤器
	if($cacheres !==true){
		call_user_func($cacheres);
	}
	echo $_PAGE->html;
}
//判断是否打印调试信息
if(Conf::$need_debug_log&&Conf::$auto_print_debug){
	Debug::print_all();
}
?>