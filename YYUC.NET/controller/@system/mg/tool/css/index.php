<?php
/**
ignore_user_abort(true);
set_time_limit(0);
ini_set('memory_limit','2048M');
$m = new SampleModel();
if($m->try_post()){
	$css = $m->css;
	$baseurl = $m->baseurl;
	$subpath = $m->subpath;
	$csspath = $m->csspath;
	if(trim($css) ==''){
		$css = file_get_contents($baseurl);
	}	
	preg_match_all("/\s*url\s*\(([^\)]+)\)/i",$css,$links);
	$csss = array_unique($links[1]);
	
	$imgpath = YYUC_FRAME_PATH.'view/'.conf::$view_folder.'/@style/media/images/'.$subpath.'/';
	$csspath = YYUC_FRAME_PATH.'view/'.conf::$view_folder.'/@style/css/'.$csspath;
	File::creat_dir($imgpath);
	File::creat_dir_with_filepath($csspath);
	
	$i=0;
	foreach ($csss as $c){
		$c = str_replace('"', '', $c);
		$c = str_replace("'", '', $c);
		$url = HttpClient::dealUrl($baseurl, trim($c));
		$basename = (++$i).basename($url);
		//存储图片
		file_put_contents($imgpath.$basename, file_get_contents($url));
		$css = str_replace($c, 'img@'.$subpath.'/'.$basename, $css);
	}
	$css = str_ireplace('gbk','utf-8', $css);
	$css = str_ireplace('gb2312', 'utf-8', $css);
	$css = str_ireplace('宋体', 'SimSun', $css);
	$css = str_ireplace('微软雅黑', 'Microsoft YaHei', $css);
	$css = str_ireplace('黑体', 'SimHei', $css);
	$css = str_ireplace(iconv('utf-8','gbk', '宋体')  , 'SimSun', $css);
	$css = str_ireplace(iconv('utf-8','gbk', '微软雅黑')  , 'Microsoft YaHei', $css);
	$css = str_ireplace(iconv('utf-8','gbk', '黑体')  , 'SimHei', $css);	
	file_put_contents($csspath, $css);
	$m->css = $css;	
}
**/
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<title>css图片下载</title><script type="text/javascript">var yyuc_jspath = "/@system/";</script><script type="text/javascript" src="/@system/js/jquery.js"></script><script type="text/javascript" src="/@system/js/yyucadapter.js"></script>
		<!-- stylesheets -->
		<link rel="stylesheet" type="text/css" href="/@system/mg/reset.css" />
		<link rel="stylesheet" type="text/css" href="/@system/mg/style.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="/@system/mg/style_full.css" />
		<link id="color" rel="stylesheet" type="text/css" href="/@system/mg/colors/blue.css" />
		<!--[if IE]><script language="javascript" type="text/javascript" src="/@system/mg/excanvas.min.js"></script><![endif]-->
		<script src="/@system/mg/smooth.js" type="text/javascript"></script>
		<script src="/@system/mg/smooth.table.js" type="text/javascript"></script>
		<script type="text/javascript">
		
		</script>
		<style type="text/css">
		#maintt tr td input{
			width: 95%;
		}
		</style>
	</head>
	<body>
		<div id="content" style="margin: 0px 0px 0px 0px;">
			<div id="right" style="margin: 0px 0px 0px 0px;">
				<div class="box" style="margin: 0px 0px 0px 0px;">
					<div class="title">
						<h5>css图片下载</h5>
						<div class="search">
						<button class="button" onclick="location.href=location.href;">刷新</button>
						<button class="button" onclick="history.go(-1);">返回</button>
						</div>
					</div>
					<!-- end box / title -->
					<div class="table">
						<form action="index.html" method="post" id="bcform">
						 
						<table id="maintt">
								<tr>
									<th style="width: 100px;">基准url：</th>
									<td><?php echo $m->text('baseurl'); ?></td>
								</tr>
								<tr>
									<th style="width: 100px;">相对css路径(文件)：</th>
									<td><?php echo $m->text('csspath'); ?></td>
								</tr>
								<tr>
									<th style="width: 100px;">相对images路径(文件夹)：</th>
									<td><?php echo $m->text('subpath'); ?></td>
								</tr>
								<tr>
									<th style="width: 100px;">
									css文本：<br/>
									保存在C:/css文件夹下
									</th>
									<td><?php echo $m->textarea('css','style="height:300px;width:99%;"'); ?></td>
								</tr>
						</table>
						<!-- pagination -->
						<div class="pagination pagination-left">
							<div class="results">
							<button type="submit"  class="button">确定下载</button>
							</div>
						</div>
						</form>
					</div>
				</div>
			</div>
			<!-- end content / right -->
		</div>
		<!-- end content -->
	</body>
</html>