<?php
/**框架时间缓存模式*/
define('CACHE_TIME', 1);
/**框架数据库监测缓存模式*/
define('CACHE_DB_MONITOR', 2);
/**常规缓存模式*/
define('CACHE_NORMAL', 3);

class Page{
	/**请求是否需要缓存输出,默认:false没有缓存。<br/>CACHE_TIME：框架时间模式<br/>CACHE_DB_MONITOR：框架数据库监测模式<br/>CACHE_NORMAL:常规模式<br/>注意框架缓存模式下 before_action无论是否开启缓存都会被执行*/
	public static $cache_type = false;
	/**缓存页面保存的时间(单位：小时)（默认null 则为无限时间保留） */
	public static $cache_time = null;
	/**数据库监测模式下缓存检测的数据表，请以逗号分开各个表名如：'users,events'*/
	public static $cache_dbs = null;
	/**此次请求是否要指向视图,默认:true*/
	public static $need_view = true;
	/**此次请求要指向的自定义视图,默认:null*/
	public static $my_view = null;	
	/**存储model验证的错误信息*/
	public static $model_err = array();
	/**存储后台强制前台执行的JS语句信息*/
	public static $js_arr = array();
	/**存储i18n配置文件信息*/
	public static $i18n = null;	
	/**此次请求执行之前的过滤器,请不要修改此参数*/
	public static $sys_before_action = null;
	/**是否含有表单令牌并验证通过*/
	public static $tk_ok = false;
	/**令牌验证数据*/
	public static $tk_str = null;
	/**Memcached缓存服务器*/
	public static $memcached = null;
	public static $memcached_name = null;	
	
	/**
	 * 设置对应的view视图
	 * 视图的相对路径<br/>
	 * 根目录定位请开头补“/”
	 * @param string $viewpam 视图名称
	 */
	public static function view($viewpam){
		if(strpos($viewpam, '/')===0){
			Page::$my_view = substr($viewpam, 1);
		}elseif(strpos(Index::$me->controller_path, '/')===false){
			Page::$my_view = $viewpam;
		}else{
			Page::$my_view = dirname(Index::$me->controller_path).'/'.$viewpam;
		}
	}
	
	/**
	 * 系统静态文件地址返回
	 * @param string $folder 相对根路径的文件或文件夹路径
	 */
	public static function asset($folder){
		return (empty(Conf::$remote_path) ? '/': Conf::$remote_path).$folder;
	}
	
	/**
	 * 忽略此次请求的视图(控制器执行之后直接退出)
	 */
	public static function ignore_view(){
		self::$need_view = false;
	}
	
	/**
	 * 此次请求的视图为空
	 */
	public static function empty_view(){
		self::$my_view = '@EMPTY';
	}
	/**
	 * 页面访问控制函数<br/>
	 * 必须在控制器首行加入
	 * @param string $fun 验证函数名称 定义在 /fun/access_validations.php下php函数
	 */
	public static function access_control($fun){
		self::$sys_before_action = $fun;
		call_user_func($fun);
	}
	
	/**
	 * 页面缓存方式设置为常规缓存
	 */
	public static function cache_normal(){
		self::$cache_type = CACHE_NORMAL;
		//如果开启了常规缓存 但请求的地址不是常规缓存后缀
		if($_SERVER['TRANS_NORMAL_CACHE'] !== true){
			Redirect::to_404();
		}
	}
	
	/**
	 * 页面缓存方式设置为时间缓存
	 * @param integer $time 缓存保留时间(单位：小时)
	 */
	public static function cache_time($time = null){
		self::$cache_type = CACHE_TIME;
		self::$cache_time = $time;
	}
	
	/**
	 * 页面缓存方式设置为库表检测
	 * @param string $dbs 检测的数据表，请以逗号分开各个表名如：'users,events'
	 */
	public static function cache_dbs($dbs){
		self::$cache_type = CACHE_DB_MONITOR;
		self::$cache_dbs = $dbs;
	}
	/**
	 * 页面级缓存设置
	 */
	public static function set($k,$v = null){
		$_SERVER['YYUC_PTEMP'.$k] = $v;
	}
	/**
	 * 页面级缓存读取
	 */
	public static function get($k, $default = null){
		if(isset($_SERVER['YYUC_PTEMP'.$k])){
			return $_SERVER['YYUC_PTEMP'.$k];
		}
		return $default === null ? false : YYUC::value($default);
	}
}
?>