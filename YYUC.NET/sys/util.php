<?php



/**
 * charat函数扩展
 * 
 * @param string $string
 * @param int $index
 */
function char_at($string, $index){
	if($index < mb_strlen($string)){
		return mb_substr($string, $index, 1);
	}
	else{
		return -1;
	}
}
?>