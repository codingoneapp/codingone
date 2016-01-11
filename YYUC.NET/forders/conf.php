<?php
class Conf{
/////////////////////框架管理中心登录配置
	/**登录页面~框架管理中心登录页面,可任意定义 如:config.yy 访问地址则为:http://www.yourdomain.com/config.yy*/
	public static $management_center_target = "config.yy";
	/**登录密码 ~框架管理中心登录密码 尽量复杂些*/
	public static $management_center_password = "yyuc.net";
/////////////////////主数据库配置 编码必须为 utf-8
	/**数据库地址~*/
	public static $db_host = "127.0.0.1";
	/**数据库端口~*/
	public static $db_port = "3306";
	/**数据库名~*/
	public static $db_dbname = "test";
	/**数据库用户名~*/
	public static $db_username = "root";
	/**数据库密码~*/
	public static $db_password = "root";
	/**数据库表前缀~为了区分同一个数据库中不同框架的数据表,建议设置此项。*/
	public static $db_tablePrefix = "";
/////////////////////常用配置	
	/**网站首页~网站首页所指向的控制器文件,不带后缀 如:user/show  错误的方式:user/show.php*/
	public static $index_target = "index";
	/**系统模式~开发模式和生产模式的区别,正式部署后一定要改为:false*/
	public static $is_developing = true;
	/**数据库日志~是否需要记录操作数据库的SQL日志默认:false,日志地址:floder/log/db*/
	public static $need_db_log = true;
	/**调试日志~是否需要记录系统的各项调试参数默认:false*/
	public static $need_debug_log = false;
	/**自动打印调试日志~开启调试日志记录后是否要在每次请求之后自动打印这些记录默认:false*/
	public static $auto_print_debug = false;
	/**系统日志级别~0：不输出日志，1：错误信息输出，2：警告和错误信息输出，3：提示、警告和错误信息输出4：提示、警告、错误和调试信息输出，日志地址:floder/log*/
	public static $log_level = 1;
	/**是否压缩~网页内容是否要压缩输出(一般web服务器会处理压缩)默认:false*/
	public static $need_gzip = false;
	/**缓存时间~对于开启基于时间过期模式的缓存页面,在此设定缓存页面的保留时间(单位:小时，如为0.1则是6分钟)（默认:null 为永久保留）,特殊缓存时间的设置可在控制器中通过page对象修改*/
	public static $cache_time = null;
	/**Jquery Url~Jquery的加载地址,默认为空。不设置则从框架加载,因特网环境推荐为:http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js*/
	public static $jquery_path = "";
	/**Session过期时间~设置访问客户端的Session过期时间(注意：这只是Session的标记删除时间，并不保证这段时间后一定会被回收，单位：分钟)，默认:60*/
	public static $session_time = 600;
	/**不开启Session的Controller路径~不需要开启Session的路径，节约服务器资源*/
	public static $nosession_path = "";
	
/////////////////////非常用配置,一般无需修改
	/**国际化~默认的网站国际化支持如zh-cn:中文简体,zh-tw:中文繁体,en:英语*/
	public static $default_i18n = "zh-cn";
	/**静态引用Url~为了提高访问速度(CDN等)网站静态文件(js/css/gif/jpg...)的Url路径。不存在静态文档服务器请设为空。示例:http://static.yyuc.net/resource/*/
	public static $remote_path = "";
	public static $local_remote = "";
	/**视图文件后缀~视图模版的后缀*/
	public static $view_suffix = ".html";
	/**是否开启无控制器时自动寻找对应视图~默认:true*/
	public static $auto_find_view = true;
	

	/**视图文件夹~view文件夹下的一组视图,如果有其他的视图文件夹,通过修改此项可以方便的修改网站的布局格式*/
	public static $view_folder = "default";
	/**网址根路径*/
	public static $http_path = "";
	/**左标签~视图代码左标签,视图模版中的便签开始。 一般为'{'*/
	public static $left_tag = "{";
	/**右标签~视图代码右标签,视图模版中的便签结束。 一般为'}'*/
	public static $right_tag = "}";
	/**静态后缀~控制器请求的页面后缀,如.html,.shtml等 一般为.html就好（注意:.htm默认为不走控制器的所以不要设置）*/
	public static $suffix = ".html";
	/**参数分隔符~替代问号传参的参数分割符号*/
	public static $parameter_separate = "-";
	/**分页参数分隔符~用来分割分页信息的分隔符*/
	public static $paging_separate = "_";

	
	
	/**DB连接的扩展信息~一般用于大数据量时的 主从数据库和多库数据库*/
	public static $db_boys = array(
	//'db1'=>array('host'=>'localhost','port'=>'3306','dbname'=>'sino','username'=>'root','password'=>'root'),
	//'db2'=>array('host'=>'localhost','port'=>'3306','dbname'=>'sino','username'=>'root','password'=>'root'),
	//'db3'=>array('host'=>'localhost','port'=>'3306','dbname'=>'sino','username'=>'root','password'=>'root'),
	//'db4'=>array('host'=>'localhost','port'=>'3306','dbname'=>'sino','username'=>'root','password'=>'root')
	);
	
	
	/**MongoDB连接的扩展信息~非关系型数据库专用*/
	public static $mongo = array(
			'default' => array(
					'host'     => '192.168.0.240',
					'port'     => 27017,
					'username' => '',
					'password' => '',
			),	
	);

/////////////////////特殊路由设置
	
	/**一般的绝对路由规则*/
	public static $routing = array(
	'reload'=>'reload'
	);
	/**一般的前置路由规则*/
	public static $routing_bef = array(
			//'reload'=>'reload'
	);
	/**正则表达式的路由规则~此规则最为常用*/
	public static $routing_reg = array(
		//厂商入口
		'/firm\/\d+\/\S*$/' => 'firm/list',
		'/product\/\d+\/\S*$/' => 'firm/plist',
		//标签入口
		'/tag\/\d+\/\S*$/' => 'tag/tagtype',
		//统一文档入口
		'/.*/' => 'home',
			
	);
	
	
	/**邮件发送配置规则*/
	public static $email = array(
	'reg'=>array('protocol'=>'smtp','smtp_host'=>'smtp.qq.com','smtp_user'=>'','smtp_pass'=>'','from'=>array('','注册信息')),
	'findpwd'=>array('protocol'=>'smtp','smtp_host'=>'smtp.qq.com','smtp_user'=>'','smtp_pass'=>'','from'=>array('','密码找回'))
	);
	
/////////////////////缓存及文件存储配置

	/**MemCached连接信息~*/
	public static $memcached = array(
			'cache1'=>array('127.0.0.1','11211'),
			'cache2'=>array('127.0.0.1','11211')
	);
	public static $memcached_un = null;
	public static $memcached_pwd = null;
	/**Redis连接信息~*/
	public static $redis = array('10.122.74.247','6379');
	/**全局变量适配器~(可选项:file,memcached,redis)这是多用户共用缓存的好方式，大型系统不推荐file模式，默认:file*/
	public static $cache_adapter = 'file';
	
	/**会话适配器~(可选项:session,cache)session:系统默认的Session机制，cache:运用系统缓存保存session*/
	public static $session_adapter = "session";	
	/**会话前缀~为了实现避免多框架下的Session混淆。*/	
	public static $session_prefix = "";
	
	
/////////////////////elfinder 结合阿里云存储的方式
	/**阿里云存储的访问授权~主机地址*/
	public static $alioss_host = "oss-cn-hangzhou.aliyuncs.com";
	/**阿里云存储的访问授权~主机地址*/
	public static $alioss_url = "http://weixinguanjia.oss-cn-hangzhou.aliyuncs.com/";
	/**阿里云存储的访问授权~accessid*/
	public static $alioss_accesskey = "QOBDGyzV6NYuS4HB";
	/**阿里云存储的访问授权~accesskey*/
	public static $alioss_accesssecret = "14u3GSs48U0SUuCO2RP23im1jVvvV3";
	/**阿里云存储的访问授权~bucket*/
	public static $alioss_bucket = "weixinguanjia";
}
?>