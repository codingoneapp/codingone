<?php
/**
 * 歪歪框架模型类文件
 * @author mqq
 * 
 */
class SampleModel{
	/**是否原生模型*/
	public $YYUCSYS_isorgin = true;
	/**表名*/
	public $YYUCSYS_tablename;
	/**数据符合规范的标识*/
	public $YYUCSYS_val_fail = false;
	/**主键*/
	public $id;
	/**存贮字段的待选值数组 */
	public static $YYUCSYS_FIELD_DATA = array();
	/**存贮字段的描述数组 */
	public static $YYUCSYS_FIELD_LABLE = array();
	/**存贮字段的类型数组 */
	public static $YYUCSYS_FIELD_TYPE = array();
	/**存贮字段的默认值数组 */
	public static $YYUCSYS_FIELD_DEFAULT = array();
	/**存贮字段是否允许非空的数组 */
	public static $YYUCSYS_FIELD_CANNULL = array();
	/**存贮字段Form验证字串的数组 */
	public static $YYUCSYS_FIELD_FORMVAL = array();
	/**存贮字段的验证错误信息数组 */
	public $YYUCSYS_field_error = array();
	/**Form提交的加密后的区别标志*/
	protected  $YYUCSYS_post_id = '';
	/**Form提交的区别标志*/
	protected $YYUCSYS_postid = '';
	/**是否是初始的验证信息设置*/
	public static $YYUCSYS_first_valset = array();
	
	/**
	 * 根据post请求内容填充这个Model<br/>
	 * 这是表单字段自动提交的最常用方法
	 * 
	 * @return Model 模型本身
	 */
	public function load_from_post(){
		// 先进行解码
		$newpost = array();
		if(Page::$tk_str != null){
			$prevstr = $this->YYUCSYS_tablename.'T'.$this->YYUCSYS_postid;
			foreach ($_POST as $k=>$v){
				$newpost[String::decryption($k,Page::$tk_str )] = $v;
			}
		}else{
			$prevstr = $this->YYUCSYS_tablename.'T'.$this->YYUCSYS_post_id;
			$newpost = $_POST;
		}
		Page::$tk_str = null;
		
		$begin = strlen($prevstr);
		if(!empty($newpost[$prevstr.'id'])&&isset($this->YYUCSYS_db)){
			$this->find($newpost[$prevstr.'id']);
		}
		
		if(get_magic_quotes_gpc()){
			foreach ($newpost as $k=>$v){				
				if(strpos($k, $prevstr)===0){				
					$field = substr($k, $begin);
					if(is_array($v)){
						$v = ','.implode(',', $v).',';
					}
					$this->$field = stripslashes($v);
				}
			}
		}else{
			foreach ($newpost as $k=>$v){				
				if(strpos($k, $prevstr)===0){				
					$field = substr($k, $begin);
					if(is_array($v)){
						$v = ','.implode(',', $v).',';
					}
					$this->$field = $v;
				}
			}
		}
		//填充实体
		if(!$this->YYUCSYS_isorgin){
			$this->fill_entity_field();
		}
		return $this;
	}
	
	/**
	 * 处理以地址形式提交的上传文件
	 * 
	 * @param string $key 上传控件的的name值提交上来的属性
	 * @param string $folderpath pub文件夹下的相对文件夹路径
	 * @param string $isFile 上一参数是否是文件，如果是则直接覆盖文件
	 * 
	 * @return Model this
	 */
	public function trans_file($name, $folderpath=null, $isFile = false){
		$this->$name = trim(Upload::tosave_upload_file($this->field_form_name($name),$folderpath, $isFile));
		return $this;
	}
	
	/**
	 * 试探行的填充这个model
	 * 如果能填充则采用post填充并返回：true否则返回：false
	 * 
	 * @return boolean
	 */
	public function try_post(){
		if(Request::post()){
			$this->load_from_post();
			return true;
		}
		return false;
	}
	
	/**
	 * 试探行的填充这个model
	 * 如果能填充则采用post填充并返回：true否则返回：false
	 *
	 * @return boolean
	 */
	public function try_get(){
		if(Request::get()){
			$this->load_from_get();
			return true;
		}
		return false;
	}
	/**
	 * 根据get请求内容填充这个Model<br/>
	 * 这个方法通常用在信息检索页面的批量属性提交<br/>
	 * 切不可用此方法得来的数据进行CUD操作！
	 * 
	 * @return Model 模型本身
	 */
	public function load_from_get(){
		$prevstr = $this->YYUCSYS_tablename.'T'.$this->YYUCSYS_post_id;
		$begin = strlen($prevstr);
		if(!empty($_GET[$prevstr.'id'])&&isset($this->YYUCSYS_db)){
			$this->find($_GET[$prevstr.'id']);
		}
		foreach ($_GET as $k=>$v){
			if(strpos($k, $prevstr)===0){
				$field = substr($k, $begin);
				$this->$field = $v;
			}
		}
		//填充实体
		if(!$this->YYUCSYS_isorgin){
			$this->fill_entity_field();
		}
		return $this;
	}
	
	/**
	 * 根据get请求内容填充这个Model并返回实际GET提交的数组<br/>
	 * 这个方法通常用在信息检索页面的批量属性提交<br/>
	 * 切不可用此方法得来的数据进行CUD操作！
	 *
	 * @return array 提交的数据
	 */
	public function load_array_from_get(){
		$prevstr = $this->YYUCSYS_tablename.'T'.$this->YYUCSYS_post_id;
		$begin = strlen($prevstr);
		if(!empty($_GET[$prevstr.'id'])&&isset($this->YYUCSYS_db)){
			$this->find($_GET[$prevstr.'id']);
		}
		$bakarr = array();
		foreach ($_GET as $k=>$v){
			if(strpos($k, $prevstr)===0){
				$field = substr($k, $begin);
				$this->$field = $v;
				if(trim($v)!='' && strpos($field, 'TXT@')!==0){
					$bakarr[$field] = $v;
				}				
			}
		}
		//填充实体
		if(!$this->YYUCSYS_isorgin){
			$this->fill_entity_field();
		}
		return $bakarr;
	}
	
	/**
	 * 根据post请求内容填充多个model并存入数组中
	 * @return array 模型集合
	 */
	public function load_all_from_post(){
		$prevstr = $this->YYUCSYS_tablename.'T';
		$begin = strlen($prevstr);
		$begin2 = strlen($prevstr)+32;
		//定义存储提交信息的数组
		$posts = array();
		if(get_magic_quotes_gpc()){
			foreach ($_POST as $k=>$v){
				if(strpos($k, $prevstr)===0){
					$uuid = substr($k, $begin,32);
					$field = substr($k, $begin2);
					if(!isset($posts[$uuid])){
						$posts[$uuid] = array();
					}
					if(is_array($v)){
						$v = ','.implode(',', $v).',';
					}
					$posts[$uuid][$field] = stripslashes($v);
				}
			}
		}else{
			foreach ($_POST as $k=>$v){
				if(strpos($k, $prevstr)===0){
					$uuid = substr($k, $begin,32);
					$field = substr($k, $begin2);
					if(!isset($posts[$uuid])){
						$posts[$uuid] = array();
					}
					if(is_array($v)){
						$v = ','.implode(',', $v).',';
					}
					$posts[$uuid][$field] = $v;
				}
			}
		}
		//填充实体
		$models = array();
		foreach ($posts as $k=>$post){
			if(!$this->YYUCSYS_isorgin){
				$mod = new $this->YYUCSYS_tablename(k);
			}else{
				$mod = new model($this->YYUCSYS_tablename,k);
			}			
			if(!empty($post['id'])&&isset($this->YYUCSYS_db)){
				$mod->find($post['id']);
			}
			foreach ($post as $kk=>$vv){
				$mod->$kk = $vv;
			}
			if(!$this->YYUCSYS_isorgin){
				$mod->fill_entity_field();
			}
			$models [] = $mod;
		}		
		return $models;
	}
	
	/**
	 * 构造函数
	 * @param string $tablename 虚拟表名
	 * @param string $postid 表单提交的区分ID
	 */
	function __construct($tablename = 'f',$postid = ''){
		if(get_class($this) == 'SampleModel'){
			$this->YYUCSYS_isorgin = true;
		}else{
			$this->YYUCSYS_isorgin = false;
		}
		$this->YYUCSYS_postid = $postid;
		$this->YYUCSYS_post_id = strlen($postid) === 32 ? $postid : md5('YYUC_'.$postid);
		$this->YYUCSYS_tablename = $tablename;
		//如果不是原始模型 增加各个字段的验证信息供页面form标签使用
		if(!$this->YYUCSYS_isorgin&&!isset(self::$YYUCSYS_FIELD_FORMVAL[$this->YYUCSYS_tablename])){
			self::$YYUCSYS_FIELD_FORMVAL[$this->YYUCSYS_tablename] = array();
			self::$YYUCSYS_first_valset[$this->YYUCSYS_tablename] = true;
			$this->validate();
			self::$YYUCSYS_first_valset[$this->YYUCSYS_tablename] = false;
		}
	}
	/**
	 * 设置某一字段的错误信息 支持多个信息输出
	 * @param string $field 字段名称
	 * @param string $errmsg 错误信息
	 */
	public function set_err_msg($field,$errmsg){
		if(!isset($this->YYUCSYS_field_error[$field])){
			$this->YYUCSYS_field_error[$field] = array();
		}
		$this->YYUCSYS_field_error[$field][] = $errmsg;
		$this->YYUCSYS_val_fail = true;
	}
	/**
	 * 获得模型的验证错误信息数组<br/>
	 * 这是一个以字段名称为数组下标的二维数组
	 * 没有则返回空数组
	 * @return array 错误信息数组
	 */
	public function errors(){
		return $this->YYUCSYS_field_error;
	}
	/**
	 * 获得某一字段的验证错误信息数组<br/>
	 * 返回该字段的验证错误信息,没有则返回空数组
	 * @param string $field 字段名称
	 * @return array 错误信息数组
	 */
	public function field_error($field){
		if(!isset($this->YYUCSYS_field_error[$field])){
			return array();
		}
		return $this->YYUCSYS_field_error[$field];
	}
	/**
	 * 获得某一字段的验证错误信息字串(','分隔)<br/>
	 * 返回该字段的验证错误信息,没有则返回空串
	 * @param string $field 字段名称
	 * @return string 错误信息
	 */
	public function field_errors($field){
		if(!isset($this->YYUCSYS_field_error[$field])){
			return '';
		}
		return implode(',', $this->YYUCSYS_field_error[$field]);
	}
	/**
	 * 字段国内电话号码格式合法性验证
	 * @param string $field 字段名称
	 * @param string $errmsg 错误信息
	 */
	public function val_tel($field,$errmsg=null){
		$this->val_reg($field,"/^((\d{7,8})|(\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$/", $errmsg===null?YYUC::i18n('validate.tel'):$errmsg);
	}
	/**
	 * 字段国内手机合法性验证
	 * @param string $field 字段名称
	 * @param string $errmsg 错误信息
	 */
	public function val_phone($field,$errmsg=null){
		$this->val_reg($field,"/^0{0,1}(13[0-9]|14[0-9]|15[0-9]|18[0-9])[0-9]{8}$/", $errmsg===null?YYUC::i18n('validate.phone'):$errmsg);
	}
	/**
	 * 字段国内电话号码和手机格式合法性验证
	 * @param string $field 字段名称
	 * @param string $errmsg 错误信息
	 */
	public function val_telphone($field,$errmsg=null){
		$this->val_reg($field,"/(^0{0,1}(13[0-9]|14[0-9]|15[0-9]|18[0-9])[0-9]{8}$)|(^((\d{10})|(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})|(\d{4}|\d{3})-(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1})|(\d{7,8})-(\d{4}|\d{3}|\d{2}|\d{1}))$)/", $errmsg===null?YYUC::i18n('validate.tel'):$errmsg);
	}
	/**
	 * 字段Email格式合法性验证
	 * @param string $field 字段名称
	 * @param string $errmsg 错误信息
	 */
	public function val_email($field,$errmsg=null){
		$this->val_reg($field,"/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", $errmsg===null?YYUC::i18n('validate.email'):$errmsg);
	}
	/**
	 * 字段url合法性验证
	 * @param string $field 字段名称
	 * @param string $errmsg 错误信息
	 */
	public function val_url($field,$errmsg=null){
		$this->val_reg($field,"/^(http|https):\/\/[^\s]*$/", $errmsg===null?YYUC::i18n('validate.url'):$errmsg);
	}
	/**
	 * 字段非空验证
	 * @param string $field 字段名称
	 * @param string $errmsg 错误信息
	 */
	public function val_notnull($field,$errmsg=null){
		if(!$this->set_field_required_string($field,null, $errmsg===null?YYUC::i18n('validate.null'):$errmsg)){
			if(trim($this->$field)==''){
				$this->set_err_msg($field,$errmsg?$errmsg:YYUC::i18n('validate.null'));
			}
		}
	}
	/**
	 * 字段是否为数字的验证
	 * @param string $field 字段名称
	 * @param string $errmsg 错误信息
	 */
	public function val_number($field,$errmsg=null){
		if($this->$field==''){
			$this->$field = '0';
		}
		$this->val_reg($field,"/^\d+\.?\d*$/", $errmsg===null?YYUC::i18n('validate.number'):$errmsg);
	}
	/**
	 * 字段是否为整数的验证
	 * @param string $field 字段名称
	 * @param string $errmsg 错误信息
	 */
	public function val_integer($field,$errmsg=null){
		if($this->$field==''){
			$this->$field = null;
		}
		$this->val_reg($field,"/^(0|[1-9][0-9]*)$/", $errmsg===null?YYUC::i18n('validate.integer'):$errmsg);
	}
	/**
	 * 字段是否为中文的验证
	 * @param string $field 字段名称
	 * @param string $errmsg 错误信息
	 */
	public function val_chinese($field,$errmsg=null){		
		$this->val_reg($field,"/^[\x{4e00}-\x{9fa5}\s]+$/u", $errmsg===null?YYUC::i18n('validate.chinese'):$errmsg,'/^[\u4e00-\u9fa5]+$/i');
	}
	/**
	 * 字段是否为英文和数字的验证
	 * @param string $field 字段名称
	 * @param string $errmsg 错误信息
	 */
	public function val_english($field,$errmsg=null){
		$this->val_reg($field,"/^[1-9a-zA-Z\s]+$/", $errmsg===null?YYUC::i18n('validate.english'):$errmsg);
	}
	/**
	 * 字段最小长度的验证
	 * @param string $field 字段名称
	 * @param integer $min 允许的最小长度
	 * @param string $errmsg 错误信息{0}替换为$min
	 */
	public function val_min_length($field,$min,$errmsg=null){
		$this->val_reg($field,"/^.{".$min.",}$/", str_replace('{0}', $min, $errmsg===null?YYUC::i18n('validate.short'):$errmsg));
	}
	/**
	 * 字段最大长度的验证
	 * @param string $field 字段名称
	 * @param integer $max 允许的最大长度
	 * @param string $errmsg 错误信息 {0}替换为$max
	 */
	public function val_max_length($field,$max,$errmsg=null){
		$this->val_reg($field,"/^.{0,".$max."}$/", str_replace('{0}', $max, $errmsg===null?YYUC::i18n('validate.long'):$errmsg));
	}
	/**
	 * 字段的自定义正则表达式验证
	 * @param string $field 字段名称
	 * @param string $reg 正则表达式
	 * @param string $errmsg 错误信息
	 * @param string $regjs  字段的前台js信息的验证规则
	 */
	public function val_reg($field,$reg,$errmsg,$regjs=null){
		if($regjs===null){
			$regjs = $reg;
		}
		//非空验证通过才执行其他验证
		if(!$this->set_field_required_string($field, $regjs, $errmsg)&&$this->$field!=''&&!preg_match($reg, $this->$field)){
			$this->set_err_msg($field,$errmsg);
		}
	}
	
	/**
	 * 最小值校验
	 */
	public function val_min($field,$min,$errmsg=null){
		$num = floatval($this->$field);
		if($num<$min){
			$this->set_err_msg($field,$errmsg===null?YYUC::i18n('validate.min'):$errmsg);
		}
	}
	/**
	 * 最大值校验
	 */
	public function val_max($field,$max,$errmsg=null){
		$num = floatval($this->$field);
		if($num>$max){
			$this->set_err_msg($field,$errmsg===null?YYUC::i18n('validate.max'):$errmsg);
		}
	}
	/**
	 * 输出hidden标签
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	public function hidden($name,$attrs=''){
		$value = 'value = "'.htmlspecialchars($this->$name,ENT_QUOTES).'"';
		if(is_array($attrs)){
			if(array_key_exists('value', $attrs)){
				$value = '';
			}
			$attrs = YYUC_get_attrs_from_array($attrs);
		}
		return '<input type="hidden" '.$value.' name="'.$this->elname($name).'" id="'.$this->elid($name).'" '.$attrs.'/>';
	}
	
	/**
	 * 所有有效的数据都输出hidden标签<br/>
	 * 这是一种页面参数整体传递的简便方式<br/>
	 * 为了便于灵活覆盖，建议将其放在Form的最顶端(tk方法之后)<br/>
	 * 把页面信息放在页面中是很不安全的，所以强烈建议只在新增时使用此方法
	 * 
	 * @param string $names 字段名称数组
	 * @return string 标签html字串
	 */
	public function all($names = null){
		if($names === null){
			$names = array_keys(get_object_vars($this));
		}
				
		$htmlres = '';
		foreach ($names as $k){
			if(!String::start_with($k, 'YYUCSYS')){
				if(!empty($this->$k)){
					$value = htmlspecialchars($this->$k,ENT_QUOTES);
					$htmlres .= '<textarea style="display:none" name="'.$this->elname($k).'">'.$value.'</textarea>';
				}				
			}
		}
		return $htmlres;
	}
	
	/**
	 * 输出text标签
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	public function text($name,$attrs=''){
		return $this->input_type_text('text', $name,$attrs);
	}
	
	/**
	 * 验证码标签
	 * @param string $name 自定义验证字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @param string $local 要展现的验证码位置元素的ID 一定要在Form内部
	 * @param boolean $ischinese 是否是中文验证 默认false
	 * @param integer $codenum 验证码字符个数默认4
	 * @param integer $width 宽度默认120
	 * @param integer $height 高度默认45
	 * 
	 * @return string 标签html字串
	 */
	function vercode($local,$width=120,$height=45,$attrs='',$ischinese=false,$codenum=4){
		if(is_array($attrs)){
			$attrs = YYUC_get_attrs_from_array($attrs);
		}
		$elname = $this->elname('YYUC_vercode');
		$id = $this->elid('YYUC_vercode');
		$versrc = '/@system/vercode-'.$codenum.'-'.($ischinese ? '1' : '0').Conf::$suffix;
		$html = '<input type="text" required="required" placeholder="点击输入验证码" name="'.$elname.'" id="'.$id.'" '.$attrs.'/>';
		$html .= '<script>';
		$html .= '$(function(){$("#'.$id.'").blur();$("#'.$id.'").focus(function(){ if(!$("#yyuc_verimg").is("img")){';
		$html .= 'var verimg = $(\'<img id="yyuc_verimg" title="点击切换" style="width:'.$width.'px;height:'.$height.'px;" />\');';
		$html .= 'verimg.attr("src","'.$versrc.'?r="+Math.random());';
		$html .= '$("#'.$local.'").append(verimg);';
		$html .= 'verimg.click(function(){ verimg.attr("src","'.$versrc.'?r="+Math.random());})';
		$html .= '}});';		
		$html .= '});</script>';
		return $html;
	}
	
	/**
	 *判断是不是验证码通过验证了
	 */
	function vercodeok(){
		if(isset($this->YYUC_vercode)){
			return strtolower($this->YYUC_vercode) == strtolower(Session::get('YYUC_vercode'));
		}else{
			return false;
		}		
	}
	/**
	 * 输出password标签
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	public function password($name,$attrs=''){
		return $this->input_type_text('password', $name,$attrs);
	}
	
	/**
	 * 文件上传
	 * 
	 * 参照Upload::init方法使用
	 */
	public function upload($name,$config=null,$data=null,$url=null,$picareaid=null,$cutpic=null){
		Upload::bind_model($this);
		return Upload::init($name,$config,$data,$url,$picareaid,$cutpic);
	}
	/**
	 * 输出range标签
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	public function range($name,$attrs=''){
		return $this->input_type_text('range',$name,$attrs);
	}
	/**
	 * 输出email标签
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	public function email($name,$attrs=''){
		return $this->input_type_text('email', $name,$attrs);
	}
	
	/** 
	 * 取得属性再Form中的name
	 * 
	 * @param string $name 字段名称 标签name
	 * @return string
	 */
	public function field_form_name($name){
		return $this->YYUCSYS_tablename.'T'.$this->YYUCSYS_post_id.$name;
	}
	/**
	 * 不同type的input标签调用
	 * @param string $type 标签类型 input hidden...
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	public function input_type_text($type,$name,$attrs=''){
		if(is_array($attrs)){
			$attrs = YYUC_get_attrs_from_array($attrs);
		}
		$value = htmlspecialchars(isset($this->$name)?$this->$name:'',ENT_QUOTES);
		return '<input type="'.$type.'" '.$this->field_required_string($name).' value="'.$value.'" name="'.$this->elname($name).'" id="'.$this->elid($name).'" '.$attrs.'/>';
	}
	
	/**
	 * color标签调用
	 * @param string $type 标签类型 input hidden...
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	public function color($name,$attrs='',$needal = false){
		if(is_array($attrs)){
			$attrs = YYUC_get_attrs_from_array($attrs);
		}
		$value = htmlspecialchars(isset($this->$name)?$this->$name:'',ENT_QUOTES);		
		$datevalue = ($value==''||$value=='0')?'':$value;
		$tag = '<div name="Alpha_'.$this->elid($name).'" id="Alpha_'.$this->elid($name).'"></div><input type="hidden" needal="'.$needal.'" colorid="Alpha_'.$this->elid($name).'" relobj="yyuccolor" rel="yyuc" value="'.$value.'" id="'.$this->elid($name).'" name="'.$this->elname($name).'" />';
		return $tag;
	}
	
	/**
	 * 获得页面标签的id
	 * @param string $name 字段名称 标签name
	 * @return string 标签id
	 */
	public function elid($name){
		return $this->YYUCSYS_tablename.$this->YYUCSYS_postid.$name;
	}
	
	/**
	 * 获得页面标签的name<br/>
	 * 如果开启了表单令牌此处获得的name是经过框架加密的(防止恶意信息提交)
	 * @param string $name 字段名称 标签name
	 * @return string 标签id
	 */
	public function elname($name){
		if(Page::$tk_str != null){
			return String::encryption($this->YYUCSYS_tablename.'T'.$this->YYUCSYS_postid.$name,Page::$tk_str);
		}else{
			return $this->YYUCSYS_tablename.'T'.$this->YYUCSYS_post_id.$name;
		}		
	}
	
	
	/**
	 * 输出selectinput标签
	 */	
	public function selectinput($array,$name='',$attrs='',$autoindex = null,$autotext = true){
		return $this->select($array,$name,$attrs,$autoindex,$autotext,true);
	}
	/**
	 * 输出select标签
	 * 
	 * @param mixed $array 数据或者字段
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @param boolean $autoindex 是否自动数字索引 不填则自动判断
	 * @param boolean $autotext 是否同时提交文字
	 * @param boolean $inputsel 是否支持输入过滤
	 */
	public function select($array,$name='',$attrs='',$autoindex = false,$autotext = true,$inputsel = false){
		$additionstr = '"';
		if(is_string($array)){
			$attrs = $name;
			$name = $array;			
			$array = $this->field_data($name);
		}
		if(is_array($attrs)){
			$attrs = YYUC_get_attrs_from_array($attrs);
		}
		
		$value = $this->$name;
		$value = explode(',', $value);
		if(strpos($attrs, 'multiple')!==false){
			$additionstr = '[]"';
		}elseif(count($value)>1){
			$additionstr = '[]" multiple="multiple" ';
		}
		$autotextstr = '';
		if($autotext){
			$autotextstr ='yyuc_autotext="'.$this->elname('TXT@'.$name).'"';
		}
		if($inputsel){
			$autotextstr .= ' rel="yyuc" relobj="yyucselectinput" ';
		}
		$tag = '<select  '.$this->field_required_string($name).' name="'.$this->elname($name).$additionstr.'  id="'.$this->elid($name).'" '.$attrs.' '.$autotextstr.'>';
		
		$isnumarr = $autoindex;
		foreach ($array as $k=>$v){
			if($isnumarr===null){
				if($k===0 && isset($array[count($array)-1])){				
					$isnumarr = true;
				}else{
					$isnumarr = false;
				}
			}
			if($isnumarr){
				$k = $v;
			}
			$vvstr = '';
			if(is_array($v)){
				$tv = $v;
				$v = null;
				foreach ($tv as $kk=>$vv){
					if($v===null && $kk !='k'){
						$v = $vv;
					}
					$vvstr .= 'yyucattr_'.$kk.'="'.htmlspecialchars($vv).'" ';
				}
			}
			$tag.='<option '.$vvstr.' value="'.htmlspecialchars($k).'" '.(in_array($k.'', $value,true)? 'selected="selected"' : '').'>'.htmlspecialchars($v).'</option>';
		}
		$tag.='</select>';
		return $tag;
	}
	
	
	/**
	 * 输出联动select标签
	 * @param string $tn 表名称(满足 id pid name 的数据库要求 一级pid为0)
	 * @param array $name_arr 字段名称数组 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @param string $addwhere 附加SQL条件
	 * @return string 标签html字串
	 */
	public function mulselect($tn,$name_arr,$attrs='',$addwhere='',$impstr='',$autotext = true){
		$seluuid = uniqid();
		if(is_array($attrs)){
			$attrs = YYUC_get_attrs_from_array($attrs);
		}
		$arr_len = count($name_arr);
		
		$addwherestr = '';
		$pid = '0';
		if(!empty($addwhere)){
			$addwherestr = ' and '.$addwhere;
		}
		
		$alltags = '';
		
		$autotextstr = '';
		
		$nopid = false;
		foreach ($name_arr as $index=>$name){
			$value = $this->$name;
			$onchange = '';
			if($index<($arr_len-1)){
				if(is_array($tn)){
					$ttn = $tn[$index];
					$ntn = $tn[$index+1];
					if($pid =='0'){
						$nopid = true;
					}				
				}else{
					$ntn = $tn;
					$ttn = $tn;
				}
				$change = '"_.getnextsel(this,\''.String::encryption($ntn).'\',\''.String::encryption($addwherestr).'\')"';
				$onchange  = 'onchange='.$change.' onblur='.$change;
			}else{
				if(is_array($tn)){
					$ttn = $tn[$index];
				}else{
					$ttn = $tn;
				}
			}
			
			if($autotext){
				$autotextstr ='yyuc_autotext="'.$this->elname('TXT@'.$name).'"';
			}

			$tag = '<select seluuid="'.$seluuid.'" selindex="'.$index.'" '.$this->field_required_string($name).' name="'.$this->elname($name).'"  id="'.$this->elid($name).'" '.$attrs.' '.$onchange.' '.$autotextstr.'>';
			$tag.='<option value="" >'.YYUC::i18n('.select_please').'</option>';
			if($pid != null){
				$m = new Model($ttn);
				$beg = "pid='".$pid."'";
				if($nopid){
					$beg = "1=1";
					$nopid = false;
				}
				$array = $m->where($beg.$addwherestr)->map_array('id', 'name');
				$pid = null;
				foreach ($array as $k=>$v){
					$seltxt = '';
					if($k == $value){
						$seltxt = 'selected="selected"';
						$pid = $value;
					}
					$tag.='<option value="'.htmlspecialchars($k).'" '.$seltxt.'>'.htmlspecialchars($v).'</option>';
				}
			}
			$tag.='</select>';
			if($alltags != ''){
				$alltags = $alltags.$impstr;
			}
			$alltags .= $tag;
		}
		return $alltags;
	}
	/**
	 * 输出checkbox标签
	 * @param mixed $array 数据或者字段
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	public function checkbox($array,$name='',$attrs='',$space='&nbsp;&nbsp;'){
		if(is_string($array)){
			$attrs = $name;
			$name = $array;			
			if(is_array($attrs)){
				$attrs = YYUC_get_attrs_from_array($attrs);
			}
			$value = false;
			if($this->$name=='1'){
				$value = true;
			}
			return '<input type="hidden" name="'.$this->elname($name).'" value="0"/><input type="checkbox" value="1" name="'.$this->elname($name).'" '.($value?'checked="checked"':'').' id="'.$this->elid($name).'" '.$attrs.'/>';
		}else {
			if(isset($array[0])){
				$arr = $array;
				$array = array();
				foreach ($arr as $ar){
					$array[$ar] = $ar;
				}
			}
			
			if(isset($this->$name)){
				$value = $this->$name;
			}			
			$value = explode(',', $value);
			$tag = '';
			
			foreach ($array as $k=>$v){
				$k=strval($k);
				$tag.='<input type="checkbox" name="'.$this->elname($name).'[]" value="'.htmlspecialchars($k).'" '.((in_array($k, $value,true))? 'checked="checked"' : '').' '.$attrs.'/>'.$v.$space;
			}
			return $tag;
		}

	}
	/**
	 * 输出radio标签
	 * @param mixed $array 数据或者字段
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	public function radio($array,$name='',$attrs='',$space='&nbsp;&nbsp;'){
		if(is_string($array)){
			$attrs = $name;
			$name = $array;			
			$array = $this->field_data($name);
		}
		if(is_array($attrs)){
			$attrs = YYUC_get_attrs_from_array($attrs);
		}
		$value = $this->$name;
		$tag = '';
		foreach ($array as $k=>$v){
			
			$tag.='<input type="radio" name="'.$this->elname($name).'" value="'.htmlspecialchars($k).'" '.(($k==$value)? 'checked="checked"' : '').' '.$attrs.'/>'.$v.$space;
		}
		return $tag;
	}
	/**
	 * 输出textarea标签
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	function textarea($name,$attrs=''){
		if(is_array($attrs)){
			$attrs = YYUC_get_attrs_from_array($attrs);
		}
		$value = htmlspecialchars(isset($this->$name)?$this->$name:'',ENT_QUOTES);
		$tag = '<textarea name="'.$this->elname($name).'" '.$this->field_required_string($name).' id="'.$this->elid($name).'" '.$attrs.'>'.$value.'</textarea>';
		return $tag;
	}
	/**
	 * 输出texteditor标签
	 * @param string $name  字段名称 标签name
	 * @param mixed $level 配置级别 编辑框的展现复杂度:{1-7},或者原始的构建数组
	 * @param integer $width 宽度 默认640
	 * @param integer $height 高度 默认300
	 * @return string 标签html字串
	 */
	function texteditor($name,$level=3,$width='640px',$height='300px'){
		$value = htmlspecialchars(isset($this->$name)?$this->$name:'',ENT_QUOTES);
		$tag = '<textarea style="width:'.$width.';height:'.$height.';" width="'.$width.'" height="'.$height.'" name="'.$this->elname($name).'" id="'.$this->elid($name).'">'.$value.'</textarea>';
		$opts = null;
		if(is_integer($level)){
			$level = intval($level);
			$allowupload = ($level==1||$level==3||$level==4||$level==6||$level==7)?'true':'false';
			$opts = '{afterChange:window.kindeditorAfterChange,langType:"'.$_SERVER['HTTP_ACCEPT_LANGUAGE'].'",allowImageUpload:'.$allowupload.',allowFlashUpload:'.$allowupload.',allowMediaUpload:'.$allowupload.',allowFileManager:'.($level==4||$level==7?'true':'false').',items:kindeditor_item'.($level==1?'0':intval($level/5+1)).'}';
		}else {
			$opts = '{afterChange:window.kindeditorAfterChange,langType:"'.$_SERVER['HTTP_ACCEPT_LANGUAGE'].'",'.$level.'}';
		}
		$opts = htmlspecialchars($opts,ENT_QUOTES);		
		return $tag.'<input type="hidden" relobj="kindeditor" rel="yyuc" editorid="'.$this->elid($name).'" value="'.$opts.'" />';
	}
	/**
	 * 输出日期时间标签
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	function datetime($name,$attrs='',$isdate = true){
		if(is_array($attrs)){
			$attrs = YYUC_get_attrs_from_array($attrs);
		}
		$value = trim($this->$name);
		$datevalue = ($value==''||$value=='0')?'': $isdate ? date('Y-m-d H:i:s',strtotime($value)) : date('Y-m-d H:i:s',$value);
		if($isdate){
			$isdate = 'true';
		}else{
			$isdate = 'false';
		}
		$tag = '<input type="text" '.$this->field_required_string($name).' value="'.$datevalue.'"  id="'.$this->elid($name).'" '.$attrs.' name="'.$this->elname($name).'" onfocus="WdatePicker({ dateFmt:\'yyyy-MM-dd HH:mm:ss\'})" relobj="yyuccalendar" rel="yyuc"/>';
		return $tag;
	}
	/**
	 * 输出日期标签
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	function date($name,$attrs='',$isdate = true){
		if(is_array($attrs)){
			$attrs = YYUC_get_attrs_from_array($attrs);
		}
		$value = trim($this->$name);
		$datevalue = ($value==''||$value=='0')? '' : $isdate ? date('Y-m-d',strtotime($value)) : date('Y-m-d',$value);
		if($isdate){
			$isdate = 'true';
		}else{
			$isdate = 'false';
		}
		$tag = '<input type="text" '.$this->field_required_string($name).' value="'.$datevalue.'"  id="'.$this->elid($name).'" '.$attrs.' name="'.$this->elname($name).'" onfocus="WdatePicker({ dateFmt:\'yyyy-MM-dd\'})" relobj="yyuccalendar" rel="yyuc"/>';
		return $tag;
	}

	
	/**
	 * 输出手机日期标签
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	function mdate($name,$attrs='',$isdate = true){
		if(is_array($attrs)){
			$attrs = YYUC_get_attrs_from_array($attrs);
		}
		$value = trim($this->$name);
		$datevalue = ($value==''||$value=='0')? '' : $isdate ? date('Y-m-d',strtotime($value)) : date('Y-m-d',$value);
		if($isdate){
			$isdate = 'true';
		}else{
			$isdate = 'false';
		}
		$tag = '<input type="text" '.$this->field_required_string($name).' value="'.$datevalue.'"  id="'.$this->elid($name).'" '.$attrs.' name="'.$this->elname($name).'" mdate="date" relobj="yyucmcalendar" rel="yyuc"/>';
		return $tag;
	}
	
	/**
	 * 输出日期时间标签
	 * @param string $name 字段名称 标签name
	 * @param mixed $attrs 其他属性 可以是字符或者数组
	 * @return string 标签html字串
	 */
	function mdatetime($name,$attrs='',$isdate = true){
		if(is_array($attrs)){
			$attrs = YYUC_get_attrs_from_array($attrs);
		}
		$value = trim($this->$name);
		$datevalue = ($value==''||$value=='0')?'': $isdate ? date('Y-m-d H:i:s',strtotime($value)) : date('Y-m-d H:i:s',$value);
		if($isdate){
			$isdate = 'true';
		}else{
			$isdate = 'false';
		}
		$tag = '<input type="text" '.$this->field_required_string($name).' value="'.$datevalue.'"  id="'.$this->elid($name).'" '.$attrs.' name="'.$this->elname($name).'" mdate="datetime" relobj="yyucmcalendar" rel="yyuc"/>';
		return $tag;
	}
	/**
	 * 字段不能为空 则返回 required="required" 否则返回 ''
	 * @param string $field 字段名称 
	 */
	public function field_required_string($field){
		$valstr = " ";
		if(self::$YYUCSYS_FIELD_CANNULL[$this->YYUCSYS_tablename][$field]===false){
			$valstr .= 'required="required" ';			
		}
		if(!$this->YYUCSYS_isorgin){
			//自定义的构造
			if(isset(self::$YYUCSYS_FIELD_FORMVAL[$this->YYUCSYS_tablename])&&isset(self::$YYUCSYS_FIELD_FORMVAL[$this->YYUCSYS_tablename][$field])){
				$tvalstr = self::$YYUCSYS_FIELD_FORMVAL[$this->YYUCSYS_tablename][$field];
				$tvalstr = str_replace('@YYUCSYSID', '@'.$this->id, $tvalstr);
				$valstr .= 'YYUCVAL="'.htmlspecialchars($tvalstr,ENT_QUOTES).'" ';
			}
		}
		return $valstr;
	}
	/**
	 * 根据字段的验证信息 构造Form表单的验证规则
	 * @param string $field 字段名称 
	 * @param string $reg 验证的正则规则
	 * @param string $msg 错误信息
	 */
	public function set_field_required_string($field,$reg,$msg){
		if($reg===null){
			//需要非空的信息写入
			self::$YYUCSYS_FIELD_CANNULL[$this->YYUCSYS_tablename][$field] = false;
		}elseif(self::$YYUCSYS_first_valset[$this->YYUCSYS_tablename]){//第一次执行
			if(!isset(self::$YYUCSYS_FIELD_FORMVAL[$this->YYUCSYS_tablename][$field])){
				self::$YYUCSYS_FIELD_FORMVAL[$this->YYUCSYS_tablename][$field] = '';
			}
			self::$YYUCSYS_FIELD_FORMVAL[$this->YYUCSYS_tablename][$field] = $reg.'REG@MSG'.$msg.'ONE@ANOTHER'.self::$YYUCSYS_FIELD_FORMVAL[$this->YYUCSYS_tablename][$field];
		}
		return self::$YYUCSYS_first_valset[$this->YYUCSYS_tablename];
	}
}
?>