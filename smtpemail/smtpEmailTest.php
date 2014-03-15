<?php

include "smtpEmail.php";

class smtpEmailTest extends PHPUnit_Framework_TestCase {
	private $smtpServer = null;
	public function setUp() {
		$this->smtpServer = new smtpEmail("smtp.qq.com", 25, "780860343@qq.com", "suxiaolindy110is");
	}

	public function testSetup() {
		$ret = $this->smtpServer->send("1358081510@qq.com", "hello smtp", "<a href='http://moonphp.sinaapp.com'>moonphp</a>");
		$this->assertTrue($ret);
	}
}

//
//$s = new smtpEmail("smtp.qq.com", 25, "780860343@qq.com", "suxiaolindy110is");
//
//$receivers = array("xuesem@126.com", "1358081510@qq.com");
//$ret = $s->send($receivers, "hello smtp", "<a href='http://moonphp.sinaapp.com'>moonphp</a>");
//var_dump($ret);
?>
