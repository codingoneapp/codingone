<?php
class DB{
	/**db链接*/
	public $conn = null;
	public $host = null;
	public $port = null;
	public $username = null;
	public $password = null;
	public $dbname = null;
	/**事务*/
	public $autocommit = true;
	public $hascommit = false;
	
	/**
	 * 单例DB引用
	 * 
	 * @var DB
	 */
	public static $db = null;
	
	/**
	 * 数据库操作描述
	 * 
	 * @var DBDes
	 */
	public static $dbdes = null;
	/**db多库*/
	public static $is_main_sub = false;
	public static $current_db = null;
	/**此次查询是否是CUD操作*/
	public static $is_cud = false;
	/**
	 * 单列模式获得数据库连接类<br/>
	 * 如果人为指定连接名称则DB类会按照多库规则处理,不会自动切换数据库连接<br/>
	 * @param string $conn_name 多库并存的情况下指明要获得连接的名称<br/>传入false则锁定到主配数据库<br/>传入null切换主配数据库恢复到主从模式<br/>传入true时随机切换到从属数据库
	 * @return DB
	 */
	public static function get_db($conn_name=true){
		if(empty(Conf::$db_boys)){
			$conn_name = false;
		}
		if(self::$db === null){
			self::$db = new DB();
			self::$db->to_connect($conn_name);
		}else{
			//特定指定的数据库
			if(is_string($conn_name)){
				if($conn_name===self::$current_db){
					self::$is_main_sub = false;
				}				
			}elseif($conn_name===true){
				//要切换到从属数据库
				if(is_string(self::$current_db)){
					//已经选择了从属数据库就不再选了
					self::$is_main_sub = true;
					return self::$db;
				}else{
					self::$db->to_connect($conn_name);
				}
			}elseif($conn_name===false){
				//要锁定到主数据库
				if(!is_string(self::$current_db)){
					//已经选择了从属数据库就不再选了
					self::$is_main_sub = false;
					return self::$db;
				}else{
					self::$db->to_connect($conn_name);
				}
			}else{
				//恢复到默认
				if(!is_string(self::$current_db)){
					//已经选择了从属数据库就不再选了
					self::$is_main_sub = true;
					return self::$db;
				}else{
					self::$db->to_connect($conn_name);
				}
			}
		}
		return self::$db;
	}
	
	/**
	 * 数据库连接测试<br/>
	 * @param string $host 连接地址
	 * @param string $port 连接端口
	 * @param string $username 连接用户名
	 * @param string $password 连接密码
	 * @param string $dbname 默认数据库名称
	 * @return mixed <br/>成功返回true<br/>失败返回失败原因
	 */
	public static function test_conn($host,$port,$username,$password,$dbname = null){
		$link = @mssql_connect($host.':'.$port, $username, $password);
		if (!$link){
			return '连接测试失败';
		}
		if($dbname == null){
			return true;
		}
		$db_list = mssql_list_dbs($link);
		while ($row = mssql_fetch_object($db_list)) {
			if($row->Database === $dbname){
				return true;
			}
		}
		return '连接成功，数据库"'.$dbname.'"不存在';
	}

	/**
	 * 数据库操作类初始化<br/>
	 * @param string $host 连接地址
	 * @param string $port 连接端口
	 * @param string $username 连接用户名
	 * @param string $password 连接密码
	 * @param string $dbname 默认数据库名称
	 */
	public function init($host,$port,$username,$password,$dbname){
		$this->host =  $host;
		$this->port =  $port;
		$this->username = $username;
		$this->password = $password;
		$this->dbname = $dbname;
		
		if (!$this->conn=mssql_connect($host,$username,$password)){
			die('<center>MSSQL链接建立失败,请进入<a href="'.Conf::$http_path.Conf::$management_center_target.'">开发管理中心</a>配置数据库连接</center>');
		}
		mssql_select_db($dbname, $this->conn);
		
		mssql_query("SET ANSI_WARNINGS ON",$this->conn);		
		mssql_query("SET ANSI_NULLS ON",$this->conn);
		//mssql_query("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=utf8", $this->conn);
		//mssql_query("SET NAMES 'utf8'");
		//mssql_query("set charset utf8");
	}
	
	/**
	 * 根据配置文件初始化数据库连接类<br/>
	 * 开发中请用get_db方法获取
	 * @param string $conn_name 多库并存的情况下指明要获得连接的名称<br/>传入false则锁定到主配数据库<br/>传入null恢复到主从模式<br/>传入true时随机切换到从属数据库
	 */
	function to_connect($conn_name=null){
		if(self::$db!==null){
			if(!self::$db->autocommit && self::$db->hascommit===false){
				die('当前数据库('.(is_string(self::$current_db) ? self::$current_db : '主数据库').')事务还未提交或回滚,无法进行库切换，请检查程序源码');
			}
		}
		if($conn_name===null||$conn_name===false){
			self::$is_main_sub = (!empty(Conf::$db_boys)&&$conn_name===null)?  true: false;
			self::$current_db = $conn_name;
			$this->init(Conf::$db_host,Conf::$db_port,Conf::$db_username,Conf::$db_password,Conf::$db_dbname);
			Log::debug('切换到主数据库');			
		}else{
			$pz_arr = null;			
			if($conn_name===true){
				self::$current_db = array_rand(Conf::$db_boys);
				$pz_arr = Conf::$db_boys[self::$current_db];
				self::$is_main_sub = true;
			}else{
				self::$current_db = $conn_name;
				$pz_arr = Conf::$db_boys[$conn_name];
				self::$is_main_sub = false;
			}			
			$this->init($pz_arr['host'],$pz_arr['port'],$pz_arr['username'],$pz_arr['password'],$pz_arr['dbname']);
			Log::debug('切换到数据库:'.self::$current_db);
		}		
	}	
	/**
	 * 开启事务
	 */
	public function begin_transaction(){
		$this->base_query('START TRANSACTION');
		$this->autocommit = false;
	}
	
	/**
	 * 事务回滚
	 */
	public function rollback(){
		$this->base_query('ROLLBACK');
		$this->hascommit = null;
	}
	
	/**
	 * 事务提交
	 */
	public function commit(){
		$this->base_query('COMMIT');
		$this->hascommit = true;
	}
	
	/**
	 * 获得原生的SQL字符串到参数中 不进行任何的SQL字符转移
	 * 
	 * @param string sql
	 * @return string
	 */
	public static function raw($sql){
		return '@yYUc_RaW@'.$sql;
	}
	/**
	 *基于MySql的底层查询封装<br/>
	 * @param string $sql 要执行查询的SQL语句参数请用"?"代替
	 * @param array $pam 数字下标的数组，数组项依次替换SQL中的"?"参数
	 */
	public function base_query($sql,$pam=null) {
		if(is_array($sql)){
			return $this->base_query($sql[0],$sql[1]);
		}else if(!isset($pam)){
			//DB自动切换
			if(self::$is_main_sub){
				//如果是主从模式
				if(self::$is_cud && self::$current_db!==null){
					//如过是CUD操作
					self::get_db(null);
				}elseif (!self::$is_cud && self::$current_db===null){
					self::get_db(true);
				}
			}
			if(Conf::$need_db_log){
				//开发模式下记录db日志				
				file_put_contents(YYUC_FRAME_PATH.'/log/db/'.date('Y_m_d_H',time()).'.log', date('i:s',time()).','.self::$current_db.': '.$sql."\n",FILE_APPEND);
			}
			//调试日志
			if(Conf::$need_debug_log){
				Debug::log_sql_begin(self::$current_db.':'.$sql);
			}
			//数据验证的执行
			if(self::$dbdes  !== null){
				self::$dbdes->db = $this;
				DBDes::init_by_sql($sql);
				if(self::$dbdes->operate !='R'){
					Cache::set('YYUC_TABLE_TIME'.self::$dbdes->real_table,time());
				}
				if(is_callable('db_validations')){
					db_validations(clone self::$dbdes);
				}				
				self::$dbdes = null;
			}
			$this->statement = null;			
			
			$this->statement = mssql_query(iconv("utf-8","gbk",$sql), $this->conn);
			if(Conf::$need_debug_log){
				Debug::log_sql_end();
			}
			if (!$this->statement){
				Log::error("执行出错：".$sql);
			}
			return $this->statement;
		}else{
			 if(is_string($pam)){//参数为字符串类型
			 	$sql = str_replace('@yYUc_SqL@',"'".str_replace("'","''",$pam)."'",$sql);
			 }else if(is_array($pam)){//单纯数组
			 	$sqlss = explode('@yYUc_SqL@', $sql);
			 	$sqllen = count($pam);
			 	$sql = '';
			 	for($i=0;$i<$sqllen;$i++){
			 		if($pam[$i]===null){
			 			$sql.=($sqlss[$i]."null");
			 		}elseif(strpos($pam[$i], '@yYUc_RaW@') === 0){
			 			$sql.=($sqlss[$i].substr($pam[$i], 10));
			 		}else{
			 			$sql.=($sqlss[$i]."'".str_replace("'","''",$pam[$i])."'");
			 		}
			 	}
			 	$sql.= $sqlss[$sqllen];
			 }
			 return $this->base_query($sql);
		}
	}
	
	/**
	 * 执行无结果查询(增，删，改)<br/>
	 * @param string $sql 要执行查询的SQL语句参数请用"?"代替
	 * @param array $pam 数字下标的数组，数组项依次替换SQL中的"?"参数
	 */
	function execute($sql,$pam=null){
		self::$is_cud = true;
		if(self::$dbdes ==null){
			self::$dbdes = new DBDes();
		}
		if(is_string($sql)){
			$sql = str_replace('?', '@yYUc_SqL@', $sql);
		}
		$res =  $this->base_query($sql,$pam);
		self::$is_cud = false;
		
		return $res;
	}
	
	/**
	 * 普通SQL查询
	 * @param string $sql 要执行查询的SQL语句参数请用"?"代替
	 * @param array $pam 数字下标的数组，数组项依次替换SQL中的"?"参数
	 * @return array 字符下标的数组集合
	 */
	function query($sql,$pam=null,$limit1=0,$limit2=9999,$callback = null){
		$res = array();
		if(is_string($sql)){
			$sql = str_replace('?', '@yYUc_SqL@', $sql);
		}
		$query = $this->base_query($sql,$pam);
		if(self::$dbdes ==null){
			self::$dbdes = new DBDes();
			self::$dbdes->operate = 'R';
		}
		$ks = -1;
		$zs = $limit1+$limit2;
		if(is_callable($callback)){
			while($tn=mssql_fetch_assoc($query)){
				$ks++;
				if($ks >=$limit1 && $ks<$zs){
					call_user_func_array($callback,array($this->changecode($tn)));
				}elseif($ks<$limit1){
					continue;
				}else{
					break;
				}		
			}
		}else{
			while($tn=mssql_fetch_assoc($query)){
				$ks++;
				if($ks >=$limit1 && $ks<$zs){
					$res[] = $this->changecode($tn);
				}elseif($ks<$limit1){
					continue;
				}else{
					break;
				}
			}
			return $res;
		}	
	}
	function changecode($inp){
		if(is_array($inp)){
			foreach ($inp as $k=>$v){
				$inp[$k] = iconv("gbk","utf-8",$v);
			}
			return $inp;
		}else{
			return iconv("gbk","utf-8",$inp);
		}
	}
	
	
	/**
	 * 普通SQL查询
	 * @param string $sql 要执行查询的SQL语句参数请用"?"代替
	 * @param array $pam 数字下标的数组，数组项依次替换SQL中的"?"参数
	 * @return array<model> 模型集合
	 */
	function query_model($sql,$pam=null,$limit1=0,$limit2=9999,$callback = null){
		$res = array();
		if(is_string($sql)){
			$sql = str_replace('?', '@yYUc_SqL@', $sql);
		}
		$query = $this->base_query($sql,$pam);
		if(self::$dbdes ==null){
			self::$dbdes = new DBDes();
			self::$dbdes->operate = 'R';
		}
		$ks = -1;
		$zs = $limit1+$limit2;
		if(is_callable($callback)){
			while($tn=mssql_fetch_assoc($query)){
				$ks++;
				if($ks >=$limit1 && $ks<$zs){
					call_user_func_array($callback,array((object)$this->changecode($tn)));
				}elseif($ks<$limit1){
					continue;
				}else{
					break;
				}
				
			}
		}else{
			while($tn=mssql_fetch_assoc($query)){
				$ks++;
				if($ks >=$limit1 && $ks<$zs){
					$res[] = (object)$this->changecode($tn);
				}elseif($ks<$limit1){
					continue;
				}else{
					break;
				}				
			}
			return $res;
		}
	}
	/**
	 * 单列数据和集合查询
	 * @param string $sql 要执行查询的SQL语句参数请用"?"代替
	 * @param array $pam 数字下标的数组，数组项依次替换SQL中的"?"参数
	 * @return array 某一数据字段结果集合
	 */
	function one_column_array($sql,$pam=null){
		$query = $this->base_query($sql,$pam);
		$res = array();
		while($tn=mssql_fetch_row($query)){
			$res[] = $tn[0];
		}		
		if(count($res)>0){
			return $res;
		}
		return null;
	}
	

	/**
	 * 查询并返回Model实体类的集合
	 * @param Model $model 数据模型
	 * @param mixed $condition 查询条件
	 * @param string $order 排序方式
	 * @param string $limit 查询范围
	 * @param string $field 查询字段
	 * @param array $pam 数字下标的数组，数组项依次替换$condition中的"?"参数
	 * @return array Model实体类的集合
	 */
	function fill_models($model,$condition=null,$order=null,$limit=null,$field='*',$pam=null){	
		$sql = $this->com_sql($model->YYUCSYS_real_tablename,$condition,$order,$limit,$field,$pam);
		$res = array();
		$query = $this->base_query($sql);
		$modelname = $model->YYUCSYS_tablename;
		if(!$model->YYUCSYS_isorgin){//如果是用户的扩展类
			while($tn=mssql_fetch_assoc($query)){
				$newmodel = new $modelname();
				foreach ($tn as $k=>$v){
					$newmodel->$k = $this->changecode($v);
				}
				$newmodel->fill_virtual_field();
				$res[] = $newmodel;
			}
		}else{
			while($tn=mssql_fetch_assoc($query)){
				$newmodel = new Model($modelname);
				foreach ($tn as $k=>$v){
					$newmodel->$k = $this->changecode($v);
				}
				$res[] = $newmodel;
			}
		}		
		return $res;	
	}
	
	/**
	 * 查询并返回Model实体类的字符下标的数组集合
	 * @param Model $model 数据模型
	 * @param mixed $condition 查询条件
	 * @param string $order 排序方式
	 * @param string $limit 查询范围
	 * @param string $field 查询字段
	 * @param array $pam 数字下标的数组，数组项依次替换$condition中的"?"参数
	 * @return array 字符下标的数组集合
	 */
	function fill_models_array($model,$condition=null,$order=null,$limit=null,$field='*',$pam=null){	
		$sql = $this->com_sql($model->YYUCSYS_real_tablename,$condition,$order,$limit,$field,$pam);
		return $query = $this->query($sql);
	}
	
	
	/**
	 * 填充这个Model实体
	 * @param Model $model 要填充数据模型
	 * @param mixed $condition 查询条件
	 * @param array $pam 数字下标的数组，数组项依次替换$condition中的"?"参数
	 * @return boolean 是否填充成功
	 */
	function fill_one_model(& $model,$condition,$pam=null,$ord=null,$limit=null){
		$sql = $this->com_sql($model->YYUCSYS_real_tablename,$condition,$ord,$limit,$model->YYUCSYS_select,$pam);
		$query = $this->base_query($sql);
		if($tn=mssql_fetch_assoc($query)){
			foreach ($tn as $k=>$v){
				$model->$k = $this->changecode($v);
			}
			return true;
		}
		return false;
	}
	/**
	 * 向数据库表中添加一条数据
	 * @param string $table 表名
	 * @param array $data 要添加的数据，字符下标的数组形式
	 * @return integer 成功插入返回插入ID，插入失败返回null
	 */
	function insert_one_entity($table,$data=array(),$forceinsert = false){		
		$ssql = "insert into [$table] (";
		$vars = array();
		$vals = array();
		$pres = array();
		$columns = $this->list_fields($table);
		if(!$forceinsert){
			unset($data['id']);
		}		
		foreach ($data as $k=>$val){
			if(in_array($k,$columns)){
				$vars[] = "[$k]";
				$vals[] = $val === null ? self::raw('null'):$val;
				$pres[] = '@yYUc_SqL@';
			}
		}
		$ssql.=(implode(",", $vars).") values (".implode(",", $pres).")");
		if($this->execute($ssql,$vals)){
			if($forceinsert){
				return $data['id'];
			}else{
					$res = mssql_query("SELECT @@identity AS id" ,$this->conn);
					if ($row = mssql_fetch_assoc($res)) {
						return $row["id"];
					}
			}			
		}
		return null;
	}
	/**
	 * 更新数据
	 * @param string $table 表名
	 * @param mixed $condition 要更新的where条件或者字符下标的条件数组
	 * @param array $data 要更新的数据，字符下标的数组形式
	 * @param array $pam 数字下标的数组，数组项依次替换$condition中的"?"参数
	 * @return integer 成功返回受影响的行数，更新失败返回null
	 */
	function update_entity($table,$condition=null,$data=array(),$pam=null){
		$ssql = "update [$table] set ";
		$vars = array();
		$vals = array();
		$columns = $this->list_fields($table);
		foreach ($data as $k=>$val){
			if(in_array($k,$columns)){
				$vars[] = "[$k]=@yYUc_SqL@ ";
				$vals[] = $val === null ? self::raw('null'):$val;;
			}
		}
		$ssql.=implode(",", $vars);
		
		if(!empty($condition)){
			if(is_string($condition)){
				$condition = str_replace('?', '@yYUc_SqL@', $condition);
			}
			$whereres = $this->con_sql($condition,$pam);
			$ssql .= trim($whereres[0]);
			$pam = $whereres[1];
			if($pam!==null){
				$vals = array_merge($vals, $pam);
			}			
		}
		if($this->execute($ssql,$vals)){
			return mssql_rows_affected($this->conn);
		}
		return null;
	}
	/**
	 * 根据条件删除
	 * @param string $table 表名
	 * @param mixed $condition 要更新的where条件或者字符下标的条件数组
	 * @param array $pam 数字下标的数组，数组项依次替换$condition中的"?"参数
	 * @return integer 成功返回受影响的行数，更新失败返回null
	 */
	function del_entity($table,$condition=null,$pam=null){
		$ssql = "delete from [$table] ";
		if(!empty($condition)){
			if(is_string($condition)){
				$condition = str_replace('?', '@yYUc_SqL@', $condition);
			}
			$whereres = $this->con_sql($condition,$pam);
			$ssql .= trim($whereres[0]);
			$pam = $whereres[1];		
		}
		if($this->execute($ssql,$pam)){
			$res = mssql_query("SELECT @@identity AS id" ,$this->conn);
			if ($row = mssql_fetch_assoc($res)) {
				return $row["id"];
			}
		}
		return null;
	}
	/**
	 * 获得数据库表名称集合
	 * @return array 数据库表名称集合
	 */
	function list_tables(){
		return $this->one_column_array('SHOW TABLES FROM '.Conf::$db_dbname);
	}
	/**
	 * 判断表中是否含有符合条件的数据
	 * @param string $table 表名
	 * @param mixed $condition 要更新的where条件或者字符下标的条件数组
	 * @param array $pam 数字下标的数组，数组项依次替换$condition中的"?"参数
	 * @return boolean 含有返回true不含有返回false
	 */
	function has($table,$condition=null,$pam=null){
		$sql = $this->com_sql($table,$condition,null,null,'id',$pam);
		return !(count($this->query($sql))===0);
	}

	static $tablefieldsarray = array();
	/**
	 * 获得数据库表字段综合信息数组<br/>
	 * 成功获得后将存入 静态变量中 作为数据缓冲
	 * @param string $table 表名
	 * @return array 数据表的详细字段信息集合
	 */
	function get_fields_array($table){
		if(empty(self::$tablefieldsarray[$table])){
			self::$tablefieldsarray[$table] = $this->query("SELECT a.colorder as IIID,a.name as [Field],b.name as [Type],case a.isnullable when 1 then 'YES'else 'NO' end as [Null],e.text as [Default],'' as [Comment] from syscolumns a left join systypes b on a.xtype=b.xusertype inner join sysobjects d on a.id=d.id and (d.xtype='U' or d.xtype='V') and d.name<>'dtproperties' left join syscomments e on a.cdefault=e.id where d.name='$table' order by a.colorder");
		}
		return self::$tablefieldsarray[$table];
	}
	
	static $tablefields = array();
	/**
	 * 获得数据库表字段名称一维数组<br/>
	 * 成功获得后将存入 静态变量中 作为数据缓冲
	 * @param $table 表名
	 * @return array 数据表的字段名称集合
	 */	
	function list_fields($table){
		if(empty(self::$tablefields[$table])){
			$all_field = $this->get_fields_array($table);
			$res = array();
			foreach ($all_field as $af){
				$res[] = $af['Field'];
			}
			self::$tablefields[$table] = $res;
		}
		return self::$tablefields[$table];
	}
	
	///////////////////////以下为通用方法
	/**
	 * 根据键值组合条件
	 */
	private function _con_or_sql($ord,$val,&$pam){
		$vars = array();
		foreach ($val as $k=>$v){
			if($v===true){
				$vars[] = "[$k] is not null";
			}elseif($v===null){
				$vars[] = "[$k] is null";
			}elseif(is_array($v)){
				//如果是数组 结合OR的形式
				if(isset($v[0])){
					//数字索引数组
					$temporval = array();
					foreach ($v as $vvv){
						$temporval[] = "[$k]=@yYUc_SqL@";
						$pam[] = $vvv;
					}
					$vars[] = '('.implode(" or ", $temporval).')';
				}else{
					$vars[]  = $this->_con_or_sql($k,$v,$pam);
				}				
			}elseif(strpos($k, '@')!==false){
				$ks = explode('@', $k);
				if($ks[1]=='~'){
					$vars[] = "[".$ks[0]."] like @yYUc_SqL@";
					$pam[] = '%'.$v.'%';
				}elseif($ks[1]=='|~'){
					$vars[] = "[".$ks[0]."] like @yYUc_SqL@";
					$pam[] = $v.'%';
				}elseif($ks[1]=='~|'){
					$vars[] = "[".$ks[0]."] like @yYUc_SqL@";
					$pam[] = '%'.$v;
				}else{
					$vars[] = "[".$ks[0]."] ".$ks[1]." @yYUc_SqL@";
					$pam[] = $v;
				}
			}else{
				$vars[] = "[$k] = @yYUc_SqL@";
				$pam[] = $v;
			}
		}
		return '('.implode(" ".$ord." ", $vars).')';
	}
	
	/**
	 * 并且关系的条件语句组合
	 * @param mix $where 如过是数组形式如下<br/>
	 * array('un'=>'mqq','up@~'=>'mqq','uc@|~'=>'mqq','uc@~|'=>'mqq','age@>'=>'13','bz'=>array(....),'OR'=>array('a'=>'1','b'=>'2'))
	 * @param array $pam 数字下标的数组，数组项依次替换$where中的"?"参数
	 * @return array 拼接好的SQL条件[0]为语句 [1]为$pam参数
	 */
	public function con_sql($where=null,$pam=null){
		if($where===null){
			return array(null,null);
		}
		$sql = '';	
		if(is_string($where)){
			$sql .= ' where '.$where;
		}else if (is_array($where)||!empty($where)){
			$vars = array();
			$vals = array();
			foreach ($where as $k=>$val){
				if($val===true){
					$vars[] = "[$k] is not null";
				}elseif($val===null){
					$vars[] = "[$k] is null";
				}elseif(is_array($val)){
					//如果是数组 结合OR的形式
					if(isset($val[0])){
						//数字索引数组
						$temporval = array();
						foreach ($val as $vvv){
							$temporval[] = "[$k]=@yYUc_SqL@";
							$vals[] = $vvv;
						}
						$vars[] = '('.implode(" or ", $temporval).')';
					}else{
						$vars[]  = $this->_con_or_sql($k,$val,$vals);
					}					
				}elseif(strpos($k, '@')!==false){					
					$ks = explode('@', $k);
					if($ks[1]=='~'){
						$vars[] = "[".$ks[0]."] like @yYUc_SqL@";
						$vals[] = '%'.$val.'%';
					}elseif($ks[1]=='|~'){
						$vars[] = "[".$ks[0]."] like @yYUc_SqL@";
						$vals[] = $val.'%';
					}elseif($ks[1]=='~|'){
						$vars[] = "[".$ks[0]."] like @yYUc_SqL@";
						$vals[] = '%'.$val;
					}else{
						$vars[] = "[".$ks[0]."] ".$ks[1]." @yYUc_SqL@";
						$vals[] = $val;
					}					
				}else{
					$vars[] = "[$k]=@yYUc_SqL@";
					$vals[] = $val;
				}				
				
			}
			$sql.=(" where ".implode(" and ", $vars));
			$pam = $vals;
		}
		return array($sql,$pam);
	}

	/**
	 * 组合单表的SQL查询语句
	 * @param string $table 表名
	 * @param mixed $where 查询条件
	 * @param string $order 排序方式
	 * @param string $limit 查询范围
	 * @param string $field 查询字段
	 * @param array $pam 数字下标的数组，数组项依次替换$condition中的"?"参数
	 */
	public function com_sql($table,$where=null,$order=null,$limit=null,$field='*',$pam=null){
		$twhere = '';
		if(!empty($where)){
			if(is_string($where)){
				$where = str_replace('?', '@yYUc_SqL@', $where);
			}
			$whereres = $this->con_sql($where,$pam);
			$twhere = $whereres[0];
			$pam = $whereres[1];
		}
		$ord = '';
		if(!empty($order)){
			$ord .= ' order by '.$order;
		}
		$sql = "select $field from [$table] ".$twhere .$ord;
		
		if(!empty($limit)){
			$fl1 = 0;
			$fl2 = 10;
			if(strpos($limit, ',')!==false){
				$limit = explode(',', $limit);
				$fl1 = intval(trim($limit[0]));
				$fl2 = intval(trim($limit[1]));
			}else{
				$fl2 = intval(trim($limit));
			}
			$sql = "SELECT TOP $fl2  $field FROM [$table] WHERE (id NOT IN	(SELECT TOP $fl1 id	FROM [$table] $twhere $ord)) ".str_replace(' where ', ' and ', $twhere)." $ord";
			if(!empty($pam)){
				$pam = array_merge($pam, $pam);
			}
		}
		
		
		return array($sql,$pam);
	}
	/**
	 * 获得数据库版本
	 * @return string 版本号
	 */
	function version(){
		return mssql_get_server_info();
	}
}
?>