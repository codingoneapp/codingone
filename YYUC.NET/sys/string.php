<?php
class String{

	/**
	 * 判断字符串开头
	 * 
	 * @param string $haystack
	 * @param string $needle
	 * @param boolean $case 是否大小写敏感
	 */
	public static function start_with($haystack,$needle,$case=true){
		if($case){
			return strpos($haystack, $needle, 0) === 0;
		}
		return stripos($haystack, $needle, 0) === 0;
	}

	/**
	 * 判断字符串结尾
	 *
	 * @param string $haystack
	 * @param string $needle
	 * @param boolean $case 是否大小写敏感
	 */
	public static function end_with($haystack,$needle,$case=true){
		$expectedPosition = strlen($haystack) - strlen($needle);

		if($case){
			return strrpos($haystack, $needle, 0) === $expectedPosition;
		}
		return strripos($haystack, $needle, 0) === $expectedPosition;		
	}
	
	/**
	 * 判断字符包含
	 *
	 * @param string $haystack
	 * @param string $needle
	 * @param boolean $case 是否大小写敏感
	 */
	public static function contain($haystack,$needle,$case=true){
		if($case){
			return strpos($haystack, $needle, 0) !== false;
		}
		return stripos($haystack, $needle, 0) !== false;
	}
	
	/**
	 * 根据正则表达式找到匹配到的第一个字串
	 * @param string $str 被查找的字符串
	 * @param string $reg 正则表达式
	 * @return string
	 */
	public static function  find_first_string_by_reg($str,$reg){
		$out = array();
		if(1==preg_match($reg,$str,$out,PREG_OFFSET_CAPTURE)){
			return $out[0][0];
		} else {
			return '';
		}
	}
	
	/**
	 * 字符串转换为二进制
	 * 
	 * @param string $str
	 */
	public static function str_to_binary($str){
		$str = base64_encode($str);
		$len = strlen($str);
		$data = '';
		for($i=0; $i<$len; $i++) {
			$data .= sprintf("%08b", ord(substr($str, $i, 1)));
		}
		echo $str.'</br>';
		echo $data;
		die();
		return  $data;
	}
	
	/**
	 * 二进制转换为字符串
	 *
	 * @param string $str
	 */
	public static function binary_to_str($str){
		$len = strlen($str);
		$data = '';
        for($i=0; $i<($len/8); $i++) {
                $data .= chr(intval(substr($str, $i * 8, 8), 2));
        }
		return base64_decode($data);
	}
	
	function encrypt($string, $action = 'ENCODE', $hash = '',$needbase64= true)
	{
		if($action != 'E' && $needbase64){
			$string = base64_decode($string);
		}
		$code = '';
		$key = md5($hash);
		$keylen = strlen($key);
		$strlen = strlen($string);
		for ($i = 0; $i < $strlen; $i ++) {
			//echo $i;
			$k = $i % $keylen; //余数  将字符全部位移
			$code .= $string[$i] ^ $key[$k];//位移
		}
		if($action != 'D' && $needbase64){
			$code = base64_encode($code);
		}
		return $code;
	}
	
	public static function strcode($string,$operation,$key='')	{
		$key=md5($key);
		$key_length=strlen($key);
		$string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
		$string_length=strlen($string);
		$rndkey=$box=array();
		$result='';
		for($i=0;$i<=255;$i++)		{
			$rndkey[$i]=ord($key[$i%$key_length]);
			$box[$i]=$i;
		}
		for($j=$i=0;$i<256;$i++)		{
			$j=($j+$box[$i]+$rndkey[$i])%256;
			$tmp=$box[$i];
			$box[$i]=$box[$j];
			$box[$j]=$tmp;
		}
		for($a=$j=$i=0;$i<$string_length;$i++)		{
			$a=($a+1)%256;
			$j=($j+$box[$a])%256;
			$tmp=$box[$a];
			$box[$a]=$box[$j];
			$box[$j]=$tmp;
			$result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
		}
		if($operation=='D')		{
			if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))			{
				return substr($result,8);
			}
			else{
				return'';
			}
		}
		else{
			return str_replace('=','',base64_encode($result));
		}
	}
	
	
	/**
	 * 字符串加密
	 * @param string $str 加密字串
	 * @param string $key 加密依据
	 * @return string
	 */
	public static function  encryption($str,$key=''){
		//加密的算法是取余
		return self::encrypt($str,'E',$key.Conf::$management_center_password);
	}
	/**
	 * 字符串解密
	 * @param string $str 解密字串
	 * @param string $key 解密依据
	 * @return string
	 */
	public static function  decryption($str,$key=''){
		return self::encrypt($str,'D',$key.Conf::$management_center_password);
	}
	
	/**
	 * 英数字符串加密
	 * @param string $str 加密字串
	 * @param string $key 加密依据
	 * @return string
	 */
	public static function  encode($str){
		$arrReturn = array ();
		for($i = 0; $i < strlen ( $str ); ++ $i) {
			$a = dechex ( ord ( $str {$i} ) );
			for($j = 0; $j < strlen ( $a ); ++ $j) {
				$char = $a {$j};
				if ($char >= 'a') {
					$arrReturn [] = chr ( ord ( $char ) + 10 );
				} else {
					$arrReturn [] = chr ( ord ( $char ) + 49 );
				}
			}
		}
		return implode ('', $arrReturn);
	}
	/**
	 * 英数字符串解密
	 * @param string $str 解密字串
	 * @param string $key 解密依据
	 * @return string
	 */
	public static function  decode($str){
		$chinese = array ();
		for($i = 0; $i < strlen ( $str ); $i += 2) {
			$arr = array ();
			for($j = 0; $j < 2; $j ++) {
				$char = $str {$i + $j};
				$arr [] = ord ( $char ) >= 107 ? chr ( ord ( $char ) - 10 ) : chr ( ord ( $char ) - 49 );
			}
			$chinese [] = chr ( hexdec ( $arr [0] . $arr [1] ) );
		}
		return implode ( '', $chinese );
	}
	
	/**
	 * 
	 * 生成文章导读
	 * @param string $str 字符串(Html代码)
	 * @param integer $len(缩小后的长度)
	 */
	public static function  smalltext($str,$len){
		return mb_substr(trim(strip_tags($str)), 0, $len) ;
	}
	
	/**
	 * 标准化正文内容
	 * @param string $str 字符串(Html代码)
	 */
	public static function  measuretext($str,$appendtag=''){
		$str = strip_tags($str,'<ul><ol><p><b><i><br><li><img><strong><em><u><center><a><table><tr><th><td>'.$appendtag);
		$str =preg_replace('/\s\s+/', " ", $str);
		$str =str_replace('&nbsp;', ' ', $str);
		$str = preg_replace('#<br\s*?/?>#i', "\n", $str);
		$str =preg_replace('/\n\s+/', "\n", $str);
		$str = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $str);
		$str = preg_replace('/(<[^>]+) align=".*?"/i', '$1', $str);
		$str = preg_replace('/(<[^>]+) class=".*?"/i', '$1', $str);
		return nl2br($str);
	}
	
	
	
	
	/**
	 * 特定字符分割，去除分割后的空字符
	 * @param string $str 被分割的字符串
	 * @param string $splitchar 分隔符
	 * @return array 分割结果结果
	 */
	public static function split($str, $splitchar=','){		
		return array_filter(explode($splitchar, $str));
	}
	
	/**
	 * 整数进制压缩为36位
	 * @param integer|string $intvalue 被压缩的整数
	 */
	public static function smallint($intvalue){
		return base_convert($intvalue,10,36);
	}
	
	/**
	 * 整数进制恢复为十进制
	 * @param string $int36value 被还原的36进制整数
	 */
	public static function toint($int36value){
		return base_convert($int36value,36,10);
	}
	
	
}