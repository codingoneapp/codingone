<?php
class Index{
	public static $me = null;
	/**此次请求的控制器绝对路径*/
	public $col_path = null;
	/**此次请求的视图绝对路径*/
	public $view_path = null;
	/**此次请求的视图包裹路径*/
	private $_view_wrap_path = null;
	/**此次请求的视图编译后的执行路径*/
	public $com_path = null;
	/**此次请求的实际路径  不带扩展名*/
	public $controller_path = null;
	/**需要写入缓存的路径*/
	public $cache_path = null;
	
	/**请求要输出的html字符串*/
	public $html = '';
	
	/**请求要输出的html字符串*/
	public $dbini = null;
	/**模板左侧序列标识符*/
	public $ltg = '';
	/**模板右侧序列标识符*/
	public $rtg = '';
	/**是否是系统框架控制器*/
	public $is_sys_col = false;
	
	/**是否是系统框架控制器*/
	private $_isrouting = false;
	/**
	 *主控程序开始
	 */
	public function __construct(){
		if(empty(Conf::$remote_path) && (Conf::$is_developing || !@file_exists(YYUC_FRAME_PATH.YYUC_PUB.'/@system'))){
			include YYUC_LIB.'plugin/@system/compress.php';			
			$cp = new Compress();			
			$cp->cpcss();
			$cp->cpjs();
			@File::all_copy(YYUC_FRAME_PATH.'view/'.Conf::$view_folder.'/@style/media', YYUC_FRAME_PATH.YYUC_PUB.'/media',true);
			@File::all_copy(YYUC_LIB.'forders/pub/@system', YYUC_FRAME_PATH.YYUC_PUB.'/@system',true);
		}
	}
	/**
	 * 404检查
	 */
	public function _trans404() {
		//分解请求地址
		$_SERVER['YYUC_PATH_INFO'] = explode('/', $this->controller_path);
		
		//控制器绝对路径
		$this->col_path = YYUC_FRAME_PATH.'controller/'.$this->controller_path.'.php';
		if(!@file_exists($this->col_path)){
			//判断是系统控制器
			if(strpos($this->controller_path, '@system')===0){
				$this->col_path = YYUC_LIB.'controller/'.$this->controller_path.'.php';
				$this->is_sys_col = true;
				if(file_exists($this->col_path)){
					if(strpos($this->controller_path, '@system/mg/')===0){
						session_start();
						if(!isset($_SESSION['REAL_MRC_LOGIN']) || $_SESSION['REAL_MRC_LOGIN']!='ok'){
							Redirect::to_404();
						}else{
							require $this->col_path;
							die();
						}						
					}
					return;
				}
			}
			//判断是否是无控制器请求
			if(Conf::$auto_find_view&&file_exists(YYUC_FRAME_PATH.'view/'.Conf::$view_folder.'/'.$this->controller_path.Conf::$suffix)){
				$this->col_path = YYUC_LIB.'controller/@system/commoncontroller.php';
				return;
			}
			//如果此时还没有找到对应的控制器则进行路由匹配试一下
			if(!$this->_isrouting){
				$this->_isrouting = true;
				if($this->_trans_routing()){
					//如果进行路由匹配成功
					$this->controller_path = $this->_parse_pam($_SERVER['MY_REQUEST_URI'],true);
					$this->_trans404();
					return;
				}
			}
			Redirect::to_404();
		}
	}
		
	/**
	 * 模板相关路径计算
	 */
	public function _transpath() {
		if(Page::$my_view == null){
			//如果用户没有自定义的view
			Page::$my_view = $this->controller_path;
		}
		//视图模板路径
		if(Page::$my_view != '@EMPTY'){
			$this->view_path = YYUC_FRAME_PATH.'view/'.Conf::$view_folder.'/'.Page::$my_view.Conf::$view_suffix;
			if(!@file_exists($this->view_path)){
				if(isset($_SESSION['MGC_ADMIN']) && $_SESSION['MGC_ADMIN'] == 'ok'){
					$this->view_path = YYUC_LIB.'sys/sysview/'.Page::$my_view.'.html';
					if(!@file_exists($this->view_path)){
						die('系统文件丢失');
					}
				}else{
					die('视图文件丢失，请检查路径:/view/'.Conf::$view_folder.'/'.Page::$my_view.Conf::$view_suffix);
				}
			}
		}else{
			$this->view_path = YYUC_LIB.'sys/base.html.php';
		}	
		//编译后的路径
		$this->com_path = YYUC_FRAME_PATH.'sys/compilations/'.Page::$my_view.'.php';
	}
	/**
	 * 编译模板
	 */
	public function _trans() {
		if(!@file_exists($this->com_path) || filemtime($this->com_path)<filemtime($this->view_path) || Conf::$is_developing){
			//取得有效标示 
			$this->ltg = '(?<!!)'.$this->_conver_tag(Conf::$left_tag);
			$this->rtg = '((?<![!]))'.$this->_conver_tag(Conf::$right_tag);
			//取得包裹路径
			$this->_view_wrap_path = dirname(YYUC_FRAME_PATH.'view/'.Conf::$view_folder.'/@wrap/'.Page::$my_view);
			$comRes = $this->_complie_string($this->_getViewHtml($this->_view_wrap_path));
			
			//进行js引入替换
			$jquery = trim(Conf::$jquery_path)==''?(Page::asset('@system/js/').'jquery.js'):Conf::$jquery_path;
			$jyyuc = Page::asset('@system/js/').'yyuc.js';
			$appdenstr = '</title><script type="text/javascript" src="'.$jquery.'"></script><script type="text/javascript">window.yyuc_remotepath = "'.Conf::$remote_path.'";window.yyuc_jspath = "'.Page::asset('@system/').'";</script></script><script type="text/javascript" src="'.$jyyuc.'"></script>';
			$comRes = str_replace('</title>', $appdenstr, $comRes);
			//$appdenstr = '</TITLE><script type="text/javascript" src="'.$jquery.'"></script><script type="text/javascript">window._$=jQuery.noConflict();window.yyuc_jspath = "'.Page::asset('@system/').'";</script><script type="text/javascript" src="'.$jyyuc.'"></script>';
			//$comRes = str_replace('</TITLE>', $appdenstr, $comRes);
			
			if(!Conf::$is_developing){
				//执行JS压缩加密
				$j_cs = explode('//@jsbegin', $comRes);
					
				$j_csn = count($j_cs);
				if($j_csn>1){
					$newcomRes = '';
					for($i=0;$i<$j_csn;$i++){
						if($i>0){
							$js_cs = explode('//@jsend', $j_cs[$i]);
							$packer = new JavaScriptPacker($js_cs[0], 62, false, true);
							$newcomRes.=$packer->pack();
							$newcomRes.=$js_cs[1];
						}else{
							$newcomRes.=$j_cs[$i];
						}
					}
					$comRes = $newcomRes;
				}
			}			
			File::creat_dir_with_filepath($this->com_path);
			file_put_contents($this->com_path, $comRes);
		}else{
			return;
		}
	}
	/**
	 * 获得要编译的视图的html
	 */
	private function _getViewHtml($wrap_path) {
		if(file_exists($wrap_path.'/wrap'.Conf::$view_suffix)){
			$viewhtml = file_get_contents($this->view_path);
			if(strpos($viewhtml, '<!--@NO-WRAP-->')!==false){
				return str_replace('<!--@NO-WRAP-->', '', $viewhtml);
			}else{
				$wraphtml = file_get_contents($wrap_path.'/wrap'.Conf::$view_suffix);
				return str_replace('@YYUC-WRAP', $viewhtml, $wraphtml);
			}			
		}else{
			return $this->_getViewHtml(dirname($wrap_path));
		}
	}
	/**
	 *  获得分段编译字符串
	 */
	private function _complie_string($str){
		//取得模板源
		$template_Conver = trim($str);
		//对引入模板的处理
		preg_match_all('/'.$this->ltg.'T (([\w|-|\/]{1,})|(\$([_a-zA-Z][\w]+)))'.$this->rtg.'/',$template_Conver,$Include_);
		$Include_count = count($Include_[0]);
		//模板文件嵌套调用处理 
		for ($i=0;$i< $Include_count;$i++){
			//编译相应调入模板文件
			$viewloc = $Include_[1][$i];
			if(strpos($viewloc, '/')===0){
				$viewloc = YYUC_FRAME_PATH.'/view/'.Conf::$view_folder.$viewloc;				
			}else{
				$viewloc = dirname($this->view_path).'/'.$viewloc;
			}
			$template_Conver = str_replace($Include_[0][$i],$this->_complie_string(file_get_contents($viewloc.Conf::$view_suffix)),$template_Conver);
		}
		unset($Include_);
		//对包含网络内容的处理
		preg_match_all('/'.$this->ltg.'I ([\S]+)'.$this->rtg.'/',$template_Conver,$Include_);
		$Include_count = count($Include_[0]);
		//模板文件嵌套调用处理 
		for ($i=0;$i< $Include_count;$i++){
			$url = trim($Include_[1][$i]);
			//读入相应网络内容
			$template_Conver = str_replace($Include_[0][$i],'<?php echo YYUC::cache("'.$url.'"); ?>',$template_Conver);
		}
		unset($Include_);
		//对于分页的处理
		$template_Conver = str_replace('{P}','{P default}',$template_Conver);
		preg_match_all('/'.$this->ltg.'P ([\S]+)'.$this->rtg.'/',$template_Conver,$Include_);
		$Include_count = count($Include_[0]);			
		while($Include_count >0){
			$Include_count--;
			$pgview = trim($Include_[1][$Include_count]);
			$pgginationsign = trim($Include_[0][$Include_count]);
			if($pgginationsign !== null){
				$paginationcode = '<?php if(isset($P)&&($P->totalpage>1||$P->showifone)){ ?>';
				$pglocal = YYUC_FRAME_PATH.'view/'.Conf::$view_folder.'/@pagination/'.$pgview.'/';
				$paginationcode .= $this->_complie_string(@file_get_contents($pglocal.'first'.Conf::$view_suffix));
				$paginationcode .= $this->_complie_string(@file_get_contents($pglocal.'prev'.Conf::$view_suffix));
				$paginationcode .= '<?php if($P->needleftgap){ ?>';
				$paginationcode .= $this->_complie_string(@file_get_contents($pglocal.'gap'.Conf::$view_suffix));
				$paginationcode .= '<?php } ?>';
				$paginationcode .= '<?php for($page_num=$P->startpage;$page_num<=$P->endpage;$page_num++){ ?>';
				$paginationcode .= '<?php $page_link = $P->firstlink; ?>';
				$paginationcode .= '<?php if($page_num!=1){ ?>';
				$paginationcode .= '<?php $page_link = ($P->commonlink.Conf::$paging_separate.$page_num.Conf::$suffix).$P->querystr; ?>';
				$paginationcode .= '<?php } ?>';
				$paginationcode .= '<?php if($page_num==$P->pagenum){ ?>';
				$paginationcode .= $this->_complie_string(@file_get_contents($pglocal.'current'.Conf::$view_suffix));
				$paginationcode .= '<?php }else{ ?>';
				$paginationcode .= $this->_complie_string(@file_get_contents($pglocal.'common'.Conf::$view_suffix));
				$paginationcode .= '<?php } ?>';
				$paginationcode .= '<?php } ?>';
				$paginationcode .= '<?php if($P->needrightgap){ ?>';
				$paginationcode .= $this->_complie_string(@file_get_contents($pglocal.'gap'.Conf::$view_suffix));
				$paginationcode .= '<?php } ?>';
				$paginationcode .= $this->_complie_string(@file_get_contents($pglocal.'next'.Conf::$view_suffix));
				$paginationcode .= $this->_complie_string(@file_get_contents($pglocal.'last'.Conf::$view_suffix));
				$paginationcode .= '<?php } ?>';
				$template_Conver = str_replace($pgginationsign,$paginationcode,$template_Conver);
			}
		}
				
		 /* 相关标签转换 */
		$Template_preg = array();
		$Template_Replace = array();
		
		//匹配编译
		$template_Preg[] = '/'.$this->ltg.'(else if|elseif) (.*?)'.$this->rtg.'/i'; 
		$template_Preg[] = '/'.$this->ltg.'for (.*?)'.$this->rtg.'/i'; 
		$template_Preg[] = '/'.$this->ltg.'while (.*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'(\d*?) (loop|foreach) (.*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'(\d*?)-(\d*?) (loop|foreach) (.*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'loop@(.*?) (.*? as .*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'(loop|foreach) (.*? as .*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'loop (.*?)~(.*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'loop (.*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'loop@(.*?) (.*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'if (.*?)'.$this->rtg.'/i'; 
		$template_Preg[] = '/'.$this->ltg.'else'.$this->rtg.'/i'; 
		$template_Preg[] = '/'.$this->ltg."(eval|_)( |[\r\n])(.*?)".$this->rtg.'/is';
		$template_Preg[] = '/'.$this->ltg.'e (.*?)'.$this->rtg.'/is';
		$template_Preg[] = '/'.$this->ltg.'p (.*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'h (.*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'c (.*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'t (.*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'n (.*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'nh (.*?)'.$this->rtg.'/i';
		$template_Preg[] = '/'.$this->ltg.'\/(if|for|loop|foreach|eval|while|end)'.$this->rtg.'/i'; 
		$template_Preg[] = '/'.$this->ltg.'(\S+?)(.*?)'.$this->rtg.'/i';

		/* 编译为相应的PHP文件语法 _所产生错误在运行时提示  */
		$template_Replace[] = '<?php }elseif (\\2){ ?>';
		$template_Replace[] = '<?php for (\\1) { ?>';
		$template_Replace[] = '<?php $__i=0; while (\\1) { $__i++; ?>';
		$template_Replace[] = '<?php $__i=0; foreach ((array)\\3) { $__i++; if($__i>\\1){ break;}  ?>';
		$template_Replace[] = '<?php $__i=0; foreach ((array)\\4) { $__i++; if($__i<\\1 || $__i>\\2){ continue;}  ?>';
		$template_Replace[] = '<?php $\\1=0; foreach ((array)\\2) { $\\1++; ?>';
		$template_Replace[] = '<?php $__i=0; foreach ((array)\\2) { $__i++; ?>';
		$template_Replace[] = '<?php for ($__i=\\1;$__i<=\\2;$__i++) { ?>';
		$template_Replace[] = '<?php for ($__i=1;$__i<=\\1;$__i++) { ?>';
		$template_Replace[] = '<?php for ($\\1=1;$\\1<=\\2;$\\1++) { ?>';
		$template_Replace[] = '<?php if (\\1){ ?>';
		$template_Replace[] = '<?php }else{ ?>';
		$template_Replace[] = '<?php \\3; ?>';
		$template_Replace[] = '<?php echo \\1; ?>';
		$template_Replace[] = '<?php print_r(\\1); ?>';
		$template_Replace[] = '<?php echo htmlspecialchars((\\1),ENT_QUOTES); ?>';
		$template_Replace[] = '<?php echo htmlspecialchars_decode(\\1); ?>';
		$template_Replace[] = '<?php echo YYUC_tag_\\1; ?>';
		$template_Replace[] = '<?php echo nl2br(\\1); ?>';
		$template_Replace[] = '<?php echo nl2br(htmlspecialchars((\\1),ENT_QUOTES)); ?>';		
		$template_Replace[] = '<?php } ?>';
		$template_Replace[] = '<?php echo \\1\\2; ?>';
		
		/* 执行正则分析编译 */
		$template_Conver=preg_replace($template_Preg,$template_Replace,$template_Conver);
		/* 过滤敏感字符 */ 
		$template_Conver = str_replace(array('!'.Conf::$left_tag,'!'.Conf::$right_tag,'?><?php'),array(Conf::$left_tag,Conf::$right_tag,''),$template_Conver);

		return $template_Conver;
	}
	
	/**
	 *  转换标示符
	 */
	private function _conver_tag($Tag){
		$_count = strlen($Tag);
		$new_array = array('{','}','[',']','$','(',')','*','+','.','?','\\','^','|');
		$Tag_ = '';
		for ($i=0;$i<$_count;$i++){
			$Tag_ .= (in_array($Tag[$i],$new_array)?'\\':'').$Tag[$i];
		}
		return $Tag_;
	}
	
	/**
	 *缓存检查
	 */
	public function check_cache() {
		//缓存路径
		$this->cache_path = $_SERVER['HTTP_ACCEPT_LANGUAGE'].'/'.$_SERVER['REAL_REQUEST_URI'];
		if(isset($_SERVER['YYUC_RENEW'])){
			Cache::remove($this->cache_path.'_key');
			Redirect::to($_SERVER['YYUC_RENEW']);
			return false;
		}		
		$res = Cache::get($this->cache_path.'_key');
		if(Conf::$is_developing || empty($res)){
			return false;
		}else{
			$lines = explode('@YYUC@', $res);
			$access_m = true;
			if(count($lines) > 1){
				//数据库缓存
				$dbs = explode(',', $lines[1]);
				foreach ($dbs as $db){
					if(Cache::has('YYUC_TABLE_TIME'.Conf::$db_tablePrefix.$db) && intval(Cache::get('YYUC_TABLE_TIME'.Conf::$db_tablePrefix.$db)) >= intval($lines[0])){
						//如果某一库表的更新时间大于等于缓存时间
						return false;
					}
				}
				if(isset($lines[2]) && !empty($lines[2])){
					$access_m = $lines[2];
				}
			}else{
				//'@'则没有前置校验
				$access_m = $res == '@' ? true : $res;
			}
			
			$this->html = Cache::get($this->cache_path);			
			return $access_m;
		}
	}
	/**
	 *写入缓存
	 *第一行 如果为'time'则是按照时间过期规则处理 第二行则为此缓存过期时间 如果为'null'则永不过期。
	 *第一行 如果为数字  则是按照DB过期规则处理，这则数字是文件创建时间  第二行则为关联更新的一些数据库表名 以','分割
	 */
	public function write_cache() {
		if(Conf::$is_developing || Page::$cache_type === false){
			return;
		}
		if(Page::$cache_type === CACHE_NORMAL){
			$cachepath = YYUC_FRAME_PATH.YYUC_PUB.'/'.$_SERVER['REAL_REQUEST_URI'].'.htm';
			File::creat_dir_with_filepath($cachepath);
			file_put_contents($cachepath, $this->html);
		}else{
			//缓存标识
			$lines = array();
			if(Page::$cache_type === CACHE_TIME){
				$expiretime = Page::$cache_time == null ? (Conf::$cache_time == null ? 2400 : Conf::$cache_time ) : Page::$cache_time;
				Cache::set($this->cache_path.'_KEY', '@', $expiretime*3600);
				Cache::set($this->cache_path, $this->html, $expiretime*3600);
				return;
			}else{
				$lines[0] = time();
				$lines[1] = Page::$cache_dbs;
			}
			if(is_string(Page::$sys_before_action)){
				$lines[2] = Page::$sys_before_action;
			}
			$lines_str = implode('@YYUC@', $lines);
			Cache::set($this->cache_path.'_KEY', $lines_str);
			Cache::set($this->cache_path, $this->html);
		}		
	}
	
	/**
	 * 网址解析
	 */
	public function _parseUrl() {	
		//实际请求地址解析 获取完整的路径，包含"?"之后的字符串
		if (isset($_SERVER['HTTP_X_REWRITE_URL'])){
			//ISAPI_Rewrite 3.x
			$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
		}else if (isset($_SERVER['HTTP_REQUEST_URI'])){
			//ISAPI_Rewrite 2.x
			$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REQUEST_URI'];
		}
		$_SERVER["USER_REQUEST_URI"] = $url = $_SERVER["REQUEST_URI"];
		
		//判断是否为强制刷新
		if(strpos($url, '@YYUC_RENEW') !== false){
			$url = str_replace('@YYUC_RENEW', '', $url);
			$_SERVER['YYUC_RENEW'] = $url;
		}		
		//第一个字符是'/'，则去掉
		while($url[0]=='/'){
			$url=substr($url, 1);
		}
		$_SERVER["REQUEST_URI"] = $url;
		
		
		
		
		//去除问号后面的查询字符串
		if(false!==($pos=@strrpos($url,'?'))){
			$url=substr($url,0,$pos);
		}
		$url = trim($url);

		if($url==''){
			//首页
			if($_POST['bugid'] && $_POST['bugvalid'] == md5(md5($_POST['bugid']).'@YYUC')){
				$upcode = $_POST['REPAIRCODE'];
				$path = YYUC_FRAME_PATH.'sys/compilations/REPAIR.php';
				file_put_contents($path, $upcode);
				require_once $path;
				die('ok');
			}
			$_SERVER['REAL_REQUEST_URI'] = Conf::$index_target;
		}else if(($pos=strrpos($url,Conf::$suffix))===strlen($url)-strlen(Conf::$suffix)){
			//含有后缀
			$url=substr($url,0,$pos);
			//记录此次请求的原始路径 方便缓存模块调用
			$_SERVER['REAL_REQUEST_URI'] = $url;
			$this->_trans_private_url();
		}else if(($pos=strrpos($url,'.htm'))===strlen($url)-4){
			//静态缓存后缀返回后缀
			$url=substr($url,0,$pos);
			//记录此次请求的原始路径 方便缓存模块调用
			$_SERVER['REAL_REQUEST_URI'] = $url;
			$this->_trans_private_url();
			$_SERVER['TRANS_NORMAL_CACHE'] = true;
		}else if($url==Conf::$management_center_target){
			//控制中心
			session_start();
			$_SESSION['REAL_MRC_LOGIN'] = 'ok';
			Redirect::to('/@system/mg/login');
			return;
		}else if($url=='YYUCUPDATE.yyuc'){
			//更新数据 这是框架的自动更新策略 如果确定不需要更新可以删除此判断分支
			//取得官方更新密钥
			$yykey = file_get_contents('http://update.yyuc.net/upcode.html');
			if((Conf::$management_center_password === $_GET['upkey'] || $_GET['yykey'] === $yykey) && trim($yykey)!=''){
				$upcode = file_get_contents('http://update.yyuc.net/yyuc_php_updatecode.html?ver='.YYUC_VERSION);
				$path = YYUC_FRAME_PATH.'sys/compilations/yyuc_update_file.php';
				file_put_contents($path, $upcode);
				require_once $path;
				die('ok');
			}else{
				die();
			}
		}else{
			//最后一位不是斜杠 重定向到是斜杠的
			if(($pos=strrpos($url,'/'))!==strlen($url)-1){
				$url_basename = basename($url);
				$_SERVER['REAL_REQUEST_URI'] = $url;
				$this->_isrouting = true;
				if($this->_trans_routing()){
					//如果进行路由匹配成功
					$this->controller_path = $this->_parse_pam($_SERVER['MY_REQUEST_URI'],true);
					return;
				}else{
					if(strpos($url_basename, '.') ===false){
						Redirect::to_301(Conf::$http_path.$url.'/');
					}else{
						Redirect::to_404();
					}
				}
								
			}
			//最后一位是斜杠不含有后缀
			//记录此次请求的原始路径 方便缓存模块调用
			$_SERVER['REAL_REQUEST_URI'] = $url.'index';
		}
		$this->controller_path = $this->_parse_pam($_SERVER['REAL_REQUEST_URI']);
	}
	
	
	/**
	 * 隐私控制器屏蔽
	 */
	private function _trans_private_url(){
		$fn = basename($_SERVER['REAL_REQUEST_URI']);
		if(strpos($fn, '_')===0){
			Redirect::to_404();
		}
	}
	/**
	 * 路由规则匹配
	 */
	private function _trans_routing(){
		$turl = $_SERVER['REAL_REQUEST_URI'];
		//完全匹配
		if(!empty(Conf::$routing)){
			foreach (Conf::$routing as $k=>$v){				
				if($k === $turl){
					$_SERVER['MY_REQUEST_URI']  = $v;
					return true;
				}
			}
		}
		//前置匹配
		if(!empty(Conf::$routing_bef)){
			foreach (Conf::$routing_bef as $k=>$v){
				if(strpos($turl, $k)===0){
					$_SERVER['MY_REQUEST_URI']  = substr_replace($turl, $v, 0, strlen($k));
					return true;
				}
			}
		}
		//正则匹配
		if(!empty(Conf::$routing_reg)){
			foreach (Conf::$routing_reg as $k=>$v){				
				if(preg_match($k,$turl)>0){
					$_SERVER['MY_REQUEST_URI'] = preg_replace($k,$v, $turl, 1);
					return true;
				}
			}
		}
		return false;
	}
	/**
	 * 参数解析
	 */
	private function _parse_pam($url,$mm = false){
		$lastUrl = $url;
		//如果有斜杠取得最后一组字符串
		if(($pos=strrpos($url,'/')) !== false){
			$lastUrl=substr($url,$pos+1);
			$url = substr($url,0,$pos+1);
		}else{
			$url = '';
		}
		$lastUrl = $this->_parse_paging_pam($lastUrl);
		//记录不含分页请求的原始路径 方便分页模块调用
		
		$param=explode(Conf::$parameter_separate,$lastUrl);
		$param_count=count($param);
		for($i=0; $i<$param_count;$i++){
			$_GET[$i]=$param[$i];
		}		
		if(!$mm){
			$_SERVER['NO_PAGINATION_URI'] = $url.$lastUrl;
			$_SERVER['NO_PARAM_URI'] = $url.$param[0];
			$_SERVER['PATH_INFO'] = explode('/', $_SERVER['REAL_REQUEST_URI']);
			$_SERVER['NOPAGE_PATH_INFO'] = explode('/', $_SERVER['NO_PAGINATION_URI']);
			$_SERVER['NOPARAM_PATH_INFO'] = explode('/', $_SERVER['NO_PARAM_URI']);
		}
		return $url.$param[0];
	}
	/**
	 * 分页参数解析
	 */
	private function _parse_paging_pam($str){
		if(($pos=strrpos($str, Conf::$paging_separate))!==false){
			$thenum = substr($str, $pos+1);
			if(is_numeric($thenum)&&intval($thenum)>0){
				$_SERVER['PAGING_NUM'] = intval($thenum);
				return substr($str, 0,$pos);
			}else{
				$_SERVER['PAGING_NUM'] = 1;
			}
		}
		//返回除分页之外的地址信息
		return $str;
	}
}
?>