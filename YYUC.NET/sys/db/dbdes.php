<?php
/**
 * 数据库操作描述
 * @author mqq
 *
 */
class DBDes{
	
	/**
	 * 执行的db连接
	 *
	 * @var DB
	 */
	public $db = null;
	/**
	 * 表名
	 * 
	 * @var string
	 */
	public $table = null;
	
	/**
	 * 真实表名
	 *
	 * @var string
	 */
	public $real_table = null;
	
	/**
	 * 数据模型
	 * 
	 * @var Model
	 */
	public $model = null;
	
	/**
	 * 所执行的SQL
	 *
	 * @var string
	 */
	public $sql = null;
	
	/**
	 * 所执行的操作 C R U D
	 *
	 * @var string
	 */
	public $operate = null;
	
	/**
	 * 根据SQL初始话操作类
	 */
	public static function init_by_sql($sql){
		if(DB::$dbdes === null){
			DB::$dbdes = new DBDes();
		}
		DB::$dbdes->sql = trim($sql);
		if(DB::$dbdes->operate === null){
			if(stripos(DB::$dbdes->sql, 'insert')===0){
				DB::$dbdes->operate = 'C';
			}elseif(stripos(DB::$dbdes->sql, 'delete')===0){
				DB::$dbdes->operate = 'D';
			}elseif(stripos(DB::$dbdes->sql, 'update')===0){
				DB::$dbdes->operate = 'U';
			}elseif(stripos(DB::$dbdes->sql, 'select')===0){
				DB::$dbdes->operate = 'R';
			}
		}
		if(DB::$dbdes->real_table === null && DB::$dbdes->operate != 'R'){
			$sql_arr = explode(' ', DB::$dbdes->sql);
			$ind = 0;
			foreach ($sql_arr as $tem){
				$ttn = strtolower(trim($tem));
				if($ind > 0 && $ttn !='' && $ttn != 'into' && $ttn != 'from'){
					DB::$dbdes->real_table = str_replace('`','', $ttn);
					break;
				}
				$ind++;
			}
		}
	}
	
	/**
	 * 
	 * 根据model初始话操作类
	 *
	 * @param Model $m
	 * @param string $opt
	 */
	public static function init_by_model($m, $opt =null){
		if(DB::$dbdes === null){
			DB::$dbdes = new DBDes();
		}
		DB::$dbdes->model = $m;
		DB::$dbdes->real_table = $m->YYUCSYS_real_tablename;
		DB::$dbdes->table = $m->YYUCSYS_tablename;
		DB::$dbdes->operate = $opt;
	}
}