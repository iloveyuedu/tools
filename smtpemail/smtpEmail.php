<?php

/**
 * 基本的smtp服务器交互发邮件
 */
class smtpEmail {
	private $smtpServer = null;
	private $errno, $errstr;
	private $lastReturn = "";
	private $username = "", $password = "";

	private $clientServerRec = array();

	/**
	 * @param String $host smtp服务器
	 * @param Int $port smtp服务器端口
	 * @package String $username 授权帐号
	 * @param String $password 授权密码
	 * @param Int Timeout fsock超时设置
	 *
	 * @return Object 成功返回$this
	 */
	public function __construct($host, $port, $username, $password, $timeout = -1) {
		$this->username = $username;
		$this->password = $password;
		$this->smtpServer = fsockopen($host, $port, $this->errno, $this->errstr, $timeout);
		$this->clientServerRec["connect"] = fread($this->smtpServer, 1024);
		return $this;
	}

	public function do_command($command, $expectReturn = "") {
		fwrite($this->smtpServer, $command . "\r\n");
		$this->lastReturn = fread($this->smtpServer, 1024);

		$this->clientServerRec[$command] = trim($this->lastReturn, "\r\n");
		return $this->code_exists($expectReturn);
	}

	public function code_exists($code = "") {
		if (!$code) {
			return true;
		}
		return stripos($this->lastReturn, strval($code)) !== false;
	}

	/**
	 * @param String $receiver 收件人
	 * @param String $title 标题
	 * @param String $message 发送内容
	 *
	 * @return Boolean 成功返回true，否则返回false
	 */
	public function send($receiver, $title, $message) {
		$msgBody = "from:<{$this->username}>\r\nto:<$receiver>\r\nsubject:{$title}\r\n\r\n{$message}\r\n.";
		$suc = $this->do_command("HELO localhost", 250)
				&& $this->do_command("auth login", 334)
				&& $this->do_command(base64_encode($this->username), 334)
				&& $this->do_command(base64_encode($this->password), 235)
				&& $this->do_command("mail from:<{$this->username}>", 250)
				&& $this->do_command("rcpt to:<$receiver>", 250)
				&& $this->do_command("data", 354)
				&& $this->do_command($msgBody, 250)
				&& $this->do_command("quit", 221)
				&& $this->do_command("", "")
				;

		return $suc;
	}

	/**
	 * 返回和服务器交互信息
	 */
	public function debug() {
		return $this->clientServerRec();
	}
}

?>
