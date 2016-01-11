<?php
/*
 *
 *简单的一个备份数据类
 *author FC
 *
 */
class DBDataBackup{
	public $mysql_link = null;
	/**每个备份文件的记录条数*/
	public $onefile_num = 500;
	public $dbName = null;
	public function __construct($mysql_link,$dbname){
		$this->mysql_link = $mysql_link;
		$this->dbName = $dbname;
		@mysql_select_db($dbname, $mysql_link);
	}
	
	public function backupTables($dbName,$dataDir,$justStruts = false,$tableNames = array('@all')){//开始备份		
		$tables=$this->_delarray($tableNames);
		foreach($tables as $tablename){
			if(trim($tablename)==''){//表不存在时
				continue;
			}
			//************************以下是形成SQL的前半部分**************
			//如果存在表，就先删除
			$sqls = "DROP TABLE IF EXISTS `$tablename`;\n--\n";
			//读取表结构
			$rs = mysql_query("SHOW CREATE TABLE `$tablename`",$this->mysql_link);
			$row=mysql_fetch_row($rs);
			//获得表结构组成SQL
			$sqls.=$row['1'].";\n--\n";
			unset($rs);
			unset($row);
			if(Conf::$db_tablePrefix!=''){
				$sqls = preg_replace('/`'.Conf::$db_tablePrefix.'/', '`@YYUCTP_', $sqls,2);
			}			
			file_put_contents($dataDir.'/structs.sql',$sqls,FILE_APPEND);
			unset($sqls);	
		}
		if(!$justStruts){
			$session_state = array();
			$session_state['tables'] = $tables;
			$session_state['begin'] = 0;
			$session_state['dir'] = $dataDir;
			$_SESSION['db_backup_state'] = &$session_state;
		}else{
			file_put_contents($dataDir.'/over.flag','struts',FILE_APPEND );
		}
	}
	function continueBackup(){
		if(isset($_SESSION['db_backup_state'])){
			$state = &$_SESSION['db_backup_state'];
			$tablename = $state['tables'][0];
			$begin = intval($state['begin']);
			$dir = $state['dir'];
			$rs=mysql_query("select count(*) from `$tablename`",$this->mysql_link);
			$row = mysql_fetch_row($rs);
			$totalnum = intval($row[0]);
			//查寻出表中的所有数据
			$rs=mysql_query("select * from `$tablename` limit $begin,".$this->onefile_num,$this->mysql_link);
			//表的字段个数
			$field=mysql_num_fields($rs);
			//形成此种SQL语句
			$rownum = 0;
			while($rows=mysql_fetch_row($rs)){
				$comma='';//逗号
				$sqls = "INSERT INTO `$tablename` VALUES(";
				for($i=0;$i<$field;$i++){
					if($rows[$i]===null){
						$sqls.=$comma."null";						
					}else{
						$sqls.=$comma."'".mysql_escape_string($rows[$i])."'";
					}
					$comma=',';
				}
				$sqls.=");\n";
				$rownum++;
				if(Conf::$db_tablePrefix!=''){
					$sqls = str_replace('`'.Conf::$db_tablePrefix, '`@YYUCTP_', $sqls);
				}				
				file_put_contents($dir.'/'.$tablename.'_'.$begin.'.sql',$sqls,FILE_APPEND );
				unset($sqls);
			}
			if($totalnum<=$begin+$this->onefile_num){
				$_SESSION['db_backup_state_msg'] = '表：'.$state['tables'][0].'总数：'.$totalnum.'已经完成';
				array_shift($state['tables']);
				if(count($state['tables'])==0){
					unset($_SESSION['db_backup_state']);
					unset($_SESSION['db_backup_state_msg']);
					file_put_contents($dir.'/over.flag','all',FILE_APPEND );
					return true;
				}else{
					$state['begin'] = 0;
				}				
			}else{
				$state['begin'] = $begin+$this->onefile_num;
				$_SESSION['db_backup_state_msg'] = '表：'.$state['tables'][0].'总数：'.$totalnum.'完成约：'.$state['begin'].'.';
			}
			return false;
		}
		return true;
	}
	private function _delarray($array){//处理传入进来的数组
		foreach($array as $tables){
			if($tables=='@all'){//所有的表(获得表名时不能按常规方式来组成一个数组)
				$newtables=mysql_list_tables($this->dbName,$this->mysql_link);
				$tableList = array();
				$l_numroes = mysql_numrows($newtables);
				for ($i = 0; $i < $l_numroes; $i++){
					$tableList [] = mysql_tablename($newtables, $i);
				}
			}else{
				$tableList=$array;
				break;
			}
		}
		return $tableList;
	}
}
?>