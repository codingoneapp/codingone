<?php
Page::ignore_view();
if('renew'==Request::get(1)){
	die('11');
	Session::clear();
	Cookie::clear();
}
$m = new SampleModel();
if($m->try_post()){
	if($m->pwd == Conf::$management_center_password){
		Auth::im_admin('YYUC_sys');
		Redirect::to('index');
	}else{
		Session::once('logerr','登录失败！');
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>YYUC开发管理中心</title>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<script type="text/javascript">var yyuc_jspath = "/@system/";</script>
		<script type="text/javascript" src="/@system/js/jquery.js"></script>
		<script type="text/javascript" src="/@system/js/yyucadapter.js"></script>
		<!-- stylesheets -->
		<link rel="stylesheet" type="text/css" href="/@system/mg/reset.css" />
		<link rel="stylesheet" type="text/css" href="/@system/mg/style.css" media="screen" />
		<link id="color" rel="stylesheet" type="text/css" href="/@system/mg/colors/blue.css" />
		<style>
		html,body{ overflow: hidden;}
		</style>
		<!-- scripts (jquery) -->
		<script type="text/javascript">
		var style_path = "/@system/mg/colors";
			$(document).ready(function () {
				$("input.focus").focus(function () {
					if (this.value == this.defaultValue) {
						this.value = "";
					}
					else {
						this.select();
					}
				});
				$("input.focus").blur(function () {
					if ($.trim(this.value) == "") {
						this.value = (this.defaultValue ? this.defaultValue : "");
					}
				});
				if(window.parent && window.parent != window){
					window.parent.location.href = location.href;
				}
			});
		</script>
		<script src="/@system/mg/smooth.js" type="text/javascript"></script>
	</head>
	<body>
		<div id="login">
			<!-- login -->
			<div class="title">
				<h5>YYUC开发管理中心登录</h5>
				<div class="corner tl"></div>
				<div class="corner tr"></div>
			</div>
			<?php if(hold('logerr')){?>
			<div class="messages">
				<div id="message-error" class="message message-error">
					<div class="image">
						<img src="/@system/mg/icons/error.png" alt="Error" height="32" />
					</div>
					<div class="text">
						<h6></h6>
						<span><?php echo once('logerr');?></span>
					</div>
					<div class="dismiss">
						<a href="#message-error"></a>
					</div>
				</div>
			</div>
			<?php }?>
			<div class="inner">
				<form action="" method="post">
				<div class="form">
					<!-- fields -->
					<div class="fields">
						<div class="field">
							<div class="label">
								<label for="password">密码:</label>
							</div>
							<div class="input">
							<?php echo $m->password('pwd','size="40" class="focus"');?>
							</div>
						</div>
						<div class="buttons">
							<input type="submit" value="登录" />
						</div>
					</div>
					<!-- end fields -->
				</div>
				</form>
			</div>
			<!-- end login -->
			<div id="colors-switcher" class="color">
				<a href="" class="blue"></a>
				<a href="" class="green"></a>
				<a href="" class="brown"></a>
				<a href="" class="purple"></a>
				<a href="" class="red"></a>
				<a href="" class="greyblue"></a>
			</div>
		</div>
	</body>
</html>