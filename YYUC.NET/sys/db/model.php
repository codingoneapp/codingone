<?php
/**
 * 歪歪框架模型类文件
 * @author mqq
 * 
 */
class Model extends SampleModel{
	/**数据库真实表名*/
	public $YYUCSYS_real_tablename;
	/**
	 * DB类的引用
	 * @var DB
	 */
	public $YYUCSYS_db = null;
	/**排序方式*/
	public $YYUCSYS_order = null;
	/**分组方式*/
	public $YYUCSYS_group = null;
	/**查询范围*/
	public $YYUCSYS_limit = null;
	/**要查询的字段 默认为‘*’*/
	public $YYUCSYS_select = '*';
	/**要查询的条件可以为数组和字符串‘*’*/
	public $YYUCSYS_condition = null;
	/**要查询的条件参数‘*’*/
	public $YYUCSYS_pam = null;
	/**
	 * 设置这个Model的标识id<br/>
	 * 只是设置主键字段,不执行实际的DB查询操作<br/>
	 * 一般在更新或删除之前调用
	 * @param string $id 主键
	 * @return Model 模型本身
	 */
	public function id($id){
		$this->id = $id;
		return $this;
	}
	/**
	 * 根据id或者数组条件填充这个model<br/>
	 * 示例: find(5) 或 find(array('name'=>'mqq','sex'=>'man'))
	 * @param mixed $id 主键或条件数组
	 * @param array $pam 参数值的数组
	 * @return Model 模型本身
	 */
	public function find($id=null,$pam=null){
		$fillok = false;
		DBDes::init_by_model($this,'R');
		if($id === null){
			$fillok = $this->YYUCSYS_db->fill_one_model($this,$this->YYUCSYS_condition,null,$this->YYUCSYS_order,$this->YYUCSYS_group,'1');
		}elseif(is_array($id)||$pam!==null){
			$fillok = $this->YYUCSYS_db->fill_one_model($this,$id,$pam,$this->YYUCSYS_order,$this->YYUCSYS_group,'1');
		}else{
			$fillok = $this->YYUCSYS_db->fill_one_model($this,array('id'=>$id));
		}
		if($fillok&&!$this->YYUCSYS_isorgin){
			$this->fill_virtual_field();
		}
		return $this;	
	}
	
	/**
	 * 判断该模型是否含有ID<br/>
	 * 查看数据库中是否有独立的一条数据与model对应
	 * @return boolean 
	 */
	public function has_id(){
		return trim($this->id) != '';
	}

	/**
	 * 查询并返回模型结果集<br/>
	 * 如果$condition为数组则根据数组条件返回符合结果的列表<br/>
	 * 如果$condition是字串则必须是 where语句之后的字串，亦可通过?和$pam数组组合成SQL语句<br/>
	 * 如果不传入条件则根据where方法 的预设参数查询,如果where未被调用过则列出所有<br/>
	 * @param mixed $condition 条件字符串或条件数组
	 * @param array $pam 参数数组
	 * @return array Model实体的集合
	 */
	public function list_all($condition=null,$pam=null){
		DBDes::init_by_model($this,'R');
		if(is_array($condition)){
			return $this->YYUCSYS_db->fill_models($this,$condition,$this->YYUCSYS_order,$this->YYUCSYS_group,$this->YYUCSYS_limit,$this->YYUCSYS_select);	
		}else if (is_string($condition)){
			return $this->YYUCSYS_db->fill_models($this,$condition,$this->YYUCSYS_order,$this->YYUCSYS_group,$this->YYUCSYS_limit,$this->YYUCSYS_select,$pam);	
		}else {
			return $this->YYUCSYS_db->fill_models($this,$this->YYUCSYS_condition,$this->YYUCSYS_order,$this->YYUCSYS_group,$this->YYUCSYS_limit,$this->YYUCSYS_select,$this->YYUCSYS_pam);
		}
	}
	
	/**
	 * 查询并返回单列结果数组<br/>
	 * @param string $column 列名
	 */
	public function list_column_data( $column='id'){
		DBDes::init_by_model($this,'R');
		return $this->YYUCSYS_db->fill_model_one_column($this,$this->YYUCSYS_condition,$this->YYUCSYS_order,$this->YYUCSYS_limit,$column,$this->YYUCSYS_pam);
	}
	
	/**
	 * 查询并返回数组结果集<br/>
	 * 如果$condition为数组则根据数组条件返回符合结果的列表<br/>
	 * 如果$condition是字串则必须是 where语句之后的字串，亦可通过?和$pam数组组合成SQL语句<br/>
	 * 如果不传入条件则根据where方法 的预设参数查询,如果where未被调用过则列出所有<br/>
	 * @param mixed $condition 条件字符串或条件数组
	 * @param array $pam 参数数组
	 * @return array 字符下标的数组集合
	 */
	public function list_all_array($condition=null,$pam=null){
		DBDes::init_by_model($this,'R');
		if(is_array($condition)){
			return $this->YYUCSYS_db->fill_models_array($this,$condition,$this->YYUCSYS_order,$this->YYUCSYS_group,$this->YYUCSYS_limit,$this->YYUCSYS_select);	
		}else if (is_string($condition)){
			return $this->YYUCSYS_db->fill_models_array($this,$condition,$this->YYUCSYS_order,$this->YYUCSYS_group,$this->YYUCSYS_limit,$this->YYUCSYS_select,$pam);	
		}else {
			return $this->YYUCSYS_db->fill_models_array($this,$this->YYUCSYS_condition,$this->YYUCSYS_order,$this->YYUCSYS_group,$this->YYUCSYS_limit,$this->YYUCSYS_select,$this->YYUCSYS_pam);
		}
	}
	
	/**
	 * 传入要查询的条件
	 * @param mixed $condition 条件字符串或条件数组
	 * @param array $pam 参数值的数组
	 * @return Model 模型本身
	 */
	public function where($condition,$pam=null){
		if(!empty($condition)){
			$this->YYUCSYS_condition = $condition;
			$this->YYUCSYS_pam = $pam;
		}		
		return $this;	
	}
	
	/**
	 * 对于指定ID的数据进行插入操作
	 * @param $data
	 */
	public function insert($data = null){
		$this->save($data,true);
	}
	
	/**
	 * 保存或更新此条信息<br/>
	 * @return mixed <br/>验证失败返回false<br/>存储失败返回null<br/>存储成功返回本身
	 */
	public function save($data = null,$forceinsert = false){
		if($data === null ){
			$data = get_object_vars($this);
		}
		foreach($data as $k=>$v){
			if($v === '' && (strpos($this->type($k), 'date')===0 || strpos($this->type($k), 'int') >= 0)){
				$data[$k] = null;
			}
			$this->$k = $v;
		}
		if(!$this->YYUCSYS_isorgin){
			//虚拟字段填入实体字段
			$this->fill_entity_field();
			$this->validate();
			if($this->YYUCSYS_val_fail){//如果验证失败
				return false;
			}
		}
		if(trim($data['id'])=='' || $forceinsert){
			DBDes::init_by_model($this,'C');
			$id = $this->YYUCSYS_db->insert_one_entity($this->YYUCSYS_real_tablename,$data,$forceinsert);
			if(!empty($id)){
				$this->id = $id;
				return $this;
			}else{
				return null;
			}
		}else{
			DBDes::init_by_model($this,'U');
			unset($data['id']);
			$res = $this->YYUCSYS_db->update_entity($this->YYUCSYS_real_tablename,"id='".$this->id."'",$data);
			if($res!==null){
				return $this;
			}else{
				return null;
			}
		}

	}
	/**
	 * 删除本条信息
	 * @return mixed 删除成功返回 1 失败返回null
	 */
	public function remove(){
		DBDes::init_by_model($this,'D');
		return $this->YYUCSYS_db->del_entity($this->YYUCSYS_real_tablename,"id='".$this->id."'");
	}
	/**
	 * 批量更新信息
	 * 如果不传入数据$data且存在id则$condition相当于$data并依据ID进行$condition数据更新<br/>
	 * 如果不传入数据$data且不存在id自动将这个Model的除id之外的其他字段属性作为更新数据<br/>
	 * @param array $condition 条件数组
	 * @param array $data 更新的数据数组
	 * @return boolean 是否更新成功
	 */
	public function update($condition,$data=null){
		DBDes::init_by_model($this,'U');
		if($data===null){
			if($this->has_id()){
				return $this->YYUCSYS_db->update_entity($this->YYUCSYS_real_tablename,array('id'=>$this->id),$condition);
			}else{
				return $this->YYUCSYS_db->update_entity($this->YYUCSYS_real_tablename,$condition,$this->get_model_array());
			}			
		}else{
			return $this->YYUCSYS_db->update_entity($this->YYUCSYS_real_tablename,$condition,$data);
		}		
	}
	/**
	 * 批量删除数据<br/>
	 * 如果不传入条件则自动将这个Model的除id之外的其他字段属性作为条件<br/>
	 * @param mixed $condition 条件数组或字串
	 * @param mixed $pam 参数数组
	 * @return  mixed 删除成功返回删除的条数 失败返回null
	 */
	public function delete($condition=null,$pam=null){
		DBDes::init_by_model($this,'D');
		if($condition===null){
			if($this->YYUCSYS_condition){
				return $this->YYUCSYS_db->del_entity($this->YYUCSYS_real_tablename,$this->YYUCSYS_condition);
			}else{
				return $this->YYUCSYS_db->del_entity($this->YYUCSYS_real_tablename,$this->get_model_array());
			}			
		}else{
			return $this->YYUCSYS_db->del_entity($this->YYUCSYS_real_tablename,$condition,$pam);
		}		
	}
	/**
	 * 获得模型属性信息的数组形式<br/>
	 * 只包含数据库中已有的字段 不包含ID信息(特殊指定$fields除外)
	 * 
	 * @param string|array $fields 需要特定指定的字段 
	 * @return array 模型的信息数组
	 */
	public function get_model_array($fields = null){
		$cons = array();
		if($fields === null){
			$cons = get_object_vars($this);
			unset($cons->id);
		}elseif (is_string($fields)){
			$cons = array($fields => $this->$fields);
		}else{
			foreach ($fields as $field){
				$cons[$field] = $this->$field;
			}
		}
		
		$cols =  $this->YYUCSYS_db->list_fields($this->YYUCSYS_real_tablename);
		$data = array();
		foreach ($cons as $k=>$v){
			if($v!==null && in_array($k, $cols)){
				$data[$k]=$v;
			}
		}
		return $data;
	}
	/**
	 * 获得模型属性信息的数组形式<br/>
	 * 只包含数据库中已有的字段 包含ID信息
	 * @return array 模型的信息数组
	 */
	public function get_model_array_with_id(){
		$cons = get_object_vars($this);
		$cols =  $this->YYUCSYS_db->list_fields($this->YYUCSYS_real_tablename);
		$data = array();
		foreach ($cons as $k=>$v){			
			if(in_array($k, $cols)){
				$data[$k]=$v;
			}
		}
		return $data;
	}
	
	/**
	 * 获得一个和此模型一模一样的克隆 <br/>
	 * 不克隆ID信息
	 * @return Model 新的模型
	 */
	public function get_a_clone(){
		$newmodel =  clone $this;
		$newmodel->id = null;
		return $newmodel;
	}
	
	/**
	 * 构造函数
	 * @param string $tablename 表名
	 * @param string $postid 表单提交的区分ID
	 */
	function __construct($tablename,$postid=''){
		if(get_class($this) == 'Model'){
			$this->YYUCSYS_isorgin = true;
		}else{
			$this->YYUCSYS_isorgin = false;
		}
		$this->YYUCSYS_postid = $postid;
		$this->YYUCSYS_post_id = strlen($postid)===32 ? $postid : md5('YYUC_'.$postid);
		$this->YYUCSYS_db = DB::get_db();
		$this->YYUCSYS_tablename = $tablename;
		$this->YYUCSYS_real_tablename = Conf::$db_tablePrefix.$tablename;
		//填充一些有默认值的字段和字段描述
		if(!isset(self::$YYUCSYS_FIELD_LABLE[$this->YYUCSYS_tablename])){
			self::$YYUCSYS_FIELD_LABLE[$this->YYUCSYS_tablename] = array();
			self::$YYUCSYS_FIELD_DATA[$this->YYUCSYS_tablename] = array();
			self::$YYUCSYS_FIELD_TYPE[$this->YYUCSYS_tablename] = array();
			self::$YYUCSYS_FIELD_DEFAULT[$this->YYUCSYS_tablename] = array();
			self::$YYUCSYS_FIELD_CANNULL[$this->YYUCSYS_tablename] = array();
			$defaults = $this->YYUCSYS_db->get_fields_array($this->YYUCSYS_real_tablename);
			foreach ($defaults as $default){
				if(trim($default['Default'])!=''){
					//存储默认值
					self::$YYUCSYS_FIELD_DEFAULT[$this->YYUCSYS_tablename][$default['Field']] = $default['Default'];
				}
				if(trim($default['Null'])=='NO'){
					//是否允许非空
					self::$YYUCSYS_FIELD_CANNULL[$this->YYUCSYS_tablename][$default['Field']] = false;
				}else{
					self::$YYUCSYS_FIELD_CANNULL[$this->YYUCSYS_tablename][$default['Field']] = true;
				}
				if(strpos($default['Type'], 'enum')===0){
					self::$YYUCSYS_FIELD_TYPE[$this->YYUCSYS_tablename][$default['Field']] = 'enum';
					//存储枚举类型的数据
					$dataarray = array();					
					$tempstr = substr($default['Type'], 6,strlen($default['Type'])-8);
					$arr1 = explode("','", $tempstr);
					$arr2 = explode(",", $default['Comment']);
					if(count($arr1)===count($arr2)){
						$temparr2 = explode(':', $arr2[0]);
						if(count($temparr2)>1){
							//存储该字段的lable
							self::$YYUCSYS_FIELD_LABLE[$this->YYUCSYS_tablename][$default['Field']] = $temparr2[0];
						}
						$arr2[0] = $temparr2[count($temparr2)-1];
						foreach ($arr1 as $k=>$v){
							$dataarray[$v] = $arr2[$k];
						}
					}else{
						foreach ($arr1 as $v){
							$dataarray[$v] = $v;
						}
						//存储该字段的lable
						self::$YYUCSYS_FIELD_LABLE[$this->YYUCSYS_tablename][$default['Field']] = $default['Comment'];
					}
					//存储该字段的候选数据
					self::$YYUCSYS_FIELD_DATA[$this->YYUCSYS_tablename][$default['Field']] = $dataarray;
				}else{
					//存储该字段的lable
					self::$YYUCSYS_FIELD_TYPE[$this->YYUCSYS_tablename][$default['Field']] = $default['Type'];
					self::$YYUCSYS_FIELD_LABLE[$this->YYUCSYS_tablename][$default['Field']] = $default['Comment'];
				}
			}
		}		
		//该表Model数据已经初始化过 给各个字段赋初始值
		$defaults = &self::$YYUCSYS_FIELD_DEFAULT[$this->YYUCSYS_tablename];
		foreach ($defaults as $field=>$default){
			$this->$field = $default;
		}
		//如果不是原始模型 增加各个字段的验证信息供页面form标签使用
		if(!$this->YYUCSYS_isorgin&&!isset(self::$YYUCSYS_FIELD_FORMVAL[$this->YYUCSYS_tablename])){
			self::$YYUCSYS_FIELD_FORMVAL[$this->YYUCSYS_tablename] = array();
			self::$YYUCSYS_first_valset[$this->YYUCSYS_tablename] = true;
			$this->validate();
			self::$YYUCSYS_first_valset[$this->YYUCSYS_tablename] = false;
		}
		//默认的field字段写入
		$fields = $this->YYUCSYS_db->list_fields($this->YYUCSYS_real_tablename);
		$this->YYUCSYS_select = '`'.implode('`,`', $fields).'`';
	}
	/**
	 * 获得某一字段的Lable<br/>
	 * 默认为数据库中定义的字段注释
	 * @param string $field 字段名称
	 * @return string 字段描述
	 */
	public function lable($field){
		$lable = self::$YYUCSYS_FIELD_LABLE[$this->YYUCSYS_tablename][$field];
		if(trim($lable)==''){
			return $field;
		}
		return $lable;
	}
	/**
	 * 获得某一字段的数据类型
	 * @param string $field 字段名称
	 * @return string 字段描述
	 */
	public function type($field){
		return self::$YYUCSYS_FIELD_TYPE[$this->YYUCSYS_tablename][$field];
	}
	/**
	 * 设置或者读取某一字段的初始化数据数组 <br/>
	 * $data为数组则设置字段的信息初始化数组<br/>
	 * $data为空则返回字段的信息初始化数组
	 * @param string $field 字段名称
	 * @param array $data 信息数组
	 * @return array 信息数组
	 */
	public function field_data($field,$data=null){
		if($data===null){
			return self::$YYUCSYS_FIELD_DATA[$this->YYUCSYS_tablename][$field];
		}else if(is_array($data)){
			self::$YYUCSYS_FIELD_DATA[$this->YYUCSYS_tablename][$field] = $data;
			return $data;
		}
	}
	/**
	 * 获得某一字段值对应的文本<br/>
	 * 返回针对enum类型字段的值对应的文本或者配置文件中配置的值所对应的文本
	 * @param string $field 字段名称
	 * @return string 字段值描述
	 */
	public function field_text($field){
		if(empty($field)){
			return null;
		}
		$data=$this->field_data($field);
		if(isset($data)){
			return $data[$this->$field];
		}else{
			return Page::$i18n['common'][$this->$field];
		}
	}
	/**
	 * 设置查询排序
	 * @param string $order 排序
	 * @return Model 模型本身
	 */
	public function order($order){
		$this->YYUCSYS_order = $order;
		return $this;
	}
	/**
	 * 设置查询分组
	 * @param string $group 分组
	 * @return Model 模型本身
	 */
	public function group($group){
		$this->YYUCSYS_group = $order;
		return $this;
	}
	/**
	 * 设置查询区间
	 * @param string $limit 区间
	 * @return Model 模型本身
	 */
	public function limit($limit,$limit2=null){
		if($limit2 !== null){
			$this->YYUCSYS_limit = $limit.','.$limit2;
		}else{
			$this->YYUCSYS_limit = $limit;
		}
		
		return $this;
	}
	/**
	 * 设置查询字段 如:"id,name"
	 * @param string $select 要检索的字段
	 * @return Model 模型本身
	 */
	public function field($select){
		$this->YYUCSYS_select = $select;
		return $this;
	}
	/**
	 * 字段的 唯一性验证
	 * @param string $field 字段名称
	 * @param string $errmsg 错误信息
	 */
	public function val_unique($field,$errmsg=null){
		if(!$this->set_field_required_string($field,'YYUCUNIQUE@'.$this->YYUCSYS_tablename.'@'.$field.'@YYUCSYSID', $errmsg===null?YYUC::i18n('validate.unique'):$errmsg)){
			$query_arr = array($field=>$this->$field);
			if(trim($this->id)!=''){
				$query_arr['id@<>'] = $this->id;
			}
			if($this->$field!=''&&$this->YYUCSYS_db->has($this->YYUCSYS_real_tablename,$query_arr)){
				$this->set_err_msg($field,$errmsg?$errmsg:YYUC::i18n('validate.unique'));
			}
		}
	}

	/**
	 * 将数据表的的两个字段的对应数据转换为键值数组形式
	 * @param string $field1 key
	 * @param string $field2 value
	 * @param array $res_arr 默认预置数组
	 * @return array 键值数组
	 */
	public function map_array($field1,$field2,$res_arr = array()){
		DBDes::init_by_model($this,'R');
		$field1 = strpos($field1, ' ')>0? trim($field1) : '`'.trim($field1).'`';
		$field2 = strpos($field2, ' ')>0? trim($field2) : '`'.trim($field2).'`';
		$sql = $this->YYUCSYS_db->com_sql($this->YYUCSYS_real_tablename,$this->YYUCSYS_condition,$this->YYUCSYS_order,$this->YYUCSYS_group,$this->YYUCSYS_limit,"distinct $field1 as k,$field2 as v",$this->YYUCSYS_pam);
		$res = $this->YYUCSYS_db->query($sql);
		foreach ($res as $arr){
			$res_arr[$arr['k']] = $arr['v'];
		}
		return $res_arr;
	}
	/**
	 * 将数据表的的一个字段的值和多个字段的键值对对应的数据转换为键值数组-Map的形式
	 * @param string $field1 key
	 * @param string $fields 要填充到Map的字段值
	 * @return array 一键多值数组
	 */
	public function map_array_kmap($field1,$fields='*'){
		DBDes::init_by_model($this,'R');
		$sql = $this->YYUCSYS_db->com_sql($this->YYUCSYS_real_tablename,$this->YYUCSYS_condition,$this->YYUCSYS_order,$this->YYUCSYS_group,$this->YYUCSYS_limit,"`$field1` as k,".$fields,$this->YYUCSYS_pam);
		$res = $this->YYUCSYS_db->query($sql);
		$res_arr = array();
		foreach ($res as $arr){
			$res_arr[$arr['k']] = $arr;
		}
		return $res_arr;
	}
	
	/**
	 * 将数据表的的一个字段的值作为键值 此键值对应一个二维数组 数组中存放符合该键值的所有行的数据
	 * @param string $field1 key
	 * @param array $farray 要填充到Map的Array(二级键值)
	 * @return array 一键多值数组
	 */
	public function map_array_allres($field1,$field='*'){
		if($field=='*'){
			$field = $this->YYUCSYS_real_tablename.'.*';
		}
		DBDes::init_by_model($this,'R');
		$sql = $this->YYUCSYS_db->com_sql($this->YYUCSYS_real_tablename,$this->YYUCSYS_condition,$this->YYUCSYS_order,$this->YYUCSYS_group,$this->YYUCSYS_limit,"`$field1` as k,".$field." ",$this->YYUCSYS_pam);
		$res = $this->YYUCSYS_db->query($sql);
		$res_arr = array();
		foreach ($res as $arr){
			if(!isset($res_arr[$arr['k']])){
				$res_arr[$arr['k']] = array();
			}
			$res_arr[$arr['k']][] = $arr;
		}
		return $res_arr;
	}
	/**
	 * 计算某一字段的和
	 * @param string $field 参数数组
	 * @return integer 计数
	 */
	public function sum($field=null){
		DBDes::init_by_model($this,'R');
		$sql = $this->YYUCSYS_db->com_sql($this->YYUCSYS_real_tablename,$this->YYUCSYS_condition,null,null,null,"sum($field) c",$this->YYUCSYS_pam);
		$res = $this->YYUCSYS_db->query($sql);
		return floatval($res[0]['c']);
	}
	/**
	 * 计算行数
	 * @param string $field 统计的参数（*）
	 * @return integer 计数
	 */
	public function count($field=null){
		if($field===null){
			$field = '*';
		}
		$sql = $this->YYUCSYS_db->com_sql($this->YYUCSYS_real_tablename,$this->YYUCSYS_condition,null,null,null,"count($field) c",$this->YYUCSYS_pam);
		DBDes::init_by_model($this,'R');
		$res = $this->YYUCSYS_db->query($sql);
		return intval($res[0]['c']);
	}
	/**
	 * 查询并返回某字段的最大值<br/>
	 * 如果$condition为数组则根据数组条件返回符合结果的列表<br/>
	 * 如果$condition是字串则必须是 where语句之后的字串，亦可通过?和$pam数组组合成SQL语句<br/>
	 * 如果不传入条件则根据where方法 的预设参数查询,如果where未被调用过则列出所有<br/>
	 * @param string $field 要查询 的字段
	 * @param miexd $condition 条件字符串或条件数组
	 * @param miexd $pam 参数数组
	 * @return integer 最大值
	 */
	public function max($field,$condition=null,$pam=null){
		DBDes::init_by_model($this,'R');
		if($condition===null){
			$sql = $this->YYUCSYS_db->com_sql($this->YYUCSYS_real_tablename,$this->YYUCSYS_condition,null,null,null,"max(`$field`) as c",$this->YYUCSYS_pam);
		}else{
			$sql = $this->YYUCSYS_db->com_sql($this->YYUCSYS_real_tablename,$condition,null,null,null,"max(`$field`) as c",$pam);
		}
		$res = $this->YYUCSYS_db->query($sql);
		if(count($res)>0){
			return intval($res[0]['c']);
		}
		return 0;
	}
	
	/**
	 * 查询并返回某字段的最小值<br/>
	 * 如果$condition为数组则根据数组条件返回符合结果的列表<br/>
	 * 如果$condition是字串则必须是 where语句之后的字串，亦可通过?和$pam数组组合成SQL语句<br/>
	 * 如果不传入条件则根据where方法 的预设参数查询,如果where未被调用过则列出所有<br/>
	 * @param string $field 要查询 的字段
	 * @param miexd $condition 条件字符串或条件数组
	 * @param miexd $pam 参数数组
	 * @return integer 最小值
	 */
	public function min($field,$condition=null,$pam=null){
		DBDes::init_by_model($this,'R');
		if($condition===null){
			$sql = $this->YYUCSYS_db->com_sql($this->YYUCSYS_real_tablename,$this->YYUCSYS_condition,null,null,null,"min(`$field`) c",$this->YYUCSYS_pam);
		}else{
			$sql = $this->YYUCSYS_db->com_sql($this->YYUCSYS_real_tablename,$condition,null,null,null,"min(`$field`) c",$pam);
		}
		$res = $this->YYUCSYS_db->query($sql);
		if(count($res)>0){
			return intval($res[0]['c']);
		}
		return 0;
	}
	
	/**
	 * 查询并返回某字段的平均值<br/>
	 * 如果$condition为数组则根据数组条件返回符合结果的列表<br/>
	 * 如果$condition是字串则必须是 where语句之后的字串，亦可通过?和$pam数组组合成SQL语句<br/>
	 * 如果不传入条件则根据where方法 的预设参数查询,如果where未被调用过则列出所有<br/>
	 * @param string $field 要查询 的字段
	 * @param miexd $condition 条件字符串或条件数组
	 * @param miexd $pam 参数数组
	 * @return integer 平均值
	 */
	public function avg($field,$condition=null,$pam=null){
		if($condition===null){
			$sql = $this->YYUCSYS_db->com_sql($this->YYUCSYS_real_tablename,$this->YYUCSYS_condition,null,null,null,"avg(`$field`) c",$this->YYUCSYS_pam);
		}else{
			$sql = $this->YYUCSYS_db->com_sql($this->YYUCSYS_real_tablename,$condition,null,null,null,"avg(`$field`) c",$pam);
		}
		DBDes::init_by_model($this,'R');
		$res = $this->YYUCSYS_db->query($sql);
		if(count($res)>0){
			return intval($res[0]['c']);
		}
		return 0;
	}
	
	/**
	 * 查询是否含有符合条件的数据<br/>
	 * 如果$condition为数组则根据数组条件返回符合结果的列表<br/>
	 * 如果$condition是字串则必须是 where语句之后的字串，亦可通过?和$pam数组组合成SQL语句<br/>
	 * 如果不传入条件则根据where方法 的预设参数查询,如果where未被调用过则列出所有<br/>
	 * @param miexd $condition 条件字符串或条件数组
	 * @param miexd $pam 参数数组
	 * @return boolean
	 */
	public function has($condition=null,$pam=null){
		if($condition===null){
			$sql = $this->YYUCSYS_db->com_sql($this->YYUCSYS_real_tablename,$this->YYUCSYS_condition,null,null,null,"1",$this->YYUCSYS_pam);
		}else{
			$sql = $this->YYUCSYS_db->com_sql($this->YYUCSYS_real_tablename,$condition,null,null,null,"1",$pam);
		}
		DBDes::init_by_model($this,'R');
		$res = $this->YYUCSYS_db->query($sql);
		return count($res)>0;	
	}
	
	/**
	 * 判断当前model是否在数据库中存在真实的对应
	 * 
	 * @param string|array $fields 需要特定指定的字段
	 * @param boolean $fillme 是否填充当前model 默认:true
	 * @return boolean
	 */
	public function is_real($fields = null, $fillme = true){
		$conditions = null;
		if($fields === null){
			$conditions = $this->get_model_array_with_id();
		}else{
			$conditions = $this->get_model_array($fields);
			if(count($fields) != count($conditions)){
				return false;
			}
		}
		//注意还原YYUCSYS_select
		$oldfield = $this->YYUCSYS_select;
		$ms = $this->field('*')->list_all($conditions);
		$this->field($oldfield);
		if(count($ms) > 0){
			if($fillme){
				$this->find($ms[0]->id);
			}
			return true;
		}
		return false;
	}
}
?>