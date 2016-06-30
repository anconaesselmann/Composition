<?php
namespace aae\security {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class EncoderTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$obj = new Encoder();
		}
		
	}
}