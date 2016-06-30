<?php
namespace aae\api {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class APIRequestTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new APIRequest("Fu", "ba", array("one", "two"));
		}
		
	}
}