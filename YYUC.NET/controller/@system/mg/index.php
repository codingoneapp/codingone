<?php
if(!Auth::is_admin('YYUC_sys')){
	die();
}
$menu = array(
	array(
		'name' => '框架基本信息',
		'url' => 'jc',
		'children' => array(),
	),
	array(
		'name' => '建站工具',
		'url' => 'tool',
		'children' => array(
			array(
				'name' => 'CSS下载',
				'url' => 'css',
			),
			array(
				'name' => '源码加密',
				'url' => 'jm',
			),
		),
	),
);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<title>后台管理系统</title>
		<script type="text/javascript">var yyuc_jspath = "/@system/";</script>
		<script type="text/javascript" src="/@system/js/jquery.js"></script>
		<script type="text/javascript" src="/@system/js/yyucadapter.js"></script>
		<!-- stylesheets -->
		<link rel="stylesheet" type="text/css" href="/@system/mg/reset.css" />
		<link rel="stylesheet" type="text/css" href="/@system/mg/style.css" media="screen" />
		<link id="color" rel="stylesheet" type="text/css" href="/@system/mg/colors/blue.css" />
		<style>
		html,body{
			overflow: hidden;
		}
		#content
		</style>
		<script type="text/javascript">
		var style_path = "/@system/mg/colors";
		</script>
		<!-- scripts (jquery) -->
		<!--[if IE]><script language="javascript" type="text/javascript" src="/@system/mg/excanvas.min.js"></script><![endif]-->
		<!-- scripts (custom) -->
		<script src="/@system/mg/smooth.js" type="text/javascript"></script>
		<script src="/@system/mg/smooth.menu.js" type="text/javascript"></script>
		<script src="/@system/mg/smooth.table.js" type="text/javascript"></script>
		<script type="text/javascript">
		$(function(){
			if(window.parent&&window.parent!=window){
				window.parent.location = location.href;
			}
			$('#content,#rightmain,#left').height($(window).height()-115);
			$(window).resize(function(){
				$('#content,#rightmain,#left').height($(window).height()-115);
			});
		});
		</script>
	</head>
	<body>
		<div id="colors-switcher" class="color">
				<a href="javascript:;" class="blue" title=""></a>
				<a href="javascript:;" class="green" title=""></a>
				<a href="javascript:;" class="brown" title=""></a>
				<a href="javascript:;" class="purple" title=""></a>
				<a href="javascript:;" class="red" title=""></a>
				<a href="javascript:;" class="greyblue" title=""></a>
		</div>
		<!-- header -->
		<div id="header">
			<div id="header-inner">
				<div id="home">
					<a href="http://www.yyuc.net" title="Home"></a>
					<h1>YYUC框架开发管理中心</h1>
				</div>
				<!-- quick -->
				<ul id="quick">
				<!-- 
				<li>
				<a href="mmsz.html" title="Settings"  target="rightmain"><span class="icon"><img src="/@system/mg/icons/cog.png" alt="Settings" /></span><span>密码设置</span></a>
				</li>
				-->				
					<li>				
						<a href="login-renew.html" title="Settings"><span class="icon"><img src="/@system/mg/icons/cross.png" alt="Settings" /></span><span>退出系统</span></a>
					</li>
				</ul>
				<!-- end quick -->
				<div class="corner tl"></div>
				<div class="corner tr"></div>
			</div>
		</div>
		<!-- end header -->
		<!-- content -->
		<div id="content">
			<!-- end content / left -->
			<div id="left">
				<div id="menu">
				<?php $mainurl = '@system/mg'; ?>
				<?php $__i=0; foreach ((array)$menu as $m1) { $__i++; ?>
				<?php $m1url = $mainurl.'/'.$m1['url']; ?>
				<h6 id="h-menu-<?php echo $m1['url']; ?>">
				<a class="lv1" href="javascript:;" id="<?php echo $m1['url']; ?>"><span><?php echo $m1['name']; ?></span></a>
				</h6>
				<ul id="menu-<?php echo $m1['url']; ?>" class="closed">
				<?php $m1s = $m1['children']; ?>
					<?php $__i=0; foreach ((array)$m1s as $m2) { $__i++; ?>
					<?php $m2url = $m1url.'/'.$m2['url']; ?>
						<?php if (isset($m2['children'])){ ?>
							<?php $m2s = $m2['children']; ?>					
							<li class="collapsible">
							<a class="plus lv2" href="javascript:;"><?php echo $m2['name']; ?></a>
							<ul class="collapsed">
								<?php $__i=0; foreach ((array)$m2s as $m3) { $__i++; ?>
								<?php $m3url = $m2url.'/'.$m3['url']; ?>
								<li>
								<a href="<?php if (strpos($m3url, 'http')===0){  echo $m3url;  }else{ ?>/<?php echo $m3url; ?>/<?php } ?>" target="<?php if (isset($m3['target'])){  echo $m3['target'];  }else{ ?>rightmain<?php } ?>"><?php echo $m3['name']; ?></a>
								</li>
								<?php } ?>
							</ul>
							</li>
						<?php }else{ ?>
							<li>
							<a href="<?php if (strpos($m2url, 'http')===0){  echo $m2url;  }else{ ?>/<?php echo $m2url; ?>/<?php } ?>" target="<?php if (isset($m2['target'])){  echo $m2['target'];  }else{ ?>rightmain<?php } ?>"><?php echo $m2['name']; ?></a>
							</li>
						<?php } ?>					
					<?php } ?>
				</ul>
				<?php } ?>			
				<ul id="menu-xxnrh6" class="closed">	
													
				</ul>
				</div>
			</div>
			<!-- end content / left -->
			<!-- content / right -->
			<div id="right">
			<iframe frameborder="0"  id="rightmain" name="rightmain" style="width:100%" src="home/index.html"></iframe>

			</div>
			<!-- end content / right -->
		</div>
		<!-- end content -->
		<!-- footer -->
		<div id="footer" >
			<p>Copyright &copy; 2010-2013 YYUC All Rights Reserved.</p>
		</div>
		<!-- end footert -->
	</body>
</html>