<?php

/**
 * 获取MongDB类
 *
 * @author Qingqun Meng  <qmeng@jidekuai.com>
 * @since PHP5.3
 * @version 1.0 2012-7-24
 */
class MongoConn{
	
	/**
	 * 记录上一次连接的名称
	 *
	 * @var string
	 */
	static $last_conn_name = null;
	
	/**
	 * 记录上一次连接
	 *
	 * @var Mongo
	 */
	static $last_conn = null;
	
	/**
	 * 获得链接
	 * 
	 * @param string $connection
	 * @return Mongo
	 */
	public static function connection($connection = null)
	{
		if(is_null($connection))
		{
			$connection = 'default';
		}
		
		if(static::$last_conn_name === $connection && !is_null(static::$last_conn))
		{
			return static::$last_conn;
		}
		$conn = conf::$mongo[$connection];		
		
		$linkstr = $conn['host'].':'.$conn['port'];
		if(isset($conn['username']) && trim($conn['username']) !='')
		{
			$linkstr = $conn['username'].':'.$conn['password'].'@'.$linkstr;
		}
		$mconn = new Mongo('mongodb://'.$linkstr);
		static::$last_conn_name = $connection;
		static::$last_conn = $mconn;
		return $mconn;
	}
	
	/**
	 * 建立连接并指定到给定的数据库
	 * 
	 * @param string $database
	 * @param string $connection
	 * @return MongoDB
	 */
	public static function database($database, $connection = null)
	{
		$mconn = static::connection($connection);	
		return $mconn->selectDB($database);
	}
	
	/**
	 * 建立连接并指定到给定的数据库的集合
	 *
	 * @param string $database
	 * @param string $collection
	 * @param string $connection
	 * @return MongoCollection
	 */
	public static function collection($database, $collection, $connection = null)
	{
		$mconn = static::connection($connection);
		return $mconn->selectCollection($database, $collection);
	}
	
	/**
	 * 建立连接并指定到给定的数据库的文件系统
	 *
	 * @param string $database
	 * @param string $collection
	 * @param string $connection
	 * @return MongoCollection
	 */
	public static function gridfs($database, $connection = null)
	{
		$db = static::database($database, $connection);
		return $db->getGridFS();
	}
}