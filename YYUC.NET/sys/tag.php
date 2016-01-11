<?php
$WEB = Conf::$http_path;
/**开发中心的文件路径*/
$STA = Conf::$remote_path==''?'/':Conf::$remote_path;
/**开发中心的文件路径*/
$SYS = Page::asset('@system/');
/**JS文件调用路径*/
$JS = Page::asset('js/');
/**CSS文件调用路径*/
$CSS = Page::asset('css/');
/**图片文件调用路径*/
$IMG = Page::asset('media/images/');
/**动画文件调用路径*/
$ANI = Page::asset('media/animations/');
/**视频文件调用路径*/
$VID = Page::asset('media/videos/');
/**声音文件调用路径*/
$SOU = Page::asset('media/sounds/');
//国际化页面文字
/**通用页面国际化信息*/
$COM = YYUC::i18n();
/**具体页面国际化*/
$TXT = YYUC::i18n_page_init(Page::$my_view);
/**
 * select 标签
 * @param array $array 待选下拉框的列表数组
 * @param string $name 属性值
 * @param mixed $attrs 其他属性可以是字符或者数组
 */
function YYUC_tag_select(&$array,$name,$attrs=''){
	$nameid = $name;
	global $$name;
	$value = $$name;
	if(is_array($attrs)){
		$attrs = YYUC_get_attrs_from_array($attrs);
	}
	$additionstr = '"';
	if(!is_array($value)||$value===$array){
		$value = explode(',',trim($value));
		if(strpos($attrs, 'multiple')!==false){
			$additionstr = '[]"';
		}elseif(count($value)>1){
			$additionstr = '[]" multiple="multiple" ';
		}
	}else{
		if(strpos($attrs, 'multiple')!==false){
			$additionstr = '[]"';
		}else{
			$additionstr = '[]" multiple="multiple" ';
		}
	}
	
	$tag = '<select name="'.$nameid.$additionstr.' id="'.$nameid.'" '.$attrs.'>';
	foreach ($array as $k=>$v){		
		$tag.='<option value="'.htmlspecialchars($k).'" '.(in_array($k.'', $value,true)? 'selected="selected"' : '').'>'.htmlspecialchars($v).'</option>';
	}
	$tag.='</select>';
	return $tag;
}
/**
 * checkbox 标签
 * @param array $array 待选下拉框的列表数组
 * @param string $name 属性值
 * @param mixed $attrs 其他属性可以是字符或者数组
 */
function YYUC_tag_checkbox($array,$name='',$attrs=''){
	if(is_string($array)){
		$name = $array;
		$attrs = $name;
		$nameid = $name;
		$value = YYUC_getNameValue($name);
		if(is_array($attrs)){
			$attrs = YYUC_get_attrs_from_array($attrs);
		}	
		if($value=='1'){
			$value = true;
		}else{
			$value = false;
		}
		$tag = '<input type="hidden" name="'.$nameid.'" value="0"/><input type="checkbox" value="1" name="'.$nameid.'" '.($value?'checked="checked"':'').' id="'.$nameid.'" '.$attrs.'/>';
		return $tag;
	}else{
		$nameid = $name;
		global $$name;
		$value = $$name;
		if(!is_array($value)){			
			$value = explode(',', trim($value));
		}		
		$tag = '';
		foreach ($array as $k=>$v){			
			$tag.=$v.':<input type="checkbox" name="'.$nameid.'[]" value="'.htmlspecialchars($k).'" '.((in_array($k, $value))? 'checked="checked"' : '').'/>&nbsp;&nbsp;';
		}
		return $tag;
	}
	
}

/**
 * input 标签
 * @param string $type 标签类型
 * @param string $name 属性值
 * @param mixed $attrs 其他属性可以是字符或者数组
 */
function YYUC_tag_input($type,$name,$attrs=''){
	$nameid = $name;
	$value = YYUC_getNameValue($name);
	if(is_array($attrs)){
		$attrs = YYUC_get_attrs_from_array($attrs);
	}
	if($value!=''){
		$value = 'value="'.$value.'"';
	}
	$tag = '<input type="'.$type.'" '.$value.' name="'.$nameid.'" id="'.$nameid.'" '.$attrs.'/>';
	return $tag;
}
/**
 * input text 标签
 * @param string $name 属性值
 * @param mixed $attrs 其他属性可以是字符或者数组
 */
function YYUC_tag_text($name,$attrs=''){
	return YYUC_tag_input('text', $name,$attrs);
}
/**
 * input password 标签
 * @param string $name 属性值
 * @param mixed $attrs 其他属性可以是字符或者数组
 */
function YYUC_tag_password($name,$attrs=''){
	return YYUC_tag_input('password', $name,$attrs);
}
/**
 * input email 标签
 * @param string $name 属性值
 * @param mixed $attrs 其他属性可以是字符或者数组
 */
function YYUC_tag_email($name,$attrs=''){
	return YYUC_tag_input('email', $name,$attrs);
}
/**
 * input range 标签
 * @param string $name 属性值
 * @param mixed $attrs 其他属性可以是字符或者数组
 */
function YYUC_tag_range($name,$attrs=''){
	return YYUC_tag_input('range', $name,$attrs);
}
/**
 * 根据数组拼接标签属性
 * @param $array
 * @return string 属性字串
 */
function YYUC_get_attrs_from_array(&$array){
	$attrs = '';
	foreach ($array as $k=>$v){
		$attrs.=" $k=\"".htmlspecialchars($v)."\"";
	}
	return $attrs;
}
/**
 * radio 标签
 * @param array $array 待选下拉框的列表数组
 * @param string $name 属性值
 * @param mixed $attrs 其他属性可以是字符或者数组
 */
function YYUC_tag_radio($array,$name,$attrs=''){
	$nameid = $name;
	$value = YYUC_getNameValue($name);
	if(is_array($attrs)){
		$attrs = YYUC_get_attrs_from_array($attrs);
	}
	$tag = '';
	foreach ($array as $k=>$v){
		$tag.=$v.':<input type="radio" name="'.$nameid.'" value="'.htmlspecialchars($k).'" '.(($k==$value)? 'checked="checked"' : '').' '.$attrs.'/>&nbsp;&nbsp;';
	}
	return $tag;
}
/**
 * input hidden 标签
 * @param string $name 属性值
 * @param mixed $attrs 其他属性可以是字符或者数组
 */
function YYUC_tag_hidden($name,$attrs=''){
	$nameid = $name;
	$value = YYUC_getNameValue($name);
	if(is_array($attrs)){
		$attrs = YYUC_get_attrs_from_array($attrs);
	}
	if($value!=''){
		$value = 'value="'.$value.'"';
	}
	$tag = '<input type="hidden" '.$value.' name="'.$nameid.'" id="'.$nameid.'" '.$attrs.'/>';
	return $tag;
}
/**
 * textarea 标签
 * @param string $name 属性值
 * @param mixed $attrs 其他属性可以是字符或者数组
 */
function YYUC_tag_textarea($name,$attrs=''){
	$nameid = $name;
	$value = YYUC_getNameValue($name);
	if(is_array($attrs)){
		$attrs = YYUC_get_attrs_from_array($attrs);
	}
	$tag = '<textarea name="'.$nameid.'" id="'.$nameid.'" '.$attrs.'>'.$value.'</textarea>';
	return $tag;
}
/**
 * input text 日期时间 标签
 * @param string $name 属性值
 * @param mixed $attrs 其他属性可以是字符或者数组
 */
function YYUC_tag_datetime($name,$attrs=''){
	$nameid = $name;
	$value = YYUC_getNameValue($name);
	if(is_array($attrs)){
		$attrs = YYUC_get_attrs_from_array($attrs);
	}
	$datevalue = ($value==''||$value=='0')?'':date('Y-m-d H:i:s',$value);
	$tag = '<input type="text" value="'.$datevalue.'"  id="'.$nameid.'" '.$attrs.' onfocus="yyuccalendar.initCalendar(this,true,$(this).next(\'input\')[0])"/><input type="hidden" relobj="yyuccalendar" rel="yyuc" value="'.$value.'" name="'.$nameid.'" />';
	return $tag;
}
/**
 * input text 日期标签
 * @param string $name 属性值
 * @param mixed $attrs 其他属性可以是字符或者数组
 */
function YYUC_tag_date($name,$attrs=''){
	$nameid = $name;
	$value = YYUC_getNameValue($name);
	if(is_array($attrs)){
		$attrs = YYUC_get_attrs_from_array($attrs);
	}
	$datevalue = ($value==''||$value=='0')?'':date('Y-m-d',$value);
	$tag = '<input type="text" value="'.$datevalue.'"  id="'.$nameid.'" '.$attrs.' onfocus="yyuccalendar.initCalendar(this,false,$(this).next(\'input\')[0])"/><input type="hidden" relobj="yyuccalendar" rel="yyuc" value="'.$value.'" name="'.$nameid.'" />';
	return $tag;
}
/**
 * input text 颜色标签
 * @param string $name 属性值
 * @param boolean $needal 不透明度选择框 默认true
 * @param mixed $attrs 其他属性可以是字符或者数组
 */
function YYUC_tag_color($name,$needal = true){
	$nameid = $name;
	$value = YYUC_getNameValue($name);
	if(is_array($attrs)){
		$attrs = YYUC_get_attrs_from_array($attrs);
	}
	$datevalue = ($value==''||$value=='0')?'':$value;
	$tag = '<div name="Alpha_'.$nameid.'" id="Alpha_'.$nameid.'"></div><input type="hidden" needal="'.$needal.'" colorid="Alpha_'.$nameid.'" relobj="yyuccolor" rel="yyuc" value="'.$value.'" id="'.$nameid.'" name="'.$nameid.'" />';
	return $tag;
}
/**
 * texteditor 标签
 * @param string $name  字段名称 标签name
 * @param mixed $level 配置级别 编辑框的展现复杂度:{1-7},或者原始的构建数组
 * @param integer $width 宽度 默认640
 * @param integer $height 高度 默认300
 * @return string 标签html字串
 */
function YYUC_tag_texteditor($name,$level=3,$width='640px',$height='300px'){
	$nameid = $name;
	$value = YYUC_getNameValue($name);
	global $yyuc_filedname_index;
	$namerealid = str_replace('[]', '', $name);
	$namerealid = $namerealid.trim($yyuc_filedname_index[$namerealid]);	
	$tag = '<textarea width="'.$width.'" height="'.$height.'" style="width: '.$width.';height:'.$height.'," name="'.$nameid.'" id="'.$namerealid.'">'.$value.'</textarea>';
	$opts = null;
	if(!is_array($level)){
		$level = intval($level);
		$allowupload = ($level==3||$level==4||$level==6||$level==7||$level==10)?'true':'false';
		$opts = '{langType:"'.$_SERVER['HTTP_ACCEPT_LANGUAGE'].'",allowImageUpload:'.$allowupload.',allowFlashUpload:'.$allowupload.',allowMediaUpload:'.$allowupload.',allowFileUpload:'.$allowupload.',allowFileManager:'.($level==4||$level==7?'true':'false').',items:kindeditor_item'.($level==1?'0':intval($level/5+1)).'}';
	}else {
		$opts = json_encode($level);
	}
	$opts = htmlspecialchars($opts,ENT_QUOTES);
	
	return $tag.'<input type="hidden" relobj="kindeditor" rel="yyuc" editorid="'.$namerealid.'" value="'.$opts.'" />';
}
/**
 * 验证码标签
 * @param string $text 要输入验证码的文本框ID 一定要在Form内部
 * @param string $local 要展现的验证码位置元素的ID 一定要在Form内部
 * @param boolean $sound 验证码是否开启声音提示功能
 * @param integer $width 宽度默认115
 * @param integer $height 高度默认30
 * @param integer $codenum 验证码字符个数默认4
 */
function YYUC_tag_vercode($text,$local,$sound=false,$width=115,$height=30,$codenum=4){
	$YYSYS = Page::asset('@system/');
	$html = '<script>';
	$html .= '$(function(){$("#'.$text.'").data("mbl",1);$("#'.$text.'").blur();$("#'.$text.'").data("mbl",0);$("#'.$local.'").append(\'<input type="hidden" id="yyuc_verimg_tag"/>\');$("#'.$text.'").focus(function(){ if(!$("#yyuc_verimg").is("img")){';
	$html .= 'var verimg = $(\'<img id="yyuc_verimg" title="双击切换" />\');';
	$html .= 'verimg.attr("src","/@system/securimage/loadver-'.$width.'-'.$height.'-'.$codenum.Conf::$suffix.'?r="+Math.random());';
	$html .= '$("#'.$local.'").append(verimg);';
	if($sound){
		$soundurl = $YYSYS.'securimage/securimage_play.swf?audio_file=/@system/securimage/loadsound'.Conf::$suffix.'&amp;bgColor1=#fff&amp;bgColor2=#fff&amp;iconColor=#777&amp;borderWidth=1&amp;borderColor=#000';
		$swfhtm = '<object type="application/x-shockwave-flash" data="'.$soundurl.'" height="32" width="32"><param name="movie" value="'.$soundurl.'"></object>';
		$html .= '$("#'.$local.'").append(\''.$swfhtm.'\');';
	}
	$html .= 'verimg.dblclick(function(){ verimg.attr("src","/@system/securimage/loadver-'.$width.'-'.$height.'-'.$codenum.Conf::$suffix.'?r="+Math.random());})';
	$html .= '}});';
	$html .= 'var toyz = function(){if($.trim($("#'.$text.'").val())==""){return;}if($.trim($("#'.$text.'").val()).length!='.$codenum.'){$("#'.$text.'").val("");return;};$("#'.$text.'").data("mbl",1);$("#'.$text.'").blur();$("#'.$text.'").data("mbl",0);$("#'.$text.'").attr("disabled",true);ajax("/@system/securimage/checkver'.Conf::$suffix.'",{"code":$.trim($("#'.$text.'").val())},function(m){if(m=="ok"){$("#'.$local.'").html("");}else{$("#'.$text.'").val("");$("#'.$text.'").attr("disabled",false);}});};';
	$html .= '$("#'.$text.'").keyup(function(){$(this).val($.trim($(this).val()));if($(this).val().length=='.$codenum.'){toyz();}});$("#'.$text.'").blur(function(){if($("#'.$text.'").data("mbl")==1){return;};toyz();});';
	$html .= '});</script>';
	return $html;
}

/**
 * 取得表单元素的实际值
 * @param string $name 表单name属性
 * @return mixed 对应变量的值(string|array)
 */
function YYUC_getNameValue($name){
	if(strpos($name, '[]')==false){
		global $$name;
		return htmlspecialchars(trim($$name),ENT_QUOTES);
	}else{
		$name = str_replace('[]', '', $name);
		global $$name;
		$realvar = $$name;
		if(is_array($realvar)){
			global $yyuc_filedname_index;
			if(empty($yyuc_filedname_index)){
				$yyuc_filedname_index = array();
			}		
			if(!isset($yyuc_filedname_index[$name])){
				$yyuc_filedname_index[$name] = 0;
			}
			$res = htmlspecialchars(trim($realvar[$yyuc_filedname_index[$name]]),ENT_QUOTES);
			$yyuc_filedname_index[$name] = $yyuc_filedname_index[$name] + 1;
			return $res;
		}
	}
}
?>