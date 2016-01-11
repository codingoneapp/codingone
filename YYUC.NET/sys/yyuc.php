<?php
class YYUC{
	private static $i18ns = array();
	
	/**
	 * 执行页面信息的i18n匹配<br/>
	 * 一般为系统内部调用
	 * @param string $path url请求的视图路径
	 */
	public static function i18n_page_init($path){
		$pi18npath = YYUC_FRAME_PATH.'i18n/'.$_SERVER['HTTP_ACCEPT_LANGUAGE'].'/'.$path.'.php';
		if(file_exists($pi18npath)){
			return include($pi18npath);
		}
		return array();
	}
	
	/**
	 * 页面的token标识<br/>
	 * 一般为视图form开始调用
	 * @param string $path url请求的视图路径
	 */
	public static function token(){
		$tkid = uniqid();
		if(!isset($_SESSION['YYUC_FORM_TOKEN'])){
			$_SESSION['YYUC_FORM_TOKEN'] = array();
		}
		$tk_arr = &$_SESSION['YYUC_FORM_TOKEN'];
		$tk_arr[$tkid] = microtime(true);
		Page::$tk_str = $tk_arr[$tkid].'@YYUC@'.$tkid;
		return '<input type="hidden" value="'.$tkid.'" name="YYUC_FORM_TOKEN"/>';
	}
	
	/**
	 * 返回引入本控制器文件夹下的文件绝对路径<br/>
	 * 为了便于区分和防止恶意访问到，一般用前缀"_"标注
	 * @param  string $colname php文件名(不含后缀)
	 */
	public static function another($colname ='_'){
		$papath = dirname(Index::$me->col_path);
		while(!file_exists($papath.'/'.$colname.'.php')){
			$papath = dirname($papath);
		}
		return $papath.'/'.$colname.'.php';
	}
	/**
	 * i18n加载
	 * @param $name 配置文件的标识名称
	 */
	public static function i18n($name = ''){
		$names = explode('.', $name);
		$key = $names[0];
		$app = '_'.$names[0];
		if($key == ''){
			$key = 'YYUC';
			$app = '';
		}
		if(!isset(self::$i18ns[$key])){
			self::$i18ns[$key] = include(YYUC_FRAME_PATH.'i18n/'.$_SERVER['HTTP_ACCEPT_LANGUAGE'].$app.'.php');
		}
		$arrs = &self::$i18ns[$key];
		$name_count = count($names);
		if($name_count > 1 ){
			for($i=1; $i<$name_count; $i++){
				$arrs = &$arrs[$names[$i]];
			}			
		}		
		return $arrs;		
	}
	
	/**
	 * 返回给定的值<br/>
	 * 如果传入回调函数则回调值会被返回
	 *
	 * @param  mixed  $value
	 * @return mixed
	 */
	public static function value($value){
		return (is_callable($value) and ! is_string($value)) ? call_user_func($value) : $value;
	}
	
	/**
	 * 返回给定的值URL形式<br/>
	 *
	 * @param  mixed  $value
	 * @return mixed
	 */
	public static function url($url){
		if(strpos($url, '://')===false){
			if(strpos($url, '.')===false&&(strrpos($url, '/') != strlen($url)-1)){
				$url = $url.Conf::$suffix;
			}
			if(strpos($url, '/')===0){
				$url = Conf::$http_path.substr($url, 1);
			}else {
				//控制器相对路径
				$lpath = dirname($_SERVER['REAL_REQUEST_URI']);
				$lpath = $lpath=='.' ? '' : $lpath.'/';
				$url = Conf::$http_path.$lpath.$url;
			}
		}
		return $url;
	}
	
	/**
	 * 取得URL的缓存<br/>
	 * 对内部URL实行URL内部的缓存机制<br/>
	 * 对外部URL进行配置文件里的缓存时间设置
	 * @param  string  $url
	 * @return string 
	 */
	public static function cache($url){
		if(strpos($url, '://')===false){
			if(strpos($url, '/')!==0){
				//控制器相对路径
				$lpath = dirname($_SERVER['REAL_REQUEST_URI']);
				$lpath = $lpath=='.' ? '' : $lpath.'/';
				$url = '/'.$lpath.$url;
			}
			//缓存路径
			$cache_path = $_SERVER['HTTP_ACCEPT_LANGUAGE'].$url;
			$res = Cache::get($cache_path.'_key');
			//这是没有前置校验的
			$access_m = true;
			if(Conf::$is_developing || empty($res)){
				$access_m = false;
			}else{
				$lines = explode('@YYUC@', $res);
				if(count($lines) > 1){
					//数据库缓存
					$dbs = explode(',', $lines[1]);
					foreach ($dbs as $db){
						if(Cache::has('YYUC_TABLE_TIME'.Conf::$db_tablePrefix.$db) && intval(Cache::get('YYUC_TABLE_TIME'.Conf::$db_tablePrefix.$db)) >= intval($lines[0])){
							//如果某一库表的更新时间大于等于缓存时间
							$access_m = false;
						}
					}				
				}
			}
			if($access_m){
				return Cache::get($this->cache_path);
			}else{
				return file_get_contents(self::url($url));
			}
		}else{
			$k = base64_encode($url);
			if(!Cache::has('YYUC_PUBCACHE/'.$k)){
				Cache::set('YYUC_PUBCACHE/'.$k, @file_get_contents($url), Conf::$cache_time);
			}
			return Cache::get('YYUC_PUBCACHE/'.$k);
		}
		return $url;
	}
	
	/**
	 * 获得IP端位置
	 * @return array('国家','省','市','电信') | false
	 */
	public static function local_ip($ip){
		$res = HttpClient::quickGet('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip,1,false);
		if($res){
			$res = json_decode($res);
			if($res->code=='0'){
				if(trim($res->data->city)!=''){
					$plen = mb_strlen($res->data->region);
					$clen = mb_strlen($res->data->city);
					if(mb_stripos($res->data->region,'省')==$plen-1){
						$res->data->region = mb_substr($res->data->region, 0,$plen-1);
					}
					if(mb_stripos($res->data->city,'市')==$clen-1){
						$res->data->city = mb_substr($res->data->city, 0,$clen-1);
					}
					return array($res->data->country,$res->data->region,$res->data->city,$res->data->isp);
				}
			}
		}
		return false;
	}
	
	
	/**
	 * 获得手机号码位置
	 * @return array('国家','省','市','电信') | false
	 */
	public static function local_cell($tel){
		$res = HttpClient::quickGet('http://life.tenpay.com/cgi-bin/mobile/MobileQueryAttribution.cgi?chgmobile='.$tel,1,false);
		if($res){
			$res = simplexml_load_string($res);
			if($res->retmsg=='OK'){
				if(trim($res->province)!=''){
					return array('中国',trim($res->province),trim($res->city),trim($res->supplier));
				}
			}
		}
		return false;
	}
	

	/**
	 * elfinder 初始化设置
	 * 
	 * @param string $usrpath
	 * @param string $usrurl
	 * @param string $pathname
	 * @param string $maxsize
	 * @param arrat $allow
	 * @param string $syspath
	 * @param string $sysurl
	 * @param string $sysname
	 */
	public static function set_elfinder($usrpath,$usrurl,$pathname='我的图库',$maxsize='500k',$allow=array('image'),$syspath=null,$sysurl=null,$sysname='系统图库'){
		$_SESSION['yyuc_upload_path'] = $usrpath;
		File::creat_dir($usrpath);
		$_SESSION['yyuc_upload_url'] = $usrurl;
		$_SESSION['yyuc_upload_name'] = $pathname;
		$_SESSION['yyuc_upload_maxsize'] = $maxsize;
		$_SESSION['yyuc_upload_allow'] = $allow;
		$_SESSION['yyuc_media_path'] = $syspath;
		$_SESSION['yyuc_media_url'] = $sysurl;
		$_SESSION['yyuc_media_name'] = $sysname;
		$_SESSION['yyuc_elfinder_driver'] = 'LocalFileSystem';
		return false;
	}
	
	/**
	 * elfinder 初始化设置
	 *
	 * @param string $usrpath
	 * @param string $usrurl
	 * @param string $pathname
	 * @param string $maxsize
	 * @param arrat $allow
	 * @param string $syspath
	 * @param string $sysurl
	 * @param string $sysname
	 */
	public static function oss_elfinder($usrpath,$pathname='我的图库',$maxsize='500k',$allow=array('image'),$syspath=null,$sysname='系统图库'){
		$_SESSION['yyuc_upload_path'] = $usrpath;
		$_SESSION['yyuc_upload_url'] = Conf::$alioss_url.$usrpath;
		$_SESSION['yyuc_upload_name'] = $pathname;
		$_SESSION['yyuc_upload_maxsize'] = $maxsize;
		$_SESSION['yyuc_upload_allow'] = $allow;
		$_SESSION['yyuc_media_path'] = $syspath;
		$_SESSION['yyuc_media_url'] = Conf::$alioss_url.$syspath;
		$_SESSION['yyuc_media_name'] = $sysname;
		$_SESSION['yyuc_elfinder_driver'] = 'AliYun';
		return false;
	}
	
	/**
	 * 根据url路径获得物理路径
	 * @param string $url
	 */
	public static function elfinder_path($url){
		return urldecode(str_replace($_SESSION['yyuc_upload_url'], $_SESSION['yyuc_upload_path'], $url));
	}
	
	public static function print_stack_trace($prienttag = true){
		$array = debug_backtrace();
		$filep = '';
		foreach($array as $row){
			$args = $prienttag ? ('('.var_export($row['args'],true).')') :'';
			$filep .= ($row['file'].':'.$row['line'].'行,调用方法:'.$row['function'].$args."\n");
		}
		return $filep;
	}
}