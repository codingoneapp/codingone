<?php
/**
 * 缓存类
 * 
 * @author mqq
 *
 */
class Cache{
	
	/**
	 * 
	 * @var Memcache
	 */
	public static $memcached = null;
	
	/**
	 * 
	 * @var Redis
	 */
	public static $redis = null;
	/**
	 * 保存缓存数据
	 * 
	 * @param string $k 键
	 * @param mixed $v 值
	 * @param string $time 保存持续时间(单位:秒,0为永远)
	 */
	public static function set($k, $v, $time = 0){
		if(Conf::$cache_adapter == 'memcached'){
			return self::get_memcached()->set($k, serialize($v),time()+$time);
		}elseif(Conf::$cache_adapter == 'redis'){
			if($time === 0){
				self::get_redis()->set($k, serialize($v));
			}else{
				self::get_redis()->setex($k, $time,serialize($v));
			}
			self::get_redis()->expire($k,$time);
		}else{			
			$path = YYUC_FRAME_PATH.'sys/cache/'.str_replace('%2F', '/', urlencode($k));
			File::creat_dir_with_filepath($path);
			return file_put_contents($path, ($time === 0 ? '0' : (time()+ intval($time))).'@YYUC_CACHE@'.serialize($v), LOCK_EX);
		}		
	}
	
	/**
	 * 永久要保存的缓存数据<br/>
	 * 这个方法通过文件形式存储缓存
	 *
	 * @param string $k 键
	 * @param mixed $v 值
	 */
	public static function forever($k, $v = null){
		$path = YYUC_FRAME_PATH.'sys/cache/YYUCFOREVER/'.str_replace('%2F', '/', urlencode($k));
		if($v === null){
			//读取
			if(file_exists($path)){
				return unserialize(file_get_contents($path));
			}
			return null;
		}else{
			//设置
			File::creat_dir_with_filepath($path);
			file_put_contents($path, serialize($v), LOCK_EX);
		}		
	}
	/**
	 * 删除某个永久缓存
	 *
	 * @param string $k 键
	 */
	public static function forget($k){
		return File::remove_file_with_parentdir(YYUC_FRAME_PATH.'sys/cache/YYUCFOREVER/'.str_replace('%2F', '/', urlencode($k)));
	}
	/**
	 * 获得缓存的值
	 * 
	 * @param string $k 键
	 * @return mixed 值
	 */
	public static function get($k,$default = null){
		if(Conf::$cache_adapter == 'memcached'){
			$res = self::get_memcached()->get($k);
			return !$res? $default : unserialize($res);
		}elseif(Conf::$cache_adapter == 'redis'){
			$res = self::get_redis()->get($k);
			return !$res ? $default : unserialize($res);
		}else{
			$path = YYUC_FRAME_PATH.'sys/cache/'.str_replace('%2F', '/', urlencode($k));
			if(file_exists($path)){
				$res = explode('@YYUC_CACHE@', file_get_contents($path));
				if($res[0] == '0' || intval($res[0]) >= time()){
					return unserialize($res[1]);
				}else{
					File::remove_file_with_parentdir($path);
				}
			}
			return $default;
		}
		
	}
	
	/**
	 * 判断是否存在某个缓存值
	 *
	 * @param string $k 键
	 * @return boolean
	 */
	public static function has($k){
		if(Conf::$cache_adapter == 'memcached'){
			$res = self::get_memcached()->get($k);
			return $res !== false && $res !== Memcached::RES_NOTFOUND;
		}elseif(Conf::$cache_adapter == 'redis'){
			return self::get_redis()->exists($k);
		}else{
			$path = YYUC_FRAME_PATH.'sys/cache/'.str_replace('%2F', '/', urlencode($k));
			if(file_exists($path)){
				$res = explode('@YYUC_CACHE@', file_get_contents($path));
				if($res[0] == '0' || intval($res[0]) >= time()){
					return true;
				}else{
					File::remove_file_with_parentdir($path);
				}
			}
			return false;
		}
		
	}
	
	/**
	 * 删除某个缓存值
	 *
	 * @param string $k 键
	 */
	public static function remove($k){
		if(Conf::$cache_adapter == 'memcached'){
			return self::get_memcached()->delete($k);
		}elseif(Conf::$cache_adapter == 'redis'){
			self::get_redis()->delete($k);
		}else{
			return File::remove_file_with_parentdir(YYUC_FRAME_PATH.'sys/cache/'.str_replace('%2F', '/', urlencode($k)));
		}		
	}
	
	/**
	 * 获得Memcache连接
	 * 
	 * @return Memcache Memcache连接
	 */
	public static function get_memcached(){
		if(Cache::$memcached === null){
			Cache::$memcached = new Memcached('ocs');
			if (count(Cache::$memcached ->getServerList()) == 0) {/*建立连接前，先判断*/			
				/*所有option都要放在判断里面，因为有的option会导致重连，让长连接变短连接！*/
				Cache::$memcached ->setOption(Memcached::OPT_COMPRESSION, false);
				Cache::$memcached ->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
			
				/* addServer 代码必须在判断里面，否则相当于重复建立’ocs’这个连接池，可能会导致客户端php程序异常*/
				
				foreach (Conf::$memcached as $k=>$memconf){
					Cache::$memcached ->addServer($memconf[0], $memconf[1]);
				}
				if(Conf::$memcached_un){
					Cache::$memcached ->setSaslAuthData(Conf::$memcached_un, Conf::$memcached_pwd);
				}
			}			
			return Cache::$memcached;
		}else{
			return Cache::$memcached;
		}
	}
	
	
	/**
	 * 获得Redis连接
	 *
	 * @return Redis Redis连接
	 */
	public static function get_redis(){
		if(Cache::$redis === null){
			Cache::$redis = new Redis();
			Cache::$redis->connect(Conf::$redis[0],Conf::$redis[1]);
			return Cache::$redis;
		}else{
			return Cache::$redis;
		}
	}
}