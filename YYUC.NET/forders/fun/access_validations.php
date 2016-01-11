<?php
/**
 * 此方法是框架访问权限验证的钩子方法<br/>
 * $uri 为请求的URL的相对路径<br/>
 * 如:http://www.yyuc.com/admin/set/index.html 则$uri为:admin/set/index<br/>
 * $uri为实际的控制器路径，而并非用户真实的请求路径(开启自定义路由的情况下两者并不相同)
 * @param $uri
 */
function access_validations($uri){
	
}
/**
 * 数据执行校验
 *
 * @param DBDes $dbdes
 */
function db_validations($dbdes){

}

/**
 *  404挽救
 */
function rescue_404($url){
	
}
/*****************自定义的页面验证写在此处*******************/

?>
