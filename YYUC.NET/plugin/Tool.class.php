<?php
/**
 * 日志记录
 * @author mqq
 *
 */
class Tool{
	/**
	 * uuid生成
	 * @param $str
	 */
	public static function uuid($str=null){
		if($str!==null){
			return md5(uniqid().$str);
		}else {
			return uniqid();
		}
	}
}
?>