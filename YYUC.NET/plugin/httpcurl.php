<?php
class HttpCurl{
/**
     * combineURL
     * 拼接url
     * @param string $baseURL   基于的url
     * @param array  $keysArr   参数列表数组
     * @return string           返回拼接的url
     */
    public static function combineURL($baseURL,$keysArr){
    	if(empty($keysArr)){
    		return $baseURL;
    	}
        $combined = $baseURL."?";
        $valueArr = array();

        foreach($keysArr as $key => $val){
            $valueArr[] = "$key=$val";
        }

        $keyStr = implode("&",$valueArr);
        $combined .= ($keyStr);
        
        return $combined;
    }

    /**
     * combineQuery
     * 拼接url
     * @param string $baseURL   基于的url
     * @param array  $keysArr   参数列表数组
     * @return string           返回拼接的url
     */
    public static function combineQuery($keysArr){
    	$combined = '';
    	$valueArr = array();
    
    	foreach($keysArr as $key => $val){
    		$valueArr[] = "$key=$val";
    	}
    
    	$keyStr = implode("&",$valueArr);
    	$combined .= ($keyStr);    
    	return $combined;
    }
    
    /**
     * get_contents
     * 服务器通过get请求获得内容
     * @param string $url       请求的url,拼接后的
     * @return string           请求返回的内容
     */
    public static function get_contents($url,&$status_code){
       	$ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        //-------请求为空
        if(empty($response)){
            return null;
        }

        return $response;
    }

    /**
     * get
     * get方式请求资源
     * @param string $url     基于的baseUrl
     * @param array $keysArr  参数列表数组      
     * @return string         返回的资源内容
     */
    public static function quickget($url, &$keysArr=null,&$statuscode=null){
    	if(is_array($keysArr)){
    		$combined = self::combineURL($url, $keysArr);
    	}else{
    		$combined = self::combineURL($url, array());
    	}
    	if($keysArr != null){
    		return self::get_contents($combined,$keysArr);
    	}        
        return self::get_contents($combined,$statuscode);
    }

    /**
     * post
     * post方式请求资源
     * @param string $url       基于的baseUrl
     * @param array $keysArr    请求的参数列表
     * @param int $flag         标志位
     * @return string           返回的资源内容
     */
    public static function quickpost($url, $keysArr, $timeout=60,$post_header=false){
        $ch = curl_init();        
        if(is_array($post_header)){
        	curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header); //设置头信息的地方 
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch,CURLOPT_TIMEOUT,$timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($ch, CURLOPT_POST, TRUE); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $keysArr); 
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
    
    
    public $curl = null;
    public $url = null;
    public $cookies = array();
    public $headers = array();
    public $content = '';
    public $errormsg = '';
    public $headers_only = false;
    public $justhtml = false;
    public $charset = null;
    public $persist_cookies = true;
    
    function initset($url=null,$isfirst=false){
    	$url = empty($url)? $this->url : $url;
    	$this->url = $url;
    	if($this->curl==null){
    		$this->curl = curl_init();    		
    		curl_setopt($this->curl, CURLOPT_AUTOREFERER, 1); //自动REFERER
    		curl_setopt($this->curl, CURLOPT_HEADER, 1); //返回header部分
    		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
    		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
    		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true); //返回字符串，而非直接输出 
    		curl_setopt($this->curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.29 Safari/537.36");
    	}
    	curl_setopt($this->curl, CURLOPT_URL, $this->url);
    	curl_setopt($this->curl, CURLOPT_REFERER, $this->url);
    	if ($this->persist_cookies && !empty($this->cookies)) {
    		$cookie = '';
    		foreach ($this->cookies as $key => $value) {
    			$cookie .= "{$key}={$value}; ";
    		}
    		curl_setopt($this->curl, CURLOPT_COOKIE, $cookie); //存储cookies
    	}
    	curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'GET');
    	curl_setopt($this->curl, CURLOPT_POSTFIELDS, null);    	
    } 
    /**
     * 
     */
    function __construct(){
    }
    
    /**
     * post
     * post方式请求资源
     * @param string $url       基于的baseUrl
     * @param array $keysArr    请求的参数列表
     * @param int $flag         标志位
     * @return string           返回的资源内容
     */
    public function post($url=null, $keysArr=null,$native=true){
    	$this->initset($url);
    	curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'POST');
    	if(!empty($keysArr)){
    		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $keysArr);
    	}	
    	$this->transres($this->curl);
    	return $this->getcontent($native);
    }
    
    /**
     * get
     * get方式请求资源
     * @param string $url       基于的baseUrl
     * @param array $keysArr    请求的参数列表
     * @param int $flag         标志位
     * @return string           返回的资源内容
     */
    public function get($url=null, $keysArr=null,$native=true){    	
    	if(!empty($keysArr)){
    		$url = self::combineURL($url, $keysArr);
    	}
    	$this->initset($url);
    	$this->transres($this->curl);
    	return $this->getcontent($native);
    }
    /**
     * getfile
     * 文件下载
     * @param string $url       基于的baseUrl
     * @param array $keysArr    请求的参数列表
     * @param int $flag         标志位
     * @return string           返回的资源内容
     */
    public function get_file($url=null, $keysArr=null){    	
    	if(!empty($keysArr)){
    		$url = self::combineURL($url, $keysArr);
    	}
    	$this->initset($url);
    	curl_setopt($this->curl, CURLOPT_HEADER, 0);
    	$res = curl_exec($this->curl);
    	curl_setopt($this->curl, CURLOPT_HEADER, 1);
    	return $res;
    }
    
       
    
    function transres($curl){
    	$response = curl_exec($curl);
    	$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    	$header = substr($response, 0, $header_size);
    	$body = substr($response, $header_size);
    	
    	$rarr = explode("\n", $header);
    	$this->headers = array();
    	$this->content = $body;
    	$this->errormsg = '';
    	$atStart = true;
    	foreach ($rarr as $line) {
    		if ($atStart) {
    			// Deal with first line of returned data
    			$atStart = false;
    			if (!preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/', $line, $m)) {
    				$this->errormsg = "Status code line invalid: ".htmlentities($line);
    				return false;
    			}
    			$http_version = $m[1]; // not used
    			$this->status = $m[2];
    			$status_string = $m[3]; // not used
    			if($this->status == '404'){
    				$this->content = null;
    				return false;
    			}
    			continue;
    		}
    	
    		
    		if (!preg_match('/([^:]+):\\s*(.*)/', $line, $m)) {
    	   		// Skip to the next header
    			continue;
    		}
    		$key = strtolower(trim($m[1]));
    		$val = trim($m[2]);
    		// Deal with the possibility of multiple headers of same name
    		if (isset($this->headers[$key])) {
    			if (is_array($this->headers[$key])) {
    				$this->headers[$key][] = $val;
    			} else {
    					$this->headers[$key] = array($this->headers[$key], $val);
    			}
    		} else {
    				$this->headers[$key] = $val;
    		}
    	}
    	
    	//gzip
    	if (isset($this->headers['content-encoding']) && $this->headers['content-encoding'] == 'gzip') {
    		$this->debug('Content is gzip encoded, unzipping it');
    		$this->content = substr($this->content, 10); // See http://www.php.net/manual/en/function.gzencode.php
    		$this->content = gzinflate($this->content);
    	}
    	
    	//判断页面编码
    	if(stripos($this->headers['content-type'], 'charset') !== false){
    		$encode = String::find_first_string_by_reg($this->headers['content-type'],"/\bcharset\s*=\s*\w+[-]?[8]?\b/");
    		$encode = explode('=', $encode);
    		if(isset($encode[1])){
    			$this->charset = trim($encode[1]);
    		}
    	}
    	
    	if ($this->persist_cookies && isset($this->headers['set-cookie'])) {
    		$cookies = $this->headers['set-cookie'];
    	
    		if (!is_array($cookies)) {
    			$cookies = array($cookies);
    		}    		
    		foreach ($cookies as $cookie) {
    			if (preg_match('/([^=]+)=(.+)/', $cookie, $m)) {
    				$value = explode(';', $m[2]);
    				$this->cookies[$m[1]] = $value[0];
    			}
    		}
    	}
    }
    
    function getcontent($native = false) {
    	if($native){
    		return $this->content;
    	}
    	if($this->charset === null){
    		$encode = String::find_first_string_by_reg(strtolower($this->content),"/\bcharset\s*=\s*\w+[-]?[8]?\b/");
    		if($encode ==''){
    			$encode = String::find_first_string_by_reg(strtolower($this->content),"/\bcharset\s*=\s*\"\s*\w+[-]?[8]?\s*\"\s*\b/");
    		}
    		if($encode!=''){
    			$encode = explode('=', $encode);
    			if(isset($encode[1])){
    				$encode = trim($encode[1]);
    				$encode = str_replace('"', '', $encode);
    				$encode = str_replace("'", '', $encode);
    				$this->charset = $encode;
    			}
    		}
    	}
    	if($this->charset !=null && $this->charset != 'utf8' && $this->charset != 'utf-8'){
    		return iconv($this->charset, "utf-8//IGNORE",$this->content);
    	}
    	return $this->content;
    }
    
    public function set_cookies($cookies) {
    	$this->cookies = $array;
    }
    
    public function set_referer($referer) {
    	$this->referer = $array;
    }
}