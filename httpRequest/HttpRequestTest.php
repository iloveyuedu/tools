<?php

include "HttpRequest.php";

class HttpRequestTest extends PHPUnit_Framework_TestCase {
	private $request = null;
	public function setUp() {
		$this->request = new HttpRequest("localhost", 20, 'post');
	}
	public function testPost() {
		$return = $this->request->post("/php/20140313/post_return.php", array(
			"name"=>"zhangsan",
			"age"=>20,
		))->get_r_body();

		echo $return;
		$this->assertEquals(array("name"=>"zhangsan", "age"=>20), json_decode($return, true));
	}
}


?>
