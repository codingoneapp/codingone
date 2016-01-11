<?php
/**
ini_set('display_errors', true);
error_reporting(E_ALL);
Page::ignore_view();
ignore_user_abort(true);
set_time_limit(0);
ini_set('memory_limit','2048M');
$m = new SampleModel();
$errmsg = '';
if($m->try_post()){
	if(function_exists('beast_encode_file')){
		$php = $m->phptext;
		$baseurl = $m->baseurl;
		
		if(trim($php) !=''){
			$tempfile = YYUC_FRAME_PATH.'sys/YYUC_TEMP_JM_PHP';
			file_put_contents($tempfile, $php);
			beast_encode_file($tempfile,$tempfile.'HOU');
			Response::download('jm.php',file_get_contents($tempfile.'HOU'),Mime::$php);
		}elseif(trim($baseurl) !=''){
			$transpath = $baseurl;
			if(is_dir($transpath)){
				
				File::all_copy($transpath, $transpath.'_beast_encode__temp');
				die('1234');
				File::work_with_file($transpath.'_beast_encode__temp',function($fn){
					if(strpos($fn, '.php')!==false){
						if(defined('YYUC_VPDDOM')){
							file_put_contents($fn,str_replace('<?php', '<?php if(YYUC_VPDDOM!="'.YYUC_VPDDOM.'")die();', file_get_contents($fn)));
						}
						beast_encode_file($fn,str_replace('_beast_encode__temp', '', $fn));
					}					
				});
				File::remove_dir( $transpath.'_beast_encode__temp');
				$errmsg='操作完成';
			}else{
				$errmsg='错误的文件夹路径';
			}
		}
	}else{
		$errmsg='未安装加密组件';
	}	
}
**/
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<title>源代码加密</title><script type="text/javascript">var yyuc_jspath = "/@system/";</script><script type="text/javascript" src="/@system/js/jquery.js"></script><script type="text/javascript" src="/@system/js/yyucadapter.js"></script>
		<!-- stylesheets -->
		<link rel="stylesheet" type="text/css" href="/@system/mg/reset.css" />
		<link rel="stylesheet" type="text/css" href="/@system/mg/style.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="/@system/mg/style_full.css" />
		<link id="color" rel="stylesheet" type="text/css" href="/@system/mg/colors/blue.css" />
		<!--[if IE]><script language="javascript" type="text/javascript" src="/@system/mg/excanvas.min.js"></script><![endif]-->
		<script src="/@system/mg/smooth.js" type="text/javascript"></script>
		<script src="/@system/mg/smooth.table.js" type="text/javascript"></script>
		<script type="text/javascript">
		$(function(){
			<?php if($errmsg !=''){
				?>
				tusi('<?php echo $errmsg;?>');
				<?php 
			}?>
		});
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
						<h5>源代码加密</h5>
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
									<th style="width: 100px;">加密文件夹<br/>(工程目录下)</th>
									<td><?php echo $m->text('baseurl'); ?></td>
								</tr>
								
								<tr>
									<th style="width: 100px;">
									PHP文本：<br/>
									</th>
									<td><?php echo $m->textarea('phptext','style="height:300px;width:99%;"'); ?></td>
								</tr>
						</table>
						<!-- pagination -->
						<div class="pagination pagination-left">
							<div class="results">
							<button type="submit"  class="button">确定加密</button>
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