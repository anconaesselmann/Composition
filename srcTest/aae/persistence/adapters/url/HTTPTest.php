<?php
namespace aae\persistence\adapters\url {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class HTTPTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new HTTP();
		}
		
	}
}