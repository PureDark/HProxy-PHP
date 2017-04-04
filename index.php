<?php
	//定义编码  
    $post = ($_SERVER['REQUEST_METHOD']=='POST');
	$headers = get_all_headers();
	if(isset($headers["Origin-Url"])&&!empty($headers["Origin-Url"])){
		$originUrl = urldecode($headers["Origin-Url"]);
        $html = getHtml($originUrl, $post, $headers);
        $charset = "utf-8";
        foreach($http_response_header as $header){
            if(preg_match('/charset.*?([\w-]+)/', $header, $matches)){
                $charset = $matches[1];
            }
        }
	    header( "Content-Type:text/plain;charset=".$charset);  
		echo $html;
	}else{
	    header( "Content-Type:text/plain;charset=utf-8");  
		foreach($headers as $key => $value) { 
			echo "<!--$key => $value-->\r\n";
		} 
	}
	
	function get_all_headers() { 
		$headers = array(); 
	 
		foreach($_SERVER as $key => $value) { 
			if(substr($key, 0, 5) === 'HTTP_') { 
				$key = substr($key, 5); 
				$key = strtolower($key); 
				$key = str_replace('_', ' ', $key); 
				$key = ucwords($key); 
				$key = str_replace(' ', '-', $key); 
				$headers[$key] = $value; 
			} 
		} 
		
		return $headers; 
	} 
    
    function getHtml($url, $post = false, $headers = array()){
        $headerStr = "";
        foreach($headers as $key => $value){
            $headerStr .= $key.":".$value."\r\n";
        }
        $opts = array('http' => array('header' => $headerStr, 'timeout' => $this->timeout), );
        $context = stream_context_create($opts);
        $code = file_get_contents($url, false, $context);
		return $code;
    }
?>