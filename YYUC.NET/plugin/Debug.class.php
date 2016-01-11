<?php
class Debug {
	/**记录所调用的SQL语句以及所执行的时间*/
	public static $sql_time = array();
	/**记录各个调试点以及它们的间隔时间时间*/
	public static $benchmark_time = array();

	/**
	 * 开启或关闭debug记录模式 
	 * @param boolean $tag
	 */
	public static function set_debug($tag = true) {
		Conf::$need_debug_log = $tag;
	}

	/**
	 * 开启或关闭是否自动打印各项调试日志
	 * @param boolean $tag
	 */
	public static function auto_print_debug($tag = true) {
		Conf::$auto_print_debug = $tag;
	}
	/**
	 * 记录SQL开始
	 * @param string $sql
	 */
	public static function log_sql_begin($sql) {
		$the_arr = array();
		$the_arr[] = $sql;
		$the_arr[] = microtime(true);
		self::$sql_time[] = $the_arr;
	}
	/**
	 * 记录SQL结束
	 */
	public static function log_sql_end() {
		$the_arr = &self::$sql_time[count(self::$sql_time) - 1];
		$the_arr[] = microtime(true);
	}
	/**
	 * 记录时间基准点
	 * @param string $tname 基准点名称
	 */
	public static function benchmark($tname) {
		$the_arr = array();
		$the_arr[] = $tname;
		$the_arr[] = microtime(true);
		self::$benchmark_time[] = $the_arr;
	}
	/**
	 * 打印表单的POST请求
	 */
	public static function print_post() {
		echo '<br/>POST请求信息：<br/>';
		foreach ($_POST as $k=>$v){
			echo htmlspecialchars($k).'=>'.htmlspecialchars($v).'<br/>';
		}
		echo '<br/>';
	}

	/**
	 * 打印表单的GET请求
	 */
	public static function print_get() {
		echo '<br/>GET请求信息：<br/>';
		foreach ($_GET as $k=>$v){
			echo htmlspecialchars($k).'=>'.htmlspecialchars($v).'<br/>';
		}
		echo '<br/>';
	}

	/**
	 * 打印系统信息和请求的头信息
	 */
	public static function print_server() {
		echo '<br/>GET系统信息和请求信息：<br/>';
		foreach ($_SERVER as $k=>$v){
			echo htmlspecialchars($k).'=>'.htmlspecialchars($v).'<br/>';
		}
		echo '<br/>';
	}
	/**
	 * 打印系统SESSION信息
	 */
	public static function print_session() {
		echo '<br/>当前SESSION信息：<br/>';
		foreach ($_SESSION as $k=>$v){
			echo htmlspecialchars($k).'=>'.htmlspecialchars($v).'<br/>';
		}
		echo '<br/>';
	}
	/**
	 * 打印系统COOKIE信息
	 */
	public static function print_cookie() {
		echo '<br/>当前COOKIE信息：<br/>';
		foreach ($_COOKIE as $k=>$v){
			echo htmlspecialchars($k).'=>'.htmlspecialchars($v).'<br/>';
		}
		echo '<br/>';
	}
	/**
	 * 打印当前请求的控制器路径
	 */
	public static function print_controller() {
		global $_PAGE;
		echo '<br/>请求的控制器路径信息：<br/>';
		echo $_PAGE->col_path;
		echo '<br/>';
	}
	/**
	 * 打印当前内存使用情况
	 */
	public static function print_memory() {
		global $_PAGE;
		echo '<br/>内存使用情况：<br/>';
		echo memory_get_usage();
		echo 'bytes<br/>';
	}
	/**
	 * 打印各个时间节点的间隔
	 */
	public static function print_benchmark_time() {
		$kstime = 0;
		if (count(self::$benchmark_time) > 1) {
			echo '<br/>节点运行时间跟踪：<br/>';
			foreach (self::$benchmark_time as $ts) {				
				if ($kstime !== 0) {
					echo '节点名称：' . $ts[0] . '<br/>';
					echo '阶段耗时：' . ($ts[1] - $kstime) . '秒<br/>';
				}
				$kstime = $ts[1];
			}
		}
	}

	/**
	 * 打印各个SQL语句执行的时间
	 */
	public static function print_sql_time() {
		if (self::$sql_time) {
			echo '<br/>SQL运行时间跟踪：<br/>';
			foreach (self::$sql_time as $ts) {
				if ($kstime !== 0) {
					echo 'SQL语句：' . $ts[0] . '<br/>';
					echo '阶段耗时：' . ($ts[2] - $ts[1]) . '秒<br/>';
				}
			}
		}
	}
	/**
	 * 打印出所有Debug信息
	 */
	public static function print_all() {
		self::benchmark('执行结束');
		self::print_memory();
		self::print_server();
		self::print_get();
		self::print_post();
		self::print_session();
		self::print_cookie();
		self::print_controller();
		self::print_benchmark_time();
		self::print_sql_time();
	}
}

?>