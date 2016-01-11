<?php 
class Users extends Model {
	//此处定义虚拟字段
	//public $virtual_field = null;
	/**
	* 构造函数
	*/
	function __construct($postid=''){
		parent::__construct('users',$postid);		
	}

	/**
	* 数据入库之前的合法性验证
	*/
	public function validate(){
		//验证示例
		$this->val_eamil('mail','邮箱格式貌似不正确！');
		//$this->val_min_length('un', 3,'帐号长度不能小于3个字符！');
		//$this->val_max_length('un', 30,'帐号长度不能大于于30个字符！');
		$this->val_max_length('pwd', 60,'密码长度不能大于于60个字符！');
		$this->val_min_length('pwd', 6,'密码长度不能小于6个字符！');
		$this->val_unique('nick','你来晚了，该昵称太受欢迎了，换一个吧！');
		//$this->val_unique('un','该帐号已经被注册！');
		$this->val_unique('mail','该邮箱已经被注册了!');
		$this->val_integer('qq','QQ号码不正确！');
		$this->val_tel('tel','电话号码格式不正确！');
		$this->val_chinese('name','既然在中国，就用用中文名字吧！');
		
		//非空校验
		$this->val_notnull('mail','不能留空呀！');
		$this->val_notnull('pwd','不能留空呀！');
		$this->val_notnull('nick','不能留空呀！');
		$this->val_notnull('mail','不能留空呀！');
		$this->val_notnull('name','不能留空呀！');
		$this->val_notnull('bnian','不能留空呀！');
		$this->val_notnull('byue','不能留空呀！');
		$this->val_notnull('bri','不能留空呀！');
		$this->val_notnull('lsheng','不能留空呀！');
		$this->val_notnull('lshi','不能留空呀！');
		$this->val_notnull('lxian','不能留空呀！');
		$this->val_notnull('sex','不能留空呀！');
	}

	/**
	* 根据数据库的数据进行虚拟字段的填充
	*/
	public function fill_virtual_field(){
		//虚拟字段填充示例
		//$this->virtual_field = $this->id.'_'.$this->name;
		$this->local = $this->getStringlocal();
		$this->nyr = $this->bnian.'年'.$this->byue.'月'.$this->bri.'日';		
	}

	function getStringlocal(){		
		$l = u_getareas();
		return $l[$this->lsheng].'/'.$l[$this->lshi].'/'.$l[$this->lxian];
	}
	
	/**
	* 根据模型中的虚拟字段回填数据库字段数据
	*/
	public function fill_entity_field(){
		//回填示例
		//$names = explode('_', $this->virtual_field);
		$this->birthday = $this->bnian.'-'.$this->byue.'-'.$this->bri;		
	}

	/**
	* 读取Model类的访问地址,有些模型数据的访问地址不止一个,需自行扩展
	*/
	public function path(){
		//组合示例
		//$path = '/'.$this->theme.'/'.date('Y-m-d', $this->posttime).'/'.$this->id.'.html';
		//return $path;
		
	}

}
?>