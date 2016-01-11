<?php
/**
 * 文件上传插件
 * @author fl
 *
 */
class Upload{
	
	/**
	 * 
	 * @var Model
	 */
	public static $_model = null;
	/**
	 * 设置要进行上传预处理的Model<br/>
	 * 传入Model后会自动对应上传数据
	 * 
	 * @param Model $model
	 */
	public static function bind_model($model){
		self::$_model = $model;
	}
	
/**
 * 文件上传插件<br/>
 * $config的配置说明：<br/>
 * width:上传按钮的宽度(integer)<br/>
 * height:上传按钮的高度(integer)<br/>
 * fontSize:上传按钮的文本大小(integer :15)(单位:px)<br/>
 * text:上传按钮的文本内容(string)<br/>
 * fontColor:上传按钮的文本颜色(string :#000000)<br/>
 * backgroundImg:上传按钮的背景图片(string :/media/images/bg.jpg)<br/>
 * filenameSize:文件名称的文本大小(integer :15)(单位:px)<br/>
 * filenameColor:文件名称的文本颜色(string :#000000)<br/>
 * style:上传控件的一些必要的样式补充，不会覆盖原有样式定义(string :margin-top:10px;)<br/>
 * size:上传文件的大小限制(integer :150)(单位:KB)<br/>
 * type:上传文件的类型限制(string :jpg,gif,png)<br/>
 * onfailure:上传失败后回调的JS函数名称(string :alert)<br/>
 * onsuccess:上传成功后回调的JS函数名称，不设置则不调用，回传参数为上传文件的访问URL(string :alert)<br/>
 * showdel:是否显示删除上传文件的按钮,默认为:false,自定义URL的情况下该参数无效，请自行添加删除方法(string :false)<br/>
 * ondel:删除按钮点击后的JS函数名称，不设置则不调用，回传参数为上传文件的访问URL(string :alert)<br/>
 * $data为 文件上传中的附带的提交信息默认为空数组(string : array('name'=>'xiaoming','age'=>'18')   )<br/>
 * $url 为用户处理上传的请求URL地址，一般编辑页面使用。连同Form提交的新增页面一般用 tosave_upload_file($name,$path)<br/>
 * $picareaid 为图片文件上传后要显示的位置 不设置则不显示<br/>
 * $cutpic 是图片剪切的相关配置，不设置则不会进行剪切处理：<br/>
 * buttonStyle:剪切确认提交的按钮样式(string :margin-top:10px;)<br/>
 * buttonClass:css样式
 * buttonText:剪切确认提交的按钮文本(string :确定剪裁;)<br/>
 * width:剪切图片的目标宽度,不设置则不限定(integer :15)(单位:px)<br/>
 * height:剪切图片的目标高度,不设置则不限定(integer :15)(单位:px)<br/>
 * onsuccess:剪切成功后回调的JS函数名称，不设置则不调用，回传参数为文件的访问URLURL(string :alert)<br/>
 * @param string $name 标签name要求唯一
 * @param array $config 上传文件的配置信息
 * @param array $data 文件上传中的附带提交信息
 * @param string $url 文件上传提交的URL
 * @param string $picareaid 图片文件上传后要显示的位置ID
 * @param array $cutpic 图片上传后需要剪裁的相关配置
 */
public static function init($name,$config=null,$data=null,$url=null,$picareaid=null,$cutpic=null){
	$YYSYS = Page::asset('@system/');
	$STA = Conf::$remote_path==''?'/':Conf::$remote_path;
	if($url==null){
		$url = '/@system/upload.html';
	}
	$picareaid = trim($picareaid);
	$value = '';
	if(is_null(self::$_model)){
		$value = YYUC_getNameValue($name);
	}else{
		$value = trim(self::$_model->$name);
		$name = self::$_model->field_form_name($name);
	}
	
	if(!isset($config)){
		$config=array();
	}
	if(!isset($data)){
		$data=array();
	}
	if(!is_array($config)){
		$picareaid = $config;
		$config = array();
	}
	$sysconfig = array(
			'width'	=> 100,
			'height'=>32,
			'fontSize'=>15,
			'filenameSize'=>13,
			'text'=>'上传文件',
			'class'=>'',
			'filenameColor'=>'#000000',
			'fontColor'=>'#000000',
			'backgroundImg'=>'',
			'size'=>600,
			'type'=>'jpg,gif,png,jpeg,bmp,xls,doc,xlsx,docx,rar,zip,ppt,pptx,txt',
			'onfailure'=>'alert',
			'onselect'=>'false',
			'onsuccess'=>'false',
			'ondel'=>'false',
			'showdel'=>false,
			'showfilename'=>false,
			'showbfb'=>false,
			'style'=>'',
			'btnstyle'=>''
	);
	$config = array_merge($sysconfig,$config);
	$ttid = 'YYUC_'.md5('YYUC_UPLOAD_'.$name.microtime());
	$surl = $url;
	
	$width = $config['width'];
	$class = $config['class'];
	$height = intval($config['height']);
	$lineheight = intval($height/5*4);
	$fontSize = $config['fontSize'];
	$filenameSize = $config['filenameSize'];
	$text = $config['text'];
	$filenameColor = $config['filenameColor'];
	$fontColor = $config['fontColor'];
	$backgroundImg = trim($config['backgroundImg']);
	$btntype="button";
	if($backgroundImg!=''){
		$btntype = "image";
	}
	$size = $config['size'];
	$_SESSION['YYUC_last_upsizelimit'] = $size;
	$type = $config['type'];
	$onfailure = $config['onfailure'];
	$onselect = $config['onselect'];
	$onsuccess = $config['onsuccess'];
	$ondel = $config['ondel'];
	$showdel = $config['showdel'];
	$showfilename = $config['showfilename'];
	$showbfb = $config['showbfb'];
	$style = $config['style'];
	$btnstyle = $config['btnstyle'];
	$htm = '';
	if(is_array($cutpic)){
		$htm = '<input type="hidden" relobj="yyuccutpic" rel="yyuc" />';		
		$url = '/@system/upload.html';
		$cutwidth =  isset($cutpic['width'])?intval($cutpic['width']):0;
		$cutheight =  isset($cutpic['height'])?intval($cutpic['height']):0;
		$buttonStyle = 	isset($cutpic['buttonStyle'])?$cutpic['buttonStyle']:'';
		$buttonClass = 	isset($cutpic['buttonClass'])?$cutpic['buttonClass']:'';
		$buttonText = isset($cutpic['buttonText'])?$cutpic['buttonText']:'确定剪裁';
		if($cutwidth==0||$cutheight==0){
			$aspectRatio = 'null';
		}else{
			$aspectRatio = $cutwidth/$cutheight;
		}
		$oncutsuccess = isset($cutpic['onsuccess'])?$cutpic['onsuccess']:'false';
	}else{
		$url = $surl;
		
	}
$htm .= <<<EOT
<iframe name="{$ttid}_if" style="position:absolute; left:-10000px;"></iframe>
<table  style="border:none;padding:0;margin:0;width:auto;display: inline-block;"><tr style="border:none;padding:0;margin:0;"><td style="border:none;padding:0;margin:0;width:auto;">
<div style="display:none" id="{$ttid}_div" action="$url" method="post" enctype="multipart/form-data" target="{$ttid}_if">
<span style="{$style}display:-moz-inline-box;display:inline-block;width:{$width}px;height:{$height}px;line-height: {$height}px;position: relative;">
<input name="YYUC_UPLOAD_file" style="position: absolute;height: 100%;width:{$width}px;right: 0;z-index:100000" type="file"/>
EOT;
if($btntype=='button'){
$htm .= <<<EOT
<button class="{$class}" style="{$btnstyle}display:block; position: absolute;height: 100%;z-index:99999;">{$text}</button>
EOT;
}else{
$htm .= <<<EOT
<input src="{$backgroundImg}" class="{$class}" type="{$btntype}" style="{$btnstyle}display:block; position: absolute;height: 100%;width:100%;z-index:1;line-height: {$lineheight}px;font-size:{$fontSize}px;color:{$fontColor};text-align: center;" value="{$text}"/>
EOT;
}

$htm .= <<<EOT
</span>
<input type="hidden" name="YYUC_UPLOAD_SIZE" value="{$size}"/>
<input type="hidden" name="YYUC_UPLOAD_ID" value="{$ttid}"/>
EOT;
//隐参添加
foreach ($data as $k=>$v){
$htm .= '<input type="hidden" name="'.$k.'" value="'.htmlspecialchars($v,ENT_QUOTES).'"/>';
}
$htm .= <<<EOT
</div>
</td><td style="border:none;padding:0;margin:0;width:auto;">
<span id="{$ttid}_span" style="display:-moz-inline-box;display:inline-block;width:{$width}px;height:{$height}px;">
</span>
</td>
EOT;
//删除按钮
if ($showdel){
$htm .= '<td style="border:none;padding:0;margin:0;width:auto;"><span id="'.$ttid.'_del" style="display: none;height:'.$height.'px;line-height: '.$height.'px;"><img src="'.$YYSYS.'img/close.png" syle="cursor:pointer;" title="删除"/></span></td>';
}
$htm .= <<<EOT
<td style="border:none;padding:0;margin:0;width:auto;"><span id="{$ttid}_loadpic" style="display: none;"><img src="{$YYSYS}img/loading/18.gif"/></span></td>
EOT;
if ($showbfb){
	$htm .= <<<EOT
<td style="border:none;padding:0;margin:0;width:auto;"><span id="{$ttid}_bfb" style="line-height:{$height}px;color:{$filenameColor};font-size:{$filenameSize}px;"></span></td>
EOT;
}
if ($showfilename){
$htm .= <<<EOT
<td style="border:none;padding:0;margin:0;width:auto;"><span id="{$ttid}_name" style="line-height:{$height}px;color:{$filenameColor};font-size:{$filenameSize}px;"></span></td>
EOT;
}
$htm .= <<<EOT
</tr></table>
<input type="hidden" name="$name" id="{$ttid}_text" value="{$value}"/>
<script>
$(function(){
EOT;
//隐参添加
if ($showdel&&$value!=''){
$htm .= '$("#'.$ttid.'_del").css("cursor","pointer").show();';
}
if ($picareaid!='' && $value!=''){
$htm .= "\n$('#{$picareaid}').html('<img src=\"{$value}?_='+(new Date()).getTime()+'\"/>');\n";
}
if ($showdel){
$htm .= <<<EOT
window.{$ttid}_clear = function(){
	$('#{$ttid}_bfb').html('');
	$('#{$ttid}_name').html('');
	if('{$picareaid}'!=''){ $('#{$picareaid}').html('');}
	$('#{$ttid}_del').hide();
	var oldkey = $.trim($('#{$ttid}_text').val());
	if(oldkey!=''&&oldkey.indexOf('/')==0){
		$('#{$ttid}_text').val('@-@'+oldkey);
	}else if(oldkey.indexOf('@-@')!=-1){
		var oldkey2 = oldkey.split('@-@');
		$('#{$ttid}_text').val('@-@'+oldkey2[1]);
	}else{
		$('#{$ttid}_text').val('');
	}
}
$('#{$ttid}_del').click(function(){
	if(!confirm('确定要删除吗？')){return;}
	window.{$ttid}_clear();
	if({$ondel}){
		{$ondel}('oldkey','{$ttid}');
	}
});
EOT;
}
$htm .= <<<EOT
	var {$ttid}_form = window.{$ttid}_form = $('<form style="position: absolute;left:-10000px;" id="{$ttid}" action="$url" method="post" enctype="multipart/form-data" target="{$ttid}_if"></form>');
	$('body').append({$ttid}_form);
	{$ttid}_form.html($('#{$ttid}_div').html());
	$('#{$ttid}_div').remove();
	window.{$ttid}_resize = function(){
	if($('#{$ttid}_span').is(':hidden')){
		{$ttid}_form.hide();
	}else{
		{$ttid}_form.show();
		var {$ttid}_offset = $('#{$ttid}_span').offset();
		{$ttid}_form.css('left',{$ttid}_offset.left).css('top',{$ttid}_offset.top);
	}
  		
	};
	setInterval(window.{$ttid}_resize,55);
	var {$ttid}pic = $('#{$ttid}').find('input[name="YYUC_UPLOAD_file"]');
	{$ttid}pic.css('opacity',0);
	{$ttid}pic.change(function(){
		var thefiles = $(this).val().replaceAll('\\\\','/').split('/');
		var filename = thefiles[thefiles.length-1];
		if($.trim(filename)==''){
			return;
		}
		//后缀校验
		var needsuffixs = ',{$type},'.toLowerCase();
		var suffixs = filename.split('.');
		if(needsuffixs.indexOf((','+suffixs[suffixs.length-1]+',').toLowerCase())==-1){
			if({$onfailure}){
				{$onfailure}('文件格式不被允许',$('#{$ttid}'),1,'{$ttid}');
			}
		}else{
EOT;
//选中事件
if($onselect!='false'){
$htm .= <<<EOT
			if({$onselect}){
				if(!{$onselect}(filename,$('#{$ttid}'),'{$ttid}')){
					return;
				}
			}
EOT;
}


$htm .= <<<EOT
			$('#{$ttid}_name').html(filename);
			window.{$ttid}_jd = 0;
			{$ttid}_process()
			$('#{$ttid}').submit();
			$('#{$ttid}_loadpic').show();

			$('iframe[name="{$ttid}_if"]').unbind('load').load(function(){
				if(!window.{$ttid}){
					$('#{$ttid}_loadpic').hide();
					$('#{$ttid}_bfb').html('');	
					if({$onfailure}){
						{$onfailure}('网络连接超时或文件大小超出',$('#ttid'),3,'{$ttid}');
						clearTimeout(window.{$ttid}jdproc);						
					}
				}
			});
		}
	});

EOT;

if(is_array($cutpic)){
	$htm .= <<<EOT
		window.{$ttid}_picpos = {};
		window.{$ttid}_picpos.cw = {$cutwidth};
		window.{$ttid}_picpos.ch = {$cutheight};
		window.{$ttid}_picpos.ctbl = {$aspectRatio};
EOT;
}
	
$htm .= <<<EOT

});
function {$ttid}_process(){
	if(window.{$ttid}_jd < 99){
		var nexttime = parseInt(Math.sqrt(window.{$ttid}_jd)*100)*2;
		window.{$ttid}_jd = window.{$ttid}_jd+1;
		$('#{$ttid}_bfb').html(window.{$ttid}_jd+'%');
		window.{$ttid}jdproc = setTimeout(function(){
			{$ttid}_process();
		},nexttime);
	}else{
		$('#{$ttid}_bfb').html('');
		$('#{$ttid}_loadpic').hide();
		if({$onfailure}){
			{$onfailure}('网络连接超时或文件大小超出',$('#ttid'),3,'{$ttid}');
		}
	}
}
function {$ttid}_success(url,key){
	$('#{$ttid}_del').show();
	window.{$ttid}_jd = 100;
	$('#{$ttid}_bfb').html('100%');
	$('#{$ttid}_loadpic').hide();
	clearTimeout(window.{$ttid}jdproc);
	var oldkey = $('#{$ttid}_text').val();
	if(oldkey.indexOf('@-@')!=-1){
		var oldkey2 = oldkey.split('@-@');
		$('#{$ttid}_text').val(key+'@-@'+oldkey2[1]);
	}else if(oldkey.indexOf('/')==0){
		$('#{$ttid}_text').val(key+'@-@'+oldkey);
	}else{
		$('#{$ttid}_text').val(key);
	}
EOT;


//图片放置位置
if($picareaid!=''){
$htm .= <<<EOT
	window.{$ttid}_upimg = $('<img id="{$ttid}_realpic" src="'+url+'"/>');
	$('#{$picareaid}').html('').append(window.{$ttid}_upimg);
EOT;
}

//成功上传事件
if($onsuccess!='false'){
	$htm .= <<<EOT
	if({$onsuccess}){
		{$onsuccess}(url,$('#{$ttid}'),'{$ttid}');
	}
EOT;
}
if($picareaid!=''){
//开启图片上传后剪裁
	if(is_array($cutpic)){
$htm .= <<<EOT
	window.{$ttid}_uppimg = $('<img id="{$ttid}_realpic" style="position: absolute;left:-999999px;top:-999999px;max-width:999999px;max-height:999999px;" src="'+url+'"/>');
	$('body').append(window.{$ttid}_uppimg);
	var centerbtn = $('<center><button style="{$buttonStyle}" class="{$buttonClass}">{$buttonText}</button></center>');
	$('#{$picareaid}').append(centerbtn);
	window.{$ttid}_picpos.key = key;
	var {$ttid}_savepos = function(c){
		var xbfb = window.{$ttid}_uppimg.width()/window.{$ttid}_upimg.width();
		var ybfb = window.{$ttid}_uppimg.height()/window.{$ttid}_upimg.height();
		window.{$ttid}_picpos.x = c.x*xbfb;
		window.{$ttid}_picpos.y = c.y*ybfb;
		window.{$ttid}_picpos.x2 = c.x2*xbfb;
		window.{$ttid}_picpos.y2 = c.y2*ybfb;
		window.{$ttid}_picpos.w = c.w*xbfb;
		window.{$ttid}_picpos.h = c.h*ybfb;
		if(c.w < 20 || c.h < 20){
			return false;
		}
		return true;
	}
	$('#{$ttid}_text').attr('needjc','yes');
	window.{$ttid}_upimg.ready(function(){
	$('#{$ttid}_bfb').html('');
	$('#{$ttid}_name').html('');
		$('#{$ttid}_realpic').Jcrop({
			aspectRatio: window.{$ttid}_picpos.ctbl
		},function(){window.{$ttid}_jcrop_api = this;});
	});
	
	centerbtn.find('button').click(function(){
		if(!{$ttid}_savepos(window.{$ttid}_jcrop_api.tellScaled())){
			return;
		}
EOT;
//隐参添加
$htm .= <<<EOT
		$(this).attr('disabled',true);
		
		_.ajax('{$surl}',window.{$ttid}_form.serialize()+'&'+$.param(window.{$ttid}_picpos),function(m){
			if(m!='no'){
				$('#{$ttid}_text').attr('needjc','no');
				window.{$ttid}_uppimg.remove();
				var thejchpic = $('<img src="{$STA}'+m+'?'+Math.random()+'"/>');
				$('#{$picareaid}').html(thejchpic);
				var oldkey = $('#{$ttid}_text').val();
				if(oldkey.indexOf('@-@')!=-1){
					var oldkey2 = oldkey.split('@-@');
					$('#{$ttid}_text').val(m+'@-@'+oldkey2[1]);
				}else if(oldkey.indexOf('/')==0){
					$('#{$ttid}_text').val(m+'@-@'+oldkey);
				}else{
					$('#{$ttid}_text').val(m);
				}
EOT;

//成功剪裁事件
if($oncutsuccess!='false'){
$htm .= <<<EOT
	if({$oncutsuccess}){
		{$oncutsuccess}('{$STA}'+m,$('#{$ttid}'),'{$ttid}');
	}
EOT;
}
$htm .= <<<EOT
			}else{
				alert('System error');
			}
		});
		return false;
	});
EOT;
	}
}

$htm .= <<<EOT
}
function {$ttid}_failure(size,type){
	if({$onfailure}){
		if(type==2){
			{$onfailure}('文件大小为：'+size+'K，超出了{$size}K的限制,上传失败！',$('#ttid'),2,'{$ttid}');
		}else if(type==4){
			{$onfailure}(size,$('#ttid'),4,'{$ttid}');
		}
		clearTimeout(window.{$ttid}jdproc);
		$('#{$ttid}_bfb').html('');
		$('#{$ttid}_name').html('');
		$('#{$ttid}_loadpic').hide();
	}
}
</script>
EOT;
return $htm;
}
/**
 * 
 * @param string $filepath 文件路径
 * @param string $funupload
 * @param string $funcut
 */
static function store_upload_file($filepath, $funupload=null,$funcut=null){
	self::save_upload_file($filepath, $funupload,$funcut,true);
}
/**
 * 存储上传的文件
 * $subpath 为相对于框架文件夹的目录<br/>
 * 如果没有访问权限的限制，建议存放在pub文件夹下<br/>
 * $subpath 不要包含文件后缀，方法会自动根据上传问价的后缀添加<br/>
 * 如：pub/user1/head，其中head为文件名如上传图片为gif格式则为head.gif<br/>
 * 一般无需调用，只在特殊指定上传控件的url参数时使用
 * @param string $subpath 相对网站目录的路径
 */
static function save_upload_file($subpath, $funupload=null,$funcut=null,$isfile=false){
	
	//临时目录清空
	if(trim(Conf::$remote_path) !=''){
		$syspathp = Conf::$local_remote;
		$STA = Conf::$remote_path;
	}else{
		$syspathp = YYUC_FRAME_PATH.YYUC_PUB.'/';
		$STA = '/';
	}
	File::clear_dir($syspathp.'upload/temp/',(time()-86400));
	File::creat_dir_with_filepath($syspathp.$subpath);	
	if(!empty($_FILES)&&isset($_FILES['YYUC_UPLOAD_file'])){
		//第一次上传
		$thisid = $_POST['YYUC_UPLOAD_ID'];		
		$thissize = intval($_SESSION['YYUC_last_upsizelimit'])*1000;
		if($thissize==0){
			$thissize = intval($_POST['YYUC_UPLOAD_SIZE'])*1000;
		}
		$f = $_FILES['YYUC_UPLOAD_file'];
		if($f['size']>$thissize){
			Response::text('<html><head><script>window.parent.'.$thisid.'=1;window.parent.'.$thisid.'_failure('.($f['size']/1000).',2);</script></head><body>1111</body></html>',Mime::$html);
		}else {
			if($isfile){
				$filename = $subpath;
			}else{
				$path_parts = pathinfo($f['name']);
				$theoldname = substr($f['name'],0,strrpos($f['name'],'.'));
				if(mb_strlen($theoldname)>12){
					$theoldname = mb_substr($theoldname,0,10).'..';
				}
				$oldname = base64_encode($theoldname);
				$oldname = str_replace('/', 'ITI@I', $oldname);
				$filename = $subpath.'_'.$oldname.'.'.$path_parts['extension'];
			}
			
			if(stripos($filename, '.php')!==false){
				die();
			}
			
			if (!move_uploaded_file($f['tmp_name'], $syspathp.$filename)) {
				Response::text('<html><head><script>window.parent.'.$thisid.'=1;window.parent.'.$thisid.'_failure("很抱歉上传发生未知错误，请联系平台客服。错误数据：'.$filename.'",4);</script></head><body>1111</body></html>',Mime::$html);
			}else{
				if($funupload !==null){
					call_user_func_array($funupload,array($syspathp.$filename));
				}
				Response::text('<html><head><script>window.parent.'.$thisid.'=1;window.parent.'.$thisid.'_success("'.$STA.$filename.'","'.$filename.'");</script></head><body>1111</body></html>',Mime::$html);
			}
		}
	}else if(Request::post('key')){
		//剪切
		$filename = Request::post('key');
		if(!file_exists($syspathp.$filename)){
			return false;
		}
		//剪切之后的处理
		$img_r = imagecreatefromstring(file_get_contents($syspathp.$filename));
		if($img_r===false){
			Response::text('no');
		}
		$dsw = Request::post('w');
		$dsh = Request::post('h');
		if(Request::post('cw')!='0'&&Request::post('ch')!='0'){
			$dsw = Request::post('cw');
			$dsh = Request::post('ch');
		}
		$dst_r = ImageCreateTrueColor($dsw,$dsh);
		imagecopyresampled($dst_r,$img_r,0,0,Request::post('x'),Request::post('y'),$dsw,$dsh,Request::post('w'),Request::post('h'));
		//$path_parts = pathinfo($filename);
		$oldnames = substr($filename,0,strrpos($filename,'.'));
		$oldnames = explode('_', $oldnames);
		$oldname = $oldnames[count($oldnames)-1];
		$filename2 = $subpath.'_'.$oldname.'.jpg';
		imagejpeg($dst_r,$syspathp.$filename2,90);
		if($filename2!=$filename){
			unlink($syspathp.$filename);
		}
		if($funcut !==null){
			call_user_func_array($funcut,array($syspathp.$filename2));
		}
		Response::text($filename2);
	}else{
		return false;
	}
	return true;
}
/**
 * 根据页面标识 存储上传的文件 通常用在Form提交中的预上传处理
 * @param string $key 上传控件的的name值
 * @param string $folderpath 框架文件夹下的相对文件夹路径
 * @param string $isFile 上一参数是否是文件，如果是则直接覆盖文件
 * @return string 此次存储的文件相对网站根目录的路径没有信息提交则返回false
 */
static function tosave_upload_file($key,$folderpath=null,$isFile = false){
	return self::_resave_upload_file(Request::post($key),$folderpath,$isFile);
}	
/**
 * 根据页面标识 存储上传的文件 通常用在Form提交中的预上传处理
 * @param string $key 上传控件的的name值提交上来的属性
 * @param string $folderpath pub文件夹下的相对文件夹路径
 * @param string $isFile 上一参数是否是文件，如果是则直接覆盖文件
 * @return string 此次存储的文件相对网站根目录的路径没有信息提交则返回false
 */
private static function _resave_upload_file($key,$folderpath=null,$isFile = false){
	//临时目录清空
	if(trim(Conf::$remote_path) !=''){
		$syspathp = Conf::$local_remote;
		$STA = Conf::$remote_path;
	}else{
		$syspathp = YYUC_FRAME_PATH.YYUC_PUB.'/';
		$STA = '/';
	}
	if(stripos($key, 'http')===0){
		//是修改且从未被修改过
		return $key;
	}elseif(trim($key)==''){
		//提交信息为空
		return false;
	}elseif(strpos($key, '/')===0){
		//是修改且从未被修改过
		return $key;
	}elseif (strpos($key, '@-@')===0){
		//删除上次的文件地址
		@unlink($syspathp.substr($key, 4));
		return false;
	}elseif (strpos($key, '@-@')!==false){
		//修改后删除上次的文件地址
		$keys = explode('@-@', $key);
		$key = $keys[0];
		@unlink($syspathp.$keys[1]);
	}
	if($folderpath===null){
		$folderpath = 'upload/auto/'.date('Y/m/d',time());
	}
	if(!$isFile){
		File::creat_dir($syspathp.$folderpath);
		$tsubppath = str_replace('//', '/',$folderpath.'/'.basename($key));
		$newpath = $syspathp.$folderpath.'/'.basename($key);
	}else{
		File::creat_dir_with_filepath($syspathp.$folderpath);
		$tsubppath = str_replace('//', '/',$folderpath);
		$newpath = $syspathp.$folderpath;
	}	
	@unlink($newpath);
	rename($syspathp.$key, $newpath);
	return $STA.$tsubppath;
}

}