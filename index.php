<?php
    $post = ($_SERVER['REQUEST_METHOD']=='POST');
	$headers = get_all_headers();
	if(isset($headers["Origin-Url"])&&!empty($headers["Origin-Url"])){
		$originUrl = urldecode($headers["Origin-Url"]);
        $headerArray = array();
        foreach($headers as $key => $value){
            if($key=="Host" || $key=="User-Agent")
                continue;
            $headerArray[] = $key.":".$value;
        }
        $ch = curl_init($originUrl);
        //print_r($headerArray);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, $post);
        if($post)
            curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $responseHeaders = substr($response, 0, $headerSize);
        curl_close($ch);
        
        $headerArray =  explode("\r\n", $responseHeaders);
        $charset = "none";
        foreach($headerArray as $header){
            if(preg_match('/charset.*?([\w-]+)/i', $header, $matches)){
                $charset = $matches[1];
            }
        }
        $charset = ($charset != "none")?";charset=".$charset:"";
	    header( "Content-Type:text/plain".$charset);  
        $html = substr($response, $headerSize);
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
?>