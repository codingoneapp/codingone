<?php
/**
 * 日志记录
 * @author mqq
 *
 */
class Log{
	/**
	 * 调试记录
	 * @param $str
	 */
	public static function debug($str){
		global $_PAGE;
		if(Conf::$log_level>3)
		file_put_contents(YYUC_FRAME_PATH.'/log/debug/'.date('Y_m_d',time()).'.log', date('H:i:s',time()).': '.$str."@".$_PAGE->controller_path."\n",FILE_APPEND);
	}
	/**
	 * 信息记录
	 * @param $str
	 */
	public static function info($str){
		global $_PAGE;
		if(Conf::$log_level>2)
		file_put_contents(YYUC_FRAME_PATH.'/log/info/'.date('Y_m_d',time()).'.log', date('H:i:s',time()).': '.$str."@".$_PAGE->controller_path."\n",FILE_APPEND);
	}
	/**
	 * 警告记录
	 * @param $str
	 */
	public static function warn($str){
		global $_PAGE;
		if(Conf::$log_level>1)
		file_put_contents(YYUC_FRAME_PATH.'/log/warn/'.date('Y_m_d',time()).'.log', date('H:i:s',time()).': '.$str."@".$_PAGE->controller_path."\n",FILE_APPEND);
	}
	/**
	 * 错误记录
	 * @param $str
	 */
	public static function error($str){
		global $_PAGE;
		if(Conf::$log_level>0)
		file_put_contents(YYUC_FRAME_PATH.'/log/error/'.date('Y_m_d',time()).'.log', date('H:i:s',time()).': '.$str."@".$_PAGE->controller_path."\n",FILE_APPEND);
	}
}
?>