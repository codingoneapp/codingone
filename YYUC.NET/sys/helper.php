<?php
/**
 *FORM提交的令牌 防止重复提交
 *对于CUD的信息提交建议加上令牌参数
 */
function tk(){
	return YYUC::token();
}

/**
 * 读取特定的i18n配置信息
 * 
 * @param string $name
 */
function i18n($name = ''){
	return YYUC::i18n($name);
}

/**
 * 返回引入本控制器文件夹下的文件绝对路径<br/>
 * 为了便于区分和防止恶意访问到，一般用前缀"_"标注
 * @param  string $colname php文件名(不含后缀)
 */
function another($colname ='_'){
	return YYUC::another();
}

/**
 *
 * 生成文章导读
 * @param string $str 字符串(Html代码)
 * @param integer $len(缩小后的长度)
 */
function pv($str,$len){
	return String::smalltext($str, $len);
}

/**
 *
 * 整理texteditor的提交内容
 * @param string $str 字符串(Html代码)
 * @param integer $len(缩小后的长度)
 */
function stdtxt($str){	
	return String::measuretext($str);
}

/**
 * 取得Session一次性显示内容
 * @param string $k Session参数
 * @return string Session内容
 */
function once($k){
	return Session::flush($k);
}

/**
 * 判断Session是否含有一次性显示内容
 * @param string $k Session参数
 * @return boolean
 */
function hold($k){
	return Session::hold($k);
}

/**
 *  URL编码
 */
function ec($str){
	return str_replace(conf::$paging_separate,'@PG@',str_replace(conf::$parameter_separate,'@PA@', rawurlencode($str))) ;
}

/**
 *  URL解码
 */
function dc($str){
	return rawurldecode(str_replace('@PG@',conf::$paging_separate,str_replace('@PA@', conf::$parameter_separate,$str)));
}

/**
 * 当前用户请求的URL
 */
function url(){
	return conf::$http_path.Request::url();
}

/**
 * 当前用户请求的不含分页信息的URL
 */
function npgurl(){
	return conf::$http_path.Request::url_nopage();
}

/**
 * 当前用户请求的不含参数和分页信息的URL
 */
function npaurl(){
	return conf::$http_path.Request::url_nopam();
}

/**
 * 执行JS的吐丝方法
 */
function toast($msg){
	Response::exejs('YYUC.toast("'.htmlspecialchars($msg,ENT_QUOTES).'")');
}
function tusi($msg){
	toast($msg);
}

 