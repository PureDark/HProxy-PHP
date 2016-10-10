<?php
	//定义编码  
	header( "Content-Type:text/plain;charset=utf-8");  
	include("class.steal.php");
	$steal = new Steal("file_get_contents");
	$headers = get_all_headers();
	if(isset($headers["Origin-Url"])&&!empty($headers["Origin-Url"])){
		$originUrl = urldecode($headers["Origin-Url"]);
		$referer = (isset($headers["Referer"]))?$headers["Referer"]:"";
		$cookie = (isset($headers["Cookie"]))?$headers["Cookie"]:"";
		echo $steal->getCode($originUrl, $referer, $cookie);
	}else{
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
?>