<?php

/**
 * 模拟http请求，使用fsockopen实现
 */

class HttpRequest {
	private $stream = NULL;
	private $requestHeader = array();
	private $requestType = '';
	private $requestTypeMap = array(// 目前仅支持 post 和 get
		'POST',
		'GET',
	);
	private $errno, $errstr;

	private $responseStr = "", $responseHeader = array(), $responseBody = "";
	private $responseStaus = array();

	/**
	 * $param String $host 请求host
	 * @param Int $timeout 请求超时限制
	 * @param String $requestType 请求动词
	 *
	 * @return Object 成功返回$this
	 */
	public function __construct($host, $timeout = -1, $requestType) {
		$this->requestType = strtoupper($requestType);
		$this->requestHeader['host'] = $host;
		$this->requestHeader['connection'] = "close";
		$this->stream = fsockopen($host, 80, $this->errno, $this->errstr, $timeout);

		return $this;
	}

	public function add_header($key, $value) {
		$this->requestHeader[$key] = $value;

		return $this;
	}

	/**
	 * 模拟post请求
	 * @param String $uri post请求uri
	 * @param Array $data post请求数据
	 *
	 * @return Object 成功返回$this
	 */
	public function post($uri, $data) {
		$this->requestHeader["Content-Type"] = "application/x-www-form-urlencoded";
		$header = "{$this->requestType} {$uri} HTTP/1.1\r\n";
		foreach($this->requestHeader as $name => $value) {
			$name = ucfirst(strtolower($name));
			$header .= "{$name}: {$value}\r\n";
		}
		$pData = array();
		foreach($data as $name => $value) {
			$pData[] = "{$name}=" . urlencode($value);
		}
		$pData = implode("&", $pData);
		$header .= "Content-Length: " . strlen($pData);
		$header .= "\r\n\r\n";
		$header .= $pData . "\r\n";

		fwrite($this->stream, $header);
		$this->get_response();

		return $this;
	}

	/**
	 * 模拟get请求
	 * @param String $uri get请求uri
	 *
	 * @return Object 成功返回$this
	 */
	public function get($uri) {
		$header = "{$this->requestType} {$uri} HTTP/1.1\r\n";
		foreach($this->requestHeader as $name => $value) {
			$name = ucfirst(strtolower($name));
			$header .= "{$name}: {$value}\r\n";
		}
		fwrite($this->stream, $header."\r\n");
		$this->get_response();

		return $this;
	}

	/**
	 * 获取响应
	 */
	private function get_response() {
		$this->responseStr = '';
		while(!feof($this->stream)) {
			$this->responseStr .= fread($this->stream, 1024);
		}

		$segs = explode("\r\n", $this->responseStr);
		list($scheme, $statusCode, $statusText) = explode(" ", array_shift($segs));
		$this->responseStaus[$statusCode] = array($scheme, $statusText);
		$this->responseBody = array_pop($segs);
		array_pop($segs);
		foreach($segs as $item) {
			list($name, $value) = explode(":", $item);
			$this->responseHeader[trim($name)] = trim($value);
		}
		return true;
	}

	/**
	 * 获取响应头
	 */
	public function get_r_header() {
		return $this->responseHeader;
	}

	/**
	 * 获取响应体
	 */
	public function get_r_body() {
		return $this->responseBody;
	}
}

?>
