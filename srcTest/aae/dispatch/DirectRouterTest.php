<?php
namespace aae\dispatch {
	require_once strstr(__FILE__, 'Test', true).'/aae/autoload/AutoLoader.php';
	class DirectRouterTest extends \PHPUnit_Framework_TestCase {
		public function test___construct() {
			$receiver = $this->getMockBuilder('\aae\dispatch\receiver\ReceiverInterface')
				->disableOriginalConstructor()
				->getMock();
			$obj = new DirectRouter($receiver);
		}
		
	}
}