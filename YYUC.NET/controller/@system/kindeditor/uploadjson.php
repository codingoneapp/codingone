<?php
//验证设置 必须的！！！
//access_control($fun);
Page::$need_view = false;

$subpath = 'upload/kindeditor/'.Request::get(1).'/'.date("Ymd").'/';
//文件保存目录路径
$save_path = YYUC_FRAME_PATH.YYUC_PUB.'/'.$subpath;
File::creat_dir($save_path);
//文件保存目录URL
$save_url = Conf::$http_path.$subpath;
//定义允许上传的文件扩展名
$ext_arr = array(
	'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
	'flash' => array('swf', 'flv'),
	'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
	'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf'),
);
//最大文件大小 1M 
$max_size = 1000000;
//有上传文件时
if (empty($_FILES) === false) {
	//原文件名
	$file_name = $_FILES['imgFile']['name'];
	//服务器上临时文件名
	$tmp_name = $_FILES['imgFile']['tmp_name'];
	if(stripos($file_name, '.php')!==false){
		die();
	}
	if(stripos($tmp_name, '.php')!==false){
		die();
	}
	//文件大小
	$file_size = $_FILES['imgFile']['size'];
	//检查文件名
	if (!$file_name) {
		kindeditoralert("请选择文件。");
	}
	//检查是否已上传
	if (@is_uploaded_file($tmp_name) === false) {
		kindeditoralert("临时文件可能不是上传文件。");
	}
	//检查文件大小
	if ($file_size > $max_size) {
		kindeditoralert("上传文件大小超过限制。");
	}
	//检查目录名
	$dir_name = empty($_GET[1]) ? 'image' : trim($_GET[1]);
	if (empty($ext_arr[$dir_name])) {
		kindeditoralert("目录名不正确。");
	}
	//获得文件扩展名
	$temp_arr = explode(".", $file_name);
	$file_ext = array_pop($temp_arr);
	$file_ext = trim($file_ext);
	$file_ext = strtolower($file_ext);
	//检查扩展名
	if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
		kindeditoralert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。");
	}
	//新文件名
	$new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
	//移动文件
	$file_path = $save_path . $new_file_name;
	if (move_uploaded_file($tmp_name, $file_path) === false) {
		kindeditoralert("上传文件失败。");
	}
	$file_url = $save_url . $new_file_name;
	
	header('Content-type: text/html; charset=UTF-8');
	echo json_encode(array('error' => 0, 'url' => $file_url));
	exit;
}else{
	kindeditoralert("上传文件大小超过限制,上传文件失败。");
}

function kindeditoralert($msg) {
	header('Content-type: text/html; charset=UTF-8');
	echo json_encode(array('error' => 1, 'message' => $msg));
	exit;
}
?>