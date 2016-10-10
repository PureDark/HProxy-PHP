<?php
/*==============================================================
#	  Program: Novel Search Group
#	 FileName: Steal.class.php
#		 Desc: 采集功能类
#	   Author: pakey
#		Email: pakey@qq.com
#	 HomePage: http://www.pakey.net
#   LastChange: 2011-08-11 03:19:14
===============================================================*/

class Steal{
	//采集类型
	private $collecttype = "curl";
	//user-agent 
	private $collectuseragent = "KuaiYanKanShu Spider+(+http://www.kuaiyankanshu.net/about/spider.html)";
	//超时时间
	private $timeout=30;
	
	public function __construct($collecttype="curl") {
		$this->collecttype = $collecttype;
	}

	/**
	 * 普通获取内容
	 * 
	 * @param $url 采集的网址
	 * @param $referurl 采集的时候发送refer信息
	 * @access public
	 * @return str
	 */
	public function getCode($url, $referurl='', $cookie='')
	{
		$buf = parse_url($url);
		$host = $buf['host'];
		if(empty($referurl))
			$referurl = "http://$host/";
		switch ($this->collecttype)
		{
			case 'file_get_contents':
				$opts = array('http' => array('header' => "Content-type:application/x-www-form-urlencoded\r\n" . "User-Agent:" . $this->collectuseragent . "\r\n" . "Referer:$referurl\r\n" . "Cookie:$cookie/\r\n", 'timeout' => $this->timeout), );
				$context = stream_context_create($opts);
				$code = file_get_contents($url, false, $context);
				break;
			case 'curl':
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_REFERER, $referurl);
				curl_setopt($ch, CURLOPT_COOKIE, $cookie);
				curl_setopt($ch, CURLOPT_USERAGENT, $this->collectuseragent);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
				$code = curl_exec($ch);
				curl_close($ch);
				break;
			case 'fsockopen':
				if (isset($buf['path']))
				{
					$page = $buf['path'];
					if (isset($buf['query']) and trim($buf['query']) !== "")
					{
						$page .= "?" . trim($buf['query']);
					}
				}
				else
				{
					$page = '/';
				}
				if (isset($buf['port']))
				{
					$port = $buf['port'];
				}
				else
				{
					$port = 80;
				}

				$header = "GET $page HTTP/1.1\r\n";
				$header .= "Host: $host\r\n";
				$header .= "Connection: close\r\n";
				$header .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5\r\n";
				$header .= "Accept-Language: zh-cn,zh;q=0.5\r\n";
				$header .= "Accept-Charset: gb2312,utf-8;q=0.7,*;q=0.7\r\n";
				$header .= "User-Agent:" . $this->collectuseragent . "\r\n";
				$header .= "Cookie: $cookie\r\n\r\n";
				$header .= "Referer: $referurl\r\n\r\n";
				$code = "";
				$fp = pfsockopen($host, $port, $errno, $errstr, $this->timeout);

				if ($fp)
				{
					fputs($fp, $header);
					while (!feof($fp))
						$code .= fgets($fp, 1024);
					fclose($fp);
				}
				else
				{
					return false;
				}
				break;

		}
		if ($code != "")
		{
			return $code;
		}
		else
		{
			return false;
		}
		return $code;
	}
}