<?php
/**
 * Html文本处理相关
 * @author mqq
 *
 */
class HtmlOperate{
	/**
	 * html字串的纯文本化
	 * @param $str html文本
	 * @param $len 剪切的长度
	 */
	static function smalltext($str,$len=120){
		$str = strip_tags($str,"");
		$str = htmlspecialchars_decode($str);
		$str = ereg_replace("\t","",$str);
		$str = ereg_replace("\r\n","",$str);
		$str = ereg_replace("\r","",$str);
		$str = ereg_replace("\n","",$str);
		$str = ereg_replace(" ","",$str);
		$str = trim($str);
		return trim(mb_substr($str, $start,$len));
	}
	/**
	 * html字串的多余格式滤除，只保留IMG标签换行用br代替
	 * @param $str html文本
	 */
	static function SimpleHtml($str){
		$search = array ("'<script[^>]*?>.*?</script>'si","'<style[^>]*?>.*?</style>'si");
		$replace = array ("","");
		$str = preg_replace($search, $replace, $str);
		$str = str_ireplace("<p", "\n<p", $str);
		$str = str_ireplace("<d", "\n<d", $str);
		$str = str_ireplace("<u", "\n<u", $str);
		$str = str_ireplace("<b", "\n<b", $str);
		$str = strip_tags($str,"<img><center>");
		$str = nl2br($str);
		$str = ereg_replace("\r\n"," ",$str);
		$str = ereg_replace("\r"," ",$str);
		$str = ereg_replace("\n"," ",$str);
		$str = ereg_replace("\t"," ",$str);
		while(strpos($str, "  ")!==false){
			$str = str_replace("  ", " ", $str);
		}
		while(strpos($str, "<br /> <br />")!==false){
			$str = str_replace("<br /> <br />", "<br />", $str);
		}
		while(strpos($str, "<br /><br />")!==false){
			$str = str_replace("<br /><br />", "<br />", $str);
		}
		return trim($str);
	}
}

?>