<?php
class Request{
	
	/**
	 * 获取get方式提交的参数<br/>
	 * 参数为null则为判断是否有get提交
	 * @param string $pam 参数名称
	 * @param mixed $default 如果不存在请求内容则为传入的默认值，此值可以为闭包函数
	 * @return string 参数值
	 */
	public static function get($pam=null, $default = null){
		if($pam===null){
			return count($_GET)>1?true:false;
		}
		return isset($_GET[$pam]) ? trim($_GET[$pam]) : ($default === null? false : YYUC::value($default));
	}
	
	/**
	 * 获取post方式提交的参数<br/>
	 * 参数为null则为判断是否有post提交
	 * @param string $pam 参数名称
	 * @param mixed $default 如果不存在请求内容则为传入的默认值，此值可以为闭包函数
	 * @return mixed 参数值 字串或数组
	 */
	public static function post($pam=null, $default = null,$xssclean = false){
		if($pam===null){
			return count($_POST)>0;
		}
		if(!isset($_POST[$pam])){
			return $default === null ? false : YYUC::value($default);
		}
		if($xssclean){
			$_POST[$pam] = htmlspecialchars($_POST[$pam]);
		}
		if(is_string($_POST[$pam])){
			return get_magic_quotes_gpc() ? stripslashes($_POST[$pam]) : $_POST[$pam];
		}else if(is_array($_POST[$pam])){
			if(get_magic_quotes_gpc()){
				$res_arr = array();
				foreach ($_POST[$pam] as $res){
					$res_arr [] = stripslashes(trim($res));
				}
				$_POST[$pam] = $res_arr;
			}
			return $_POST[$pam];
		}
	}
	
	/**
	 * 获取提交的参数(包含get,post等方式)<br/>
	 * 参数为null则为判断是否有post提交
	 * @param string $pam 参数名称
	 * @param mixed $default 如果不存在请求内容则为传入的默认值，此值可以为闭包函数
	 * @return mixed 参数值 字串或数组
	 */
	public static function obtain($pam=null, $default = null){
		if($pam===null){
			return count($_REQUEST)>0;
		}
		if(!isset($_REQUEST[$pam])){
			return $default === null ? false : YYUC::value($default);
		}
		if(is_string($_REQUEST[$pam])){
			return get_magic_quotes_gpc() ? stripslashes($_REQUEST[$pam]) : $_REQUEST[$pam];
		}else if(is_array($_REQUEST[$pam])){
			if(get_magic_quotes_gpc()){
				$res_arr = array();
				foreach ($_REQUEST[$pam] as $res){
					$res_arr [] = stripslashes(trim($res));
				}
				$_REQUEST[$pam] = $res_arr;
			}
			return $_REQUEST[$pam];
		}
	}
	
	/**
	 * 获得客户端IP(真实的IP地址)
	 * @return string 客户端IP地址
	 */
	public static function ip(){
		$ip = null;		
		if($_SERVER["HTTP_ALI_CDN_REAL_IP"]){
			$ip = $_SERVER["HTTP_ALI_CDN_REAL_IP"];
		}elseif($_SERVER["HTTP_X_FORWARDED_FOR"]){
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}elseif($_SERVER["HTTP_CLIENT_IP"]){
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}elseif ($_SERVER["REMOTE_ADDR"]){
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		return $ip;
	}
	/**
	 * 获得客户端位置
	 * @return array('国家','省','市','电信') | false
	 */
	public static function local(){
		return YYUC::local_ip(self::ip());
	}
	
	
	/**
	 * 判断用户是否通过代理访问<br/>
	 * 对于超匿名代理无法判断出来<br/>
	 * 如果网站本身采用了cdn加速等功能的话正常用户会被误判断成代理访问的。
	 * 
	 * @return string 客户端IP地址
	 */
	public static function is_proxy(){
		return $_SERVER["REMOTE_ADDR"] != self::ip();
	}
	
	/**
	 * URL请求中含有分页参数表示的页数<br/>
	 * 没有分页参数则返回1
	 * @return integer 页数
	 */
	public static function page($num=null){
		if($num===null){
			if(isset($_SERVER['PAGING_NUM'])){
				return intval($_SERVER['PAGING_NUM']);
			}else{
				return 1;
			}
		}else{
			$_SERVER['PAGING_NUM'] = $num;
		}
		
	}
	
	/**
	 * 层级式URL解析时的各级名称<br/>
	 * 级别索引 从0开始
	 * @param integer $index  索引
	 * @return string URL中的路径名称
	 */
	public static function part($index = null){
		if($index === null){
			return count($_SERVER['PATH_INFO']);
		}elseif($index < 0){
			return $_SERVER['PATH_INFO'][count($_SERVER['PATH_INFO'])+$index];
		}else{
			return $_SERVER['PATH_INFO'][$index];
		}
	}
	
	/**
	 * 层级式URL解析时的各级名称<br/>
	 * 级别索引 从0开始
	 * @param integer $index  索引
	 * @return string URL中的路径名称
	 */
	public static function part_nopage($index = null){
		if($index === null){
			return count($_SERVER['NOPAGE_PATH_INFO']);
		}elseif($index < 0){
			return $_SERVER['NOPAGE_PATH_INFO'][count($_SERVER['NOPAGE_PATH_INFO'])+$index];
		}else{
			return $_SERVER['NOPAGE_PATH_INFO'][$index];
		}
	}
	
	/**
	 * 层级式URL解析时的各级名称<br/>
	 * 级别索引 从0开始
	 * @param integer $index  索引
	 * @return string URL中的路径名称
	 */
	public static function part_noparam($index = null){
		if($index === null){
			return count($_SERVER['NOPARAM_PATH_INFO']);
		}elseif($index<0){
			return $_SERVER['NOPARAM_PATH_INFO'][count($_SERVER['NOPARAM_PATH_INFO'])+$index];
		}else{
			return $_SERVER['NOPARAM_PATH_INFO'][$index];
		}
	}
	/**
	 * 层级式URL解析时的数组<br/>
	 * @return array 层级式URL解析时的数组
	 */
	public static function parts(){
		return $_SERVER['PATH_INFO'];
	}
	
	
	/**
	 * 层级式URL解析时的各级名称<br/>
	 * 级别索引 从右向左 从0开始
	 * @param integer $index  索引
	 * @return string URL中的路径名称
	 */
	public static function rpart($index = null){
		$conlen = count($_SERVER['PATH_INFO']);
		if($index === null){
			return $conlen;
		}else{
			return $_SERVER['PATH_INFO'][$conlen-$index-1];
		}
	}
	
	/**
	 * 判断当前请求是不是常规缓存的请求
	 *
	 * @return string URL
	 */
	public static function is_normal_cache(){
		return isset($_SERVER['TRANS_NORMAL_CACHE']);
	}
	
	/**
	 * 获得用户请求的真实不带分页的URL<br/>
	 * 不带请求后缀
	 * 
	 * @return string URL
	 */
	public static function url_nopage(){
		return $_SERVER['NO_PAGINATION_URI'];
	}
	
	/**
	 * 获得用户请求的真实不带参数和分页的URL<br/>
	 * 不带请求后缀
	 *
	 * @return string URL
	 */
	public static function url_nopam(){
		return $_SERVER['NO_PARAM_URI'];
	}
	
	/**
	 * 获得用户请求的加工过的URL<br/>
	 * 不带请求后缀
	 *
	 * @return string URL
	 */
	public static function url(){
		return $_SERVER['REAL_REQUEST_URI'];
	}
	/**
	 * 获得用户请求的真实的URL<br/>
	 * 不带请求后缀
	 *
	 * @return string URL
	 */
	public static function user_url(){
		return $_SERVER['USER_REQUEST_URI'];
	}
	
	/**
	 * 获得此次请求的来路URL<br/>
	 * 不带请求后缀
	 *
	 * @return string URL
	 */
	public static function from($includeexturl = false){
		if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], conf::$http_path) !== false){
			return $_SERVER['HTTP_REFERER'];
		}elseif($includeexturl && isset($_SERVER['HTTP_REFERER'])){
			return $_SERVER['HTTP_REFERER'];
		}
		return null;
	}
	
	/**
	 * 获得用户请求的客户端语言<br/>
	 *
	 * @return string
	 */
	public static function lan(){
		return $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	}
	
	/**
	 * 控制器层级式URL解析时的各级名称<br/>
	 * 级别索引 从0开始
	 * @param integer $index  索引
	 * @return string URL中的路径名称
	 */
	public static function part_control($index = null){
		if($index === null){
			return count($_SERVER['YYUC_PATH_INFO']);
		}elseif($index <0){
			return $_SERVER['YYUC_PATH_INFO'][count($_SERVER['YYUC_PATH_INFO'])+$index];
		}else{
			return $_SERVER['YYUC_PATH_INFO'][$index];
		}		
	}
	
	/**
	 * 获得ajax请求的JSON数据 并转换为PHP数组<br/>
	 * 客户端请求的参数名称必须是:'data'<br/>
	 * @return array 请求数组
	 */
	public static function json($arrtype = true){
		$str = self::post('data');
		if(empty($str)){
			return null;
		}
		return json_decode($str,$arrtype);
	}
	
	/**
	 * 获得当前host<br/>
	 * @return string HOST
	 */
	public static function host(){
		return $_SERVER ['HTTP_HOST'];
	}
	
	/**
	 * refer检测<br/>
	 * @return string $include
	 */
	public static function check_reference($include=''){
		$refer = trim($_SERVER['HTTP_REFERER']);
		if(strpos($refer, $_SERVER ['HTTP_HOST'])!==false){
			if($include==''){
				return true;
			}else{
				if(strpos($refer, $include)!==false){
					return true;
				}
			}
		}
		
		return false;
	}
	
}