<?php
/**
 * 系统静态类文件编辑,供框架系统模块调用
 * @author mqq
 */
class ClassConfigEdit{
	/**
	 *读取配置文件
	 * @param $filename 配置文件名
	 * @param $block 所要读取的区块
	 */
	public function load($filename,$block){
		$confstr = file_get_contents($filename);
		$confstrs = explode('/////////////////////', $confstr);
		$dnconfstr = $confstrs[$block];
		$dnconfstrs = explode('/**', $dnconfstr);
		$conlen = count($dnconfstrs);
		$res = array();
		for($i=1;$i<$conlen;$i++){
			$thisconf = array();
			//每一段配置的循环
			$thisstrs = explode('*/', $dnconfstrs[$i]);
			$ms = explode('~', $thisstrs[0]);
			$mstitle = trim($ms[0]);
			$mscon = trim($ms[1]);
			$configstr = trim($thisstrs[1]);
			$configstrs = explode(' = ', trim($thisstrs[1]));
			$configstr1 = trim($configstrs[0]);
			$configstr2 = trim($configstrs[1]);
			$configstr2 = substr($configstr2, 0, strlen($configstr2)-1);
			$conftype = 0;
			if(substr($configstr2, 0, 1)=='"'){
				$configstr2 = substr($configstr2, 1, strlen($configstr2)-2);
				$conftype = 1;
			}
			$thisconf['title'] = $mstitle;//名称
			$thisconf['con'] = $mscon;//备注
			$thisconf['conf'] = $configstr;//整个字符
			$thisconf['confl'] = str_replace("public static ", "", $configstr1);//左侧内容
			$thisconf['confr'] = $configstr2;//右侧内容
			$thisconf['conft'] = $conftype;//类型
			$res[] = $thisconf;
		}
		return $res;
	}
	/**
	 * 写入配置文件
	 * @param $filename  配置文件名
	 * @param $post 前台提交的配置信息
	 */
	public function write($filename,$post){
		$confstr = file_get_contents($filename);
		$lines =count($post['conf']);
		for($i=0;$i<$lines;$i++){
			$post['confr'][$i] = trim($post['confr'][$i]);
			if($post['conft'][$i] == '1'){
				$post['confr'][$i] = '"'.$post['confr'][$i].'"';
			}
			$newconf = "public static ".$post['confl'][$i]." = ".$post['confr'][$i].";";
			$confstr = str_replace(stripslashes($post['conf'][$i]), $newconf, $confstr);
		}
		file_put_contents($filename, $confstr);
	}
}
?>